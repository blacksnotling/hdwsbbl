<?php get_header(); ?>
  <?php do_action( 'bblm_template_before_posts' ); ?>
	<?php if (have_posts()) : ?>
    <?php do_action( 'bblm_template_before_loop' ); ?>
		<?php while (have_posts()) : the_post(); ?>
      <?php do_action( 'bblm_template_before_content' ); ?>
      <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

        <header class="entry-header">
          <h2 class="entry-title"><?php the_title(); ?></h2>
        </header><!-- .entry-header -->

				<?php $bblm_cup = new BBLM_CPT_Cup; ?>
				<?php $bblm_stats = new BBLM_Stat; ?>
				<?php $bblm_award = new BBLM_CPT_Award; ?>
				<?php $bblm_comp = new BBLM_CPT_Comp; ?>
				<?php $bblm_team = new BBLM_CPT_Team; ?>

				<div class="entry-content">

					<div class="details bblm_cup_description bblm_details">
						<?php the_content(); ?>
					</div>

<?php
				//Grab the series ID for use in the database
				$cupid = get_the_ID();

				//Determine if any mayches have taken place in this series.
				$matchnum = $bblm_cup->get_number_games();

				//From this point, we only go further if any matches have been played
				if ( 0 < $matchnum ) {

					$bblm_stats->display_stats_breakdown();

					echo '<h3>' . __( 'Winners of this Championship Cup', 'bblm' ) . '</h3>';
					$bblm_award->display_cup_winners();

					echo '<h3>' . __( 'Competitions for this Championship Cup', 'bblm' ) . '</h3>';
					$bblm_comp->display_comp_list_with_stats();

					echo '<h3>' . __( 'Team Statistics for this Championship Cup', 'bblm' ) . '</h3>';
					$bblm_team->display_team_list_with_stats();

					echo '<h3>' . __( 'Player Statistics for this Championship Cup', 'bblm' ) . '</h3>';
					$stat_limit = bblm_get_stat_limit();
					$bblm_stats->display_top_players_table( $cupid, 'bblm_cup', $stat_limit );
					$bblm_stats->display_top_killers_table( $cupid, 'bblm_cup', $stat_limit );

					echo '<h3>' . __( 'Awards', 'bblm' ) . '</h3>';
					$bblm_award->display_list_award_winners();

				}//end of if matches
				else {
					print("	<div class=\"info\">\n		<p>No matches have been played for this Championship Cup. Stay tuned for future updates.</p>\n	</div>\n");
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
			<?php get_sidebar(); ?>
			<?php get_footer(); ?>
