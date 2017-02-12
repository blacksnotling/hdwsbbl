<?php
/**
 * The template for displaying 'Stadiums' - Archive View
 *
 * @package		BBowlLeagueMan/Templates
 * @category	Template
 * @author 		Blacksnotliung
 */

get_header(); ?>
	<?php if (have_posts()) : ?>
		<h2><?php echo __( 'Stadiums', 'bblm'); ?></h2>
		<ul>
		<?php while (have_posts()) : the_post(); ?>

				<li id="post-<?php the_ID(); ?>" class="entry-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="View <?php the_title(); ?>"><?php the_title(); ?></a></li>


		<?php endwhile;?>
	</ul>
	<?php endif; ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
