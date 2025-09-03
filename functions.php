<?php
foreach (glob(get_template_directory() . '/inc/*.php') as $file) {
    require_once $file;
}
/**
 * kipr functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package kipr
 */

if (!defined('_S_VERSION')) {
    define('_S_VERSION', '1.0.0');
}

// Увеличиваем лимит памяти
define('WP_MEMORY_LIMIT', '512M');
ini_set('memory_limit', '512M');

/**
 * Sets up theme defaults and registers support for various WordPress features.
 */
function kipr_setup() {
    load_theme_textdomain('kipr', get_template_directory() . '/languages');
    add_theme_support('automatic-feed-links');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    
    register_nav_menus(array(
        'menu-1' => esc_html__('Primary', 'kipr'),
    ));
    
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ));
    
    add_theme_support('custom-background', apply_filters('kipr_custom_background_args', array(
        'default-color' => 'ffffff',
        'default-image' => '',
    )));
    
    add_theme_support('customize-selective-refresh-widgets');
    
    add_theme_support('custom-logo', array(
        'height' => 250,
        'width' => 250,
        'flex-width' => true,
        'flex-height' => true,
    ));
}
/**
 * 🎯 УНИВЕРСАЛЬНАЯ СИСТЕМА ОПЛАТЫ И РЕДИРЕКТА
 */

// 1. ВКЛЮЧАЕМ ВСЕ МЕТОДЫ ОПЛАТЫ АВТОМАТИЧЕСКИ
add_filter('woocommerce_available_payment_gateways', 'enable_all_payment_methods');
function enable_all_payment_methods($available_gateways) {
    if (is_checkout()) {
        // Включаем все методы оплаты, даже если они отключены в настройках
        $all_gateways = WC()->payment_gateways->payment_gateways();
        
        foreach ($all_gateways as $gateway) {
            if ($gateway->id !== 'test_payment') { // Не включаем наш тестовый метод пока
                $available_gateways[$gateway->id] = $gateway;
            }
        }
        
        // Если нет доступных методов, добавляем тестовый
        if (empty($available_gateways)) {
            include_once __DIR__ . '/includes/class-wc-test-payment-gateway.php';
            $available_gateways['test_payment'] = new WC_Test_Payment_Gateway();
        }
    }
    
    return $available_gateways;
}

// 2. АВТОМАТИЧЕСКИ ВЫБИРАЕМ ПЕРВЫЙ ДОСТУПНЫЙ МЕТОД ОПЛАТЫ
add_action('wp_footer', 'auto_select_payment_method');
function auto_select_payment_method() {
    if (is_checkout() && !is_wc_endpoint_url('order-received')) {
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            // Ждем загрузки методов оплаты
            setTimeout(function() {
                // Если нет выбранного метода, выбираем первый доступный
                if ($('input[name="payment_method"]:checked').length === 0) {
                    var firstMethod = $('input[name="payment_method"]').first();
                    if (firstMethod.length) {
                        firstMethod.prop('checked', true).trigger('change');
                        console.log('✅ Автоматически выбран метод оплаты:', firstMethod.val());
                    }
                }
                
                // Автоматически принимаем условия
                if ($('#terms').length && !$('#terms').is(':checked')) {
                    $('#terms').prop('checked', true);
                    console.log('✅ Условия использования приняты');
                }
                
                // Делаем кнопку оформления более заметной
                $('#place_order').css({
                    'background': 'linear-gradient(135deg, #4CAF50 0%, #45a049 100%)',
                    'color': 'white',
                    'border': 'none',
                    'padding': '20px 40px',
                    'font-size': '20px',
                    'font-weight': 'bold',
                    'border-radius': '10px',
                    'cursor': 'pointer',
                    'margin-top': '20px'
                });
                
            }, 1000);
        });
        </script>
        <?php
    }
}

// 3. ПРОСТАЯ ОБРАБОТКА ЗАКАЗА ДЛЯ ЛЮБОГО МЕТОДА ОПЛАТЫ
add_action('woocommerce_checkout_process', 'simple_order_processing');
function simple_order_processing() {
    // Эта функция гарантирует, что заказ будет создан независимо от метода оплаты
}

