<?php
/**
 * BBowlLeagueMan Teamplate View Season
 *
 * Page Template to view a Season's details
 *
 * @author 		Blacksnotling
 * @category 	Template
 * @package 	BBowlLeagueMan/Templates
 */
/*
 * Template Name: View Season
 */
?>
<?php get_header(); ?>
	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
<?php
		//Generate the permalink from the db setting. this is due to a difference between dev and Prd!
		$options = get_option('bblm_config');
		$seasonink = get_permalink(htmlspecialchars($options['page_season'], ENT_QUOTES));
?>
<div class="entry">
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<h2 class="entry-title"><?php the_title(); ?></h2>

				<div class="details season">
					<?php the_content(); ?>
				</div>
<?php
				//Grab the season ID for use in the database
				$seasonsql = 'SELECT S.sea_id, UNIX_TIMESTAMP(S.sea_sdate) AS sdate, UNIX_TIMESTAMP(S.sea_fdate) AS edate, sea_active FROM '.$wpdb->prefix.'season S, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE S.sea_id = J.tid AND J.prefix = \'sea_\' AND J.pid = P.ID AND P.ID = '.$post->ID;
				if ($sd = $wpdb->get_row($seasonsql)) {
				}

				$matchnumsql = 'SELECT COUNT(*) AS MATCHNUM FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P, '.$wpdb->prefix.'season S WHERE C.sea_id = S.sea_id AND M.c_id = C.c_id AND C.c_counts = 1 AND C.type_id = 1 AND M.m_id = J.tid AND J.prefix = \'m_\' AND J.pid = P.ID AND S.sea_id = '.$sd->sea_id;
				$matchnum = $wpdb->get_var($matchnumsql);
				$compnumsql = 'SELECT COUNT(*) AS compnum FROM '.$wpdb->prefix.'comp M, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE M.c_counts = 1 AND M.c_show = 1 AND M.type_id = 1 AND M.c_id = J.tid AND J.prefix = \'c_\' AND J.pid = P.ID AND M.sea_id = '.$sd->sea_id;
				$compnum = $wpdb->get_var($compnumsql);
				$cupnumsql = 'SELECT COUNT(*) AS cupnum FROM '.$wpdb->prefix.'series M, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P, '.$wpdb->prefix.'comp C WHERE C.series_id = M.series_id AND M.series_id = J.tid AND J.prefix = \'series_\' AND J.pid = P.ID AND C.c_counts = 1 AND C.c_show = 1 AND C.type_id = 1 AND C.sea_id = '.$sd->sea_id;
				$cupnum = $wpdb->get_var($cupnumsql);
				$playernumsql = 'SELECT COUNT(DISTINCT P.p_id) AS value FROM '.$wpdb->prefix.'season S, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'match_player P WHERE S.sea_id = C.sea_id AND C.c_id = M.c_id AND M.m_id = P.m_id AND C.c_counts = 1 AND C.c_show = 1 AND C.type_id = 1 AND S.sea_id = '.$sd->sea_id.' GROUP BY S.sea_id';
				$playernum = $wpdb->get_var($playernumsql);
				$teamnumsql = 'SELECT COUNT(DISTINCT P.t_id) AS value FROM '.$wpdb->prefix.'season S, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'team_comp P, '.$wpdb->prefix.'team T WHERE P.t_id = T.t_id AND T.type_id = 1 AND S.sea_id = C.sea_id AND P.c_id = C.c_id AND C.c_counts = 1 AND C.c_show = 1 AND S.sea_id = '.$sd->sea_id.' GROUP BY S.sea_id';
				$teamnum = $wpdb->get_var($teamnumsql);
				//$deathnumsql = 'SELECT COUNT(F.f_id) AS DEAD FROM '.$wpdb->prefix.'player_fate F, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'series S WHERE C.series_id = S.series_id AND M.c_id = C.c_id AND F.m_id = M.m_id AND (F.f_id = 1 OR F.f_id = 6) AND S.series_id = '.$cupid;
				$deathnumsql = 'SELECT COUNT(F.f_id) AS DEAD FROM '.$wpdb->prefix.'player_fate F, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C WHERE (F.f_id = 1 OR F.f_id = 6) AND F.m_id = M.m_id AND M.c_id = C.c_id AND  C.c_counts = 1 AND C.c_show = 1 AND C.type_id = 1 AND C.sea_id = '.$sd->sea_id;
				$deathnum = $wpdb->get_var($deathnumsql);

				$matchstatssql = 'SELECT SUM(M.m_tottd) AS TD, SUM(M.m_totcas) AS CAS, SUM(M.m_totcomp) AS COMP, SUM(M.m_totint) AS MINT FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'season S WHERE C.sea_id = S.sea_id AND M.c_id = C.c_id AND C.c_counts = 1 AND C.type_id = 1 AND S.sea_id = '.$sd->sea_id ;
				if ($matchstats = $wpdb->get_results($matchstatssql)) {
					foreach ($matchstats as $ms) {
						$tottd = $ms->TD;
						$totcas = $ms->CAS;
						$totcomp = $ms->COMP;
						$totint = $ms->MINT;
					}
				}
				//From this point, we only go further if any matches have been played
				if ($matchnum > 0) {
?>
				<h3>Overall Statistics and information</h3>
<?php
				if ($sd->sea_active) {
					print("<p>This Season began in <strong>".date("M y", $sd->sdate)."</strong> and is currently <strong>active</strong>.");
				}
				else {
					print("<p>This Season ran between <strong>".date("M y", $sd->sdate)."</strong> and <strong>".date("M y", $sd->edate)."</strong>.");
				}
				print(" During that time, <strong>".$playernum."</strong> Players in <strong>".$teamnum."</strong> Teams have played <strong>".$matchnum."</strong> Matches in <strong>".$compnum."</strong> Competitions for <strong>".$cupnum."</strong> Championship Cups.");
				if ($sd->sea_active) {
					print(" So far ");
				}
				else {
					print(" In total ");
				}
				print("they have managed to:</p>");
?>
				<ul>
					<li>Score <strong><?php print($tottd); ?></strong> Touchdowns (average <strong><?php print(round($tottd/$matchnum,1)); ?></strong> per match);</li>
					<li>Make <strong><?php print($totcomp); ?></strong> successful Completions (average <strong><?php print(round($totcomp/$matchnum,1)); ?></strong> per match);</li>
					<li>Cause <strong><?php print($totcas); ?></strong> Casualties (average <strong><?php print(round($totcas/$matchnum,1)); ?></strong> per match);</li>
					<li>Catch <strong><?php print($totint); ?></strong> Interceptions (average <strong><?php print(round($totint/$matchnum,1)); ?></strong> per match).</li>
					<li>Kill <strong><?php print($deathnum); ?></strong> players (average <strong><?php print(round($deathnum/$matchnum,1)); ?></strong> per match).</li>
				</ul>

<?php
				$biggestattendcesql = 'SELECT UNIX_TIMESTAMP(M.m_date) AS MDATE, M.m_gate AS VALUE, P.post_title AS MATCHT, P.guid AS MATCHLink FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE M.m_id = J.tid AND J.prefix = \'m_\' AND J.pid = P.ID AND M.c_id = C.c_id AND C.c_show = 1 AND C.type_id = 1 AND C.c_counts = 1 AND ((M.div_id = 1 OR M.div_id = 2 OR M.div_id = 3)) AND C.sea_id = '.$sd->sea_id.' ORDER BY M.m_gate DESC, MDATE ASC LIMIT 1';
				$biggestattendcenonfinalsql = 'SELECT UNIX_TIMESTAMP(M.m_date) AS MDATE, M.m_gate AS VALUE, P.post_title AS MATCHT, P.guid AS MATCHLink FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE M.m_id = J.tid AND J.prefix = \'m_\' AND J.pid = P.ID AND M.c_id = C.c_id AND C.c_show = 1 AND C.type_id = 1 AND C.c_counts = 1 AND M.div_id != 1 AND M.div_id != 2 AND M.div_id != 3 AND C.sea_id = '.$sd->sea_id.' ORDER BY M.m_gate DESC, MDATE ASC LIMIT 1';
				$bcn = $wpdb->get_row($biggestattendcenonfinalsql);
?>
					<ul>
						<li>The Highest recorded attendance (not a Final or Semi-Final) is <strong><?php print(number_format($bcn->VALUE)); ?> fans</strong> in the match between <strong><?php print($bcn->MATCHT); ?></strong> on <?php print(date("d.m.25y", $bcn->MDATE)); ?></li>
<?php
					if ($bc = $wpdb->get_row($biggestattendcesql)) {
?>
						<li>The Highest recorded attendance (Final or Semi-Final) is <strong><?php print(number_format($bc->VALUE)); ?> fans</strong> in the match between <strong><?php print($bc->MATCHT); ?></strong> on <?php print(date("d.m.25y", $bc->MDATE)); ?></li>
<?php
					}
?>

					</ul>
<?php

				$championssql = 'SELECT COUNT(A.a_name) AS ANUM, P.post_title, P.guid FROM '.$wpdb->prefix.'awards_team_comp T, '.$wpdb->prefix.'awards A, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P, '.$wpdb->prefix.'comp C WHERE T.c_id = C.c_id AND C.c_counts = 1 AND C.c_show = 1 AND C.type_id = 1 AND T.t_id = J.tid AND J.prefix = \'t_\' AND J.pid = P.ID AND A.a_id = 1 AND A.a_id = T.a_id AND C.sea_id = '.$sd->sea_id.' GROUP BY T.t_id ORDER BY A.a_id DESC, P.post_title ASC';
				if ($champions = $wpdb->get_results($championssql)) {
					print("<h3>Championship Cup Winners this season</h3>\n");
					$zebracount = 1;
					print("<table>\n	<tr>\n		<th class=\"tbl_name\">Team</th>\n		<th class=\"tbl_stat\">Championships</th>\n		</tr>\n");
					foreach ($champions as $champ) {
						if ($zebracount % 2) {
							print("	<tr>\n");
						}
						else {
							print("	<tr class=\"tbl_alt\">\n");
						}
						print("		<td><a href=\"".$champ->guid."\" title=\"View more about ".$champ->post_title."\">".$champ->post_title."</a></td>\n		<td>".$champ->ANUM."</td>\n		</tr>\n");
						$zebracount++;
					}
					print("</table>\n");
				}

				$compseasonsql = 'SELECT P.post_title, P.guid FROM '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE C.c_counts = 1 AND C.c_show = 1 AND C.type_id = 1 AND C.c_id = J.tid AND J.prefix = \'c_\' AND J.pid = P.ID AND C.sea_id = '.$sd->sea_id.' ORDER BY C.c_id ASC';
				if ($compseason = $wpdb->get_results($compseasonsql)) {
					print("<h3>Competitions this season</h3>\n	<ul>\n");
					foreach ($compseason as $cs) {
						print("<li><a href=\"".$cs->guid."\" title=\"Read more about this competition\">".$cs->post_title."</a></li>\n");
					}
					print("	</ul>\n");
				}
?>
				<h3>Team Performance this Season</h3>
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
						<th class="tbl_stat">Win%</th>
					</tr>
					</thead>
					<tbody>

<?php
				$teamstatssql = 'SELECT P.post_title, SUM(T.tc_played) AS TP, SUM(T.tc_W) AS TW, SUM(T.tc_L) AS TL, SUM(T.tc_D) AS TD, SUM(T.tc_tdfor) AS TDF, SUM(T.tc_tdagst) AS TDA, SUM(T.tc_casfor) AS TCF, SUM(T.tc_casagst) AS TCA, SUM(T.tc_INT) AS TI, SUM(T.tc_comp) AS TC, P.guid FROM '.$wpdb->prefix.'team_comp T, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'team Z WHERE Z.t_id = T.t_id anD Z.t_show = 1 AND C.c_id = T.c_id AND C.c_counts = 1 AND C.c_show = 1 AND C.type_id = 1 AND T.t_id = J.tid AND J.prefix = \'t_\' AND J.pid = P.ID AND C.sea_id = '.$sd->sea_id.' GROUP BY T.t_id ORDER BY P.post_title ASC LIMIT 0, 30 ';
				if ($teamstats = $wpdb->get_results($teamstatssql)) {
					$zebracount = 1;
					foreach ($teamstats as $tst) {
						if ($zebracount % 2) {
							print("					<tr>\n");
						}
						else {
							print("					<tr class=\"tbl_alt\">\n");
						}
						print("						<td><a href=\"".$tst->guid."\" title=\"Read more on ".$tst->post_title."\">".$tst->post_title."</a></td>\n						<td>".$tst->TP."</td>\n						<td>".$tst->TW."</td>\n						<td>".$tst->TL."</td>\n						<td>".$tst->TD."</td>\n						<td>".$tst->TDF."</td>\n						<td>".$tst->TDA."</td>\n						<td>".$tst->TCF."</td>\n						<td>".$tst->TCA."</td>\n						<td>".$tst->TC."</td>\n						<td>".$tst->TI."</td>\n						");
						if ($tst->TP > 0) {
							print("<td>".number_format((($tst->TW/$tst->TP)*100))."%</td>\n");
						}
						else {
							print("<td>N/A</td>\n");
						}
						print("					</tr>\n");
						$zebracount++;
					}

				}
?>
				</tbody>
				</table>
<?php

				print("<h3>Player Performance this Season</h3>\n");

					  ///////////////////////////
					 // Start of Player Stats //
					///////////////////////////
					$stat_limit = bblm_get_stat_limit();
					$bblm_star_team = bblm_get_star_player_team();


					//Generates an array containing all the Stats that are going to be checked
					$playerstatsarray = array();
					$playerstatsarray[0]['item'] = "mp_spp";
					$playerstatsarray[0]['title'] = "Best Players";
					$playerstatsarray[0]['error'] = "The Best Player list is not available at the moment";
					$playerstatsarray[1]['item'] = "mp_td";
					$playerstatsarray[1]['title'] = "Top Scorers";
					$playerstatsarray[1]['error'] = "The Touch Downs have been made yet!";
					$playerstatsarray[2]['item'] = "mp_cas";
					$playerstatsarray[2]['title'] = "Most Vicious";
					$playerstatsarray[2]['error'] = "No casualties have been caused yet";
					$playerstatsarray[3]['item'] = "mp_comp";
					$playerstatsarray[3]['title'] = "Top Passers";
					$playerstatsarray[3]['error'] = "No Completions have been recorded yet";
					$playerstatsarray[4]['item'] = "mp_int";
					$playerstatsarray[4]['title'] = "Top Interceptors";
					$playerstatsarray[4]['error'] = "No Inteceptions have been recorded yet";
					$playerstatsarray[5]['item'] = "mp_mvp";
					$playerstatsarray[5]['title'] = "Most Valuable Players (MVP)";
					$playerstatsarray[5]['error'] = "The Most Valuable Players list is not available at the moment";

					//For each of the stats, print the top players list. If none are found, display the relevant error
					foreach ($playerstatsarray as $tpa) {
						//Generic SQL Call, populated with the stat we are looking for
						$statsql = 'SELECT Y.post_title, T.WPID, Y.guid, SUM(M.'.$tpa['item'].') AS VALUE, R.pos_name FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'match X, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' Y, '.$wpdb->prefix.'position R WHERE P.pos_id = R.pos_id AND P.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = Y.ID AND M.m_id = X.m_id AND X.c_id = C.c_id AND C.c_counts = 1 AND C.type_id = 1 AND M.p_id = P.p_id AND P.t_id = T.t_id AND M.'.$tpa['item'].' > 0 AND C.sea_id = '.$sd->sea_id.' AND T.t_id != '.$bblm_star_team.' GROUP BY P.p_id ORDER BY VALUE DESC LIMIT '.$stat_limit;

						print("<h4>".$tpa['title']."</h4>\n");
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
									print("	<td><a href=\"".$ts->guid."\" title=\"View more details on ".$ts->post_title."\">".$ts->post_title."</a></td>\n	<td>" . esc_html( $ts->pos_name ) . "</td>\n	<td><a href=\"" . get_post_permalink( $ts->WPID ) . "\" title=\"Read more on this team\">" . esc_html( get_the_title( $ts->WPID ) ) . "</a></td>\n	<td>".$ts->VALUE."</td>\n	</tr>\n");
									$prevvalue = $ts->VALUE;
								}
								$zebracount++;
						}
							print("</table>\n");
						}
						else {
							print("	<div class=\"info\">\n		<p>".$tpa[ 'error' ]."</p>\n	</div>\n");
						}
					}
				//==================
				// -- Top Killer --
				//==================
					$statsql = 'SELECT O.post_title, O.guid, COUNT(*) AS VALUE , E.pos_name, T.WPID FROM `'.$wpdb->prefix.'player_fate` F, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' O, '.$wpdb->prefix.'position E, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'comp C WHERE P.t_id = T.t_id AND P.pos_id = E.pos_id AND P.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = O.ID AND (F.f_id = 1 OR F.f_id = 6) AND P.p_id = F.pf_killer AND F.m_id = M.m_id AND M.c_id = C.c_id AND C.type_id = 1 AND C.c_counts = 1 AND C.c_show = 1 AND C.sea_id = '.$sd->sea_id.' AND T.t_id != '.$bblm_star_team.' GROUP BY F.pf_killer ORDER BY VALUE DESC LIMIT '.$stat_limit;
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
								print("	<td><a href=\"".$ts->guid."\" title=\"View more details on ".$ts->post_title."\">".$ts->post_title."</a></td>\n	<td>" . esc_html( $ts->pos_name ) . "</td>\n	<td><a href=\"" . get_post_permalink( $ts->WPID ) . "\" title=\"Read more on this team\">" . esc_html( get_the_title( $ts->WPID ) ) . "</a></td>\n	<td>".$ts->VALUE."</td>\n	</tr>\n");
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
					if (0 == $sd->sea_active) {
						//the Season is over, display the awards!
?>
					<h3 id="awardsfull">Awards</h3>
					<h4>Main Awards</h4>
					<table>
						<tr>
							<th class="tbl_name">Award</th>
							<th class="tbl_name">Team</th>
							<th class="tbl_name">Competition</th>
						</tr>
<?php
					$compmajorawardssql = 'SELECT A.a_name, P.post_title, P.guid, H.post_title AS CompName, H.guid AS CompLink FROM '.$wpdb->prefix.'awards A, '.$wpdb->prefix.'awards_team_comp B, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp Y, '.$wpdb->posts.' H WHERE C.c_id = Y.tid AND Y.prefix = \'c_\' AND Y.pid = H.ID AND A.a_id = B.a_id AND a_cup = 1 AND B.t_id = J.tid AND J.prefix = \'t_\' AND J.pid = P.ID AND B.c_id = C.c_id AND C.c_show = 1 AND C.c_counts = 1 AND C.type_id = 1 AND C.sea_id = '.$sd->sea_id.' ORDER BY C.c_id ASC, A.a_id ASC';
					//$compmajorawardssql = 'SELECT A.a_name, P.post_title, P.guid FROM '.$wpdb->prefix.'awards A, '.$wpdb->prefix.'awards_team_comp B, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P, '.$wpdb->prefix.'comp C WHERE A.a_id = B.a_id AND a_cup = 1 AND B.t_id = J.tid AND J.prefix = \'t_\' AND J.pid = P.ID AND B.c_id = C.c_id AND C.c_show = 1 AND C.c_counts = 1 AND C.sea_id = '.$sd->sea_id.' ORDER BY A.a_id ASC';
					if ($cmawards = $wpdb->get_results($compmajorawardssql)) {
						$zebracount = 1;
						foreach ($cmawards as $cma) {
							if ($zebracount % 2) {
								print("						<tr>\n");
							}
							else {
								print("						<tr class=\"tbl_alt\">\n");
							}
								print("		<td>".$cma->a_name."</td>\n		<td><a href=\"".$cma->guid."\" title=\"Read more about ".$cma->post_title."\">".$cma->post_title."</a></td>\n		<td><a href=\"".$cma->CompLink."\" title=\"Read more about ".$cma->CompName."\">".$cma->CompName."</a></td>\n	</tr>\n");
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
					$compteamawardssql = 'SELECT A.a_name, P.post_title, P.guid, B.ats_value AS value FROM '.$wpdb->prefix.'awards A, '.$wpdb->prefix.'awards_team_sea B, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE A.a_id = B.a_id AND a_cup = 0 AND B.t_id = J.tid AND J.prefix = \'t_\' AND J.pid = P.ID AND B.sea_id = '.$sd->sea_id.' ORDER BY A.a_id ASC';
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
					$compplayerawardssql = 'SELECT A.a_name, P.post_title AS Pname, P.guid AS Plink, B.aps_value AS value, T.WPID FROM '.$wpdb->prefix.'awards A, '.$wpdb->prefix.'awards_player_sea B, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P, '.$wpdb->prefix.'player R, '.$wpdb->prefix.'team T WHERE R.t_id = T.t_id AND R.p_id = B.p_id AND A.a_id = B.a_id AND a_cup = 0 AND B.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = P.ID AND B.sea_id = '.$sd->sea_id.' ORDER BY A.a_id ASC';
					if ($cpawards = $wpdb->get_results($compplayerawardssql)) {
						$zebracount = 1;
						foreach ($cpawards as $cpa) {
							if ($zebracount % 2) {
								print("						<tr>\n");
							}
							else {
								print("						<tr class=\"tbl_alt\">\n");
							}
								print("		<td>".$cpa->a_name."</td>\n		<td><a href=\"".$cpa->Plink."\" title=\"Read more about ".$cpa->Pname."\">".$cpa->Pname."</a></td>\n		<td><a href=\"".  get_post_permalink( $cpa->WPID ) ."\" title=\"Read more about this team\">" . esc_html( get_the_title( $cpa->WPID ) ) . "</a></td>\n		<td>".$cpa->value."</td>\n	</tr>\n");
							$zebracount++;
						}
					}
?>
					</table>
<?php
				}//end of awards

				}//end of if matches
				else {
					print("	<div class=\"info\">\n		<p>No matches have been played in this Season yet. Stay tuned for further updates as the games start rolling in.</p>\n	</div>\n");
				}

?>
				<p class="postmeta"><?php edit_post_link( __( 'Edit', 'oberwald' ), ' <strong>[</strong> ', ' <strong>]</strong> '); ?></p>

			</div>
		</div>


		<?php endwhile;?>
	<?php endif; ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
