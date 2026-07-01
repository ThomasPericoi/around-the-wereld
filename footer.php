</main>

<!-- Footer -->
<footer id="footer">
    <!-- Pre Footer -->
    <?php if ($url = get_field('prefooter_url', 'options')) : ?>
        <?php
        $prefooter_classes = array(get_field('prefooter_background_shadow', 'options') ? 'shadowed' : false);
        $prefooter_classes_attr = implode(' ', array_filter($prefooter_classes));
        $prefooter_background = get_field('prefooter_background', 'options');
        $prefooter_cta_styles = array($prefooter_background ? 'background-image: url(' . esc_url($prefooter_background) . ')' : false);
        $prefooter_cta_styles_attr = implode('; ', array_filter($prefooter_cta_styles));
        ?>
        <div id="pre-footer" class="<?= esc_attr($prefooter_classes_attr); ?>">
            <div class="container">
                <a class="cta-block" href="<?= esc_url($url); ?>" style="<?= esc_attr($prefooter_cta_styles_attr); ?>">
                    <?php if ($introduction = get_field('prefooter_introduction', 'options')) : ?>
                        <h2 class="h3-size title"><?= wp_kses_post($introduction); ?></h2>
                    <?php endif; ?>
                </a>
            </div>
        </div>
    <?php endif; ?>
    <!-- Main Footer -->
    <div id="main-footer">
        <div class="container">
            <div class="cols-wrapper">
                <div class="col">
                    <?php if ($footer_title_1 = get_field('footer_title_1', 'options')) : ?>
                        <h2 class="h3-size title"><?= wp_kses_post($footer_title_1); ?></h2>
                    <?php endif; ?>
                    <?php if ($description = get_field('footer_description', 'options')) : ?>
                        <div class="description formatted">
                            <?= wp_kses_post($description); ?>
                        </div>
                    <?php endif; ?>
                    <?php if (have_rows('footer_contacts', 'options')) : ?>
                        <nav class="addresses" aria-label="<?= esc_attr__('Contact details', 'around-the-wereld'); ?>">
                            <ul class="addresses">
                                <?php while (have_rows('footer_contacts', 'options')) : the_row(); ?>
                                    <?php
                                    $contact_type = get_sub_field('type');
                                    $contact_text = get_sub_field('text');
                                    ?>
                                    <?php switch ($contact_type):
                                        case 'address': ?>
                                            <li class="address"><span><?php get_template_part('assets/medias/icons/map-marker.svg'); ?><?= esc_html($contact_text); ?></span></li>
                                        <?php break;
                                        case 'mail': ?>
                                            <li class="address"><a href="mailto:<?= esc_attr(sanitize_email($contact_text)); ?>"><?php get_template_part('assets/medias/icons/envelop.svg'); ?><?= esc_html($contact_text); ?></a></li>
                                        <?php break;
                                        case 'phone': ?>
                                            <li class="address"><a href="tel:<?= esc_attr(preg_replace('/[^0-9+]/', '', $contact_text)); ?>"><?php get_template_part('assets/medias/icons/phone.svg'); ?><?= esc_html($contact_text); ?></a></li>
                                    <?php break;
                                    endswitch; ?>
                                <?php endwhile; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>

                <div class="col">
                    <?php if ($footer_title_2 = get_field('footer_title_2', 'options')) : ?>
                        <h2 class="h3-size title"><?= wp_kses_post($footer_title_2); ?></h2>
                    <?php endif; ?>
                    <?php if (get_field('footer_primary_cta_text', 'options') || get_field('footer_primary_cta_image', 'options') || get_field('footer_secondary_cta_text', 'options') || get_field('footer_secondary_cta_image', 'options')) : ?>
                        <div class="btn-wrapper">
                            <?php if (get_field('footer_primary_cta_text', 'options') || get_field('footer_primary_cta_image', 'options')) : ?>
                                <?php atw_render_cta('footer_primary', 'options'); ?>
                            <?php endif; ?>
                            <?php if (get_field('footer_secondary_cta_text', 'options') || get_field('footer_secondary_cta_image', 'options')) : ?>
                                <?php atw_render_cta('footer_secondary', 'options'); ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <?php if (get_field('theme_open_dyslexic', 'options')) : ?>
                        <!-- OpenDyslexic Toggle -->
                        <div class="dyslexic-toggle">
                            <input type="checkbox" id="open-dyslexic" name="open-dyslexic" class="screen-reader-only" />
                            <label for="open-dyslexic"><?= esc_html(get_field('footer_open_dyslexic_label', 'options') ?: __('Enable OpenDyslexic', 'around-the-wereld')); ?></label>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Sub Footer -->
    <div id="sub-footer">
        <div class="container container-lg">
            <div class="menu menu-footer">
                <span>
                    <?php
                    printf(
                        esc_html__('%1$s | %2$s %3$d | %4$s', 'around-the-wereld'),
                        esc_html__('Copyrights', 'around-the-wereld'),
                        esc_html(get_bloginfo('name')),
                        esc_html(wp_date('Y')),
                        esc_html__('All rights reserved', 'around-the-wereld')
                    );
                    ?>
                </span>
                <?php if (has_nav_menu('footer-submenu')) : ?>
                    <?php wp_nav_menu(array('theme_location' => 'footer-submenu', 'container' => false, 'depth' => 1)); ?>
                <?php endif; ?>
            </div>
            <?php get_template_part('template-parts/signature'); ?>

        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>

</html>
