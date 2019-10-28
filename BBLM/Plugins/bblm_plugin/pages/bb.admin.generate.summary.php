<?php
/**
 * BBowlLeagueMan Generate Summary
 *
 * Generates the weekly summary
 *
 * @author 		Blacksnotling
 * @category 	Core
 * @package 	BBowlLeagueMan/Pages
 */

//Check the file is not being accessed directly
if (!function_exists('add_action')) die('You cannot run this file directly. Naughty Person');
?>
<div class="wrap">
	<h2>Generate Warzone Summary</h2>
	<p>This page generates the weekly Warzone summery. Take it slow and everything should be fine :-D</p>

<?php
if(isset($_POST['bblm_sum_create'])) {
	  //////////////////////////
	 // Step 4: Add to Database //
	//////////////////////////
/*		print("<pre>\n");
		print_r($_POST);
		print("</pre>\n");*/

		$bblm_safe_input = array();

		if (get_magic_quotes_gpc()) {
			$_POST['bblm_stitle'] = stripslashes($_POST['bblm_stitle']);
			$_POST['bblm_soutput'] = stripslashes($_POST['bblm_soutput']);
		}
		$bblm_safe_input['stitle'] = esc_sql( $_POST['bblm_stitle'] );
		$bblm_safe_input['scontent'] = esc_sql( $_POST['bblm_soutput'] );

		//generate time NOW.
		$bblm_date_now = date('Y-m-j H:i:59');

		//filter page body
		$bblm_removable = array("<p>","&nbsp;");
		$bblm_page_content = $bblm_safe_input['scontent'];
		$bblm_page_content = str_replace($bblm_removable,"",$bblm_page_content);
		$bblm_page_content = str_replace("</p>","\n\n",$bblm_page_content);

		//snaitise page title
		$bblm_page_title = "Season ".$_POST['bblm_sseano']." - Week ".$_POST['bblm_sweekno']." - ".$bblm_safe_input['stitle'];

		//convert page title to slug
		$bblm_page_slug = sanitize_title("s".$_POST['bblm_sseano']."-wk".$_POST['bblm_sweekno']."-".$bblm_safe_input['stitle']);

		//generate GUID
		$bblm_guid = home_url();
		$bblm_guid .= "/warzone/";
		$bblm_guid .= $bblm_page_slug;

		//determine logged in user
		global $user_ID;
      	get_currentuserinfo();

/*		print("<p>".$bblm_page_title."</p>");
		print("<p>".$bblm_page_slug."</p>");
		print("<p>".$bblm_guid."</p>");
		print($bblm_safe_input['scontent']); */

		//Finally, we construct the sql
		$postsql = 'INSERT INTO '.$wpdb->posts.' (`ID`, `post_author`, `post_date`, `post_date_gmt`, `post_content`, `post_title`, `post_category`, `post_excerpt`, `post_status`, `comment_status`, `ping_status`, `post_password`, `post_name`, `to_ping`, `pinged`, `post_modified`, `post_modified_gmt`, `post_content_filtered`, `post_parent`, `guid`, `menu_order`, `post_type`, `post_mime_type`, `comment_count`) VALUES (\'\', \''.$user_ID.'\', \''.$bblm_date_now.'\', \''.$bblm_date_now.'\', \''.$bblm_page_content.'\', \''.$bblm_page_title.'\', \'0\', \'\', \'draft\', \'open\', \'open\', \'\', \''.$bblm_page_slug.'\', \'\', \'\', \''.$bblm_date_now.'\', \''.$bblm_date_now.'\', \'\', \'0\', \''.$bblm_guid.'\', \'0\', \'post\', \'\', \'0\')';
		//print("<p>".$postsql."</p>");

		if (FALSE !== $wpdb->query($postsql)) {
			$bblm_post_number = $wpdb->insert_id;//captured from SQL string
		}

		$postmetasql = 'INSERT INTO '.$wpdb->postmeta.' (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES (\'\', \''.$bblm_post_number.'\', \'_edit_last\', \'1\')';

		if (FALSE !== $wpdb->query($postmetasql)) {
			$sucess = TRUE;
		}

		// Now we flush the re-write rules to make them regenerate the rules to include our new page.
		$wp_rewrite->flush_rules();
?>
	<div id="updated" class="updated fade">
		<p>
	<?php
	if ($sucess) {
		print("Summary has been saved!. You can now <a href=\"".home_url()."/wp-admin/post.php?action=edit&post=".$bblm_post_number."\" title=\"Edit the page\">edit the summary</a> before publishing it</p>");
		print("<p>Please remember to do the following:</p>\n<ul>	<li>Add the correct categories (<strong>Warzone</strong>, <strong>Summary</strong>, <strong>Season X</strong> and any relevant cups)</li>\n	<li>Please add a post excert for when it is displayed on the front screen.</li>\n</ul>");
	}
	else {
		print("Something went wrong");
	}
?>
		</p>
	</div>
<?php
}
else if(isset($_POST['bblm_gen_preview'])) {
	  //////////////////////////
	 // Step 3: Show Preview //
	//////////////////////////
/*		print("<pre>\n");
		print_r($_POST);
		print("</pre>\n");*/

	$sumoutput = "";
	// Start of Summary Generation //
	//dates and competitions
	$sumoutput .= "<ul>\n";
	$sumoutput .= "	<li><strong>Duration</strong>: ".date('jS M 25y',strtotime($_POST['bblm_sdatef']))." - ".date('jS M 25y',strtotime($_POST['bblm_sdatet']))."</li>\n";
	$sumoutput .= "	<li><strong>Competition(s)</strong>: ";

	$sumcompsql = 'SELECT DISTINCT M.c_id, P.post_title AS Comp, P.guid AS CompLink, C.sea_id AS Sea FROM '.$wpdb->prefix.'match M, '.$wpdb->posts.' P, '.$wpdb->prefix.'bb2wp J, '.$wpdb->prefix.'comp C WHERE M.c_id = C.c_id AND M.c_id = J.tid AND J.prefix = \'c_\' AND J.pid = P.ID AND M.m_date > \''.$_POST['bblm_sdatef'].'\' AND M.m_date < \''.$_POST['bblm_sdatet'].'\' ORDER BY P.post_title ASC';
	if ($sumcomp = $wpdb->get_results($sumcompsql,ARRAY_A)) {
		$is_followon = 0;
		foreach ($sumcomp as $sc) {
			if ($is_followon) {
				$sumoutput .= ", ";
			}
			$sumoutput .= "<a href=\"".$sc['CompLink']."\" title=\"View more on this Competition\">".$sc['Comp']."</a>";
			$is_followon = 1;
		}
	}
	else {
		print($sumcompsql);
	}

	$sumoutput .= "</li>\n";
	$sumoutput .= "	<li><strong>Season</strong>: " . bblm_get_season_link( $sc['Sea'] ) . "</li>\n";
	$sumoutput .= "</ul>\n\n";
	$sumoutput .= "[Your Text goes here]\n\n";

	$sumoutput .= "<h3>Quick Summary of Week ".$_POST['bblm_sweekno']."</h3>\n";

	$matchbdsql = 'SELECT COUNT(M.m_id) AS Games, P.post_title AS Comp, P.guid AS CompLink, D.div_name FROM '.$wpdb->prefix.'match M, '.$wpdb->posts.' P, '.$wpdb->prefix.'bb2wp J, '.$wpdb->prefix.'division D WHERE M.div_id = D.div_id AND M.c_id = J.tid AND J.prefix = \'c_\' AND J.pid = P.ID AND M.m_date > \''.$_POST['bblm_sdatef'].'\' AND M.m_date < \''.$_POST['bblm_sdatet'].'\' GROUP BY M.div_id ORDER BY Comp ASC, D.div_id ASC';
	$sumoutput .= "<ul>\n";
	if ($matchbd = $wpdb->get_results($matchbdsql)) {
		foreach ($matchbd as $mbd) {
			$sumoutput .= "	<li><strong>".$mbd->Games."</strong> games in <strong>".$mbd->div_name."</strong> - ".$mbd->Comp."</li>\n";
		}
	}

	$sumstatssql = 'SELECT SUM(M.m_tottd) AS TTD, SUM(M.m_totcas) AS TCAS, SUM(M.m_totcomp) AS TCOMP, SUM(M.m_totint) AS TINT FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C WHERE M.c_id = C.c_id AND C.c_counts = 1 AND C.c_show = 1 AND M.m_date > \''.$_POST['bblm_sdatef'].'\' AND M.m_date < \''.$_POST['bblm_sdatet'].'\'';
	$sumstat = $wpdb->get_row($sumstatssql);
	$deathsnumsql = 'SELECT COUNT(*) AS Deaths FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'player_fate F, '.$wpdb->prefix.'comp C WHERE F.m_id = M.m_id AND (F.f_id = 1 OR F.f_id = 6 OR F.f_id = 7) AND C.c_id = M.c_id AND C.c_counts = 1 AND C.c_show = 1 AND M.m_date > \''.$_POST['bblm_sdatef'].'\' AND M.m_date < \''.$_POST['bblm_sdatet'].'\'';
	$deathsnum = $wpdb->get_var($deathsnumsql);
	$sumoutput .= "<li><strong>".$sumstat->TTD."</strong> Touchdowns scored</li>\n";
	$sumoutput .= "<li><strong>".$sumstat->TCAS."</strong> Casualties caused (<strong>".$deathsnum."</strong> resulted in Death)</li>\n";
	$sumoutput .= "<li><strong>".$sumstat->TCOMP."</strong> Completions made</li>\n";
	if ($sumstat->TINT > 1) {
		$sumoutput .= "<li><strong>".$sumstat->TINT."</strong> Interceptions made</li>\n";
	}
	else if (1 == $sumstat->TINT) {
		$sumoutput .= "<li><strong>".$sumstat->TINT."</strong> Interception made</li>\n";
	}

	//Highest attemdance
	$biggestattendcesql = 'SELECT UNIX_TIMESTAMP(M.m_date) AS MDATE, M.m_gate AS VALUE, P.post_title AS MATCHT, P.guid AS MATCHLink FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE M.m_id = J.tid AND J.prefix = \'m_\' AND J.pid = P.ID AND M.c_id = C.c_id AND C.c_show = 1 AND C.type_id = 1 AND C.c_counts = 1 AND  M.m_date > \''.$_POST['bblm_sdatef'].'\' AND M.m_date < \''.$_POST['bblm_sdatet'].'\'  ORDER BY M.m_gate DESC, MDATE ASC LIMIT 1';
	$bc = $wpdb->get_row($biggestattendcesql);
	$sumoutput .= "<li><strong>".number_format($bc->VALUE)." fans</strong> was the highest recorded attendance.</li>\n";


	$sumoutput .= "</ul>\n\n";
	//Checking to see if any deaths are recorded, used to print obituarys below
	if ($deathsnum > 0) {
		$is_death = 1;
	}

	//Check to see if match results are to be shown
	if ($_POST['bblm_smtcres']) {
		$sumoutput .= "<h3>Match Results for Week ".$_POST['bblm_sweekno']."</h3>\n\n<p>(numbers in brackets are the casualties caused by each team)</p>\n\n";

		$matchsql = 'SELECT M.m_id, B.guid AS MatchLink, C.post_title AS Comp, D.div_name, T.post_title AS Ta, G.post_title AS Tb, M.m_gate, M.m_teamAtd, M.m_teamBtd, M.m_teamAcas, M.m_teamBcas FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'bb2wp N, '.$wpdb->posts.' B, '.$wpdb->prefix.'bb2wp X, '.$wpdb->posts.' C, '.$wpdb->prefix.'division D, '.$wpdb->prefix.'bb2wp R, '.$wpdb->posts.' T, '.$wpdb->prefix.'bb2wp F, '.$wpdb->posts.' G WHERE M.m_teamA = R.tid AND R.prefix = \'t_\' AND R.pid = T.ID AND M.m_teamB = F.tid AND F.prefix = \'t_\' AND F.pid = G.ID AND M.div_id = D.div_id AND M.c_id = X.tid AND X.prefix = \'c_\' AND X.pid = C.ID AND M.m_id = N.tid AND N.prefix = \'m_\' AND N.pid = B.ID AND M.m_date > \''.$_POST['bblm_sdatef'].'\' AND M.m_date < \''.$_POST['bblm_sdatet'].'\' ORDER BY M.c_id DESC, M.div_id ASC LIMIT 0, 30 ';
		if ($matches = $wpdb->get_results($matchsql)) {
			$is_first_m = "1";
			$is_first_d = "1";
			$prev_comp = "";
			$prev_div = "";

				foreach ($matches as $ma) {
					if ($ma->Comp !== $prev_comp) {
						if (!$is_first_m) {
							$sumoutput .= "</ul>\n";
						}
						$sumoutput .= "<h4>".$ma->Comp." - ".$ma->div_name."</h4>\n<ul>\n";
						$prev_comp = $ma->Comp;
						$prev_div = $ma->div_name;
						$is_first_m = "0";
						$is_first_d = "1"; //this ensures that if it is a new comp, a new div is not triggered
					}
					//Now we check for the division
					if (!$is_first_d) {
						if ($ma->div_name !== $prev_div) {
							$prev_div = $ma->div_name;
							$sumoutput .= "</ul>\n<h4>".$ma->Comp." - ".$ma->div_name."</h4>\n<ul>\n";
							$is_first_d = "1";
						}
					}
					$sumoutput .= "	<li><a href=\"".$ma->MatchLink."\" title=\"View full details of this encounter\">".$ma->Ta." ".$ma->m_teamAtd." (".$ma->m_teamAcas.") - ".$ma->m_teamBtd." (".$ma->m_teamBcas.") ".$ma->Tb."</a> (<em>att ".number_format($ma->m_gate)."</em>)";

					$scorerslistsql = 'SELECT T.t_sname, P.post_title AS Player, M.mp_td AS TD, M.p_id FROM '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P, '.$wpdb->prefix.'team T WHERE M.t_id = T.t_id AND M.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = P.ID AND M.mp_td > 0 AND M.m_id = '.$ma->m_id.' ORDER BY M.t_id ASC LIMIT 16';
					if ($scorerslist = $wpdb->get_results($scorerslistsql)) {
						$sumoutput .= "		<ul>\n";

						$last_player = "";
						$last_team = "";
						$is_first_t = 1;

						foreach ($scorerslist as $sl) {
							//if the player has already been outputted then skip them
							if ($last_player !== $sl->p_id) {
								if ($last_team == $sl->t_sname) {
									$sumoutput .= ", ";
								}
								else {
									if (!$is_first_t) {
										$sumoutput .= "</li>\n";
									}
									$sumoutput .= "			<li><strong>".$sl->t_sname."</strong>: ";
								}
								$sumoutput .= $sl->Player;

								//if a player has scored more than 1 TD than let us know.
								if (1 < $sl->TD) {
									$sumoutput .= " (x".$sl->TD.")";
								}
								//$sumoutput .= "</li>\n";
								$last_player = $sl->p_id; // recorded so we can skip
								$last_team = $sl->t_sname; // recorded so we can go to a new line.
								$is_first_t = 0;
							}
						}
						$sumoutput .= "			</li>\n		</ul>\n";
					}

					$sumoutput .= "</li>\n";
					//force the loop to check for a new div next time
					$is_first_d = "0";
				}
			$sumoutput .= "</ul>\n";
		}
	}

	//Obituaries
	if ($is_death) {
		$obitsql = 'SELECT O.post_title AS PLAYER, O.guid AS PLAYERLink, X.pos_name, T.t_name AS TEAM, T.t_guid AS TEAMLink, F.pf_desc AS FATE FROM '.$wpdb->prefix.'player_fate F, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'position X, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' O, '.$wpdb->prefix.'team T WHERE P.t_id = T.t_id AND F.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = O.id AND F.p_id = P.p_id AND P.pos_id = X.pos_id AND F.m_id = M.m_id AND M.c_id = C.c_id AND C.c_counts = 1 and C.c_show = 1 AND C.type_id = 1 AND (F.f_id = 1 OR F.f_id = 6 OR F.f_id = 7) AND M.m_date > \''.$_POST['bblm_sdatef'].'\' AND M.m_date < \''.$_POST['bblm_sdatet'].'\' ORDER BY F.m_id ASC LIMIT 0, 30 ';
		if ($obit = $wpdb->get_results($obitsql)) {
			$sumoutput .= "<h3>Obituaries for Week ".$_POST['bblm_sweekno']."</h3>\n\n	<ul>\n";
			foreach ($obit as $o) {
				$sumoutput .= "		<li><strong><a href=\"".$o->PLAYERLink."\" title=\"Read more about this player\">".$o->PLAYER."</a> (".$o->pos_name." for <a href=\"".$o->TEAMLink."\" title=\"Read more about this team\">".$o->TEAM."</a>)</strong>: <em>".$o->FATE."</em> </li>\n";
			}
			$sumoutput .= "	</ul>\n";
			}
	}

	//Check to see if league standings are to be shown
	if (0 < $_POST['bblm_sftbl']) {
		//quickly loop through the original summarycomp array to determine the competition name
		reset($sumcomp);
		foreach ($sumcomp as $sc) {
			if ($sc['c_id'] == $_POST['bblm_sftbl']) {
				$lsname = $sc['Comp'];
			}
		}
		$sumoutput .= "<h3>League Standings for the ".$lsname." (Week ".$_POST['bblm_sweekno'].")</h3>\n";

		//copied strigh from view.comp
		$standingssql = 'SELECT P.post_title, P.guid, C.*, D.div_name, SUM(C.tc_tdfor-C.tc_tdagst) AS TDD, SUM(C.tc_casfor-C.tc_casagst) AS CASD FROM '.$wpdb->posts.' P, '.$wpdb->prefix.'bb2wp J, '.$wpdb->prefix.'team_comp C, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'division D WHERE T.t_show = 1 AND C.div_id = D.div_id AND T.t_id = C.t_id AND T.t_id = J.tid AND J.prefix = \'t_\' AND J.pid = P.ID AND C.c_id = '.$_POST['bblm_sftbl'].' GROUP BY C.t_id ORDER BY D.div_id ASC, C.tc_points DESC, TDD DESC, CASD DESC';
		if ($standings = $wpdb->get_results($standingssql)) {
			$is_first_div = 1;
			$zebracount = 1;
			foreach ($standings as $stand) {
				//print the end of a table tag unless this was the first table
				if ($lastdiv !== $stand->div_name) {
					if (!TRUE == $is_first_div) {
						$sumoutput .= "</table>\n";
						$zebracount = 1;
					}
					$sumoutput .= "<h4>$stand->div_name</h4>\n<table>\n <tr>\n  <th>Team</th>\n  <th>Pld</th>\n  <th>W</th>\n  <th>D</th>\n  <th>L</th>\n  <th>TF</th>\n  <th>TA</th>\n  <th>TD</th>\n  <th>CF</th>\n  <th>CA</th>\n  <th>CD</th>\n  <th>PTS</th>\n </tr>\n";
				}
				$lastdiv = $stand->div_name;
				if ($zebracount % 2) {
					$sumoutput .= "		<tr>\n";
				}
				else {
					$sumoutput .= "		<tr class=\"bblm_tbl_alt\">\n";
				}
				$sumoutput .= "  <td><a href=\"".$stand->guid."\" title=\"View more information about ".$stand->post_title."\">".$stand->post_title."</a></td>\n <td>".$stand->tc_played."</td>\n  <td>".$stand->tc_W."</td>\n  <td>".$stand->tc_D."</td>\n  <td>".$stand->tc_L."</td>\n  <td>".$stand->tc_tdfor."</td>\n  <td>".$stand->tc_tdagst."</td>\n  <td>".$stand->TDD."</td>\n  <td>".$stand->tc_casfor."</td>\n  <td>".$stand->tc_casagst."</td>\n  <td>".$stand->CASD."</td>\n  <td><strong>".$stand->tc_points."</strong></td>\n	</tr>\n";
				//set flag so resulting </table> is printed
				$is_first_div = 0;
				$zebracount++;
			}
		$sumoutput .= "</table>\n";
		}
		//end of copy and paste from view.comp

	}
	//Check to see if Player stats are to be shown
	if ("0" !== $_POST['bblm_splstat']) {
		$is_seasonstat = 0;

		if ("S" == $_POST['bblm_splstat']) {
			//we are dealing with a season
			$is_seasonstat = 1;
		}
		//quickly loop through the original summarycomp array to determine the competition name
		reset($sumcomp);
		foreach ($sumcomp as $sc) {
			if ($is_seasonstat) {
				$sea_id = $sc['sea_id'];
				$lsname = bblm_get_season_name( $sc['Sea'] );
			}
			else {
				if ($sc['c_id'] == $_POST['bblm_sftbl']) {
					$lsname = $sc['Comp'];
				}
			}
		}
		$bblm_star_team = bblm_get_star_player_team();

		$sumoutput .= "<h3>Player Statistics for the ".$lsname." (Week ".$_POST['bblm_sweekno'].")</h3>\n";
		//modified Copy and Paste from view.comp
		//create stat array
		$statarray = array();
		$statarray[0][0] = "mp_spp";
		$statarray[0][1] = "Best Players";
		$statarray[1][0] = "mp_td";
		$statarray[1][1] = "Top Scorers";
		$statarray[2][0] = "mp_cas";
		$statarray[2][1] = "Most Vicious";
		$statarray[3][0] = "mp_comp";
		$statarray[3][1] = "Top Passers";
		$statarray[4][0] = "mp_int";
		$statarray[4][1] = "Top Interceptors";

		foreach ($statarray as $sa) {

			//load in the SQL based on the comp/season selection
			if ($is_seasonstat) {
				$statsql = 'SELECT Y.post_title, O.post_title AS TEAM, O.guid AS TEAMLink, Y.guid, SUM(M.'.$sa[0].') AS VALUE, R.pos_name FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'match X, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' Y, '.$wpdb->prefix.'position R, '.$wpdb->prefix.'bb2wp I, '.$wpdb->posts.' O WHERE P.t_id = I.tid AND I.prefix = \'t_\' AND I.pid = O.ID AND P.pos_id = R.pos_id AND P.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = Y.ID AND M.m_id = X.m_id AND X.c_id = C.c_id AND C.c_counts = 1 AND M.p_id = P.p_id AND P.t_id = T.t_id AND M.'.$sa[0].' > 0 AND C.sea_id = '.$sea_id.' AND T.t_id != '.$bblm_star_team.' GROUP BY P.p_id ORDER BY VALUE DESC LIMIT 10';
			}
			else {
				$statsql = 'SELECT Y.post_title, O.post_title AS TEAM, O.guid AS TEAMLink, Y.guid, SUM(M.'.$sa[0].') AS VALUE, R.pos_name FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'match X, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' Y, '.$wpdb->prefix.'position R, '.$wpdb->prefix.'bb2wp I, '.$wpdb->posts.' O WHERE P.t_id = I.tid AND I.prefix = \'t_\' AND I.pid = O.ID AND P.pos_id = R.pos_id AND P.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = Y.ID AND M.m_id = X.m_id AND X.c_id = C.c_id AND C.c_counts = 1 AND M.p_id = P.p_id AND P.t_id = T.t_id AND M.'.$sa[0].' > 0 AND C.c_id = '.$_POST['bblm_splstat'].' AND T.t_id != '.$bblm_star_team.' GROUP BY P.p_id ORDER BY VALUE DESC LIMIT 10';
			}
			if ($topstats = $wpdb->get_results($statsql)) {
			$sumoutput .= "<h4>".$sa[1]."</h4>\n";
					$sumoutput .= "<table class=\"bblm_table\">\n	<tr>\n		<th class=\"bblm_tbl_stat\">#</th>\n		<th class=\"bblm_tbl_name\">Player</th>\n		<th>Position</th>\n		<th class=\"bblm_tbl_name\">Team</th>\n		<th class=\"bblm_tbl_stat\">Value</th>\n		</tr>\n";
					$zebracount = 1;
					$prevvalue = 0;
						foreach ($topstats as $ts) {
						if ($zebracount % 2) {
							$sumoutput .= "	<tr>\n";
						}
						else {
							$sumoutput .= "	<tr class=\"bblm_tbl_alt\">\n";
						}
						if ($ts->VALUE > 0) {
							if ($prevvalue == $ts->VALUE) {
								$sumoutput .= "	<td>-</td>\n";
							}
							else {
								$sumoutput .= "	<td><strong>".$zebracount."</strong></td>\n";
							}
							$sumoutput .= "	<td><a href=\"".$ts->guid."\" title=\"View more details on ".$ts->post_title."\">".$ts->post_title."</a></td>\n	<td>".$ts->pos_name."</td>\n	<td><a href=\"".$ts->TEAMLink."\" title=\"Read more on this team\">".$ts->TEAM."</a></td>\n	<td>".$ts->VALUE."</td>\n	</tr>\n";
							$prevvalue = $ts->VALUE;
						}
						$zebracount++;
					}
					$sumoutput .= "</table>\n";
				}
		}//end of for each stat

		//End of modified Copy and Paste from view.comp

	}
	// End of Summary Generation //
?>
<h3>Preview</h3>
<p>Below is the preview of the generated summary. Don't worry about the style, focus on the content</p>
<style>
#bblm_sumpreview {
	font-family: 'Lucida Grande', Verdana, Arial, Sans-Serif;
	font-size: 1em;
	color:#333;
	border-bottom: 1px solid #999;
	border-right: 1px solid #999;
	border-top: 2px solid #000;
	border-left: 2px solid #000;
	padding: 10px;
}
#bblm_sumpreview textarea {
	display:none;
	width: 100%;
}
#bblm_sumpreview table {
	margin-left: auto;
	margin-right: auto;
	border: 1px solid #000000;
	border-collapse: collapse;
}
#bblm_sumpreview table tr.bblm_tbl_alt {
	background-color: #ddd;
}
#bblm_sumpreview table td {
	text-align: center;
	border-left-style: dotted;
	border-bottom-style: solid;
	border-color: #000000;
	border-width: 1px;
	padding: 0.3em;
}
#bblm_sumpreview table th {
	border-bottom: 4px solid #000000;
	background-color: #9F2500;
	color: #ffffff;
	font-weight: bold;
	text-align: center;
}
</style>
<div id="bblm_sumpreview">
<?php
	//We only output the title, we need to pass it to the next one
	print("<h2> Season ".$_POST['bblm_sseano']." - Week ".$_POST['bblm_sweekno']." Summary - ".stripslashes($_POST['bblm_stitle'])."</h2>");
	print($sumoutput);

