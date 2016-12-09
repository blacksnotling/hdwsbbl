<?php
/*
Template Name: Star Players race View
*/
/*
*	Filename: page-star-players.php
*	Description: .Page to display the Star Players of the league. This replace the view team for this page.
*/
?>
<?php get_header(); ?>
	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
			<div class="entry">
				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<h2 class="entry-title"><?php the_title(); ?></h2>
					<div class="details">
					<?php the_content(); ?>
					</div>

					<p class="postmeta"><?php edit_post_link( __( 'Edit', 'oberwald' ) ' <strong>[</strong> ', ' <strong>]</strong> '); ?></p>

				</div>
			</div>


		<?php endwhile;?>
	<?php endif; ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
