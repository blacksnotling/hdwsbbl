<?php
/**
 * BBowlLeagueMan Teamplate View Team
 *
 * Page Template to view a Teams's details
 *
 * @author 		Blacksnotling
 * @category 	Template
 * @package 	BBowlLeagueMan/Templates
 */
 /*
  * Template Name: View Team
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
		$teaminfosql = 'SELECT T.*, J.tid AS teamid, R.r_name, L.guid AS racelink, T.stad_id FROM '.$wpdb->prefix.'team T, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P, '.$wpdb->prefix.'race R, '.$wpdb->prefix.'bb2wp K, '.$wpdb->posts.' L WHERE T.r_id = K.tid AND K.prefix = \'r_\' AND K.pid = L.ID AND R.r_id = T.r_id AND T.t_id = J.tid AND J.prefix = \'t_\' AND J.pid = P.ID AND P.ID = '.$post->ID;
		//stad //stadLink
		if ($ti = $wpdb->get_row($teaminfosql)) {
				$tid = $ti->teamid;

				if ($ti->t_roster) {
					$rosterlinksql = 'SELECT P.guid FROM '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE J.prefix = \'roster\' AND J.pid = P.ID AND J.tid = '.$tid;
					$rosterlink = $wpdb->get_var($rosterlinksql);
				}

				//Determine if a custom logo is present
				$filename = $_SERVER['DOCUMENT_ROOT']."/images/teams/".$ti->t_sname."_big.gif";
				if (file_exists($filename)) {
					$timg = "<img src=\"".home_url()."/images/teams/".$ti->t_sname."_big.gif\" alt=\"".$ti->t_sname." Logo\" />";
				}
				else {
					$timg = "<img src=\"".home_url()."/images/races/race".$ti->r_id.".gif\" alt=\"".$ti->r_name." Logo\" />";
				}
		}
?>
				<div class="bblm_details bblm_team_description">
					<?php the_content(); ?>
				</div>
<?php
			//Set default value to flag if the team has played a game or not
			$has_played = 1;

			$overallsql = "SELECT SUM(T.tc_played) AS OP, SUM(T.tc_W) AS OW, SUM(T.tc_L) AS OL, SUM(T.tc_D) AS OD, SUM(T.tc_tdfor) AS OTF, SUM(T.tc_tdagst) AS OTA, SUM(T.tc_comp) AS OC, SUM(T.tc_casfor) AS OCASF, SUM(T.tc_casagst) AS OCASA, SUM(T.tc_int) AS OINT, C.c_counts FROM ".$wpdb->prefix."team_comp T, ".$wpdb->prefix."comp C WHERE C.WPID = T.c_id AND T.tc_played > 0 AND T.t_id = ".$tid ." GROUP BY C.c_counts ORDER BY C.c_counts DESC";

			if ($ohs = $wpdb->get_results($overallsql)) {

				if ( !empty( $ohs ) ) { //Need something better - IE a result has been returned
?>
				<h3>Career Statistics for <?php the_title(); ?></h3>
				<table class="bblm_table">
					<tr>
						<th class="bblm_tbl_title">Team</th>
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
						<th class="bblm_tbl_stat">%</th>
					</tr>
<?php
 			foreach ($ohs as $oh) {
?>
					<tr>
						<td>
<?php
				if ( 1 == $oh->c_counts ) {
					echo 'League Record';
				}
				else {
					echo 'Exhibition Record';
				}
?>
						</td>
						<td><?php print($oh->OP); ?></td>
						<td><?php print($oh->OW); ?></td>
						<td><?php print($oh->OL); ?></td>
						<td><?php print($oh->OD); ?></td>
						<td><?php print($oh->OTF); ?></td>
						<td><?php print($oh->OTA); ?></td>
						<td><?php print($oh->OCASF); ?></td>
						<td><?php print($oh->OCASA); ?></td>
						<td><?php print($oh->OC); ?></td>
						<td><?php print($oh->OINT); ?></td>
						<td><?php if ($oh->OP > 0) {print(number_format(($oh->OW/$oh->OP)*100)); } else {print("N/A"); } ?></td>
					</tr>
<?php
 				}
?>
				</table>

				<h4>Key</h4>
				<ul class="bblm_expandablekey">
					<li><strong>P</strong>: Number of games Played</li>
					<li><strong>TF</strong>: Number of Touchdowns scored by the team</li>
					<li><strong>TA</strong>: Number of Touchdowns scored against the team</li>
					<li><strong>CF</strong>: Number of casulties caused by the team</li>
					<li><strong>CA</strong>: Number of casulties the team has suffered</li>
					<li><strong>%</strong>: Teams win percentage (including Draws)</li>
				</ul>

				<h3>Performance by Season</h3>
<?php
			$seasonsql = 'SELECT C.sea_id, SUM(T.tc_played) AS PLD, SUM(T.tc_W) AS win, SUM(T.tc_L) AS lose, SUM(T.tc_D) AS draw, SUM(T.tc_tdfor) AS TDf, SUM(T.tc_tdagst) AS TDa, SUM(T.tc_casfor) AS CASf, SUM(T.tc_casagst) AS CASa, SUM(T.tc_comp) AS COMP, SUM(T.tc_int) AS cINT FROM '.$wpdb->prefix.'team_comp T, '.$wpdb->prefix.'comp C WHERE T.c_id = C.WPID AND tc_played > 0 AND C.c_counts = 1 AND T.t_id = '.$tid.' GROUP BY C.sea_id ORDER BY C.sea_id DESC';
			if ( $seah = $wpdb->get_results( $seasonsql ) ) {
				$zebracount = 1;
?>
				<table class="bblm_table bblm_sortable">
					<thead>
						<tr>
							<th class="bblm_tbl_title"><?php echo __( 'Season', 'bblm'); ?></th>
							<th class="bblm_tbl_stat"><?php echo __( 'P', 'bblm'); ?></th>
							<th class="bblm_tbl_stat"><?php echo __( 'W', 'bblm'); ?></th>
							<th class="bblm_tbl_stat"><?php echo __( 'L', 'bblm'); ?></th>
							<th class="bblm_tbl_stat"><?php echo __( 'D', 'bblm'); ?></th>
							<th class="bblm_tbl_stat"><?php echo __( 'TF', 'bblm'); ?></th>
							<th class="bblm_tbl_stat"><?php echo __( 'TA', 'bblm'); ?></th>
							<th class="bblm_tbl_stat"><?php echo __( 'CF', 'bblm'); ?></th>
							<th class="bblm_tbl_stat"><?php echo __( 'CA', 'bblm'); ?></th>
							<th class="bblm_tbl_stat"><?php echo __( 'COMP', 'bblm'); ?></th>
							<th class="bblm_tbl_stat"><?php echo __( 'INT', 'bblm'); ?></th>
							<th class="bblm_tbl_stat"><?php echo __( '%', 'bblm'); ?></th>
						</tr>
					</thead>
					<tbody>
<?php
				foreach ( $seah as $sh ) {
					if ( $zebracount % 2 ) {
						echo '<tr>';
					}
					else {
						echo '<tr class="bblm_tbl_alt">';
					}
					echo '<td>' . bblm_get_season_link( $sh->sea_id ) . '</td>';
					echo '<td>' . $sh->PLD . '</td>';
					echo '<td>' . $sh->win . '</td>';
					echo '<td>' . $sh->lose . '</td>';
					echo '<td>' . $sh->draw . '</td>';
					echo '<td>' . $sh->TDf . '</td>';
					echo '<td>' . $sh->TDa . '</td>';
					echo '<td>' . $sh->CASf . '</td>';
					echo '<td>' . $sh->CASa . '</td>';
					echo '<td>' . $sh->COMP . '</td>';
					echo '<td>' . $sh->cINT . '</td>';

					if ( $sh->PLD >0 ) {
						echo '<td>' . number_format( ( $sh->win / $sh->PLD ) * 100 ) . '</td>';
					}
					else {
						echo '<td>' . __( 'N/A' , 'bblm' ) . '</td>';
					}
					echo '</tr>';

					$zebracount++;
				}
				echo '</tbody>';
				echo '</table>';
			}

?>
				<h3>Performance by Competition</h3>

<?php
			$matchhsql = 'SELECT C.WPID AS CWPID, SUM(T.tc_played) AS PLD, SUM(T.tc_W) AS win, SUM(T.tc_L) AS lose, SUM(T.tc_D) AS draw, SUM(T.tc_tdfor) AS TDf, SUM(T.tc_tdagst) AS TDa, SUM(T.tc_casfor) AS CASf, SUM(T.tc_casagst) AS CASa, SUM(T.tc_comp) AS COMP, SUM(T.tc_int) AS cINT FROM '.$wpdb->prefix.'team_comp T, '.$wpdb->prefix.'comp C WHERE T.c_id = C.WPID AND tc_played > 0 AND T.t_id = '.$tid.' GROUP BY C.c_id ORDER BY C.c_id DESC';

			if ($matchh = $wpdb->get_results($matchhsql)) {
				$zebracount = 1;
				print("	<table class=\"bblm_table bblm_sortable\">\n	<thead>\n		<tr>\n			<th class=\"bblm_tbl_title\">Competition</th>\n			<th class=\"bblm_tbl_stat\">P</th>\n			<th class=\"bblm_tbl_stat\">W</th>\n			<th class=\"bblm_tbl_stat\">L</th>\n			<th class=\"bblm_tbl_stat\">D</th>\n			<th class=\"bblm_tbl_stat\">TF</th>\n			<th class=\"bblm_tbl_stat\">TA</th>\n			<th class=\"bblm_tbl_stat\">CF</th>\n			<th class=\"bblm_tbl_stat\">CA</th>\n			<th class=\"bblm_tbl_stat\">COMP</th>\n			<th class=\"bblm_tbl_stat\">INT</th>\n			<th class=\"bblm_tbl_stat\">%</th>\n		</tr>\n	</thead>\n	<tbody>\n");

				foreach ($matchh as $mh) {
					if ($zebracount % 2) {
						print("		<tr>\n");
					}
					else {
						print("		<tr class=\"bblm_tbl_alt\">\n");
					}
					print("			<td>" . bblm_get_competition_link( $mh->CWPID ) . "</td>\n			<td>".$mh->PLD."</td>\n			<td>".$mh->win."</td>\n			<td>".$mh->lose."</td>\n			<td>".$mh->draw."</td>\n			<td>".$mh->TDf."</td>\n			<td>".$mh->TDa."</td>\n			<td>".$mh->CASf."</td>\n			<td>".$mh->CASa."</td>\n			<td>".$mh->COMP."</td>\n			<td>".$mh->cINT."</td>\n");
					if ($mh->PLD > 0) {
						print("			<td>".number_format(($mh->win/$mh->PLD)*100)."</td>\n");
					}
					else {
						print("			<td>N/A</td>\n");
					}
					print("		</tr>\n");

					$zebracount++;
				}
				print("</tbody>\n	</table>\n");
			}
?>



			<h3>Players</h3>
<?php

			//Initialise variables
			$teamcap = 0;

			//determine Team Captain
			$teamcaptainsql = 'SELECT * FROM '.$wpdb->prefix.'team_captain WHERE tcap_status = 1 and t_id = '.$tid;
			if ($tcap = $wpdb->get_row($teamcaptainsql)) {
				$teamcap = $tcap->p_id;
			}

			$playerssql = 'SELECT P.p_num, K.post_title, K.guid, L.pos_name, P.p_status, P.p_cost, P.p_cost_ng, P.p_id FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' K, '.$wpdb->prefix.'position L WHERE P.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = K.ID AND P.pos_id = L.pos_id AND P.t_id = '.$tid.' ORDER BY P.p_status DESC, P.p_num ASC';
			if ($player = $wpdb->get_results($playerssql)) {
				$is_first = 1;
				$current_status = "";

				foreach ($player as $pd) {
					if ($current_status !== $pd->p_status) {
						if (!TRUE == $is_first) {
							print("			</ul>\n");
						}
						if ($pd->p_status) {
							$status_text = "Active Players";
						}
						else {
							$status_text = "Former Players";
						}
						print("			<h4>".$status_text."</h4>\n			<ul>\n");
					}
					$current_status = $pd->p_status;
						print("				<li>#".$pd->p_num." - <a href=\"".$pd->guid."\" title=\"View more information about ".$pd->post_title."\">".$pd->post_title."</a>");
						if ($teamcap == $pd->p_id) {
							print(" <strong>(Captain)</strong>");
							//Assignes the Captain to a link for future use in the Sidebar!
							$teamcaplink = "<a href=\"".$pd->guid."\" title=\"View more information about ".$pd->post_title."\">".$pd->post_title."</a>";
						}
						print(" - " . esc_html( $pd->pos_name ) . " (".number_format($pd->p_cost)."gp)</li>\n");

					$is_first = 0;
				}
				print("			</ul>\n");

				/*		Star Player who have played for this team	*/
				//grab the ID of the "Star Player team
				$bblm_star_team = bblm_get_star_player_team();

				$starplayerssql = 'SELECT P.post_title, P.guid, COUNT(*) AS VISITS FROM '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P, '.$wpdb->prefix.'player X WHERE P.ID = J.pid AND J.prefix = "p_" AND J.tid = X.p_id AND M.p_id = X.p_id AND X.t_id = '.$bblm_star_team.' AND M.t_id = '.$tid.' GROUP BY M.p_id ORDER BY P.post_title ASC';
				if ($starplayers = $wpdb->get_results($starplayerssql)) {
					print("			<h4>Star Players hired</h4>\n			<ul>\n");
					foreach ($starplayers as $spv) {
						print("				<li><a href=\"".$spv->guid."\" title=\"View the details of this Star Player\">".$spv->post_title."</a>");
						if (1 < $spv->VISITS) {
							print(" (x".$spv->VISITS.")");
						}
						print("</li>\n");
					}
					print("			</ul>\n");
				}
				/*		End of Star Players	*/

				if ($ti->t_roster) {
									print("<p><a href=\"".$rosterlink."/\" title=\"View the teams full roster \">View Full Roster &gt;&gt;</a></p>");
				}

				//Transfers
				//We determine if the team has been involved in any transfers, if they have the function displays them
				$trans = new BBLM_CPT_Transfer;
				$trans->display_team_transfer_history();

			}
			else {
				print("<div class=\"bblm_info\">\n	<p>No players have been found for this team.</p>\n	</div>\n");
			}
		} //end of if a team has played a match


		//The next part is displayed regardless of if a team hs plyed  match or not (google code issue 18)
				$fixturesql = 'SELECT F.f_teamA, F.f_teamB, UNIX_TIMESTAMP(F.f_date) AS fdate, D.div_name, T.WPID AS tAid, Y.WPID AS tBid, C.WPID AS CWPID FROM '.$wpdb->prefix.'fixture F, '.$wpdb->prefix.'division D, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'team Y, '.$wpdb->prefix.'comp C WHERE (F.f_teamA = '.$tid.' OR F.f_teamB = '.$tid.') AND F.div_id = D.div_id AND F.f_teamA = T.t_id AND F.f_teamB = Y.t_id AND C.WPID = F.c_id AND F.f_complete = 0 ORDER BY f_date ASC LIMIT 0, 30 ';

			if ($fixtures = $wpdb->get_results($fixturesql)) {
				print("<h3>Upcoming Matches (Fixtures)</h3>\n\n");
				print("<table class=\"bblm_table bblm_expandable\">\n		 <tr>\n		   <th class=\"bblm_tbl_matchdate\">Date</th>\n		   <th class=\"bblm_tbl_matchname\">opponent</th>\n		   <th class=\"bblm_tbl_matchname\">Competition</th>\n		 </tr>\n");

				$is_first = 0;
				$current_div = "";
				$zebracount = 1;

				//grab the ID of the "tbd" team
				$bblm_tbd_team = bblm_get_tbd_team();


				foreach ($fixtures as $fd) {
					if (($zebracount % 2) && (10 < $zebracount)) {
						print("		 <tr class=\"bblm_tbl_hide\">\n");
					}
					else if (($zebracount % 2) && (10 >= $zebracount)) {
						print("		 <tr>\n");
					}
					else if (10 < $zebracount) {
						print("		 <tr class=\"bblm_tbl_alt bblm_tbl_hide\">\n");
					}
					else {
						print("		 <tr class=\"bblm_tbl_alt\">\n");
					}
					print("		 	<td>".date("d.m.y", $fd->fdate)."</td>\n		 	<td>\n");
					if ($tid == $fd->f_teamA) {
						if ($bblm_tbd_team == $fd->f_teamB) {

							echo __( 'To Be Determined', 'bblm');

						}
						else {

							$team_name = esc_html( get_the_title( $fd->tBid ) );
							$team_link = get_post_permalink( $fd->tBid );
							print("<a href=\"" . $team_link . "\" title=\"Learn more about " . $team_name . "\">" . $team_name . "</a>");

						}
					}
					else if ($tid == $fd->f_teamB) {
						if ($bblm_tbd_team == $fd->f_teamA) {

							echo __( 'To Be Determined', 'bblm');

						}
						else {

							$team_name = esc_html( get_the_title( $fd->tAid ) );
							$team_link = get_post_permalink( $fd->tAid );
							print("<a href=\"" . $team_link . "\" title=\"Learn more about " . $team_name . "\">" . $team_name . "</a>");

						}
					}
					print("</td>\n		 	<td>" . bblm_get_competition_link( $fd->CWPID ) . " (".$fd->div_name.")</td>\n			</tr>\n");
					$zebracount++;
				}
				print("</table>\n");
			} //end of if fixtures SQL

		if ($has_played) {
?>
				<h3>Recent Matches</h3>
<?php
				$matchssql = 'SELECT M.m_id, S.post_title AS Mtitle, S.guid AS Mlink, T.WPID AS tAid, R.WPID AS tBid, UNIX_TIMESTAMP(M.m_date) AS mdate, N.mt_winnings, N.mt_att, N.mt_tv, N.mt_comment, N.mt_result, M.m_teamA, M.m_teamB, M.m_teamAtd, M.m_teamBtd,';
				$matchssql .= ' M.m_teamAcas, M.m_teamBcas FROM '.$wpdb->prefix.'match_team N, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'team R, '.$wpdb->prefix.'bb2wp A, '.$wpdb->posts.' S, '.$wpdb->prefix.'comp C WHERE M.c_id = C.WPID';
				$matchssql .= ' AND N.m_id = M.m_id AND M.m_teamA = T.t_id AND M.m_teamB = R.t_id AND M.m_id = A.tid AND A.prefix = \'m_\' AND A.pid = S.ID AND N.t_id = '.$tid.' ORDER BY M.m_date DESC';

				if ($matchs = $wpdb->get_results($matchssql)) {
				$zebracount = 1;
				$alt = "FALSE";
					print("<table class=\"bblm_table bblm_sortable bblm_expandable\" id=\"bblm_recentmatches\">\n	<thead>\n		 <tr>\n		   <th>Date</th>\n		   <th class=\"bblm_tbl_matchname\">Opponent</th>\n		   <th class=\"bblm_tbl_stat\">TF</th>\n		   <th class=\"bblm_tbl_stat\">TA</th>\n		   <th class=\"bblm_tbl_stat\">CF</th>\n		   <th class=\"bblm_tbl_stat\">CA</th>\n		   <th>Fans</th>\n		   <th>TV</th>\n		   <th>Result</th>\n		 </tr>\n	</thead>\n	<tbody>\n");
					foreach ($matchs as $ms) {
						/*
						  This one is a little different, we check for zebra (as normal but if it is also over 10 then we need to add the hooks to collapse it.
						*/
						if (($zebracount % 2) && (10 < $zebracount)) {
							print("		<tr class=\"bblm_tbl_hide\">\n");
						}
						else if (($zebracount % 2) && (10 >= $zebracount)) {
							print("		<tr>\n");
						}
						else if (10 < $zebracount) {
							print("		<tr class=\"bblm_tbl_alt bblm_tbl_hide\">\n");
							$alt = TRUE;
						}
						else {
							print("		<tr class=\"bblm_tbl_alt\">\n");
							$alt = TRUE;
						}
						print("		   <td><a href=\"".$ms->Mlink."\" title=\"View full details of ".$ms->Mtitle."\">".date("d.m.y", $ms->mdate)."</a></td>\n		   <td class=\"bblm_tbl_matchop\">");

						if ($tid == $ms->m_teamA) {
							$team_name = esc_html( get_the_title( $ms->tBid ) );
							$team_link = get_post_permalink( $ms->tBid );
							print("<a href=\"" . $team_link . "\" title=\"View more details about " . $team_name . "\">" . $team_name . "</a></td>\n		   <td>".$ms->m_teamAtd."</td>\n		   <td>".$ms->m_teamBtd."</td>\n		   <td>".$ms->m_teamAcas."</td>\n		   <td>".$ms->m_teamBcas."</td>\n");
						}
						else if ($tid == $ms->m_teamB) {
							$team_name = esc_html( get_the_title( $ms->tAid ) );
							$team_link = get_post_permalink( $ms->tAid );
							print("<a href=\"" . $team_link . "\" title=\"View more details about " . $team_name . "\">" . $team_name . "</a></td>\n		   <td>".$ms->m_teamBtd."</td>\n		   <td>".$ms->m_teamAtd."</td>\n		   <td>".$ms->m_teamBcas."</td>\n		   <td>".$ms->m_teamAcas."</td>\n");
						}
						print("		   <td>".number_format($ms->mt_winnings)."</td>\n		   <td>".number_format($ms->mt_tv)."</td>\n		   <td>".$ms->mt_result."</td>\n		 </tr>\n");
						//printing of match comment
						print("		<tr id=\"mcomment-".$ms->m_id."\" class=\"mcomment");
						if ($alt) {
							print(" bblm_tbl_alt\">\n");
						}
						else {
							print("\">\n");
						}
						print("		   <td colspan=\"9\">".$ms->mt_comment."</td>\n		</tr>\n");

						$alt = FALSE;
						$zebracount++;
					}
					print("	</tbody>\n	</table>\n");
				}
?>


				<h3 id="awardsfull">Awards list in full</h3>
<?php
				//Initialise variables
				$ccfail = 0;
				$cafail = 0;
				$safail = 0;
				$has_cups = 0;
				$championshipssql = 'SELECT A.a_name, B.c_id AS CWPID FROM '.$wpdb->prefix.'awards A, '.$wpdb->prefix.'awards_team_comp B, '.$wpdb->prefix.'comp C WHERE A.a_id = B.a_id AND a_cup = 1 AND B.c_id = C.WPID AND B.t_id = '.$tid.' ORDER BY A.a_id ASC';
				if ($champs = $wpdb->get_results($championshipssql)) {
					$has_cups = 1;
					$zebracount = 1;
					print("<h4>Championships</h4>\n");
					print("<table class=\"bblm_table\">\n	<tr>\n		<th class=\"bblm_tbl_name\">Title</th>\n		<th class=\"bblm_tbl_name\">Competition</th>\n	</tr>\n");
					foreach ($champs as $cc) {
						if ($zebracount % 2) {
							print("		<tr>\n");
						}
						else {
							print("		<tr class=\"bblm_tbl_alt\">\n");
						}
						print("		<td>".$cc->a_name."</td>\n		<td>" . bblm_get_competition_link( $cc->CWPID ) . "</td>\n	</tr>\n");
						$zebracount++;
					}
					print("</table>\n");
				}
				else {
					$ccfail = 1;
				}

				$seasonsql = 'SELECT A.a_name, B.sea_id AS season, B.ats_value FROM '.$wpdb->prefix.'awards A, '.$wpdb->prefix.'awards_team_sea B WHERE A.a_id = B.a_id AND B.t_id = '.$tid.' ORDER BY A.a_id ASC';
				if ( $sawards = $wpdb->get_results( $seasonsql ) ) {
					$zebracount = 1;
?>
					<h4><?php echo __( 'Awards from Seasons', 'bblm'); ?></h4>
					<table class="bblm_table">
						<thead>
							<tr>
								<th class="bblm_tbl_name"><?php echo __( 'Award', 'bblm'); ?></th>
								<th class="bblm_tbl_name"><?php echo __( 'Competition', 'bblm'); ?></th>
								<th class="bblm_tbl_stat"><?php echo __( 'Value', 'bblm'); ?></th>
							</tr>
						</thead>
						<tbody>
<?php
					foreach ( $sawards as $sa ) {
						if ($zebracount % 2) {
							echo '<tr>';
						}
						else {
							echo '<tr class="bblm_tbl_alt">';
						}
						echo '<td>' . $sa->a_name . '</td>';
						echo '<td>' . bblm_get_season_link( $sa->season ) . '</td>';
						echo '<td>' . $sa->ats_value . '</td>';
						echo '</tr>';
						$zebracount++;
					}
					echo '</tbody>';
					echo '</table>';
				}
				else {
					$safail = 1;
				}

				$compawardssql = 'SELECT A.a_name, B.c_id AS CWPID, B.atc_value FROM '.$wpdb->prefix.'awards A, '.$wpdb->prefix.'awards_team_comp B, '.$wpdb->prefix.'comp C WHERE A.a_id = B.a_id AND a_cup = 0 AND B.c_id = C.WPID AND B.t_id = '.$tid.' ORDER BY A.a_id ASC';
				if ($cawards = $wpdb->get_results($compawardssql)) {
					$zebracount = 1;
					print("<h4>Awards from Competitions</h4>\n");
					print("<table class=\"bblm_table\">\n	<tr>\n		<th class=\"bblm_tbl_name\">Award</th>\n		<th class=\"bblm_tbl_name\">Competition</th>\n		<th class=\"bblm_tbl_stat\">Value</th>\n	</tr>\n");
					foreach ($cawards as $ca) {
						if ($zebracount % 2) {
							print("		<tr>\n");
						}
						else {
							print("		<tr class=\"bblm_tbl_alt\">\n");
						}
						print("		<td>".$ca->a_name."</td>\n		<td>" . bblm_get_competition_link( $ca->CWPID ) . "</td>\n		<td>".$ca->atc_value."</td>\n	</tr>\n");
						$zebracount++;
					}
					print("</table>\n");
				}
				else {
					$cafail = 1;
				}

				if ($cafail && $safail && $ccfail) {
					//no awards at all
					print("	<div class=\"bblm_info\">\n		<p>This team has not any awards as of yet.</p>\n	</div>\n");
				}


				}//end of count stats
			}//end of if plyed a match
			else {
				$has_played = 0;
				print("	<div class=\"bblm_info\">\n		<p>This Team has not yet made their debut!. Stay tuned to see how this team develops.</p>\n	</div>\n");
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
