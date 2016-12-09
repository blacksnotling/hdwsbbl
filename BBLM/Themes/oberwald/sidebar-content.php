<?php /* Dynamic content that goes at the bottom of the #maincontent section above the Footer */ ?>
<?php if ( dynamic_sidebar('maincontent-bottom') ) : ?>
	<div id="content-bottom">
		<?php dynamic_sidebar('maincontent-bottom'); ?>
	</div><!-- end of #content-bottom -->
	<?php /* I need to remove this eentually when the widget is created */
			if (function_exists('bblm_display_dyk')) {
					bblm_display_dyk();
			}
	 ?>
<?php endif; ?>