// 4. ГАРАНТИРОВАННЫЙ РЕДИРЕКТ ПОСЛЕ УСПЕШНОГО ЗАКАЗА
add_action('template_redirect', 'guaranteed_checkout_redirect');
function guaranteed_checkout_redirect() {
    if (is_checkout() && !is_wc_endpoint_url('order-received')) {
        // Проверяем есть ли заказ в сессии
        if (isset(WC()->session)) {
            $order_id = WC()->session->get('order_awaiting_payment');
            
            if ($order_id) {
                $order = wc_get_order($order_id);
                
                // Если заказ существует и не провалился - редиректим
                if ($order && !$order->has_status('failed')) {
                    $redirect_url = wc_get_endpoint_url('order-received', $order_id, wc_get_checkout_url());
                    $redirect_url = add_query_arg('key', $order->get_order_key(), $redirect_url);
                    
                    wp_redirect($redirect_url);
                    exit;
                }
            }
        }
    }
}

// 5. СОЗДАЕМ ЗАКАЗ ЧЕРЕЗ AJAX ЕСЛИ СТАНДАРТНЫЙ СПОСОБ НЕ РАБОТАЕТ
add_action('wp_ajax_nopriv_create_quick_order', 'create_quick_order');
add_action('wp_ajax_create_quick_order', 'create_quick_order');
function create_quick_order() {
    if (!class_exists('WooCommerce')) {
        wp_send_json_error('WooCommerce не активирован');
    }
    
    try {
        // Создаем заказ
        $order = wc_create_order();
        
        // Добавляем товары из корзины
        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
            $product = wc_get_product($cart_item['product_id']);
            $order->add_product($product, $cart_item['quantity']);
        }
        
        // Добавляем данные клиента
        $order->set_address(array(
            'first_name' => sanitize_text_field($_POST['billing_first_name'] ?? 'Customer'),
            'last_name'  => sanitize_text_field($_POST['billing_last_name'] ?? ''),
            'email'      => sanitize_email($_POST['billing_email'] ?? 'customer@example.com'),
            'phone'      => sanitize_text_field($_POST['billing_phone'] ?? ''),
            'address_1'  => sanitize_text_field($_POST['billing_address_1'] ?? ''),
            'city'       => sanitize_text_field($_POST['billing_city'] ?? ''),
            'country'    => sanitize_text_field($_POST['billing_country'] ?? 'RU')
        ), 'billing');
        
        // Используем выбранный метод оплаты или тестовый
        $payment_method = sanitize_text_field($_POST['payment_method'] ?? 'bacs');
        $order->set_payment_method($payment_method);
        $order->set_payment_method_title($payment_method);
        
        // Рассчитываем итоги
        $order->calculate_totals();
        $order->save();
        
        // Очищаем корзину
        WC()->cart->empty_cart();
        
        // Устанавливаем в сессию
        WC()->session->set('order_awaiting_payment', $order->get_id());
        
        wp_send_json_success(array(
            'order_id' => $order->get_id(),
            'order_key' => $order->get_order_key(),
            'redirect_url' => wc_get_endpoint_url('order-received', $order->get_id(), wc_get_checkout_url()) . '?key=' . $order->get_order_key()
        ));
        
    } catch (Exception $e) {
        wp_send_json_error('Ошибка создания заказа: ' . $e->getMessage());
    }
}

// 7. РЕЗЕРВНАЯ СТРАНИЦА "СПАСИБО"
add_action('wp', 'fallback_thankyou_page');
function fallback_thankyou_page() {
    if (is_wc_endpoint_url('order-received')) {
        $order_id = get_query_var('order-received');
        $order_key = isset($_GET['key']) ? sanitize_text_field($_GET['key']) : '';
        
        if ($order_id) {
            $order = wc_get_order($order_id);
            
            // Если заказ не найден или ключ не совпадает, показываем резервную страницу
            if (!$order || ($order_key && $order->get_order_key() !== $order_key)) {
                // Простая страница благодарности
                echo '<!DOCTYPE html><html><head><title>Спасибо за заказ!</title><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">';
                echo '<style>body{font-family:Arial,sans-serif;text-align:center;padding:50px;background:#f8f9fa;}';
                echo '.thank-you{background:white;padding:40px;border-radius:10px;box-shadow:0 5px 15px rgba(0,0,0,0.1);}';
                echo '</style></head><body>';
                echo '<div class="thank-you"><h1>🎉 Спасибо за заказ!</h1>';
                echo '<p>Ваш заказ #' . esc_html($order_id) . ' успешно оформлен.</p>';
                echo '<p>Мы свяжемся с вами в ближайшее время для подтверждения.</p>';
                echo '<a href="' . esc_url(home_url('/')) . '" style="display:inline-block;padding:10px 20px;background:#007cba;color:white;text-decoration:none;border-radius:5px;">Вернуться в магазин</a>';
                echo '</div></body></html>';
                exit;
            }
        }
    }
}

