<?php
/**
 * Template part for displaying results in search pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package kipr
 */
// Получаем данные поста
$post_id = get_the_ID();
$post_title = get_the_title();
$post_excerpt = get_the_excerpt();
$post_url = get_permalink();
$post_thumbnail = get_the_post_thumbnail_url($post_id, 'medium'); // Миниатюра поста
?>

<a href="<?php echo esc_url($post_url); ?>" class="new-items__item _p_rel _bg_fff product__featured-link">
    <div class="product__featured-item">
        <?php if ($post_thumbnail) : ?>
            <img src="<?php echo esc_url($post_thumbnail); ?>" alt="<?php echo esc_attr($post_title); ?>" class="img">
        <?php endif; ?>
        
    </div>
    <div class="product__featured-inform">
        <h3 class="size-36"><?php echo esc_html($post_title); ?></h3>
        <div class="text size-18"><?php echo esc_html($post_excerpt); ?></div>
    </div>
</a>
