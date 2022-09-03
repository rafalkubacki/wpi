<?php

$context = Timber::get_context();
$page = new TimberPost();
$page->acf = get_fields($page);

$context['page'] = $page;
Timber::render('page.twig', $context);
