<?php
/**
 * BBowlLeagueMan Teamplate View Player
 *
 * Page Template to view Players details
 *
 * @author 		Blacksnotling
 * @category 	Template
 * @package 	BBowlLeagueMan/Templates
 */
/*
 * Template Name: View Player
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
		<?php
			/*
			Gather Information for page
			*/
      //Although this will be replaced by functions, it still needs to exist for the time being until everything is converted
			$playersql = 'SELECT P.*, T.WPID, E.pos_name, P.WPID AS PWPID FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' X, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'position E WHERE P.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = X.ID AND X.ID = '.$post->ID.' AND P.t_id = T.t_id AND P.pos_id = E.pos_id';
			//if ($player = $wpdb->get_results($playersql)) {
			if ( $pd = $wpdb->get_row( $playersql ) ) {
				$pspp = $pd->p_spp;

			} //end of if playersql

      $legacy = 0;

				if ( 0 == $pd->p_status ) {
					$status = "Inactive";
				}
				else {
					$status = "Active";
				}
		?>

		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

			<header class="entry-header">
				<h2 class="entry-title"><?php the_title(); ?></h2>
			</header><!-- .entry-header -->

				<div class="entry-content">
<?php
          if ( BBLM_CPT_Player::is_player_legacy( $pd->PWPID ) ) {
            $legacy = 1;
            bblm_display_legacy_notice( "Player" );
          }

          BBLM_CPT_Player::display_player_characteristics( $pd->PWPID );
?>

					<div class="bblm_details bblm_player_description">
						<?php the_content(); ?>
					</div>

