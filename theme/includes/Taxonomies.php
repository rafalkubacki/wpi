<?php


class Taxonomies
{

    public static $slugs = [
        'new-taxonomy' => "taxonomy/new",
    ];

    public static function getSlug($taxType, $lang = null)
    {

        if (!empty(self::$slugs) && isset(self::$slugs[$taxType])) {
            return self::$slugs[$taxType];
        }

        return $taxType;
    }

    public static function register()
    {
        register_taxonomy('new-taxonomy', ['new-post'], [
            'labels' => [
                'name' => __('Categories', THEME_DOMAIN),
                'singular_name' => __('Category', THEME_DOMAIN),
                'add_new' => __('Add category', THEME_DOMAIN),
                'add_new_item' => __('Add new category', THEME_DOMAIN),
                'edit_item' => __('Edit category', THEME_DOMAIN),
                'new_item' => __('New category', THEME_DOMAIN),
                'all_items' => __('All categories', THEME_DOMAIN),
                'view_item' => __('Show categories', THEME_DOMAIN),
                'search_items' => __('Search category', THEME_DOMAIN),
                'not_found' => __('Not found', THEME_DOMAIN),
                'not_found_in_trash' => __('Not found in trash', THEME_DOMAIN)
            ],
            'public' => false,
            'publicly_queryable' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => ['slug' => self::getSlug('new-taxonomy')],
            'meta_box_cb' => false,
            'capability_type' => 'post',
            'has_archive' => false,
            'hierarchical' => false,
            'menu_position' => null
        ]);
    }
}