<?php

use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

class TwigFilters
{
    public static function register($twig)
    {
        $twig->addExtension(new Twig_Extension_StringLoader());
        $twig->addFilter(new Twig_SimpleFilter('is_svg', [TwigFilters::class, 'is_svg']));
        $twig->addFilter(new Twig_SimpleFilter('svg', [TwigFilters::class, 'svg']));
        $twig->addFilter(new Twig_SimpleFilter('tel', [TwigFilters::class, 'tel']));
        $twig->addFilter(new Twig_SimpleFilter('parse', [TwigFilters::class, 'parse']));
        $twig->addFilter(new Twig_SimpleFilter('target', [TwigFilters::class, 'target']));
        $twig->addFilter(new Twig_SimpleFilter('video_src', [TwigFilters::class, 'video_src']));
        $twig->addFilter(new Twig_SimpleFilter('estimated_time', [TwigFilters::class, 'estimated_time']));
        $twig->addFilter(new Twig_SimpleFilter('pluralize', [TwigFilters::class, 'pluralize']));
        $twig->addFilter(new Twig_SimpleFilter('exist', [TwigFilters::class, 'exist']));
        $twig->addFilter(new Twig_SimpleFilter('srcset', [TwigFilters::class, 'srcset']));
        $twig->addFilter(new Twig_SimpleFilter('srcsetdynamic', [TwigFilters::class, 'srcsetdynamic']));

        $twig->addFilter(new Twig_SimpleFilter('ldate', [TwigFilters::class, 'ldate']));
        $twig->addFilter(new Twig_SimpleFilter('css', [TwigFilters::class, 'css']));
        $twig->addFilter(new Twig_SimpleFilter('js', [TwigFilters::class, 'js']));
        $twig->addFilter(new Twig_SimpleFilter('clean', [TwigFilters::class, 'clean']));
        $twig->addFilter(new Twig_SimpleFilter('is_first', [TwigFilters::class, 'is_first']));
        $twig->addFilter(new Twig_SimpleFilter('is_last', [TwigFilters::class, 'is_last']));
        $twig->addFilter(new Twig_SimpleFilter('padding', [TwigFilters::class, 'padding']));
        $twig->addFilter(new Twig_SimpleFilter('override', [TwigFilters::class, 'override']));
        $twig->addFilter(new Twig_SimpleFilter('strtotime', [TwigFilters::class, 'strtotime']));
        $twig->addFilter(new Twig_SimpleFilter('inline_css', [TwigFilters::class, 'inline_css']));

        return $twig;
    }

    public static function inline_css($html, ...$css)
    {
        $cssToInlineStyles = new CssToInlineStyles();

        return $cssToInlineStyles->convert(
            $html,
            implode("\n", $css)
        );
    }

    public static function strtotime($date)
    {
        return strtotime($date) * 1000;
    }

    public static function tel($value)
    {
        return preg_replace('/\D/', '', $value);
    }

    public static function clean($array)
    {
        if (is_array($array)) {
            foreach ($array as $key => $sub_array) {
                $result = self::clean($sub_array);
                if ($result === false) {
                    unset($array[$key]);
                } else {
                    $array[$key] = $result;
                }
            }
        }

        if (empty($array)) {
            return false;
        }

        return $array;
    }

    public static function parse($text, $class = '')
    {
        $class = $class ? 'class="' . $class . '"' : '';
        $text = preg_replace("/~(.+?)~/", '<span ' . $class . '>$1</span>', $text);
        $text = preg_replace("/\-\-(.+?)\-\-/", '<small>$1</small>', $text);

        return $text;
    }

    public static function is_first($array, $index)
    {
        $first = false;
        $exist = array_search($index, $array);

        if ($exist !== false && (
                $exist == 0 ||
                ((int)$array[$exist - 1] != (int)$index - 1))
        ) {
            $first = true;
        }

        return $first;
    }

    public static function is_last($array, $index)
    {
        $last = false;
        $exist = array_search($index, $array);
        if ($exist !== false && (
                (isset($array[$exist + 1]) && (int)$array[$exist + 1] != (int)$index + 1) ||
                ((int)$exist == count($array) - 1)
            )
        ) {
            $last = true;
        }

        return $last;
    }

    public static function target($value)
    {
        return '_blank' == $value ? 'target="_blank" rel="noreferrer noopener"' : '';
    }

    public static function svg($value)
    {

        $file = get_stylesheet_directory() . '/../../../' . str_replace(get_site_url(), '', $value);

        if (file_exists($file)) {
            return file_get_contents($file);
        }

        return $value;
    }