?>
<form name="bblm_sum_preview" method="post" id="post">
	<textarea rows='30' cols='100' name='bblm_soutput' id='bblm_soutput'><?php print($sumoutput); ?></textarea>
	<textarea rows='10' cols='600' name='bblm_stitle' id='bblm_stitle'><?php print(stripslashes($_POST['bblm_stitle'])); ?></textarea>
	<input type="hidden" name="bblm_sweekno" value="<?php print($_POST['bblm_sweekno']); ?>">
	<input type="hidden" name="bblm_sseano" value="<?php print($_POST['bblm_sseano']); ?>">
</div>

	<p>If you wish to continue and generate the summary, Please press the below button, otherwise pres the back button to return to the previous options page.</p>
	<p>Also bear in mind that you can modify the wording of the summary, once it is in a post format.</p>

	<p class="submit">
	<input type="submit" name="bblm_sum_create" value="Confirm and Generate" title="Confirm and Generate"/>
	</p>
</form>
<?php
}

else if(isset($_POST['bblm_select_dates'])) {
	  ////////////////////////////
	 // Step 2: Options Screen //
	////////////////////////////

?>
	<form name="bblm_editfixture" method="post" id="post">
<?php
/*		print("<pre>\n");
		print_r($_POST);
		print("</pre>\n");*/

		//First we check to see if any maches have been played for that time period
		$matchcountsql = 'SELECT COUNT(*) FROM '.$wpdb->prefix.'match M WHERE M.m_date > \''.$_POST['bblm_sdatef'].'\' AND M.m_date < \''.$_POST['bblm_sdatet'].'\'';
		$matchcount = $wpdb->get_var($matchcountsql);

		if (0 == $matchcount) {
			print("<p><strong>There are no matches for this period. Please try again.</strong></p>\n");
		}
		else {
?>
	<p>The options below will help define what is displayed.</p>
	<table class="form-table">
	<tr class="form-field form-required">
		<th scope="row" valign="top"><label for="bblm_stitle">Title</label></th>
		<td><input name="bblm_stitle" type="text" size="50" maxlength="100" value=""><br />
		The Title that the summary will have. Something whitty. Does <strong>not</strong> need to include the week number or season. The system will add that.</td>

	</tr>
	<tr class="form-field form-required">
		<th scope="row" valign="top"><label for="bblm_sweekno">Week:</label></th>
		<td><input name="bblm_sweekno" type="text" size="5" maxlength="2" value=""><br />
		The Week number. Should follow on from the last.</td>
	</tr>
	<tr class="form-field form-required">
		<th scope="row" valign="top"><label for="bblm_sseano">Season:</label></th>
		<td><input name="bblm_sseano" type="text" size="5" maxlength="2" value=""><br />
		The Season <em>number</em></td>
	</tr>
	<tr>
		<th scope="row">Match results:</th>
		<td>
			<fieldset><legend class="screen-reader-text"><span>Match Results</span></legend>
			Do you want the summary to include a list of the matches?<br />
			<label title='No'><input type="radio" value="0" name="bblm_smtcres"> No</label><br />
			<label title='Yes'><input type="radio" value="1" name="bblm_smtcres" checked="yes"> Yes</label><br />
			</fieldset>
		</td>
	</tr>
<?php
		//Now we determine the lst of Competitions that took part in this time period
		$complistsql = 'SELECT DISTINCT C.c_id, C.ct_id AS Type, c_name FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C WHERE C.c_id = M.c_id AND M.m_date > \''.$_POST['bblm_sdatef'].'\' AND M.m_date < \''.$_POST['bblm_sdatet'].'\' ORDER BY Type ASC';
		//$complistsql = 'SELECT DISTINCT C.c_id, C.ct_id AS Type, c_name FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C WHERE C.c_id = M.c_id AND M.m_date > \'2008-04-28\' AND M.m_date < \'2008-05-10\' ORDER BY Type ASC LIMIT 0, 30 ';
		if ($complist = $wpdb->get_results($complistsql)) {
?>
	<tr>
		<th scope="row">League Table:</th>
		<td>
		<fieldset><legend class="screen-reader-text"><span>League Table</span></legend>
		One of the Competitions during this period may have had a league table. Do you want it displayed in the Summary? If so, please select the one you want displayed<br />
		<label title="No"><input type="radio" value="0" name="bblm_sftbl" checked="yes"> <strong>No</strong> - I don't want to show a league table.</label><br />
<?php
			foreach ($complist as $cl) {
				if (1 ==$cl->Type || 2 ==$cl->Type) {
					print("		<label title=\"Yes\"><input type=\"radio\" value=\"".$cl->c_id."\" name=\"bblm_sftbl\"> <strong>Yes</strong> - ".$cl->c_name.".</label><br />\n");
				}
			}
?>
		</fieldset></td>
	</tr>
	<tr>
		<th scope="row">Player Stats:</th>

		<td>
		<fieldset><legend class="screen-reader-text"><span>Player Stats</span></legend>
		Do you want to inlude Player stats? If so, select an option.<br />
		<label title="No"><input type="radio" value="0" name="bblm_splstat" checked="yes"> <strong>No</strong> - No Stats please</label><br />
		<label title="Yes"><input type="radio" value="S" name="bblm_splstat"> <strong>Yes</strong> - Stats for the Season to date</label><br />
<?php
			foreach ($complist as $cl) {
				print("		<label title=\"yes\"><input type=\"radio\" value=\"".$cl->c_id."\" name=\"bblm_splstat\"> <strong>Yes</strong> - Stats for ".$cl->c_name.".</label><br />\n");
			}
?>
		</fieldset></td>
	</tr>
	</table>
<?php
		} //end of if - comp listing
?>
	<input type="hidden" name="bblm_sdatef" size="2" value="<?php print($_POST['bblm_sdatef']." 00:00:00"); ?>">
	<input type="hidden" name="bblm_sdatet" size="2" value="<?php print($_POST['bblm_sdatet']." 23:59:59"); ?>">

	<p>Press the below button to vew the summary. This has a habit of taking some time to generate so please wait nicely.</p>
	<p class="submit">
	<input type="submit" name="bblm_gen_preview" value="View Preview" title="View Preview"/>
	</p>
</form>

<?php
		} //end of "there are matches" else

}
else {
	  ///////////////////////////////
	 // Step 1: Select comp + div //
	///////////////////////////////
?>
<form name="bblm_select dates" method="post" id="post">

	<p>Before we can begin, you must first select the dates that the summary will cover. The default range is a week.</p>
	<p><strong>Important:</strong> It is recconmended to run the dates over 6 days (Thurs - Wed) to avoid incomplete data.</p>
	<table class="form-table">
	<tr class="form-field form-required">
		<th scope="row" valign="top"><label for="bblm_sdatef">From</label></th>
		<td><input name="bblm_sdatef" type="text" size="20" maxlength="20" value="<?php print(date('Y-m-d',strtotime("-1 week"))); ?>"></td>

	</tr>
	<tr class="form-field form-required">
		<th scope="row" valign="top"><label for="bblm_sdatet">To:</label></th>
		<td><input name="bblm_sdatet" type="text" size="20" maxlength="20" value="<?php print(date('Y-m-d')); ?>"></td>
	</tr>
	</table>


	<p class="submit">
	<input type="submit" name="bblm_select_dates" value="Continue" title="Continue"/>
	</p>
</form>
<?php

}
?>

</div>
