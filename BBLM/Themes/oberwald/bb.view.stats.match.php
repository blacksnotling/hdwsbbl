<?php
/*
Template Name: Statistics - Match Records
*/
/*
*	Filename: bb.view.stats.match.php
*	Version: 0.0b
*	Description: .Match Records
*/
/* -- Change History --
20090828 - 0.0 - Initial creation of file.

*/
?>
<?php get_header(); ?>
	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
		<div id="breadcrumb">
			<p><a href="<?php echo get_option('home'); ?>" title="Back to the front of the HDWSBBL">HDWSBBL</a> &raquo; <a href="<?php echo get_option('home'); ?>/stats" title="Back to the main Statistics page">Statistics</a> &raquo; <?php the_title(); ?></p>
		</div>
			<div class="entry">
				<h2><?php the_title(); ?></h2>

				<?php the_content('Read the rest of this entry &raquo;'); ?>

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