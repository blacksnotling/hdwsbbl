<?php
/**
 * BBowlLeagueMan Teamplate View Cup
 *
 * Page Template to view a Championship Cup details
 *
 * @author 		Blacksnotling
 * @category 	Template
 * @package 	BBowlLeagueMan/Templates
 */
/*
 * Template Name: View Cup
 */
?>
<?php get_header(); ?>
	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
			<div class="entry">
				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<h2 class="entry-title"><?php the_title(); ?></h2>

				<div class="details cup">
					<?php the_content(); ?>
				</div>

<?php
				//Grab the series ID for use in the database
				$seriesidsql = 'SELECT J.tid FROM '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE J.prefix = \'series_\' AND J.pid = P.ID AND  P.ID = '.$post->ID;
				$cupid = $wpdb->get_var($seriesidsql);

				//Determine if any mayches have taken place in this series.
				$matchnumsql = 'SELECT COUNT(*) AS MATCHNUM FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'series S, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE C.series_id = S.series_id AND M.c_id = C.c_id AND C.c_counts = 1 AND M.m_id = J.tid AND J.prefix = \'m_\' AND J.pid = P.ID AND S.series_id = '.$cupid;
				$matchnum = $wpdb->get_var($matchnumsql);

				//From this point, we only go further if any matches have been played
				if (0 < $matchnum) {

					$matchstatssql = 'SELECT SUM(M.m_tottd) AS TD, SUM(M.m_totcas) AS CAS, SUM(M.m_totcomp) AS COMP, SUM(M.m_totint) AS MINT FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'series S WHERE C.series_id = S.series_id AND M.c_id = C.c_id AND C.c_counts = 1 AND S.series_id = '.$cupid;
					if ($matchstats = $wpdb->get_results($matchstatssql)) {
						foreach ($matchstats as $ms) {
							$tottd = $ms->TD;
							$totcas = $ms->CAS;
							$totcomp = $ms->COMP;
							$totint = $ms->MINT;
						}
					}
					//Counts the Dead. NOTE: THis does not check for c_counts = 1 as the cups page will show al
					$deathnumsql = 'SELECT COUNT(F.f_id) AS DEAD FROM '.$wpdb->prefix.'player_fate F, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C WHERE M.c_id = C.c_id AND F.m_id = M.m_id AND (F.f_id = 1 OR F.f_id = 6) AND C.series_id = '.$cupid;
					$deathnum = $wpdb->get_var($deathnumsql);
					$compnumsql = 'SELECT COUNT(*) AS ccount FROM '.$wpdb->prefix.'comp WHERE c_counts = 1 AND series_id = '.$cupid;
					$compnum = $wpdb->get_var($compnumsql);
					$playermnumsql = 'SELECT COUNT(DISTINCT P.p_id) AS value FROM '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'match_player P WHERE C.c_id = M.c_id AND M.m_id = P.m_id AND C.c_counts = 1 AND C.c_show = 1 AND C.series_id = '.$cupid.' GROUP BY C.series_id';
					$playernum = $wpdb->get_var($playermnumsql);
					$teamnumsql = 'SELECT COUNT(DISTINCT P.t_id) AS value FROM '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'team_comp P WHERE P.c_id = C.c_id AND C.c_counts = 1 AND C.c_show = 1 AND C.series_id = '.$cupid.' GROUP BY C.series_id';
					$teamnum = $wpdb->get_var($teamnumsql);
?>
				<h3>Overall Statistics and information</h3>
				<p><strong><?php print($playernum); ?></strong> Players in <strong><?php print($teamnum); ?></strong> Teams have played <strong><?php print($matchnum); ?></strong> Matches in <strong><?php print($compnum); ?></strong> Competitions for this Championship Cup. To date they have managed to:</p>
				<ul>
					<li>Score <strong><?php print($tottd); ?></strong> Touchdowns (average <strong><?php print(round($tottd/$matchnum,1)); ?></strong> per match);</li>
					<li>Make <strong><?php print($totcomp); ?></strong> successful Completions (average <strong><?php print(round($totcomp/$matchnum,1)); ?></strong> per match);</li>
					<li>Cause <strong><?php print($totcas); ?></strong> Casualties (average <strong><?php print(round($totcas/$matchnum,1)); ?></strong> per match);</li>
					<li>Catch <strong><?php print($totint); ?></strong> Interceptions (average <strong><?php print(round($totint/$matchnum,1)); ?></strong> per match).</li>
					<li>Kill <strong><?php print($deathnum); ?></strong> players (average <strong><?php print(round($deathnum/$matchnum,1)); ?></strong> per match).</li>
				</ul>
<?php
					$biggestattendcesql = 'SELECT UNIX_TIMESTAMP(M.m_date) AS MDATE, M.m_gate AS VALUE, P.post_title AS MATCHT, P.guid AS MATCHLink FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE M.m_id = J.tid AND J.prefix = \'m_\' AND J.pid = P.ID AND M.c_id = C.c_id AND C.c_show = 1 AND C.type_id = 1 AND C.c_counts = 1 AND C.series_id = '.$cupid.' ORDER BY M.m_gate DESC, MDATE ASC LIMIT 1';
					$biggestattendcenonfinalsql = 'SELECT UNIX_TIMESTAMP(M.m_date) AS MDATE, M.m_gate AS VALUE, P.post_title AS MATCHT, P.guid AS MATCHLink FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE M.m_id = J.tid AND J.prefix = \'m_\' AND J.pid = P.ID AND M.c_id = C.c_id AND C.c_show = 1 AND C.type_id = 1 AND C.c_counts = 1 AND M.div_id != 1 AND M.div_id != 2 AND M.div_id != 3 AND C.series_id = '.$cupid.' ORDER BY M.m_gate DESC, MDATE ASC LIMIT 1';
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
					print("<h3>Winners of this Championship Cup</h3>\n");
					$winnerssql = 'SELECT T.t_name, T.t_guid, COUNT(*) as wins FROM '.$wpdb->prefix.'awards_team_comp A, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'series S WHERE C.c_id = A.c_id AND A.t_id = T.t_id AND C.series_id = S.series_id AND A.a_id = 1 AND S.series_id = '.$cupid.' GROUP BY A.t_id ORDER BY wins DESC';
					if ($winners = $wpdb->get_results($winnerssql)) {
						$has_completed = 1;
						$zebracount = 1;
						print("<table>\n	<tr>\n		<th class=\"tbl_name\">Team</th>\n		<th class=\"tbl_stat\">Wins</th>\n	</tr>");
						foreach ($winners as $wi) {
							if ($zebracount % 2) {
								print("	<tr>\n");
							}
							else {
								print("	<tr class=\"tbl_alt\">\n");
							}
							print("		<td><a href=\"".$wi->t_guid."\" title=\"View more information on ".$wi->t_name."\">".$wi->t_name."</a></td>\n		<td>".$wi->wins."</td>\n	</tr>");
							$zebracount++;
						}
						print("</table>\n");
					}
					else {
						print("	<div class=\"info\">\n		<p>No teams have previously won this Championship Cup!</p>\n	</div>\n");
					}

					print("<h3>Competitions for this Championship Cup</h3>\n");
					$complistingsql = 'SELECT P.post_title, P.guid, SUM(T.tc_played) AS PLD, SUM(T.tc_tdfor) AS TD, SUM(T.tc_casfor) AS CAS, SUM(T.tc_comp) AS COMP, SUM(T.tc_int) AS cINT FROM '.$wpdb->prefix.'team_comp T, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P, '.$wpdb->prefix.'series S WHERE C.series_id = S.series_id AND T.c_id = C.c_id AND J.tid = C.c_id AND J.prefix = \'c_\' AND J.pid = P.ID AND tc_played > 0 AND C.c_counts = 1 AND C.c_show = 1 AND S.series_id = '.$cupid.' GROUP BY C.c_id ORDER BY C.c_id DESC';
					if ($compl = $wpdb->get_results($complistingsql)) {
						$zebracount = 1;
						print("	<table class=\"sortable\">\n	<thead>\n		<tr>\n			<th class=\"tbl_title\">Competition</th>\n			<th class=\"tbl_stat\">Games</th>\n			<th class=\"tbl_stat\">TD</th>\n			<th class=\"tbl_stat\">CAS</th>\n			<th class=\"tbl_stat\">COMP</th>\n			<th class=\"tbl_stat\">INT</th>\n		</tr>\n	</thead>\n	<tbody>\n");
						foreach ($compl as $cl) {
							if ($zebracount % 2) {
								print("		<tr>\n");
							}
							else {
								print("		<tr class=\"tbl_alt\">\n");
							}
							print("			<td><a href=\"".$cl->guid."\" title=\"View more info about ".$cl->post_title."\">".$cl->post_title."</a></td>\n			<td>".($cl->PLD/2)."</td>\n			<td>".$cl->TD."</td>\n			<td>".$cl->CAS."</td>\n			<td>".$cl->COMP."</td>\n			<td>".$cl->cINT."</td>\n		</tr>\n");

							$zebracount++;
						}
						print("	</tbody>\n	</table>\n");
					}

					print("<h3>Team Statistics for this Championship Cup</h3>\n");
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
						<th class="tbl_stat">Win%</th>
					</tr>
					</thead>
					<tbody>
<?php
					//$teamistingsql = 'SELECT P.post_title, SUM(T.tc_played) AS TP, SUM(T.tc_W) AS TW, SUM(T.tc_L) AS TL, SUM(T.tc_D) AS TD, SUM(T.tc_tdfor) AS TDF, SUM(T.tc_tdagst) AS TDA, SUM(T.tc_casfor) AS TCF, SUM(T.tc_casagst) AS TCA, SUM(T.tc_INT) AS TI, SUM(T.tc_comp) AS TC, P.guid FROM '.$wpdb->prefix.'team_comp T, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P, '.$wpdb->prefix.'comp C WHERE C.c_id = T.c_id AND C.c_counts = 1 AND C.c_show = 1 AND T.t_id = J.tid AND J.prefix = \'t_\' AND J.pid = P.ID AND T.tc_played > 0 AND C.series_id = '.$cupid.' GROUP BY T.t_id ORDER BY P.post_title ASC';
					$teamistingsql = 'SELECT Z.t_name, SUM(T.tc_played) AS TP, SUM(T.tc_W) AS TW, SUM(T.tc_L) AS TL, SUM(T.tc_D) AS TD, SUM(T.tc_tdfor) AS TDF, SUM(T.tc_tdagst) AS TDA, SUM(T.tc_casfor) AS TCF, SUM(T.tc_casagst) AS TCA, SUM(T.tc_INT) AS TI, SUM(T.tc_comp) AS TC, Z.t_guid FROM '.$wpdb->prefix.'team_comp T, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'team Z WHERE Z.t_id = T.t_id AND C.c_id = T.c_id AND Z.t_show = 1 AND T.tc_played > 0 AND C.series_id = '.$cupid.' GROUP BY T.t_id ORDER BY Z.t_name ASC';
					if ($teamstats = $wpdb->get_results($teamistingsql)) {
						$zebracount = 1;
						foreach ($teamstats as $tst) {
							if ($zebracount % 2) {
								print("					<tr>\n");
							}
							else {
								print("					<tr class=\"tbl_alt\">\n");
							}
							print("						<td><a href=\"".$tst->t_guid."\" title=\"Read more on ".$tst->t_name."\">".$tst->t_name."</a></td>\n						<td>".$tst->TP."</td>\n						<td>".$tst->TW."</td>\n						<td>".$tst->TL."</td>\n						<td>".$tst->TD."</td>\n						<td>".$tst->TDF."</td>\n						<td>".$tst->TDA."</td>\n						<td>".$tst->TCF."</td>\n						<td>".$tst->TCA."</td>\n						<td>".$tst->TC."</td>\n						<td>".$tst->TI."</td>\n						");
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
					print("<h3>Player Statistics for this Championship Cup</h3>\n");
					  ///////////////////////////
					 // Start of Player Stats //
					///////////////////////////
					$options = get_option('bblm_config');
					$stat_limit = htmlspecialchars($options['display_stats'], ENT_QUOTES);
					$bblm_star_team = htmlspecialchars($options['team_star'], ENT_QUOTES);



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
						$statsql = 'SELECT Y.post_title, T.t_name AS TEAM, T.t_guid AS TEAMLink, Y.guid, SUM(M.'.$tpa['item'].') AS VALUE, R.pos_name FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'match X, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' Y, '.$wpdb->prefix.'position R WHERE P.pos_id = R.pos_id AND P.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = Y.ID AND M.m_id = X.m_id AND X.c_id = C.c_id AND M.p_id = P.p_id AND P.t_id = T.t_id AND M.'.$tpa['item'].' > 0 AND C.series_id = '.$cupid.' AND T.t_id != '.$bblm_star_team.' GROUP BY P.p_id ORDER BY VALUE DESC LIMIT '.$stat_limit;

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
									print("	<td><a href=\"".$ts->guid."\" title=\"View more details on ".$ts->post_title."\">".$ts->post_title."</a></td>\n	<td>".$ts->pos_name."</td>\n	<td><a href=\"".$ts->TEAMLink."\" title=\"Read more on this team\">".$ts->TEAM."</a></td>\n	<td>".$ts->VALUE."</td>\n	</tr>\n");
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
					$statsql = 'SELECT O.post_title, O.guid, COUNT(*) AS VALUE , E.pos_name, T.t_name AS TEAM, T.t_guid AS TeamLink FROM `'.$wpdb->prefix.'player_fate` F, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' O, '.$wpdb->prefix.'position E, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'comp C WHERE P.t_id = T.t_id AND P.pos_id = E.pos_id AND P.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = O.ID AND (F.f_id = 1 OR F.f_id = 6) AND P.p_id = F.pf_killer AND F.m_id = M.m_id AND M.c_id = C.c_id AND C.type_id = 1 AND C.c_counts = 1 AND C.c_show = 1 AND C.series_id = '.$cupid.' AND T.t_id != '.$bblm_star_team.' GROUP BY F.pf_killer ORDER BY VALUE DESC LIMIT '.$stat_limit;
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
								print("	<td><a href=\"".$ts->guid."\" title=\"View more details on ".$ts->post_title."\">".$ts->post_title."</a></td>\n	<td>".$ts->pos_name."</td>\n	<td><a href=\"".$ts->TeamLink."\" title=\"Read more on this team\">".$ts->TEAM."</a></td>\n	<td>".$ts->VALUE."</td>\n	</tr>\n");
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

					//If this cup had any winners determined, then one must have completed and therefore have awards
					if ($has_completed) {
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
					//$compmajorawardssql = 'SELECT A.a_name, P.post_title, P.guid, H.post_title AS CompName, H.guid AS CompLink FROM '.$wpdb->prefix.'awards A, '.$wpdb->prefix.'awards_team_comp B, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp Y, '.$wpdb->posts.' H WHERE C.c_id = Y.tid AND Y.prefix = \'c_\' AND Y.pid = H.ID AND A.a_id = B.a_id AND a_cup = 1 AND B.t_id = J.tid AND J.prefix = \'t_\' AND J.pid = P.ID AND B.c_id = C.c_id AND C.c_show = 1 AND C.c_counts = 1 AND C.series_id = '.$cupid.' ORDER BY A.a_id ASC, C.c_id DESC';
					$compmajorawardssql = 'SELECT A.a_name, T.t_name, T.t_guid, H.post_title AS CompName, H.guid AS CompLink FROM '.$wpdb->prefix.'awards A, '.$wpdb->prefix.'awards_team_comp B, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp Y, '.$wpdb->posts.' H WHERE C.c_id = Y.tid AND Y.prefix = \'c_\' AND Y.pid = H.ID AND A.a_id = B.a_id AND a_cup = 1 AND B.t_id = T.t_id AND B.c_id = C.c_id AND C.series_id = '.$cupid.' ORDER BY A.a_id ASC, C.c_id DESC';
					if ($cmawards = $wpdb->get_results($compmajorawardssql)) {
						$zebracount = 1;
						foreach ($cmawards as $cma) {
							if ($zebracount % 2) {
								print("						<tr>\n");
							}
							else {
								print("						<tr class=\"tbl_alt\">\n");
							}
								print("		<td>".$cma->a_name."</td>\n		<td><a href=\"".$cma->t_guid."\" title=\"Read more about ".$cma->t_name."\">".$cma->t_name."</a></td>\n		<td><a href=\"".$cma->CompLink."\" title=\"Read more about ".$cma->CompName."\">".$cma->CompName."</a></td>\n	</tr>\n");
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
							<th class="tbl_name">Competition</th>
							<th class="tbl_stat">Value</th>
						</tr>
<?php
					//$compteamawardssql = 'SELECT A.a_name, P.post_title, P.guid, B.atc_value AS value, U.post_title AS Comp, U.guid AS CompLink FROM '.$wpdb->prefix.'awards A, '.$wpdb->prefix.'awards_team_comp B, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp Y, '.$wpdb->posts.' U WHERE Y.tid = C.c_id AND Y.prefix = \'c_\' AND Y.pid = U.ID AND C.c_id = B.c_id AND A.a_id = B.a_id AND a_cup = 0 AND B.t_id = J.tid AND J.prefix = \'t_\' AND J.pid = P.ID AND C.series_id = '.$cupid.' ORDER BY A.a_id ASC, C.c_id DESC';
					$compteamawardssql = 'SELECT A.a_name, T.t_name, T.t_guid, B.atc_value AS value, U.post_title AS Comp, U.guid AS CompLink FROM '.$wpdb->prefix.'awards A, '.$wpdb->prefix.'awards_team_comp B, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp Y, '.$wpdb->posts.' U, '.$wpdb->prefix.'team T WHERE Y.tid = C.c_id AND Y.prefix = \'c_\' AND Y.pid = U.ID AND C.c_id = B.c_id AND A.a_id = B.a_id AND a_cup = 0 AND B.t_id = T.t_id AND C.series_id = '.$cupid.' ORDER BY A.a_id ASC, C.c_id DESC';
					if ($ctawards = $wpdb->get_results($compteamawardssql)) {
						$zebracount = 1;
						foreach ($ctawards as $cta) {
							if ($zebracount % 2) {
								print("						<tr>\n");
							}
							else {
								print("						<tr class=\"tbl_alt\">\n");
							}
								print("		<td>".$cta->a_name."</td>\n		<td><a href=\"".$cta->t_guid."\" title=\"Read more about ".$cta->t_name."\">".$cta->t_name."</a></td>\n		<td><a href=\"".$cta->CompLink."\" title=\"View more on this Competition\">".$cta->Comp."</a></td>\n		<td>".$cta->value."</td>\n	</tr>\n");
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
							<th class="tbl_name">Competition</th>
							<th class="tbl_stat">Value</th>
						</tr>
<?php
					//$compplayerawardssql = 'SELECT A.a_name, P.post_title AS Pname, P.guid AS Plink, B.apc_value AS value, F.post_title AS Tname, F.guid AS Tlink, U.post_title AS Comp, U.guid AS CompLink FROM '.$wpdb->prefix.'awards A, '.$wpdb->prefix.'awards_player_comp B, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P, '.$wpdb->prefix.'player R, '.$wpdb->prefix.'bb2wp D, '.$wpdb->posts.' F, '.$wpdb->prefix.'bb2wp Y, '.$wpdb->posts.' U, '.$wpdb->prefix.'comp C WHERE Y.tid = C.c_id AND Y.prefix = \'c_\' AND Y.pid = U.ID AND C.c_id = B.c_id AND R.t_id = D.tid AND D.prefix = \'t_\' AND D.pid = F.ID AND R.p_id = B.p_id AND A.a_id = B.a_id AND a_cup = 0 AND B.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = P.ID AND C.series_id = '.$cupid.' ORDER BY A.a_id ASC, C.c_id DESC';
					$compplayerawardssql = 'SELECT A.a_name, P.post_title AS Pname, P.guid AS Plink, B.apc_value AS value, T.t_name AS Tname, T.t_guid AS Tlink, U.post_title AS Comp, U.guid AS CompLink FROM '.$wpdb->prefix.'awards A, '.$wpdb->prefix.'awards_player_comp B, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P, '.$wpdb->prefix.'player R, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'bb2wp Y, '.$wpdb->posts.' U, '.$wpdb->prefix.'comp C WHERE Y.tid = C.c_id AND Y.prefix = \'c_\' AND Y.pid = U.ID AND C.c_id = B.c_id AND R.t_id = T.t_id AND R.p_id = B.p_id AND A.a_id = B.a_id AND a_cup = 0 AND B.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = P.ID AND C.series_id = '.$cupid.' ORDER BY A.a_id ASC, C.c_id DESC';
					if ($cpawards = $wpdb->get_results($compplayerawardssql)) {
						$zebracount = 1;
						foreach ($cpawards as $cpa) {
							if ($zebracount % 2) {
								print("						<tr>\n");
							}
							else {
								print("						<tr class=\"tbl_alt\">\n");
							}
								print("		<td>".$cpa->a_name."</td>\n		<td><a href=\"".$cpa->Plink."\" title=\"Read more about ".$cpa->Pname."\">".$cpa->Pname."</a></td>\n		<td><a href=\"".$cpa->Tlink."\" title=\"Read more about ".$cpa->Tname."\">".$cpa->Tname."</a></td>\n		<td><a href=\"".$cpa->CompLink."\" title=\"View more on this Competition\">".$cpa->Comp."</a></td>\n		<td>".$cpa->value."</td>\n	</tr>\n");
							$zebracount++;
						}
					}
?>
					</table>
<?php
					}//end of if $has_completed

				}//end of if matches
				else {
					print("	<div class=\"info\">\n		<p>No Competitions or matches have been played for this Championship Cup. Stay tuned for future updates.</p>\n	</div>\n");
				}


?>

				<p class="postmeta"><?php edit_post_link( __( 'Edit', 'oberwald' ), ' <strong>[</strong> ', ' <strong>]</strong> '); ?></p>

			</div>
		</div>


		<?php endwhile;?>
	<?php endif; ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
