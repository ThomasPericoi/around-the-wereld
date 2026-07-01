<?php
/* ADMIN
--------------------------------------------------------------- */

// Add options page
add_action('acf/init', function () {
    if (!function_exists('acf_add_options_page')) {
        return;
    }

    acf_add_options_page(array(
        'page_title'    => __('Theme options', 'around-the-wereld'),
        'menu_title'    => __('Theme options', 'around-the-wereld'),
        'menu_slug'     => 'theme-general-settings',
        'capability'    => 'edit_pages',
        'redirect'      => false,
        'position'      => 2,
        'update_button' => __('Update', 'around-the-wereld'),
        'updated_message' => __('All good', 'around-the-wereld'),
        'icon_url'      => 'dashicons-admin-settings',
    ));
});

// Add and delete roles
function atw_manage_user_roles()
{
    remove_role('subscriber');
    remove_role('contributor');
    remove_role('author');
}
add_action('after_switch_theme', 'atw_manage_user_roles');
