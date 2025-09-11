<?php
get_header();

$category = get_queried_object();
if (!$category || !isset($category->term_id)) {
    echo '<p>Error: Product category not found.</p>';
    exit;
}

// Массив для хранения ID уже выведенных товаров
$displayed_products = [];

$popular_args = [
    'post_type'      => 'product',
    'posts_per_page' => -1,
    'tax_query'      => [[
        'taxonomy' => 'product_cat',
        'field'    => 'id',
        'terms'    => $category->term_id
    ]],
    'meta_query'     => [[
        'key'     => 'total_sales',
        'value'   => '1',
        'compare' => '='
    ]]
];

$popular_products = new WP_Query($popular_args);

// Сохраняем ID популярных товаров
if ($popular_products->have_posts()) {
    while ($popular_products->have_posts()) : $popular_products->the_post();
        $displayed_products[] = get_the_ID();
    endwhile;
    wp_reset_postdata();
}

// 2️⃣ Новинки (new_tovar = 1)
$new_args = [
    'post_type'      => 'product',
    'posts_per_page' => 10,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'tax_query'      => [[
        'taxonomy' => 'product_cat',
        'field'    => 'id',
        'terms'    => $category->term_id
    ]],
    'meta_query'     => [[
        'key'     => 'new_tovar',
        'value'   => '1',
        'compare' => '='
    ]],
    'post__not_in'   => $displayed_products, // Исключаем уже выведенные товары
];
$new_products = new WP_Query($new_args);

// Сохраняем ID товаров из второй секции
if ($new_products->have_posts()) {
    while ($new_products->have_posts()) : $new_products->the_post();
        $displayed_products[] = get_the_ID();
    endwhile;
    wp_reset_postdata();
}

// 3️⃣ Рекомендуемые товары (опять фильтр по total_sales = 1)
$general_args = [
    'post_type'      => 'product',
    'posts_per_page' => -1,
    'post__not_in'   => $displayed_products, // Исключаем уже выведенные товары
    'tax_query'      => [[
        'taxonomy' => 'product_cat',
        'field'    => 'id',
        'terms'    => $category->term_id
    ]],
    'meta_query'     => [[
        'key'     => 'total_sales',
        'value'   => '1',
        'compare' => '='
    ]]
];
$general_products = new WP_Query($general_args);

// Сохраняем ID товаров из третьей секции
if ($general_products->have_posts()) {
    while ($general_products->have_posts()) : $general_products->the_post();
        $displayed_products[] = get_the_ID();
    endwhile;
    wp_reset_postdata();
}

// 4️⃣ Последняя секция: "Other products" — товары без total_sales и new_tovar
$other_args = [
    'post_type'      => 'product',
    'posts_per_page' => -1,
    'post__not_in'   => $displayed_products, // Исключаем уже выведенные товары
    'tax_query'      => [[
        'taxonomy' => 'product_cat',
        'field'    => 'id',
        'terms'    => $category->term_id,
        'operator' => 'NOT IN'
    ]]
];
$other_products = new WP_Query($other_args);
?>


<main class="main">
<?php
// Динамически добавляем стили с помощью PHP
echo '<style>
    .cosmetic__banner::before {
        content: "";
        background-image: url("' . get_field('image_in_banner_product') . '");
    }
