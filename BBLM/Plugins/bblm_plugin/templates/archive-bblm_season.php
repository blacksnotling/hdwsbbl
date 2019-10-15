<?php get_header(); ?>
  <?php do_action( 'bblm_template_before_posts' ); ?>
	<?php if (have_posts()) : ?>
    <?php do_action( 'bblm_template_before_loop' ); ?>

		<header class="page-header entry-header">

      <h2 class="entry-title"><?php echo __( 'Seasons', 'bblm'); ?></h2>
      <div class="archive-description"><?php echo bblm_echo_archive_desc( 'season' ) ?></div>

    </header><!-- .page-header -->

		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<ul>

				<?php while (have_posts()) : the_post(); ?>
					<?php do_action( 'bblm_template_before_content' ); ?>
<?php
						if ( BBLM_CPT_Season::is_season_active( get_the_ID() ) ) {
							echo '<li class="active bblm_season_active">';
						}
						else {
							echo '<li>';
						}
?>
					  <a href="<?php the_permalink(); ?>" title="<?php echo __( 'Read more about', 'bblm'); ?> <?php the_title(); ?>"><?php the_title(); ?></a></li>

					<?php do_action( 'bblm_template_after_content' ); ?>
				<?php endwhile; ?>

			</ul>

			<footer class="entry-footer">
				<p class="postmeta">&nbsp;</p>
			</footer><!-- .entry-footer -->

		</article><!-- .post-ID -->

	<?php do_action( 'bblm_template_after_loop' ); ?>
	<?php endif; ?>

<?php do_action( 'bblm_template_after_posts' ); ?>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
