<?php
/*
Template Name: Guestbook / Page with comments
*/
?>
<?php get_header(); ?>
	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
			<div class="entry">
				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<h2 class="entry-title"><?php the_title(); ?></h2>

					<?php the_content(); ?>

					<p class="postmeta"><?php edit_post_link( __( 'Edit', 'oberwald' ), ' <strong>[</strong> ', ' <strong>]</strong> '); ?></p>

					<?php comments_template( '', true ); ?>

				</div>
			</div>

		<?php endwhile; ?>
	<?php endif; ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
