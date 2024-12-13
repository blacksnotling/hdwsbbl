<?php get_header(); ?>
<div id="primary" class="content-area content-area-right-sidebar">
  <main id="main" class="site-main" role="main">
  <?php do_action( 'bblm_template_before_posts' ); ?>
	<?php if (have_posts()) : ?>
    <?php do_action( 'bblm_template_before_loop' ); ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

		<header class="page-header entry-header">

      <h2 class="entry-title"><?php echo __( 'Seasons', 'bblm'); ?></h2>
      <div class="archive-description"><?php echo bblm_echo_archive_desc( 'season' ) ?></div>

    </header><!-- .page-header -->

		<div class="entry-content">
			<ul>

				<?php while (have_posts()) : the_post(); ?>
					<?php do_action( 'bblm_template_before_content' ); ?>
<?php
						if ( BBLM_CPT_Season::is_season_active( get_the_ID() ) ) {
							echo '<li class="bblm_season_active">';
						}
						else {
							echo '<li>';
						}
?>
					  <a href="<?php the_permalink(); ?>" title="<?php echo __( 'Read more about', 'bblm'); ?> <?php the_title(); ?>"><?php the_title(); ?></a></li>

					<?php do_action( 'bblm_template_after_content' ); ?>
				<?php endwhile; ?>

			</ul>

    </div><!-- .entry-content -->

    <footer class="entry-footer">
    	<p class="postmeta">&nbsp;</p>
    </footer><!-- .entry-footer -->

    </article><!-- .post-ID -->

    <?php do_action( 'bblm_template_after_content' ); ?>
    <?php endif; ?>
    <?php do_action( 'bblm_template_after_posts' ); ?>
    </main><!-- #main -->
    </div><!-- #primary -->
    <?php get_sidebar(); ?>
    <?php get_footer(); ?>
