<?php
/**
 * The template for displaying 'Stadiums' - Single View
 *
 * @package		BBowlLeagueMan/Templates
 * @category	Template
 * @author 		Blacksnotliung
 */

get_header(); ?>
	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
			<div class="entry">
				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<h2 class="entry-title"><?php the_title(); ?></h2>
<?php
				$stadium = new BBLM_CPT_Stadium;

				echo "<h3>".__( 'Home Teams', 'bblm' )."</h3>\n";
				$stadium->home_teams();
?>
				<div class="details staddet">
					<?php the_content(); ?>
				</div>
<?php
				echo "<h3>".__( 'Matches that have taken place in this stadium', 'bblm' )."</h3>\n";
				$stadium->echo_recent_matches();

?>
				<p class="postmeta"><?php edit_post_link( __( 'Edit', 'bblm' ), ' <strong>[</strong> ', ' <strong>]</strong> '); ?></p>

			</div>
			</div>


		<?php endwhile; ?>
	<?php endif; ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
