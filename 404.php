<?php get_header(); ?>

<?php
$title = get_field('404_title', 'options') ?: __('Page not found', 'around-the-wereld');
$subtitle = get_field('404_subtitle', 'options') ?: __('Are you lost?', 'around-the-wereld');
$button_label = get_field('404_redirect_button_label', 'options') ?: __('Return to the home page', 'around-the-wereld');
?>

<!-- Hero -->
<section id="error-404" class="hero home-404">
    <div class="container container-sm">
        <h1><?= wp_kses_post($title); ?></h1>
        <h2 class="h3-size"><?= wp_kses_post($subtitle); ?></h2>
        <a class="btn btn-primary" href="<?= esc_url(home_url('/')); ?>"><?= esc_html($button_label); ?></a>
    </div>
</section>

<?php get_template_part('template-parts/latest', 'posts', array(
    'amount' => 3,
    'title' => __('Our latest posts', 'around-the-wereld'),
)); ?>

<?php get_footer(); ?>
