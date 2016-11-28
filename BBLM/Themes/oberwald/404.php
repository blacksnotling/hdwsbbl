<?php get_header(); ?>
		<div id="breadcrumb">
			<p><a href="<?php echo home_url(); ?>" title="Back to the front of the HDWSBBL">HDWSBBL</a> &raquo; Page not found!</p>
		</div>
<div class="entry">
	<h2>Illegal Procedure!!</h2>
	<p>It looks like the page you are looking for has moved or the link you where given was incorrect. Please feel free to use the search box below to find what you are looking for:</p>
	<p><?php get_search_form(); ?></p>

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
