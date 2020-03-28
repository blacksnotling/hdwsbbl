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
<div id="primary" class="content-area content-area-right-sidebar">
  <main id="main" class="site-main" role="main">
  <?php do_action( 'bblm_template_before_posts' ); ?>
	<?php if (have_posts()) : ?>
		<?php do_action( 'bblm_template_before_loop' ); ?>
		<?php while (have_posts()) : the_post(); ?>
			<?php do_action( 'bblm_template_before_content' ); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

				<header class="page-header entry-header">

					<h2 class="entry-title"><?php the_title(); ?></h2>

				</header><!-- .page-header -->

					<div class="entry-content">

					<?php the_content(); ?>
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
					print("	<div class=\"bblm_info\">\n		<p>There are no races currently set-up</p>\n	</div>\n");
}

?>
</div><!-- .entry-content -->

<footer class="entry-footer">
	<p class="postmeta"><?php bblm_display_page_edit_link(); ?></p>
</footer><!-- .entry-footer -->

</article><!-- .post-ID -->

<?php do_action( 'bblm_template_after_content' ); ?>
<?php endwhile; ?>
<?php do_action( 'bblm_template_after_loop' ); ?>
<?php endif; ?>
<?php do_action( 'bblm_template_after_posts' ); ?>
</main><!-- #main -->
</div><!-- #primary -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>