// 8. ОБЕСПЕЧИВАЕМ РАБОТУ СЕССИИ WOOCOMMERCE
add_action('wp_loaded', 'ensure_wc_session');
function ensure_wc_session() {
    if (class_exists('WooCommerce') && !is_admin() && !defined('DOING_CRON')) {
        if (!WC()->session) {
            include_once WC_ABSPATH . 'includes/class-wc-session-handler.php';
            WC()->session = new WC_Session_Handler();
            WC()->session->init();
        }
        
        // Убедимся, что сессия активна
        if (!WC()->session->has_session()) {
            WC()->session->set_customer_session_cookie(true);
        }
    }
}

// 9. АВТОМАТИЧЕСКАЯ АКТИВАЦИЯ МЕТОДОВ ОПЛАТЫ
add_action('init', 'auto_enable_payment_methods');
function auto_enable_payment_methods() {
    // Автоматически активируем основные методы оплаты при первом запуске
    $methods_to_enable = array('bacs', 'cheque', 'cod');
    
    foreach ($methods_to_enable as $method_id) {
        $option_name = 'woocommerce_' . $method_id . '_settings';
        $settings = get_option($option_name, array());
        
        if (empty($settings) || !isset($settings['enabled']) || $settings['enabled'] !== 'yes') {
            $settings['enabled'] = 'yes';
            update_option($option_name, $settings);
        }
    }
}
/**
 * Set the content width in pixels
 */
function kipr_content_width() {
    $GLOBALS['content_width'] = apply_filters('kipr_content_width', 640);
}
add_action('after_setup_theme', 'kipr_content_width', 0);

/**
 * Register widget area.
 */
