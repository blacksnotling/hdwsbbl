<?php get_header(); ?>
<div id="primary" class="content-area content-area-left-sidebar">
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

				<div class="bblm_details bblm_season_description">
					<?php the_content(); ?>
				</div>
<?php
				$season = get_the_ID();
				$seasonactive = BBLM_CPT_Season::is_season_active( get_the_ID() );

				$matchnumsql = 'SELECT COUNT(*) AS MATCHNUM FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE M.c_id = C.c_id AND C.c_counts = 1 AND C.type_id = 1 AND M.m_id = J.tid AND J.prefix = \'m_\' AND J.pid = P.ID AND C.sea_id = ' . $season;
				$matchnum = $wpdb->get_var( $matchnumsql );

				//From this point, we only go further if any matches have been played
				if ($matchnum > 0) {

					$bblm_team = new BBLM_CPT_Team;
					$bblm_stats = new BBLM_Stat;
					$bblm_award = new BBLM_CPT_Award;
					$bblm_comp = new BBLM_CPT_Comp;

					$bblm_stats->display_stats_breakdown();

					echo '<h3>' . __( 'Championship Cup Winners this season', 'bblm') . '</h3>';
					$bblm_award->display_cup_winners_in_a_season();

					echo '<h3>' . __( 'Competitions this season', 'bblm') . '</h3>';
					$bblm_comp->display_comp_list_with_stats();

					echo '<h3>' . __( 'Team Statistics for this season', 'bblm') . '</h3>';
					$bblm_team->display_team_list_with_stats();

					echo '<h3>' . __( 'Player Statistics for this Season', 'bblm' ) . '</h3>';
					$stat_limit = bblm_get_stat_limit();
					$bblm_stats->display_top_players_table( $season, 'bblm_season', $stat_limit );
					$bblm_stats->display_top_killers_table( $season, 'bblm_season', $stat_limit );

					//Awards
					if ( !$seasonactive ) {
						//the Season is over, display the awards!
?>
						<h3 id="awardsfull bblm_awardsfull"><?php echo __( 'Awards', 'bblm'); ?></h3>
						<?php $bblm_award->display_list_award_winners(); ?>

<?php
					}//end of awards (if ( !$seasonactive ) {))

				}//end of if matches
				else {
?>
					<div class="bblm_info">
						<p><?php echo __( 'No matches have been played in this Season yet. Stay tuned for further updates as the games start rolling in!' , 'bblm' ) ?></p>
					</div>
<?php
				}

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
