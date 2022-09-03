<?php

/* Redirects all 404 to home */
// wp_redirect(home_url(), 301);

$context = Timber::get_context();
Timber::render('404.twig', $context);
