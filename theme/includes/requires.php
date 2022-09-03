<?php
$neededPlugins = 0;

if (!class_exists('Timber')) {
    add_action('admin_notices', function () {
        echo '<div class="error"><p>Timber not activated</p></div>';
    });

    $neededPlugins++;

    return '<p>Timber not activated</p>';
}

if (!class_exists('ACF') or !function_exists('get_field')) {
    add_action('admin_notices', function () {
        echo '<div class="error"><p>ACF not activated</p></div>';
    });

    $neededPlugins++;

    return '<p>ACF not activated</p>';
}

if ($neededPlugins) {
    return 'Check the ACF or Timber plugin';
}
