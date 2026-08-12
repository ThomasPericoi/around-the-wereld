<?php
/* Template Name: Menu page */
get_header(); ?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

        <?php
        $post_id = get_the_ID();
        $content = trim(get_the_content());
        $has_thumbnail = has_post_thumbnail();
        $thumbnail = $has_thumbnail ? get_post(get_post_thumbnail_id()) : false;
        $thumbnail_caption = $thumbnail ? $thumbnail->post_excerpt : '';
        $hero_background = get_field('page_hero_background');
        $subtitle = get_field('page_subtitle');
        $hero_backgrounds = array('primary', 'primary-dark', 'primary-light', 'secondary', 'secondary-dark', 'secondary-light', 'tertiary', 'tertiary-dark', 'tertiary-light', 'white', 'black', 'content-color');
        $hero_classes = array(
            'hero',
            'hero-simple',
            $has_thumbnail ? 'hero-has-media' : false,
            in_array($hero_background, $hero_backgrounds, true) ? 'hero-bg-' . $hero_background : false,
        );
        $hero_classes_attr = implode(' ', array_filter($hero_classes));
        $menu_sections = get_field('menu_sections') ?: array();
        $section_slugs = array();

        if (!empty($menu_sections)) {
            foreach ($menu_sections as $section_index => $section) {
                $section_title = !empty($section['section_title']) ? $section['section_title'] : sprintf(__('Section %d', 'around-the-wereld'), $section_index + 1);
                $base_slug = sanitize_title($section_title) ?: 'menu-section-' . ($section_index + 1);
                $slug = $base_slug;
                $suffix = 2;

                while (in_array($slug, $section_slugs, true)) {
                    $slug = $base_slug . '-' . $suffix;
                    $suffix++;
                }

                $section_slugs[] = $slug;
            }
        }
        ?>

        <!-- Hero -->
        <section id="hero-<?= esc_attr($post_id); ?>" class="<?= esc_attr($hero_classes_attr); ?>">
            <div class="container container-sm">
                <div class="breadcrumbs">
                    <?php if (function_exists('rank_math_the_breadcrumbs')) rank_math_the_breadcrumbs(); ?>
                </div>
                <h1 class="title-accent"><?= esc_html(get_the_title()); ?></h1>
                <?php if ($subtitle) : ?>
                    <h2 class="p-size"><?= wp_kses_post($subtitle); ?></h2>
                <?php endif; ?>
                <?php if ($has_thumbnail) : ?>
                    <figure class="hero-media">
                        <?= get_the_post_thumbnail($post_id, 'full'); ?>
                        <?php if ($thumbnail_caption) : ?>
                            <figcaption><?= esc_html($thumbnail_caption); ?></figcaption>
                        <?php endif; ?>
                    </figure>
                <?php endif; ?>
            </div>
        </section>

        <?php if ($content) : ?>
            <!-- Content -->
            <section id="content-<?= esc_attr($post_id); ?>" class="menu-content">
                <div class="container container-sm formatted">
                    <?php the_content(); ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if (!empty($menu_sections)) : ?>
            <!-- Menu -->
            <section id="menu-<?= esc_attr($post_id); ?>" class="menu-page">
                <div class="container container-lg">
                    <nav class="menu-sections-nav" aria-label="<?= esc_attr__('Menu sections', 'around-the-wereld'); ?>">
                        <ul>
                            <?php foreach ($menu_sections as $section_index => $section) : ?>
                                <?php if (!empty($section['section_title'])) : ?>
                                    <li>
                                        <a href="#<?= esc_attr($section_slugs[$section_index]); ?>">
                                            <?= esc_html($section['section_title']); ?>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </nav>

                    <div class="menu-sections">
                        <?php foreach ($menu_sections as $section_index => $section) : ?>
                            <?php
                            $section_title = !empty($section['section_title']) ? $section['section_title'] : sprintf(__('Section %d', 'around-the-wereld'), $section_index + 1);
                            $section_description = !empty($section['section_description']) ? $section['section_description'] : '';
                            $section_items = !empty($section['section_items']) ? $section['section_items'] : array();
                            ?>
                            <article id="<?= esc_attr($section_slugs[$section_index]); ?>" class="menu-section">
                                <header class="menu-section-header">
                                    <h2 class="h3-size"><?= esc_html($section_title); ?></h2>
                                    <?php if ($section_description) : ?>
                                        <div class="formatted">
                                            <?= wp_kses_post($section_description); ?>
                                        </div>
                                    <?php endif; ?>
                                </header>

                                <?php if ($section_items) : ?>
                                    <ul class="menu-items">
                                        <?php foreach ($section_items as $item) : ?>
                                            <?php
                                            $item_title = !empty($item['item_title']) ? $item['item_title'] : '';
                                            $item_description = !empty($item['item_description']) ? $item['item_description'] : '';
                                            $item_price = !empty($item['item_price']) ? $item['item_price'] : '';
                                            $item_label = !empty($item['item_label']) ? $item['item_label'] : '';
                                            $item_options = !empty($item['item_options']) ? array_values(array_filter($item['item_options'], function ($option) {
                                                return !empty($option['option_title']) || !empty($option['option_price']);
                                            })) : array();
                                            $item_classes = array(
                                                'menu-item',
                                                !empty($item['item_has_spacing']) ? 'menu-item-has-spacing' : false,
                                            );
                                            $item_classes_attr = implode(' ', array_filter($item_classes));
                                            ?>
                                            <?php if ($item_title || $item_description || $item_price || $item_options) : ?>
                                                <li class="<?= esc_attr($item_classes_attr); ?>">
                                                    <div class="menu-item-main">
                                                        <?php if ($item_title) : ?>
                                                            <h3 class="h5-size"><?= esc_html($item_title); ?></h3>
                                                        <?php endif; ?>
                                                        <?php if ($item_label) : ?>
                                                            <span class="menu-item-label"><?= esc_html($item_label); ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <?php if ($item_description) : ?>
                                                        <p><?= wp_kses_post($item_description); ?></p>
                                                    <?php endif; ?>
                                                    <?php if ($item_price) : ?>
                                                        <span class="menu-item-price"><?= esc_html($item_price); ?></span>
                                                    <?php endif; ?>
                                                    <?php if ($item_options) : ?>
                                                        <ul class="menu-item-options">
                                                            <?php foreach ($item_options as $option) : ?>
                                                                <?php
                                                                $option_title = !empty($option['option_title']) ? $option['option_title'] : '';
                                                                $option_price = !empty($option['option_price']) ? $option['option_price'] : '';
                                                                ?>
                                                                <?php if ($option_title || $option_price) : ?>
                                                                    <li>
                                                                        <?php if ($option_title) : ?>
                                                                            <span><?= esc_html($option_title); ?></span>
                                                                        <?php endif; ?>
                                                                        <?php if ($option_price) : ?>
                                                                            <strong><?= esc_html($option_price); ?></strong>
                                                                        <?php endif; ?>
                                                                    </li>
                                                                <?php endif; ?>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    <?php endif; ?>
                                                </li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>

<?php endwhile;
endif; ?>

<?php get_footer(); ?>
