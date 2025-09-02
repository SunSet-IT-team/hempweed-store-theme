<?php
// Меняем тексты basket → cart (но не классы/id)
add_filter( 'gettext', 'replace_basket_with_cart', 999, 3 );
add_filter( 'ngettext', 'replace_basket_with_cart', 999, 3 );

function replace_basket_with_cart( $translated, $text, $domain ) {
    if ( stripos( $translated, 'basket' ) !== false ) {
        $translated = str_ireplace( 'Basket', 'Cart', $translated );
        $translated = str_ireplace( 'basket', 'cart', $translated );
    }
    return $translated;
}

// JS-скрипт: замена basket → cart на фронте
add_action('wp_footer', function() {
    ?>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        function replaceText(node) {
            if (node.nodeType === Node.TEXT_NODE) {
                node.nodeValue = node.nodeValue.replace(/Basket/gi, function(match) {
                    return match === "Basket" ? "Cart" : "cart";
                });
            } else {
                node.childNodes.forEach(replaceText);
            }
        }
        replaceText(document.body);
    });
    </script>
    <?php
});
