<?php

/*
 * Append styles and scripts to site
 */

class Assets
{
    public function __construct()
    {
        add_action('wp_enqueue_scripts', [$this, 'registerStyles']);
        add_action('wp_enqueue_scripts', [$this, 'registerScripts']);
    }

    public function registerStyles()
    {
        $version = $_SERVER['SERVER_NAME'] === 'localhost' ? time() : THEME_VERSION;
        $page_template = get_page_template_slug() ? get_page_template_slug() : 'home';

        wp_enqueue_style('base', get_stylesheet_directory_uri() . '/assets/base.bundle.css', false, $version);
        wp_enqueue_style($page_template, get_stylesheet_directory_uri() . '/assets/' . $page_template . '.bundle.css', false, $version);
    }

    public function registerScripts()
    {
        $version = $_SERVER['SERVER_NAME'] === 'localhost' ? time() : THEME_VERSION;
        $page_template = get_page_template_slug() ? get_page_template_slug() : 'home';

        wp_enqueue_script('base', get_stylesheet_directory_uri() . '/assets/base.bundle.js', false, $version, true);
        wp_enqueue_script($page_template, get_stylesheet_directory_uri() . '/assets/' . $page_template . '.bundle.js', false, $version, true);
    }
}