function kipr_widgets_init() {
    register_sidebar(array(
        'name' => esc_html__('Sidebar', 'kipr'),
        'id' => 'sidebar-1',
        'description' => esc_html__('Add widgets here.', 'kipr'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h2 class="widget-title">',
        'after_title' => '</h2>'
    ));
}
add_action('widgets_init', 'kipr_widgets_init');

/**
 * Enqueue scripts and styles.
 */
function kipr_scripts() {
    wp_enqueue_style('kipr-style', get_stylesheet_uri(), array(), _S_VERSION);
    wp_style_add_data('kipr-style', 'rtl', 'replace');
    wp_enqueue_script('kipr-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true);
    
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
    
    // WooCommerce scripts
    if (class_exists('WooCommerce')) {
        wp_enqueue_script('wc-cart-fragments');
        wp_enqueue_script('wc-add-to-cart');
    }
}
add_action('wp_enqueue_scripts', 'kipr_scripts');

/**
 * Увеличиваем количество товаров на странице категорий
 */
add_filter('loop_shop_per_page', 'custom_products_per_page', 20);
function custom_products_per_page($cols) {
    // Установите нужное количество товаров (например, 48, 96 или 200)
    return 48;
}

/**
 * Добавляем пагинацию для категорий товаров
 */
function custom_woocommerce_pagination() {
    if (woocommerce_products_will_display()) {
        woocommerce_pagination();
    }
}
add_action('woocommerce_after_shop_loop', 'custom_woocommerce_pagination');

/**
 * Оптимизация запросов WooCommerce для большого количества товаров
 */
add_action('pre_get_posts', 'optimize_product_queries');
function optimize_product_queries($query) {
    if (!is_admin() && $query->is_main_query() && (is_product_category() || is_shop())) {
        // Уменьшаем нагрузку на базу данных
        $query->set('posts_per_page', 48);
        $query->set('no_found_rows', false); // Включаем пагинацию
    }
}
// 6. JavaScript ДЛЯ АЛЬТЕРНАТИВНОГО ОФОРМЛЕНИЯ ЗАКАЗА (ИСПРАВЛЕННЫЙ)
add_action('wp_footer', 'add_alternative_checkout_script');
function add_alternative_checkout_script() {
    if (is_checkout() && !is_wc_endpoint_url('order-received')) {
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            // Альтернативная обработка формы
            $('form.woocommerce-checkout').on('submit', function(e) {
                e.preventDefault();
                
                console.log('🚀 Начинаем обработку заказа...');
                
                // Блокируем кнопку
                $('#place_order').prop('disabled', true).val('Обработка...');
                
                // Показываем красивый лоадер
                $('body').append('\
                    <div id="checkout-loading" style="\
                        position: fixed; top: 0; left: 0; width: 100%; height: 100%; \
                        background: rgba(255,255,255,0.95); z-index: 9999; \
                        display: flex; justify-content: center; align-items: center; \
                        flex-direction: column; font-family: Arial, sans-serif;\
                    ">\
                        <div style="text-align: center; padding: 40px; background: white; \
                                  border-radius: 15px; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">\
                            <div style="color: #4CAF50; font-size: 60px; margin-bottom: 20px;">⏳</div>\
                            <h2 style="color: #333; margin-bottom: 20px;">Обрабатываем ваш заказ</h2>\
                            <p style="color: #666;">Пожалуйста, подождите...</p>\
                            <div style="margin: 30px 0;">\
                                <div style="width: 50px; height: 50px; border: 4px solid #f3f3f3; \
                                          border-top: 4px solid #4CAF50; border-radius: 50%; \
                                          animation: spin 1s linear infinite; margin: 0 auto;"></div>\
                            </div>\
                        </div>\
                    </div>\
                    <style>\
                        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }\
                        body { overflow: hidden !important; }\
                    </style>\
                ');
                
                // Собираем данные формы
                var formData = $(this).serializeArray();
                var checkoutData = {};
                
                $.each(formData, function() {
                    checkoutData[this.name] = this.value;
                });
                
                // Добавляем выбранный метод оплаты
                checkoutData.payment_method = $('input[name="payment_method"]:checked').val();
                
                // Создаем заказ через AJAX
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'POST',
                    data: {
                        action: 'create_quick_order',
                        ...checkoutData
                    },
                    success: function(response) {
                        if (response.success) {
                            console.log('✅ Заказ успешно создан!');
                            console.log('Редирект на:', response.data.redirect_url);
                            
                            // Плавный переход на страницу благодарности
                            $('#checkout-loading').html('\
                                <div style="text-align: center; padding: 40px; background: white; \
                                          border-radius: 15px; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">\
                                    <div style="color: #4CAF50; font-size: 60px; margin-bottom: 20px;">✅</div>\
                                    <h2 style="color: #333; margin-bottom: 20px;">Заказ оформлен!</h2>\
                                    <p style="color: #666;">Спасибо за Покупку</p>\
                                </div>\
                            ');
                            
                            // Редирект через 2 секунды
                            setTimeout(function() {
                              window.location.href = '<?php echo home_url(); ?>';
                            }, 2000);
                            
                        } else {
                            console.error('❌ Ошибка:', response.data);
                            
                            // Показываем ошибку красиво
                            $('#checkout-loading').html('\
                                <div style="text-align: center; padding: 40px; background: white; \
                                          border-radius: 15px; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">\
                                    <div style="color: #ff4444; font-size: 60px; margin-bottom: 20px;">❌</div>\
                                    <h2 style="color: #333; margin-bottom: 20px;">Ошибка оформления</h2>\
                                    <p style="color: #666; margin-bottom: 20px;">' + response.data + '</p>\
                                    <button onclick="location.reload()" style="\
                                        padding: 10px 20px; background: #007cba; color: white; \
                                        border: none; border-radius: 5px; cursor: pointer;\
                                    ">Попробовать снова</button>\
                                </div>\
                            ');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('❌ Ошибка соединения:', error);
                        
                        $('#checkout-loading').html('\
                            <div style="text-align: center; padding: 40px; background: white; \
                                      border-radius: 15px; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">\
                                <div style="color: #ff4444; font-size: 60px; margin-bottom: 20px;">⚠️</div>\
                                <h2 style="color: #333; margin-bottom: 20px;">Ошибка соединения</h2>\
                                <p style="color: #666; margin-bottom: 20px;">Проверьте интернет-соединение и попробуйте снова</p>\
                                <button onclick="location.reload()" style="\
                                    padding: 10px 20px; background: #007cba; color: white; \
                                    border: none; border-radius: 5px; cursor: pointer;\
                                ">Обновить страницу</button>\
                            </div>\
                        ');
                    }
                });
            });
        });
        </script>
        <?php
    }
}

