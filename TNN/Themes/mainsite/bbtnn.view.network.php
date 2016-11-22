<?php
/*
Template Name: Network Listing
*/
/*
*	Filename: bbtn.view.network.php
*	Version: 1.0
*	Description: .The Template for the front page of the Team News Network
*/
/* -- Change History --
20100815 - 0.1b - Initial creation and completion of file.(beta)
20100826 - 0.1 - rename of file and removal of beta tag. development is underway. Also updated the call to the network display function
20100913 - 1.0 - bump to V1.0

*/
?>
<?php get_header(); ?>
	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
		<div id="breadcrumb">
			<p><a href="<?php echo get_option('home'); ?>" title="Back to the front of the HDWSBBL">HDWSBBL</a> &raquo; <?php the_title(); ?></p>
		</div>
			<div class="entry">
				<h2><?php the_title(); ?></h2>

				<?php the_content('Read the rest of this entry &raquo;'); ?>
				<?php bbtnn_print_sites_list_detailed(); ?>
				<h3>Tags used accross the network</h3>
				<div class="details"><?php wp_tag_cloud(); ?></div>
				<h3>Categories used accross the network</h3>
				<div class="details"><?php wp_tag_cloud( array( 'taxonomy' => 'category' ) ); ?></div>

				<p class="postmeta"><?php edit_post_link('Edit', ' <strong>[</strong> ', ' <strong>]</strong> '); ?></p>

			</div>


		<?php endwhile; ?>
	<?php endif; ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>