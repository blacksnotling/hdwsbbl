<?php
/*
Template Name: View Competition
*/
/*
*	Filename: bb.view.comp.php
*	Description: Page template to view a competitions details.
*/
?>
<?php get_header(); ?>
	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
		<div id="breadcrumb">
			<p><a href="<?php echo get_option('home'); ?>" title="Back to the front of the BBBBL">BBBBL</a> &raquo; <a href="<?php echo get_option('home'); ?>/competitions/" title="Back to the Competition listing">Competitions</a> &raquo; <?php the_title(); ?></p>
		</div>
			<div class="entry">
				<h2><?php the_title(); ?></h2>
<?php
		//$compsql = "SELECT P.post_title, C.c_id, S.series_name, Y.sea_name, T.ct_name, C.ct_id, C.c_active, C.c_showstandings, UNIX_TIMESTAMP(C.c_sdate) AS sdate, UNIX_TIMESTAMP(C.c_edate) AS edate FROM '.$wpdb->prefix.'comp AS C, '.$wpdb->prefix.'bb2wp AS J, '.$wpdb->prefix.'series S, '.$wpdb->prefix.'season Y, '.$wpdb->prefix.'comp_type T, ".$wpdb->posts." P WHERE Y.sea_id = C.sea_id AND C.c_id = J.tid AND C.series_id = S.series_id AND C.ct_id = T.ct_id AND J.pid = P.ID AND P.ID =".$post->ID;
		$compsql = 'SELECT P.post_title, C.c_id, C.sea_id, C.series_id, I.post_title AS SERIES, I.guid AS SERIESLink, G.post_title AS SEASON, G.guid AS SEASONLink, T.ct_name, C.ct_id, C.c_active, C.c_showstandings, UNIX_TIMESTAMP(C.c_sdate) AS sdate, UNIX_TIMESTAMP(C.c_edate) AS edate FROM '.$wpdb->prefix.'comp AS C, '.$wpdb->prefix.'bb2wp AS J, '.$wpdb->prefix.'bb2wp Y, '.$wpdb->posts.' G, '.$wpdb->prefix.'comp_type T, '.$wpdb->posts.' P, '.$wpdb->prefix.'bb2wp U, '.$wpdb->posts.' I WHERE C.sea_id = Y.tid AND Y.prefix = \'sea_\' AND Y.pid = G.ID AND C.c_id = J.tid AND C.series_id = U.tid AND U.prefix = \'series_\' AND U.pid = I.ID AND C.ct_id = T.ct_id AND J.pid = P.ID AND P.ID = '.$post->ID;;
		if ($cd = $wpdb->get_row($compsql)) {

			$today = date("U");

			if (($cd->c_active) && ($cd->sdate > $today)) {
				print("	<div class=\"info\">\n		<p>This Competition is <strong>Upcoming</strong>. It is due to start on ".date("jS M Y", $cd->sdate).".</p>\n	</div>\n");
				$cstatus = "Upcoming";
				$cduration = "TBC";
			}
			else if ($cd->c_active) {
				print("	<div class=\"info\">\n		<p>This Competition is currently <strong>active</strong>. Stay tuned for further updates.</p>\n	</div>\n");
				$cstatus = "Active";
				$cduration = date("d.m.Y", $cd->sdate)." - Present";
			}
			else {
				$winnersql = 'SELECT P.post_title, P.guid FROM '.$wpdb->prefix.'awards_team_comp A, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE A.t_id = J.tid AND J.prefix = \'t_\' AND J.pid = P.ID AND A.a_id = 1 AND A.c_id = '.$cd->c_id.' LIMIT 1';
				$cw = $wpdb->get_row($winnersql);
				print("	<div class=\"info\">\n		<p>This Competition is now <strong>complete</strong>. The winners were <a href=\"".$cw->guid."\" title=\"View more on the winners\">".$cw->post_title."</a>.(<a href=\"#awardsfull\" title=\"See the rest of the awards assigned in this competition\">See more Awards</a>)</p>\n	</div>\n");
				$cstatus = "Complete";
				$cduration = date("d.m.Y", $cd->sdate)." - ".date("d.m.Y", $cd->edate);
			}
?>
				<div class="details comp">
					<?php the_content('Read the rest of this entry &raquo;'); ?>
				</div>

<?php
			//  Code Flow  //
			/*	-Check to see if standings are to be shown
				-If yes check the comp type and draw standings based on that!
				-If not then list teams
			*/
			if ($cd->c_showstandings) {
				print("<h3>Standings</h3>\n");
				//Check to see the type of the league
				if (3 == $cd->ct_id) {
					//We have a tournament
					//gather brackets from data base, they MUST be sorted by div, order. blanks must be present if there are any byes
					$bracketssql = 'SELECT C.cb_text, D.div_name FROM '.$wpdb->prefix.'comp_brackets C, '.$wpdb->prefix.'division D WHERE C.div_id = D.div_id AND C.c_id = '.$cd->c_id.' ORDER BY C.div_ID DESC, cb_order ASC';
					$brackets = $wpdb->get_results($bracketssql, ARRAY_N);
					//determine number of games (which determines the layout to be used
					$numgames = count($brackets);
					if (7 == $numgames) {
?>
				<table>
					<tr>
						<th><?php print($brackets[0][1]); ?></th>
						<th><?php print($brackets[4][1]); ?></th>
						<th><?php print($brackets[6][1]); ?></th>
					</tr>
					<tr>
						<td><?php print($brackets[0][0]); ?></td>
						<td rowspan="2"><?php print($brackets[4][0]); ?></td>
						<td rowspan="4"><?php print($brackets[6][0]); ?></td>
			      		</tr>
			      		<tr>
						<td><?php print($brackets[1][0]); ?></td>
					</tr>
					<tr>
						<td><?php print($brackets[2][0]); ?></td>
						<td rowspan="2"><?php print($brackets[5][0]); ?></td>
					</tr>
					<tr>
						<td><?php print($brackets[3][0]); ?></td>
					</tr>
				</table>
<?php
					} //end of if 7 games
					else if (3 == $numgames) {
?>
				<table>
					<tr>
						<th><?php print($brackets[0][1]); ?></th>
						<th><?php print($brackets[2][1]); ?></th>
					</tr>
					<tr>
						<td><?php print($brackets[0][0]); ?></td>
						<td rowspan="2"><?php print($brackets[2][0]); ?></td>
					</tr>
					<tr>
						<td><?php print($brackets[1][0]); ?></td>
					</tr>
				</table>
<?php
					} //end of else if 3 games
										else if (15 == $numgames) {
					?>
<!--									<table>
										<tr>
											<th><?php print($brackets[0][1]); ?></th>
											<th><?php print($brackets[2][1]); ?></th>
										</tr>
										<tr>
											<td><?php print($brackets[0][0]); ?></td>
											<td rowspan="2"><?php print($brackets[2][0]); ?></td>
										</tr>
										<tr>
											<td><?php print($brackets[1][0]); ?></td>
										</tr>
									</table>-->

				<table>
					<tr>
						<th><?php print($brackets[0][1]); ?></th>
						<th><?php print($brackets[8][1]); ?></th>
						<th><?php print($brackets[12][1]); ?></th>
						<th><?php print($brackets[14][1]); ?></th>
					</tr>
					<tr>
						<td><?php print($brackets[0][0]); ?></td>
						<td rowspan="2"><?php print($brackets[8][0]); ?></td>
						<td rowspan="4"><?php print($brackets[12][0]); ?></td>
						<td rowspan="8"><?php print($brackets[14][0]); ?></td>
					</tr>
					<tr>
						<td><?php print($brackets[1][0]); ?></td>
					</tr>
					<tr>
						<td><?php print($brackets[2][0]); ?></td>
						<td rowspan="2"><?php print($brackets[9][0]); ?></td>
					</tr>
					<tr>
						<td><?php print($brackets[3][0]); ?></td>
					</tr>
					<tr>
						<td><?php print($brackets[4][0]); ?></td>
						<td rowspan="2"><?php print($brackets[10][0]); ?></td>
						<td rowspan="4"><?php print($brackets[13][0]); ?></td>
					</tr>
					<tr>
						<td><?php print($brackets[5][0]); ?></td>
					</tr>
					<tr>
						<td><?php print($brackets[6][0]); ?></td>
						<td rowspan="2"><?php print($brackets[11][0]); ?></td>
					</tr>
					<tr>
						<td><?php print($brackets[7][0]); ?></td>
					</tr>
				</table>
<?php
					} //end of else if 15 games
					else {
						//something has gone wrong
						print("<p>something has gone wrong</p>");
					}



				} //end of if tourney
				else {
					//We have something other than a tournament. Begin normal printout
					//May need to split this in the future, depending on league requirements
					//$standingssql = 'SELECT P.post_title, P.guid, C.*, D.div_name, SUM(C.tc_tdfor-C.tc_tdagst) AS TDD, SUM(C.tc_casfor-C.tc_casagst) AS CASD FROM '.$wpdb->posts.' P, '.$wpdb->prefix.'bb2wp J, '.$wpdb->prefix.'team_comp C, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'division D WHERE T.t_show = 1 AND C.div_id = D.div_id AND T.t_id = C.t_id AND T.t_id = J.tid AND J.prefix = \'t_\' AND J.pid = P.ID AND C.c_id = '.$cd->c_id.' GROUP BY C.t_id ORDER BY D.div_id ASC, C.tc_points DESC, TDD DESC, CASD DESC';
					$standingssql = 'SELECT T.t_name, T.t_guid, C.*, D.div_name, D.div_id, SUM(C.tc_tdfor-C.tc_tdagst) AS TDD, SUM(C.tc_casfor-C.tc_casagst) AS CASD FROM '.$wpdb->prefix.'team_comp C, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'division D WHERE T.t_show = 1 AND C.div_id = D.div_id AND T.t_id = C.t_id AND C.c_id = '.$cd->c_id.' GROUP BY C.t_id ORDER BY D.div_id ASC, C.tc_points DESC, TDD DESC, CASD DESC, C.tc_tdfor DESC, C.tc_casfor DESC, T.t_name ASC';
					if ($standings = $wpdb->get_results($standingssql)) {
						$is_first_div = 1;
						$zebracount = 1;
						foreach ($standings as $stand) {
							//print the end of a table tag unless this was the first table
							if ($lastdiv !== $stand->div_name) {
								if (!TRUE == $is_first_div) {
									print("</table>\n");
									$zebracount = 1;
								}
								//cross division hardcode
								if (14 == $stand->div_id) {
									print("<h3>** New World Confrence (NWC) **</h3>");
								}
								if (16 == $stand->div_id) {
									print("<h3>** Old World Confrence (OWC) **</h3>");
								}
								//end cross division hard code
								print("<h4>".$stand->div_name."</h4>\n<table>\n <tr>\n  <th>Team</th>\n  <th>Pld</th>\n  <th>W</th>\n  <th>D</th>\n  <th>L</th>\n  <th>TF</th>\n  <th>TA</th>\n  <th>TD</th>\n  <th>CF</th>\n  <th>CA</th>\n  <th>CD</th>\n  <th>PTS</th>\n </tr>\n");
							}
							$lastdiv = $stand->div_name;
							if ($zebracount % 2) {
								print("					<tr>\n");
							}
							else {
								print("					<tr class=\"tbl_alt\">\n");
							}
							print("  <td><a href=\"".$stand->t_guid."\" title=\"View more information about ".$stand->t_name."\">".$stand->t_name."</a></td>\n <td>".$stand->tc_played."</td>\n  <td>".$stand->tc_W."</td>\n  <td>".$stand->tc_D."</td>\n  <td>".$stand->tc_L."</td>\n  <td>".$stand->tc_tdfor."</td>\n  <td>".$stand->tc_tdagst."</td>\n  <td>".$stand->TDD."</td>\n  <td>".$stand->tc_casfor."</td>\n  <td>".$stand->tc_casagst."</td>\n  <td>".$stand->CASD."</td>\n  <td><strong>".$stand->tc_points."</strong></td>\n	</tr>\n");

							//set flag so resulting </table> is printed
							$is_first_div = 0;
							$zebracount++;
						}
						print("</table>\n");
?>
				<h4>Key</h4>
				<ul class="expandablekey">
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
				print("<h3>Participents</h3>\n<p>Not all the participents for this Competition have been announced. So far the following teams have confirmed that they will be taking part:</p>");
				$participentssql = 'SELECT DISTINCT(P.post_title), P.guid FROM '.$wpdb->posts.' P, '.$wpdb->prefix.'bb2wp J, '.$wpdb->prefix.'team_comp C, '.$wpdb->prefix.'team T WHERE T.t_show = 1 AND T.t_id = C.t_id AND T.t_id = J.tid AND J.prefix = \'t_\' AND J.pid = P.ID AND C.c_id = '.$cd->c_id.' ORDER BY P.post_title ASC';
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
			$matchsql = 'SELECT UNIX_TIMESTAMP(M.m_date) AS mdate, M.m_gate, M.m_teamAtd, M.m_teamBtd, M.m_teamAcas, M.m_teamBcas, P.guid, P.post_title FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE M.c_id = '.$cd->c_id.' AND M.m_id = J.tid AND J.prefix = \'m_\' AND J.pid = P.ID ORDER BY M.m_date DESC';
			if ($match = $wpdb->get_results($matchsql)) {
				//We have matches so we can proceed

				$biggestattendcesql = 'SELECT UNIX_TIMESTAMP(M.m_date) AS MDATE, M.m_gate AS VALUE, P.post_title AS MATCHT, P.guid AS MATCHLink FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE M.m_id = J.tid AND J.prefix = \'m_\' AND J.pid = P.ID AND M.c_id = C.c_id AND C.c_show = 1 AND C.type_id = 1 AND C.c_counts = 1 AND (M.div_id = 1 OR M.div_id = 2 OR M.div_id = 3) AND C.c_id = '.$cd->c_id.' ORDER BY M.m_gate DESC, MDATE ASC LIMIT 1';
				$biggestattendcenonfinalsql = 'SELECT UNIX_TIMESTAMP(M.m_date) AS MDATE, M.m_gate AS VALUE, P.post_title AS MATCHT, P.guid AS MATCHLink FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE M.m_id = J.tid AND J.prefix = \'m_\' AND J.pid = P.ID AND M.c_id = C.c_id AND C.c_show = 1 AND C.type_id = 1 AND C.c_counts = 1 AND M.div_id != 1 AND M.div_id != 2 AND M.div_id != 3 AND C.c_id = '.$cd->c_id.' ORDER BY M.m_gate DESC, MDATE ASC LIMIT 1';
?>
					<ul>
<?php
					if ($bcn = $wpdb->get_row($biggestattendcenonfinalsql)) {
?>
						<li>The Highest recorded attendance (not a Final or Semi-Final) is <strong><?php print(number_format($bcn->VALUE)); ?> fans</strong> in the match between <strong><?php print($bcn->MATCHT); ?></strong> on <?php print(date("d.m.25y", $bcn->MDATE)); ?></li>
<?php
					}
					if ($bc = $wpdb->get_row($biggestattendcesql)) {
?>
						<li>The Highest recorded attendance (Final or Semi-Final) is <strong><?php print(number_format($bc->VALUE)); ?> fans</strong> in the match between <strong><?php print($bc->MATCHT); ?></strong> on <?php print(date("d.m.25y", $bc->MDATE)); ?></li>
<?php
					}
?>

					</ul>
<?php
				print("<h3>Matches</h3>\n");
				print("<table class=\"expandable\">\n	<thead>\n		 <tr>\n		   <th class=\"tbl_matchdate\">Date</th>\n		   <th class=\"tbl_matchname\">Match</th>\n		   <th class=\"tbl_matchresult\">Result</th>\n		   <th class=\"tbl_matchgate\">Gate</th>\n		 </tr>\n	</thead>\n	<tbody>");
				$zebracount = 1;
				foreach ($match as $md) {
					if (($zebracount % 2) && (10 < $zebracount)) {
						print("		<tr class=\"tb_hide\">\n");
					}
					else if (($zebracount % 2) && (10 >= $zebracount)) {
						print("		<tr>\n");
					}
					else if (10 < $zebracount) {
						print("		<tr class=\"tbl_alt tb_hide\">\n");
					}
					else {
						print("		<tr class=\"tbl_alt\">\n");
					}
					print("		   <td>".date("d.m.y", $md->mdate)."</td>\n		   <td><a href=\"".$md->guid."\" title=\"View the details of the match\">".$md->post_title."</a></td>\n		   <td>".$md->m_teamAtd." - ".$md->m_teamBtd." (".$md->m_teamAcas." - ".$md->m_teamBcas.")</td>\n		   <td><em>".number_format($md->m_gate)."</em></td>\n		 </tr>\n");
					$zebracount++;
				}
				print("	</tbody>\n	</table>\n");
				//set a flag so we know that a game has been played (therefore it has begun, list stats etc).
				$match_present = 1;
			} //end of if match SQL
			else {
				//There are no matches to display
				print("	<div class=\"info\">\n		<p>No Matches have taken place in this competition yet. Stay tuned for further updates.</p>	</div>.\n");
			} //end of matches

			  //////////////
			 // Fixtures //
			//////////////
			$fixturesql = 'SELECT UNIX_TIMESTAMP(F.f_date) AS fdate, D.div_name, T.t_id AS TA, M.t_id AS TB, V.post_title AS TAname, O.post_title AS TBname, V.guid AS TAlink, O.guid AS TBlink FROM '.$wpdb->prefix.'fixture F, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'bb2wp U, '.$wpdb->posts.' V, '.$wpdb->prefix.'team M, '.$wpdb->prefix.'bb2wp N, '.$wpdb->posts.' O, '.$wpdb->prefix.'division D WHERE D.div_id = F.div_id AND T.t_id = F.f_teamA AND M.t_id = F.f_teamB AND T.t_id = U.tid AND U.prefix = \'t_\' AND U.pid = V.ID AND M.t_id = N.tid AND N.prefix = \'t_\' AND N.pid = O.ID AND F.f_complete = 0 AND F.c_id = '.$cd->c_id.' ORDER BY F.f_date ASC, F.div_id DESC';
			if ($fixtures = $wpdb->get_results($fixturesql)) {
				print("<h3>Upcoming Fixtures</h3>\n");
				print("<table class=\"expandable\">\n		 <tr>\n		   <th class=\"tbl_matchdate\">Date</th>\n		   <th class=\"tbl_matchname\">Match</th>\n		 </tr>\n");

				$is_first = 0;
				$current_div = "";
				$zebracount = 1;

				//grab the ID of the "tbd" team
				$options = get_option('bblm_config');
				$bblm_tbd_team = htmlspecialchars($options['team_tbd'], ENT_QUOTES);

				//print("  <table>\n		 <tr>\n		   <th>Date</th>\n		   <th>Match</th>\n		   </tr>\n");



				foreach ($fixtures as $fd) {
/*					if ($fd->div_name !== $current_div) {
						$current_div = $fd->div_name;
						if (1 !== $is_first) {
							print(" </table>\n");
						}
						$is_first = 1;
					}
					if ($is_first) {
						print("<h4>".$fd->div_name."</h4>\n  <table>\n		 <tr>\n		   <th class=\"tbl_matchdate\">Date</th>\n		   <th class=\"tbl_matchname\">Match</th>\n		   </tr>\n");
						$is_first_div = 0;
					}*/
					if (($zebracount % 2) && (10 < $zebracount)) {
						print("		<tr class=\"tb_hide\">\n");
					}
					else if (($zebracount % 2) && (10 >= $zebracount)) {
						print("		<tr>\n");
					}
					else if (10 < $zebracount) {
						print("		<tr class=\"tbl_alt tb_hide\">\n");
					}
					else {
						print("		<tr class=\"tbl_alt\">\n");
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
				print("<h3>Team Statistics</h3>\n");
				//$teamstatssql = 'SELECT P.post_title, P.guid, COUNT(*) AS PLAYED, T.tc_W, T.tc_L, T.tc_D, SUM(C.mt_td) AS TD, SUM(C.mt_cas) AS CAS, SUM(C.mt_int) AS MINT, SUM(C.mt_comp) AS COMP FROM '.$wpdb->prefix.'match_team C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'team_comp T WHERE C.t_id = T.t_id AND C.m_id = M.m_id AND C.t_id = J.tid AND J.prefix = \'t_\' AND J.pid = P.ID AND M.c_id = '.$cd->c_id.' AND T.c_id = '.$cd->c_id.' GROUP BY T.t_id ORDER BY P.post_title ASC LIMIT 0, 30 ';
				//$teamstatssql = 'SELECT P.post_title, P.guid, SUM(T.tc_played) AS TP, SUM(T.tc_W) AS TW, SUM(T.tc_L) AS TL, SUM(T.tc_D) AS TD, SUM(T.tc_tdfor) AS TDF, SUM(T.tc_tdagst) AS TDA, SUM(T.tc_casfor) AS TCF, SUM(T.tc_casagst) AS TCA, SUM(T.tc_INT) AS TI, SUM(T.tc_comp) AS TC, P.guid FROM '.$wpdb->prefix.'team_comp T, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P, '.$wpdb->prefix.'team Z WHERE Z.t_id = T.t_id AND Z.t_show = 1 AND T.t_id = J.tid AND J.prefix = \'t_\' AND J.pid = P.ID AND T.c_id = '.$cd->c_id.' GROUP BY T.t_id ORDER BY P.post_title ASC';
				$teamstatssql = 'SELECT Z.t_name, Z.t_guid, SUM(T.tc_played) AS TP, SUM(T.tc_W) AS TW, SUM(T.tc_L) AS TL, SUM(T.tc_D) AS TD, SUM(T.tc_tdfor) AS TDF, SUM(T.tc_tdagst) AS TDA, SUM(T.tc_casfor) AS TCF, SUM(T.tc_casagst) AS TCA, SUM(T.tc_INT) AS TI, SUM(T.tc_comp) AS TC FROM '.$wpdb->prefix.'team_comp T, '.$wpdb->prefix.'team Z WHERE Z.t_id = T.t_id AND Z.t_show = 1 AND T.c_id = '.$cd->c_id.' GROUP BY T.t_id ORDER BY Z.t_name ASC';
				if ($teamstats = $wpdb->get_results($teamstatssql)) {
					$zebracount = 1;
?>
				<table class="sortable">
					<thead>
					<tr>
						<th class="tbl_name">Team</th>
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
						<th class="tbl_stat">Win %</th>
					</tr>
					</thead>
					<tbody>
<?php
					foreach ($teamstats as $ps) {
						if ($zebracount % 2) {
							print("					<tr>\n");
						}
						else {
							print("					<tr class=\"tbl_alt\">\n");
						}
?>
						<td><a href="<?php print($ps->t_guid); ?>" title="Read more on <?php print($ps->t_name); ?>"><?php print($ps->t_name); ?></a></td>
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
					print("	</tbody>\n</table>\n");
				} //end of if team-stats SQL
				  ///////////
				 // Player //
				///////////
				print("<h3>Player Statistics</h3>\n");

				$options = get_option('bblm_config');
				$stat_limit = htmlspecialchars($options['display_stats'], ENT_QUOTES);
				$bblm_star_team = htmlspecialchars($options['team_star'], ENT_QUOTES);

					  ///////////////////////////
					 // Start of Player Stats //
					///////////////////////////

				//Generates an array containing all the Stats that are going to be checked
				$playerstatsarray = array();
				$playerstatsarray[0][item] = "mp_spp";
				$playerstatsarray[0][title] = "Best Players";
				$playerstatsarray[0][error] = "The Best Player list is not available at the moment";
				$playerstatsarray[1][item] = "mp_td";
				$playerstatsarray[1][title] = "Top Scorers";
				$playerstatsarray[1][error] = "The Touch Downs have been made yet!";
				$playerstatsarray[2][item] = "mp_cas";
				$playerstatsarray[2][title] = "Most Vicious";
				$playerstatsarray[2][error] = "No casualties have been caused yet";
				$playerstatsarray[3][item] = "mp_comp";
				$playerstatsarray[3][title] = "Top Passers";
				$playerstatsarray[3][error] = "No Completions have been recorded yet";
				$playerstatsarray[4][item] = "mp_int";
				$playerstatsarray[4][title] = "Top Interceptors";
				$playerstatsarray[4][error] = "No Inteceptions have been recorded yet";
				$playerstatsarray[5][item] = "mp_mvp";
				$playerstatsarray[5][title] = "Most Valuable Players (MVP)";
				$playerstatsarray[5][error] = "The Most Valuable Players list is not available at the moment";

				//For each of the stats, print the top players list. If none are found, display the relevant error
				foreach ($playerstatsarray as $tpa) {
					//Generic SQL Call, populated with the stat we are looking for
					$statsql = 'SELECT Y.post_title, T.t_name AS TEAM, T.t_guid AS TEAMLink, Y.guid, SUM(M.'.$tpa[item].') AS VALUE, R.pos_name FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'match X, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' Y, '.$wpdb->prefix.'position R WHERE P.pos_id = R.pos_id AND P.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = Y.ID AND M.m_id = X.m_id AND M.p_id = P.p_id AND P.t_id = T.t_id AND M.'.$tpa[item].' > 0 AND X.c_id = '.$cd->c_id.' AND T.t_id != '.$bblm_star_team.' GROUP BY P.p_id ORDER BY VALUE DESC LIMIT '.$stat_limit;
					print("<h4>".$tpa[title]."</h4>\n");
					if ($topstats = $wpdb->get_results($statsql)) {
						print("<table class=\"expandable\">\n	<tr>\n		<th class=\"tbl_stat\">#</th>\n		<th class=\"tbl_name\">Player</th>\n		<th>Position</th>\n		<th class=\"tbl_name\">Team</th>\n		<th class=\"tbl_stat\">Value</th>\n		</tr>\n");
						$zebracount = 1;
						$prevvalue = 0;

						foreach ($topstats as $ts) {
							if (($zebracount % 2) && (10 < $zebracount)) {
								print("	<tr class=\"tb_hide\">\n");
							}
							else if (($zebracount % 2) && (10 >= $zebracount)) {
								print("	<tr>\n");
							}
							else if (10 < $zebracount) {
								print("	<tr class=\"tbl_alt tb_hide\">\n");
							}
							else {
								print("	<tr class=\"tbl_alt\">\n");
							}
							if ($ts->VALUE > 0) {
								if ($prevvalue == $ts->VALUE) {
									print("	<td>-</td>\n");
								}
								else {
									print("	<td><strong>".$zebracount."</strong></td>\n");
								}
								print("	<td><a href=\"".$ts->guid."\" title=\"View more details on ".$ts->post_title."\">".$ts->post_title."</a></td>\n	<td>".$ts->pos_name."</td>\n	<td><a href=\"".$ts->TEAMLink."\" title=\"Read more on this team\">".$ts->TEAM."</a></td>\n	<td>".$ts->VALUE."</td>\n	</tr>\n");
								$prevvalue = $ts->VALUE;
							}
							$zebracount++;
						}
						print("</table>\n");
					}
					else {
						print("	<div class=\"info\">\n		<p>".$tpa[error]."</p>\n	</div>\n");
					}
				}

				//==================
				// -- Top Killer --
				//==================
					$statsql = 'SELECT O.post_title, O.guid, COUNT(*) AS VALUE , E.pos_name, T.t_name AS TEAM, T.t_guid AS TeamLink FROM `'.$wpdb->prefix.'player_fate` F, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' O, '.$wpdb->prefix.'position E, '.$wpdb->prefix.'team T WHERE P.t_id = T.t_id AND P.pos_id = E.pos_id AND P.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = O.ID AND (F.f_id = 1 OR F.f_id = 6) AND P.p_id = F.pf_killer AND F.m_id = M.m_id AND M.c_id = '.$cd->c_id.' AND T.t_id != '.$bblm_star_team.' GROUP BY F.pf_killer ORDER BY VALUE DESC LIMIT '.$stat_limit;
					print("<h4>Top Killers</h4>\n");
					if ($topstats = $wpdb->get_results($statsql)) {
						print("<table class=\"expandable\">\n	<tr>\n		<th class=\"tbl_stat\">#</th>\n		<th class=\"tbl_name\">Player</th>\n		<th>Position</th>\n		<th class=\"tbl_name\">Team</th>\n		<th class=\"tbl_stat\">Value</th>\n		</tr>\n");
						$zebracount = 1;
						$prevvalue = 0;

						foreach ($topstats as $ts) {
							if (($zebracount % 2) && (10 < $zebracount)) {
								print("	<tr class=\"tb_hide\">\n");
							}
							else if (($zebracount % 2) && (10 >= $zebracount)) {
								print("	<tr>\n");
							}
							else if (10 < $zebracount) {
								print("	<tr class=\"tbl_alt tb_hide\">\n");
							}
							else {
								print("	<tr class=\"tbl_alt\">\n");
							}
							if ($ts->VALUE > 0) {
								if ($prevvalue == $ts->VALUE) {
									print("	<td>-</td>\n");
								}
								else {
									print("	<td><strong>".$zebracount."</strong></td>\n");
								}
								print("	<td><a href=\"".$ts->guid."\" title=\"View more details on ".$ts->post_title."\">".$ts->post_title."</a></td>\n	<td>".$ts->pos_name."</td>\n	<td><a href=\"".$ts->TEAMLink."\" title=\"Read more on this team\">".$ts->TEAM."</a></td>\n	<td>".$ts->VALUE."</td>\n	</tr>\n");
								$prevvalue = $ts->VALUE;
							}
							$zebracount++;
						}
						print("</table>\n");
					}
					else {
						print("	<div class=\"info\">\n		<p>Nobody has killed anybody else!</p>\n	</div>\n");
					}

					  /////////////////////////
					 // End of Player Stats //
					/////////////////////////

					//Awards
					if (0 == $cd->c_active) {
						//the comp is over, display the awards!
?>
					<h3 id="awardsfull">Awards</h3>
					<h4>Main Awards</h4>
					<table>
						<tr>
							<th class="tbl_name">Award</th>
							<th class="tbl_name">Team</th>
						</tr>
<?php
					$compmajorawardssql = 'SELECT A.a_name, P.post_title, P.guid FROM '.$wpdb->prefix.'awards A, '.$wpdb->prefix.'awards_team_comp B, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE A.a_id = B.a_id AND a_cup = 1 AND B.t_id = J.tid AND J.prefix = \'t_\' AND J.pid = P.ID AND B.c_id = '.$cd->c_id.' ORDER BY A.a_id ASC';
					if ($cmawards = $wpdb->get_results($compmajorawardssql)) {
						$zebracount = 1;
						foreach ($cmawards as $cma) {
							if ($zebracount % 2) {
								print("						<tr>\n");
							}
							else {
								print("						<tr class=\"tbl_alt\">\n");
							}
								print("		<td>".$cma->a_name."</td>\n		<td><a href=\"".$cma->guid."\" title=\"Read more about ".$cma->post_title."\">".$cma->post_title."</a></td>\n	</tr>\n");
							$zebracount++;
						}
					}
?>
					</table>
					<h4>Awards assigned to Teams</h4>
					<table>
						<tr>
							<th class="tbl_name">Award</th>
							<th class="tbl_name">Team</th>
							<th class="tbl_stat">Value</th>
						</tr>
<?php
					$compteamawardssql = 'SELECT A.a_name, P.post_title, P.guid, B.atc_value AS value FROM '.$wpdb->prefix.'awards A, '.$wpdb->prefix.'awards_team_comp B, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE A.a_id = B.a_id AND a_cup = 0 AND B.t_id = J.tid AND J.prefix = \'t_\' AND J.pid = P.ID AND B.c_id = '.$cd->c_id.' ORDER BY A.a_id ASC';
					if ($ctawards = $wpdb->get_results($compteamawardssql)) {
						$zebracount = 1;
						foreach ($ctawards as $cta) {
							if ($zebracount % 2) {
								print("						<tr>\n");
							}
							else {
								print("						<tr class=\"tbl_alt\">\n");
							}
								print("		<td>".$cta->a_name."</td>\n		<td><a href=\"".$cta->guid."\" title=\"Read more about ".$cta->post_title."\">".$cta->post_title."</a></td>\n		<td>".$cta->value."</td>\n	</tr>\n");
							$zebracount++;
						}
					}
?>
					</table>
					<h4>Awards assigned to Players</h4>
					<table>
						<tr>
							<th class="tbl_name">Award</th>
							<th class="tbl_name">Player</th>
							<th class="tbl_name">Team</th>
							<th class="tbl_stat">Value</th>
						</tr>
<?php
					$compplayerawardssql = 'SELECT A.a_name, P.post_title AS Pname, P.guid AS Plink, B.apc_value AS value, F.post_title AS Tname, F.guid AS Tlink FROM '.$wpdb->prefix.'awards A, '.$wpdb->prefix.'awards_player_comp B, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P, '.$wpdb->prefix.'player R, '.$wpdb->prefix.'bb2wp D, '.$wpdb->posts.' F WHERE R.t_id = D.tid AND D.prefix = \'t_\' AND D.pid = F.ID AND R.p_id = B.p_id AND A.a_id = B.a_id AND a_cup = 0 AND B.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = P.ID AND B.c_id = '.$cd->c_id.' ORDER BY A.a_id ASC';
					if ($cpawards = $wpdb->get_results($compplayerawardssql)) {
						$zebracount = 1;
						foreach ($cpawards as $cpa) {
							if ($zebracount % 2) {
								print("						<tr>\n");
							}
							else {
								print("						<tr class=\"tbl_alt\">\n");
							}
								print("		<td>".$cpa->a_name."</td>\n		<td><a href=\"".$cpa->Plink."\" title=\"Read more about ".$cpa->Pname."\">".$cpa->Pname."</a></td>\n		<td><a href=\"".$cpa->Tlink."\" title=\"Read more about ".$cpa->Tname."\">".$cpa->Tname."</a></td>\n		<td>".$cpa->value."</td>\n	</tr>\n");
							$zebracount++;
						}
					}
?>
					</table>
<?

					}

			}//end of if_matches (for stats)




		} //end of if sql

		//Did You Know Display Code
		if (function_exists(bblm_display_dyk)) {
			bblm_display_dyk();
		}
?>
				<p class="postmeta"><?php edit_post_link('Edit', ' <strong>[</strong> ', ' <strong>]</strong> '); ?></p>

			</div>

		<?php endwhile;?>
	<?php endif; ?>

</div><!-- end of #maincontent -->
<?php
		//Gathering data for the sidebar
		//number of teams
		$teamnosql = 'SELECT COUNT( DISTINCT P.t_id ) AS value FROM '.$wpdb->prefix.'team_comp P WHERE P.c_id = '.$cd->c_id.' GROUP BY P.c_id';
		$tno = $wpdb->get_var($teamnosql);

		//comps this season
		$comptseasql = 'SELECT C.c_id, P.post_title, P.guid FROM '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE C.c_id = J.tid AND J.prefix = \'c_\' AND J.pid = P.ID AND C.type_id = 1 AND C.sea_id = '.$cd->sea_id.' AND C.c_id != '.$cd->c_id;

		//comps for this cup
		$comptcupsql = 'SELECT C.c_id, P.post_title, P.guid FROM '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE C.c_id = J.tid AND J.prefix = \'c_\' AND J.pid = P.ID AND C.series_id = '.$cd->series_id.' AND C.c_id != '.$cd->c_id;
?>

	<div id="subcontent">
		<ul>
			<li class="sideinfo"><h2>Comp Information</h2>
			  <ul>
			   <li><strong>Status:</strong> <?php print($cstatus) ?></li>
			   <li><strong>Duration:</strong> <?php print($cduration) ?></li>
			   <li><strong>Format:</strong> <?php print($cd->ct_name) ?></li>
			   <li><strong>Cup:</strong> <a href="<?php print($cd->SERIESLink) ?>" title="Read more about this Cup"><?php print($cd->SERIES) ?></a></li>
			   <li><strong>Season:</strong> <a href="<?php print($cd->SEASONLink) ?>" title="Read more about this Season"><?php print($cd->SEASON) ?></a></li>
			   <li><strong>Number of teams:</strong> <?php print($tno) ?></li>
			  </ul>
			 </li>
			 <li><h2>Other Competitions this Season</h2>
<?php
			if ($comptsea = $wpdb->get_results($comptseasql)) {
				print("					<ul>\n");
					foreach ($comptsea as $csea) {
						print("						<li><a href=\"".$csea->guid."\" title=\"View more on this Competition\">".$csea->post_title."</a></li>");
					}
				print("					</ul>\n");
			}
			else {
				print("					<p>None at present.</p>\n");
			}
?>
			 </li>
			 <li><h2>Other Competitions for this Cup</h2>
<?php
			if ($comptcup = $wpdb->get_results($comptcupsql)) {
				print("					<ul>\n");
					foreach ($comptcup as $ccup) {
						print("						<li><a href=\"".$ccup->guid."\" title=\"View more on this Competition\">".$ccup->post_title."</a></li>");
					}
				print("					</ul>\n");
			}
			else {
				print("					<p>None at present.</p>\n");
			}
?>
			 </li>
<?php
if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('sidebar-common') ) : ?>
			<li><h2 class="widgettitle">Opps</h2>
			  <ul>
			   <li>Something has gone wrong and you have lost your widget settings. better log in quick and fix it!</li>
			  </ul>
			</li>
<?php endif;
?>

		</ul>
	</div><!-- end of #subcontent -->
<?php get_footer(); ?>