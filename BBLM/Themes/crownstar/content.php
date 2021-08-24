<?php
/**
 * @package Crownstar
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php if ( get_the_post_thumbnail() ) { ?>
		<a class="entry-thumbnail" href="<?php echo esc_url( get_permalink() ); ?>">
			<?php the_post_thumbnail( 'thumbnail' ); ?>
		</a>
	<?php } ?>

	<div class="single-entry">
		<header class="entry-header">
			<?php the_title( sprintf( '<h1 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h1>' ); ?>
			<?php if ( 'post' == get_post_type() ) : ?>
				<div class="entry-details">
					<?php do_action( 'crownstar_before_entry_details' ); ?>
					<?php crownstar_entry_date(); ?>
					<?php do_action( 'crownstar_entry_details' ); ?>
				</div>
			<?php endif; ?>
		</header><!-- .entry-header -->

		<div class="entry-content entry-content">
			<?php
				/* translators: %s: Name of current post */
				the_content( sprintf(
					__( 'Continue reading %s <span class="meta-nav">&rarr;</span>', 'crownstar' ),
					the_title( '<span class="screen-reader-text">"', '"</span>', false )
				) );
			?>

			<?php
				wp_link_pages( array(
					'before' => '<div class="page-links">' . __( 'Pages:', 'crownstar' ),
					'after'  => '</div>',
				) );
			?>
		</div><!-- .entry-content -->

		<?php crownstar_entry_footer(); ?>
	</div>
</article><!-- #post-## -->
