<?php
$labels = [
    'name' => __('Events', 'around-the-wereld'),
    'singular_name' => __('Event', 'around-the-wereld'),
    'add_new' => __('Add event', 'around-the-wereld'),
    'add_new_item' => __('Add event', 'around-the-wereld'),
    'edit_item' => __('Edit event', 'around-the-wereld'),
    'new_item' => __('New event', 'around-the-wereld'),
    'view_item' => __('View event', 'around-the-wereld'),
    'view_items' => __('View events', 'around-the-wereld'),
    'search_items' => __('Search events', 'around-the-wereld'),
    'not_found' => __('No events found.', 'around-the-wereld'),
    'all_items' => __('All events', 'around-the-wereld'),
    'not_found_in_trash' => __('No events found in trash.', 'around-the-wereld'),
    'parent_item_colon' => '',
];

$args = [
    'labels' => $labels,
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true,
    'show_in_menu' => true,
    'show_in_rest' => true,
    'query_var' => true,
    'hierarchical' => false,
    'capability_type' => 'post',
    'supports' => [
        'title',
        'editor',
        'thumbnail',
        'excerpt',
        'revisions',
    ],
    'taxonomies' => [],
    'has_archive' => 'agenda',
    'rewrite' => ['slug' => 'agenda/%event_date%', 'with_front' => true],
    'menu_position' => 5,
    'menu_icon' => 'dashicons-calendar-alt',
];

add_rewrite_tag('%event_date%', '([0-9]{4}-[0-9]{2}-[0-9]{2})');

register_post_type('event', $args);

add_rewrite_rule(
    '^agenda/([0-9]{4}-[0-9]{2}-[0-9]{2})/([^/]+)/?$',
    'index.php?event=$matches[2]&event_date=$matches[1]',
    'top'
);

// Resolve the event date used in dated permalinks.
function atw_get_event_permalink_date($post_id)
{
    $event_date = function_exists('get_field') ? get_field('event_start_date', $post_id) : get_post_meta($post_id, 'event_start_date', true);
    $event_timestamp = $event_date ? strtotime($event_date) : false;

    if (!$event_timestamp) {
        $event_timestamp = get_post_time('U', false, $post_id);
    }

    return date_i18n('Y-m-d', $event_timestamp);
}

// Replace the event date placeholder in single event permalinks.
function atw_event_permalink($post_link, $post)
{
    if ($post->post_type !== 'event') {
        return $post_link;
    }

    return str_replace('%event_date%', atw_get_event_permalink_date($post->ID), $post_link);
}
add_filter('post_type_link', 'atw_event_permalink', 10, 2);

// Prepare the event archive query for custom date ordering.
function atw_order_event_archive_by_start_date($query)
{
    if (is_admin() || !$query->is_main_query() || !$query->is_post_type_archive('event')) {
        return;
    }

    $query->set('meta_key', 'event_start_date');
    $query->set('orderby', 'meta_value');
    $query->set('order', 'ASC');
    $query->set('atw_event_archive_order', true);
    $query->set('meta_query', [
        [
            'key' => 'event_start_date',
            'compare' => 'EXISTS',
            'type' => 'DATETIME',
        ],
    ]);
}
add_action('pre_get_posts', 'atw_order_event_archive_by_start_date');

// Sort future events first, then past events in reverse date order.
function atw_order_event_archive_clauses($clauses, $query)
{
    if (!$query->get('atw_event_archive_order')) {
        return $clauses;
    }

    global $wpdb;

    $now = current_time('mysql');
    $event_date = "{$wpdb->postmeta}.meta_value";

    $clauses['orderby'] = $wpdb->prepare(
        "CASE WHEN {$event_date} >= %s THEN 0 ELSE 1 END ASC,
        CASE WHEN {$event_date} >= %s THEN {$event_date} END ASC,
        CASE WHEN {$event_date} < %s THEN {$event_date} END DESC",
        $now,
        $now,
        $now
    );

    return $clauses;
}
add_filter('posts_clauses', 'atw_order_event_archive_clauses', 10, 2);

// Count upcoming events for small UI badges.
function atw_get_upcoming_events_count()
{
    static $count = null;

    if ($count !== null) {
        return $count;
    }

    $query = new WP_Query([
        'post_type' => 'event',
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'fields' => 'ids',
        'meta_query' => [
            [
                'key' => 'event_start_date',
                'value' => current_time('mysql'),
                'compare' => '>=',
                'type' => 'DATETIME',
            ],
        ],
        'suppress_filters' => false,
    ]);

    $count = absint($query->found_posts);

    wp_reset_postdata();

    return $count;
}

// Detect whether a menu item points to the event archive.
function atw_is_event_archive_menu_item($item)
{
    $archive_link = get_post_type_archive_link('event');

    if (!$archive_link || empty($item->url)) {
        return false;
    }

    $item_url = untrailingslashit($item->url);
    $archive_url = untrailingslashit($archive_link);

    return $item_url === $archive_url;
}

// Add the upcoming events count to the agenda item in the header menu.
function atw_add_event_count_to_menu_item($title, $item, $args, $depth)
{
    if (($args->theme_location ?? '') !== 'header-menu' || $depth !== 0 || !atw_is_event_archive_menu_item($item)) {
        return $title;
    }

    $count = atw_get_upcoming_events_count();

    return sprintf(
        '%1$s <span class="menu-count-badge" aria-label="%2$s">%3$s</span>',
        $title,
        esc_attr(sprintf(_n('%s upcoming event', '%s upcoming events', $count, 'around-the-wereld'), number_format_i18n($count))),
        esc_html(number_format_i18n($count))
    );
}
add_filter('nav_menu_item_title', 'atw_add_event_count_to_menu_item', 10, 4);

// Mark the agenda menu item active on event archive and single event pages.
function atw_add_event_menu_current_class($classes, $item, $args, $depth)
{
    if (($args->theme_location ?? '') !== 'header-menu' || $depth !== 0 || !atw_is_event_archive_menu_item($item)) {
        return $classes;
    }

    if (is_post_type_archive('event') || is_singular('event')) {
        $classes[] = 'current-menu-item';
    }

    return $classes;
}
add_filter('nav_menu_css_class', 'atw_add_event_menu_current_class', 10, 4);
