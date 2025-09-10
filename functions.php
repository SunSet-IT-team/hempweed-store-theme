<?php
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
add_action('after_setup_theme', 'kipr_setup');

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
 * Include additional files
 */
require get_template_directory() . '/inc/custom-header.php';
require get_template_directory() . '/inc/template-tags.php';
require get_template_directory() . '/inc/template-functions.php';
require get_template_directory() . '/inc/customizer.php';

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
        background: 'f8d7da';
        border: 1px solid 'f5c6cb';
        color: '721c24';
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
        
        // Очищаем номер от форматирования
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

function Get_ID_By_Slug($slug) {
    $term = get_term_by('slug', $slug, 'product_cat');
    return $term ? $term->term_id : 0;
}
// Редирект после оформления заказа на кастомную страницу "Спасибо"
add_filter( 'woocommerce_get_checkout_order_received_url', 'my_custom_thankyou_url', 10, 2 );

function my_custom_thankyou_url( $url, $order_id ) {
    // Подставляем ссылку на твою страницу "Спасибо"
    return site_url( '/thank-you/' );
}

// Регистрируем таксономию "Cannabinoids"
function register_cannabinoids_taxonomy() {
    $labels = array(
        'name'              => 'Cannabinoids',
        'singular_name'     => 'Cannabinoid',
        'search_items'      => 'Search Cannabinoids',
        'all_items'         => 'All Cannabinoids',
        'parent_item'       => 'Parent Cannabinoid',
        'parent_item_colon' => 'Parent Cannabinoid:',
        'edit_item'         => 'Edit Cannabinoid',
        'update_item'       => 'Update Cannabinoid',
        'add_new_item'      => 'Add New Cannabinoid',
        'new_item_name'     => 'New Cannabinoid',
        'menu_name'         => 'Cannabinoids',
    );

    $args = array(
        'hierarchical'      => true, // как категории (true), а не как теги (false)
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'cannabinoids' ),
    );

    // Подвязываем к товарам (product)
    register_taxonomy( 'cannabinoids', array( 'product' ), $args );
}
add_action( 'init', 'register_cannabinoids_taxonomy' );
