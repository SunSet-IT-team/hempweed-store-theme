<?php
/**
 * Фолбэк-редирект на "Спасибо за заказ"
 */
add_action('woocommerce_thankyou', function ($order_id) {
    if (is_admin() || wp_doing_ajax()) {
        return;
    }

    // если уже на правильном эндпоинте — ничего не делаем
    if (function_exists('is_wc_endpoint_url') && is_wc_endpoint_url('order-received')) {
        return;
    }

    $order = wc_get_order($order_id);
    if ($order instanceof WC_Order) {
        $url = $order->get_checkout_order_received_url();
        if ($url) {
            wp_safe_redirect($url);
            exit;
        }
    }
}, 1);
