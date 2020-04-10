<?php
/**
 * The template for displaying search results pages.
 *
 * @package Crownstar
 */

get_header(); ?>

	<section id="primary" class="content-area content-area-<?php echo crownstar_get_sidebar_setting(); ?>-sidebar">
		<main id="main" class="site-main" role="main">

		<?php if ( have_posts() ) : ?>

			<header class="page-header entry-header">
				<h1 class="page-title entry-title"><?php printf( __( 'Search Results for: %s', 'crownstar' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
			</header><!-- .page-header -->

			<?php /* Start the Loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>

				<?php
				get_template_part( 'content', 'search' );
				?>

			<?php endwhile; ?>

			<?php crownstar_paging_nav(); ?>

		<?php else : ?>

			<?php get_template_part( 'content', 'none' ); ?>

		<?php endif; ?>

		</main><!-- #main -->
	</section><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
