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
		<h2><?php echo __( 'Championship Cups', 'bblm'); ?></h2>
<?php
		if ( $options = get_option('bblm_config') ) {

			$archive_cup_text = htmlspecialchars( $options['archive_cup_text'], ENT_QUOTES );

			//validates if something was not set
			if ( strlen( $archive_cup_text ) !== 0 ) {

				 echo "<p>".nl2br( $archive_cup_text )."</p>\n";

			}
		}

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
<?php endif; ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
