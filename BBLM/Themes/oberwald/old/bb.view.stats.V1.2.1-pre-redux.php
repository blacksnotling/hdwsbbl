<?php
/*
Template Name: Statistics
*/
/*
*	Filename: bb.view.stats.php
*	Version: 1.2.1
*	Description: .Page template to display statistics
*/
/* -- Change History --
20080418 - 0.1b - Initial creation of file.
20080419 - 0.2b - Begna intial work on stat listing
20080425 - 0.2.1b - fixed a bug where a comment was not closed and was causing an error
20080625 - 0.3b - Began re-working the page into its final shape!
20080629 - 0.4b - Added team breakdown and other assirted stats
20080702 - 0.4.1b - added some thead and tbody tags to the sortable tables
20080707 - 0.4.2b - Fixed a divisional error and added some restrictions to a sql query
		 - 0.5b - commented out the middle section of the page. that will wait until 1.1!
20080717 - 0.6b - removed the sortable table comment, finished the stat tables off.
20080718 - 0.7b - fixed the player stats table for active stats. it was calling on bb_team which i eliminated in 0.6b...
20080729 - 0.7.1b - re-ordered the champions list to order, team name
20080730 - 1.0 - bump to Version 1 for public release.
20090124 - 1.1 - Players who are still acive appear bold in the top players list. a caption also appears explaining the boldness!
20090330 - 1,2 - Editied to filter out non hdwsbbl details
20090606 - 1.2.1 - editied the teams summery to remove the "To Be Determined" Team

*/
?>
<?php get_header(); ?>
	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
		<div id="breadcrumb">
			<p><a href="<?php echo get_option('home'); ?>" title="Back to the front of the HDWSBBL">HDWSBBL</a> &raquo; <?php the_title(); ?></p>
		</div>
			<div class="entry">
				<h2><?php the_title(); ?></h2>

				<?php the_content('Read the rest of this entry &raquo;'); ?>

<?php
				$matchnumsql = 'SELECT COUNT(*) AS MATCHNUM FROM bb_match M, bb_comp C, bb2wp J, '.$wpdb->posts.' P WHERE M.c_id = C.c_id AND C.c_counts = 1 AND C.c_show = 1 AND C.type_id = 1 AND M.m_id = J.tid AND J.prefix = \'m_\' AND J.pid = P.ID';
				$matchnum = $wpdb->get_var($matchnumsql);
				$compnumsql = 'SELECT COUNT(*) AS compnum FROM bb_comp M, bb2wp J, '.$wpdb->posts.' P WHERE M.c_counts = 1 AND M.c_show = 1 AND M.type_id = 1 AND M.c_id = J.tid AND J.prefix = \'c_\' AND J.pid = P.ID';
				$compnum = $wpdb->get_var($compnumsql);
				$cupnumsql = 'SELECT COUNT(*) AS cupnum FROM bb_series M, bb2wp J, '.$wpdb->posts.' P WHERE M.series_id = J.tid AND M.series_show = 1 AND J.prefix = \'series_\' AND J.pid = P.ID';
				$cupnum = $wpdb->get_var($cupnumsql);
				$playernumsql = 'SELECT COUNT(*) AS playernum FROM bb_player M, bb_team T, bb2wp J, '.$wpdb->posts.' P WHERE M.t_id = T.t_id AND T.t_show = 1 AND T.type_id = 1 AND M.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = P.ID';
				$playernum = $wpdb->get_var($playernumsql);
				$teamnumsql = 'SELECT COUNT(*) AS teamnum FROM bb_team M, bb2wp J, '.$wpdb->posts.' P WHERE M.t_show = 1 AND T.type_id = 1 AND M.t_id = J.tid AND J.prefix = \'t_\' AND J.pid = P.ID';
				$teamnum = $wpdb->get_var($teamnumsql);
				$seanumsql = 'SELECT COUNT(*) AS seanum FROM bb_season M, bb2wp J, '.$wpdb->posts.' P WHERE M.sea_id = J.tid AND J.prefix = \'sea_\' AND J.pid = P.ID';
				$seanum = $wpdb->get_var($seanumsql);
				$sppnumsql = 'SELECT SUM(M.p_spp) AS sppnum FROM bb_player M, bb2wp J, '.$wpdb->posts.' P, bb_team T WHERE T.t_id = M.m_id AND T.type_id = 1 AND M.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = P.ID';
				$sppnum = $wpdb->get_var($sppnumsql);

				$matchstatssql = 'SELECT SUM(M.m_tottd) AS TD, SUM(M.m_totcas) AS CAS, SUM(M.m_totcomp) AS COMP, SUM(M.m_totint) AS MINT FROM bb_match M, bb_comp C WHERE M.c_id = C.c_id AND C.c_counts = 1 AND C.c_show = 1 AND C.type_id = 1';
				if ($matchstats = $wpdb->get_results($matchstatssql)) {
					foreach ($matchstats as $ms) {
						$tottd = $ms->TD;
						$totcas = $ms->CAS;
						$totcomp = $ms->COMP;
						$totint = $ms->MINT;
					}
				}
