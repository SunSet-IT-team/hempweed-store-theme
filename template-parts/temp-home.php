<?php
/**
 * Template Name: Главная
**/

get_header();
?>
<main class="main">
  <section class="banner">
    <div class="banner__container container flex">
      <div class="banner__container-item direction">
        <span class="subtitle size-30"><?php the_field('home_banner_subtitle') ?></span>
        <h1 class="title size-50"><?php the_field('home_banner_title') ?></h1>
      </div>
        <?php
        // Получаем значение поля ACF
        $home_banner_link = get_field('home_banner_link');

        // Проверяем, есть ли значение
        if ($home_banner_link) :
        ?>
            <a href="<?php echo esc_url($home_banner_link); ?>" class="btn circle__btn">New items</a>
        <?php endif; ?>
    </div>
    <img src="<?php echo get_template_directory_uri(); ?>/img/home/cannabis-marijuana-leaf-closeup.png" alt="" class="banner__img img">
    <img src="<?php echo get_template_directory_uri(); ?>/img/background/background-list/bk1.png" class="back__img bk1 back__left">
  </section>

  <ul class="features container flex">
    <?php if( have_rows('home_banner_link_advantages') ): ?>
      <?php while( have_rows('home_banner_link_advantages') ): the_row(); ?>
        <li class="features__item">
          <span class="size-32"><?php the_sub_field('home_banner_link_advantages_text'); ?></span>
        </li>
      <?php endwhile; ?>
    <?php endif; ?>
  </ul>

  <section id="categories" class="categories container padding-bot-100 _p_rel">
    <ul class="categories__grid flex">
        <?php
$cannabinoids_terms = get_terms([
    'taxonomy'   => 'cannabinoids',
    'hide_empty' => false,
    'orderby'    => 'name',
    'order'      => 'ASC',
]);

