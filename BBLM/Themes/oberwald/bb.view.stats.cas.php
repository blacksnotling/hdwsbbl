<?php
/*
Template Name: Statistics - CAS
*/
/*
*	Filename: bb.view.stats.cas.php
*	Description: .CAS Related Stats
*/
?>
<?php get_header(); ?>
	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
		<div id="breadcrumb">
			<p><a href="<?php echo home_url(); ?>" title="Back to the front of the HDWSBBL">HDWSBBL</a> &raquo; <a href="<?php echo home_url(); ?>/stats" title="Back to the main Statistics page">Statistics</a> &raquo; <?php the_title(); ?></p>
		</div>
			<div class="entry">
				<h2><?php the_title(); ?></h2>

				<?php the_content('Read the rest of this entry &raquo;'); ?>
<?php
		 /*-- Stats part A -- */
		 $mostxteamseasonsql = 'SELECT A.ats_value AS VALUE, T.t_name AS TEAM, S.post_title, S.guid, T.t_guid AS TEAMLink FROM '.$wpdb->prefix.'awards_team_sea A, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' S WHERE A.sea_id = J.tid AND J.prefix = \'sea_\' AND J.pid = S.ID AND A.t_id = T.t_id AND T.type_id = 1 AND A.a_id = 12 ORDER BY VALUE DESC, A.sea_id ASC LIMIT 1';
		 $mxts = $wpdb->get_row($mostxteamseasonsql);
		 $mostxplayerseasonsql = 'SELECT A.aps_value AS VALUE, L.post_title AS PLAYER, L.guid AS PLAYERLink, T.t_name AS TEAM, S.post_title, S.guid, T.t_guid AS TEAMLink, X.pos_name FROM '.$wpdb->prefix.'awards_player_sea A, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' S, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'bb2wp K, '.$wpdb->posts.' L, '.$wpdb->prefix.'position X WHERE P.pos_id = X.pos_id AND P.p_id = K.tid AND K.prefix = \'p_\' AND K.pid = L.ID AND A.sea_id = J.tid AND J.prefix = \'sea_\' AND J.pid = S.ID AND A.p_id = P.p_id AND P.t_id = T.t_id AND T.type_id = 1 AND A.a_id = 12 ORDER BY VALUE DESC, A.sea_id ASC LIMIT 1';
		 $mxps = $wpdb->get_row($mostxplayerseasonsql);
		 $mostxteamcompsql = 'SELECT A.atc_value AS VALUE, T.t_name AS TEAM, S.post_title, S.guid, T.t_guid AS TEAMLink FROM '.$wpdb->prefix.'awards_team_comp A, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' S WHERE A.c_id = J.tid AND J.prefix = \'c_\' AND J.pid = S.ID AND A.t_id = T.t_id AND T.type_id = 1 AND A.a_id = 12 ORDER BY VALUE DESC, A.c_id ASC LIMIT 1';
		 $mxtc = $wpdb->get_row($mostxteamcompsql);
		 $mostxplayercompsql = 'SELECT A.apc_value AS VALUE, L.post_title AS PLAYER, L.guid AS PLAYERLink, T.t_name AS TEAM, S.post_title, S.guid, T.t_guid AS TEAMLink, X.pos_name FROM '.$wpdb->prefix.'awards_player_comp A, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' S, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'bb2wp K, '.$wpdb->posts.' L, '.$wpdb->prefix.'position X WHERE P.pos_id = X.pos_id AND P.p_id = K.tid AND K.prefix = \'p_\' AND K.pid = L.ID AND A.c_id = J.tid AND J.prefix = \'c_\' AND J.pid = S.ID AND A.p_id = P.p_id AND P.t_id = T.t_id AND T.type_id = 1 AND A.a_id = 12 ORDER BY VALUE DESC, A.c_id ASC LIMIT 1';
		 $mxpc = $wpdb->get_row($mostxplayercompsql);
		 $mostxteammatchsql = 'SELECT T.t_name AS TEAM, T.t_guid AS TEAMLink, M.mt_cas AS VALUE, UNIX_TIMESTAMP(X.m_date) AS MDATE FROM '.$wpdb->prefix.'team T, '.$wpdb->prefix.'match_team M, '.$wpdb->prefix.'match X, '.$wpdb->prefix.'comp C WHERE T.t_id = M.t_id AND M.m_id = X.m_id AND C.c_id = X.c_id AND C.c_counts = 1 AND C.c_show = 1 AND C.type_id = 1 AND M.mt_cas > 0 ORDER BY VALUE DESC, M.m_id ASC LIMIT 1';
		 $mxtm = $wpdb->get_row($mostxteammatchsql);
		 $mostxplayermatchsql = 'SELECT Y.post_title AS PLAYER, T.t_name AS TEAM, T.t_guid AS TEAMLink, Y.guid AS PLAYERLink, M.mp_cas AS VALUE, R.pos_name, UNIX_TIMESTAMP(X.m_date) AS MDATE FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' Y, '.$wpdb->prefix.'position R, '.$wpdb->prefix.'match X, '.$wpdb->prefix.'comp C  WHERE M.m_id = X.m_id AND C.c_id = X.c_id AND C.c_counts = 1 AND C.c_show = 1 AND C.type_id = 1 AND P.pos_id = R.pos_id AND P.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = Y.ID AND M.p_id = P.p_id AND P.t_id = T.t_id AND M.mp_counts = 1 AND M.mp_cas > 0 ORDER BY VALUE DESC, M.m_id ASC LIMIT 1';
		 $mxpm = $wpdb->get_row($mostxplayermatchsql);