?>

				<h3>Overall Statistics</h3>

				<p>Since the <strong>HDWSBBL's</strong> inception, <strong><?php print($playernum); ?></strong> Players in <strong><?php print($teamnum); ?></strong> Teams have played <strong><?php print($matchnum); ?></strong> Matches in <strong><?php print($compnum); ?></strong> Competitions for <strong><?php print($cupnum); ?></strong> Championship Cups over <strong><?php print($seanum); ?></strong> Seasons. In total they have managed to:</p>
				<ul>
					<li>Score <strong><?php print($tottd); ?></strong> Touchdowns;</li>
					<li>Make <strong><?php print($totcomp); ?></strong> successful Completions;</li>
					<li>Cause <strong><?php print($totcas); ?></strong> Casualties;</li>
					<li>Catch <strong><?php print($totint); ?></strong> Interceptions;</li>
					<li>Earn a total of <strong><?php print($sppnum); ?></strong> Star Player Points.</li>
				</ul>

				<h3>HDWSBBL Cup Winners</h3>
<?php
				$championssql = 'SELECT COUNT(A.a_name) AS ANUM, P.post_title, P.guid FROM bb_awards_team_comp T, bb_awards A, bb2wp J, '.$wpdb->posts.' P, bb_comp C WHERE T.c_id = C.c_id AND T.t_id = J.tid AND J.prefix = \'t_\' AND J.pid = P.ID AND A.a_id = 1 AND A.a_id = T.a_id AND C.type_id = 1 GROUP BY T.t_id ORDER BY ANUM DESC, P.post_title ASC';
				if ($champions = $wpdb->get_results($championssql)) {
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
?>



				<h3>Statistics Breakdown by Season</h3>
<?php
				//$seasonsql = 'SELECT S.sea_name, COUNT(m_id)AS NUMMAT, SUM(M.m_tottd) AS TD, SUM(M.m_totcas) AS CAS, SUM(M.m_totcomp) AS COMP, SUM(M.m_totint) AS MINT FROM bb_match M, bb_season S, bb_comp C WHERE M.c_id = C.c_id AND C.sea_id = S.sea_id AND C.c_counts = 1 GROUP BY S.sea_name ORDER BY S.sea_id DESC';
				$seasonsql = 'SELECT O.post_title, O.guid, COUNT(m_id)AS NUMMAT, SUM(M.m_tottd) AS TD, SUM(M.m_totcas) AS CAS, SUM(M.m_totcomp) AS COMP, SUM(M.m_totint) AS MINT FROM bb_match M, bb_season S, bb_comp C, bb2wp J, '.$wpdb->posts.' O WHERE S.sea_id = J.tid AND J.prefix = \'sea_\' AND J.pid = O.ID AND M.c_id = C.c_id AND C.sea_id = S.sea_id AND C.c_counts = 1 AND C.c_show = 1 AND C.type_id = 1 GROUP BY S.sea_name ORDER BY S.sea_id DESC';
				if ($seasonstats = $wpdb->get_results($seasonsql)) {
					print("<table class=\"sortable\">\n	<thead>\n	<tr>\n		<th class=\"tbl_name\">Season</th>\n		<th class=\"tbl_stat\">Games</th>\n		<th class=\"tbl_stat\">TD</th>\n		<th class=\"tbl_stat\">CAS</th>\n		<th class=\"tbl_stat\">COMP</th>\n		<th class=\"tbl_stat\">INT</th>\n	</tr>\n	</thead>\n	<tbody>\n");
					$zebracount = 1;
					foreach ($seasonstats as $ss) {
						if ($zebracount % 2) {
							print("		<tr>\n");
						}
						else {
							print("		<tr class=\"tbl_alt\">\n");
						}
						print("		<td><a href=\"".$ss->guid."\" title=\"Read more about ".$ss->post_title."\">".$ss->post_title."</a></td>\n		<td>".$ss->NUMMAT."</td>\n		<td>".$ss->TD."</td>\n		<td>".$ss->CAS."</td>\n		<td>".$ss->COMP."</td>\n		<td>".$ss->MINT."</td>\n	</tr>\n");
						$zebracount++;
					}
					print("</tbody>\n</table>\n");

				}
?>
				<h3>Statistics Breakdown by Competition</h3>
<?php
				$compsql = 'SELECT P.post_title, P.guid, COUNT(m_id)AS NUMMAT, SUM(M.m_tottd) AS TD, SUM(M.m_totcas) AS CAS, SUM(M.m_totcomp) AS COMP, SUM(M.m_totint) AS MINT FROM bb_match M, bb_comp C, bb2wp J, '.$wpdb->posts.' P WHERE C.c_id = J.tid AND J.prefix = \'c_\' AND J.pid = P.ID AND M.c_id = C.c_id AND C.c_counts = 1 AND C.c_show = 1 AND C.type_id = 1 GROUP BY C.c_id ORDER BY C.c_sdate DESC';
				if ($compstats = $wpdb->get_results($compsql)) {
					print("<table class=\"sortable\">\n	<thead>\n	<tr>\n		<th class=\"tbl_name\">Competition</th>\n		<th class=\"tbl_stat\">Games</th>\n		<th class=\"tbl_stat\">TD</th>\n		<th class=\"tbl_stat\">CAS</th>\n		<th class=\"tbl_stat\">COMP</th>\n		<th class=\"tbl_stat\">INT</th>\n	</tr>\n	</thead>\n	<tbody>\n");
					$zebracount = 1;
					foreach ($compstats as $ss) {
						if ($zebracount % 2) {
							print("		<tr>\n");
						}
						else {
							print("		<tr class=\"tbl_alt\">\n");
						}
						print("		<td><a href=\"".$ss->guid."\" title=\"Read more on ".$ss->post_title."\">".$ss->post_title."</a></td>\n		<td>".$ss->NUMMAT."</td>\n		<td>".$ss->TD."</td>\n		<td>".$ss->CAS."</td>\n		<td>".$ss->COMP."</td>\n		<td>".$ss->MINT."</td>\n	</tr>\n");
						$zebracount++;
					}
					print("	</tbody>\n</table>\n");

				}
?>

<!--	<h3>Statistics Relating to Touchdowns</h3>
	<ul>
	   <li><strong>Most Touchdowns scored in a match (both teams):</strong> X</li>
<?php
/*		//abandoned for now. alternative might be to capture the first, pop it out and chaeck for matching entries.
		$topteamssql = 'SELECT P.post_title, M.m_tottd AS VALUE, UNIX_TIMESTAMP(M.m_date) AS mdate, P.guid, M.m_teamAtd, M.m_teamBtd FROM bb_match M, bb2wp J, '.$wpdb->posts.' P WHERE M.m_id = J.tid AND J.prefix = \'m_\' AND J.pid = P.ID ORDER BY VALUE DESC, mdate DESC LIMIT 0, 15 ';
		$topteams = $wpdb->get_results($topteamssql, ARRAY_N);
		print("<li><pre>");
		print_r($topteams);
		print("</pre></li>\n");

		//Load an array to contan the top scores.
		$topvalue = array();
		$topvalue['value'] = $topteams[0][1];
		$topvalue['match'] = "<a href=\"".$topteams[0][3]."\" title=\"View more details on this match\">".$topteams[0][0]." (".$topteams[0][4]." - ".$topteams[0][5].")</a>";

		$loopc = 1;
		while ($loopc < 15) {
			print("<li>this (".$loopc.") - ".$topteams[$loopc][1]."</li>");
			if ($topvaue['value'] == $topteams[$loopc][1]) {
				$topvalue['match'] .= ", <a href=\"".$topteams[$loopc][3]."\" title=\"View more details on this match\">".$topteams[$loopc][0]." (".$topteams[$loopc][4]." - ".$topteams[$loopc][5].")</a>";
			}
			else {
				print("<li>Don't match? (".$topteams[$loopc][1]." vs ".$topvalue['value'].")</li>");
			}
		$loopc++;
		}

		print("<li><pre>");
		print_r($topvalue);
		print("</pre></li>\n");*/
?>
	   <li><strong>Most Touchdowns scored in a match (single team):</strong> X</li>
	   <li><strong>Most Touchdowns scored in a match (single player):</strong> X</li>
	</ul>

	<h3>Statistics Relating to Casulties</h3>
	<ul>
	   <li><strong>Most Casulties caused in a match (both teams):</strong> X</li>
	   <li><strong>Most Casulties caused in a match (single team):</strong> X</li>
	   <li><strong>Most Casulties caused in a match (single player):</strong> X</li>
	</ul>

	<h3>Statistics Relating to Completions</h3>
	<ul>
	   <li><strong>Most passes completed in a match (both teams):</strong> X</li>
	   <li><strong>Most passes completed in a match (single team):</strong> X</li>
	   <li><strong>Most passes completed in a match (single player):</strong> X</li>
	</ul>

	<h3>Miscellaneous Statistics</h3>
	<ul>
	   <li><strong>Biggest Loss:</strong> X</li>
	   <li><strong>Biggest Win:</strong> X</li>
	   <li><strong>Longest Winning Streak:</strong> X games</li>
	   <li><strong>Longest Losing Streak:</strong> X games</li>
	   <li><strong>Most Star Player points Awarded in a match:</strong> X</li>
	   <li><strong>Largest Recorded Team Value:</strong> Xgp</li>
	   <li><strong>Most Expensive Player:</strong> Xgp</li>
	   <li><strong>Highest attendance (League):</strong> X</li>
	   <li><strong>Highest attendance (Tournament):</strong> X</li>
	   <li><strong>Average Attendance (League):</strong> X</li>
	   <li><strong>Average Attendance (Tournament):</strong> X</li>
	   <li><strong>Highest Attendance for a single team (in one match):</strong> X</li>
	   <li><strong>Highest Attendance for a single team (Average):</strong> X</li>
	   <li><strong>Team with biggest following (FF):</strong> X</li>
	</ul>
-->

				<h3>Statistics Breakdown by Teams</h3>
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
				//$teamstatssql = 'SELECT P.post_title, SUM(T.tc_played) AS TP, SUM(T.tc_W) AS TW, SUM(T.tc_L) AS TL, SUM(T.tc_D) AS TD, SUM(T.tc_tdfor) AS TDF, SUM(T.tc_tdagst) AS TDA, SUM(T.tc_casfor) AS TCF, SUM(T.tc_casagst) AS TCA, SUM(T.tc_INT) AS TI, SUM(T.tc_comp) AS TC, P.guid FROM bb_team_comp T, bb2wp J, '.$wpdb->posts.' P, bb_comp C WHERE C.c_id = T.c_id AND C.c_counts = 1 AND C.c_show = 1 AND C.type_id = 1 AND T.t_id = J.tid AND J.prefix = \'t_\' AND J.pid = P.ID GROUP BY T.t_id ORDER BY P.post_title ASC LIMIT 0, 30 ';
				$teamstatssql = 'SELECT P.post_title, SUM(T.tc_played) AS TP, SUM(T.tc_W) AS TW, SUM(T.tc_L) AS TL, SUM(T.tc_D) AS TD, SUM(T.tc_tdfor) AS TDF, SUM(T.tc_tdagst) AS TDA, SUM(T.tc_casfor) AS TCF, SUM(T.tc_casagst) AS TCA, SUM(T.tc_INT) AS TI, SUM(T.tc_comp) AS TC, P.guid FROM bb_team_comp T, bb2wp J, '.$wpdb->posts.' P, bb_comp C, bb_team Z WHERE Z.t_id = T.t_id AND Z.t_show = 1 AND C.c_id = T.c_id AND C.c_counts = 1 AND C.c_show = 1 AND C.type_id = 1 AND T.t_id = J.tid AND J.prefix = \'t_\' AND J.pid = P.ID GROUP BY T.t_id ORDER BY P.post_title ASC';
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

				<h3 id="statstable">Statistics Breakdown by Players</h3>
<?php
				$options = get_option('bblm_config');
				$stat_limit = htmlspecialchars($options['display_stats'], ENT_QUOTES);

				$MASTERSQL = 'SELECT Y.post_title, T.t_name, SUM(M.mp_td) AS TD, SUM(M.mp_cas) AS CAS, SUM(M.mp_comp) AS COMP, SUM(M.mp_int) AS MINT, SUM(M.mp_mvp) AS MVP, P.p_spp AS SPP, Y.guid, X.pos_name FROM bb_player P, bb_team T, bb_match_player M, bb_comp C, bb_match X, bb2wp J, '.$wpdb->posts.' Y, bb_position X WHERE P.pos_id = X.pos_id AND P.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = Y.ID AND M.m_id = X.m_id AND X.c_id = C.c_id AND C.c_counts = 1 AND C.type_id = 1 AND M.p_id = P.p_id AND P.t_id = T.t_id AND P.p_spp > 0 GROUP BY P.p_id ORDER BY SPP DESC LIMIT '.$stat_limit;


			/*	$topsppsql = 'SELECT Y.post_title, T.t_name, SUM(M.mp_spp) AS SPP, Y.guid FROM bb_player P, bb_team T, bb_match_player M, bb_comp C, bb_match X, bb2wp J, '.$wpdb->posts.' Y WHERE P.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = Y.ID AND M.m_id = X.m_id AND X.c_id = C.c_id AND C.c_counts = 1 AND M.p_id = P.p_id AND P.t_id = T.t_id AND P.p_spp > 0 GROUP BY P.p_id ORDER BY SPP DESC LIMIT '.$stat_limit;
				if ($topspp = $wpdb->get_results($topsppsql)) {
					print("<h4>Top Players</h4>\n");
					print("<table>\n	</tr>\n		<th>#</th>\n		<th>Player</th>\n		<th>Team</th>\n		<th>value</th>\n		</tr>\n");
					$pcount = 1;

					foreach ($topspp as $ts) {
						print("	</tr>\n		<td>".$pcount."</td>\n		<td><a href=\"".$ts->guid."\" title=\"View more details on ".$ts->post_title."\">".$ts->post_title."</a></td>\n		<td>".$ts->t_name."</td>\n		<td>".$ts->SPP."</td>\n	</tr>\n");
						$pcount++;
					}
					print("</table>\n");
				}

				$toptdql = 'SELECT Y.post_title, T.t_name, SUM(M.mp_td) AS TD, Y.guid FROM bb_player P, bb_team T, bb_match_player M, bb_comp C, bb_match X, bb2wp J, '.$wpdb->posts.' Y WHERE P.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = Y.ID AND M.m_id = X.m_id AND X.c_id = C.c_id AND C.c_counts = 1 AND M.p_id = P.p_id AND P.t_id = T.t_id AND P.p_spp > 0 GROUP BY P.p_id ORDER BY TD DESC LIMIT '.$stat_limit;
				if ($toptd = $wpdb->get_results($toptdql)) {
					print("<h4>Top Scorers</h4>\n");
					print("<table>\n	</tr>\n		<th>#</th>\n		<th>Player</th>\n		<th>Team</th>\n		<th>value</th>\n		</tr>\n");
					$pcount = 1;

					foreach ($toptd as $ts) {
						print("	</tr>\n		<td>".$pcount."</td>\n		<td><a href=\"".$ts->guid."\" title=\"View more details on ".$ts->post_title."\">".$ts->post_title."</a></td>\n		<td>".$ts->t_name."</td>\n		<td>".$ts->TD."</td>\n	</tr>\n");
						$pcount++;
					}
					print("</table>\n");
				}*/


				//Formation of Generic Query
				//--------------------------
/*				if(isset($_POST['bblm_filter_submit'])) {
					print("<hr />\n<pre>\n");
					print_r($_POST);
					print("</pre>\n<hr />\n");
				}*/

				$statsql = 'SELECT Y.post_title, Y.guid, SUM(';

				//determine the stat we are looking up
				if (isset($_POST['bblm_stat'])) {
					$stat = $_POST['bblm_stat'];

					switch ($stat) {
						case ("spp" == $stat):
					    	$statsql .= 'M.mp_spp';
					    	$title = "Best Players";
						    break;
						case ("td" == $stat):
					    	$statsql .= 'M.mp_td';
					    	$title = "Top Scorers";
						    break;
						case ("comp" == $stat):
					    	$statsql .= 'M.mp_comp';
					    	$title = "Top Passers";
						    break;
						case ("cas" == $stat):
					    	$statsql .= 'M.mp_cas';
					    	$title = "Most Vicious";
						    break;
						case ("gint" == $stat):
					    	$statsql .= 'M.mp_int';
					    	$title = "Top Inteceptors";
						    break;
						case ("mvp" == $stat):
					    	$statsql .= 'M.mp_mvp';
					    	$title = "Most Valuable Players";
						    break;
						default:
					    	$statsql .= 'M.mp_spp';
					    	$title = "Best Players";
						    break;
					}
				}
				else {
					//form not submitted so load in default values
					    	$statsql .= 'M.mp_spp';
					    	$title = "Best Players";
				}


				$statsql .= ') AS VALUE, R.pos_name, O.post_title AS TEAM, O.guid AS TEAMLink, T.t_active, P.p_status FROM bb_player P, bb_match_player M, bb_comp C, bb_match X, bb2wp J, '.$wpdb->posts.' Y, bb_position R, bb2wp I, '.$wpdb->posts.' O, bb_team T WHERE ';
				//$statsql .= 'C.c_id = 9 AND ';
				//$statsql .= 'C.sea_id = 2 AND ';

				//the default is to show the stats for all time (this comes into pay when showing active players
				$period_alltime = 1;

				//determine the status we are looking up
				if (isset($_POST['bblm_status'])) {
					$status = $_POST['bblm_status'];
					//note that the sql is only modified if the "active" option is selected
					switch ($status) {
						case ("active" == $status):
					    	$statsql .= 'P.p_status = 1 AND T.t_active = 1 AND ';
					    	$period_alltime = 0;
						    break;
					}
				}

				$statsql .= 'P.t_id = T.t_id AND R.pos_id = P.pos_id AND P.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = Y.ID AND M.m_id = X.m_id AND X.c_id = C.c_id AND C.c_counts = 1 AND C.c_show = 1 AND P.t_id = I.tid AND I.prefix = \'t_\' AND I.pid = O.ID AND M.p_id = P.p_id AND P.p_spp > 0 GROUP BY P.p_id ORDER BY VALUE DESC LIMIT '.$stat_limit;
?>
				<form name="bblm_filterstats" method="post" id="post" action="#statstable">
				<p>Show
					<select name="bblm_stat" id="bblm_stat">
						<option value="spp"<?php if (spp == $_POST['bblm_stat']) { print(" selected=\"selected\""); } ?>>Best all Round</option>
						<option value="td"<?php if (td == $_POST['bblm_stat']) { print(" selected=\"selected\""); } ?>>Top Scoring</option>
						<option value="cas"<?php if (cas == $_POST['bblm_stat']) { print(" selected=\"selected\""); } ?>>Most Vicious</option>
						<option value="comp"<?php if (comp == $_POST['bblm_stat']) { print(" selected=\"selected\""); } ?>>Best Passing</option>
						<option value="gint"<?php if (gint == $_POST['bblm_stat']) { print(" selected=\"selected\""); } ?>>Top Intecepting</option>
						<option value="mvp"<?php if (mvp == $_POST['bblm_stat']) { print(" selected=\"selected\""); } ?>>Most Valuable</option>
					</select>
				players of
					<select name="bblm_status" id="bblm_status">
						<option value="alltime"<?php if (alltime == $_POST['bblm_status']) { print(" selected=\"selected\""); } ?>>All Time</option>
						<option value="active"<?php if (active == $_POST['bblm_status']) { print(" selected=\"selected\""); } ?>>Active</option>
					</select>
				<input name="bblm_filter_submit" type="submit" id="bblm_filter_submit" value="Filter" /></p>
				</form>

<?php
				if ($topstats = $wpdb->get_results($statsql)) {
					print("<h4>".$title."</h4>\n");
					if ($period_alltime) {
						print("	<p>Any Payer names in <strong>bold</strong> indicates that the player is still acive in the HDWSBBL</p>\n");
					}
					print("<table class=\"expandable\">\n	<tr>\n		<th class=\"tbl_stat\">#</th>\n		<th class=\"tbl_name\">Player</th>\n		<th>Position</th>\n		<th class=\"tbl_name\">Team</th>\n		<th class=\"tbl_stat\">Value</th>\n	</tr>\n");
					$pcount = 1;
					$zebracount = 1;
					$prevvalue = 0;

					foreach ($topstats as $ts) {
						if ($ts->VALUE > 0) {
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
							if ($ts->VALUE > 0) {
								if ($prevvalue == $ts->VALUE) {
									print("	<td>-</td>\n");
								}
								else {
									print("	<td><strong>".$zebracount."</strong></td>\n");
								}
								if ($ts->t_active && $ts->p_status && $period_alltime) {
									//here we chack to see if the player is stil acitve. If they are then the entry goes bold!
									print("	<td><strong><a href=\"".$ts->guid."\" title=\"View more details on ".$ts->post_title."\">".$ts->post_title."</a></strong></td>\n	<td>".$ts->pos_name."</td>\n	<td><a href=\"".$ts->TEAMLink."\" title=\"Read more on this team\">".$ts->TEAM."</a></td>\n	<td>".$ts->VALUE."</td>\n	</tr>\n");
								}
								else {
									print("	<td><a href=\"".$ts->guid."\" title=\"View more details on ".$ts->post_title."\">".$ts->post_title."</a></td>\n	<td>".$ts->pos_name."</td>\n	<td><a href=\"".$ts->TEAMLink."\" title=\"Read more on this team\">".$ts->TEAM."</a></td>\n	<td>".$ts->VALUE."</td>\n	</tr>\n");
								}
								$prevvalue = $ts->VALUE;
							}
							$zebracount++;
						}
						$pcount++;
					}
					print("</table>\n");
				}
				else {
					//print("<p>".$statsql."</p>");
					print("	<div class=\"info\">\n		<p>There are no recorded vlues for that selection. Please try another</p>\n	</div>\n");
				}



		//Did You Know Display Code
		if (function_exists(bblm_display_dyk)) {
			bblm_display_dyk();
		}
?>
				<p class="postmeta"><?php edit_post_link('Edit', ' <strong>[</strong> ', ' <strong>]</strong> '); ?></p>

			</div>


		<?php endwhile;?>
	<?php endif; ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>