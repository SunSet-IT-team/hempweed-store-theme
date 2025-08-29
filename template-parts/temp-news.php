<?php
/**
 * Template Name: Новости
**/

get_header();

// Определяем текущую страницу
$paged = max(1, get_query_var('paged', 1));

// Количество постов на страницу (18 + 18 = 36)
$posts_per_page = 36;
?>

<main class="main new_list">
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
        <li>News</li>
      </ul>
      <h1 class="title size-50"><?php echo the_title(); ?></h1>
      
      <!-- Динамический список категорий -->
      <ul class="filter__category">
        <?php
        // Получаем все категории
        $categories = get_categories(array(
          'orderby' => 'name',
          'order'   => 'ASC'
        ));

        // Генерируем ссылки для каждой категории
        foreach ($categories as $category) :
          $category_link = add_query_arg('category', $category->slug, get_permalink());
        ?>
          <li>
            <a href="<?php echo esc_url($category_link); ?>" <?php echo (isset($_GET['category']) && $_GET['category'] === $category->slug) ? 'class="active"' : ''; ?>>
              <?php echo esc_html($category->name); ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
  <img src="<?php echo get_template_directory_uri(); ?>/img/home/cannabis-marijuana-leaf-closeup.png" alt="" class="banner__img img">
</section>

<?php
// Запрос постов
$args = array(
  'post_type'      => 'post',
  'posts_per_page' => $posts_per_page,
  'paged'          => $paged,
  'orderby'        => 'date',
  'order'          => 'DESC'
);

// Добавляем фильтр по категории, если она выбрана
if (!empty($_GET['category'])) {
  $args['category_name'] = sanitize_text_field($_GET['category']);
}

$query = new WP_Query($args);

// Правильный расчет количества страниц
$total_pages = $query->max_num_pages;

// Если есть посты, выводим контент
if ($query->have_posts()) :
  ?>

  <section class="news">
      <?php
      // Разделяем посты на два слайдера
      $counter = 0;
      $slider1_posts = [];
      $slider2_posts = [];
      $slider3_posts = [];
      $slider4_posts = [];

      while ($query->have_posts()) : $query->the_post();
          if ($counter < 9) {
            $slider1_posts[] = get_the_ID();
        } elseif ($counter < 18) {
            $slider2_posts[] = get_the_ID();
        } elseif ($counter < 27) {
            $slider3_posts[] = get_the_ID();
        } else {
            $slider4_posts[] = get_the_ID();
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



<!-- баннер -->
  <?php
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => 1, // Выбираем только 1 товар
        'orderby'        => 'rand', // Рандомизация при каждом обновлении страницы
        'meta_query'     => array(
            'relation' => 'AND', // Оба мета-ключа должны присутствовать
            array(
                'key'   => 'new_tovar',
                'value' => '1',
            ),
            array(
                'key'   => 'total_new',
                'compare' => 'EXISTS', // Проверяем, что этот ключ существует
            )
        )
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) : 
        while ($query->have_posts()) : $query->the_post();
            global $product; // Получаем объект продукта WooCommerce
  ?>
      <section class="banners-inform banners-inform__news container flex">
          <div class="banners-inform__news-item">
            <span class="new">new</span>
            <span class="price size-26">from <?php echo $product->get_price_html(); ?></span>
            <?php if (has_post_thumbnail()) : ?>
                <img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'medium'); ?>" alt="<?php the_title(); ?>" class="img banners-inform__img">
            <?php endif; ?>
          </div>
          <div class="banners-inform__block column">
              <h2 class="banners-inform__title title size-50">
                <?php 
                  $title = wp_strip_all_tags(get_the_title()); 
                  echo mb_substr($title, 0, 70) . (mb_strlen($title) > 70 ? '...' : '');
                ?>
              </h2>
              <div class="text size-30">
                <?php 
                  $description = wp_strip_all_tags(get_the_excerpt()); 
                  echo mb_substr($description, 0, 200) . (mb_strlen($description) > 200 ? '...' : '');
                ?>
              </div>
              <a href="<?php the_permalink(); ?>" class="btn btn_yellow">Order</a>
          </div>
      </section>
  <?php 
        endwhile;
        wp_reset_postdata();
    endif;
  ?>


        <?php if (!empty($slider3_posts)) : ?>
            <div class="swiper-container new__slider">
                <div class="swiper-wrapper">
                    <?php foreach ($slider3_posts as $post_id) : ?>
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
        <?php if (!empty($slider4_posts)) : ?>
            <div class="swiper-container new__slider new__slider-min">
                <div class="swiper-wrapper">
                    <?php foreach ($slider4_posts as $post_id) : ?>
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

        <!-- Пагинация -->
        <?php if ($total_pages > 1) : ?>
            <div class="pagination container">
                <?php
                echo paginate_links(array(
                    'base'      => get_permalink() . '%_%',
                    'format'    => 'page/%#%/',
                    'current'   => $paged,
                    'total'     => $total_pages,
                    'prev_text' => '←',
                    'next_text' => '→',
                ));
                ?>
            </div>
        <?php endif; ?>
    </section>


<?php else : ?>
    <p>Постов нет.</p>
<?php endif; ?>


</main>
<?php get_footer(); ?>