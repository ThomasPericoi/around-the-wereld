<?php
$cta_title = get_field('cta_section_title', 'options');

if (!$cta_title) {
    return;
}

$cta_text = get_field('cta_section_text', 'options');
$cta_image = get_field('cta_section_image', 'options');
$cta_button_link = get_field('cta_section_link', 'options');
$has_cta_link = !empty($cta_button_link['url']) && !empty($cta_button_link['title']);
$cta_text_allowed_tags = wp_kses_allowed_html('post');
$cta_tag = $has_cta_link ? 'a' : 'div';
$cta_attrs = '';

if ($has_cta_link) {
    unset($cta_text_allowed_tags['a']);

    $cta_button_target = !empty($cta_button_link['target']) ? $cta_button_link['target'] : '_self';
    $cta_button_rel = $cta_button_target === '_blank' ? ' rel="noopener noreferrer"' : '';
    $cta_attrs = sprintf(
        ' href="%1$s" target="%2$s"%3$s aria-label="%4$s"',
        esc_url($cta_button_link['url']),
        esc_attr($cta_button_target),
        $cta_button_rel,
        esc_attr($cta_button_link['title'])
    );
}
?>

<!-- CTA Section -->
<section id="cta-section" class="cta-section">
    <div class="container container-lg">
        <<?= esc_attr($cta_tag); ?> class="cta-section-inner <?= $cta_image ? 'cta-section-has-media' : ''; ?>"<?= $cta_attrs; ?>>
            <?php if ($cta_image) : ?>
                <figure class="cta-section-media">
                    <?= wp_get_attachment_image($cta_image, 'large'); ?>
                </figure>
            <?php endif; ?>

            <div class="cta-section-content formatted">
                <h2 class="h2-size"><?= wp_kses_post($cta_title); ?></h2>

                <?php if ($cta_text) : ?>
                    <div class="cta-section-text">
                        <?= wp_kses($cta_text, $cta_text_allowed_tags); ?>
                    </div>
                <?php endif; ?>

                <?php if ($has_cta_link) : ?>
                    <div class="btn-wrapper cta-section-actions" aria-hidden="true">
                        <span class="btn btn-primary btn-icon-arrow-right icon-before">
                            <?= esc_html($cta_button_link['title']); ?>
                        </span>
                    </div>
                <?php endif; ?>
            </div>
        </<?= esc_attr($cta_tag); ?>>
    </div>
</section>
