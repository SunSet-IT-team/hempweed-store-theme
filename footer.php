<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package kipr
 */

?>

  <section class="form background__yellow" id="contact">
    <div class="form__container container">
      <?php echo do_shortcode ('[fluentform id="3"]'); ?>
    </div>
  </section>

  <footer class="footer">
    <div class="footer__container container flex">
      <div class="footer__block column">
        <a href="/" class="footer__logo logo">
          <img src="<?php the_field('logotipe', 'option'); ?>" alt="logotipe" class="logo__img img">
        </a>
        <a href="/privacy-policy/" class="footer__block-item">Privacy policy</a>
        <img src="<?php echo get_template_directory_uri(); ?>/img/carts.png" alt="" class="img">
      </div>

      <div class="footer__block footer__menu column">
        <h3 class="title">Shopping</h3>
				<?php
					$args = array(
							'taxonomy' => 'product_cat',
							'parent' => 0, // Вывести только родительские категории
							'hide_empty' => false, // Показывать пустые категории
					);
					$categories = get_terms($args);
					
					if (!empty($categories) && !is_wp_error($categories)) {
							foreach ($categories as $category) {
									echo '<a href="' . get_term_link($category) . '" class="">' . $category->name . '</a>';
							}
					}
				?>
      </div>

      <div class="footer__block footer__menu column">
        <h3 class="title">Information</h3>
        <a href="/news">News</a>
        <a href="/news/?category=video">Video</a>
        <a href="/about-us">About us</a>
        <a href="/partnership">Partnership</a>
		  <a href="/privacy-policy">Privacy Policy</a>
		  <a href="/general-terms-and-condition-of-use">General Terms and Condition of Use</a>
      </div>

      <div class="footer__block footer__contacts column">
        <h3 class="title">Information</h3>
        <a href="mailto:<?php the_field('email', 'option'); ?>"><?php the_field('email', 'option'); ?></a>
        <a href="tel:<?php the_field('phone', 'option'); ?>"><?php the_field('phone', 'option'); ?></a>
        <?php if( have_rows('social_media', 'option') ): ?>
          <ul class="social flex">
            <?php while( have_rows('social_media', 'option') ): the_row(); 
            // переменные
            $social_network_icon = get_sub_field('social_network_icon', 'option');
            $social_network_link = get_sub_field('social_network_link', 'option');
            ?>
              <li>
                <a href="<?php echo $social_network_link; ?>">
                  <img src="<?php echo $social_network_icon['url']; ?>" alt="<?php echo $social_network_icon['alt'] ?>" class="img">
                </a>
              </li>
            <?php endwhile; ?>
          </ul>
        <?php endif; ?>
      </div>

      <div class="footer__bottom flex">
        <img src="<?php echo get_template_directory_uri(); ?>/img/sistem/18.svg" alt="" class="img">
<span>© 
    <?php
    $start_year = 2021; // Год основания сайта
    $current_year = date('Y'); // Текущий год
    
    // Если текущий год больше года основания, показываем диапазон (2021-2024)
    if ($current_year > $start_year) {
        echo $start_year . '-' . $current_year;
    } else {
        // Если это тот же год, показываем только его (2021)
        echo $start_year;
    }
    ?>
</span>
      </div>

    </div>
  </footer>

  <aside class="modal _modal_background">
    <div class="adult__cntr _modal_cntr _flex_center" modal-adult-desc>
      <div class="adult__wrp _modal_wrp">
        <div class="adult__body _modal_body">
          <h2 class="_color_000 _fs_40 _heding"><?php the_field('popup_window', 'option'); ?></h2>
        </div>

        <div class="adult__footer _modal_footer">
          <button class="agree__btn _btn _red_btn _btn_hover">
            <strong class="_fs_24 _fw_500">I an over 18 years old</strong>
          </button>
        </div>
      </div>

    </div>

    <div class="_modal_cntr _flex_center"></div>
    <div class="_modal_cntr _flex_center"></div>

  </aside>


  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
  <script src="<?php echo get_template_directory_uri(); ?>/js/slider1.js"></script>
  <script src="<?php echo get_template_directory_uri(); ?>/js/main.js"></script>
  <script src="<?php echo get_template_directory_uri(); ?>/js/app.js" type="module"></script>
    </div>
  </div>
  <?php wp_footer(); ?>
</body>
</html>