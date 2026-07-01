<?php if ($args['images']): ?>
    <div class="galleries-wrapper">
        <div class="swiper gallery" aria-label="<?= $args['description']; ?>">
            <div class="swiper-wrapper">
                <?php foreach ($args['images'] as $image) : ?>
                    <figure class="swiper-slide">
                        <img src="<?= esc_url($image['url']); ?>" alt="<?= esc_attr($image['alt']); ?>" />
                    </figure>
                <?php endforeach; ?>
            </div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
            <div class="controls">
                <div class="swiper-pagination"></div>
                <button class="js-maximize" aria-label="<?= __('Switch to fullscreen', 'around-the-wereld'); ?>"><?php get_template_part('assets/medias/icons/maximize.svg'); ?></button>
            </div>
        </div>

        <div class="fullscreen-modal hidden" role="dialog" aria-modal="true" aria-label="<?= __('Fullscreen gallery', 'around-the-wereld'); ?>">
            <div class="fullscreen-modal-content">
                <div class="swiper fullscreen-gallery" aria-label="<?= $args['description']; ?>">
                    <div class="swiper-wrapper">
                        <?php foreach ($args['images'] as $image) : ?>
                            <figure class="swiper-slide">
                                <img src="<?= esc_url($image['url']); ?>" alt="<?= esc_attr($image['alt']); ?>" />
                                <?php if ($image['caption']) : ?>
                                    <figcaption><?= esc_html($image['caption']); ?></figcaption>
                                <?php endif; ?>
                            </figure>
                        <?php endforeach; ?>
                    </div>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
                    <div class="controls">
                        <div class="swiper-pagination"></div>
                        <button class="js-minimize" aria-label="<?= __('Close fullscreen', 'around-the-wereld'); ?>"><?php get_template_part('assets/medias/icons/minimize.svg'); ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
