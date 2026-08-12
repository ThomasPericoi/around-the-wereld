    <?php get_template_part('template-parts/keywords-banner'); ?>
    <?php get_template_part('template-parts/contact-map'); ?>
    
    </main>

    <!-- Footer -->
    <footer id="footer">
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
                        <?php if (have_rows('footer_socials', 'options')) : ?>
                            <ul class="socials footer-socials" aria-label="<?= esc_attr__('Social networks', 'around-the-wereld'); ?>">
                                <?php while (have_rows('footer_socials', 'options')) : the_row(); ?>
                                    <?php
                                    $social_icon = get_sub_field('icon');
                                    $social_link = get_sub_field('link');
                                    $social_icons = array('facebook', 'instagram', 'linkedin');
                                    ?>
                                    <?php if (!empty($social_link['url']) && in_array($social_icon, $social_icons, true)) : ?>
                                        <?php
                                        $social_title = !empty($social_link['title']) ? $social_link['title'] : ucfirst($social_icon);
                                        $social_target = !empty($social_link['target']) ? $social_link['target'] : '_self';
                                        $social_rel = $social_target === '_blank' ? ' rel="noopener noreferrer"' : '';
                                        ?>
                                        <li>
                                            <a class="social" href="<?= esc_url($social_link['url']); ?>" target="<?= esc_attr($social_target); ?>"<?= $social_rel; ?> aria-label="<?= esc_attr($social_title); ?>">
                                                <?php get_template_part("assets/medias/icons/socials/{$social_icon}.svg"); ?>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                <?php endwhile; ?>
                            </ul>
                        <?php endif; ?>
                    </div>

                    <div class="col">
                        <?php if ($footer_title_2 = get_field('footer_title_2', 'options')) : ?>
                            <h2 class="h3-size title"><?= wp_kses_post($footer_title_2); ?></h2>
                        <?php endif; ?>
                        <?php
                        $footer_primary_cta = atw_get_cta('footer_primary', 'options');
                        $footer_secondary_cta = atw_get_cta('footer_secondary', 'options');
                        ?>
                        <?php if ($footer_primary_cta || $footer_secondary_cta) : ?>
                            <div class="btn-wrapper">
                                <?php if ($footer_primary_cta) : ?>
                                    <?= $footer_primary_cta; ?>
                                <?php endif; ?>
                                <?php if ($footer_secondary_cta) : ?>
                                    <?= $footer_secondary_cta; ?>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sub Footer -->
        <div id="sub-footer">
            <div class="container container-lg">
                <div class="sub-footer-menu">
                    <?php if (has_nav_menu('footer-submenu')) : ?>
                        <?php wp_nav_menu(array('theme_location' => 'footer-submenu', 'container' => false, 'depth' => 1)); ?>
                    <?php endif; ?>
                </div>
                <div class="sub-footer-signature">
                    <?php get_template_part('template-parts/signature'); ?>
                </div>
                <?php if (get_field('footer_open_dyslexic', 'options')) : ?>
                    <!-- OpenDyslexic Toggle -->
                    <div class="sub-footer-dyslexic dyslexic-toggle">
                        <input type="checkbox" id="open-dyslexic" name="open-dyslexic" class="screen-reader-only" />
                        <label for="open-dyslexic"><?= esc_html(get_field('footer_open_dyslexic_label', 'options') ?: __('Enable OpenDyslexic', 'around-the-wereld')); ?></label>
                    </div>
                <?php else : ?>
                    <div class="sub-footer-dyslexic" aria-hidden="true"></div>
                <?php endif; ?>
            </div>
        </div>
    </footer>

    <?php wp_footer(); ?>
    </body>

    </html>
