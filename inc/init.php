<?php
/* INIT
--------------------------------------------------------------- */

// Add theme supports
function atw_setup_theme()
{
    load_theme_textdomain('around-the-wereld', get_template_directory() . '/languages');

    add_theme_support('post-thumbnails');
    add_theme_support('title-tag');
    add_theme_support('automatic-feed-links');
    add_theme_support('align-wide');
    add_theme_support('responsive-embeds');
    add_theme_support('editor-styles');
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
        'header-text' => array('site-title', 'site-description'),
    ));
    add_theme_support(
        'html5',
        array(
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
            'navigation-widgets',
        )
    );
    add_theme_support('disable-custom-colors');
    add_theme_support('disable-custom-font-sizes');

    register_nav_menus(
        array(
            'header-menu' => __('Header', 'around-the-wereld'),
            'footer-submenu' => __('Footer submenu', 'around-the-wereld'),
        )
    );
}
add_action('after_setup_theme', 'atw_setup_theme');

// Disable emojis
function atw_disable_emojis()
{
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
}
add_action('init', 'atw_disable_emojis');

// Close comments on the front-end
function atw_disable_comments_status()
{
    return false;
}
add_filter('comments_open', 'atw_disable_comments_status', 20, 2);
add_filter('pings_open', 'atw_disable_comments_status', 20, 2);

// Hide existing comments
function atw_disable_comments_hide_existing_comments($comments)
{
    $comments = array();
    return $comments;
}
add_filter('comments_array', 'atw_disable_comments_hide_existing_comments', 10, 2);

// Remove comments page in menu
function atw_disable_comments_admin_menu()
{
    remove_menu_page('edit-comments.php');
}
add_action('admin_menu', 'atw_disable_comments_admin_menu');

// Redirect any user trying to access comments page
function atw_disable_comments_admin_menu_redirect()
{
    global $pagenow;
    if ($pagenow === 'edit-comments.php') {
        wp_redirect(admin_url());
        exit;
    }
}
add_action('admin_init', 'atw_disable_comments_admin_menu_redirect');

// Remove comments metabox from dashboard
function atw_disable_comments_dashboard()
{
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
}
add_action('admin_init', 'atw_disable_comments_dashboard');

// Remove comments links from admin bar
function atw_disable_comments_icon_admin_bar()
{
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('comments');
}
add_action('wp_before_admin_bar_render', 'atw_disable_comments_icon_admin_bar');

