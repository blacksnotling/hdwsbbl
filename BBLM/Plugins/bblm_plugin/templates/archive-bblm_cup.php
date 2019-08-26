<?php get_header(); ?>
  <?php do_action( 'bblm_template_before_posts' ); ?>
	<?php if (have_posts()) : ?>
    <?php do_action( 'bblm_template_before_loop' ); ?>

		<header class="page-header entry-header">

			<h2 class="entry-title"><?php echo __( 'Championship Cups', 'bblm'); ?></h2>
			<div class="archive-description"><?php echo bblm_echo_archive_desc( 'cup' ) ?></div>

		</header><!-- .page-header -->

		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

			<?php do_action( 'bblm_template_before_content' ); ?>

			<?php $cup = new BBLM_CPT_Cup; ?>
			<?php echo $cup->get_cup_summary() ?>


			<?php do_action( 'bblm_template_after_content' ); ?>

			<footer class="entry-footer">
				<p class="postmeta">&nbsp;</p>
			</footer><!-- .entry-footer -->

		</article><!-- .post-ID -->

		<?php do_action( 'bblm_template_after_loop' ); ?>
	<?php endif; ?>

	<?php do_action( 'bblm_template_after_posts' ); ?>
	<?php get_sidebar(); ?>
	<?php get_footer(); ?>
