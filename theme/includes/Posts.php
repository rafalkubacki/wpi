<?php


class Posts
{
    public static $slugs = [
        'new-post' => "post/new"
    ];

    public static function getSlug($postType, $lang = null)
    {

        if (!empty(self::$slugs) && isset(self::$slugs[$postType])) {
            return self::$slugs[$postType];
        }

        return $postType;
    }

    public static function register()
    {
        register_post_type('new-post', [
            'labels' => [
                'name' => __('New posts', THEME_DOMAIN),
                'singular_name' => __('New post', THEME_DOMAIN),
                'add_new' => __('Add post', THEME_DOMAIN),
                'add_new_item' => __('Add new post', THEME_DOMAIN),
                'edit_item' => __('Edit post', THEME_DOMAIN),
                'new_item' => __('New post', THEME_DOMAIN),
                'all_items' => __('All posts', THEME_DOMAIN),
                'view_item' => __('Show posts', THEME_DOMAIN),
                'search_items' => __('Search post', THEME_DOMAIN),
                'not_found' => __('Not found', THEME_DOMAIN),
                'not_found_in_trash' => __('Not found in trash', THEME_DOMAIN)
            ],
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => [
                'slug' => self::getSlug('new-post')
            ],
            'capability_type' => 'post',
            'has_archive' => false,
            'hierarchical' => false,
            'menu_position' => null,
            'menu_icon' => 'dashicons-format-gallery',
            'supports' => ['title', 'thumbnail', 'page-attributes']
        ]);
    }
}