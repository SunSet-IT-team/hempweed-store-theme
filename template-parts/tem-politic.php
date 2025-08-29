<?php
/**
 * Template Name: Privacy Policy
**/

get_header();
?>

<main id="primary" class="site-main main">

	<?php
	// Проверяем, есть ли запись
	if ( have_posts() ) :
			while ( have_posts() ) : the_post(); ?>
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
								<li><?php echo the_title(); ?></li>
							</ul>
							<h1 class="title size-50"><?php echo the_title(); ?></h1>
						</div>
					</div>
					<img src="<?php echo get_template_directory_uri(); ?>/img/home/cannabis-marijuana-leaf-closeup.png" alt="" class="banner__img img">
				</section>
			
				<section class="relative">
				    <div class="news-list__content container">
    					<?php the_content(); ?>
    				</div>
            <img src="<?php echo get_template_directory_uri(); ?>/img/background/background-list/bg11.png" class="back__img bk11 back__left">
            <img src="<?php echo get_template_directory_uri(); ?>/img/background/background-list/bg12.png" class="back__img bk12 back__right">
				</section>

			
			
			<?php endwhile;
	endif;
	?>

	</main><!-- #main -->

<?php
get_footer();
