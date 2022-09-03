<?php
/**
 * Template Name: Homepage
 */

$context = Timber::get_context();
$context['posts'] = new Timber\PostQuery();

$page = new TimberPost();
$page->acf = get_fields($page);
$context['page'] = $page;

Timber::render('home.twig', $context);
