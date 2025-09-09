<?php

/**
 * Карточка товара (Cannabinoids)
 */

global $product;
$product_id = get_the_ID();
$product = wc_get_product( $product_id ); // гарантируем WC_Product объект
?>

<a href="<?php the_permalink(); ?>" class="product__other_item product column">
    <?php if ( has_post_thumbnail() ) : ?>
        <img src="<?php echo esc_url( get_the_post_thumbnail_url( $product_id, 'medium' ) ); ?>" alt="<?php the_title_attribute(); ?>">
    <?php endif; ?>
    <h3><?php the_title(); ?></h3>
    <span class="price">from <?php echo $product ? $product->get_price_html() : ''; ?></span>
</a>
