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
                <a href=".">Categories</a>
            </li>
            <li>/</li>
            <li>Categories</li>
          </ul>
          <div class="container">
            <!-- фото товара -->
            <img src="<?php echo get_the_post_thumbnail_url(); ?>" alt="<?php the_title(); ?>">
            <div>
                <!-- наименование товара -->
                <h2><?php the_title(); ?></h2>
                <!-- описание товара -->
                <div class="text">
                    <p><?php the_excerpt(); ?></p>
                </div>
                <div>
                    <!-- цена -->
                    <span><?php echo wc_price(get_post_meta(get_the_ID(), '_price', true)); ?></span>
                    <!-- количество -->
                    <input type="number" name="quantity" value="1" min="1">
                    <!-- Добавить в корзину -->
                    <form method="post" action="?add-to-cart=<?php echo get_the_ID(); ?>">
                        <button type="submit" name="add-to-cart" value="<?php echo get_the_ID(); ?>">Add+</button>
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
    <ul class="features container flex grin">
        <?php while( have_rows('advantages_product') ): the_row(); 
            $image = get_sub_field('advantages_product_img');
            $text = get_sub_field('advantages_product_text');
        ?>
        <li class="features__item features__item-image-block">
            <?php if( $image ): ?>
                <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($text); ?>" class="features__item-image img">
            <?php endif; ?>
            <span class="size-32"><?php echo esc_html($text); ?></span>
        </li>
        <?php endwhile; ?>
    </ul>
    <?php endif; ?>


        <!-- Разметка для табов -->
        <div class="product-details">
            <div class="product-details__tabs">
                <?php 
                    $has_description = have_rows('product_information_deteile');
                    $has_reviews = have_rows('product_information_review_block');
                    $has_overview = get_field('product_information_move');

                    if ($has_description): ?>
                        <button class="product-details__tab active" data-tab="description">Description</button>
                    <?php endif;
                    if ($has_reviews): ?>
                        <button class="product-details__tab <?php echo !$has_description ? 'active' : ''; ?>" data-tab="reviews">Feedback</button>
                    <?php endif;
                    if ($has_overview): ?>
                        <button class="product-details__tab <?php echo (!$has_description && !$has_reviews) ? 'active' : ''; ?>" data-tab="overview">Reviews</button>
                    <?php endif;
                ?>
            </div>
            <div class="product-details__content">
                <!-- Вкладка Описание -->
                <?php if ($has_description): ?>
                    <div class="product-details__tab-content active" id="description">
                        <?php while (have_rows('product_information_deteile')): the_row(); ?>
                            <div class="product-details__description-item">
                                <?php 
                                    $image = get_sub_field('product_information_deteile_img'); 
                                    if ($image):
                                ?>
                                    <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" class="product-details__description-img">
                                <?php endif; ?>
                                <div class="product-details__description-text">
                                    <?php the_sub_field('product_information_deteile_text'); ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php endif; ?>

                <!-- Вкладка Отзывы -->
                <?php if ($has_reviews): ?>
                    <div class="product-details__tab-content <?php echo !$has_description ? 'active' : ''; ?>" id="reviews">
                        <div class="swiper swiper_feedback">
                            <div class="swiper-wrapper">
                                <?php while (have_rows('product_information_review_block')): the_row(); ?>
                                    <div class="swiper-slide feedback_block">
                                    <?php 
                                        $review_img = get_sub_field('product_information_review_block_img'); 
                                        if ($review_img): 
                                            // Если изображение есть, выводим его
                                            $img_url = esc_url($review_img['url']);
                                            $img_alt = esc_attr($review_img['alt']);
                                        else: 
                                            // Если изображения нет, задаем путь к изображению по умолчанию
                                            $img_url = get_template_directory_uri() . '/img/home/profil.jpg';
                                            $img_alt = 'Default Image'; // Альт-текст для изображения по умолчанию
                                        endif;
                                    ?>
                                        <img src="<?php echo $img_url; ?>" alt="<?php echo $img_alt; ?>" class="feedback__img"> 

                                        <div class="feedback__content">
                                            <h3 class="feedback__name"><?php the_sub_field('product_information_review_block_name'); ?></h3>
                                            <ul class="star">
                                                <?php 
                                                    $rating_images = get_sub_field('product_information_review_block_reiting'); 
                                                    if ($rating_images):
                                                        foreach ($rating_images as $rating):
                                                            $image_url = !empty($rating['product_information_review_block_reiting_img']['url']) 
                                                                ? esc_url($rating['product_information_review_block_reiting_img']['url']) 
                                                                : get_template_directory_uri() . '/img/home/Star5.png'; // Замените путь на реальный
                                                ?>
                                                            <li>
                                                                <img src="<?php echo $image_url; ?>" alt="рейтинг" class="img">
                                                            </li>
                                                <?php 
                                                        endforeach; 
                                                    endif; 
                                                ?>
                                            </ul>

                                            <div class="text">
                                                <p><?php the_sub_field('product_information_review_block_text'); ?></p>
                                            </div>
                                        </div>            
                                    </div>
                                <?php endwhile; ?>
                            </div>
                            <div class="feedback__swiper-button-next swiper-button-next arrow-next arrow"></div>
                            <div class="feedback__swiper-button-prev swiper-button-prev arrow-prev arrow"></div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Вкладка Обзор -->
                <?php if ($has_overview): ?>
                    <div class="product-details__tab-content <?php echo (!$has_description && !$has_reviews) ? 'active' : ''; ?>" id="overview">
                        <div class="product-details__video">
                            <video controls>
                                <source src="<?php echo esc_url($has_overview); ?>" type="video/mp4">
                            </video>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>



  <script>
    document.addEventListener("DOMContentLoaded", function() {
        const tabs = document.querySelectorAll(".product-details__tab");
        const contents = document.querySelectorAll(".product-details__tab-content");

        tabs.forEach(tab => {
            tab.addEventListener("click", function() {
                tabs.forEach(t => t.classList.remove("active"));
                contents.forEach(c => c.classList.remove("active"));
                
                this.classList.add("active");
                document.getElementById(this.dataset.tab).classList.add("active");
            });
        });

        new Swiper(".swiper_feedback", {
            navigation: {
                nextEl: ".feedback__swiper-button-next",
                prevEl: ".feedback__swiper-button-prev",
            },
            slidesPerView: 2,
            spaceBetween: 20,
            breakpoints: {
                768: {
                    slidesPerView: 1
                }
            }
        });
    });
  </script>
  <style>
  .product-details__tab-content { display: none; }
  .product-details__tab-content.active { display: block; }
  .product-details__tab.active { font-weight: bold; }
  </style>


    <!-- Сопутствующие товары -->
<?php 
$upsell_products = $product->get_upsell_ids();
if (!empty($upsell_products)) : ?>
    <section class="popular background__yellow">
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
    </section>
<?php endif; ?>


<?php endwhile; ?>
<?php get_footer(); ?>