<?php
					if (0 == $pd->p_status) {
						//If the player is inactive, see if they were killed.
						$fatesql = 'SELECT pf_killer, f_id, pf_desc FROM `'.$wpdb->prefix.'player_fate` WHERE ( f_id = 1 OR f_id = 6 OR f_id = 7 ) AND p_id = '.$pd->p_id.' LIMIT 1';
						if ($fate = $wpdb->get_row($fatesql)) {
              echo '<h3>' . __( 'Obituary', 'bblm') . '</h3>';
              echo '<p>'. __( 'This player is Dead! They were killed by ','bblm' );
							if ("0" == $fate->pf_killer) {
								print("an unkown player.</p>\n");
							}
							else if ("C" == $fate->pf_killer) {
								print("the crowd!</p>\n");
							}
							else if ("W" == $fate->pf_killer) {
								print("a wizard!</p>\n");
							}
							else if ("$pd->p_id" == $fate->pf_killer) {
								print("Themself!!</p>\n");
							}
							else {
								//It must be a player
								$killersql = 'SELECT P.post_title AS PLAYER, P.guid AS PLAYERLink, T.WPID FROM '.$wpdb->prefix.'player_fate F, '.$wpdb->prefix.'player X, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE X.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = P.ID AND F.pf_killer = X.p_id AND X.t_id = T.t_id AND F.p_id = '.$pd->p_id.' LIMIT 1';
								if ($killer = $wpdb->get_row($killersql)) {
									print("<a href=\"".$killer->PLAYERLink."\" title=\"Read more about this player\">".$killer->PLAYER."</a> from <a href=\"" . get_post_permalink( $killer->WPID ) . "\" title=\"Read more about this team\">" . esc_html( get_the_title( $killer->WPID ) ) . "</a>");
								}
								else {
									print("an unkown player.</p>\n");
								}
							}
							print("							<div class=\"bblm_details bblm_obit\">\n							<p>".$fate->pf_desc."</p>\n							</div>\n");
						}
					}

					// -- KILLER --
					$killersql = 'SELECT O.post_title AS PLAYER, O.guid AS PLAYERLink, T.WPID, X.pos_name FROM '.$wpdb->prefix.'player_fate F, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' O, '.$wpdb->prefix.'position X WHERE F.p_id = P.p_id AND P.t_id = T.t_id AND P.pos_id = X.pos_id AND F.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = O.ID AND F.pf_killer = '.$pd->p_id.' AND F.p_id != '.$pd->p_id.' ORDER BY F.m_id ASC';
					if ( $killer = $wpdb->get_results( $killersql ) ) {
						//If the player has killed people
?>
						<h3><?php echo __( 'Killer!', 'bblm' ); ?></h3>
						<p><?php echo __( 'This player has killed another player in the course of their career. They have killed the following players:', 'bblm' ); ?></p>
						<ul>
<?php
						foreach ( $killer as $k ) {

							echo '<li><a href="' . $k->PLAYERLink . '" title="Read more about ' . $k->PLAYER . '">' . $k->PLAYER . '</a> (' . esc_html( $k->pos_name ) . ' for <a href="' . get_post_permalink( $k->WPID ) . '" title="Read more about this team">' . esc_html( get_the_title( $k->WPID ) ) . '</a>)</li>';
						}
?>
						</ul>
<?php
					}


					$statssql = 'SELECT COUNT(*) AS GAMES, SUM(M.mp_td) AS TD, SUM(M.mp_cas) AS CAS, SUM(M.mp_comp) AS COMP, SUM(M.mp_int) AS MINT, SUM(M.mp_mvp) AS MVP, SUM(M.mp_spp) AS SPP, T.WPID, M.mp_counts FROM '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'team T WHERE M.t_id = T.t_id AND M.p_id = '.$pd->p_id.' GROUP BY M.mp_counts, T.t_id ORDER BY M.mp_counts DESC, T.t_id DESC';
					if ( $stats = $wpdb->get_results( $statssql ) ) {
						//This player has played at least one game

						//Transfers
						//Before we display the stats, we check to see if the player has been transfered, and display the transfer history for them
						$trans = new BBLM_CPT_Transfer;
						$trans->display_player_transfer_history();
	?>
						<h3 class="bblm-table-caption"><?php echo __( 'Player Statistics', 'bblm' ); ?></h3>
            <div role="region" aria-labelledby="Caption01" tabindex="0">
						<table class="bblm_table">
							<thead>
	 							<tr>
	 								<th class="bblm_tbl_title"><?php echo __( 'Performance', 'bblm' ); ?></th>
	 								<th class="bblm_tbl_stat"><?php echo __( 'P', 'bblm' ); ?></th></th>
	 								<th class="bblm_tbl_stat"><?php echo __( 'TD', 'bblm' ); ?></th></th>
	 								<th class="bblm_tbl_stat"><?php echo __( 'CAS', 'bblm' ); ?></th></th>
	 								<th class="bblm_tbl_stat"><?php echo __( 'COMP', 'bblm' ); ?></th></th>
	 								<th class="bblm_tbl_stat"><?php echo __( 'INT', 'bblm' ); ?></th></th>
	 								<th class="bblm_tbl_stat"><?php echo __( 'MVP', 'bblm' ); ?></th></th>
	 								<th class="bblm_tbl_stat"><?php echo __( 'SPP', 'bblm' ); ?></th></th>
	 							</tr>
							</thead>
							<tbody>
<?php
						foreach ( $stats as $s ) {
							if ( $s->mp_counts ) {
								echo '<tr>';
								echo '<td><a href="' . get_post_permalink( $s->WPID ) . '" title="Read more about this team">' . esc_html( get_the_title( $s->WPID ) ) . '</a></td>';
							}
							else {
								echo '<tr>';
								echo '<td>' . __('Exhibition Record', 'bblm') . '</td>';
							}

							echo '<td>' . $s->GAMES . '</td>';
							echo '<td>' . $s->TD . '</td>';
							echo '<td>' . $s->CAS . '</td>';
							echo '<td>' . $s->COMP . '</td>';
							echo '<td>' . $s->MINT . '</td>';
							echo '<td>' . $s->MVP . '</td>';
							echo '<td>' . $s->SPP . '</td>';
							echo '</tr>';
						}
	?>
							</tbody>
						</table>
          </div>

            <h3 class="bblm-table-caption"><?php echo __( 'Performance by Season', 'bblm' ); ?></h3>
            <div role="region" aria-labelledby="Caption01" tabindex="0">
						<table class="bblm_table">
							<thead>
								<tr>
									<th class="bblm_tbl_title"><?php echo __( 'Season', 'bblm' ); ?></th>
									<th class="bblm_tbl_stat"><?php echo __( 'P', 'bblm' ); ?></th>
									<th class="bblm_tbl_stat"><?php echo __( 'TD', 'bblm' ); ?></th>
									<th class="bblm_tbl_stat"><?php echo __( 'CAS', 'bblm' ); ?></th>
									<th class="bblm_tbl_stat"><?php echo __( 'INT', 'bblm' ); ?></th>
									<th class="bblm_tbl_stat"><?php echo __( 'COMP', 'bblm' ); ?></th>
									<th class="bblm_tbl_stat"><?php echo __( 'MVP', 'bblm' ); ?></th>
									<th class="bblm_tbl_stat"><?php echo __( 'SPP', 'bblm' ); ?></th>
								</tr>
							</thead>
							<tbody>
<?php
					$playerseasql = 'SELECT C.sea_id, COUNT(*) AS GAMES, SUM(M.mp_td) AS TD, SUM(M.mp_cas) AS CAS, SUM(M.mp_comp) AS COMP, SUM(M.mp_int) AS MINT, SUM(M.mp_mvp) AS MVP, SUM(M.mp_spp) AS SPP FROM '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'match Q WHERE C.c_counts = 1 AND M.m_id = Q.WPID AND Q.c_id = C.WPID AND M.p_id = P.p_id AND M.p_id = '.$pd->p_id.' GROUP BY C.sea_id ORDER BY C.sea_id DESC';
					if ( $playersea = $wpdb->get_results( $playerseasql ) ) {
					$zebracount = 1;
						foreach ( $playersea as $pc ) {
							if ( $zebracount % 2 ) {
                echo '<tr class="bblm_tbl_alt">';
							}
							else {
								echo '<tr>';
							}
							echo '<td>' . bblm_get_season_link( $pc->sea_id ) . '</td>';
							echo '<td>' . $pc->GAMES . '</td>';
							echo '<td>' . $pc->TD . '</td>';
							echo '<td>' . $pc->CAS . '</td>';
							echo '<td>' . $pc->MINT . '</td>';
							echo '<td>' . $pc->COMP . '</td>';
							echo '<td>' . $pc->MVP . '</td>';
							echo '<td>' . $pc->SPP . '</td>';
							echo '</tr>';

							$zebracount++;
						}
					}
?>
						</tbody>
					</table>
        </div>

          <h3 class="bblm-table-caption"><?php echo __( 'Performance by Championship Cup', 'bblm' ); ?></h3>
          <div role="region" aria-labelledby="Caption01" tabindex="0">
          <table class="bblm_table">
            <thead>
              <tr>
                <th class="bblm_tbl_title"><?php echo __( 'Championsip Cup', 'bblm' ); ?></th>
                <th class="bblm_tbl_stat"><?php echo __( 'P', 'bblm' ); ?></th>
                <th class="bblm_tbl_stat"><?php echo __( 'TD', 'bblm' ); ?></th>
                <th class="bblm_tbl_stat"><?php echo __( 'CAS', 'bblm' ); ?></th>
                <th class="bblm_tbl_stat"><?php echo __( 'INT', 'bblm' ); ?></th>
                <th class="bblm_tbl_stat"><?php echo __( 'COMP', 'bblm' ); ?></th>
                <th class="bblm_tbl_stat"><?php echo __( 'MVP', 'bblm' ); ?></th>
                <th class="bblm_tbl_stat"><?php echo __( 'SPP', 'bblm' ); ?></th>
              </tr>
            </thead>
            <tbody>
<?php
        $playerseasql = 'SELECT C.series_id, COUNT(*) AS GAMES, SUM(M.mp_td) AS TD, SUM(M.mp_cas) AS CAS, SUM(M.mp_comp) AS COMP, SUM(M.mp_int) AS MINT, SUM(M.mp_mvp) AS MVP, SUM(M.mp_spp) AS SPP FROM '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'match Q WHERE C.c_counts = 1 AND M.m_id = Q.WPID AND Q.c_id = C.WPID AND M.p_id = P.p_id AND M.p_id = '.$pd->p_id.' GROUP BY C.series_id ORDER BY C.series_id DESC';
        if ( $playersea = $wpdb->get_results( $playerseasql ) ) {
        $zebracount = 1;
          foreach ( $playersea as $pc ) {
            if ( $zebracount % 2 ) {
              echo '<tr class="bblm_tbl_alt">';
            }
            else {
              echo '<tr>';
            }
            echo '<td>' . bblm_get_cup_link( $pc->series_id ) . '</td>';
            echo '<td>' . $pc->GAMES . '</td>';
            echo '<td>' . $pc->TD . '</td>';
            echo '<td>' . $pc->CAS . '</td>';
            echo '<td>' . $pc->MINT . '</td>';
            echo '<td>' . $pc->COMP . '</td>';
            echo '<td>' . $pc->MVP . '</td>';
            echo '<td>' . $pc->SPP . '</td>';
            echo '</tr>';

            $zebracount++;
          }
        }
?>
          </tbody>
        </table>
      </div>

						<h3 class="bblm-table-caption"><?php echo __( 'Performance by Competition', 'bblm' ); ?></h3>
            <div role="region" aria-labelledby="Caption01" tabindex="0">
						<table class="bblm_table">
							<thead>
								<tr>
									<th class="bblm_tbl_title"><?php echo __( 'Competition', 'bblm' ); ?></th>
									<th class="bblm_tbl_stat"><?php echo __( 'P', 'bblm' ); ?></th>
									<th class="bblm_tbl_stat"><?php echo __( 'TD', 'bblm' ); ?></th>
									<th class="bblm_tbl_stat"><?php echo __( 'CAS', 'bblm' ); ?></th>
									<th class="bblm_tbl_stat"><?php echo __( 'INT', 'bblm' ); ?></th>
									<th class="bblm_tbl_stat"><?php echo __( 'COMP', 'bblm' ); ?></th>
									<th class="bblm_tbl_stat"><?php echo __( 'MVP', 'bblm' ); ?></th>
									<th class="bblm_tbl_stat"><?php echo __( 'SPP', 'bblm' ); ?></th>
								</tr>
							</thead>
							<tbody>
<?php
					$playercompsql = 'SELECT COUNT(*) AS GAMES, SUM(M.mp_td) AS TD, SUM(M.mp_cas) AS CAS, SUM(M.mp_comp) AS COMP, SUM(M.mp_int) AS MINT, SUM(M.mp_mvp) AS MVP, SUM(M.mp_spp) AS SPP, C.WPID AS CWPID FROM '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'match Q WHERE M.m_id = Q.WPID AND Q.c_id = C.WPID AND M.p_id = P.p_id AND M.p_id = '.$pd->p_id.' GROUP BY C.c_id ORDER BY C.c_id DESC';
					if ( $playercomp = $wpdb->get_results( $playercompsql ) ) {
						$zebracount = 1;
						foreach ( $playercomp as $pc ) {
							if ( $zebracount % 2 ) {
                echo '<tr class="bblm_tbl_alt">';
							}
							else {
								echo '<tr>';
							}
							echo '<td>' . bblm_get_competition_link( $pc->CWPID  ) . '</td>';
							echo '<td>' . $pc->GAMES . '</td>';
							echo '<td>' . $pc->TD . '</td>';
							echo '<td>' . $pc->CAS . '</td>';
							echo '<td>' . $pc->MINT . '</td>';
							echo '<td>' . $pc->COMP . '</td>';
							echo '<td>' . $pc->MVP . '</td>';
							echo '<td>' . $pc->SPP . '</td>';
							echo '</tr>';
							$zebracount++;
						}
					}
?>
							</tbody>
						</table>
          </div>

					<h3 class="bblm-table-caption"><?php echo __( 'Recent Matches', 'bblm' ); ?></h3>
          <div role="region" aria-labelledby="Caption01" tabindex="0">
						<table class="bblm_table bblm_sortable bblm_expandable">
							<thead>
								<tr>
									<th><?php echo __( 'Vs', 'bblm' ); ?></th>
									<th><?php echo __( 'TD', 'bblm' ); ?></th>
									<th><?php echo __( 'CAS', 'bblm' ); ?></th>
									<th><?php echo __( 'INT', 'bblm' ); ?></th>
									<th><?php echo __( 'COMP', 'bblm' ); ?></th>
									<th><?php echo __( 'MVP', 'bblm' ); ?></th>
									<th><?php echo __( 'SPP', 'bblm' ); ?></th>
									<th><?php echo __( 'MNG?', 'bblm' ); ?></th>
<?php
                  if ( $legacy ) {
?>
									<th><?php echo __( 'Increase', 'bblm' ); ?></th>
									<th><?php echo __( 'Injury', 'bblm' ); ?></th>
<?php
                  }
?>
								</tr>
							</thead>
							<tbody>
<?php
            //Assumes player has played in one match for this to display thus far
            $playermatchsql = 'SELECT P.*, M.WPID AS MWPID, UNIX_TIMESTAMP(M.m_date) AS mdate, A.WPID as TA, A.t_id AS TAid, B.WPID AS TB, B.t_id AS TBid FROM '.$wpdb->prefix.'match_player P, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'team A, '.$wpdb->prefix.'team B WHERE M.m_teamA = A.t_id AND M.m_teamB = B.t_id AND M.WPID = P.m_id AND P.p_id = ' . $pd->p_id . ' ORDER BY M.m_date DESC';
						if ( $playermatch = $wpdb->get_results( $playermatchsql ) ) {
						$zebracount = 1;
							foreach ( $playermatch as $pm ) {
								if ( ( $zebracount % 2 ) && ( 10 < $zebracount ) ) {
                  echo '<tr class="bblm_tbl_alt bblm_tbl_hide">';
								}
								else if ( ( $zebracount % 2 ) && ( 10 >= $zebracount ) ) {
                  echo '<tr class="bblm_tbl_alt">';
								}
								else if ( 10 < $zebracount ) {
									echo '<tr class="bblm_tbl_hide">';
								}
								else {
									echo '<tr>';
								}
								if ( $pm->TAid == $pd->t_id ) {
                  echo '<td>' . bblm_get_team_link( $pm->TB ) . '<br />' . bblm_get_match_link_date( $pm->MWPID ) . '</td>';
								}
								else {
									echo '<td>' . bblm_get_team_link( $pm->TA ) . '<br />' . bblm_get_match_link_date( $pm->MWPID ) . '</td>';
								}
								echo '<td>';
								if ( 0 == $pm->mp_td ) {
									echo '0';
								}
								else {
									echo '<strong>' . $pm->mp_td . '</strong>';
								}
								echo '</td>';
								echo '<td>';
								if ( 0 == $pm->mp_cas ) {
									echo '0';
								}
								else {
									echo '<strong>' . $pm->mp_cas . '</strong>';
								}
								echo '</td>';
								echo '<td>';
								if ( 0 == $pm->mp_int ) {
									echo '0';
								}
								else {
									echo '<strong>' . $pm->mp_int . '</strong>';
								}
								echo '</td>';
								echo '<td>';
								if ( 0 == $pm->mp_comp ) {
									echo '0';
								}
								else {
									echo '<strong>' . $pm->mp_comp. '</strong>';
								}
								echo '</td>';
								echo '<td>';
								if ( 0 == $pm->mp_mvp ) {
									echo '0';
								}
								else {
									echo '<strong>' . $pm->mp_mvp . '</strong>';
								}
								echo '</td>';
								echo '<td>';
								if ( 0 == $pm->mp_spp ) {
									echo '0';
								}
								else {
									echo '<strong>' . $pm->mp_spp . '</strong>';
								}
								echo '</td>';
								echo '<td>';
								if ( 0 == $pm->mp_mng ) {
									echo '0';
								}
								else {
									echo '<strong>Y</strong>';
								}
								echo '</td>';
                if ( $legacy ) {
  								echo '<td>';
  								if ( "none" == $pm->mp_inc ) {
  									echo '-';
  								}
  								else {
  									echo '<strong>' . $pm->mp_inc . '</strong>';
  								}
  								echo '</td>';
  								echo '<td>';
  								if ( "none" == $pm->mp_inj ) {
  									echo '-';
  								}
  								else {
  									echo '<strong>' . $pm->mp_inj . '</strong>';
  								}
  								echo '</td>';
                }//end of if legacy
								$zebracount++;
							}
						}
?>
							</tbody>
						</table>
          </div>

<?php
            if ( ! $legacy ) {
?>
              <h3 class="bblm-table-caption"><?php echo __( 'Increase History', 'bblm' ); ?></h3>
              <div role="region" aria-labelledby="Caption01" tabindex="0">
                <table class="bblm_table bblm_sortable bblm_expandable">
                  <thead>
                    <tr>
                      <th><?php echo __( '#', 'bblm' ); ?></th>
                      <th><?php echo __( 'Vs', 'bblm' ); ?></th>
                      <th><?php echo __( 'Increase', 'bblm' ); ?></th>
                      <th><?php echo __( 'Cost', 'bblm' ); ?></th>
                    </tr>
                  </thead>
                  <tbody>
<?php
              $incinjsql = 'SELECT M.WPID AS MWPID, A.WPID AS TAWPID, B.WPID AS TBWPID, A.t_id AS TAid, B.t_id AS TBid, P.t_id AS TPid, S.skill_name, X.inc_name, X.inc_tier, X.inc_spp, X.inc_cost FROM '.$wpdb->prefix.'player_increase I, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team A, '.$wpdb->prefix.'team B, '.$wpdb->prefix.'skills S, '.$wpdb->prefix.'increase X WHERE S.skill_id = I.skill_id AND X.inc_id = I.inc_id AND M.m_teamA = A.t_id AND M.m_teamB = B.t_id AND M.WPID = I.m_id AND I.p_id = P.WPID AND P.WPID = ' . $pd->PWPID . ' ORDER BY X.inc_tier ASC';
              if ( $incinj = $wpdb->get_results( $incinjsql) ) {
                $zebracount = 1;
                foreach ( $incinj as $ii ) {
                  if ( $zebracount % 2 ) {
                    echo '<tr class="bblm_tbl_alt">';
                  }
                  else {
                    echo '<tr>';
                  }
                  echo '<td>' . bblm_ordinal( $ii->inc_tier ) . ' Skill</td>';
                  if ( $ii->TAid == $ii->TPid ) {
                    echo '<td>' . bblm_get_team_link( $ii->TBWPID ) . '<br />' . bblm_get_match_link_date( $ii->MWPID ) . '</td>';
                  }
                  else {
                    echo '<td>' . bblm_get_team_link( $ii->TAWPID ) . '<br />' . bblm_get_match_link_date( $ii->MWPID ) . '</td>';
                  }
                  echo '<td>' . esc_html( $ii->skill_name ) . ' (' . esc_html( $ii->inc_name ) . ')</td>';
                  echo '<td>-' . esc_html( $ii->inc_spp ) . 'SPP / +' . number_format( esc_html( $ii->inc_cost ) ) . 'gp</td>';
                  echo '</tr>';

                  $zebracount++;
                }
              }
              else {
                //The player has gained no increases or suffered no injuries
                echo '<tr><td colSpan="3">' . __( 'This Player has gained no increases', 'bblm' ) . '</td></tr>';
              }
?>
                  </tbody>
                  </table>
              </div>

              <h3 class="bblm-table-caption"><?php echo __( 'Injury History', 'bblm' ); ?></h3>
              <div role="region" aria-labelledby="Caption01" tabindex="0">
                <table class="bblm_table bblm_sortable bblm_expandable">
                  <thead>
                    <tr>
                      <th><?php echo __( 'Vs', 'bblm' ); ?></th>
                      <th><?php echo __( 'Injury', 'bblm' ); ?></th>
                      <th><?php echo __( 'MNG?', 'bblm' ); ?></th>
                    </tr>
                  </thead>
                  <tbody>
<?php
              $incinjsql = 'SELECT M.WPID AS MWPID, A.WPID AS TAWPID, B.WPID AS TBWPID, A.t_id AS TAid, B.t_id AS TBid, P.t_id AS TPid, X.inj_name, X.inj_mng FROM '.$wpdb->prefix.'player_increase I, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team A, '.$wpdb->prefix.'team B, '.$wpdb->prefix.'injury X WHERE X.inj_id = I.inj_id AND M.m_teamA = A.t_id AND M.m_teamB = B.t_id AND M.WPID = I.m_id AND I.p_id = P.WPID AND P.WPID = ' . $pd->PWPID . ' ORDER BY M.m_date DESC';
              if ( $incinj = $wpdb->get_results( $incinjsql) ) {
                $zebracount = 1;
                foreach ( $incinj as $ii ) {
                  if ( $zebracount % 2 ) {
                    echo '<tr class="bblm_tbl_alt">';
                  }
                  else {
                    echo '<tr>';
                  }
                  if ( $ii->TAid == $ii->TPid ) {
                    echo '<td>' . bblm_get_team_link( $ii->TBWPID ) . '<br />' . bblm_get_match_link_date( $ii->MWPID ) . '</td>';
                  }
                  else {
                    echo '<td>' . bblm_get_team_link( $ii->TAWPID ) . '<br />' . bblm_get_match_link_date( $ii->MWPID ) . '</td>';
                  }
                  echo '<td>' . esc_html( $ii->inj_name ) . '</td>';
                  if ( $ii->inj_mng ) {
                    echo '<td>Y</td>';
                  }
                  else {
                    echo '<td>N</td>';
                  }
                  echo '</tr>';

                  $zebracount++;
                }
              }
              else {
                //The player has suffered no injuries
                echo '<tr><td colSpan="3">' . __( 'This Player has sustained no injuries', 'bblm' ) . '</td></tr>';
              }
?>
                </tbody>
                </table>
              </div>

<?php

        }//end of if not legacy
?>

						<h3><?php echo __( 'Awards list in full', 'bblm' ); ?></h3>
<?php
						$championshipssql = 'SELECT A.a_name, B.c_id AS CWPID FROM '.$wpdb->prefix.'player X, '.$wpdb->prefix.'awards A, '.$wpdb->prefix.'awards_team_comp B, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'match_player Z, '.$wpdb->prefix.'match V WHERE X.p_id = Z.p_id AND V.m_id = Z.m_id AND V.c_id = C.WPID AND X.t_id = B.t_id AND A.a_id = B.a_id AND a_cup = 1 AND B.c_id = C.WPID AND X.p_id = '.$pd->p_id.' GROUP BY C.c_id ORDER BY A.a_id ASC LIMIT 0, 30 ';
						if ($champs = $wpdb->get_results($championshipssql)) {
							$has_cups = 1;
							$zebracount = 1;
              echo '<h4 class="bblm-table-caption">' . __('Championships','bblm' ) . '</h4>';
							print("<table class=\"bblm_table\">\n	<thead><tr>\n		<th class=\"bblm_tbl_name\">Title</th>\n		<th class=\"bblm_tbl_name\">Competition</th>\n	</tr></thead><tbody>\n");
							foreach ($champs as $cc) {
								if ($zebracount % 2) {
                  print("		<tr class=\"bblm_tbl_alt\">\n");
								}
								else {
									print("		<tr>\n");
								}
								print("		<td>".$cc->a_name."</td>\n		<td>" . bblm_get_competition_link( $cc->CWPID ) . "</td>\n	</tr>\n");
								$zebracount++;
							}
							print("</tbody></table>\n");
						}
						else {
							$ccfail = 1;
						}

						$seasonsql = 'SELECT A.a_name, B.sea_id, B.aps_value FROM '.$wpdb->prefix.'awards A, '.$wpdb->prefix.'awards_player_sea B WHERE A.a_id = B.a_id AND B.p_id = '.$pd->p_id.' ORDER BY A.a_id ASC';
						if ($sawards = $wpdb->get_results($seasonsql)) {
							$zebracount = 1;
              echo '<h4 class="bblm-table-caption">' . __('Awards from Seasons','bblm' ) . '</h4>';
							print("<table class=\"bblm_table\">\n	<thead><tr>\n		<th class=\"bblm_tbl_name\">Award</th>\n		<th class=\"bblm_tbl_name\">Competition</th>\n		<th class=\"bblm_tbl_stat\">Value</th>\n	</tr></thead><tbody>\n");
							foreach ($sawards as $sa) {
                if ($zebracount % 2) {
                  print("		<tr class=\"bblm_tbl_alt\">\n");
								}
								else {
									print("		<tr>\n");
								}
								print("		<td>".$sa->a_name."</td>\n		<td>" . bblm_get_season_link( $sa->sea_id ) . "</td>\n		<td>".$sa->aps_value."</td>\n	</tr>\n");
								$zebracount++;
							}
							print("</tbody></table>\n");
						}
						else {
							$safail = 1;
						}

						$cafail = 0;
						$compawardssql = 'SELECT A.a_name, B.apc_value, B.c_id AS CWPID FROM '.$wpdb->prefix.'awards A, '.$wpdb->prefix.'awards_player_comp B, '.$wpdb->prefix.'comp C WHERE A.a_id = B.a_id AND a_cup = 0 AND B.c_id = C.WPID AND B.p_id = '.$pd->p_id.' ORDER BY A.a_id ASC';
						if ($cawards = $wpdb->get_results($compawardssql)) {
							$zebracount = 1;
							echo '<h4 class="bblm-table-caption">' . __('Awards from Competitions','bblm' ) . '</h4>';
							print("<table class=\"bblm_table\">\n	<thead><tr>\n		<th class=\"bblm_tbl_name\">Award</th>\n		<th class=\"bblm_tbl_name\">Competition</th>\n		<th class=\"bblm_tbl_stat\">Value</th>\n	</tr></thead><tbody>\n");
							foreach ($cawards as $ca) {
                if ($zebracount % 2) {
                  print("		<tr class=\"bblm_tbl_alt\">\n");
  							}
  							else {
  								print("		<tr>\n");
  							}
								print("		<td>".$ca->a_name."</td>\n		<td>" . bblm_get_competition_link( $ca->CWPID ) . "</td>\n		<td>".$ca->apc_value."</td>\n	</tr>\n");
								$zebracount++;
							}
            print("</tbody></table>\n");
						}
						else {
							$cafail = 1;
						}

						if ($cafail && $safail && $ccfail) {
							//no awards at all
							print("	<div class=\"bblm_info\">\n		<p>This player has not any awards as of yet.</p>\n	</div>\n");
						}


						$has_played = 1;
					}//end of if player has played a game
					else {
						//Player has not made debut yet
						print("	<div class=\"bblm_info\">\n	 <p>This player has not made their Debut yet. Stay tuned for further developments.</p>\n	</div>\n");
					}

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
