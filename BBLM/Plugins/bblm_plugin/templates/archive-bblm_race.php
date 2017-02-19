<?php
/**
 * The template for displaying 'Races' - Archive View
 *
 * @package		BBowlLeagueMan/Templates
 * @category	Template
 * @author 		Blacksnotliung
 */

get_header(); ?>
	<?php if (have_posts()) : ?>
		<div class="entry">
			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<h2 class="entry-title"><?php echo __( 'Races', 'bblm'); ?></h2>
<?php
		bblm_echo_archive_desc( 'race' );
?>
		<ul>
		<?php while (have_posts()) : the_post(); ?>

				<li id="post-<?php the_ID(); ?>" class="entry-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="View <?php the_title(); ?>"><?php the_title(); ?></a></li>


		<?php endwhile;?>
	</ul>
	<p class="postmeta">&nbsp;</p>
	</div>
</div>
	<?php endif; ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
