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

function atw_get_event_permalink_date($post_id)
{
    $event_date = function_exists('get_field') ? get_field('event_start_date', $post_id) : get_post_meta($post_id, 'event_start_date', true);
    $event_timestamp = $event_date ? strtotime($event_date) : false;

    if (!$event_timestamp) {
        $event_timestamp = get_post_time('U', false, $post_id);
    }

    return date_i18n('Y-m-d', $event_timestamp);
}

function atw_event_permalink($post_link, $post)
{
    if ($post->post_type !== 'event') {
        return $post_link;
    }

    return str_replace('%event_date%', atw_get_event_permalink_date($post->ID), $post_link);
}
add_filter('post_type_link', 'atw_event_permalink', 10, 2);

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
