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
<?php
		if ( $options = get_option('bblm_config') ) {

			$archive_stad_text = htmlspecialchars( $options['archive_stad_text'], ENT_QUOTES );

			//validates if something was not set
			if ( strlen( $archive_stad_text ) !== 0 ) {

				 echo "<p>".nl2br( $archive_stad_text )."</p>\n";

			}
		}
?>
		<ul>
		<?php while (have_posts()) : the_post(); ?>

				<li id="post-<?php the_ID(); ?>" class="entry-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="View <?php the_title(); ?>"><?php the_title(); ?></a></li>


		<?php endwhile;?>
	</ul>
	<p class="postmeta">&nbsp;</p>
	<?php endif; ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