?>
		<ul>
			<li><strong>Most Casualties caused in a Season (Team)</strong>: <?php print($mxts->VALUE); ?> (<a href="<?php print($mxts->TEAMLink); ?>" title="Learn more about this Team"><?php print($mxts->TEAM); ?></a> - <a href="<?php print($mxts->guid); ?>" title="Read more on this Season"><?php print($mxts->post_title); ?></a>)</li>
			<li><strong>Most Casualties caused in a Season (Player)</strong>: <?php print($mxps->VALUE); ?> (<a href="<?php print($mxps->PLAYERLink); ?>" title="See more on this Player"><?php print($mxps->PLAYER); ?></a> - <?php print($mxps->pos_name); ?> for <a href="<?php print($mxps->TEAMLink); ?>" title="Learn more about this Team"><?php print($mxps->TEAM); ?></a> - <a href="<?php print($mxps->guid); ?>" title="Read more on this Season"><?php print($mxps->post_title); ?></a>)</li>
			<li><strong>Most Casualties caused in a Competition (Team)</strong>: <?php print($mxtc->VALUE); ?> (<a href="<?php print($mxtc->TEAMLink); ?>" title="Learn more about this Team"><?php print($mxtc->TEAM); ?></a> - <a href="<?php print($mxtc->guid); ?>" title="Read more on this Competition"><?php print($mxtc->post_title); ?></a>)</li>
			<li><strong>Most Casualties caused in a Competition (Player)</strong>: <?php print($mxpc->VALUE); ?> (<a href="<?php print($mxpc->PLAYERLink); ?>" title="See more on this Player"><?php print($mxpc->PLAYER); ?></a> - <?php print($mxpc->pos_name); ?> for <a href="<?php print($mxpc->TEAMLink); ?>" title="Learn more about this Team"><?php print($mxpc->TEAM); ?></a> - <a href="<?php print($mxpc->guid); ?>" title="Read more on this Competition"><?php print($mxpc->post_title); ?></a>)</li></li>
			<li><strong>Most Casualties caused in a Match (Team)</strong>: <?php print($mxtm->VALUE); ?> (<a href="<?php print($mxtm->TEAMLink); ?>" title="Learn more about this Team"><?php print($mxtm->TEAM); ?></a> - <?php print(date("d.m.25y", $mxtm->MDATE)); ?>)</li>
			<li><strong>Most Casualties caused in a Match (Player)</strong>: <?php print($mxpm->VALUE); ?> (<a href="<?php print($mxpm->PLAYERLink); ?>" title="See more on this Player"><?php print($mxpm->PLAYER); ?></a> - <?php print($mxpm->pos_name); ?> for <a href="<?php print($mxtm->TEAMLink); ?>" title="Learn more about this Team"><?php print($mxpm->TEAM); ?></a> - <?php print(date("d.m.25y", $mxpm->MDATE)); ?>)</li>
		</ul>

