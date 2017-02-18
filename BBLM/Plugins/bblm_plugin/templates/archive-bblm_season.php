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
		if ( $options = get_option('bblm_config') ) {

			$archive_season_text = htmlspecialchars( $options['archive_season_text'], ENT_QUOTES );

			//validates if something was not set
			if ( strlen( $archive_season_text ) !== 0 ) {

				 echo "<p>".nl2br( $archive_season_text )."</p>\n";

			}
		}

		$sea = new BBLM_CPT_Season;
?>
		<ul class="season_status">
		<?php while (have_posts()) : the_post(); ?>

			<li id="post-<?php the_ID(); ?>" class="entry-title <?php echo $sea->season_status(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="View <?php the_title(); ?>"><?php the_title(); ?></a></li>

		<?php endwhile;?>
		</ul>
	</div>
</div>
<p class="postmeta">&nbsp;</p>
	<?php endif; ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