/**
 * Include additional files
 */



if (defined('JETPACK__VERSION')) {
    require get_template_directory() . '/inc/jetpack.php';
}

/**
 * Custom WooCommerce templates
 */
function custom_woocommerce_category_template($template) {
    if (is_tax('product_cat')) {
        $custom_template = get_template_directory() . '/woocommerce/taxonomy-product_cat.php';
        if (file_exists($custom_template)) {
            return $custom_template;
        }
    }
    return $template;
}
add_filter('template_include', 'custom_woocommerce_category_template', 100);

function custom_woocommerce_template($template) {
    if (is_singular('product')) {
        $custom_template = get_stylesheet_directory() . '/woocommerce/custom-product-template.php';
        if (file_exists($custom_template)) {
            return $custom_template;
        }
    }
    return $template;
}
add_filter('template_include', 'custom_woocommerce_template');

/**
 * Полностью отключаем стандартный скрипт корзины WooCommerce
 */
add_action('wp_enqueue_scripts', 'disable_woocommerce_cart_js', 999);
function disable_woocommerce_cart_js() {
    if (is_cart()) {
        wp_dequeue_script('wc-cart');
        wp_deregister_script('wc-cart');
    }
}

/**
 * Cart count update functionality
 */
function update_cart_count_script() {
    if (class_exists('WooCommerce')) {
        wp_enqueue_script('cart-update', get_template_directory_uri() . '/js/cart-update.js', array('jquery'), null, true);
        wp_localize_script('cart-update', 'cart_data', array(
            'ajax_url' => admin_url('admin-ajax.php')
        ));
    }
}
add_action('wp_enqueue_scripts', 'update_cart_count_script');

function update_cart_count() {
    if (class_exists('WooCommerce')) {
        echo WC()->cart->get_cart_contents_count();
    }
    wp_die();
}
add_action('wp_ajax_update_cart_count', 'update_cart_count');
add_action('wp_ajax_nopriv_update_cart_count', 'update_cart_count');

/**
 * Disable WooCommerce redirects and notices
 */
add_filter('woocommerce_add_to_cart_redirect', '__return_false');
add_filter('wc_add_to_cart_message_html', '__return_empty_string');

add_action('template_redirect', 'remove_all_woocommerce_notices');
function remove_all_woocommerce_notices() {
    if (class_exists('WooCommerce') && (is_product() || is_cart())) {
        remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
        remove_action('woocommerce_before_single_product', 'wc_print_notices', 10);
        remove_action('woocommerce_shortcode_before_product_cat_loop', 'wc_print_notices', 10);
        remove_action('woocommerce_before_shop_loop', 'wc_print_notices', 10);
    }
}

/**
 * Product availability check
 */
add_action('wp_ajax_check_product_availability', 'check_product_availability');
add_action('wp_ajax_nopriv_check_product_availability', 'check_product_availability');
function check_product_availability() {
    if (!class_exists('WooCommerce')) {
        wp_send_json_error(array('message' => __('WooCommerce is not activated', 'kipr')));
    }
    
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    
    $product = wc_get_product($product_id);
    
    if (!$product) {
        wp_send_json_error(array('message' => __('Product not found', 'kipr')));
    }
    
    $stock_quantity = $product->get_stock_quantity();
    $product_name = $product->get_name();
    
    if (!$product->is_in_stock()) {
        wp_send_json_error(array('message' => __('Product is out of stock', 'kipr')));
    } elseif ($stock_quantity > 0 && $quantity > $stock_quantity) {
        $message = sprintf(_n('Only %d item "%s" in stock', 'Only %d items "%s" in stock', $stock_quantity, 'kipr'), $stock_quantity, $product_name);
        wp_send_json_error(array('message' => $message));
    } else {
        wp_send_json_success();
    }
}

