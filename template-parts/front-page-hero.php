<?php
$post_id = get_queried_object_id();
$hero_items = array(
    array(
        'key' => 'home_hero_primary',
        'class' => 'front-page-hero-card-half',
    ),
    array(
        'key' => 'home_hero_secondary',
        'class' => 'front-page-hero-card-half',
    ),
    array(
        'key' => 'home_hero_wide',
        'class' => 'front-page-hero-card-wide',
    ),
);

$visible_items = array_filter($hero_items, function ($item) use ($post_id) {
    return (bool) get_field($item['key'] . '_title', $post_id);
});

if (!$visible_items) {
    return;
}
?>

<!-- Front Page Hero -->
<section id="front-page-hero" class="front-page-hero">
    <div class="container container-lg">
        <div class="front-page-hero-grid">
            <?php foreach ($visible_items as $index => $item) :
                $key = $item['key'];
                $title = get_field($key . '_title', $post_id);
                $text = get_field($key . '_text', $post_id);
                $image = get_field($key . '_image', $post_id);
                $button_link = get_field($key . '_link', $post_id);
                $has_card_link = !empty($button_link['url']) && !empty($button_link['title']);
                $text_allowed_tags = wp_kses_allowed_html('post');
                $card_tag = $has_card_link ? 'a' : 'article';
                $card_attrs = '';

                if ($has_card_link) {
                    unset($text_allowed_tags['a']);

                    $button_target = !empty($button_link['target']) ? $button_link['target'] : '_self';
                    $button_rel = $button_target === '_blank' ? ' rel="noopener noreferrer"' : '';
                    $card_attrs = sprintf(
                        ' href="%1$s" target="%2$s"%3$s aria-label="%4$s"',
                        esc_url($button_link['url']),
                        esc_attr($button_target),
                        $button_rel,
                        esc_attr($button_link['title'])
                    );
                }
                ?>

                <<?= esc_attr($card_tag); ?> class="front-page-hero-card <?= esc_attr($item['class']); ?> <?= $index % 2 ? 'front-page-hero-card-alt' : ''; ?>"<?= $card_attrs; ?>>
                    <?php if ($image) : ?>
                        <figure class="front-page-hero-media">
                            <?= wp_get_attachment_image($image, 'large'); ?>
                        </figure>
                    <?php endif; ?>

                    <div class="front-page-hero-content formatted">
                        <h2 class="h2-size"><?= wp_kses_post($title); ?></h2>

                        <?php if ($text) : ?>
                            <div class="front-page-hero-text">
                                <?= wp_kses($text, $text_allowed_tags); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($has_card_link) : ?>
                            <div class="btn-wrapper front-page-hero-actions" aria-hidden="true">
                                <span class="btn btn-primary btn-icon-arrow-right icon-before">
                                    <?= esc_html($button_link['title']); ?>
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                </<?= esc_attr($card_tag); ?>>
            <?php endforeach; ?>
        </div>
    </div>
</section>
