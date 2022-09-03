<?php
$context = Timber::context();
$context['posts'] = new Timber\PostQuery();

$sticky = get_option( 'sticky_posts' );
$args = array(
    'posts_per_page' => 1,
    'post__in' => $sticky,
    'ignore_sticky_posts' => 1
);
$context['sticky_posts'] = new Timber\PostQuery($args);
if ( isset( $sticky[0] ) ) {
    $context['sticky_post'] = $context['sticky_posts'][0];
}

Timber::render('index.twig', $context);
