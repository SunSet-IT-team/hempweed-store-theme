<?php
/**
 * Custom Product Template for WooCommerce
 */

get_header(); ?>
<?php while (have_posts()) : the_post(); ?>
<main class="main">
  <section class="banner">
        <div class="banner__container container flex">
        <div class="banner__container-item direction __cat">
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
            <li>
                <a href="/categories/">Categories</a>
            </li>
            <li>/</li>
            <li>Categories</li>
          </ul>
            <div class="cat__cntr _flex_center_stretch container ">
                <!-- фото товара -->
                <img class="cat__img" src="<?php echo get_the_post_thumbnail_url(); ?>" alt="<?php the_title(); ?>">
                <div class="cat__info _flex_col">
                    <!-- наименование товара -->
                    <h2 class="cat__title _fs_46 _fw_600"><?php the_title(); ?></h2>
                    <!-- описание товара -->
                    <?php 
                    $short_desc = get_post_meta(get_the_ID(), '_short_description', true);
                    if ($short_desc) {
                        $description = wp_strip_all_tags($short_desc);
                    } else {
                        $description = wp_strip_all_tags(get_the_content());
                    }
                    ?>
                    <div class="cat__text text">
                        <p class="_fs_30 _fw_400"><?php echo $description; ?></p>
                    </div>
                        <?php 
$stock_quantity = $product->get_stock_quantity();

if ($stock_quantity === 0) {
    echo '<p class="stock-info">Out of stock</p>';
    echo '<script>document.addEventListener("DOMContentLoaded", function() {
        let btn = document.querySelector(".single_add_to_cart_button");
        if(btn){ btn.setAttribute("disabled", "disabled"); }
    });</script>';
} elseif ($stock_quantity > 0 && $stock_quantity < 10) {
    echo '<p class="stock-info low-stock">There are ' . $stock_quantity . ' pieces left</p>';
}
?>




                    <div class="cat__form_cntr _flex_center_sb">
                        <!-- цена -->
                        <span class="_fs_48 _fw_400"><?php echo wc_price(get_post_meta(get_the_ID(), '_price', true)); ?></span>
                        <!-- количество -->
                        <input class="cat__input" type="number" name="quantity" value="1" min="1">
            <!-- Добавить в корзину -->
<?php 
global $product;
$stock_quantity = $product->get_stock_quantity(); // остаток товара
$is_in_stock = $product->is_in_stock();          // проверка наличия

// Если товара нет в наличии — добавляем класс и disabled
$disabled_class = '';
$disabled_attr  = '';
if (!$is_in_stock || $stock_quantity <= 0) {
    $disabled_class = ' disabled';
    $disabled_attr  = ' disabled="disabled"';
}
?>

<form class="cat__form" method="post">
    <!-- скрытое поле с ID товара -->
    <input type="hidden" name="add-to-cart" value="<?php echo get_the_ID(); ?>">

    <button class="cat__add_btn _fs_24 _fw_400 _flex_center _yellow_btn _btn_hover<?php echo $disabled_class; ?>"
            type="submit"
            data-product_id="<?php echo get_the_ID(); ?>"
            data-product_name="<?php echo esc_attr(get_the_title()); ?>"
            <?php echo $disabled_attr; ?>>
        <?php echo esc_html__('Add to cart', 'woocommerce'); ?>
    </button>
</form>

                    </div>
                </div>
            </div>
          <?php 
            $image = get_field('image_in_banner_product'); 
            $default_image = get_template_directory_uri() . '/img/home/cannabis-marijuana-leaf-closeup.png'; // Укажите путь к изображению по умолчанию

            $image_url = !empty($image['url']) ? esc_url($image['url']) : $default_image;
            $image_alt = !empty($image['alt']) ? esc_attr($image['alt']) : 'Баннер по умолчанию';
        ?>

        <img src="<?php echo $image_url; ?>" alt="<?php echo $image_alt; ?>" class="banner__img img">


    </section>
    
    <?php if( have_rows('advantages_product') ): ?>
    <ul class="features container flex _green">
        <?php while( have_rows('advantages_product') ): the_row(); 
            $image = get_sub_field('advantages_product_img');
            $text = get_sub_field('advantages_product_text');
        ?>
        <li class="features__item features__item-image-block _green">
            <?php if( $image ): ?>
                <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($text); ?>" class="features__item-image img">
            <?php endif; ?>
            <span class="size-32 _fs_28 _fw_700 _color_fff"><?php echo esc_html($text); ?></span>
        </li>
        <?php endwhile; ?>
    </ul>
    <?php endif; ?>


    <!-- Разметка для табов -->
