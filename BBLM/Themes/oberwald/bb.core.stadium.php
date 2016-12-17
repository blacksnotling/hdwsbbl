<?php
/*
Template Name: Stadium Listing
*/
/*
*	Filename: bb.core.stadium.php
*	Description: Page template to list the Stadiums in the league
*/
?>
<?php get_header(); ?>
	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
			<div class="entry">
				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<h2 class="entry-title"><?php the_title(); ?></h2>

					<?php the_content(); ?>
<?php
				$stadiumsql = "SELECT P.post_title, P.guid FROM ".$wpdb->prefix."stadium R, ".$wpdb->posts." P, ".$wpdb->prefix."bb2wp J WHERE R.stad_id = J.tid AND P.ID = J.pid and J.prefix = 'stad_' ORDER BY P.post_title ASC";
				if ($stadiums = $wpdb->get_results($stadiumsql)) {
					print("<ul>\n");
					foreach ($stadiums as $stad) {
						print("	<li><a href=\"".$stad->guid."\" title=\"View more informaton about ".$stad->post_title."\">".$stad->post_title."</a></li>\n");
					}
					print("</ul>\n");
				}
				else {
					print("	<div id=\"info\">\n	<p>There are no Stadiums currently set-up!</p>\n	</div>\n");
}


?>
				<p class="postmeta"><?php edit_post_link( __( 'Edit', 'oberwald' ), ' <strong>[</strong> ', ' <strong>]</strong> '); ?></p>

			</div>
		</div>


		<?php endwhile;?>
	<?php endif; ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