// Disable support for comments and trackbacks in post types
function atw_disable_comments_post_types_support()
{
    $post_types = get_post_types();
    foreach ($post_types as $post_type) {
        if (post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
}
add_action('admin_init', 'atw_disable_comments_post_types_support');

// Remove Wordpress version
function atw_remove_wordpress_version()
{
    return '';
}
add_filter('the_generator', 'atw_remove_wordpress_version');

// Hide Wordpress errors
function atw_hide_wordpress_errors()
{
    return __('An error occurred!', 'around-the-wereld');
}
add_filter('login_errors', 'atw_hide_wordpress_errors');

// Disable xmlrpc.php
add_filter('xmlrpc_enabled', '__return_false');
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');

// Remove Wordpress admin bar
// add_filter('show_admin_bar', '__return_false');

// Add category slug to body classes
function atw_add_category_slug_to_body($classes)
{
    if (is_singular('post')) {
        global $post;
        $category = get_the_category($post->ID);
        if ($category && ! is_wp_error($category)) {
            $classes[] = sanitize_html_class($category[0]->slug);
        }
    }
    return $classes;
}
add_filter('body_class', 'atw_add_category_slug_to_body');

function atw_get_stylesheet_version()
{
    static $version = null;

    if ($version !== null) {
        return $version;
    }

    $version = filemtime(get_stylesheet_directory() . '/style.css');
    $css_directory = new RecursiveDirectoryIterator(get_stylesheet_directory() . '/assets/css', FilesystemIterator::SKIP_DOTS);
    $css_files = new RecursiveIteratorIterator($css_directory);

    foreach ($css_files as $css_file) {
        if ($css_file->getExtension() === 'css') {
            $version = max($version, $css_file->getMTime());
        }
    }

    return $version;
}

function atw_get_asset_version($relative_path)
{
    $path = get_stylesheet_directory() . '/' . ltrim($relative_path, '/');

    if (file_exists($path)) {
        return filemtime($path);
    }

    return atw_get_stylesheet_version();
}

function atw_current_page_has_contact_map()
{
    if (!function_exists('get_field')) {
        return false;
    }

    $latitude = get_field('contact_map_latitude', 'options');
    $longitude = get_field('contact_map_longitude', 'options');

    return is_numeric($latitude) && is_numeric($longitude);
}

function atw_enqueue_google_fonts()
{
    // Preconnect Google Fonts domains
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";

    // Load Google Fonts
    echo '<link href="https://fonts.googleapis.com/css2?family=Abril+Fatface&family=DynaPuff:wght@400..700&family=Space+Grotesk:wght@300..700&display=swap" rel="stylesheet">' . "\n";
}
add_action('wp_head', 'atw_enqueue_google_fonts', 0);

function atw_print_document_state_script()
{
    ?>
    <script>
        document.documentElement.classList.add('js-enabled');

        try {
            if (sessionStorage.getItem('dyslexicMode') === 'true') {
                document.documentElement.classList.add('is-dyslexic');
            }
        } catch (error) {
            document.documentElement.classList.remove('is-dyslexic');
        }
    </script>
    <?php
}
add_action('wp_head', 'atw_print_document_state_script', 1);

// Add stylesheets
function atw_enqueue_theme_stylesheets()
{
    if (!is_admin()) {
        wp_deregister_style('wp-block-library');
        wp_dequeue_style('wp-block-library');
        wp_dequeue_style('dashicons');
    }
    wp_register_style('reset', get_template_directory_uri() . '/assets/css/inc/reset.css', array(), null, 'all');
    wp_register_style('wp-core', get_template_directory_uri() . '/assets/css/inc/wp-core.css', array(), null, 'all');
    wp_register_style('swiper', 'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css', array(), null, 'all');
    wp_register_style('leaflet', 'https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css', array(), '1.9.4', 'all');
    wp_register_style('style', get_stylesheet_uri(), array(), atw_get_stylesheet_version(), 'all');
    wp_enqueue_style('reset');
    wp_enqueue_style('wp-core');
    if (is_singular() && has_block('acf/gallery')) {
        wp_enqueue_style('swiper');
    }
    if (atw_current_page_has_contact_map()) {
        wp_enqueue_style('leaflet');
    }
    wp_enqueue_style('style');
}
add_action('wp_enqueue_scripts', 'atw_enqueue_theme_stylesheets');

// Add scripts
function atw_enqueue_theme_scripts()
{
    wp_register_script('swiper', 'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js', array(), null, true);
    wp_register_script('leaflet', 'https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js', array(), '1.9.4', true);
    wp_register_script('usefool', get_template_directory_uri() . '/assets/js/usefool.js', array(), atw_get_asset_version('assets/js/usefool.js'), true);
    wp_register_script('ascii-printer', get_template_directory_uri() . '/assets/js/ascii-printer.min.js', array('usefool'), atw_get_asset_version('assets/js/ascii-printer.min.js'), true);
    wp_register_script('script', get_template_directory_uri() . '/assets/js/main.js', array('usefool', 'ascii-printer'), atw_get_asset_version('assets/js/main.js'), true);
    if (is_singular() && has_block('acf/gallery')) {
        wp_enqueue_script('swiper');
    }
    if (atw_current_page_has_contact_map()) {
        wp_enqueue_script('leaflet');
    }
    wp_enqueue_script('usefool');
    wp_enqueue_script('ascii-printer');
    wp_enqueue_script('script');
}
add_action('wp_enqueue_scripts', 'atw_enqueue_theme_scripts');
