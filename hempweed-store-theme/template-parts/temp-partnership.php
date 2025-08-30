<?php
/**
 * Template Name: партнёрам
**/

get_header();
?>
<main class="main">
  <section class="banner banner__top-partner banner__category">
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
          <li>Partnership</li>
        </ul>
        <div class="banner__container-items flex">
            <h1 class="title size-50">Partnership</h1>
            <a href="#contact" class="btn circle__btn">contact</a>
        </div>
      </div>
    </div>
    <img src="<?php echo get_template_directory_uri(); ?>/img/home/cannabis-marijuana-leaf-closeup.png" alt="" class="banner__img img">
    <img src="<?php echo get_template_directory_uri(); ?>/img/background/background-list/bk1.png" class="back__img bk1 back__left">
</section>
  <section class="container text size-24">
    <?php echo the_content(); ?>
  </section>
  <section class="relative">
      <div class="docum flex container">
          <img src="<?php echo get_template_directory_uri(); ?>/img/home/image 35.png" alt="" class="docum__img img">
          <div class="docum__block direction">
              <div class="text size-26 docum__item">
                  <?php the_field('partnership_docum_text'); ?>
              </div>
              <?php 
              $partnership_docum_file = get_field('partnership_docum_file'); 
              if ($partnership_docum_file): 
              ?>
                  <a href="<?php echo $partnership_docum_file['url']; ?>" class="btn btn_red" target="_blank">More</a>
              <?php endif; ?>
          </div>
      </div>
    <img src="<?php echo get_template_directory_uri(); ?>/img/background/background-list/bk4.png" class="back__img bk4 back__left">
    <img src="<?php echo get_template_directory_uri(); ?>/img/background/background-list/bk5.png" class="back__img bk5 back__right">
  </section>
  <section class="relative">
      <div class="feedback _p_rel padding-bot-100 container">
        <h2 class="feedback__heading title center size-50"><?php the_field('review_title', 'option'); ?></h2>
        
        <?php if (have_rows('review_block', 'option')) : ?>
            <div class="swiper swiper_feedback">
                <div class="swiper-wrapper">
                    <?php $count = 0; ?>
                    <?php while (have_rows('review_block', 'option')) : the_row(); ?>
                        
                        <?php if ($count % 2 == 0): // Открываем новый слайд каждые 2 блока ?>
                            <div class="swiper-slide column">
                        <?php endif; ?>
                        
                        <div class="feedback_block">
                            <div class="feedback__content">
                              <?php 
                              $review_img = get_sub_field('review_block_img');
                              if ($review_img): ?>
                                  <img src="<?php echo esc_url($review_img['url']); ?>" alt="" class="img feedback__img">
                              <?php endif; ?>
      
                              <h3 class="feedback__name"><?php the_sub_field('review_block_name'); ?></h3>
      
                              <?php if (have_rows('review_block_reiting')) : ?>
                                  <ul class="star">
                                      <?php while (have_rows('review_block_reiting')) : the_row(); ?>
                                          <?php 
                                          $star_img = get_sub_field('review_block_reiting_img');
                                          if ($star_img): ?>
                                              <li>
                                                  <img src="<?php echo esc_url($star_img['url']); ?>" alt="рейтинг" class="img">
                                              </li>
                                          <?php else: ?>
                                              <li>
                                                  <img src="<?php echo get_template_directory_uri(); ?>/img/home/Star5.png" alt="рейтинг" class="img">
                                              </li>
                                          <?php endif; ?>
                                      <?php endwhile; ?>
                                  </ul>
                              <?php endif; ?>
      
                              <div class="text">
                                  <p><?php the_sub_field('review_block_text'); ?></p>
                              </div>
                            </div>
                        </div> <!-- /.feedback_block -->
    
                        <?php $count++; ?>
    
                        <?php if ($count % 2 == 0 || get_row_index() == count(get_field('review_block', 'option'))): // Закрываем слайд после 2 элементов ?>
                            </div> <!-- /.swiper-slide -->
                        <?php endif; ?>
    
                    <?php endwhile; ?>
    
                    <!-- Закрываем последний нечетный слайд -->
                    <?php if ($count % 2 != 0): ?>
                        </div> <!-- /.swiper-slide -->
                    <?php endif; ?>
    
                </div> <!-- /.swiper-wrapper -->
    
                <!-- Навигация -->
                <div class="feedback__arrow_wrap _flex_end_center">            
                    <div class="feedback__swiper-button-prev swiper-button-prev arrow-prev arrow"></div>
                    <div class="feedback__swiper-button-next swiper-button-next arrow-next arrow"></div>
                </div>
            </div> <!-- /.swiper -->
        <?php endif; ?>
        <img src="<?php echo get_template_directory_uri(); ?>/img/background/background-list/bg15.png" class="back__img bk15 back__left">
  </section>
  <section class="faq">
      <div class="faq__container container">
          <h2 class="title center size-50"><?php the_field('faq_title', 'option'); ?></h2>
          <div class="faq__section _flex_col">
              <?php if (have_rows('faq_questions', 'option')) : ?>
                  <?php while (have_rows('faq_questions', 'option')) : the_row(); ?>
                      <div class="faq__block _accordion_block" accordion-state="">
                          <button class="faq__btns _accordion_btn _flex_center_sb">
                              <h3 class="faq__title"><?php the_sub_field('faq_question', 'option'); ?></h3>
                              <img src="<?php echo get_template_directory_uri(); ?>/img/arrow.png" alt="" class="img faq__arrow">
                          </button>
                          <div class="text faq__text _accordion_hidden_content">
                              <p><?php the_sub_field('faq_answer', 'option'); ?></p>
                          </div>
                      </div>
                  <?php endwhile; ?>
              <?php endif; ?>
          </div>
      </div>
  </section>


</main>
<?php get_footer(); ?>