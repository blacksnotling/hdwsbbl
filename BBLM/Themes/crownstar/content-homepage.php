<?php
/**
 * The template used for displaying homepage content.
 *
 * @package Crownstar
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php if ( has_post_thumbnail() ) { ?>
			<div class="entry-thumbnail">
				<?php the_post_thumbnail( 'large' ); ?>
			</div>
		<?php } ?>

		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
	</header><!-- .entry-header -->

	<div class="entry-content">

		<?php the_content(); ?>

		<div class="homepage-widgets">
			<?php dynamic_sidebar( 'homepage-1' ); ?>
		</div>

	</div><!-- .entry-content -->
</article><!-- #post-## -->
