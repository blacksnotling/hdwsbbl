<?php
/**
 * The template for displaying 'Did You Knows' - Archive View
 *
 * @package		BBowlLeagueMan/Templates
 * @category	Template
 * @author 		Blacksnotliung
 */

get_header(); ?>

<?php do_action( 'bblm_template_before_posts' ); ?>

<?php if (have_posts()) : ?>

	<?php do_action( 'bblm_template_before_loop' ); ?>


		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<header class="entry-header">
				<h2 class="entry-title"><?php echo __( 'Did You Know?', 'bblm'); ?></h2>
			</header><!-- .entry-header -->

			<div class="entry-content">

	<?php while (have_posts()) : the_post(); ?>

		<?php do_action( 'bblm_template_before_content' ); ?>

<?php
				$type = get_post_meta( get_the_ID(), 'dyk_type', true );
?>

				<div class="dykcontainer dyk<?php echo strtolower( $type ); ?>" id="dyk<?php echo the_ID(); ?>">
					<h3 class="dykheader"><?php echo bblm_get_league_name(); ?> - <?php if( "Trivia" == $type ) { print("Did You Know"); } else { print("Fact"); } ?></h3>
<?php

				if ( ( strlen( get_the_title() ) !== 0 ) && ( "none" !== strtolower( get_the_title() ) ) ) {
?>
					<h4><?php the_title(); ?></h4>
<?php
				}
?>
					<?php the_content(); ?>
					<p><?php edit_post_link( __( 'Edit', 'bblm' ), ' <strong>[</strong> ', ' <strong>]</strong> '); ?></p>
				</div><!-- .dykcontainer .dyk -->

	<?php do_action( 'bblm_template_after_content' ); ?>


	<?php endwhile;?>

			</div><!-- .entry-content -->

	<?php do_action( 'bblm_template_after_loop' ); ?>

			<footer class="entry-footer">
				<p class="postmeta">&nbsp;</p>
			</footer><!-- .entry-footer -->

		</article><!-- .post-ID -->

<?php endif; ?>

<?php do_action( 'bblm_template_after_posts' ); ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
