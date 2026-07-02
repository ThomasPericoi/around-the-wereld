<?php
$map_title = get_field('contact_map_title', 'options');
$map_text = get_field('contact_map_text', 'options');

$map_latitude = get_field('contact_map_latitude', 'options');
$map_longitude = get_field('contact_map_longitude', 'options');
if (!is_numeric($map_latitude) || !is_numeric($map_longitude)) {
    return;
}
$map_marker_title = get_field('contact_map_marker_title', 'options');
$map_marker_subtitle = get_field('contact_map_marker_subtitle', 'options');
$map_zoom = absint(get_field('contact_map_zoom', 'options') ?: 15);
$map_latitude = (float) $map_latitude;
$map_longitude = (float) $map_longitude;
$map_zoom = min(19, max(1, $map_zoom));

$map_button = atw_get_cta('contact_map_button', 'options');
?>

<!-- Contact Map -->
<section id="contact-map" class="contact-map js-alwaysInView">
    <div class="container">
        <div class="contact-map-grid">
            <div class="contact-map-content formatted">
                <?php if ($map_title) : ?>
                    <h2 class="h3-size"><?= esc_html($map_title); ?></h2>
                <?php endif; ?>
                <?php if ($map_text) : ?>
                    <div class="contact-map-text formatted">
                        <?= wp_kses_post($map_text); ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="contact-map-frame">
                <div
                    class="contact-map-leaflet"
                    role="img"
                    aria-label="<?= esc_attr($map_title ?: __('Wereldcafe location map', 'around-the-wereld')); ?>"
                    data-leaflet-map
                    data-latitude="<?= esc_attr(sprintf('%.7F', $map_latitude)); ?>"
                    data-longitude="<?= esc_attr(sprintf('%.7F', $map_longitude)); ?>"
                    data-zoom="<?= esc_attr($map_zoom); ?>"
                    data-marker-title="<?= esc_attr($map_marker_title); ?>"
                    data-marker-subtitle="<?= esc_attr($map_marker_subtitle); ?>">
                </div>
            </div>
        </div>
        <?php if ($map_button) : ?>
            <div class="contact-map-actions btn-wrapper">
                <?= $map_button; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