<?php
		 /*-- Stats part B -- */
?>
<!--		<ul>
			<li><strong>Teams who suffered most Deaths</strong>: <a href="#" title="Learn more about this Team">TEAM</a> (X)</li>
 			<li><strong>Teams who suffered most injuries</strong>: <a href="#" title="Learn more about this Team">TEAM</a> (X)</li>
 			<li><strong>Player who suffered most injuries</strong>: <a href="#" title="See more on this Player">PLAYER</a> (X - position for <a href="#" title="Learn more about this Team">TEAM</a>)</li>
 		</ul>

 			<h3>Players who Died on debut</h3>
 			<p>Not all players have an illustrious career. Here is a list of players who died on the debut:</p> -->


			<h3>Statistics tables</h3>
<?php
				  ///////////////////////////////
				 // Filtering of Stats tables //
				///////////////////////////////

				$options = get_option('bblm_config');
				$stat_limit = htmlspecialchars($options['display_stats'], ENT_QUOTES);
				$bblm_star_team = htmlspecialchars($options['team_star'], ENT_QUOTES);

				//the default is to show the stats for all time (this comes into pay when showing active players
				$period_alltime = 1;

				//determine the status we are looking up
				if (isset($_POST['bblm_status'])) {
					$status = $_POST['bblm_status'];
					//note that the sql is only modified if the "active" option is selected
					switch ($status) {
						case ("active" == $status):
					    	$statsqlmodp .= 'AND T.t_active = 1 AND P.p_status = 1 ';
					    	$statsqlmodt .= 'AND Z.t_active = 1 ';
					    	$period_alltime = 0;
						    break;
					}
				}
?>
				<form name="bblm_filterstats" method="post" id="statstable" action="#statstable">
				<p>For the below Statistics tables, show the records for
					<select name="bblm_status" id="bblm_status">
						<option value="alltime"<?php if (alltime == $_POST['bblm_status']) { print(" selected=\"selected\""); } ?>>All Time</option>
						<option value="active"<?php if (active == $_POST['bblm_status']) { print(" selected=\"selected\""); } ?>>Active Players / Teams</option>
					</select>
				<input name="bblm_filter_submit" type="submit" id="bblm_filter_submit" value="Filter" /></p>
				</form>

