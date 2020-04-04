<?php
/**
 * @package Crownstar
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php if ( ! is_single() ) { ?><a href="<?php echo esc_url( get_permalink() ); ?>"><?php } ?>

	<?php if ( has_post_thumbnail() ) { ?>
		<div class="entry-thumbnail">
			<?php the_post_thumbnail( 'large' ); ?>
		</div>
	<?php } ?>

	<div class="single-entry">
		<header class="entry-header">
			<?php crownstar_entry_title(); ?>

			<div class="entry-details">
				<?php do_action( 'crownstar_before_entry_details' ); ?>
				<?php crownstar_entry_meta(); ?>
				<?php crownstar_entry_date(); ?>
				<?php do_action( 'crownstar_entry_details' ); ?>
			</div>
		</header><!-- .entry-header -->

		<?php if ( ! is_single() ) { ?></a><?php } ?>

		<div class="entry-content">
			<?php the_content(); ?>
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
