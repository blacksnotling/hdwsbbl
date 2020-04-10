<?php get_header(); ?>
<div id="primary" class="content-area content-area-right-sidebar">
  <main id="main" class="site-main" role="main">
  <?php do_action( 'bblm_template_before_posts' ); ?>
	<?php if (have_posts()) : ?>
			<?php do_action( 'bblm_template_before_content' ); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

		<header class="page-header entry-header">

			<h2 class="entry-title"><?php echo __( 'Blood Bowl Races', 'bblm'); ?></h2>
			<div class="archive-description"><?php echo bblm_echo_archive_desc( 'race' ) ?></div>

		</header><!-- .page-header -->

    <div class="entry-content">
      <h3 class="bblm-table-caption"><?php echo __( 'Available races', 'bblm' ); ?></h3>
<?php    BBLM_CPT_Race::get_race_listing();    ?>

    </div><!-- .entry-content -->

    <footer class="entry-footer">
      <p class="postmeta"></p>
    </footer><!-- .entry-footer -->

  </article><!-- .post-ID -->

<?php do_action( 'bblm_template_after_content' ); ?>
<?php endif; ?>
<?php do_action( 'bblm_template_after_posts' ); ?>
</main><!-- #main -->
</div><!-- #primary -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>
