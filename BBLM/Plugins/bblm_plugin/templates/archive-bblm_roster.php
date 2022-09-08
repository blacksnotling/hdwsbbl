<?php get_header(); ?>
<div id="primary" class="content-area content-area-right-sidebar">
  <main id="main" class="site-main" role="main">
  <?php do_action( 'bblm_template_before_posts' ); ?>
	<?php if (have_posts()) : ?>
			<?php do_action( 'bblm_template_before_content' ); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

		<header class="page-header entry-header">

			<h2 class="entry-title"><?php echo __( 'Team Rosters', 'bblm' ); ?></h2>
			<div class="archive-description"><?php echo __( 'The below list provides direct access to all of the rosters of the teams in the league.', 'bblm' ) ?></div>

		</header><!-- .page-header -->

    <div class="entry-content">

      <ul>
      <?php while ( have_posts() ) : the_post(); ?>
        <?php the_title(
          sprintf( '<li><a href="%s" rel="bookmark">', esc_attr( esc_url( get_permalink() ) ) ),'</a></li>');
        ?>

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
