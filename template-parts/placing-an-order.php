<?php
/**
 * Template Name: Оформление заказа
 */

if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly
}

get_header();

// Получаем объект WC_Checkout
$checkout = WC()->checkout();

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
        <li>Placing an order</li>
      </ul>
      <h1 class="title size-50">Placing an order</h1>
    </div>
  </div>
  <img src="<?php echo get_template_directory_uri(); ?>/img/home/cannabis-marijuana-leaf-closeup.png" alt="" class="banner__img img">
</section>
<section class="container comment-card">
  <?php 
  // Выводим уведомления (например, ошибки валидации)
  wc_print_notices();
  
  // Форма оформления заказа
  do_action('woocommerce_before_checkout_form', $checkout);
  ?>
  </section>
  <?php
  
  // Если пользователь не авторизован и разрешена оплата без регистрации
  if (!$checkout->is_registration_required() && $checkout->is_registration_enabled() && !is_user_logged_in()) {
    echo esc_html(apply_filters('woocommerce_checkout_must_be_logged_in_message', __('You must be logged in to checkout.', 'woocommerce')));
    return;
  } ?>

  <form name="checkout" method="post" class="checkout woocommerce-checkout container" action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data">
  
    <?php if ($checkout->get_checkout_fields()) : ?>
  
        <?php do_action('woocommerce_checkout_before_customer_details'); ?>
  
        <div class="col2-set" id="customer_details">
            <div class="col-1">
                <?php do_action('woocommerce_checkout_billing'); ?>
            </div>
  
            <div class="col-2">
                <?php do_action('woocommerce_checkout_shipping'); ?>
            </div>
        </div>
  
        <?php do_action('woocommerce_checkout_after_customer_details'); ?>
  
    <?php endif; ?>

    <?php
    // Получаем доступные методы доставки
    $packages = WC()->shipping->get_packages();
    $chosen_method = WC()->session->get('chosen_shipping_methods')[0];

    if ($packages) {
        foreach ($packages as $i => $package) {
            $available_methods = $package['rates'];
            if ($available_methods) {
                echo '<div class="shipping-methods">';
                echo '<h4>' . __('Select shipping method', 'woocommerce') . '</h4>';
                foreach ($available_methods as $method) {
                    echo '<div class="shipping-method">';
                    echo '<input type="radio" name="shipping_method[' . $i . ']" value="' . esc_attr($method->id) . '" id="shipping_method_' . esc_attr($method->id) . '" ' . checked($method->id, $chosen_method, false) . '>';
                    echo '<label for="shipping_method_' . esc_attr($method->id) . '">' . esc_html($method->label) . ' - ' . wc_price($method->cost) . '</label>';
                    echo '</div>';
                }
                echo '</div>';
            }
        }
    }
    ?>
  
    <?php do_action('woocommerce_checkout_before_order_review_heading'); ?>
  
    <h3 id="order_review_heading"><?php esc_html_e('Your order', 'woocommerce'); ?></h3>
  
    <?php do_action('woocommerce_checkout_before_order_review'); ?>
  
    <div id="order_review" class="cart woocommerce-checkout-review-order">
        <?php do_action('woocommerce_checkout_order_review'); ?>
    </div>
  
    <?php do_action('woocommerce_checkout_after_order_review'); ?>
  
  </form>
</main>

<?php do_action('woocommerce_after_checkout_form', $checkout); ?>

<script type="text/javascript">
jQuery(document).ready(function($) {
    // Функция валидации ввода - цифры, плюс и управляющие клавиши
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
        if (key === '+' && value !== '' && value !== '+') {
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
        
        if (phoneInput && !phoneInput.classList.contains('phone-mask-applied')) {
            phoneInput.classList.add('phone-mask-applied');
            
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

<?php get_footer(); ?>