if (!empty($cannabinoids_terms) && !is_wp_error($cannabinoids_terms)) {
    foreach ($cannabinoids_terms as $term) {
        // вот тут добавляем
        $image_id = get_term_meta($term->term_id, 'cannabinoid-image-id', true);
        $image_url = $image_id ? wp_get_attachment_url($image_id) : get_template_directory_uri() . '/img/home/image.png';

        $term_link = get_term_link($term);
        ?>
        <a href="<?php echo esc_url($term_link); ?>" class="categories__item">
            <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($term->name); ?>" class="img">
            <span class="size-40 title"><?php echo esc_html($term->name); ?></span>
        </a>
        <?php
    }
} else {
    echo '<p>No cannabinoids terms found</p>';
}
?>

    </ul>
  </section> 

    <section class="relative">
        <div id="new-items" class="new-items column container padding-bot-100 _p_rel">
          <h2 class="title size-40 center">New Items</h2>
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
    
          $query = new WP_Query($args);
    
          if ($query->have_posts()) : ?>
              <div class="swiper new-items__slider">
                  <div class="swiper-wrapper">
                      <?php 
                      $counter = 0;
                      while ($query->have_posts()) : $query->the_post(); global $product;
                          if ($counter % 6 == 0) : ?>
                              <div class="swiper-slide">
                                  <div class="new-items__group flex wrap">
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
                  
              </div>
              <div class="new-items__swiper-button-next swiper-button-next arrow-next arrow"></div>
              <div class="new-items__swiper-button-prev swiper-button-prev arrow-prev arrow"></div>
              <?php wp_reset_postdata(); ?>
          <?php endif; ?>
          <a href="#" class="btn">Show More</a>
        </div>
        <img src="<?php echo get_template_directory_uri(); ?>/img/background/background-list/bk2.png" class="back__img bk2 back__left">
        <img src="<?php echo get_template_directory_uri(); ?>/img/background/background-list/bk3.png" class="back__img bk3 back__right">
    
    </section>
  <section class="cosmetik">
      <div class="cosmetik__container flex container">
          <img src="<?php the_field('home_cosmet_img'); ?>" alt="" class="cosmetik__img img">
          <div class="cosmetik__items column">
              <h3 class="size-50 title"><?php the_field('home_cosmet_title'); ?></h3>
              <div class="text size-32">
                  <p><?php the_field('home_cosmet_text'); ?></p>
              </div>
              <a href="<?php the_field('home_cosmet_link'); ?>" class="btn btn_yellow">Order</a>
          </div>
      </div>
  </section>


    <section class="relative">
        <div class="docum flex container">
            <img src="<?php echo get_template_directory_uri(); ?>/img/home/image 35.png" alt="" class="docum__img img">
            <div class="docum__block direction">
                <div class="text size-26 docum__item">
                    <?php the_field('home_docum_text'); ?>
                </div>
                <?php 
                $file = get_field('home_docum_file'); 
                if ($file): 
                ?>
                    <a href="<?php echo $file['url']; ?>" class="btn btn_red" target="_blank">More</a>
                <?php endif; ?>
            </div>
        </div>
        <img src="<?php echo get_template_directory_uri(); ?>/img/background/background-list/bk4.png" class="back__img bk4 back__left">
        <img src="<?php echo get_template_directory_uri(); ?>/img/background/background-list/bk5.png" class="back__img bk5 back__right">
    </section>


    <section class="popular background__yellow">
        <div class="popular__container _p_rel column container">
        <div class="popular__title">
            <h2 class="title size-50 center">popular items</h2>
            <span>
            <?php 
                $sales_args = array(
                    'post_type'      => 'product',
                    'posts_per_page' => -1,
                    'meta_query'     => array(
                        array(
                            'key'     => 'total_sales', // ACF поле
                            'value'   => 0,
                            'compare' => '>',
                            'type'    => 'NUMERIC' // Числовое сравнение
                        )
                    )
                );
                $sales_query = new WP_Query($sales_args);
                echo $sales_query->found_posts; // Вывод количества товаров с продажами
            ?>
            </span>
        </div>
        
        <div class="swiper popular__slider">
            <div class="swiper-wrapper">
                <?php while ($sales_query->have_posts()) : $sales_query->the_post(); global $product; ?>
                    <div class="swiper-slide">
                        <a href="<?php the_permalink(); ?>" class="product new-items__item">
                            <?php if (has_post_thumbnail()) : ?>
                                <img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'medium'); ?>" alt="<?php the_title(); ?>" class="img">
                            <?php endif; ?>
                            <h3 class="size-20"><?php the_title(); ?></h3>
                            <span class="size-26 price">from <?php echo $product->get_price_html(); ?></span>
                        </a>
                    </div>
                <?php endwhile; ?>
            </div>
            <!-- Навигация -->
            
        </div>

        <div class="popular__swiper-button-next swiper-button-next arrow-next arrow"></div>
        <div class="popular__swiper-button-prev swiper-button-prev arrow-prev arrow"></div>
        
        <?php wp_reset_postdata(); ?>
        
        </div>
        <img src="<?php echo get_template_directory_uri(); ?>/img/background/background-list/bk6.png" class="back__img bk6 back__left">
        <img src="<?php echo get_template_directory_uri(); ?>/img/background/background-list/bk7.png" class="back__img bk7 back__right">
    </section>

  <section class="banners-inform container flex">
      <div class="banners-inform__block column">
          <h2 class="banners-inform__title title size-50"><?php the_field('home_bannerdop_title', 'option'); ?></h2>
          <div class="text size-30">
            <?php the_field('home_bannerdop_text', 'option'); ?>
          </div>
          <?php 
          $home_bannerdop_link = get_sub_field('home_bannerdop_link', 'option');
          if ($home_bannerdop_link): 
          ?>
              <a href="<?php echo esc_url($home_bannerdop_link); ?>" class="btn btn_yellow">Order</a>
          <?php endif; ?>
      </div>
      <img src="<?php echo get_template_directory_uri(); ?>/img/home/asdasd.png" alt="<?php the_field('home_dopbanner_title'); ?>" class="img banners-inform__img">
  </section>

  <section class="feedback _p_rel padding-bot-100 container">
      <h2 class="feedback__heading title center size-50"><?php the_field('review_title', 'option'); ?></h2>
      <div class="swiper swiper_feedback">
          <div class="swiper-wrapper">
              <?php if (have_rows('review_block', 'option')) : ?>
                  <?php while (have_rows('review_block', 'option')) : the_row(); ?>
                      <div class="swiper-slide feedback_block">
                          <?php 
                          $review_img = get_sub_field('review_block_img', 'option');
                          if ($review_img): ?>
                              <img src="<?php echo esc_url($review_img['url']); ?>" alt="" class="img feedback__img">
                          <?php endif; ?>
                          
                          <div class="feedback__content">
                              <h3 class="feedback__name"><?php the_sub_field('review_block_name', 'option'); ?></h3>
                              <ul class="star">
                                <?php if (have_rows('review_block_reiting', 'option')) : ?>
                                    <?php while (have_rows('review_block_reiting', 'option')) : the_row(); ?>
                                        <?php 
                                        $star_img = get_sub_field('review_block_reiting_img', 'option');
                                        
                                        // Если изображение существует, выводим его
                                        if ($star_img): ?>
                                            <li>
                                                <img src="<?php echo esc_url($star_img['url']); ?>" alt="рейтинг" class="img">
                                            </li>
                                        <?php else: // Если изображения нет, выводим изображение по умолчанию ?>
                                            <li>
                                                <img src="<?php echo get_template_directory_uri(); ?>/img/home/Star5.png" alt="рейтинг" class="img">
                                            </li>
                                        <?php endif; ?>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </ul>
                              <div class="text">
                                  <p><?php the_sub_field('review_block_text'); ?></p>
                              </div>
                          </div>            
                      </div>
                  <?php endwhile; ?>
              <?php endif; ?>
          </div>         
      </div>
       <!-- Навигация -->
        <div class="feedback__arrow_wrap _flex_end_center">            
            <div class="feedback__swiper-button-prev swiper-button-prev arrow-prev arrow"></div>
            <div class="feedback__swiper-button-next swiper-button-next arrow-next arrow"></div>
        </div>
       
  </section>

  <section class="faq">
      <div class="faq__container container">
          <h2 class="title center size-50"><?php the_field('faq_title', 'option'); ?></h2>
          <div class="faq__section _flex_col">
              <?php if (have_rows('faq_questions', 'option')) : ?>
                  <?php while (have_rows('faq_questions', 'option')) : the_row(); ?>
                      <div class="faq__block _accordion_block" accordion-state="">
                          <button class="faq__btns _accordion_btn _flex_center_sb">
                              <h3 class="faq__title"><?php the_sub_field('faq_question', 'option'); ?></h3>
                              <img src="<?php echo get_template_directory_uri(); ?>/img/arrow.png" alt="" class="img faq__arrow">
                          </button>
                          <div class="text faq__text _accordion_hidden_content">
                              <p><?php the_sub_field('faq_answer', 'option'); ?></p>
                          </div>
                      </div>
                  <?php endwhile; ?>
              <?php endif; ?>
          </div>
      </div>
  </section>
  <section class="news">
    <h2 class="new__heading title container center size-50"><a href="/new">News</a></h2>
    <?php 
    $posts_per_page = 18;
    $args = array(
    'post_type'      => 'post',
    'posts_per_page' => $posts_per_page,
    'paged'          => $paged,
    'orderby'        => 'date',
    'order'          => 'DESC'
    );

    $query = new WP_Query($args);

    // Если есть посты, выводим контент
    if ($query->have_posts()) :
  ?>

  <section class="news">
      <?php
      // Разделяем посты на два слайдера
      $counter = 0;
      $slider1_posts = [];
      $slider2_posts = [];

      while ($query->have_posts()) : $query->the_post();
          if ($counter < 9) {
            $slider1_posts[] = get_the_ID();
        } else {
            $slider2_posts[] = get_the_ID();
        }
          $counter++;
      endwhile;
      wp_reset_postdata();
      ?>

        <?php if (!empty($slider1_posts)) : ?>
            <div class="swiper-container new__slider">
                <div class="swiper-wrapper">
                    <?php foreach ($slider1_posts as $post_id) : ?>
                        <div class="swiper-slide">
                            <a href="<?php echo get_permalink($post_id); ?>" class="new__link _flex_col_center _p_rel">
                                <img src="<?php echo get_the_post_thumbnail_url($post_id, 'medium'); ?>" alt="<?php echo get_the_title($post_id); ?>" class="img new__img">
                                <div class="new__block _flex_col_center _p_abs">
                                    <h3 class="new__heading _color_fff _fs_26 _fw_400 _f_second"><?php echo get_the_title($post_id); ?></h3>
                                    <span class="date _color_fff _fs_24 _fw_400 _f_second"><?php echo get_the_date('d.m.Y', $post_id); ?></span>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        <?php if (!empty($slider2_posts)) : ?>
            <div class="swiper-container new__slider new__slider-min">
                <div class="swiper-wrapper">
                    <?php foreach ($slider2_posts as $post_id) : ?>
                        <div class="swiper-slide">
                            <a href="<?php echo get_permalink($post_id); ?>" class="new__link _flex_col_center _p_rel">
                                <img src="<?php echo get_the_post_thumbnail_url($post_id, 'medium'); ?>" alt="<?php echo get_the_title($post_id); ?>" class="img new__img">
                                <div class="new__block _flex_col_center _p_abs">
                                    <h3 class="new__heading _color_fff _fs_26 _fw_400 _f_second"><?php echo get_the_title($post_id); ?></h3>
                                    <span class="date _color_fff _fs_24 _fw_400 _f_second"><?php echo get_the_date('d.m.Y', $post_id); ?></span>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="new__swiper_arrow_wrap _flex_end_center container">                
                    <div class="new__swiper-button-prev swiper-button-prev arrow-prev arrow"></div>
                    <div class="new__swiper-button-next swiper-button-next arrow-next arrow"></div>
                </div>
            </div>
        <?php endif; ?>
      <?php else : ?>
    <p>Постов нет.</p>
    <?php endif; ?>
  </section>
</main>
<?php get_footer(); ?>