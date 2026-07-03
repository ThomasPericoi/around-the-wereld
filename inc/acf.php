<?php
/* ACF
--------------------------------------------------------------- */

// Save ACF local JSON inside the theme.
function atw_save_acf_groups_json($path)
{
    return get_stylesheet_directory() . '/inc/acf-json';
}
add_filter('acf/settings/save_json', 'atw_save_acf_groups_json');

// Load ACF local JSON from the theme.
function atw_load_acf_groups_json($paths)
{
    $paths[] = get_stylesheet_directory() . '/inc/acf-json';
    return $paths;
}
add_filter('acf/settings/load_json', 'atw_load_acf_groups_json');

// Keep exported ACF JSON filenames readable and stable.
function atw_name_acf_groups_json($filename, $post, $load_path)
{
    $filenames = array(
        'group_66e98ea5566a2' => 'group_theme_options_footer',
        'group_68501b4beff8f' => 'group_theme_options_404',
        'group_67f684034efa9' => 'group_page_front_page',
        'group_680b86bb90c03' => 'group_page_posts_page',
        'group_67f67834a0f5b' => 'group_component_button',
        'group_68823982990cd' => 'group_block_button',
        'group_6883f97c37661' => 'group_block_gallery',
        'group_680a44823fac2' => 'group_block_pdf_viewer',
        'group_around_the_wereld_contact' => 'group_around_the_wereld_contact',
        'group_around_the_wereld_event' => 'group_around_the_wereld_event',
        'group_around_the_wereld_menu' => 'group_around_the_wereld_menu',
        'group_around_the_wereld_page' => 'group_around_the_wereld_page',
        'group_around_the_wereld_theme_options' => 'group_around_the_wereld_theme_options',
        'group_theme_options_header' => 'group_theme_options_header',
        'group_theme_options_keywords_banner' => 'group_theme_options_keywords_banner',
        'group_theme_options_contact_map' => 'group_theme_options_contact_map',
    );

    if (!empty($post['key']) && !empty($filenames[$post['key']])) {
        return $filenames[$post['key']] . '.json';
    }

    return $filename;
}
add_filter('acf/json/save_file_name', 'atw_name_acf_groups_json', 10, 3);

// Warn admins when the required ACF plugin is missing.
function atw_display_acf_missing_notice()
{
    if (class_exists('ACF') || !current_user_can('activate_plugins')) {
        return;
    }

    echo '<div class="notice notice-error"><p>' . esc_html__('Advanced Custom Fields is required by this theme.', 'around-the-wereld') . '</p></div>';
}
add_action('admin_notices', 'atw_display_acf_missing_notice');

// Remove conflicts between footnotes and ACF
add_action('init', function () {
    remove_action('init', 'register_block_core_footnotes');
}, 1);
remove_filter('sanitize_post_meta_footnotes', '_wp_filter_post_meta_footnotes');
