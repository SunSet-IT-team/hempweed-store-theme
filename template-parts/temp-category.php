<?php
/**
 * Template Name: Категории
**/

get_header();
?>
<main class="main">
  <section class="banner banner__category">
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
          <li>Categories</li>
        </ul>
        <h1 class="title size-50">Categories</h1>
      </div>
    </div>
    <img src="<?php echo get_template_directory_uri(); ?>/img/home/cannabis-marijuana-leaf-closeup.png" alt="" class="banner__img img">
    <img src="<?php echo get_template_directory_uri(); ?>/img/background/background-list/bk1.png" class="back__img bk1 back__left">
  </section>

  <section id="categories" class="categories container padding-bot-100">
    <ul class="categories__grid flex">
        <?php
        $product_categories = get_terms(
            array(
                'taxonomy'   => 'product_cat',
                'hide_empty' => false,
                'posts_per_page' => -1 // Изменил на -1, так как -6 не имеет смысла
            )
        );

        if (!empty($product_categories) && !is_wp_error($product_categories)) {
            // Перемещаем "Other" в конец списка
            usort($product_categories, function ($a, $b) {
                return ($a->name === 'Other') - ($b->name === 'Other');
            });

            foreach ($product_categories as $category) {
                $thumbnail_id = get_term_meta($category->term_id, 'thumbnail_id', true);
                $image_url = wp_get_attachment_url($thumbnail_id);
                $image_url = $image_url ? $image_url : get_template_directory_uri() . '/img/home/image.png'; // Фолбэк изображение
                $category_link = get_term_link($category);
                ?>
                <a href="<?php echo esc_url($category_link); ?>" class="categories__item">
                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($category->name); ?>" class="img">
                    <span class="size-40 title"><?php echo esc_html($category->name); ?></span>
                </a>
                <?php
            }
        }
        ?>
    </ul>

  </section>
  
  <section class="popular background__yellow">
    <div class="popular__container column container">
      <div class="popular__title">
        <h2 class="title size-50 center">popular items</h2>
      </div>
      <div class="swiper popular__slider">
        <div class="swiper-wrapper">
        <?php
      $args = array(
          'post_type'      => 'product',
          'posts_per_page' => -1,
          'meta_query'     => array(
              array(
                  'key'   => 'new_tovar',
                  'value' => '1',
              )
          )
      );

      $query = new WP_Query($args);?>
            <?php while ($query->have_posts()) : $query->the_post(); global $product; ?>
                <div class="swiper-slide">
                    <a href="<?php the_permalink(); ?>" class="product new-items__item">
                        <span class="new">new</span>
                        <?php if (has_post_thumbnail()) : ?>
                            <img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'medium'); ?>" alt="<?php the_title(); ?>" class="img">
                        <?php endif; ?>
                        <h3 class="size-20"><?php 
                            $title = wp_strip_all_tags(get_the_title()); // Удаляем HTML-теги
                            echo mb_substr($title, 0, 70) . (mb_strlen($title) > 70 ? '...' : '');
                            ?></h3>
                        <span class="size-26 price">from <?php echo $product->get_price_html(); ?></span>
                    </a>
                </div>
            <?php endwhile; ?>
        </div>
        <!-- Навигация -->
        <div class="popular__swiper-button-next swiper-button-next arrow-next arrow"></div>
        <div class="popular__swiper-button-prev swiper-button-prev arrow-prev arrow"></div>
      </div>
    </div>
    <img src="<?php echo get_template_directory_uri(); ?>/img/background/background-list/bk6.png" class="back__img bk6 back__left">
    <img src="<?php echo get_template_directory_uri(); ?>/img/background/background-list/bk7.png" class="back__img bk7 back__right">
  </section>

  <section class="cosmetik cosmetik_itemBanner">
    <div class="cosmetik__container flex container">
      <div class="cosmetik__items column">
        <h3 class="size-50 title"><?php the_field('home_bannerdop_title', 'option'); ?></h3>
        <div class="text size-32"><?php the_field('home_bannerdop_text', 'option'); ?> </div>
        <?php
        // Получаем значение поля ACF
        $home_bannerdop_link = get_field('home_bannerdop_link', 'option');

        // Проверяем, есть ли значение
        if ($home_bannerdop_link) :
        ?>
          <a href="<?php echo esc_url($home_bannerdop_link); ?>" class="btn btn_yellow">Oder</a>
        <?php endif; ?>
      </div>
    </div>
  </section>

 <section class="relative">
    <div id="new-items" class="new-items column container padding-bot-100 _p_rel">
        <h2 class="title size-40 center">New Items</h2>
        <?php
  
        if ($query->have_posts()) : ?>
            <div class="swiper new-items__slider">
              <div class="swiper-wrapper">
                    <?php 
                    $counter = 0;
                    while ($query->have_posts()) : $query->the_post(); global $product;
                        if ($counter % 6 == 0) : ?>
                            <div class="swiper-slide">
                                <div class="new-items__group grid">
                        <?php endif; ?>
  
                        <a href="<?php the_permalink(); ?>" class="new-items__item product column">
                            <span class="new">new</span>
                            <?php if (has_post_thumbnail()) : ?>
                                <img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'medium'); ?>" alt="<?php the_title(); ?>" class="img">
                            <?php endif; ?>
                            <h3 class="size-20">
                            <?php 
                              $title = wp_strip_all_tags(get_the_title()); // Удаляем HTML-теги
                              echo mb_substr($title, 0, 70) . (mb_strlen($title) > 70 ? '...' : '');
                              ?>
                            </h3>
                            <span class="price size-26">from <?php echo $product->get_price_html(); ?></span>
                        </a>
  
                        <?php $counter++; if ($counter % 6 == 0 || $counter == $query->post_count) : ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endwhile; ?>
                </div>
                <!-- Навигация -->
                <div class="new-items__swiper-button-next swiper-button-next arrow-next arrow"></div>
                <div class="new-items__swiper-button-prev swiper-button-prev arrow-prev arrow"></div>
            </div>
            <?php wp_reset_postdata(); ?>
        <?php endif; ?>
    </div>
    <img src="<?php echo get_template_directory_uri(); ?>/img/background/background-list/bk2.png" class="back__img bk2 back__left">
    <img src="<?php echo get_template_directory_uri(); ?>/img/background/background-list/bk8.png" class="back__img bk8 back__right">
 </section>

<?php get_footer(); ?>