</style>';
?>
    <section class="banner cosmetic__banner">
        <div class="banner__container container flex">
            <div class="banner__container-item direction">
                <ul class="breadcumbs">
                    <li>
                        <a href="/">
                            <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8.24951 20.1665L8.25258 14.6665L13.7495 14.6665V20.1665" stroke="white" stroke-width="1.5" />
                                <path d="M17.8766 20.1666L20.1684 8.24992L11.0017 1.83325L1.8335 8.24992L4.12664 20.1666H17.8766Z" stroke="white" stroke-width="1.5" stroke-linejoin="round" />
                            </svg>
                        </a>
                    </li>
                    <li>/</li>
                    <li><?php single_cat_title(); ?></li> 
                </ul>
                <div class="cosmetic__banner_text _banner_text_cntr _flex_col">
                    <h1 class="title size-50 _heading"><?php single_cat_title(); ?></h1>
                </div>
                <div class="_heading_title _fw_400 _fs_30 _color_fff">
                    <?php echo category_description( $category->term_id ); ?>
                </div>
            </div>
        </div>
        <img src="<?php echo get_template_directory_uri(); ?>/img/home/cannabis-marijuana-leaf-closeup.png" alt="" class="banner__img img">
        <img src="<?php echo get_template_directory_uri(); ?>/img/background/background-list/bk1.png" class="back__img bk1 back__left">
    </section>

    <?php if ($popular_products->have_posts()) : ?>
    <div class="product-grid _cosmectic__products _top _flex_center_stretch container category-product">
        <?php while ($popular_products->have_posts()) : $popular_products->the_post(); 
            global $product;
            $product_id = get_the_ID();
        ?>
            <a href="<?php the_permalink(); ?>" class="new-items__item _p_rel _bg_fff">
                <?php if (has_post_thumbnail()) : ?>
                    <img src="<?php echo get_the_post_thumbnail_url($product_id, 'medium'); ?>" alt="<?php the_title(); ?>" class="img">
                <?php endif; ?>
                <h3 class="size-20"><?php the_title(); ?></h3>
                <?php
$prod_obj = wc_get_product( $product_id );
if ( $prod_obj ) {
    $stock_status = $prod_obj->get_stock_status();
    $stock_qty = $prod_obj->managing_stock() ? (int) $prod_obj->get_stock_quantity() : null;

    $is_oos = ( $stock_status === 'outofstock' || ( $stock_qty !== null && $stock_qty <= 0 ) );

    if ( $is_oos ) {
        echo '<span class="out-of-stock-badge">Out of stock</span>';
    }
}
?>


                <span class="size-26 price _p_abs">from <?php echo $product->get_price_html(); ?></span>
            </a>
        <?php endwhile; wp_reset_postdata(); ?>
    </div>
<?php endif; ?>

<section class="description__section container">
    <?php if( have_rows('advantages_product') ): ?>
        <ul class="description__list _flex_center _pad_60">
        <?php while( have_rows('advantages_product') ): the_row(); 
            $advantages_product_text = get_sub_field('advantages_product_text');
        ?>
            <li class="description__item _green_btn _btn">
                <p class="_color_fff _fw_300 _fs_32"><?php echo $advantages_product_text; ?></p>
            </li>
        <?php endwhile; ?>
        </ul>
    <?php endif; ?>
</section>

<?php if ($new_products->have_posts()) : ?>
    <section class="popular background__yellow">
        <div class="popular__container _p_rel column container">
            <div class="popular__title">
                <h2 class="title size-50 center">popular items</h2>
            </div>

            <div class="swiper popular__slider">
                <div class="swiper-wrapper">
                    <?php while ($new_products->have_posts()) : $new_products->the_post(); 
                        global $product;
                        $product_id = get_the_ID();
                    ?>
                        <div class="swiper-slide">
                            <a href="<?php the_permalink(); ?>" class="product new-items__item _slider_item _p_rel">
                                <?php
$prod_obj = wc_get_product( $product_id );
if ( $prod_obj ) {
    $stock_status = $prod_obj->get_stock_status();
    $stock_qty = $prod_obj->managing_stock() ? (int) $prod_obj->get_stock_quantity() : null;

    $is_oos = ( $stock_status === 'outofstock' || ( $stock_qty !== null && $stock_qty <= 0 ) );

    if ( $is_oos ) {
        echo '<span class="out-of-stock-badge">Out of stock</span>';
    } else {
        // Для New arrivals — показываем New (только если товар сюда попал)
        echo '<span class="new">New</span>';
    }
}
?>


                                <?php if (has_post_thumbnail()) : ?>
                                    <img src="<?php echo get_the_post_thumbnail_url($product_id, 'medium'); ?>" alt="<?php the_title(); ?>" class="img">
                                <?php endif; ?>
                                <h3 class="_fs_26"><?php the_title(); ?></h3>
                                <span class="size-26 price">from <?php echo $product->get_price_html(); ?></span>
                            </a>
                        </div>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
            </div>
            <div class="popular__swiper-button-next swiper-button-next arrow-next arrow"></div>
            <div class="popular__swiper-button-prev swiper-button-prev arrow-prev arrow"></div>
        </div>
    </section>
