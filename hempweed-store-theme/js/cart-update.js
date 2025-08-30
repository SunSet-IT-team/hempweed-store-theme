jQuery(function($) {
  function updateCartCount() {
      $.ajax({
          url: cart_data.ajax_url,
          type: 'POST',
          data: { action: 'update_cart_count' },
          success: function(response) {
              $('.cart-count').text(response);
          }
      });
  }

  $(document).on('added_to_cart removed_from_cart', updateCartCount);
});
