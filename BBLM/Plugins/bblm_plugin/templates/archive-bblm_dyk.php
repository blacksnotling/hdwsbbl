<?php
/**
 * The template for displaying 'Did You Knows' - Archive View
 *
 * @package		BBowlLeagueMan/Templates
 * @category	Template
 * @author 		Blacksnotliung
 */

get_header(); ?>

<?php if (have_posts()) : ?>

	<div class="entry">
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<h2 class="entry-title">Did You Know?</h2>

	<?php while (have_posts()) : the_post(); ?>

<?php
				$type = get_post_meta( get_the_ID(), 'dyk_type', true );
?>

				<div class="dykcontainer dyk<?php echo strtolower( $type ); ?>" id="dyk<?php echo the_ID(); ?>">
					<h3 class="dykheader"><?php echo bblm_get_league_name(); ?> - <?php if( "Trivia" == $type ) { print("Did You Know"); } else { print("Fact"); } ?></h3>
<?php

				if ( strlen( get_the_title() ) !== 0 ) {
?>
					<h4><?php the_title(); ?></h4>
<?php
				}
?>
					<?php the_content(); ?>
					<p><?php edit_post_link( __( 'Edit', 'bblm' ), ' <strong>[</strong> ', ' <strong>]</strong> '); ?></p>
				</div>


	<?php endwhile;?>

		</div>
	</div>

<?php endif; ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
