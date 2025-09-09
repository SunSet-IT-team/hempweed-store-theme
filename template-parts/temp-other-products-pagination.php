<?php
// Защита: если $displayed_products не определена — сделаем её массивом
$displayed_products = (isset($displayed_products) && is_array($displayed_products)) ? $displayed_products : [];

// Определяем текущую страницу (учитываем ?product-page=)
$current = 1;
if ( get_query_var('product-page') ) {
    $current = absint( get_query_var('product-page') );
} elseif ( isset($_GET['product-page']) ) {
    $current = absint( $_GET['product-page'] );
} elseif ( get_query_var('paged') ) {
    $current = absint( get_query_var('paged') );
}

// WP_Query: товары только из категории cannabinoids
$args = [
    'post_type'      => 'product',
    'posts_per_page' => 12, // 12 товаров на страницу
    'paged'          => $current,
    'post__not_in'   => $displayed_products,
    'tax_query'      => [[
        'taxonomy' => 'product_cat',
        'field'    => 'slug',
        'terms'    => 'cannabinoids',
        'operator' => 'IN'
    ]]
];

$query = new WP_Query( $args );
?>

<?php if ( $query->have_posts() ) : ?>
    <section class="relative">
        <div class="product__other_section _p_rel _flex_col_center container">
            <h2 class="title size-50 center">Other products</h2>

            <div class="product__other_slider">
                <div class="product__other_slider_wrapper">
                    <?php
                    $counter = 0;
                    while ( $query->have_posts() ) : $query->the_post();
                        // Открываем обёртку строки каждые 3 карточки
                        if ( $counter % 3 == 0 ) : ?>
                            <div class="product__other_slider_wrapper_item">
                                <div class="product__other_slider_wrapper_item_cntr">
                        <?php endif; ?>

                        <?php get_template_part('template-parts/temp-product-card'); ?>

                        <?php
                        $counter++;
                        // Закрываем строку после 3 карточек
                        if ( $counter % 3 == 0 ) : ?>
                                </div>
                            </div>
                        <?php endif;
                    endwhile;

                    // Если последний ряд неполный — закрыть его
                    if ( $counter % 3 != 0 ) : ?>
                                </div>
                            </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- --- Точная пагинация в стиле WooCommerce (nav.woocommerce-pagination > ul.page-numbers > li ... ) --- -->
            <?php
            $total = $query->max_num_pages;

            if ( $total > 1 ) :
                // Базовый URL: текущий URL без параметров product-page/paged
                $base_url = remove_query_arg( array( 'product-page', 'paged' ) );
                echo '<nav class="woocommerce-pagination" aria-label="Product Pagination">';
                echo '<ul class="page-numbers">';

                // Prev
                if ( $current > 1 ) {
                    $prev_link = esc_url( add_query_arg( 'product-page', $current - 1, $base_url ) );
                    echo '<li><a class="prev page-numbers" href="' . $prev_link . '">&larr;</a></li>';
                }

                // Номера страниц
                for ( $i = 1; $i <= $total; $i++ ) {
                    if ( $i === $current ) {
                        echo '<li><span aria-label="Page ' . $i . '" aria-current="page" class="page-numbers current">' . $i . '</span></li>';
                    } else {
                        $page_link = esc_url( add_query_arg( 'product-page', $i, $base_url ) );
                        echo '<li><a aria-label="Page ' . $i . '" class="page-numbers" href="' . $page_link . '">' . $i . '</a></li>';
                    }
                }

                // Next
                if ( $current < $total ) {
                    $next_link = esc_url( add_query_arg( 'product-page', $current + 1, $base_url ) );
                    echo '<li><a class="next page-numbers" href="' . $next_link . '">&rarr;</a></li>';
                }

                echo '</ul>';
                echo '</nav>';
            endif;
            ?>
            <!-- --- /Пагинация --- -->

        </div>
    </section>
<?php endif; wp_reset_postdata(); ?>