<div class="relative">
    <?php 
        // Проверяем наличие данных

        $has_description = false;
        if (have_rows('product_information_deteile')) {
            while (have_rows('product_information_deteile')) {
                the_row();
                if (get_sub_field('product_information_deteile_text')) {
                    $has_description = true;
                    break;
                }
            }
            reset_rows(); // Сбрасываем позицию repeater-цикла
        }

        $has_reviews = false;
        if (have_rows('product_information_review_block')) {
            while (have_rows('product_information_review_block')) {
                the_row();
                if (get_sub_field('product_information_review_block_text')) {
                    $has_reviews = true;
                    break;
                }
            }
            reset_rows(); // Сбрасываем позицию repeater-цикла
        }

        $has_overview = get_field('product_information_move');
        $has_overview = !empty($has_overview) ? esc_url($has_overview) : false;
    ?>

    <?php if ($has_description || $has_reviews || $has_overview): ?>
        <div class="product-details container">
            <div class="product-details__tabs _flex_start_center _bg_fff">
                <?php if ($has_description): ?>
                    <button class="product-details__tab active" data-tab="description">Description</button>
                <?php endif; ?>
                <?php if ($has_reviews): ?>
                    <button class="product-details__tab <?php echo !$has_description ? 'active' : ''; ?>" data-tab="reviews">
                        Feedback
                    </button>
                <?php endif; ?>
                <?php if ($has_overview): ?>
                    <button class="product-details__tab <?php echo (!$has_description && !$has_reviews) ? 'active' : ''; ?>" data-tab="overview">
                        Reviews
                    </button>
                <?php endif; ?>
            </div>

            <?php if ($has_description): ?>
    <div class="product-details__tab-content active" id="description">
        <?php 
            reset_rows(); // Перезапуск цикла repeater
            while (have_rows('product_information_deteile')): the_row(); 

                $text = get_sub_field('product_information_deteile_text');
                $image = get_sub_field('product_information_deteile_img');
        ?>
            <!-- Всегда выводим общий контейнер -->
            <div class="product-details__description-item _flex_center_sb">

                <!-- Выводим изображение, если оно есть -->
                <?php if ($image): ?>
                    <div class="product-details__description-img-cntr _flex_center _bg_fff">
                        <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" class="product-details__description-img">
                    </div>
                <?php endif; ?>

                <!-- Выводим текст, если он есть -->
                <?php if (!empty($text)): ?>
                    <div class="product-details__description-text">
                        <?php echo wp_kses_post($text); ?>
                    </div>
                <?php endif; ?>

            </div>
        <?php endwhile; ?>
    </div>
