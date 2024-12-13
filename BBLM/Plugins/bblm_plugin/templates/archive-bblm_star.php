<?php
/**
 * BBowlLeagueMan Teamplate View Star PlayerTeam
 *
 * Page Template to view the Star Player Team. This replace the view team for this page.
 *
 * @author 		Blacksnotling
 * @category 	Template
 * @package 	BBowlLeagueMan/Templates
 */
 /*
  * Template Name: View Star Player Team
  */
?>
<?php get_header(); ?>
<div id="primary" class="content-area content-area-right-sidebar">
  <main id="main" class="site-main" role="main">
  <?php do_action( 'bblm_template_before_posts' ); ?>
	<?php if (have_posts()) : ?>
		<?php do_action( 'bblm_template_before_loop' ); ?>
			<?php do_action( 'bblm_template_before_content' ); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

		<header class="page-header entry-header">

      <h2 class="entry-title"><?php echo __( 'Star Players', 'bblm'); ?></h2>
      <div class="archive-description"><?php echo bblm_echo_archive_desc( 'stars' ) ?></div>

		</header><!-- .page-header -->

			<div class="entry-content">

    <?php BBLM_Stat::display_stats_breakdown_star(); ?>

    <?php BBLM_CPT_Star::get_star_listing(); ?>

</div><!-- .entry-content -->

<footer class="entry-footer">
	<p class="postmeta">&nbsp;</p>
</footer><!-- .entry-footer -->

</article><!-- .post-ID -->

<?php do_action( 'bblm_template_after_content' ); ?>
<?php do_action( 'bblm_template_after_loop' ); ?>
<?php endif; ?>
<?php do_action( 'bblm_template_after_posts' ); ?>
</main><!-- #main -->
</div><!-- #primary -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>
