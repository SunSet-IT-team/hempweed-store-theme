<?php
/**
 * Ограничение количества товаров в категориях
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Пример: ограничиваем вывод товаров в определённых категориях
 */
add_action('pre_get_posts', function ($query) {
    // Работает только на витрине (не в админке) и в основном запросе WooCommerce
    if (is_admin() || !$query->is_main_query() || !is_tax('product_cat')) {
        return;
    }

    // Ограничиваем количество товаров только в выбранных категориях
    $limited_categories = ['semena', 'masla']; // 🔹 укажи здесь слаги категорий
    $current_category   = get_queried_object();

    if ($current_category && in_array($current_category->slug, $limited_categories, true)) {
        $query->set('posts_per_page', 12); // 🔹 сколько товаров выводить
    }
});
