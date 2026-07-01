<?php
/* POST TYPE(S)
--------------------------------------------------------------- */

// Register post types
function atw_register_custom_post_types()
{
    $post_types = ['event'];

    foreach ($post_types as $post_type) {
        $post_type_file = __DIR__ . '/post-types/' . $post_type . '.php';

        if (file_exists($post_type_file)) {
            include_once $post_type_file;
        }
    }
}
add_action('init', 'atw_register_custom_post_types');

function atw_flush_theme_rewrite_rules()
{
    atw_register_custom_post_types();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'atw_flush_theme_rewrite_rules');
