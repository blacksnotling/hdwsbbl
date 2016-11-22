<?php
/*
Template Name: Race Listing
*/
/*
*	Filename: bb.core.races.php
*	Description: Page template to list the races in the league
*/
?>
<?php get_header(); ?>
	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
		<div id="breadcrumb">
			<p><a href="<?php echo get_option('home'); ?>" title="Back to the front of the HDWSBBL">HDWSBBL</a> &raquo; Races</p>
		</div>

			<div class="entry">
				<h2><?php the_title(); ?></h2>
				<?php the_content('Read the rest of this entry &raquo;'); ?>
<?php
				$racesql = "SELECT P.post_title, P.guid FROM ".$wpdb->prefix."race R, ".$wpdb->posts." P, ".$wpdb->prefix."bb2wp J WHERE R.r_id = J.tid AND P.ID = J.pid and J.prefix = 'r_' and R.r_show = 1 ORDER BY P.post_title ASC";
				if ($races = $wpdb->get_results($racesql)) {
					print("<ul>\n");
					foreach ($races as $race) {
						print("	<li><a href=\"".$race->guid."\" title=\"View more informaton about ".$race->post_title."\">".$race->post_title."</a></li>\n");
					}
					print("</ul>\n");
				}
				else {
					print("	<div class=\"info\">\n		<p>There are no races currently set-up</p>\n	</div>\n");
}

		//Did You Know Display Code
		if (function_exists(bblm_display_dyk)) {
			bblm_display_dyk();
		}
?>
				<p class="postmeta"><?php edit_post_link('Edit', ' <strong>[</strong> ', ' <strong>]</strong> '); ?></p>

			</div>


		<?php endwhile;?>
	<?php endif; ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>