<?php
				  //////////////////////////
				 // Most VIcious Players //
				//////////////////////////
				$statsql = 'SELECT Y.post_title, T.t_name AS TEAM, T.t_guid AS TEAMLink, Y.guid, SUM(M.mp_cas) AS VALUE, R.pos_name, P.p_status, T.t_active FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' Y, '.$wpdb->prefix.'position R WHERE P.pos_id = R.pos_id AND P.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = Y.ID AND M.p_id = P.p_id AND P.t_id = T.t_id AND M.mp_counts = 1 AND M.mp_cas > 0 AND T.t_id != '.$bblm_star_team.' '.$statsqlmodp.'GROUP BY P.p_id ORDER BY VALUE DESC LIMIT '.$stat_limit;
				print("<h4>Most Vicious Players");
				if (0 == $period_alltime) {
					print(" (Active)");
				}
				print("</h4>\n");
				if ($topstats = $wpdb->get_results($statsql)) {
					if ($period_alltime) {
						print("	<p>Players who are <strong>highlighted</strong> are still active in the HDWSBBL.</p>\n");
					}
					print("<table class=\"expandable\">\n	<tr>\n		<th class=\"tbl_stat\">#</th>\n		<th class=\"tbl_name\">Player</th>\n		<th>Position</th>\n		<th class=\"tbl_name\">Team</th>\n		<th class=\"tbl_stat\">CAS</th>\n		</tr>\n");
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
							if ($ts->t_active && $ts->p_status && $period_alltime) {
								print("	<td><strong><a href=\"".$ts->guid."\" title=\"View more details on ".$ts->post_title."\">".$ts->post_title."</a></strong></td>\n");
							}
							else {
								print("	<td><a href=\"".$ts->guid."\" title=\"View more details on ".$ts->post_title."\">".$ts->post_title."</a></td>\n");
							}
							print("	<td>".$ts->pos_name."</td>\n	<td><a href=\"".$ts->TEAMLink."\" title=\"Read more on this team\">".$ts->TEAM."</a></td>\n	<td>".$ts->VALUE."</td>\n	</tr>\n");
							$prevvalue = $ts->VALUE;
						}
						$zebracount++;
					}
					print("</table>\n");
				}
				else {
					print("	<div class=\"info\">\n		<p>No players have caused any Casualties!</p>\n	</div>\n");
				}

				  ////////////////////////
				 // Most Vicious Teams //
				////////////////////////
				$statsql = 'SELECT Z.t_name AS TEAM, SUM(T.tc_casfor) AS VALUE, Z.t_guid AS TEAMLink, R.r_name, Z.t_active FROM '.$wpdb->prefix.'team_comp T, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'team Z, '.$wpdb->prefix.'race R WHERE R.r_id = Z.r_id AND Z.t_id = T.t_id AND Z.t_show = 1 AND C.c_id = T.c_id AND C.c_counts = 1 AND C.c_show = 1 AND Z.type_id = 1 '.$statsqlmodt.'GROUP BY T.t_id ORDER BY VALUE DESC LIMIT '.$stat_limit;
				print("<h4>Most Vicious Teams");
				if (0 == $period_alltime) {
					print(" (Active)");
				}
				print("</h4>\n");
				if ($topstats = $wpdb->get_results($statsql)) {
					if ($period_alltime) {
						print("	<p>Teams who are <strong>highlighted</strong> are still active in the HDWSBBL.</p>\n");
					}
					print("<table class=\"expandable\">\n	<tr>\n		<th class=\"tbl_stat\">#</th>\n		<th>Team</th>\n		<th class=\"tbl_name\">Race</th>\n		<th class=\"tbl_stat\">CAS</th>\n		</tr>\n");
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
							if ($ts->t_active && $period_alltime) {
							print("	<td><strong><a href=\"".$ts->TEAMLink."\" title=\"View more details on ".$ts->TEAM."\">".$ts->TEAM."</a></strong></td>\n");
							}
							else {
							print("	<td><a href=\"".$ts->TEAMLink."\" title=\"View more details on ".$ts->TEAM."\">".$ts->TEAM."</a></td>\n");
							}
							print("	<td>".$ts->r_name."</td>\n	<td>".$ts->VALUE."</td>\n	</tr>\n");
							$prevvalue = $ts->VALUE;
						}
						$zebracount++;
					}
					print("</table>\n");
				}
				else {
					print("	<div class=\"info\">\n		<p>No Teams have caused any Casualties!</p>\n	</div>\n");
				}

				  /////////////////////////
				 // Top Killing Players //
				/////////////////////////
				$statsql = 'SELECT O.post_title, O.guid, COUNT(*) AS VALUE , E.pos_name, T.t_name AS TEAM, T.t_guid AS TeamLink, P.p_status, T.t_active FROM `'.$wpdb->prefix.'player_fate` F, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' O, '.$wpdb->prefix.'position E, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'comp C WHERE P.t_id = T.t_id AND P.pos_id = E.pos_id AND P.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = O.ID AND (F.f_id = 1 OR F.f_id = 6) AND P.p_id = F.pf_killer AND F.m_id = M.m_id AND M.c_id = C.c_id AND C.type_id = 1 AND C.c_counts = 1 AND C.c_show = 1 AND T.t_id != '.$bblm_star_team.' '.$statsqlmodp.'GROUP BY F.pf_killer ORDER BY VALUE DESC LIMIT '.$stat_limit;
				print("<h4>Most Deadly Players");
				if (0 == $period_alltime) {
					print(" (Active)");
				}
				print("</h4>\n");
				if ($topstats = $wpdb->get_results($statsql)) {
					if ($period_alltime) {
						print("	<p>Players who are <strong>highlighted</strong> are still active in the HDWSBBL.</p>\n");
					}
					print("<table class=\"expandable\">\n	<tr>\n		<th class=\"tbl_stat\">#</th>\n		<th class=\"tbl_name\">Player</th>\n		<th>Position</th>\n		<th class=\"tbl_name\">Team</th>\n		<th class=\"tbl_stat\">Kills</th>\n		</tr>\n");
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
							if ($ts->t_active && $ts->p_status && $period_alltime) {
								print("	<td><strong><a href=\"".$ts->guid."\" title=\"View more details on ".$ts->post_title."\">".$ts->post_title."</a></strong></td>\n");
							}
							else {
								print("	<td><a href=\"".$ts->guid."\" title=\"View more details on ".$ts->post_title."\">".$ts->post_title."</a></td>\n");
							}
							print("	<td>".$ts->pos_name."</td>\n	<td><a href=\"".$ts->TEAMLink."\" title=\"Read more on this team\">".$ts->TEAM."</a></td>\n	<td>".$ts->VALUE."</td>\n	</tr>\n");
							$prevvalue = $ts->VALUE;
						}
						$zebracount++;
					}
					print("</table>\n");
				}
				else {
					print("	<div class=\"info\">\n		<p>No players have made any Kills!!</p>\n	</div>\n");
				}

				  ///////////////////////
				 // Top Killing Teams //
				///////////////////////
				$statsql = 'SELECT COUNT(*) AS VALUE , T.t_name AS TEAM, T.t_guid AS TeamLink, T.t_active, R.r_name FROM `'.$wpdb->prefix.'player_fate` F, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'race R WHERE T.r_id = R.r_id AND P.t_id = T.t_id AND (F.f_id = 1 OR F.f_id = 6) AND P.p_id = F.pf_killer AND F.m_id = M.m_id AND M.c_id = C.c_id AND C.type_id = 1 AND C.c_counts = 1 AND C.c_show = 1 '.$statsqlmodt.'GROUP BY T.t_id ORDER BY VALUE DESC, T.t_id ASC LIMIT '.$stat_limit;
				print("<h4>Most Deadly Teams");
				if (0 == $period_alltime) {
					print(" (Active)");
				}
				print("</h4>\n");
				if ($topstats = $wpdb->get_results($statsql)) {
					if ($period_alltime) {
						print("	<p>Teams who are <strong>highlighted</strong> are still active in the HDWSBBL.</p>\n");
					}
					print("<table class=\"expandable\">\n	<tr>\n		<th class=\"tbl_stat\">#</th>\n		<th>Team</th>\n		<th class=\"tbl_name\">Race</th>\n		<th class=\"tbl_stat\">Kills</th>\n		</tr>\n");
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
							if ($ts->t_active && $period_alltime) {
							print("	<td><strong><a href=\"".$ts->TEAMLink."\" title=\"View more details on ".$ts->TEAM."\">".$ts->TEAM."</a></strong></td>\n");
							}
							else {
							print("	<td><a href=\"".$ts->TEAMLink."\" title=\"View more details on ".$ts->TEAM."\">".$ts->TEAM."</a></td>\n");
							}
							print("	<td>".$ts->r_name."</td>\n	<td>".$ts->VALUE."</td>\n	</tr>\n");
							$prevvalue = $ts->VALUE;
						}
						$zebracount++;
					}
					print("</table>\n");
				}
				else {
					print("	<div class=\"info\">\n		<p>No teams have made any Kills!</p>\n	</div>\n");
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
