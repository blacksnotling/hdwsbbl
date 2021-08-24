<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @package Crownstar
 */

get_header(); ?>

	<div id="primary" class="content-area content-area-<?php echo crownstar_get_sidebar_setting(); ?>-sidebar">
		<main id="main" class="site-main" role="main">

			<section class="error-404 not-found">
				<header class="page-header">
					<h1 class="page-title"><?php _e( 'Illegal Procedure! That page can&rsquo;t be found.', 'crownstar' ); ?></h1>
				</header><!-- .page-header -->

				<div class="page-content">
					<p><?php _e( 'It looks like nothing was found at this location. Try searching for something else?', 'crownstar' ); ?></p>

					<?php get_search_form(); ?>
				</div><!-- .page-content -->
			</section><!-- .error-404 -->

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
