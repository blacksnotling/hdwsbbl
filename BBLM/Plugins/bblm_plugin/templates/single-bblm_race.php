<?php
/**
 * BBowlLeagueMan Teamplate View Race
 *
 * Page Template to view Race's details
 *
 * @author 		Blacksnotling
 * @category 	Template
 * @package 	BBowlLeagueMan/Templates
 */
/*
 * Template Name: View Race
 */
?>
<?php get_header(); ?>
<div id="primary" class="content-area content-area-right-sidebar">
  <main id="main" class="site-main" role="main">
  <?php do_action( 'bblm_template_before_posts' ); ?>
	<?php if (have_posts()) : ?>
    <?php do_action( 'bblm_template_before_loop' ); ?>
		<?php while (have_posts()) : the_post(); ?>
      <?php do_action( 'bblm_template_before_content' ); ?>
      <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

        <header class="entry-header">
          <h2 class="entry-title"><?php the_title(); ?></h2>
        </header><!-- .entry-header -->

				<div class="entry-content">
          <div class="bblm-template-logo"><?php BBLM_CPT_Race::display_race_icon( get_the_ID(), 'icon' ); ?></div>

				<div class="bblm_details bblm_race_description">
					<?php the_content(); ?>
				</div>
<?php
        $race_id = get_the_ID();
        $rr_cost = BBLM_CPT_Race::get_reroll_cost( $race_id );

        echo '<ul>';
        echo '<li><strong>' . __( 'Re-Roll Cost:', 'bblm' ) . '</strong> ' . number_format( $rr_cost ) . ' GP</li>';
        //Display the race Special rules, if any are set
        echo '<li><strong>' . __( 'Special Rules:', 'bblm' ) . '</strong> ' . strip_tags( get_the_term_list( $post->ID, 'race_rules', '', ', ', '' ) . '</li>' );
        echo '</ul>';

        $race = new BBLM_CPT_Race;

        echo '<h3 class="bblm-table-caption">' . __( 'Positions available for this Race', 'bblm' ) . '</h3>';
        $race->display_race_positions( $race_id );

        echo '<h3 class="bblm-table-caption">' . __( 'Star Players available for this Race', 'bblm' ) . '</h3>';
        $race->display_stars_available( $race_id );

        echo '<h3 class="bblm-table-caption">' . __( 'Teams representing this Race', 'bblm' ) . '</h3>';
        $race->display_teams_representing( $race_id );


?>
</div><!-- .entry-content -->

<footer class="entry-footer">
	<p class="postmeta"><?php bblm_display_page_edit_link(); ?></p>
</footer><!-- .entry-footer -->

</article><!-- .post-ID -->

<?php do_action( 'bblm_template_after_content' ); ?>
<?php endwhile; ?>
<?php do_action( 'bblm_template_after_loop' ); ?>
<?php endif; ?>
<?php do_action( 'bblm_template_after_posts' ); ?>
</main><!-- #main -->
</div><!-- #primary -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>