<?php endif; ?>

<?php if ($general_products->have_posts()) : ?>
    <section class="product__recomend_section _p_rel _flex_col_center container">
        <h2 class="title size-50 center">Featured Products</h2>
        <ul class="product__recomend_wrapper product__featured">
            <?php while ($general_products->have_posts()) : $general_products->the_post(); 
                global $product;
                $product_id = get_the_ID();
            ?>
                <a href="<?php the_permalink(); ?>" class="new-items__item _p_rel _bg_fff product__featured-link">
                    <div class="product__featured-item">
                        <?php if (has_post_thumbnail()) : ?>
                            <img src="<?php echo get_the_post_thumbnail_url($product_id, 'medium'); ?>" alt="<?php the_title(); ?>" class="img">
                        <?php endif; ?>
                        <span class="size-26 price _p_abs">from <?php echo $product->get_price_html(); ?></span>
                    </div>
                    <div class="product__featured-inform">
                        <h3 class="size-36"><?php the_title(); ?></h3>
                        <div class="text size-18"><?php the_content(); ?></div>
                    </div>
                </a>
            <?php endwhile; wp_reset_postdata(); ?>
        </ul>
    </section>
<?php endif; ?>

<?php if ($other_products->have_posts()) : ?>
    <section class="relative">
        <div class="product__other_section _p_rel _flex_col_center container">
            <h2 class="title size-50 center">Other products</h2>
            <div class="swiper product__other_slider">
                <div class="swiper-wrapper product__other_slider_wrapper">
                    <?php 
                    $counter = 0;
                    while ($other_products->have_posts()) : $other_products->the_post(); 
                        global $product;
                        $product_id = get_the_ID();

                        if ($counter % 6 == 0) : ?>
                            <div class="swiper-slide product__other_slider_wrapper_item">
                                <div class="product__other_slider_wrapper_item_cntr">
                        <?php endif; ?>

                        <a href="<?php the_permalink(); ?>" class="product__other_item product column">
                            <?php if (has_post_thumbnail()) : ?>
                                <img src="<?php echo get_the_post_thumbnail_url($product_id, 'medium'); ?>" alt="<?php the_title(); ?>">
                            <?php endif; ?>
                            <h3><?php the_title(); ?></h3>
<?php
$prod_obj = wc_get_product( $product_id );
if ( $prod_obj ) {
    $stock_status = $prod_obj->get_stock_status();
    $stock_qty = $prod_obj->managing_stock() ? (int) $prod_obj->get_stock_quantity() : null;

    $is_oos = ( $stock_status === 'outofstock' || ( $stock_qty !== null && $stock_qty <= 0 ) );

    if ( $is_oos ) {
        echo '<span class="out-of-stock-badge">Out of stock</span>';
    }
}
?>



                            <span class="price">from <?php echo $product->get_price_html(); ?></span>
                        </a>

                        <?php $counter++; if ($counter % 6 == 0) : ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endwhile; wp_reset_postdata(); ?>

                    <?php if ($counter % 6 != 0) : ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>            
            </div>
            <div class="product__other-button-next swiper-button-next arrow-next arrow"></div>
            <div class="product__other-button-prev swiper-button-prev arrow-prev arrow"></div>
        </div>
    </section>
<?php endif; ?>
</main>
<?php get_footer(); ?>
