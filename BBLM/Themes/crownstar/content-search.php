<?php
/**
 * The template part for displaying results in search pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Crownstar
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="article-header">
		<?php if ( 'post' == get_post_type() ) : ?>
			<?php crownstar_entry_meta(); ?>
			<?php crownstar_entry_date(); ?>
		<?php endif; ?>

		<?php the_title( sprintf( '<h1 class="article-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h1>' ); ?>
	</header><!-- .article-header -->

	<div class="entry-summary">
		<?php the_excerpt(); ?>
	</div><!-- .entry-summary -->

	<?php crownstar_entry_footer(); ?>
</article><!-- #post-## -->
