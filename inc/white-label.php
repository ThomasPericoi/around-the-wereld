<?php
/* WHITE LABEL
--------------------------------------------------------------- */

// Change login logo
function atw_change_login_logo()
{ ?>
    <style type="text/css">
        #login h1 a,
        .login h1 a {
            background-image: url(<?= esc_url(get_template_directory_uri() . '/assets/medias/images/Wereldcafé_color.svg'); ?>);
            width: 250px;
            height: 200px;
            background-size: 250px 200px;
            background-repeat: no-repeat;
            padding-bottom: 15px;
        }
    </style>
<?php }
add_action('login_enqueue_scripts', 'atw_change_login_logo');

// Change admin bar logo
function atw_change_admin_bar_logo()
{
    $favicon_url = esc_url(get_template_directory_uri() . '/assets/medias/images/favicon.png');
    echo '
    <style type="text/css">
        #wpadminbar #wp-admin-bar-wp-logo > .ab-item .ab-icon:before {
            background-image: url(' . $favicon_url . ') !important;
            background-position: 0 0;
            background-size: cover;
            color:rgba(0, 0, 0, 0);
        }
        #wpadminbar #wp-admin-bar-wp-logo.hover > .ab-item .ab-icon {
            background-position: 0 0;
        }
    </style>
    ';
}
add_action('wp_before_admin_bar_render', 'atw_change_admin_bar_logo');

// Change admin footer text
function atw_change_admin_footer_text()
{
    echo wp_kses_post(__('Powered by <a href="https://wordpress.org" target="_blank" rel="noopener noreferrer">WordPress</a> | Theme created by <a href="https://thomaspericoi.com/" target="_blank" rel="noopener noreferrer">Thomas Pericoi</a>', 'around-the-wereld'));
}
add_filter('admin_footer_text', 'atw_change_admin_footer_text');

// Add admin widgets
function atw_custom_dashboard_help()
{
    echo wp_kses_post(__('This theme is created by <a href="https://thomaspericoi.com/" target="_blank" rel="noopener noreferrer">Thomas Pericoi</a>.', 'around-the-wereld'));
}

function atw_add_admin_widgets()
{
    wp_add_dashboard_widget('custom_help_widget', __('Credits', 'around-the-wereld'), 'atw_custom_dashboard_help');
}
add_action('wp_dashboard_setup', 'atw_add_admin_widgets');
