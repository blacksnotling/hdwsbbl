<?php
/**
 * The template for displaying 'Did You Knows' - Archive View
 *
 * @package		BBowlLeagueMan/Templates
 * @category	Template
 * @author 		Blacksnotliung
 */

get_header(); ?>
<div id="primary" class="content-area content-area-left-sidebar">
  <main id="main" class="site-main" role="main">
<?php do_action( 'bblm_template_before_posts' ); ?>

<?php if (have_posts()) : ?>

	<?php do_action( 'bblm_template_before_loop' ); ?>


		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<header class="entry-header">
				<h2 class="entry-title"><?php echo __( 'Did You Know?', 'bblm'); ?></h2>
			</header><!-- .entry-header -->

			<div class="entry-content">

	<?php while (have_posts()) : the_post(); ?>

		<?php do_action( 'bblm_template_before_content' ); ?>

		<?php bblm_template_display_single_dyk(); ?>

	<?php do_action( 'bblm_template_after_content' ); ?>


	<?php endwhile;?>

			</div><!-- .entry-content -->

	<?php do_action( 'bblm_template_after_loop' ); ?>

			<footer class="entry-footer">
				<p class="postmeta">&nbsp;</p>
			</footer><!-- .entry-footer -->

		</article><!-- .post-ID -->

<?php endif; ?>

<?php do_action( 'bblm_template_after_posts' ); ?>
</main><!-- #main -->
</div><!-- #primary -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>
