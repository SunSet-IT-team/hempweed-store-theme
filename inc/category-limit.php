<?php
/**
 * inc/checkout-fixes.php
 * Фолбэк-редирект на "Спасибо за заказ" (order-received),
 * если штатный маршрут WooCommerce по какой-то причине не сработал.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function hempweed_force_thankyou_redirect( $order_id ) {
    // Не выполняем в админке и не во время AJAX
    if ( is_admin() || wp_doing_ajax() ) {
        return;
    }

    // Если уже на правильном эндпоинте — ничего не делаем
    if ( function_exists( 'is_wc_endpoint_url' ) && is_wc_endpoint_url( 'order-received' ) ) {
        return;
    }

    if ( empty( $order_id ) ) {
        return;
    }

    $order = wc_get_order( absint( $order_id ) );
    if ( ! $order instanceof WC_Order ) {
        return;
    }

    $url = $order->get_checkout_order_received_url();
    if ( $url ) {
        wp_safe_redirect( $url );
        exit;
    }
}
add_action( 'woocommerce_thankyou', 'hempweed_force_thankyou_redirect', 1 );
