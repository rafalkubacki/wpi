<?php

if (!class_exists('Timber')) {
  add_action('admin_notices', function () {
    echo '<div class="error"><p>Timber not activated</p></div>';
  });

  return '<p>Timber not activated</p>';
}

if (!class_exists('ACF')) {
  add_action('admin_notices', function () {
    echo '<div class="error"><p>ACF not activated</p></div>';
  });

  return '<p>ACF not activated</p>';
}

// DEFINITIONS
define('THEME_DOMAIN', wp_get_theme()['TextDomain']);
define('THEME_VERSION', wp_get_theme()['Version']);
define('PER_PAGE', get_option('posts_per_page'));

require_once __DIR__ . '/includes/autoload.php';

Timber::$dirname = ['views'];

Ajax::init();
Mail::$templatesLocation = [__DIR__, __DIR__ . '/views/email'];

class StarterSite extends TimberSite
{
  public function __construct()
  {
    add_theme_support( 'title-tag' );
    add_theme_support('post-formats', []);
    add_theme_support('post-thumbnails');
    add_theme_support('menus');
    add_theme_support('html5', ['comment-list', 'comment-form', 'search-form', 'gallery', 'caption']);
//     add_theme_support('woocommerce');

    add_filter('timber_context', [$this, 'addToContext']);
    add_filter('get_twig', [TwigFilters::class, 'register']);

    add_action('init', [Posts::class, 'register']);
    add_action('init', [Taxonomies::class, 'register']);


    $this->optionsPage();
    $this->options = AcfAdds::getOptions();

    new Base();
    new Assets();

    parent::__construct();
  }

  private function optionsPage()
  {
    // Check function exists.
    if( function_exists('acf_add_options_page') ) {

      // Register options page.
      $option_page = acf_add_options_page(array(
        'page_title'    => __('Advanced Custom Fields Options Page'),
        'menu_title'    => __('Custom Options'),
        'menu_slug'     => 'acf-options',
        'capability'    => 'edit_posts',
        'redirect'      => false
      ));
    }
  }

  public function addToContext($context)
  {
    $context['menu'] = new TimberMenu('main-menu');
    $context['theme']->version = THEME_VERSION;

    $context['site'] = $this;
    $context['options'] = $this->options;

    return $context;
  }
}

new StarterSite();
