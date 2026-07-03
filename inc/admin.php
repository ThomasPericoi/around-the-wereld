<?php
/* ADMIN
--------------------------------------------------------------- */

// Add the global theme options page for ACF fields.
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

// Remove default roles that are not needed for this site.
function atw_manage_user_roles()
{
    remove_role('subscriber');
    remove_role('contributor');
    remove_role('author');
}
add_action('after_switch_theme', 'atw_manage_user_roles');
