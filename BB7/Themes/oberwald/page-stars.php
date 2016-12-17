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
		<div id="breadcrumb">
			<p><a href="<?php echo get_option('home'); ?>" title="Back to the front of the BBBBL">BBBBL</a> &raquo; <a href="<?php echo get_option('home'); ?>/races/" title="Back to the Race listing">Races</a> &raquo; <?php the_title(); ?></p>
		</div>
			<div>
				<h2><?php the_title(); ?></h2>
				<div class="details">
					<?php the_content('Read the rest of this entry &raquo;'); ?>
				</div>

<?php
		//Did You Know Display Code
		if (function_exists(bblm_display_dyk)) {
			bblm_display_dyk();
		}
?>

				<p class="postmeta"><?php edit_post_link('Edit', ' <strong>[</strong> ', ' <strong>]</strong> '); ?></p>

			</div>


		<?php endwhile;?>
	<?php endif; ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>