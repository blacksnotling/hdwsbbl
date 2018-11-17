<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package crownstar
 */

get_header();
?>

			<section class="error-404 not-found">
				<header class="page-header">
					<h2 class="page-title"><?php esc_html_e( 'Illegal Procedure!!', 'crownstar' ); ?></h2>
				</header><!-- .page-header -->

				<div class="page-content">
					<p><?php esc_html_e( 'It looks like Nuffle has cursed you and the page you are looking for has moved or the link you where given was incorrect. Please feel free to use the search box below to find what you are looking for:', 'crownstar' ); ?></p>

					<?php
					get_search_form();
					?>

				</div><!-- .page-content -->
			</section><!-- .error-404 -->

<?php
get_footer();
