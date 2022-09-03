<?php


class Base
{
    public function __construct()
    {
        add_action('init', [$this, 'disableEmoji']);
        add_action('rest_api_init', [$this, 'disableRest']);

        add_filter('upload_mimes', [$this, 'SVGMimeType']);
        add_filter('wp_prepare_attachment_for_js', [$this, 'SVGMediaThumbnails'], 10, 3);
        add_action('admin_head', [$this, 'SVGFix']);

        add_action('wp_footer', [$this, 'removeWPembed']);

        add_action( 'pre_get_posts', [$this, 'my_home_query'] );


        $this->headCleanUp();
    }

    public function my_home_query( $query ) {
        if ( $query->is_main_query() && !is_admin() ) {
            $query->set( 'ignore_sticky_posts', 1);
        }
    }

    public function removeWPembed()
    {
        if (!is_user_logged_in()) {
            wp_deregister_script('wp-embed');
        }
    }

    public function headCleanUp()
    {
        remove_action('template_redirect', 'rest_output_link_header', 11);
        remove_action('wp_head', 'rest_output_link_wp_head');
        remove_action('wp_head', 'wp_oembed_add_discovery_links');
        remove_action('wp_head', 'wp_generator');
        remove_action('wp_head', 'wlwmanifest_link');
        remove_action('wp_head', 'rsd_link');
        remove_action('wp_head', 'wp_shortlink_wp_head');
        remove_action('wp_head', 'wp_resource_hints', 2);
    }

    public function disableRest()
    {
        Helper::force404();
        exit;
    }


    public function SVGMediaThumbnails($response, $attachment)
    {
        if ('image' === $response['type'] && 'svg+xml' === $response['subtype'] && class_exists('SimpleXMLElement')) {
            try {
                $path = get_attached_file($attachment->ID);
                if (@file_exists($path)) {
                    $svg = new SimpleXMLElement(@file_get_contents($path));
                    $src = $response['url'];
                    $width = (int)$svg['width'];
                    $height = (int)$svg['height'];

                    //media gallery
                    $response['image'] = compact('src', 'width', 'height');
                    $response['thumb'] = compact('src', 'width', 'height');

                    //media single
                    $response['sizes']['full'] = [
                        'height' => $height,
                        'width' => $width,
                        'url' => $src,
                        'orientation' => $height > $width ? 'portrait' : 'landscape',
                    ];
                }
            } catch (Exception $e) {
            }
        }

        return $response;
    }

    public function SVGMimeType($mimes)
    {
        $mimes['svg'] = 'image/svg+xml';
        $mimes['svgz'] = 'image/svg+xml';

        return $mimes;
    }

    public function SVGFix()
    {
        echo '<style>td.media-icon img[src$=".svg"], img[src$=".svg"].attachment-post-thumbnail, .thumbnail img[src$=".svg"] { width: 100% !important; height: auto !important; }</style>';
    }

    public function disableEmoji()
    {
        remove_action('admin_print_styles', 'print_emoji_styles');
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
        remove_filter('the_content_feed', 'wp_staticize_emoji');
        remove_filter('comment_text_rss', 'wp_staticize_emoji');

        add_filter('tiny_mce_plugins', function ($plugins) {
            if (is_array($plugins)) {
                return array_diff($plugins, ['wpemoji']);
            } else {
                return [];
            }
        });
    }
}
