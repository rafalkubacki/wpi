<?php

class Helper
{
    public static function force404()
    {
        global $wp_query;
        status_header(404);
        nocache_headers();
        include get_query_template('404');
        die();
    }

    public static function paged()
    {
        global $paged;
        if (!isset($paged) || !$paged) {
            $paged = 1;
        }

        return $paged;
    }

    public static function perPage()
    {
        return (int)get_option('posts_per_page');
    }

    public static function offset($count)
    {
        return (Helper::paged() - 1) * Helper::perPage() + $count;
    }

    public static function getPageByTemplate($template)
    {
        $page = new Timber\PostQuery([
            'post_type' => 'page',
            'meta_key' => '_wp_page_template',
            'meta_value' => $template,
            'orderby' => 'menu_order',
            'order' => 'DESC',
            'post_per_page' => 1,
            'hierarchical' => 0
        ]);

        if (count($page)) {
            return $page[0];
        }

        return null;
    }


    public static function getPostTaxonomies($taxonomy, $postID)
    {
        $items = get_the_terms($postID, $taxonomy) ?: [];
        return $items;
    }


    public static function getPagination($query, $count = 5, $per_page = null, $lf = false)
    {
        $per_page = $per_page ?: self::perPage();
        $pagination = new stdClass();
        $outer = floor($count / 2);

        $pages = ceil($query->found_posts / $per_page);
        $pagination->current = Helper::paged();
        $pagination->total = $pages;
        $pagination->pages = [];

        $start = max(1, $pagination->current - $outer);
        $end = min($pages, $pagination->current + $outer);

        for ($page = $start; $page <= $end; $page++) {
            $pagination->pages[] = ['link' => get_pagenum_link($page), 'label' => $page];
        }

        if ($pagination->current < $pagination->total) {
            $pagination->next = ['link' => get_pagenum_link($pagination->current + 1)];
        }

        if ($pagination->current > 1) {
            $pagination->prev = ['link' => get_pagenum_link($pagination->current - 1)];
        }

        if ($lf && $start !== 1) {
            $pagination->first = ['link' => get_pagenum_link($pages), 'label' => 1];
        }

        if ($lf && $end !== $pages) {
            $pagination->last = ['link' => get_pagenum_link($pages), 'label' => $pages];
        }

        return $pagination;
    }
}
