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
    'has_archive' => true,
    'rewrite' => ['slug' => 'agenda', 'with_front' => true],
    'menu_position' => 5,
    'menu_icon' => 'dashicons-calendar-alt',
];

register_post_type('event', $args);

function atw_order_event_archive_by_start_date($query)
{
    if (is_admin() || !$query->is_main_query() || !$query->is_post_type_archive('event')) {
        return;
    }

    $query->set('meta_key', 'event_start_date');
    $query->set('orderby', 'meta_value');
    $query->set('order', 'ASC');
    $query->set('meta_query', [
        [
            'key' => 'event_start_date',
            'value' => current_time('mysql'),
            'compare' => '>=',
            'type' => 'DATETIME',
        ],
    ]);
}
add_action('pre_get_posts', 'atw_order_event_archive_by_start_date');
