<?php
/**
 * BBowlLeagueMan Teamplate View Match
 *
 * Page Template to view a Matches details
 *
 * @author 		Blacksnotling
 * @category 	Template
 * @package 	BBowlLeagueMan/Templates
 */
/*
 * Template Name: View Match
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


<?php
			//Match Information
			$matchsql = 'SELECT M.*, M.WPID AS MWPID, UNIX_TIMESTAMP(M.m_date) AS mdate FROM '.$wpdb->prefix.'match M WHERE M.WPID = '.$post->ID.' LIMIT 1';
			if ($m = $wpdb->get_row($matchsql)) {

				//TeamA Information
				$teamAsql = 'SELECT M.*, T.WPID AS TWPID, T.t_sname, T.r_id FROM '.$wpdb->prefix.'match_team M, '.$wpdb->prefix.'team T WHERE M.t_id = T.t_id AND M.m_id = '.$m->MWPID.' AND T.t_id = '.$m->m_teamA.' LIMIT 1';
				$tA = $wpdb->get_row($teamAsql);
				//Team B Information
				$teamBsql = 'SELECT M.*, T.WPID AS TWPID, T.t_sname, T.r_id FROM '.$wpdb->prefix.'match_team M, '.$wpdb->prefix.'team T WHERE M.t_id = T.t_id AND M.m_id = '.$m->MWPID.' AND T.t_id = '.$m->m_teamB.' LIMIT 1';
				$tB = $wpdb->get_row($teamBsql);

				$teamA = bblm_get_team_name( $tA->TWPID );
				$teamB = bblm_get_team_name( $tB->TWPID );

?>
			<header class="entry-header">
				<h2 class="entry-title"><?php echo bblm_get_team_link( $tA->TWPID ); ?> vs <?php echo bblm_get_team_link( $tB->TWPID ); ?></h2>
			</header><!-- .entry-header -->

			<div class="entry-content">

<?php
        if ( $m->m_legacy ) {
          bblm_display_legacy_notice( "Match" );
        }
?>

				<table class="bblm_table">
					<thead>
						<tr>
							<th class="bblm_tbl_name"><?php echo $teamA;?></th>
							<th class="bblm_tbl_name">VS</th>
							<th class="bblm_tbl_name"><?php echo $teamB;?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><strong><?php BBLM_CPT_Team::display_team_logo( $tA->TWPID, 'medium' ); ?></strong></td>
							<th>&nbsp;</th>
							<td><strong><?php BBLM_CPT_Team::display_team_logo( $tB->TWPID, 'medium' ); ?></strong></td>
						</tr>
						<tr>
							<td class="bblm_score"><strong><?php echo $tA->mt_td;?></strong></td>
							<th class="bblm_tottux"><?php echo __( 'Score', 'bblm' ); ?></th>
							<td class="bblm_score"><strong><?php echo $tB->mt_td;?></strong></td>
						</tr>
						<tr>
							<td><?php echo $tA->mt_cas;?></td>
							<th class="bblm_tottux"><?php echo __( 'Casualities', 'bblm' ); ?></th>
							<td><?php echo $tB->mt_cas;?></td>
						</tr>
						<tr>
							<td><?php echo $tA->mt_comp;?></td>
							<th class="bblm_tottux"><?php echo __( 'Completions', 'bblm' ); ?></th>
							<td><?php echo $tB->mt_comp;?></td>
						</tr>
						<tr>
							<td><?php echo $tA->mt_int;?></td>
							<th class="bblm_tottux"><?php echo __( 'Interceptions', 'bblm' ); ?></th>
							<td><?php echo $tB->mt_int;?></td>
						</tr>
						<tr>
							<td class="bblm_tv"><?php echo number_format( $tA->mt_tv );?>gp</td>
							<th class="bblm_tottux"><?php echo __( 'Team Value', 'bblm' ); ?></th>
							<td class="bblm_tv"><?php echo number_format( $tB->mt_tv );?>gp</td>
						</tr>
						<tr>
							<td><?php echo number_format( $tA->mt_winnings );?></td>
							<th class="bblm_tottux"><?php echo __( 'Fans', 'bblm' ); ?></th>
							<td><?php echo number_format( $tB->mt_winnings );?></td>
						</tr>
						<tr>
							<td><?php echo number_format( $tA->mt_att );?> gp</td>
							<th class="bblm_tottux"><?php echo __( 'Winnings', 'bblm' ); ?></th>
							<td><?php echo number_format( $tB->mt_att );?> gp</td>
						</tr>
						<tr>
							<td><?php echo $tA->mt_ff;?></td>
							<th class="bblm_tottux"><?php echo __( 'FF Change', 'bblm' ); ?></th>
							<td><?php echo $tB->mt_ff;?></td>
						</tr>
					</tbody>
				</table>

<?php
        if ( get_the_content() != "No Report Filed Yet" ) {
          //Only display the match report if one is entered
?>
				<h3><?php echo __( 'Match Report', 'bblm' ); ?></h3>
				<div class="bblm_details bblm_match_report">
					<?php the_content(); ?>
				</div>

<?php
        }

				//Display match Trivia if something is present
				if ( "" !== $m->m_trivia ) {
?>
					<h3><?php echo __( 'Match Trivia', 'bblm' ); ?></h3>
					<div class="bblm_details bblm_match_trivia">
						<p><?php echo  $m->m_trivia; ?></p>
					</div>
<?php
} // end of if ("" !== $m->m_trivia) {
?>
			<h3 class="bblm-table-caption"><?php echo __( 'Player Actions', 'bblm' ); ?></h3>
      <div role="region" aria-labelledby="Caption01" tabindex="0">
		<table class="bblm_table">
      <thead>
  			<tr>
  				<th><?php echo $teamA;?></th>
  				<th>VS</th>
  				<th><?php echo $teamB;?></th>
  			</tr>
      </thead>
      <tbody>
			<tr>
				<td>
<?php
			//Now we loop through the player actions for the match and record any increases and build the player actions table
				//First we initialize some valuables
				$tamvp="";
				$tbmvp="";
				$playeractions="";

        $taplayersql = 'SELECT M.*, Q.p_num, Q.WPID AS PWPID FROM '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'player Q WHERE Q.p_id = M.p_id AND M.m_id = '.$m->WPID.' AND M.t_id = '.$m->m_teamA.' ORDER BY Q.p_num ASC';
				if ( $taplayer = $wpdb->get_results( $taplayersql ) ) {
					//as we have players, initialize arrays to hold injuries and increases
					$zebracount = 1;
?>
          <table class="bblm_table">
            <thead>
              <tr>
                <th>#</th>
                <th><?php echo __('Player', 'bblm' ); ?></th>
                <th><?php echo __('TD', 'bblm' ); ?></th>
                <th><?php echo __('CAS', 'bblm' ); ?></th>
                <th><?php echo __('COMP', 'bblm' ); ?></th>
                <th><?php echo __('INT', 'bblm' ); ?></th>
                <th><?php echo __('SPP', 'bblm' ); ?></th>
              </tr>
            </thead>
            <tbody>
<?php
					foreach ( $taplayer as $tap ) {
						if ( 1 == $tap->mp_mvp ) {
							//if this player has the MVP record it for later
							//first it checks to see if an MVP has already been record for this team (in the event of a concession, there will be two for a team)
							if ( "" == $tamvp ) {
								$tamvp = "#".$tap->p_num;
							}
							else {
								$tamvp .=" and #".$tap->p_num;
							}
						}
						if ( $zebracount % 2 ) {
              echo '<tr class="bblm_tbl_alt">';
						}
						else {
              echo '<tr>';
						}
?>
            <td><?php echo $tap->p_num; ?></td>
            <td><?php echo bblm_get_player_link( $tap->PWPID ); ?></td>
            <td><?php echo $tap->mp_td; ?></td>
            <td><?php echo $tap->mp_cas; ?></td>
            <td><?php echo $tap->mp_comp; ?></td>
            <td><?php echo $tap->mp_int; ?></td>
            <td><strong><?php echo $tap->mp_spp; ?></strong></td>
          </tr>
<?php
						$zebracount++;
					}
          echo '</tbody></table></div>';
					//set flag to show some player actions have been recorded
					$playeractions = 1;
					//final check of the recorded MVP. If it is blank then set the default value to show that none was assigned (which is different to not recorded)
					if ("" == $tamvp) {
						$tamvp = "N/A";
					}
				}
				else {
          echo __( 'No Player actions have been recorded for this game', 'bblm' );
					$tanp = 1;
					$tamvp = "Not recorded";
				}
?>
						</td>
						<td>&nbsp;</td>
						<td>
<?php
        $tbplayersql = 'SELECT M.*, Q.p_num, Q.WPID AS PWPID FROM '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'player Q WHERE Q.p_id = M.p_id AND M.m_id = '.$m->WPID.' AND M.t_id = '.$m->m_teamB.' ORDER BY Q.p_num ASC';
				if ( $taplayer = $wpdb->get_results( $tbplayersql ) ) {
					//as we have players, initialize arrays to hold injuries and increases
					$zebracount = 1;
?>
          <table class="bblm_table">
            <thead>
              <tr>
                <th>#</th>
                <th><?php echo __('Player', 'bblm' ); ?></th>
                <th><?php echo __('TD', 'bblm' ); ?></th>
                <th><?php echo __('CAS', 'bblm' ); ?></th>
                <th><?php echo __('COMP', 'bblm' ); ?></th>
                <th><?php echo __('INT', 'bblm' ); ?></th>
                <th><?php echo __('SPP', 'bblm' ); ?></th>
              </tr>
            </thead>
            <tbody>
<?php
					foreach ($taplayer as $tap) {
						if (1 == $tap->mp_mvp) {
							//if this player has the MVP record it for later
							//first it checks to see if an MVP has already been record for this team (in the event of a concession, there will be two for a team)
							if ("" == $tbmvp) {
								$tbmvp = "#".$tap->p_num;
							}
							else {
								$tbmvp .=" and #".$tap->p_num;
							}
						}
						if ($zebracount % 2) {
              echo '<tr class="bblm_tbl_alt">';
						}
						else {
              echo '<tr>';
						}
?>
            <td><?php echo $tap->p_num; ?></td>
            <td><?php echo bblm_get_player_link( $tap->PWPID ); ?></td>
            <td><?php echo $tap->mp_td; ?></td>
            <td><?php echo $tap->mp_cas; ?></td>
            <td><?php echo $tap->mp_comp; ?></td>
            <td><?php echo $tap->mp_int; ?></td>
            <td><strong><?php echo $tap->mp_spp; ?></strong></td>
          </tr>
<?php
						$zebracount++;
					}
          echo '</tbody></table>';
					//set flag to show some player actions have been recorded
					$playeractions = 1;
					//final check of the recorded MVP. If it is blank then set the default value to show that none was assigned (which is different to not recorded)
					if ("" == $tbmvp) {
						$tbmvp = "N/A";
					}
				}
				else {
					echo __( 'No Player actions have been recorded for this game', 'bblm' );
					$tbnp = 1;
					$tbmvp = "Not recorded";
				}
?>
						</td>
					</tr>
					<tr>
						<td><?php echo $tamvp; ?></td>
						<th class="bblm_tottux"><?php echo __( 'MVP', 'bblm' ); ?></th>
						<td><?php echo $tbmvp; ?></td>
					</tr>
					<tr>
						<td>
<?php
            echo BBLM_CPT_Match::get_match_injuries( $m->WPID, $tA->TWPID );
?>
						</td>
						<th class="bblm_tottux"><?php echo __( 'Inj', 'bblm' ); ?></th>
						<td>
<?php
            echo BBLM_CPT_Match::get_match_injuries( $m->WPID, $tB->TWPID );
?>
						</td>
					</tr>
					<tr>
						<td>
<?php
						echo BBLM_CPT_Match::get_match_increases( $m->WPID, $tA->TWPID );
?>
						</td>
						<th class="bblm_tottux"><?php echo __( 'Inc', 'bblm' ); ?></th>
						<td>
<?php
            echo BBLM_CPT_Match::get_match_increases( $m->WPID, $tB->TWPID );
?>
						</td>
					</tr>
					<tr>
						<td><?php echo stripslashes( $tA->mt_comment );?></td>
						<th class="bblm_tottux"><?php echo __( 'Comments', 'bblm' ); ?></th>
						<td><?php echo stripslashes( $tB->mt_comment );?></td>
					</tr>
          <tbody>
				</table>
<?php
		} //end of if match SQL

?>
<footer class="entry-footer">
	<p class="postmeta"><?php bblm_display_page_edit_link(); ?></p>
</footer><!-- .entry-footer -->

</div><!-- .entry-content -->
</article>

<?php do_action( 'bblm_template_after_content' ); ?>
<?php endwhile; ?>
<?php do_action( 'bblm_template_after_loop' ); ?>
<?php endif; ?>
<?php do_action( 'bblm_template_after_posts' ); ?>
</main><!-- #main -->
</div><!-- #primary -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>
