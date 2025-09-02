<?php
/**
 * Настройка связанных товаров
 */
add_filter('woocommerce_output_related_products_args', function($args) {
    $args['posts_per_page'] = 6; // сколько выводить
    $args['columns'] = 3;        // в сколько колонок
    return $args;
});