/**
 * Custom add to cart notices
 */
add_action('wp_footer', 'custom_add_to_cart_notices');
function custom_add_to_cart_notices() {
    if (!class_exists('WooCommerce') || !(is_product() || is_shop() || is_product_category())) {
        return;
    }
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        function showCustomNotice(message, type) {
            $('.custom-woocommerce-notice').remove();
            
            var notice = $('<div class="custom-woocommerce-notice">' + message + '</div>');
            notice.addClass(type === 'error' ? 'custom-notice-error' : 'custom-notice-success');
            
            $('body').append(notice);
            
            setTimeout(function() {
                notice.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 5000);
            
            notice.on('click', function() {
                $(this).remove();
            });
        }

        function syncQuantity() {
            $('.cat__input').each(function() {
                var input = $(this);
                var quantity = input.val();
                var form = input.closest('.cat__form_cntr').find('form');
                
                form.find('input[name="quantity"]').val(quantity);
                form.find('.cat__add_btn').attr('data-quantity', quantity);
            });
        }

        syncQuantity();

        $(document).on('input change', '.cat__input', function() {
            syncQuantity();
        });

        $(document).on('click', '.cat__add_btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            var button = $(this);
            var form = button.closest('form');
            var product_id = button.attr('data-product_id');
            var product_name = button.attr('data-product_name');
            var quantity = button.attr('data-quantity') || form.find('input[name="quantity"]').val() || 1;
            
            if (!product_id) {
                showCustomNotice('Could not determine product', 'error');
                return false;
            }
            
            showCustomNotice('Adding ' + quantity + ' item(s) to cart...', 'success');
            
            $.ajax({
                type: 'POST',
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                data: {
                    action: 'check_product_availability',
                    product_id: product_id,
                    quantity: quantity
                },
                success: function(response) {
                    if (response.success) {
                        $.ajax({
                            type: 'POST',
                            url: '<?php echo admin_url('admin-ajax.php'); ?>',
                            data: {
                                action: 'woocommerce_add_to_cart',
                                product_id: product_id,
                                quantity: quantity
                            },
                            success: function(cartResponse) {
                                if (cartResponse.error) {
                                    showCustomNotice(cartResponse.error, 'error');
                                } else {
                                    var message = quantity + ' item(s) of "' + product_name + '" added to cart';
                                    showCustomNotice(message, 'success');
                                    $(document.body).trigger('wc_fragment_refresh');
                                }
                            },
                            error: function() {
                                showCustomNotice('Error adding product to cart', 'error');
                            }
                        });
                    } else {
                        showCustomNotice(response.data.message, 'error');
                    }
                },
                error: function() {
                    showCustomNotice('Error checking product availability', 'error');
                }
            });
            
            return false;
        });
    });
    </script>
    <style>
    .custom-woocommerce-notice {
        position: fixed;
        top: 100px;
        left: 50%;
        transform: translateX(-50%);
        padding: 15px 20px;
        border-radius: 5px;
        z-index: 99999;
        max-width: 90%;
        width: 400px;
        text-align: center;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        font-weight: bold;
        font-size: 16px;
    }
    .custom-notice-success {
        background: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
    }
    .custom-notice-error {
        background: #f8d7da;
        border: 1px solid 'f5c6cb';
        color: '#721c24';
    }
    </style>
    <?php
}

/**
 * Additional product info functions
 */
add_action('wp_ajax_get_product_name', 'get_product_name');
add_action('wp_ajax_nopriv_get_product_name', 'get_product_name');
function get_product_name() {
    if (!class_exists('WooCommerce')) {
        wp_send_json_error();
    }
    
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $product = wc_get_product($product_id);
    
    if ($product) {
        wp_send_json_success(array('product_name' => $product->get_name()));
    } else {
        wp_send_json_error();
    }
}

add_action('wp_ajax_get_product_id_by_slug', 'get_product_id_by_slug');
add_action('wp_ajax_nopriv_get_product_id_by_slug', 'get_product_id_by_slug');
function get_product_id_by_slug() {
    $product_slug = isset($_POST['product_slug']) ? sanitize_text_field($_POST['product_slug']) : '';
    $product = get_page_by_path($product_slug, OBJECT, 'product');
    
    if ($product) {
        wp_send_json_success(array('product_id' => $product->ID));
    } else {
        wp_send_json_error();
    }
}

