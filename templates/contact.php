<?php
/* Template Name: Contact page */
get_header(); ?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

        <?php
        $post_id = get_the_ID();
        $content = trim(get_the_content());
        $hero_background = get_field('page_hero_background');
        $hero_backgrounds = array('primary', 'primary-dark', 'primary-light', 'secondary', 'secondary-dark', 'secondary-light', 'tertiary', 'tertiary-dark', 'tertiary-light', 'white', 'black', 'content-color');
        $hero_classes = array(
            'hero',
            'hero-simple',
            has_post_thumbnail() ? 'hero-has-media' : false,
            in_array($hero_background, $hero_backgrounds, true) ? 'hero-bg-' . $hero_background : false,
        );
        $hero_classes_attr = implode(' ', array_filter($hero_classes));
        ?>

        <!-- Hero -->
        <section id="hero-<?= esc_attr($post_id); ?>" class="<?= esc_attr($hero_classes_attr); ?>">
            <div class="container container-sm">
                <div class="breadcrumbs">
                    <?php if (function_exists('rank_math_the_breadcrumbs')) rank_math_the_breadcrumbs(); ?>
                </div>
                <h1><?= esc_html(get_the_title()); ?></h1>
                <?php if ($subtitle = get_field('page_subtitle')) : ?>
                    <h2 class="p-size"><?= wp_kses_post($subtitle); ?></h2>
                <?php endif; ?>
                <?php if (has_post_thumbnail()) : ?>
                    <figure class="hero-media">
                        <?= get_the_post_thumbnail($post_id, 'full'); ?>
                        <?php if (($thumbnail = get_post(get_post_thumbnail_id())) && ($excerpt = $thumbnail->post_excerpt)) : ?>
                            <figcaption><?= esc_html($excerpt); ?></figcaption>
                        <?php endif; ?>
                    </figure>
                <?php endif; ?>
            </div>
        </section>

        <?php if ($content) : ?>
            <!-- Content -->
            <section id="content-<?= esc_attr($post_id); ?>" class="contact-content">
                <div class="container container-sm formatted">
                    <?php the_content(); ?>
                </div>
            </section>
        <?php endif; ?>

        <?php
        $form_title = get_field('contact_form_title');
        $form_id = sanitize_text_field((string) get_field('contact_form_shortcode'));
        ?>

        <?php if ($form_title || $form_id) : ?>
            <!-- Contact Form -->
            <section id="contact-form-<?= esc_attr($post_id); ?>" class="contact-form">
                <div class="container container-sm formatted">
                    <?php if ($form_title) : ?>
                        <h2 class="h3-size"><?= wp_kses_post($form_title); ?></h2>
                    <?php endif; ?>
                    <?php if ($form_id) : ?>
                        <?= do_shortcode('[contact-form-7 id="' . esc_attr($form_id) . '"]'); ?>
                    <?php endif; ?>
                </div>
            </section>
        <?php endif; ?>

<?php endwhile;
endif; ?>

<?php get_footer(); ?>
