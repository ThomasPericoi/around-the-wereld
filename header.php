<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>
    <a class="skip-link sr-only-focusable" href="#main"><?= esc_html__('Skip to content', 'around-the-wereld'); ?></a>

    <!-- Header -->
    <header id="header" class="header">
        <div class="container container-lg">
            <div class="inner-header">
                <?php if (function_exists('the_custom_logo') && has_custom_logo()) : ?>
                    <?php the_custom_logo(); ?>
                <?php else : ?>
                    <a href="<?= esc_url(home_url('/')); ?>" class="sitename logo-link"><?= esc_html(get_bloginfo('name')); ?></a>
                <?php endif; ?>
                <nav class="nav-wrapper" aria-label="<?= esc_attr__('Main navigation', 'around-the-wereld'); ?>">
                    <?php wp_nav_menu([
                        'theme_location' => 'header-menu',
                        'menu_class' => 'menu menu-header',
                        'items_wrap' => '<ul id="header-menu" class="%2$s">%3$s</ul>',
                        'container' => false,
                        'depth' => 3,
                        'fallback_cb' => false,
                    ]); ?>
                </nav>
                <div class="menu-toggle-col">
                    <div class="menu-toggle-wrapper">
                        <input id="menu-toggle" class="menu-toggle" type="checkbox" role="button" tabindex="0" aria-controls="header-menu" aria-expanded="false" aria-label="<?= esc_attr__('Open menu', 'around-the-wereld'); ?>" />
                        <div class="menu-toggle-open" aria-hidden="true">
                            <span aria-hidden="true"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main id="main" aria-hidden="false">
