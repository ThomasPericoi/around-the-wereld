<?php
/* INIT
--------------------------------------------------------------- */

// Set up the theme baseline: translations, supports, and menu locations.
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

// Remove WordPress emoji assets from the front-end and admin.
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

// Keep comments and pingbacks closed everywhere.
function atw_disable_comments_status()
{
    return false;
}
add_filter('comments_open', 'atw_disable_comments_status', 20, 2);
add_filter('pings_open', 'atw_disable_comments_status', 20, 2);

// Hide existing comments from public queries.
function atw_disable_comments_hide_existing_comments($comments)
{
    $comments = array();
    return $comments;
}
add_filter('comments_array', 'atw_disable_comments_hide_existing_comments', 10, 2);

// Remove the comments screen from the admin menu.
function atw_disable_comments_admin_menu()
{
    remove_menu_page('edit-comments.php');
}
add_action('admin_menu', 'atw_disable_comments_admin_menu');

// Redirect users away from the comments admin screen.
function atw_disable_comments_admin_menu_redirect()
{
    global $pagenow;
    if ($pagenow === 'edit-comments.php') {
        wp_redirect(admin_url());
        exit;
    }
}
add_action('admin_init', 'atw_disable_comments_admin_menu_redirect');

// Remove the comments dashboard widget.
function atw_disable_comments_dashboard()
{
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
}
add_action('admin_init', 'atw_disable_comments_dashboard');

// Remove the comments shortcut from the admin bar.
function atw_disable_comments_icon_admin_bar()
{
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('comments');
}
add_action('wp_before_admin_bar_render', 'atw_disable_comments_icon_admin_bar');

// Remove comment and trackback support from every post type.
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

// Hide the WordPress version generator tag.
function atw_remove_wordpress_version()
{
    return '';
}
add_filter('the_generator', 'atw_remove_wordpress_version');

// Replace detailed login errors with a generic message.
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

// Add the first post category slug as a body class on single posts.
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

// Detect whether a menu item points to the posts page.
function atw_is_posts_page_menu_item($item)
{
    $posts_page_id = (int) get_option('page_for_posts');

    if ($posts_page_id && (int) $item->object_id === $posts_page_id) {
        return true;
    }

    if (!$posts_page_id || empty($item->url)) {
        return false;
    }

    return untrailingslashit($item->url) === untrailingslashit(get_permalink($posts_page_id));
}

// Mark the blog menu item active on single post pages.
function atw_add_blog_menu_current_class($classes, $item, $args, $depth)
{
    if (($args->theme_location ?? '') !== 'header-menu' || $depth !== 0 || !is_singular('post')) {
        return $classes;
    }

    if (atw_is_posts_page_menu_item($item)) {
        $classes[] = 'current-menu-item';
    }

    return $classes;
}
add_filter('nav_menu_css_class', 'atw_add_blog_menu_current_class', 10, 4);

// Use the newest CSS file timestamp as the main stylesheet version.
function atw_get_stylesheet_version()
{
    static $version = null;

    if ($version !== null) {
        return $version;
    }

    $version = filemtime(get_stylesheet_directory() . '/style.css');
    $css_path = get_stylesheet_directory() . '/assets/css';

    if (!is_dir($css_path)) {
        return $version;
    }

    $css_directory = new RecursiveDirectoryIterator($css_path, FilesystemIterator::SKIP_DOTS);
    $css_files = new RecursiveIteratorIterator($css_directory);

    foreach ($css_files as $css_file) {
        if ($css_file->getExtension() === 'css') {
            $version = max($version, $css_file->getMTime());
        }
    }

    return $version;
}

// Version individual assets from their file timestamp.
function atw_get_asset_version($relative_path)
{
    $path = get_stylesheet_directory() . '/' . ltrim($relative_path, '/');

    if (file_exists($path)) {
        return filemtime($path);
    }

    return atw_get_stylesheet_version();
}

// Get and normalize the global contact map data once per request.
function atw_get_contact_map_data()
{
    static $map_data = null;

    if ($map_data !== null) {
        return $map_data;
    }

    if (!function_exists('get_field')) {
        return array();
    }

    $latitude = get_field('contact_map_latitude', 'options');
    $longitude = get_field('contact_map_longitude', 'options');

    if (!is_numeric($latitude) || !is_numeric($longitude)) {
        $map_data = array();
        return $map_data;
    }

    $map_data = array(
        'title' => get_field('contact_map_title', 'options'),
        'text' => get_field('contact_map_text', 'options'),
        'latitude' => (float) $latitude,
        'longitude' => (float) $longitude,
        'marker_title' => get_field('contact_map_marker_title', 'options'),
        'marker_subtitle' => get_field('contact_map_marker_subtitle', 'options'),
        'zoom' => min(19, max(1, absint(get_field('contact_map_zoom', 'options') ?: 15))),
        'button' => atw_get_cta('contact_map_button', 'options'),
    );

    return $map_data;
}

// Check whether the global contact map has enough data to load Leaflet.
function atw_current_page_has_contact_map()
{
    return !empty(atw_get_contact_map_data());
}

// Print Google Fonts early so typography starts loading as soon as possible.
function atw_enqueue_google_fonts()
{
    // Preconnect Google Fonts domains
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";

    // Load Google Fonts
    echo '<link href="https://fonts.googleapis.com/css2?family=Abril+Fatface&family=DynaPuff:wght@400..700&family=Space+Grotesk:wght@300..700&display=swap" rel="stylesheet">' . "\n";
}
add_action('wp_head', 'atw_enqueue_google_fonts', 0);

// Add initial document state classes before the CSS is fully applied.
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

// Register and enqueue theme styles only when they are needed.
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
    wp_register_style('style', get_stylesheet_uri(), array('reset', 'wp-core'), atw_get_stylesheet_version(), 'all');
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

// Register and enqueue theme scripts only when they are needed.
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
