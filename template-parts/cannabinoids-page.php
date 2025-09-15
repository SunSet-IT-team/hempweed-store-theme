<?php
/* Template Name: Cannabinoids Page */
get_header(); 
?>

<main class="main">
  <section class="banner cosmetic__banner">
        <div class="banner__container container flex">
            <div class="banner__container-item direction">
                <ul class="breadcumbs">
                    <li>
                        <a href="/">
                            <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8.24951 20.1665L8.25258 14.6665L13.7495 14.6665V20.1665" stroke="white" stroke-width="1.5"></path>
                                <path d="M17.8766 20.1666L20.1684 8.24992L11.0017 1.83325L1.8335 8.24992L4.12664 20.1666H17.8766Z" stroke="white" stroke-width="1.5" stroke-linejoin="round"></path>
                            </svg>
                        </a>
                    </li>
                    <li>/</li>
                    <li>Cannabiniods</li> 
                </ul>
                <div class="cannabiniods__banner_text _banner_text_cntr _flex_col">
                    <h1 class="title size-50 _heading"><?php the_field('cannabiniods__title'); ?></h1>
                </div>
                <div class="_heading_title _fw_400 _fs_30 _color_fff">
                    <p>
                      <?php the_field('cannabiniods__descr'); ?>
                    </p>
                </div>
            </div>
        </div>
        <img src="<?php echo get_template_directory_uri(); ?>/img/home/cannabis-marijuana-leaf-closeup.png" alt="" class="banner__img img">
        <img src="https://hempweed.store/wp-content/themes/kipr/img/background/background-list/bk1.png" class="back__img bk1 back__left">
    </section>
    <section id="categories" class="categories container padding-bot-100 padding-top-60 _p_rel">
    <ul class="categories__grid flex">
        <?php
        // Берём термы таксономии "cannabinoids"
        $cannabinoids_terms = get_terms([
            'taxonomy'   => 'cannabinoids',
            'hide_empty' => false,
        ]);

        if (!empty($cannabinoids_terms) && !is_wp_error($cannabinoids_terms)) {
            foreach ($cannabinoids_terms as $term) {
                // Картинка (если используешь ACF или term_meta)
                $thumbnail_id = get_term_meta($term->term_id, 'thumbnail_id', true);
                $image_url = wp_get_attachment_url($thumbnail_id);

                // Фолбэк если нет картинки
                if (!$image_url) {
                    $image_url = get_template_directory_uri() . '/img/home/image.png';
                }

                // Ссылка на сам term
                $term_link = get_term_link($term);
                ?>
                <a href="<?php echo esc_url($term_link); ?>" class="categories__item">
                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($term->name); ?>" class="img">
                    <span class="size-40 title"><?php echo esc_html($term->name); ?></span>
                </a>
                <?php
            }
        }
        ?>
    </ul>
</section>


<?php get_template_part('template-parts/temp-other-products-pagination'); ?>


</main>

<?php get_footer(); ?>
