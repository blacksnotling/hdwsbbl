<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package crownstar
 */
 ?>

 			<aside id="tertiary" class="widget-area widget-area-middle" role="complementary">
 				<?php dynamic_sidebar( 'sidebar-main' ); ?>
 			</aside><!-- #secondary -->

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}
?>

<aside id="secondary" class="widget-area  widget-area-right">
	<?php dynamic_sidebar( 'sidebar-1' ); ?>
</aside><!-- #secondary -->
