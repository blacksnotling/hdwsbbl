<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package crownstar
 */

?>

	</div><!-- #content -->

	<footer id="colophon" class="site-footer">
		<div class="site-info">


				<?php
				echo esc_html__( 'Sponsored by SlySports', 'crownstar' );
				?>
				<span class="sep"> | </span>
				<?php
				echo esc_html__( 'Over 10 years of carnage on the pitch.', 'crownstar' );
				?>
				<span class="sep"> | </span>
				<a href="<?php echo esc_url( __( 'https://wordpress.org/', 'crownstar' ) ); ?>">
					<?php
					printf( esc_html__( 'Powered by %s', 'crownstar' ), 'WordPress' );
					?>
				</a>
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