    public static function override($section, $options, $groupName)
    {
        if (isset($options[$groupName])) {
            $override = $section['override'];
            $clean = self::clean($section);
            $section = $override == 0 ? $options[$groupName] : ($override == 1 ? (array_merge($options[$groupName], $clean)) : $section);
            $section['show'] = isset($section['show']) ? $section['show'] : true;
        }

        if ($groupName == 'fast_contact') {
            $ac = ActiveCampaign::getForm($section['ac_form']);
            $section['ac'] = $ac ?: null;
        }

        return $section;
    }


    public static function is_svg($value)
    {
        return $value['subtype'] === 'svg+xml';
    }

    public static function video_src($video)
    {
        if ($video['type'] == 'embed') {
            preg_match('/src="(.+?)"/', $video['embed'], $matches);
            return $matches[1];
        }

        return $video['file'];
    }

    public static function estimated_time($content = '', $word_per_minute = 300)
    {
        $clean_content = strip_shortcodes($content);
        $clean_content = strip_tags($clean_content);
        $word_count = str_word_count($clean_content);
        $time = ceil($word_count / $word_per_minute);
        return $time;
    }

    public static function js($content = '')
    {
        $file = get_stylesheet_directory() . '/' . $content;

        if (file_exists($file)) {
            return '<script type="text/javascript">' . file_get_contents($file) . '</script>';
        }

        return false;
    }

    public static function css($content = '')
    {
        $file = get_stylesheet_directory() . '/' . $content;

        if (file_exists($file)) {
            return '<style>' . file_get_contents($file) . '</style>';
        }

        return false;
    }

    public static function srcset($content, $width, $height, $sizes1x = [], $retina = [2])
    {
        $srcset = [[Timber\ImageHelper::resize($content, $width, $height), $width]];

        foreach ($sizes1x as $size) {
            $srcset[] = [Timber\ImageHelper::resize($content, $width * $size / $width, $height * $size / $width), $size];
        }

        $retinaScale = ((int)$retina[0]) > 3 ? 3 : $retina[0];
        array_shift($retina);

        foreach ($retina as $size) {
            $_content = Timber\ImageHelper::resize($content, $width * $size / $width, $height * $size / $width);
            $srcset[] = [Timber\ImageHelper::retina_resize($_content, $retinaScale), $size * $retinaScale];
        }

        $_srcset = [];
        foreach ($srcset as $src) {
            $_srcset[] = $src[0] . ' ' . $src[1] . 'w';
        }

        return implode(', ', $_srcset);
    }

    public static function srcsetdynamic($content, $breakpoints)
    {
        $srcset = [];
        $isRetina = substr($content['title'], -3) == '@2x';
        $_width = $isRetina ? $content['width'] / 2 : $content['width'];
        $_height = $isRetina ? $content['height'] / 2 : $content['height'];

        $widthPercent = round($_width / 1920 * 100);
        $widthPixels = 1920 * $widthPercent / 100;

        foreach ($breakpoints as $size) {
            if ($size <= $_width * 2) {

                $_content = Timber\ImageHelper::resize($content['url'], $_width * $size / $_width, $_height * $size / $_width);
                $srcset[] = [$_content, $size];
                $srcset[] = [Timber\ImageHelper::retina_resize($_content, 2), $size * 2];
            }
        }


        $_srcset = [];

        foreach ($srcset as $src) {
            $_srcset[] = $src[0] . ' ' . $src[1] . 'w';
        }

        $sizes = "data-sizes=\"(max-width: 1920px) " . $widthPercent . "vw, " . $widthPixels . "px\"";

        return "data-srcset=\"" . implode(', ', $_srcset) . "\" " . $sizes;
    }

    public static function padding($image)
    {
        $_width = substr($image['title'], -3) == '@2x' ? $image['width'] / 2 : $image['width'];
        $_height = substr($image['title'], -3) == '@2x' ? $image['height'] / 2 : $image['height'];

        $width = round($_width / 1920 * 100);

        $padding = ($_height / $_width * 100) - ($width == 100 ? 0 : $width);

        $padding = $padding ? "padding-bottom:$padding%;" : '';
        $width = $width ? "width:$width%;" : '';

        return $padding ? "style=\"$padding $width\"" : '';
    }

    public static function pluralize($n = 1, $one = "", $few = "", $many = "")
    {
        $text = [$one, $few, $many];

        $rules = [
            'default' => function ($n) {
                return ($n != 1);
            },
            'pl_PL' => function ($n) {
                return ($n == 1 ? 0 : ($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 10 || $n % 100 >= 20) ? 1 : 2));
            }
        ];

        if (isset($rules[get_locale()])) {
            return $text[$rules[get_locale()]($n)];
        } else {
            return $text[$rules['default']($n)];
        }
    }

    public static function ldate($date, $format = 'Y-m-d', $timezone = '')
    {
        $time = new DateTime($date);

        if ($timezone && $timezone = new DateTimeZone($timezone)) {
            $timezone && $time->setTimezone($timezone);
        }

        return $time->format($format);
    }
}