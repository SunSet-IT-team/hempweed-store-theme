<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package kipr
 */

get_header();
?>

	<main id="primary" class="site-main">

		<?php if ( have_posts() ) : ?>

			<section class="banner banner__category">
				<div class="banner__container container flex">
					<div class="banner__container-item direction">
						<h1 class="title size-50">	<?php
						/* translators: %s: search query. */
						printf( esc_html__( 'Search Results for: %s', 'kipr' ), '<span>' . get_search_query() . '</span>' );
					?></h1>
				</div>
				<img src="<?php echo get_template_directory_uri(); ?>/img/home/cannabis-marijuana-leaf-closeup.png" alt="" class="banner__img img">
			</section>

			<div class="container search-posts">
				<?php
					/* Start the Loop */
					while ( have_posts() ) :
						the_post();
		
						/**
						 * Run the loop for the search to output the results.
						 * If you want to overload this in a child theme then include a file
						 * called content-search.php and that will be used instead.
						 */
						get_template_part( 'template-parts/content', 'search' );
		
					endwhile;
		
					the_posts_navigation(array(
						'prev_text' => __('<span class="nav-prev btn">Back</span>'), // Кастомный текст "Назад"
						'next_text' => __('<span class="nav-next btn">Forward</span>'), // Кастомный текст "Вперед"
				));
		
				else :
		
					get_template_part( 'template-parts/content', 'none' );
		
				endif;
			?>
			</div>

	</main><!-- #main -->

<?php
get_footer();
