<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package kipr
 */

get_header();
?>

	<main id="primary" class="site-main main">

	<?php
	// Проверяем, есть ли запись
	if ( have_posts() ) :
			while ( have_posts() ) : the_post(); ?>
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
								<li><a href="/news">News</a></li>
								<li>/</li>
								<li><?php echo the_title(); ?></li>
							</ul>
							<h1 class="title size-50"><?php echo the_title(); ?></h1>
						</div>
					</div>
					<img src="<?php echo get_template_directory_uri(); ?>/img/home/cannabis-marijuana-leaf-closeup.png" alt="" class="banner__img img">
				</section>
			
				<section class="relative">
				    <div class="news-list__content container">
    					<div class="news-list__content-video"><?php the_field('link_to_youtube_video'); ?></div>
    					<?php the_content(); ?>
    				</div>
                    <img src="<?php echo get_template_directory_uri(); ?>/img/background/background-list/bg11.png" class="back__img bk11 back__left">
                    <img src="<?php echo get_template_directory_uri(); ?>/img/background/background-list/bg12.png" class="back__img bk12 back__right">
				</section>

					<?php
        // Получаем первую рубрику текущей записи
        $categories = get_the_category();
        if (!empty($categories)) {
            $category_slug = $categories[0]->slug; // Берём ярлык (slug) первой рубрики

            // Ищем ID категории товаров WooCommerce с таким же slug
            $product_cat = get_terms(array(
                'taxonomy'   => 'product_cat',
                'hide_empty' => false,
                'slug'       => $category_slug, // Ищем категорию товаров с таким же ярлыком
            ));

            if (!empty($product_cat)) {
                $product_cat_id = $product_cat[0]->term_id; // Берём ID найденной категории товаров

                // Запрос товаров WooCommerce
                $args = array(
                    'post_type'      => 'product',
                    'posts_per_page' => 8, // Количество товаров
                    'tax_query'      => array(
                        array(
                            'taxonomy' => 'product_cat',
                            'field'    => 'term_id',
                            'terms'    => $product_cat_id, // Фильтр по категории товаров
                        ),
                    ),
                    'meta_query'     => array(
                        array(
                            'key'     => 'total_new',
                            'compare' => 'EXISTS', // Фильтр по мета-ключу "total_new"
                        ),
                    ),
                );

                $products = new WP_Query($args);

                if ($products->have_posts()) :
                    ?>
                    <section class="popular background__yellow background__beige">
                        <div class="popular__container column container">
                            <div class="popular__title">
                                <h2 class="title size-50 center">Related Products</h2>
                            </div>
                            <div class="swiper popular__slider">
                                <div class="swiper-wrapper">
                                    <?php
                                    while ($products->have_posts()) : $products->the_post();
                                        $product = wc_get_product(get_the_ID());
                                        ?>
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
                                <div class="popular__swiper-button-next swiper-button-next arrow-next arrow"></div>
                                <div class="popular__swiper-button-prev swiper-button-prev arrow-prev arrow"></div>
                            </div>
                        </div>
                        <img src="<?php echo get_template_directory_uri(); ?>/img/background/background-list/bk6.png" class="back__img bk6 back__left">
                        <img src="<?php echo get_template_directory_uri(); ?>/img/background/background-list/bk7.png" class="back__img bk7 back__right">
                    </section>
                    <?php
                    wp_reset_postdata();
                endif;
            }
        }
        ?>
			
			<?php endwhile;
	endif;
	?>

	</main><!-- #main -->

<?php
get_footer();
