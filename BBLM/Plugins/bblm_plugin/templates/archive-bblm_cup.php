<?php
/**
 * The template for displaying Championship Cups - Archive View
 *
 * @package		BBowlLeagueMan/Templates
 * @category	Template
 * @author 		Blacksnotliung
 */

get_header(); ?>
<?php get_header(); ?>
	<?php if (have_posts()) : ?>
		<div class="entry">
			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<h2 class="entry-title"><?php echo __( 'Championship Cups', 'bblm'); ?></h2>
<?php
		bblm_echo_archive_desc( 'cup' );

		$cup = new BBLM_CPT_Cup;
?>
<table>
	<tr>
		<th>Championship Cup</th>
		<th>Competitions</th>
	</tr>
<?php while (have_posts()) : the_post(); ?>

	<tr>
		<td id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="Learn more about <?php the_title(); ?>"><?php the_title(); ?></a></td>
		<td><?php $cup->echo_number_competitions( get_the_id() ); ?></td>
	</tr>

<?php endwhile;?>
</table>
<p class="postmeta">&nbsp;</p>
</div>
</div>
<?php endif; ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
