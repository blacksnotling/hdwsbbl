<?php
/**
 * The template for displaying 'Seasons' - Archive View
 *
 * @package		BBowlLeagueMan/Templates
 * @category	Template
 * @author 		Blacksnotliung
 */

get_header(); ?>
	<?php if (have_posts()) : ?>
		<div class="entry">
			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<h2 class="entry-title"><?php echo __( 'Seasons', 'bblm'); ?></h2>
<?php
		bblm_echo_archive_desc( 'season' );

		$sea = new BBLM_CPT_Season;
?>
		<ul class="season_status">
		<?php while (have_posts()) : the_post(); ?>

			<li id="post-<?php the_ID(); ?>" class="entry-title <?php echo $sea->season_status(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="View <?php the_title(); ?>"><?php the_title(); ?></a></li>

		<?php endwhile;?>
		</ul>
		<p class="postmeta">&nbsp;</p>
	</div>
</div>

	<?php endif; ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
