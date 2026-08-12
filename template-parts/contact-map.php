<?php
$map_data = atw_get_contact_map_data();

if (!$map_data) {
    return;
}

$map_title = $map_data['title'];
$map_text = $map_data['text'];
$map_latitude = $map_data['latitude'];
$map_longitude = $map_data['longitude'];
$map_marker_title = $map_data['marker_title'];
$map_marker_subtitle = $map_data['marker_subtitle'];
$map_zoom = $map_data['zoom'];
$map_button = $map_data['button'];
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
