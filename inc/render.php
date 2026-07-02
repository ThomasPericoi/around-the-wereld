<?php
/* RENDER
--------------------------------------------------------------- */
// Get CTA
function atw_get_cta($selector_prefix, $post_id = false, $sub_field = false)
{
    $color = $sub_field ? get_sub_field("{$selector_prefix}_cta_color") : get_field("{$selector_prefix}_cta_color", $post_id);
    $type = $sub_field ? get_sub_field("{$selector_prefix}_cta_type") : get_field("{$selector_prefix}_cta_type", $post_id);

    $text_cta = $sub_field ? get_sub_field("{$selector_prefix}_cta_text") : get_field("{$selector_prefix}_cta_text", $post_id);
    $text_icon = $sub_field ? get_sub_field("{$selector_prefix}_cta_text_icon") : get_field("{$selector_prefix}_cta_text_icon", $post_id);
    $text_icon_direction = $sub_field ? get_sub_field("{$selector_prefix}_cta_text_icon_direction") : get_field("{$selector_prefix}_cta_text_icon_direction", $post_id);
    $image_cta = $sub_field ? get_sub_field("{$selector_prefix}_cta_image") : get_field("{$selector_prefix}_cta_image", $post_id);

    if (!$text_cta && empty($image_cta['image'])) {
        return '';
    }

    if ($type === 'text' && !empty($text_cta['title'])) {
        $target = !empty($text_cta['target']) ? $text_cta['target'] : '_self';
        $rel = $target === '_blank' ? ' rel="noopener noreferrer"' : '';

        return sprintf(
            '<a class="btn btn-%1$s btn-icon-%2$s %3$s" href="%4$s" target="%5$s"%6$s>%7$s</a>',
            esc_attr($color),
            esc_attr($text_icon),
            esc_attr($text_icon_direction),
            esc_url($text_cta['url']),
            esc_attr($target),
            $rel,
            esc_html($text_cta['title'])
        );
    } elseif ($type === 'image' && !empty($image_cta['image'])) {
        $target = !empty($image_cta['target']) ? $image_cta['target'] : '_self';
        $rel = $target === '_blank' ? ' rel="noopener noreferrer"' : '';

        return sprintf(
            '<a class="btn btn-%1$s btn-image" href="%2$s" title="%3$s" aria-label="%3$s" target="%4$s"%5$s>
                <img src="%6$s" alt="%3$s" />
            </a>',
            esc_attr($color),
            esc_url($image_cta['url']),
            esc_attr($image_cta['title']),
            esc_attr($target),
            $rel,
            esc_url($image_cta['image'])
        );
    }

    return '';
}

// Render CTA
function atw_render_cta($selector_prefix, $post_id = false, $sub_field = false)
{
    echo atw_get_cta($selector_prefix, $post_id, $sub_field);
}

// Render Link CTA
function atw_render_link_cta($link, $color = 'primary', $icon = 'arrow-right', $icon_direction = 'icon-after')
{
    if (empty($link['url']) || empty($link['title'])) {
        return;
    }

    $target = !empty($link['target']) ? $link['target'] : '_self';
    $rel = $target === '_blank' ? ' rel="noopener noreferrer"' : '';

    echo sprintf(
        '<a class="btn btn-%1$s btn-icon-%2$s %3$s" href="%4$s" target="%5$s"%6$s>%7$s</a>',
        esc_attr($color),
        esc_attr($icon),
        esc_attr($icon_direction),
        esc_url($link['url']),
        esc_attr($target),
        $rel,
        esc_html($link['title'])
    );
}
