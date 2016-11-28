<?php
/*
Template Name: Network Listing
*/
/*
*	Filename: bbtn.view.network.php
*	Description: .The Template for the front page of the Team News Network
*/

?>
<?php get_header(); ?>
	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
		<div id="breadcrumb">
			<p><a href="<?php echo home_url(); ?>" title="Back to the front of the HDWSBBL">HDWSBBL</a> &raquo; <?php the_title(); ?></p>
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