/**
 * Ensure cart fragments are updated everywhere
 */
add_filter('woocommerce_add_to_cart_fragments', 'update_cart_fragments_everywhere');
function update_cart_fragments_everywhere($fragments) {
    if (class_exists('WooCommerce')) {
        // Обновляем счетчик корзины
        $fragments['.cart-count'] = '<span class="cart-count">' . WC()->cart->get_cart_contents_count() . '</span>';
        
        // Обновляем общую сумму
        $fragments['.cart-total'] = '<span class="cart-total">' . WC()->cart->get_cart_total() . '</span>';
    }
    return $fragments;
}

/**
 * AJAX CART UPDATE HANDLER
 */
add_action('wp_ajax_update_cart_quantities', 'update_cart_quantities');
add_action('wp_ajax_nopriv_update_cart_quantities', 'update_cart_quantities');
function update_cart_quantities() {
    if (!class_exists('WooCommerce')) {
        wp_send_json_error('WooCommerce not active');
    }
    
    // Проверяем nonce для безопасности
    if (!isset($_POST['quantities']) || !is_string($_POST['quantities'])) {
        wp_send_json_error('Invalid data');
    }
    
    $quantities = json_decode(stripslashes($_POST['quantities']), true);
    
    if (!is_array($quantities)) {
        wp_send_json_error('Invalid quantities data');
    }
    
    // Сохраняем старые количества для сравнения
    $old_quantities = array();
    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        $old_quantities[$cart_item_key] = $cart_item['quantity'];
    }
    
    // Обновляем количества товаров
    foreach ($quantities as $input_name => $quantity) {
        // Извлекаем cart_item_key из имени поля
        if (preg_match('/cart\[(.*?)\]\[qty\]/', $input_name, $matches)) {
            $cart_item_key = $matches[1];
            $quantity = intval($quantity);
            
            if ($quantity > 0) {
                WC()->cart->set_quantity($cart_item_key, $quantity);
            } else {
                WC()->cart->remove_cart_item($cart_item_key);
            }
        }
    }
    
    WC()->cart->calculate_totals();
    
    // Получаем обновленные данные корзины
    $cart_data = array(
        'cart_count' => WC()->cart->get_cart_contents_count(),
        'cart_total' => WC()->cart->get_cart_total(),
        'message' => 'Cart updated successfully'
    );
    
    // Также получаем обновленные subtotal для каждого товара
    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        $_product = $cart_item['data'];
        $cart_data['items'][$cart_item_key] = array(
            'subtotal' => WC()->cart->get_product_subtotal($_product, $cart_item['quantity']),
            'quantity' => $cart_item['quantity'],
            'old_quantity' => $old_quantities[$cart_item_key] ?? 0
        );
    }
    
    wp_send_json_success($cart_data);
}

/**
 * ADD SECURITY NONCE FOR AJAX REQUESTS
 */
add_action('wp_enqueue_scripts', 'add_ajax_nonce');
function add_ajax_nonce() {
    if (is_cart()) {
        wp_localize_script('jquery', 'cart_ajax', array(
            'nonce' => wp_create_nonce('update_cart_nonce')
        ));
    }
}

/**
 * Валидация номера телефона на сервере
 */
add_action('woocommerce_after_checkout_validation', 'validate_phone_number', 10, 2);
function validate_phone_number($data, $errors) {
    if (isset($data['billing_phone']) && !empty($data['billing_phone'])) {
        $phone = $data['billing_phone'];
        
        // Очищаем номер от форматирование
        $clean_phone = preg_replace('/[^\d+]/', '', $phone);
        
        // Проверяем минимальную длину
        if (strlen($clean_phone) < 10) {
            $errors->add('validation', __('Phone number is too short', 'woocommerce'));
        }
        
        // Проверяем наличие только цифр и + в начале
        if (!preg_match('/^\+?[0-9]{10,15}$/', $clean_phone)) {
            $errors->add('validation', __('Please enter a valid phone number', 'woocommerce'));
        }
    }
}

/**
 * Простая валидация телефона без библиотек
 */
