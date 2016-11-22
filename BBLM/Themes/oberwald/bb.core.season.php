<?php
/*
Template Name: List Seasons
*/
/*
*	Filename: bb.core.season.php
*	Version: 1.1
*	Description: Page template to list the Stadiums in the league
*/
/* -- Change History --
20080603 - 1.0b - Intital creation of file.
20080712 - 1.1b - brought up to scratch
20080730 - 1.0 - bump to Version 1 for public release.
20090712 - 1.0.1 - Added DYK code to page
20090821 - 1.0.2 - modified the code slightly so that the current season is active
20090829 - 1.0.3 - Changed the style hook as I did not like the previous one!
20100123 - 1.1 - Updated the prefix for the custom bb tables in the Database (tracker [225])

*/
?>
<?php get_header(); ?>
	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
		<div id="breadcrumb">
			<p><a href="<?php echo get_option('home'); ?>" title="Back to the front of the HDWSBBL">HDWSBBL</a> &raquo; Seasons</p>
		</div>
			<div class="entry">
				<h2><?php the_title(); ?></h2>
				<?php the_content('Read the rest of this entry &raquo;'); ?>
<?php
				$seasonsql = 'SELECT S.sea_active, P.guid, P.post_title FROM '.$wpdb->prefix.'season S, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE S.sea_id = J.tid AND J.prefix = \'sea_\' AND J.pid = P.ID ORDER BY sea_active DESC, sea_sdate DESC';
				if ($seasons = $wpdb->get_results($seasonsql)) {
					print("	<ul>\n");
					foreach ($seasons as $s) {
						print("<li");
						if ($s->sea_active) {
							print(" class=\"active\"");
						}
						print("><a href=\"".$s->guid."\" title=\"View more informaton about ".$s->post_title."\">".$s->post_title."</a></li>\n");
					}
					print("</ul>\n");
				}
				else {
					print("<p>There are no Seasons currently set-up!</p>\n");
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