<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package kipr
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<title><?php the_title(); ?></title>
	<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/main.css">
  <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
  <script src="https://kit.fontawesome.com/6029ad4269.js" crossorigin="anonymous"></script>

	<?php wp_head(); ?>
</head>
<div class="wrapper">
  <header class="header background-green">
    <div class="header__container container flex">
      <a href="/" class="heder__logo logo">
        <img  src="<?php the_field('logotipe', 'option'); ?>" alt="logotipe"  class="logo__img img">
      </a>
      <div class="header__nav_wrap _mobile">
        <nav class="header__menu menu flex">
          <div class="menu__section">
            <a href="/categories/" class="menu__btn flex" data-toggle="dropdown-1">
              <span>Catalog</span>
              <img src="<?php echo get_template_directory_uri(); ?>/img/sistem/arrow.svg" alt="arrow" class="menu__btn-arrow">
            </a>
            <div class="menu__dropdown_hidden" data-toggle="dropdown-1">
                <ul class="menu__dropdown direction _flex_col">
                    <?php
$args = array(
    'taxonomy' => 'product_cat',
    'parent' => 0,
    'hide_empty' => false,
    'exclude' => array(Get_ID_By_Slug('cannabinoids')), // Исключаем категорию по ID
);

$categories = get_terms($args);

if (!empty($categories) && !is_wp_error($categories)) {
    foreach ($categories as $category) {
        echo '<li class="menu__dropdown-item">
                <a href="' . get_term_link($category) . '" class="menu__dropdown-link">' . $category->name . '</a>
              </li>';
    }
}
?>
                </ul>
            </div>
          </div>
          <div class="menu__section">
    <a href="<?php echo get_term_link('cannabinoids', 'product_cat'); ?>" class="menu__btn flex" data-toggle="dropdown-2">
        <span>Cannabinoids</span>
        <img src="<?php echo get_template_directory_uri(); ?>/img/sistem/arrow.svg" alt="arrow" class="menu__btn-arrow">
    </a>
    <div class="menu__dropdown_hidden" data-toggle="dropdown-2">
        <ul class="menu__dropdown direction _flex_col">
            <?php
            $args = array(
                'taxonomy' => 'product_cat',
                'parent' => 0,
                'hide_empty' => false,
                'exclude' => Get_ID_By_Slug('cannabinoids') // Исключаем категорию cannabinoids
            );
            $categories = get_terms($args);
            
            if (!empty($categories) && !is_wp_error($categories)) {
                foreach ($categories as $category) {
                    echo '<li class="menu__dropdown-item">
                        <a href="' . get_term_link($category) . '" class="menu__dropdown-link">' . $category->name . '</a>
                    </li>';
                }
            }
            ?>
        </ul>
    </div>
</div>
          <div class="menu__section">
            <a href="/news/" class="menu__btn flex" data-toggle="dropdown-3">
              <span>News</span>
              <img src="<?php echo get_template_directory_uri(); ?>/img/sistem/arrow.svg" alt="arrow" class="menu__btn-arrow">
            </a>
            <div class="menu__dropdown_hidden" data-toggle="dropdown-3">
              <ul class="menu__dropdown direction _flex_col">
              <?php
                $categories = get_categories(array(
                    'hide_empty' => false, // Показывать даже пустые категории
                ));

                if (!empty($categories)) {
                    foreach ($categories as $category) {
                        // Указываем статический URL страницы "Новости"
                        $news_page_url = '/news/';
                        $category_link = add_query_arg('category', $category->slug, $news_page_url);
                        echo '<li class="menu__dropdown-item">
                            <a href="' . esc_url($category_link) . '" class="menu__dropdown-link">' . esc_html($category->name) . '</a>
                        </li>';
                    }
                }
              ?>
              </ul>
            </div>
          </div>
          <div class="menu__section">
            <a href="/about-us" class="menu__btn flex" data-toggle="dropdown-4">
              <span>About Us</span>
              <img src="<?php echo get_template_directory_uri(); ?>/img/sistem/arrow.svg" alt="arrow" class="menu__btn-arrow">
            </a>
            <div class="menu__dropdown_hidden" data-toggle="dropdown-4">
              <ul class="menu__dropdown direction _flex_col">
                <li class="menu__dropdown-item">
                  <a href="/about-us" class="menu__dropdown-link">About us</a>
                </li>
                <li class="menu__dropdown-item">
                  <a href="/partnership" class="menu__dropdown-link">Partnership</a>
                </li>
              </ul>
            </div>
          </div>
        </nav>
        <form role="search" method="get" class="search-form _search_form_field _p_rel" action="<?php echo esc_url( home_url( '/' ) ); ?>">
          <input type="search" id="search-input" class="search-field _input_style" placeholder="Search" value="<?php echo get_search_query(); ?>" name="s" />
          <button type="submit" class="search-submit _search_btn _p_abs" aria-label="Поиск">
            <img src="<?php echo get_template_directory_uri(); ?>/img/sistem/search.svg" alt="" class="search__img">
          </button>
        </form>
      </div>
      <a href="<?php echo wc_get_cart_url(); ?>" class="card btn-imag">
          <img src="<?php echo get_template_directory_uri(); ?>/img/sistem/card.svg" alt="Корзина" class="card__img img">
          <span class="cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
      </a>
      <button class="burger__btn" type="button"><span></span><span></span><span></span></button>
    </div>
  </header>