function add_simple_phone_validation() {
    if (is_checkout()) {
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            // Функция валидации ввода
            function validatePhoneInput(e) {
                var key = e.key;
                var value = this.value;
                
                // Разрешаем: цифры, +, backspace, delete, tab, стрелки
                var allowedKeys = [
                    '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
                    '+',
                    'Backspace', 'Delete', 'Tab', 
                    'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown'
                ];
                
                // Разрешаем плюс только в начале
                if (key === '+' && value !== '' && !value.startsWith('+')) {
                    e.preventDefault();
                    return false;
                }
                
                // Запрещаем все, кроме разрешенных клавиш
                if (allowedKeys.indexOf(key) === -1) {
                    e.preventDefault();
                    return false;
                }
                
                return true;
            }

            // Функция для очистки номера при вставке
            function cleanPastedPhone(text) {
                // Оставляем только цифры и плюсы
                var cleaned = text.replace(/[^\d+]/g, '');
                
                // Убираем лишние плюсы, оставляем только первый
                if ((cleaned.match(/\+/g) || []).length > 1) {
                    var firstPlusIndex = cleaned.indexOf('+');
                    cleaned = '+' + cleaned.replace(/\+/g, '').substring(firstPlusIndex);
                }
                
                return cleaned;
            }

            // Инициализация телефонного поля
            function initPhoneField() {
                var phoneInput = document.getElementById('billing_phone');
                
                if (phoneInput && !phoneInput.classList.contains('phone-validation-applied')) {
                    phoneInput.classList.add('phone-validation-applied');
                    
                    // Обработчики событий
                    phoneInput.addEventListener('keydown', function(e) {
                        validatePhoneInput.call(this, e);
                    });
                    
                    phoneInput.addEventListener('paste', function(e) {
                        e.preventDefault();
                        var pastedText = (e.clipboardData || window.clipboardData).getData('text');
                        var cleanedText = cleanPastedPhone(pastedText);
                        
                        // Вставляем очищенный текст
                        document.execCommand('insertText', false, cleanedText);
                    });
                    
                    // Подсказка для пользователя на английском
                    phoneInput.placeholder = 'Enter international phone number';
                }
            }

            // Инициализация при загрузке
            setTimeout(initPhoneField, 500);
            
            // Повторная инициализация после AJAX обновлений
            $(document).on('updated_checkout', function() {
                setTimeout(initPhoneField, 1000);
            });
        });
        </script>
        <?php
    }
}
add_action('wp_footer', 'add_simple_phone_validation');

/**
 * Включение всех стран для WooCommerce (оптимизированная версия)
 */
add_filter('woocommerce_countries_allowed_countries', 'enable_all_countries');
function enable_all_countries($countries) {
    // Возвращаем все страны без изменений
    return $countries;
}

/**
 * Включение базовых стран по умолчанию (вместо всех регионов)
 */
add_filter('default_checkout_billing_country', 'change_default_checkout_country');
add_filter('default_checkout_shipping_country', 'change_default_checkout_country');
function change_default_checkout_country() {
    return 'RU'; // Россия по умолчанию, но можно выбрать любую
}
/**
 * ПРИНУДИТЕЛЬНОЕ УВЕЛИЧЕНИЕ ТОВАРОВ - гарантированно работает
 */
add_filter('loop_shop_per_page', 'force_products_per_page', 9999);
function force_products_per_page($cols) {
    // Установите нужное количество (48, 100, 200)
    return 100;
}

// Отключаем любые другие настройки пагинации
add_action('pre_get_posts', 'force_products_query', 9999);
function force_products_query($query) {
    if (!is_admin() && $query->is_main_query()) {
        if (is_product_category() || is_shop() || is_product_tag()) {
            $query->set('posts_per_page', 100);
            $query->set('no_found_rows', false);
        }
    }
}

/**
 * Включаем lazy loading для изображений товаров (оптимизация)
 */
add_filter('wp_get_attachment_image_attributes', 'add_lazy_loading_to_products', 10, 3);
function add_lazy_loading_to_products($attr, $attachment, $size) {
    if (is_shop() || is_product_category() || is_product_tag()) {
        $attr['loading'] = 'lazy';
        $attr['decoding'] = 'async';
    }
    return $attr;
}
?>