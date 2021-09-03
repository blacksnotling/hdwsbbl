<?php
/**
 * BBowlLeagueMan Teamplate View Competition
 *
 * Page Template to view a competitions details
 *
 * @author 		Blacksnotling
 * @category 	Template
 * @package 	BBowlLeagueMan/Templates
 */
/*
 * Template Name: View Competition
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
<?php
    $cid = get_the_ID( $post->ID );
    $meta = get_post_custom( $post->ID );

		$today = date("Y-m-d");
    $compstartdate = DateTime::createFromFormat('Y-m-d', $meta['comp_sdate'][0] );
    $compstart = $compstartdate->format('Y-m-d');

      if ( $compstart > $today ) {
        echo	'<div class="bblm_info"><p>This Competition is <strong>Upcoming</strong>. It is due to start on '.$compstartdate->format("j M 25y").'.</p></div>';
			}
			else if ( BBLM_CPT_Comp::is_competition_active( $cid ) ) {
        echo '<div class="bblm_info"><p>This Competition is currently <strong>active</strong>. Stay tuned for further updates.</p></div>';
			}
			else {
				$winnersql = 'SELECT P.post_title, P.guid FROM '.$wpdb->prefix.'awards_team_comp A, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE A.t_id = J.tid AND J.prefix = \'t_\' AND J.pid = P.ID AND A.a_id = 1 AND A.c_id = '.$cid.' LIMIT 1';
				if ( $cw = $wpdb->get_row( $winnersql ) ) {
          //Display the complete message with the winner
          echo '<div class="bblm_info"><p>This Competition is now <strong>complete</strong>. The winners were <a href=' . $cw->guid . ' title="View more on the winners">' . $cw->post_title . '</a>.(<a href="#awardsfull" title="See the rest of the awards assigned in this competition">See more Awards</a>)</p></div>';
        }
        else {
          //no winner assigned
          echo '<div class="bblm_info"><p>This Competition is now <strong>complete</strong>. <a href="#awardsfull" title="See the rest of the awards assigned in this competition">See the Awards earnt this Competition</a></p></div>';
        }
			}
      if ( $meta['comp_legacy'][0] ) {
        bblm_display_legacy_notice( "Competition" );
      }
?>
				<div class="bblm_details bblm_comp_description">
					<?php the_content(); ?>
				</div>

<?php
			if ( $meta['comp_showstandings'][0] ) {
        echo '<h3>' . __( 'Standings', 'bblm') . '</h3>';
				//Check to see if this competition has a tournament element
        $checkbracketssql = 'SELECT cb_id FROM '.$wpdb->prefix.'comp_brackets WHERE c_id = ' . $cid;
        $cb_id = $wpdb->get_var( $checkbracketssql );

        if ( !empty( $cb_id ) ) {
          BBLM_CPT_Comp::display_comp_brackets( $cid );
        }

				if ( 3 != $meta['comp_format'][0] ) {

					//If the competition tyoe is not just a knock out tournament, then display the league table
					//May need to split this in the future, depending on league requirements
					$standingssql = 'SELECT T.WPID, C.*, D.div_name, D.div_id, SUM(C.tc_tdfor-C.tc_tdagst) AS TDD, SUM(C.tc_casfor-C.tc_casagst) AS CASD FROM '.$wpdb->prefix.'team_comp C, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'division D WHERE T.t_show = 1 AND C.div_id = D.div_id AND T.t_id = C.t_id AND C.c_id = '.$cid.' GROUP BY C.t_id ORDER BY D.div_id ASC, C.tc_points DESC, TDD DESC, CASD DESC, C.tc_tdfor DESC, C.tc_casfor DESC, T.t_name ASC';
					if ($standings = $wpdb->get_results($standingssql)) {
						$is_first_div = 1;
						$zebracount = 1;
						$lastdiv  ="";
						foreach ($standings as $stand) {
							//print the end of a table tag unless this was the first table
							if ($lastdiv !== $stand->div_name) {
								if (!TRUE == $is_first_div) {
									print("</table></div>\n");
									$zebracount = 1;
								}
								//cross division hardcode
								if (14 == $stand->div_id) {
                  echo '<h3>' . __( '** New World Confrence (NWC) **', 'bblm') . '</h3>';
								}
								if (16 == $stand->div_id) {
                  echo '<h3>' . __( '** Old World Confrence (OWC) **', 'bblm') . '</h3>';
								}
								//end cross division hard code
								print("<h4>".$stand->div_name."</h4>\n<div role=\"region\" aria-labelledby=\"Caption01\" tabindex=\"0\"><table class=\"bblm_table\">\n <tr>\n  <th>Team</th>\n  <th>Pld</th>\n  <th>W</th>\n  <th>D</th>\n  <th>L</th>\n  <th>TF</th>\n  <th>TA</th>\n  <th>TD</th>\n  <th>CF</th>\n  <th>CA</th>\n  <th>CD</th>\n  <th>PTS</th>\n </tr>\n");
							}
							$lastdiv = $stand->div_name;
							if ($zebracount % 2) {
								print("					<tr>\n");
							}
							else {
								print("					<tr class=\"bblm_tbl_alt\">\n");
							}
							print("  <td><a href=\"" . get_post_permalink( $stand->WPID ) . "\" title=\"View more information about this team\">" . esc_html( get_the_title( $stand->WPID ) ) . "</a></td>\n <td>".$stand->tc_played."</td>\n  <td>".$stand->tc_W."</td>\n  <td>".$stand->tc_D."</td>\n  <td>".$stand->tc_L."</td>\n  <td>".$stand->tc_tdfor."</td>\n  <td>".$stand->tc_tdagst."</td>\n  <td>".$stand->TDD."</td>\n  <td>".$stand->tc_casfor."</td>\n  <td>".$stand->tc_casagst."</td>\n  <td>".$stand->CASD."</td>\n  <td><strong>".$stand->tc_points."</strong></td>\n	</tr>\n");

							//set flag so resulting </table> is printed
							$is_first_div = 0;
							$zebracount++;
						}
						print("</table></div>\n");
?>
        <h4><?php echo __( 'Key','bblm'); ?></h4>
				<ul class="bblm_expandablekey">
					<li><strong>P</strong>: Number of games Played</li>
					<li><strong>TF</strong>: Number of Touchdowns scored by the team</li>
					<li><strong>TA</strong>: Number of Touchdowns scored against the team</li>
					<li><strong>CF</strong>: Number of Casulties caused by the team</li>
					<li><strong>CA</strong>: Number of Casulties the team has suffered</li>
				</ul>
<?php

					} //end of if stndings
				}//end of if c_type else
			}
			else {
				//The comp is set to NOT display the standings. as a result we display a list of teams
        echo '<h3>' . __( 'Participents','bblm') . '<h3>';
        echo '<p>' . __('Not all the participents for this Competition have been announced. So far the following teams have confirmed that they will be taking part:','bblm' ) . '</p>';
				$participentssql = 'SELECT DISTINCT(P.post_title), P.guid FROM '.$wpdb->posts.' P, '.$wpdb->prefix.'bb2wp J, '.$wpdb->prefix.'team_comp C, '.$wpdb->prefix.'team T WHERE T.t_show = 1 AND T.t_id = C.t_id AND T.t_id = J.tid AND J.prefix = \'t_\' AND J.pid = P.ID AND C.c_id = '.$cid.' ORDER BY P.post_title ASC';
				if ($participents = $wpdb->get_results($participentssql)) {
						print("<ul>\n");
						foreach ($participents as $part) {
							print(" <li><a href=\"".$part->guid."\" title=\"Learn more about ".$part->post_title."\">".$part->post_title."</a></li>\n");
						}
						print("</ul>\n");
				}
			}//end of show standings else

			  /////////////
			 // Matches //
			/////////////
			$match_present = 0;
      echo '<h3 class="bblm-table-caption">' . __('Matches','bblm' ) . '</h3>';
      $matchsql = 'SELECT UNIX_TIMESTAMP(M.m_date) AS mdate, M.WPID AS MWPID, M.m_gate, M.m_teamAtd, M.m_teamBtd, M.m_teamAcas, M.m_teamBcas FROM '.$wpdb->prefix.'match M WHERE M.c_id = '.$cid.' ORDER BY M.m_date DESC';
			if ( $match = $wpdb->get_results( $matchsql ) ) {
				//We have matches so we can proceed
?>
          <table class="bblm_table bblm_expandable">
            <thead>
              <tr>
                <th class="bblm_tbl_matchdate"><?php echo __( 'Date','bblm' ); ?></th>
                <th class="bblm_tbl_matchname"><?php echo __( 'Match','bblm' ); ?></th>
                <th class="bblm_tbl_matchresult"><?php echo __( 'Result','bblm' ); ?></th>
                <th class="bblm_tbl_matchgate"><?php echo __( 'Gate','bblm' ); ?></th>
              </tr>
            </thead>
            <tbody>
<?php
				$zebracount = 1;
				foreach ( $match as $md ) {
					if ( ( $zebracount % 2 ) && ( 10 < $zebracount ) ) {
						echo '<tr class="bblm_tbl_hide">';
					}
					else if (($zebracount % 2) && (10 >= $zebracount)) {
						echo '<tr>';
					}
					else if ( 10 < $zebracount ) {
            echo '<tr class="bblm_tbl_hide bblm_tbl_alt">';
					}
					else {
            echo '<tr class="bblm_tbl_alt">';
					}
?>
                <td><?php echo date("d.m.y", $md->mdate); ?></td>
                <td><?php echo bblm_get_match_link( $md->MWPID ); ?></a></td>
                <td><?php echo $md->m_teamAtd." - ".$md->m_teamBtd." (".$md->m_teamAcas." - ".$md->m_teamBcas.")"; ?></td>
                <td><em><?php echo number_format( $md->m_gate ); ?></em></td>
              </tr>
<?php
					$zebracount++;
				}
				echo '</tbody></table>';
				//set a flag so we know that a game has been played (therefore it has begun, list stats etc).
				$match_present = 1;
			} //end of if match SQL
			else {
				//There are no matches to display
				print("<p></p>	</div>.\n");
        echo '<div class="bblm_info"><p>' . __( 'No Matches have taken place in this competition yet. Stay tuned for further updates.', 'bblm' ) . '</p></div>';
			} //end of matches

			  //////////////
			 // Fixtures //
			//////////////
			$fixturesql = 'SELECT UNIX_TIMESTAMP(F.f_date) AS fdate, D.div_name, T.t_id AS TA, M.t_id AS TB, V.post_title AS TAname, O.post_title AS TBname, V.guid AS TAlink, O.guid AS TBlink FROM '.$wpdb->prefix.'fixture F, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'bb2wp U, '.$wpdb->posts.' V, '.$wpdb->prefix.'team M, '.$wpdb->prefix.'bb2wp N, '.$wpdb->posts.' O, '.$wpdb->prefix.'division D WHERE D.div_id = F.div_id AND T.t_id = F.f_teamA AND M.t_id = F.f_teamB AND T.t_id = U.tid AND U.prefix = \'t_\' AND U.pid = V.ID AND M.t_id = N.tid AND N.prefix = \'t_\' AND N.pid = O.ID AND F.f_complete = 0 AND F.c_id = '.$cid.' ORDER BY F.f_date ASC, F.div_id DESC';
			if ($fixtures = $wpdb->get_results($fixturesql)) {
        echo '<h3 class="bblm-table-caption">' . __( 'Upcoming Fixtures', 'bblm') . '</h3>';
				print("<table class=\"bblm_table bblm_expandable\">\n		 <tr>\n		   <th class=\"bblm_tbl_matchdate\">Date</th>\n		   <th class=\"bblm_tbl_matchname\">Match</th>\n		 </tr>\n");

				$is_first = 0;
				$current_div = "";
				$zebracount = 1;

				//grab the ID of the "tbd" team
				$bblm_tbd_team = bblm_get_tbd_team();

				foreach ($fixtures as $fd) {
					if (($zebracount % 2) && (10 < $zebracount)) {
						print("		<tr class=\"bblm_tbl_hide\">\n");
					}
					else if (($zebracount % 2) && (10 >= $zebracount)) {
						print("		<tr>\n");
					}
					else if (10 < $zebracount) {
						print("		<tr class=\"bblm_tbl_alt bblm_tbl_hide\">\n");
					}
					else {
						print("		<tr class=\"bblm_tbl_alt\">\n");
					}
					print("		   <td>".date("d.m.y", $fd->fdate)."</td>\n		<td>\n");
					if ($bblm_tbd_team == $fd->TA) {
						print($fd->TAname);
					}
					else {
						print("<a href=\"".$fd->TAlink."\" title=\"Learn more about ".$fd->TAname."\">".$fd->TAname."</a>");
					}
					print(" vs ");
					if ($bblm_tbd_team == $fd->TB) {
						print($fd->TBname);
					}
					else {
						print("<a href=\"".$fd->TBlink."\" title=\"Learn more about ".$fd->TBname."\">".$fd->TBname."</a>");
					}
					print("</td>\n	</tr>\n");
					$zebracount++;
				}
				print("</table>\n");
			} //end of if fixtures SQL

			  ///////////
			 // Stats //
			///////////
			if ($match_present) {
				//At least one match has been played so we can display stays
				  ///////////
				 // Team //
				///////////
        echo '<h3 class="bblm-table-caption">' . __( 'Team Statistics', 'bblm') . '</h3>';
				$teamstatssql = 'SELECT Z.WPID, SUM(T.tc_played) AS TP, SUM(T.tc_W) AS TW, SUM(T.tc_L) AS TL, SUM(T.tc_D) AS TD, SUM(T.tc_tdfor) AS TDF, SUM(T.tc_tdagst) AS TDA, SUM(T.tc_casfor) AS TCF, SUM(T.tc_casagst) AS TCA, SUM(T.tc_INT) AS TI, SUM(T.tc_comp) AS TC FROM '.$wpdb->prefix.'team_comp T, '.$wpdb->prefix.'team Z WHERE Z.t_id = T.t_id AND Z.t_show = 1 AND T.c_id = '.$cid.' GROUP BY T.t_id ORDER BY Z.t_name ASC';
				if ($teamstats = $wpdb->get_results($teamstatssql)) {
					$zebracount = 1;
?>
        <div role="region" aria-labelledby="Caption01" tabindex="0">
				<table class="bblm_table bblm_sortable">
					<thead>
					<tr>
						<th class="bblm_tbl_name">Team</th>
						<th class="bblm_tbl_stat">P</th>
						<th class="bblm_tbl_stat">W</th>
						<th class="bblm_tbl_stat">L</th>
						<th class="bblm_tbl_stat">D</th>
						<th class="bblm_tbl_stat">TF</th>
						<th class="bblm_tbl_stat">TA</th>
						<th class="bblm_tbl_stat">CF</th>
						<th class="bblm_tbl_stat">CA</th>
						<th class="bblm_tbl_stat">COMP</th>
						<th class="bblm_tbl_stat">INT</th>
						<th class="bblm_tbl_stat">Win %</th>
					</tr>
					</thead>
					<tbody>
<?php
					foreach ($teamstats as $ps) {
						if ($zebracount % 2) {
							print("					<tr>\n");
						}
						else {
							print("					<tr class=\"bblm_tbl_alt\">\n");
						}
?>
						<td><a href="<?php print( get_post_permalink( $ps->WPID ) ); ?>" title="Read more on this team"><?php print( esc_html( get_the_title( $ps->WPID ) ) ); ?></a></td>
						<td><?php print($ps->TP); ?></td>
						<td><?php print($ps->TW); ?></td>
						<td><?php print($ps->TL); ?></td>
						<td><?php print($ps->TD); ?></td>
						<td><?php print($ps->TDF); ?></td>
						<td><?php print($ps->TDA); ?></td>
						<td><?php print($ps->TCF); ?></td>
						<td><?php print($ps->TCA); ?></td>
						<td><?php print($ps->TC); ?></td>
						<td><?php print($ps->TI); ?></td>
<?php
						//we have to break this down as 0 / 0  = big error!
						if ($ps->TP > 0) {
							print("				<td>".round(($ps->TW/$ps->TP)*100, 2)."%</td>\n");
						}
						else {
							print("				<td>0%</td>\n");
						}
?>
					</tr>
<?php
						$zebracount++;
					}
          echo '</tbody>';
          echo '</table>';
          echo '</div>';
				} //end of if team-stats SQL

				///////////////////////////
			 // Start of Player Stats //
			///////////////////////////
				echo '<h3>' . __( 'Player Statistics for this competition', 'bblm' ) . '</h3>';

				$bblm_stats = new BBLM_Stat;

				$stat_limit = bblm_get_stat_limit();

				$bblm_stats->display_top_players_table( $stat_limit );
				$bblm_stats->display_top_killers_table( $stat_limit );

					  /////////////////////////
					 // End of Player Stats //
					/////////////////////////

					//Awards
					if ( !BBLM_CPT_Comp::is_competition_active( $cid ) ) {
						//the comp is over, display the awards!

          $bblm_award = new BBLM_CPT_Award;
?>
          <h3 id="awardsfull bblm_awardsfull"><?php echo __( 'Awards', 'bblm'); ?></h3>
          <?php $bblm_award->display_list_award_winners(); ?>
<?php

					}

			}//end of if_matches (for stats)



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
