<?php
$keyword_rows = get_field('keywords_banner_items', 'options');
$keywords = array();

if (is_array($keyword_rows)) {
    foreach ($keyword_rows as $keyword_row) {
        if (!empty($keyword_row['keyword'])) {
            $keywords[] = trim($keyword_row['keyword']);
        }
    }
}

if (empty($keywords)) {
    return;
}
?>

<!-- Keywords Banner -->
<section id="keywords-banner" class="keywords-banner js-alwaysInView" aria-label="<?= esc_attr__('Wereldcafe keywords', 'around-the-wereld'); ?>">
    <div class="keywords-banner-track">
        <?php for ($i = 0; $i < 2; $i++) : ?>
            <ul class="keywords-banner-list" <?= $i > 0 ? 'aria-hidden="true"' : ''; ?>>
                <?php foreach ($keywords as $keyword) : ?>
                    <li class="h5-size"><?= esc_html($keyword); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endfor; ?>
    </div>
</section>
