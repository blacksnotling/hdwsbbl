<?php get_header(); ?>
		<div id="breadcrumb">
			<p><a href="<?php echo get_option('home'); ?>" title="Back to the front of the BBBBL">BBBBL</a> &raquo; Page not found!</p>
		</div>
<div class="entry">
	<h2>Illegal Procedure!! (or Amatuer Mistake)</h2>
	<p>Amatuer by name, amatuer by nature - It looks like the page you are looking for has moved or the link you where given was incorrect. Please feel free to use the search box below to find what you are looking for:</p>
	<p><?php include (TEMPLATEPATH . '/searchform.php'); ?></p>

<?php
		//Did You Know Display Code
		if (function_exists(bblm_display_dyk)) {
			bblm_display_dyk();
		}
?>

	<p class="postmeta"></p>
</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>