<?php endif; ?>

                <!-- Вкладка Отзывы -->
                <?php if ($has_reviews): ?>
                    <div class="feedback product-details__tab-content _p_rel <?php echo !$has_description ? 'active' : ''; ?>" id="reviews">
                        <div class="swiper swiper_feedback _flex_start">
                            <div class="swiper-wrapper">
                                <?php $count = 0; ?>
                                <?php while (have_rows('product_information_review_block')): the_row(); ?>
                                    <?php 
                                        $text = get_sub_field('product_information_review_block_text');
                                        if ($text): 
                                    ?>
                                        <?php if ($count % 2 == 0): ?>
                                            <div class="swiper-slide column">
                                        <?php endif; ?>

                                        <div class="feedback_block _flex_start _bg_fff">
                                            <?php 
                                                $review_img = get_sub_field('product_information_review_block_img'); 
                                                $img_url = $review_img ? esc_url($review_img['url']) : get_template_directory_uri() . '/img/home/profil.jpg';
                                                $img_alt = $review_img ? esc_attr($review_img['alt']) : 'Default Image';
                                            ?>
                                            <img src="<?php echo $img_url; ?>" alt="<?php echo $img_alt; ?>" class="feedback__img"> 

                                            <div class="feedback__content">
                                                <h3 class="feedback__name"><?php the_sub_field('product_information_review_block_name'); ?></h3>
                                                <ul class="star _flex_start_center">
                                                    <?php 
                                                        if (have_rows('product_information_review_block_reiting')) :
                                                            while (have_rows('product_information_review_block_reiting')) : the_row();
                                                                $star_img = get_sub_field('product_information_review_block_reiting_img');
                                                                $image_url = $star_img ? esc_url($star_img['url']) : get_template_directory_uri() . '/img/home/Star5.png';
                                                    ?>
                                                        <li>
                                                            <img src="<?php echo $image_url; ?>" alt="рейтинг" class="img">
                                                        </li>
                                                    <?php 
                                                            endwhile;
                                                        endif; 
                                                    ?>
                                                </ul>

                                                <div class="feedback__text text _fs_24 _fw_400">
                                                    <p><?php echo wp_kses_post($text); ?></p>
                                                </div>
                                            </div>            
                                        </div> 

                                        <?php $count++; ?>
                                        <?php if ($count % 2 == 0 || get_row_index() == count(get_field('product_information_review_block'))): ?>
                                            </div> 
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endwhile; ?>

                                <?php if ($count % 2 != 0): ?>
                                    </div>
                                <?php endif; ?>
                            </div> 

                            <div class="feedback__arrow_wrap _flex_end_center">            
                                <div class="feedback__swiper-button-prev swiper-button-prev arrow-prev arrow"></div>
                                <div class="feedback__swiper-button-next swiper-button-next arrow-next arrow"></div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Вкладка Обзор -->
                <?php if ($has_overview): ?>
                    <div class="product-details__tab-content <?php echo (!$has_description && !$has_reviews) ? 'active' : ''; ?>" id="overview">
                        <div class="product-details__video">
                            <iframe src="<?php echo $has_overview; ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <img src="<?php echo get_template_directory_uri(); ?>/img/background/background-list/bg9.png" class="back__img bk9 back__left">
    <img src="<?php echo get_template_directory_uri(); ?>/img/background/background-list/bg10.png" class="back__img bk10 back__right">
</div>




    <!-- Сопутствующие товары -->
<?php 
$upsell_products = $product->get_upsell_ids();
if (!empty($upsell_products)) : ?>
    <section class="popular background__yellow background__beige">
        <div class="popular__container column container">
            <div class="popular__title">
                <h2 class="title size-50 center">Related Products</h2>
            </div>
            <div class="swiper popular__slider">
                <div class="swiper-wrapper">
                    <?php foreach ($upsell_products as $upsell_id) : 
                        $upsell_product = wc_get_product($upsell_id);
                    ?>
                        <div class="swiper-slide">
                            <a href="<?php echo get_permalink($upsell_id); ?>" class="product new-items__item">
                                <?php if (has_post_thumbnail($upsell_id)) : ?>
                                    <img src="<?php echo get_the_post_thumbnail_url($upsell_id, 'medium'); ?>" alt="<?php echo get_the_title($upsell_id); ?>" class="img">
                                <?php endif; ?>
                                <h3 class="size-20"><?php echo get_the_title($upsell_id); ?></h3>
                                <span class="size-26 price">from <?php echo $upsell_product->get_price_html(); ?></span>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="popular__swiper-button-next swiper-button-next arrow-next arrow"></div>
                <div class="popular__swiper-button-prev swiper-button-prev arrow-prev arrow"></div>
            </div>
        </div>
        <img src="<?php echo get_template_directory_uri(); ?>/img/background/background-list/bk6.png" class="back__img bk6 back__left">
        <img src="<?php echo get_template_directory_uri(); ?>/img/background/background-list/bk7.png" class="back__img bk7 back__right">
    </section>
<?php endif; ?>


<?php endwhile; ?>
<?php get_footer(); ?>