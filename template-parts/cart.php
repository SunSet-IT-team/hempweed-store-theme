<?php
/**
 * Template Name: Корзина
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' ); 
get_header();
?>
<main class="main">
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
        <li>Basket</li>
      </ul>
      <h1 class="title size-50">Basket</h1>
    </div>
  </div>
  <img src="<?php echo get_template_directory_uri(); ?>/img/home/cannabis-marijuana-leaf-closeup.png" alt="" class="banner__img img">
</section>

<style>
/* Стили для кнопки */
button[name="update_cart"] {
    opacity: 1 !important;
    cursor: pointer !important;
    pointer-events: all !important;
}

button[name="update_cart"]:disabled {
    opacity: 0.7 !important;
    cursor: not-allowed !important;
}

/* Стили для уведомлений */
.cart-notice {
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
.cart-notice-success {
    background: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
}
.cart-notice-error {
    background: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
}
.cart-notice-info {
    background: #d1ecf1;
    border: 1px solid #bee5eb;
    color: #0c5460;
}
</style>

<form class="woocommerce-cart-form cart flex container" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
    <table class="shop_table shop_table_responsive cart-table woocommerce-cart-form__contents" cellspacing="0">
        <thead>
            <tr>
                <th class="product-name">Product</th>
                <th class="product-price">Price</th>
                <th class="product-quantity">Quantity</th>
                <th class="product-subtotal">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php do_action( 'woocommerce_before_cart_contents' ); ?>

            <?php 
            foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                $_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
                $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

                if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 ) {
                    $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
                    $product_price = $_product->get_price();
                    $product_currency = get_woocommerce_currency_symbol();
                    ?>
                    <tr class="woocommerce-cart-form__cart-item" data-cart-item-key="<?php echo esc_attr($cart_item_key); ?>">
                        <td class="product-name">
                            <?php echo apply_filters( 'woocommerce_cart_item_remove_link',
                                sprintf( '<a href="%s" class="remove" aria-label="%s"><svg width="18" height="21" viewBox="0 0 18 21" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15 6H15.5C16.0523 6 16.5 5.55228 16.5 5V4.5C16.5 3.94772 16.0523 3.5 15.5 3.5H12.5M15 6V18C15 19.1046 14.1046 20 13 20H5C3.89543 20 3 19.1046 3 18V6M15 6H3M12.5 3.5V3C12.5 1.89543 11.6046 1 10.5 1H7.5C6.39543 1 5.5 1.89543 5.5 3V3.5M12.5 3.5H5.5M3 6H2.5C1.94772 6 1.5 5.55228 1.5 5V4.5C1.5 3.94772 1.94772 3.5 2.5 3.5H5.5M7 9.5V16.5M11 9.5V16.5" stroke="black" stroke-width="1.25" stroke-linecap="round" /></svg></a>',
                                    esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
                                    esc_html__( 'Remove this item', 'woocommerce' )
                                ),
                                $cart_item_key ); ?>
                            <?php echo $_product->get_image(); ?>
                            <?php if ( ! $product_permalink ) {
                                echo wp_kses_post( $_product->get_name() );
                            } else {
                                echo sprintf( '<a href="%s" class="name">%s</a>', esc_url( $product_permalink ), wp_kses_post( $_product->get_name() ) );
                            } ?>
                        </td>
                        <td class="product-price" data-price="<?php echo esc_attr($product_price); ?>" data-currency="<?php echo esc_attr($product_currency); ?>">
                          <?php echo wc_price($product_price); ?>
                        </td>
                        <td class="product-quantity">
                          <?php woocommerce_quantity_input( array( 
                              'input_name' => "cart[{$cart_item_key}][qty]", 
                              'input_value' => $cart_item['quantity'], 
                              'classes' => ['cat__input'],
                              'min_value' => 1,
                              'max_value' => $_product->get_max_purchase_quantity()
                          ) ); ?>
                        </td>
                        <td class="product-subtotal" data-subtotal="<?php echo esc_attr($product_price * $cart_item['quantity']); ?>">
                            <?php echo wc_price($product_price * $cart_item['quantity']); ?>
                        </td>
                    </tr>
                    <?php
                }
            }
            do_action( 'woocommerce_cart_contents' );
            ?>
            <?php do_action( 'woocommerce_after_cart_contents' ); ?>
        </tbody>
    </table>
    <div class="cart__block column">
        <div colspan="6" class="actions">
            <button type="button" id="ajax_update_cart" class="button btn btn_yellow">Update cart</button>
            <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="button checkout-button btn">Place an order</a>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Cart AJAX functionality loaded');
    
    const updateButton = document.getElementById('ajax_update_cart');
    const form = document.querySelector('.woocommerce-cart-form');
    
    if (!updateButton || !form) {
        console.log('Update button or form not found');
        return;
    }
    
    // Функция показа уведомления
    function showNotice(message, type) {
        // Удаляем старые уведомления
        const oldNotices = document.querySelectorAll('.cart-notice');
        oldNotices.forEach(notice => notice.remove());
        
        const notice = document.createElement('div');
        notice.className = `cart-notice cart-notice-${type}`;
        notice.textContent = message;
        
        document.body.appendChild(notice);
        
        setTimeout(() => {
            notice.remove();
        }, 3000);
    }
    
    // Функция форматирования цены
    function formatPrice(amount, currencySymbol) {
        // Используем форматирование WooCommerce вместо Intl.NumberFormat
        // Это предотвратит изменение валюты
        return currencySymbol + parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }
    
    // Функция обновления корзины через AJAX
    updateButton.addEventListener('click', function() {
        console.log('AJAX update clicked');
        
        // Показываем загрузку
        const originalText = updateButton.textContent;
        updateButton.textContent = 'Обновление...';
        updateButton.disabled = true;
        
        // Собираем количества товаров
        const quantities = {};
        let hasChanges = false;
        
        document.querySelectorAll('.cat__input').forEach(input => {
            const name = input.getAttribute('name');
            const newQuantity = parseInt(input.value);
            const oldQuantity = parseInt(input.getAttribute('value'));
            
            quantities[name] = newQuantity;
            
            if (newQuantity !== oldQuantity) {
                hasChanges = true;
            }
        });
        
        if (!hasChanges) {
            showNotice('Нет изменений для обновления', 'info');
            updateButton.textContent = originalText;
            updateButton.disabled = false;
            return;
        }
        
        // Отправляем AJAX запрос
        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'update_cart_quantities',
                quantities: JSON.stringify(quantities),
                nonce: '<?php echo wp_create_nonce("update_cart_nonce"); ?>'
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                console.log('Cart updated successfully:', data);
                
                // Обновляем счетчик корзины
                if (data.data.cart_count !== undefined) {
                    const cartCounts = document.querySelectorAll('.cart-count');
                    cartCounts.forEach(element => {
                        element.textContent = data.data.cart_count;
                    });
                }
                
                // Обновляем общую сумму
                if (data.data.cart_total !== undefined) {
                    const cartTotals = document.querySelectorAll('.cart-total');
                    cartTotals.forEach(element => {
                        element.innerHTML = data.data.cart_total;
                    });
                }
                
                // Обновляем subtotal для каждого товара
                document.querySelectorAll('.woocommerce-cart-form__cart-item').forEach(row => {
                    const cartItemKey = row.dataset.cartItemKey;
                    const priceElement = row.querySelector('.product-price');
                    const price = parseFloat(priceElement.dataset.price);
                    const currency = priceElement.dataset.currency;
                    const quantityInput = row.querySelector('.cat__input');
                    const quantity = parseInt(quantityInput.value);
                    const subtotalElement = row.querySelector('.product-subtotal');
                    
                    // Обновляем subtotal
                    const subtotal = price * quantity;
                    subtotalElement.textContent = formatPrice(subtotal, currency);
                    subtotalElement.dataset.subtotal = subtotal;
                    
                    // Обновляем значение по умолчанию
                    quantityInput.setAttribute('value', quantity);
                });
                
                showNotice('Корзина успешно обновлена!', 'success');
                
                // Обновляем фрагменты WooCommerce
                if (typeof jQuery !== 'undefined') {
                    jQuery(document.body).trigger('wc_fragment_refresh');
                }
                
            } else {
                showNotice('Ошибка при обновлении корзины: ' + data.data, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotice('Ошибка сети при обновлении корзины', 'error');
        })
        .finally(() => {
            // Восстанавливаем кнопку
            updateButton.textContent = originalText;
            updateButton.disabled = false;
        });
    });
    
    // Включаем кнопку при изменении количества
    document.querySelectorAll('.cat__input').forEach(input => {
        input.addEventListener('change', function() {
            updateButton.disabled = false;
            
            // Автоматически пересчитываем subtotal при изменении количества
            const row = this.closest('.woocommerce-cart-form__cart-item');
            if (row) {
                const priceElement = row.querySelector('.product-price');
                const price = parseFloat(priceElement.dataset.price);
                const currency = priceElement.dataset.currency;
                const quantity = parseInt(this.value);
                const subtotalElement = row.querySelector('.product-subtotal');
                
                const subtotal = price * quantity;
                subtotalElement.textContent = formatPrice(subtotal, currency);
            }
        });
        // В обработчике change/input события
input.addEventListener('change', function() {
    const quantity = parseInt(this.value);
    
    if (quantity === 0) {
        // Находим кнопку удаления и кликаем по ней
        const removeButton = this.closest('.woocommerce-cart-form__cart-item').querySelector('.remove');
        if (removeButton) {
            removeButton.click();
        }
    } else {
        // Обычное обновление
        updateButton.disabled = false;
        // ... остальная логика
    }
});
        input.addEventListener('input', function() {
            updateButton.disabled = false;
        });
    });
});
</script>
</main>
<?php do_action( 'woocommerce_after_cart' ); ?>
<?php get_footer(); ?>