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
	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
			<div class="entry">
				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<h2 class="entry-title"><?php the_title(); ?></h2>
<?php
		$teaminfosql = 'SELECT T.*, J.tid AS teamid, R.r_name, L.guid AS racelink, W.post_title AS stad, W.guid AS stadlink FROM '.$wpdb->prefix.'team T, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P, '.$wpdb->prefix.'race R, '.$wpdb->prefix.'bb2wp K, '.$wpdb->posts.' L, '.$wpdb->prefix.'bb2wp Q, '.$wpdb->posts.' W WHERE T.stad_id = Q.tid AND Q.prefix = \'stad_\' AND Q.pid = W.ID AND T.r_id = K.tid AND K.prefix = \'r_\' AND K.pid = L.ID AND R.r_id = T.r_id AND T.t_id = J.tid AND J.prefix = \'t_\' AND J.pid = P.ID AND P.ID = '.$post->ID;
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
				<div class="details team">
					<?php the_content('Read the rest of this entry &raquo;'); ?>
				</div>
<?php
			//Set default value to flag if the team has played a game or not
			$has_played = 1;

			$overallsql = "SELECT SUM(T.tc_played) AS OP, SUM(T.tc_W) AS OW, SUM(T.tc_L) AS OL, SUM(T.tc_D) AS OD, SUM(T.tc_tdfor) AS OTF, SUM(T.tc_tdagst) AS OTA, SUM(T.tc_comp) AS OC, SUM(T.tc_casfor) AS OCASF, SUM(T.tc_casagst) AS OCASA, SUM(T.tc_int) AS OINT FROM ".$wpdb->prefix."team_comp T, ".$wpdb->prefix."comp C WHERE C.c_counts = 1 AND C.c_id = T.c_id AND T.tc_played > 0 AND T.t_id = ".$tid;

			if ($oh = $wpdb->get_row($overallsql)) {
				if (NULL == $oh->OP) {
					$has_played = 0;
					print("	<div class=\"info\">\n		<p>This Team has not yet made their debut!. Stay tuned to see how this team develops.</p>\n	</div>\n");
				}
				else {
?>
				<h3>Career Statistics for <?php the_title(); ?></h3>
				<table>
					<tr>
						<th class="tbl_title">Team</th>
						<th class="tbl_stat">P</th>
						<th class="tbl_stat">W</th>
						<th class="tbl_stat">L</th>
						<th class="tbl_stat">D</th>
						<th class="tbl_stat">TF</th>
						<th class="tbl_stat">TA</th>
						<th class="tbl_stat">CF</th>
						<th class="tbl_stat">CA</th>
						<th class="tbl_stat">COMP</th>
						<th class="tbl_stat">INT</th>
						<th class="tbl_stat">%</th>
					</tr>
					<tr>
						<td><?php the_title(); ?></td>
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
				</table>

				<h4>Key</h4>
				<ul class="expandablekey">
					<li><strong>P</strong>: Number of games Played</li>
					<li><strong>TF</strong>: Number of Touchdowns scored by the team</li>
					<li><strong>TA</strong>: Number of Touchdowns scored against the team</li>
					<li><strong>CF</strong>: Number of casulties caused by the team</li>
					<li><strong>CA</strong>: Number of casulties the team has suffered</li>
					<li><strong>%</strong>: Teams win percentage (including Draws)</li>
				</ul>

				<h3>Performance by Season</h3>
<?php
			$seasonsql = 'SELECT P.post_title, P.guid, SUM(T.tc_played) AS PLD, SUM(T.tc_W) AS win, SUM(T.tc_L) AS lose, SUM(T.tc_D) AS draw, SUM(T.tc_tdfor) AS TDf, SUM(T.tc_tdagst) AS TDa, SUM(T.tc_casfor) AS CASf, SUM(T.tc_casagst) AS CASa, SUM(T.tc_comp) AS COMP, SUM(T.tc_int) AS cINT FROM '.$wpdb->prefix.'team_comp T, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P, '.$wpdb->prefix.'season S WHERE T.c_id = C.c_id AND C.sea_id = S.sea_id AND J.tid = C.sea_id AND J.prefix = \'sea_\' AND J.pid = P.ID AND tc_played > 0 AND C.c_counts = 1 AND C.c_show = 1 AND T.t_id = '.$tid.' GROUP BY C.sea_id ORDER BY S.sea_id DESC';
			if ($seah = $wpdb->get_results($seasonsql)) {
				$zebracount = 1;
				print("	<table class=\"sortable\">\n	<thead>\n		<tr>\n			<th class=\"tbl_title\">Season</th>\n			<th class=\"tbl_stat\">P</th>\n			<th class=\"tbl_stat\">W</th>\n			<th class=\"tbl_stat\">L</th>\n			<th class=\"tbl_stat\">D</th>\n			<th class=\"tbl_stat\">TF</th>\n			<th class=\"tbl_stat\">TA</th>\n			<th class=\"tbl_stat\">CF</th>\n			<th class=\"tbl_stat\">CA</th>\n			<th class=\"tbl_stat\">COMP</th>\n			<th class=\"tbl_stat\">INT</th>\n			<th class=\"tbl_stat\">%</th>\n		</tr>\n	</thead>\n	<tbody>\n");

				foreach ($seah as $sh) {
					if ($zebracount % 2) {
						print("		<tr>\n");
					}
					else {
						print("		<tr class=\"tbl_alt\">\n");
					}
					print("			<td><a href=\"".$sh->guid."\" title=\"View more info about ".$sh->post_title."\">".$sh->post_title."</a></td>\n			<td>".$sh->PLD."</td>\n			<td>".$sh->win."</td>\n			<td>".$sh->lose."</td>\n			<td>".$sh->draw."</td>\n			<td>".$sh->TDf."</td>\n			<td>".$sh->TDa."</td>\n			<td>".$sh->CASf."</td>\n			<td>".$sh->CASa."</td>\n			<td>".$sh->COMP."</td>\n			<td>".$sh->cINT."</td>\n			");
					if ($sh->PLD >0) {
						print("<td>".number_format(($sh->win/$sh->PLD)*100)."</td>\n");
					}
					else {
						print("<td>N/A</td>\n");
					}
					print("		</tr>\n");


					$zebracount++;
				}
				print("	</tbody>\n	</table>\n");
			}

?>
				<h3>Performance by Competition</h3>

<?php
			//$matchhsql = "SELECT P.guid, T.*, C.c_name FROM ".$wpdb->prefix."team_comp T, ".$wpdb->prefix."comp C, ".$wpdb->prefix."bb2wp J, ".$wpdb->posts." P WHERE T.c_id = C.c_id AND J.tid = C.c_id AND J.prefix = 'c_' AND J.pid = P.ID AND tc_played > 0 AND C.c_counts = 1 AND T.t_id =".$tid." GROUP BY C.c_id";
			$matchhsql = 'SELECT P.post_title, P.guid, SUM(T.tc_played) AS PLD, SUM(T.tc_W) AS win, SUM(T.tc_L) AS lose, SUM(T.tc_D) AS draw, SUM(T.tc_tdfor) AS TDf, SUM(T.tc_tdagst) AS TDa, SUM(T.tc_casfor) AS CASf, SUM(T.tc_casagst) AS CASa, SUM(T.tc_comp) AS COMP, SUM(T.tc_int) AS cINT FROM '.$wpdb->prefix.'team_comp T, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE T.c_id = C.c_id AND J.tid = C.c_id AND J.prefix = \'c_\' AND J.pid = P.ID AND tc_played > 0 AND C.c_counts = 1 AND C.c_show = 1 AND T.t_id = '.$tid.' GROUP BY C.c_id ORDER BY C.c_id DESC';

			if ($matchh = $wpdb->get_results($matchhsql)) {
				$zebracount = 1;
				print("	<table class=\"sortable\">\n	<thead>\n		<tr>\n			<th class=\"tbl_title\">Competition</th>\n			<th class=\"tbl_stat\">P</th>\n			<th class=\"tbl_stat\">W</th>\n			<th class=\"tbl_stat\">L</th>\n			<th class=\"tbl_stat\">D</th>\n			<th class=\"tbl_stat\">TF</th>\n			<th class=\"tbl_stat\">TA</th>\n			<th class=\"tbl_stat\">CF</th>\n			<th class=\"tbl_stat\">CA</th>\n			<th class=\"tbl_stat\">COMP</th>\n			<th class=\"tbl_stat\">INT</th>\n			<th class=\"tbl_stat\">%</th>\n		</tr>\n	</thead>\n	<tbody>\n");

				foreach ($matchh as $mh) {
					if ($zebracount % 2) {
						print("		<tr>\n");
					}
					else {
						print("		<tr class=\"tbl_alt\">\n");
					}
					print("			<td><a href=\"".$mh->guid."\" title=\"View more info about ".$mh->post_title."\">".$mh->post_title."</a></td>\n			<td>".$mh->PLD."</td>\n			<td>".$mh->win."</td>\n			<td>".$mh->lose."</td>\n			<td>".$mh->draw."</td>\n			<td>".$mh->TDf."</td>\n			<td>".$mh->TDa."</td>\n			<td>".$mh->CASf."</td>\n			<td>".$mh->CASa."</td>\n			<td>".$mh->COMP."</td>\n			<td>".$mh->cINT."</td>\n");
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
				$options = get_option('bblm_config');
				$bblm_star_team = htmlspecialchars($options['team_star'], ENT_QUOTES);

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
				print("<div class=\"info\">\n	<p>No players have been found for this team.</p>\n	</div>\n");
			}
		} //end of if a team has played a match


		//The next part is displayed regardless of if a team hs plyed  match or not (google code issue 18)
				$fixturesql = 'SELECT F.f_teamA, F.f_teamB, UNIX_TIMESTAMP(F.f_date) AS fdate, D.div_name, T.WPID AS tAid, Y.WPID AS tBid, P.post_title AS Comp, P.guid AS CompLink FROM '.$wpdb->prefix.'fixture F, '.$wpdb->prefix.'division D, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'team Y, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE C.c_id = J.tid AND J.prefix = \'c_\' AND J.pid = P.ID AND (F.f_teamA = '.$tid.' OR F.f_teamB = '.$tid.') AND F.div_id = D.div_id AND F.f_teamA = T.t_id AND F.f_teamB = Y.t_id AND C.c_id = F.c_id AND C.c_counts = 1 AND F.f_complete = 0 ORDER BY f_date ASC LIMIT 0, 30 ';

			if ($fixtures = $wpdb->get_results($fixturesql)) {
				print("<h3>Upcoming Matches (Fixtures)</h3>\n\n");
				print("<table class=\"expandable\">\n		 <tr>\n		   <th class=\"tbl_matchdate\">Date</th>\n		   <th class=\"tbl_matchname\">opponent</th>\n		   <th class=\"tbl_matchname\">Competition</th>\n		 </tr>\n");

				$is_first = 0;
				$current_div = "";
				$zebracount = 1;

				//grab the ID of the "tbd" team
				$options = get_option('bblm_config');
				$bblm_tbd_team = htmlspecialchars($options['team_tbd'], ENT_QUOTES);


				foreach ($fixtures as $fd) {
					if (($zebracount % 2) && (10 < $zebracount)) {
						print("		 <tr class=\"tb_hide\">\n");
					}
					else if (($zebracount % 2) && (10 >= $zebracount)) {
						print("		 <tr>\n");
					}
					else if (10 < $zebracount) {
						print("		 <tr class=\"tbl_alt tb_hide\">\n");
					}
					else {
						print("		 <tr class=\"tbl_alt\">\n");
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
					print("</td>\n		 	<td><a href=\"".$fd->CompLink."\" title=\"Read more about ".$fd->Comp."\">".$fd->Comp."</a> (".$fd->div_name.")</td>\n			</tr>\n");
					$zebracount++;
				}
				print("</table>\n");
			} //end of if fixtures SQL

		if ($has_played) {
?>
				<h3>Recent Matches</h3>
<?php
				$matchssql = 'SELECT M.m_id, S.post_title AS Mtitle, S.guid AS Mlink, T.WPID AS tAid, R.WPID AS tBid, UNIX_TIMESTAMP(M.m_date) AS mdate, N.mt_winnings, N.mt_att, N.mt_tv, N.mt_comment, N.mt_result, M.m_teamA, M.m_teamB, M.m_teamAtd, M.m_teamBtd,';
				$matchssql .= ' M.m_teamAcas, M.m_teamBcas FROM '.$wpdb->prefix.'match_team N, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'team R, '.$wpdb->prefix.'bb2wp A, '.$wpdb->posts.' S, '.$wpdb->prefix.'comp C WHERE M.c_id = C.c_id AND C.c_show = 1 AND C.c_counts = 1';
				$matchssql .= ' AND N.m_id = M.m_id AND M.m_teamA = T.t_id AND M.m_teamB = R.t_id AND M.m_id = A.tid AND A.prefix = \'m_\' AND A.pid = S.ID AND N.t_id = '.$tid.' ORDER BY M.m_date DESC';

				if ($matchs = $wpdb->get_results($matchssql)) {
				$zebracount = 1;
				$alt = "FALSE";
					print("<table class=\"sortable expandable\" id=\"recentmatches\">\n	<thead>\n		 <tr>\n		   <th>Date</th>\n		   <th class=\"tbl_matchname\">Opponent</th>\n		   <th class=\"tbl_stat\">TF</th>\n		   <th class=\"tbl_stat\">TA</th>\n		   <th class=\"tbl_stat\">CF</th>\n		   <th class=\"tbl_stat\">CA</th>\n		   <th>Fans</th>\n		   <th>TV</th>\n		   <th>Result</th>\n		 </tr>\n	</thead>\n	<tbody>\n");
					foreach ($matchs as $ms) {
						/*
						  This one is a little different, we check for zebra (as normal but if it is also over 10 then we need to add the hooks to collapse it.
						*/
						if (($zebracount % 2) && (10 < $zebracount)) {
							print("		<tr class=\"tb_hide\">\n");
						}
						else if (($zebracount % 2) && (10 >= $zebracount)) {
							print("		<tr>\n");
						}
						else if (10 < $zebracount) {
							print("		<tr class=\"tbl_alt tb_hide\">\n");
							$alt = TRUE;
						}
						else {
							print("		<tr class=\"tbl_alt\">\n");
							$alt = TRUE;
						}
						print("		   <td><a href=\"".$ms->Mlink."\" title=\"View full details of ".$ms->Mtitle."\">".date("d.m.y", $ms->mdate)."</a></td>\n		   <td class=\"tbl_matchop\">");

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
							print(" tbl_alt\">\n");
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
				$championshipssql = 'SELECT A.a_name, P.post_title, P.guid FROM '.$wpdb->prefix.'awards A, '.$wpdb->prefix.'awards_team_comp B, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE A.a_id = B.a_id AND a_cup = 1 AND B.c_id = C.c_id AND C.c_id = J.tid AND J.prefix = \'c_\' AND J.pid = P.ID AND B.t_id = '.$tid.' ORDER BY A.a_id ASC';
				if ($champs = $wpdb->get_results($championshipssql)) {
					$has_cups = 1;
					$zebracount = 1;
					print("<h4>Championships</h4>\n");
					print("<table>\n	<tr>\n		<th class=\"tbl_name\">Title</th>\n		<th class=\"tbl_name\">Competition</th>\n	</tr>\n");
					foreach ($champs as $cc) {
						if ($zebracount % 2) {
							print("		<tr>\n");
						}
						else {
							print("		<tr class=\"tbl_alt\">\n");
						}
						print("		<td>".$cc->a_name."</td>\n		<td><a href=\"".$cc->guid."\" title=\"View full details about ".$cc->post_title."\">".$cc->post_title."</a></td>\n	</tr>\n");
						$zebracount++;
					}
					print("</table>\n");
				}
				else {
					$ccfail = 1;
				}

				$seasonsql = 'SELECT A.a_name, P.post_title, P.guid, B.ats_value FROM '.$wpdb->prefix.'awards A, '.$wpdb->prefix.'awards_team_sea B, '.$wpdb->prefix.'season C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE A.a_id = B.a_id AND B.sea_id = C.sea_id AND C.sea_id = J.tid AND J.prefix = \'sea_\' AND J.pid = P.ID AND B.t_id = '.$tid.' ORDER BY A.a_id ASC';
				if ($sawards = $wpdb->get_results($seasonsql)) {
					$zebracount = 1;
					print("<h4>Awards from Seasons</h4>\n");
					print("<table>\n	<tr>\n		<th class=\"tbl_name\">Award</th>\n		<th class=\"tbl_name\">Competition</th>\n		<th class=\"tbl_stat\">Value</th>\n	</tr>\n");
					foreach ($sawards as $sa) {
						if ($zebracount % 2) {
							print("		<tr>\n");
						}
						else {
							print("		<tr class=\"tbl_alt\">\n");
						}
						print("		<td>".$sa->a_name."</td>\n		<td><a href=\"".$sa->guid."\" title=\"View full details about ".$sa->post_title."\">".$sa->post_title."</a></td>\n		<td>".$sa->ats_value."</td>\n	</tr>\n");
						$zebracount++;
					}
					print("</table>\n");
				}
				else {
					$safail = 1;
				}

				$compawardssql = 'SELECT A.a_name, P.post_title, P.guid, B.atc_value FROM '.$wpdb->prefix.'awards A, '.$wpdb->prefix.'awards_team_comp B, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE A.a_id = B.a_id AND a_cup = 0 AND B.c_id = C.c_id AND C.c_id = J.tid AND J.prefix = \'c_\' AND J.pid = P.ID AND B.t_id = '.$tid.' ORDER BY A.a_id ASC';
				if ($cawards = $wpdb->get_results($compawardssql)) {
					$zebracount = 1;
					print("<h4>Awards from Competitions</h4>\n");
					print("<table>\n	<tr>\n		<th class=\"tbl_name\">Award</th>\n		<th class=\"tbl_name\">Competition</th>\n		<th class=\"tbl_stat\">Value</th>\n	</tr>\n");
					foreach ($cawards as $ca) {
						if ($zebracount % 2) {
							print("		<tr>\n");
						}
						else {
							print("		<tr class=\"tbl_alt\">\n");
						}
						print("		<td>".$ca->a_name."</td>\n		<td><a href=\"".$ca->guid."\" title=\"View full details about ".$ca->post_title."\">".$ca->post_title."</a></td>\n		<td>".$ca->atc_value."</td>\n	</tr>\n");
						$zebracount++;
					}
					print("</table>\n");
				}
				else {
					$cafail = 1;
				}

				if ($cafail && $safail && $ccfail) {
					//no awards at all
					print("	<div class=\"info\">\n		<p>This team has not any awards as of yet.</p>\n	</div>\n");
				}


				}//end of count stats
			}//end of if plyed a match


?>

<p class="postmeta"><?php edit_post_link( __( 'Edit', 'oberwald' ), ' <strong>[</strong> ', ' <strong>]</strong> '); ?></p>

</div>
</div>


<?php endwhile;?>
<?php endif; ?>

<?php get_sidebar('content'); ?>

</div><!-- end of #maincontent -->
<?php
		//Gathering data for the sidebar
		//Current match form
		$formsql = 'SELECT R.mt_result FROM '.$wpdb->prefix.'match_team R, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE M.m_id = J.tid AND J.prefix = \'m_\' AND J.pid = P.ID AND M.c_id = C.c_id AND C.c_counts = 1 AND C.c_show = 1 AND M.m_id = R.m_id AND R.t_id = '.$tid.' ORDER BY m_date DESC LIMIT 5';
		$currentform = "";
		if ($form = $wpdb->get_results($formsql)) {
			foreach ($form as $tf) {
				$currentform .= $tf->mt_result;
			}
		}
		else {
				$currentform = "N/A";
		}


	//determine debut season
	if ( $has_played ) {
		$seasondebutsql = 'SELECT O.guid, O.post_title FROM '.$wpdb->prefix.'match_team T, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'season S, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' O WHERE S.sea_id = J.tid AND J.prefix = \'sea_\' AND J.pid = O.ID AND C.sea_id = S.sea_id AND C.c_id = M.c_id AND C.c_show = 1 AND C.c_counts = 1 AND M.m_id = T.m_id AND T.t_id = '.$tid.' ORDER BY M.m_date ASC LIMIT 1';
		$sd = $wpdb->get_row($seasondebutsql);
	}
?>

	<div id="subcontent">
		<ul>
			<li class="sidelogo"><?php print($timg); ?></li>
			<li class="sideinfo"><h2>Team Information</h2>
			  <ul>
			   <li><strong>Status:</strong> <?php if ($ti->t_active) { print("Active"); } else { print("Disbanded"); } ?></li>
			   <li><strong>Team Value:</strong> <?php print(number_format($ti->t_tv)); ?>gp</li>
			   <li><strong>Current Form:</strong> <?php print($currentform); ?></li>
			   <li><strong>Head Coach:</strong> <?php print($ti->t_hcoach); ?></li>
<?php
		if (isset($teamcaplink)) {
?>
			   <li><strong>Current Captain:</strong> <?php print($teamcaplink); ?></li>
<?php
		}
?>
			   <li><strong>Team Owner:</strong> <?php echo '<A href="'.get_post_permalink( $tid = $ti->ID ).'" title="Learn more about '.esc_html( get_the_title( $tid = $ti->ID ) ).'">'.esc_html( get_the_title( $tid = $ti->ID ) ).'</a>'; ?></li>
			   <li><strong>Stadium:</strong> <a href="<?php print($ti->stadlink); ?>" title="Learn more about <?php print($ti->stad); ?>"><?php print($ti->stad); ?></a></li>
<?php if ( $has_played ) { ?>
			   <li><strong>Debut:</strong> <a href="<?php print($sd->guid); ?>" title="Read more on <?php print($sd->post_title); ?>"><?php print($sd->post_title); ?></a></li>
<?php	} ?>
			   <li><strong>Race:</strong> <a href="<?php print($ti->racelink); ?>" title="Read more about <?php print($ti->r_name); ?> teams"><?php print($ti->r_name); ?></a></li>
			  </ul>
<?php
				if ($ti->t_roster) {
					print("			  <ul>\n			   <li><a href=\"".$rosterlink."/\" title=\"View the teams full roster \">View Full Roster &gt;&gt;</a></li>\n			  </ul>\n");
				}
?>
			</li>
<?php
			if ($has_played) {
?>
			<li class="sideawards"><h2>Championships</h2>
<?php
		if ($has_cups) {
			print("<ul>\n");
			foreach ($champs as $cc) {
				print("	<li><strong>".$cc->a_name."</strong> - <a href=\"".$cc->guid."\" title=\"View full details about ".$cc->post_title."\">".$cc->post_title."</a></li>\n");
			}
			print("</ul>\n");
		}
		else {
			print("<p>This team has not won any Championships at present.</p>\n");
		}
		print("<p><a href=\"#awardsfull\" title=\"View all awards this team has won\">View all awards this team has won &gt;&gt;</a></p>");
?>
			</li>

			<li><h2>Currently Participating in</h2>
<?php
			$currentcompssql = 'SELECT O.post_title, O.guid from '.$wpdb->prefix.'team T, '.$wpdb->prefix.'team_comp M, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' O WHERE C.c_id = J.tid AND J.prefix = \'c_\' AND J.pid = O.ID AND M.c_id = C.c_id AND C.c_counts = 1 AND C.c_show = 1 AND C.c_active = 1 AND T.t_id = M.t_id AND T.t_id = '.$tid.' GROUP BY C.c_id LIMIT 0, 30 ';
			if ($currentcomp = $wpdb->get_results($currentcompssql)) {
				print("				<ul>\n");
				foreach ($currentcomp as $curc) {
					print("					<li><a href=\"".$curc->guid."\" title=\"Read more about ".$curc->post_title."\">".$curc->post_title."</a></li>\n");
				}
				print("				</ul>\n");
			}
			else {
				print("<p>This team is currently not taking part in any Competitions.</p>\n");
			}
			print("			</li>\n");


			$topplayerssql = 'SELECT P.post_title, P.guid, T.p_spp FROM '.$wpdb->prefix.'player T, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE T.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = P.ID AND T.t_id = '.$tid.' ORDER BY T.p_spp DESC LIMIT 5';
			if ($topp = $wpdb->get_results($topplayerssql)) {
				print("<li>\n	<h2>Top Players on this team</h2>\n	<ul>\n");
				foreach ($topp as $tp) {
					print("	<li><a href=\"".$tp->guid."\" title=\"Read more about ".$tp->post_title."\">".$tp->post_title."</a> - ".$tp->p_spp."</li>\n");
				}
				print("</ul>\n</li>\n");
			}
		}//end of if $has_played


			$otherteamssql = 'SELECT P.post_title, P.guid FROM '.$wpdb->prefix.'team T, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE T.t_id = J.tid AND J.prefix = \'t_\' AND J.pid = P.ID AND T.t_show = 1 AND T.t_id != '.$tid.' AND T.type_id = 1 ORDER BY RAND() LIMIT 5';
			if ($oteam = $wpdb->get_results($otherteamssql)) {
				print("<li>\n	<h2>Other Teams in the League</h2>\n	<ul>\n");
				foreach ($oteam as $ot) {
					print("	<li><a href=\"".$ot->guid."\" title=\"Read more about ".$ot->post_title."\">".$ot->post_title."</a></li>\n");
				}
				print("</ul>\n</li>\n");
			}

			if ( !dynamic_sidebar('sidebar-common') ) : ?>
				<li><h2 class="widgettitle">Search</h2>
				  <ul>
				   <li><?php get_search_form(); ?></li>
				  </ul>
				</li>
			<?php endif; ?>

		</ul>
	</div><!-- end of #subcontent -->
<?php get_footer(); ?>
