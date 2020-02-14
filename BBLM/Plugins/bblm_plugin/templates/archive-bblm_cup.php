<?php get_header(); ?>
<div id="primary" class="content-area content-area-left-sidebar">
  <main id="main" class="site-main" role="main">
  <?php do_action( 'bblm_template_before_posts' ); ?>
	<?php if (have_posts()) : ?>
    <?php do_action( 'bblm_template_before_loop' ); ?>

		<header class="page-header entry-header">

			<h2 class="entry-title"><?php echo __( 'Championship Cups', 'bblm'); ?></h2>
			<div class="archive-description"><?php echo bblm_echo_archive_desc( 'cup' ) ?></div>

		</header><!-- .page-header -->

		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

			<?php do_action( 'bblm_template_before_content' ); ?>

			<?php bblm_template_display_cup_listing(); ?>

			<?php do_action( 'bblm_template_after_content' ); ?>

			<footer class="entry-footer">
				<p class="postmeta">&nbsp;</p>
			</footer><!-- .entry-footer -->

		</article><!-- .post-ID -->

		<?php do_action( 'bblm_template_after_loop' ); ?>
	<?php endif; ?>

	<?php do_action( 'bblm_template_after_posts' ); ?>
</main><!-- #main -->
</div><!-- #primary -->
	<?php get_sidebar(); ?>
	<?php get_footer(); ?>
