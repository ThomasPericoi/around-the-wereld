<?php
/* BLOCKS
--------------------------------------------------------------- */

// Register blocks
function atw_register_acf_blocks()
{
    $blocks = ['button', 'gallery', 'pdf-viewer'];

    foreach ($blocks as $block) {
        $block_directory = __DIR__ . '/blocks/' . $block;

        if (file_exists($block_directory . '/block.json')) {
            register_block_type($block_directory);
        }
    }
}
add_action('init', 'atw_register_acf_blocks');

// Register custom block category
function atw_register_block_category($categories, $post)
{
    return array_merge(
        $categories,
        array(
            array(
                'slug' => 'around-the-wereld-block',
                'title' => __('Wereldcafé', 'around-the-wereld'),
            ),
        )
    );
}
add_filter('block_categories_all', 'atw_register_block_category', 10, 2);
