<?php
/**
 * BBowlLeagueMan Teamplate View Match
 *
 * Page Template to view a Matches details
 *
 * @author 		Blacksnotling
 * @category 	Template
 * @package 	BBowlLeagueMan/Templates
 */
/*
 * Template Name: View Match
 */
?>
<?php get_header(); ?>
	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
			<div class="entry">
				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>


<?php
			//Match Information
			$matchsql = 'SELECT M.*, UNIX_TIMESTAMP(M.m_date) AS mdate FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'bb2wp J WHERE M.m_id = J.tid AND J.pid = '.$post->ID.' LIMIT 1';
			if ($m = $wpdb->get_row($matchsql)) {

				//TeamA Information
				$teamAsql = 'SELECT M.*, T.WPID, T.t_sname, T.r_id FROM '.$wpdb->prefix.'match_team M, '.$wpdb->prefix.'team T WHERE M.t_id = T.t_id AND M.m_id = '.$m->m_id.' AND T.t_id = '.$m->m_teamA.' LIMIT 1';
				$tA = $wpdb->get_row($teamAsql);
				//Team B Information
				$teamBsql = 'SELECT M.*, T.WPID, T.t_sname, T.r_id FROM '.$wpdb->prefix.'match_team M, '.$wpdb->prefix.'team T WHERE M.t_id = T.t_id AND M.m_id = '.$m->m_id.' AND T.t_id = '.$m->m_teamB.' LIMIT 1';
				$tB = $wpdb->get_row($teamBsql);

				$teamA = esc_html( get_the_title( $tA->WPID ) );
				$teamB = esc_html( get_the_title( $tB->WPID ) );

				//Check for custom logo and if found set the var for use later on
				$options = get_option('bblm_config');
				$site_dir = htmlspecialchars($options['site_dir'], ENT_QUOTES);

				//Team A
				$filename = $_SERVER['DOCUMENT_ROOT']."/images/teams/".$tA->t_sname."_big.gif";
				if (file_exists($filename)) {
					$tAimg = "<img src=\"".home_url()."/images/teams/".$tA->t_sname."_big.gif\" alt=\"".$tA->t_sname." Logo\" />";
				}
				else {
					$tAimg = "<img src=\"".home_url()."/images/races/race".$tA->r_id.".gif\" alt=\"Race Logo\" />";
				}
				//Team B
				$filename = $_SERVER['DOCUMENT_ROOT']."/images/teams/".$tB->t_sname."_big.gif";
				if (file_exists($filename)) {
					$tBimg = "<img src=\"".home_url()."/images/teams/".$tB->t_sname."_big.gif\" alt=\"".$tB->t_sname." Logo\" />";
				}
				else {
					$tBimg = "<img src=\"".home_url()."/images/races/race".$tB->r_id.".gif\" alt=\"Race Logo\" />";
				}

?>
			<h2><a href="<?php print( get_post_permalink( $tA->WPID ) ); ?>" title="Read more on <?php print( $teamA ); ?>"><?php print( $teamA ); ?></a> vs <a href="<?php print( get_post_permalink( $tB->WPID ) ); ?>" title="Read more on <?php print( $teamB ); ?>"><?php print( $teamB ); ?></a></h2>
		<!--<p><?php print($post->ID); ?> - <?php print($m->m_id); ?></p>-->

				<table class="bblm_table">
					<tr>
						<th class="bblm_tbl_name"><?php print( $teamA );?></th>
						<th class="bblm_tbl_name">VS</th>
						<th class="bblm_tbl_name"><?php print( $teamB );?></th>
					</tr>
					<tr>
						<td><strong><?php print($tAimg);?></strong></td>
						<th>&nbsp;</th>
						<td><strong><?php print($tBimg);?></strong></td>
					</tr>
					<tr>
						<td class="bblm_score"><strong><?php print($tA->mt_td);?></strong></td>
						<th class="bblm_tottux">Score</th>
						<td class="bblm_score"><strong><?php print($tB->mt_td);?></strong></td>
					</tr>
					<tr>
						<td><?php print($tA->mt_cas);?></td>
						<th class="bblm_tottux">Casulties</th>
						<td><?php print($tB->mt_cas);?></td>
					</tr>
					<tr>
						<td><?php print($tA->mt_comp);?></td>
						<th class="bblm_tottux">Completions</th>
						<td><?php print($tB->mt_comp);?></td>
					</tr>
					<tr>
						<td><?php print($tA->mt_int);?></td>
						<th class="bblm_tottux">Inteceptions</th>
						<td><?php print($tB->mt_int);?></td>
					</tr>
					<tr>
						<td class="bblm_tv"><?php print(number_format($tA->mt_tv));?>gp</td>
						<th class="bblm_tottux">Team Value</th>
						<td class="bblm_tv"><?php print(number_format($tB->mt_tv));?>gp</td>
					</tr>
					<tr>
						<td><?php print(number_format($tA->mt_winnings));?></td>
						<th class="bblm_tottux">Fans</th>
						<td><?php print(number_format($tB->mt_winnings));?></td>
					</tr>
					<tr>
						<td><?php print(number_format($tA->mt_att));?> gp</td>
						<th class="bblm_tottux">Winnings</th>
						<td><?php print(number_format($tB->mt_att));?> gp</td>
					</tr>
					<tr>
						<td><?php print($tA->mt_ff);?></td>
						<th class="bblm_tottux">FF Change</th>
						<td><?php print($tB->mt_ff);?></td>
					</tr>
				</table>

				<h3>Match Report</h3>
				<div class="bblm_details">
					<?php the_content(); ?>
				</div>


<?php
				//Display match Trivia if something is present
				if ("" !== $m->m_trivia) {
					print("<h3>Match Trivia</h3>\n");
					print("<div class=\"bblm_details\">\n".$m->m_trivia."</div>");
				}
?>
			<h3>Player Actions</h3>
		<table class="bblm_table">
			<tr>
				<th><?php print( $teamA );?></th>
				<th>&nbsp;</th>
				<th><?php print( $teamB );?></th>
			</tr>
			<tr>
				<td>
<?php
			//Now we loop through the player actions for the match and record any increases and build the player actions table
				//First we initialize some valuables
				$tamvp="";
				$tbmvp="";
				$playeractions="";

				$taplayersql = 'SELECT M.*, S.guid, S.post_title, Q.p_name, Q.p_num FROM '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P, '.$wpdb->prefix.'player Q, '.$wpdb->prefix.'bb2wp R, '.$wpdb->posts.' S WHERE Q.p_id = R.tid AND R.prefix = \'p_\' AND R.pid = S.ID AND Q.p_id = M.p_id AND M.m_id = J.tid AND J.prefix = \'m_\' AND J.pid = P.ID AND M.m_id = '.$m->m_id.' AND M.t_id = '.$m->m_teamA.' ORDER BY Q.p_num ASC';
				if ($taplayer = $wpdb->get_results($taplayersql)) {
					//as we have players, initialize arrays to hold injuries and increases
					$tainj = array();
					$tainc = array();
					$zebracount = 1;
					print("<table class=\"bblm_table\">\n	<tr>\n		<th>#</th>\n		<th>Player</th>		<th>TD</th>\n		<th>CAS</th>\n		<th>COMP</th>\n		<th>INT</th>\n		<th>SPP</th>\n	</tr>\n");
					foreach ($taplayer as $tap) {
						if (1 == $tap->mp_mvp) {
							//if this player has the MVP record it for later
							//first it checks to see if an MVP has already been record for this team (in the event of a concession, there will be two for a team)
							if ("" == $tamvp) {
								$tamvp = "#".$tap->p_num;
							}
							else {
								$tamvp .=" and #".$tap->p_num;
							}
						}
						if ("none" !== $tap->mp_inj) {
						//if this player has an injury record it for later
							$tainj[] = "#".$tap->p_num." - ".$tap->p_name." - ".$tap->mp_inj;
						}
						if ("none" !== $tap->mp_inc) {
						//if this player has an injury record it for later
							$tainc[] = "#".$tap->p_num." - ".$tap->p_name." - ".$tap->mp_inc;
						}
						if ($zebracount % 2) {
							print("	<tr>\n");
						}
						else {
							print("	<tr class=\"bblm_tbl_alt\">\n");
						}
						print ("		<td>".$tap->p_num."</td>\n		<td><a href=\"".$tap->guid."\" title=\"View the details of ".$tap->post_title."\">".$tap->post_title."</a></td>\n		<td>".$tap->mp_td."</td>\n		<td>".$tap->mp_cas."</td>\n		<td>".$tap->mp_comp."</td>\n		<td>".$tap->mp_int."</td>\n		<td><strong>".$tap->mp_spp."</strong></td>\n	</tr>\n");
						$zebracount++;
					}
					print("</table>");
					//set flag to show some player actions have been recorded
					$playeractions = 1;
					//final check of the recorded MVP. If it is blank then set the default value to show that none was assigned (which is different to not recorded)
					if ("" == $tamvp) {
						$tamvp = "N/A";
					}
				}
				else {
					print("No Player actions have been recorded for this game");
					$tanp = 1;
					$tamvp = "Not recorded";
				}
?>
						</td>
						<td>&nbsp;</td>
						<td>
<?php
				$tbplayersql = 'SELECT M.*, S.guid, Q.p_name, Q.p_num FROM '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P, '.$wpdb->prefix.'player Q, '.$wpdb->prefix.'bb2wp R, '.$wpdb->posts.' S WHERE Q.p_id = R.tid AND R.prefix = \'p_\' AND R.pid = S.ID AND Q.p_id = M.p_id AND M.m_id = J.tid AND J.prefix = \'m_\' AND J.pid = P.ID AND M.m_id = '.$m->m_id.' AND M.t_id = '.$m->m_teamB.' ORDER BY Q.p_num ASC';
				if ($taplayer = $wpdb->get_results($tbplayersql)) {
					//as we have players, initialize arrays to hold injuries and increases
					$tbinj = array();
					$tbinc = array();
					$zebracount = 1;
					print("<table class=\"bblm_table\">\n	<tr>\n		<th>#</th>\n		<th>Player</th>		<th>TD</th>\n		<th>CAS</th>\n		<th>COMP</th>\n		<th>INT</th>\n		<th>SPP</th>\n	</tr>\n");
					foreach ($taplayer as $tap) {
						if (1 == $tap->mp_mvp) {
							//if this player has the MVP record it for later
							//first it checks to see if an MVP has already been record for this team (in the event of a concession, there will be two for a team)
							if ("" == $tbmvp) {
								$tbmvp = "#".$tap->p_num;
							}
							else {
								$tbmvp .=" and #".$tap->p_num;
							}
						}
						if ("none" !== $tap->mp_inj) {
						//if this player has an injury record it for later
							$tbinj[] = "#".$tap->p_num." - ".$tap->p_name." - ".$tap->mp_inj;
						}
						if ("none" !== $tap->mp_inc) {
						//if this player has an injury record it for later
							$tbinc[] = "#".$tap->p_num." - ".$tap->p_name." - ".$tap->mp_inc;
						}
						if ($zebracount % 2) {
							print("	<tr>\n");
						}
						else {
							print("	<tr class=\"bblm_tbl_alt\">\n");
						}
						print ("		<td>".$tap->p_num."</td>\n		<td><a href=\"".$tap->guid."\" title=\"View the details of ".$tap->p_name."\">".$tap->p_name."</a></td>\n		<td>".$tap->mp_td."</td>\n		<td>".$tap->mp_cas."</td>\n		<td>".$tap->mp_comp."</td>\n		<td>".$tap->mp_int."</td>\n		<td><strong>".$tap->mp_spp."</strong></td>\n	</tr>\n");
						$zebracount++;
					}
					print("</table>");
					//set flag to show some player actions have been recorded
					$playeractions = 1;
					//final check of the recorded MVP. If it is blank then set the default value to show that none was assigned (which is different to not recorded)
					if ("" == $tbmvp) {
						$tbmvp = "N/A";
					}
				}
				else {
					print("No Player actions have been recorded for this game");
					$tbnp = 1;
					$tbmvp = "Not recorded";
				}
?>
						</td>
					</tr>
					<tr>
						<td>
<?php
						print($tamvp);
?>
						</td>
						<th class="tottux">MVP</th>
						<td>
<?php
						print($tbmvp);
?>
						</td>
					</tr>
					<tr>
						<td>
						<?php
						if (isset($tainj)) {
							if (0 !== count($tainj)) {
								//If players where inj, we have details
								print("<ul>\n");
								foreach ($tainj as $taijured) {
									print("<li>".$taijured."</li>");
								}
								print("<ul>");
							}
							else {
								print("None");
							}
						}
						else {
							//we have no player actions recorded
							print("Not Recorded");
						}
						?>
						</td>
						<th class="tottux">Inj</th>
						<td>
						<?php
						if (isset($tbinj)) {
							if (0 !== count($tbinj)) {
								//If players where inj, we have details
								print("<ul>\n");
								foreach ($tbinj as $tbijured) {
									print("<li>".$tbijured."</li>");
								}
								print("<ul>");
							}
							else {
								print("None");
							}
						}
						else {
							//we have no player actions recorded
							print("Not Recorded");
						}
						?>
						</td>
					</tr>
					<tr>
						<td>
						<?php
						if (isset($tainc)) {
							if (0 !== count($tainc)) {
								//If players where inj, we have details
								print("<ul>\n");
								foreach ($tainc as $taiinc) {
									print("<li>".$taiinc."</li>\n");
								}
								print("</ul>\n");
							}
							else {
								print("None");
							}
						}
						else {
							//we have no player actions recorded
							print("Not Recorded");
						}
						?>
						</td>
						<th class="tottux">Inc</th>
						<td>
						<?php
						if (isset($tbinc)) {
							if (0 !== count($tbinc)) {
								//If players where inj, we have details
								print("<ul>\n");
								foreach ($tbinc as $tbiinc) {
									print("<li>".$tbiinc."</li>\n");
								}
								print("</ul>\n");
							}
							else {
								print("None");
							}
						}
						else {
							//we have no player actions recorded
							print("Not Recorded");
						}
						?>
						</td>
					</tr>
					<tr>
						<td><?php print(stripslashes($tA->mt_comment));?></td>
						<th class="tottux">Comms</th>
						<td><?php print(stripslashes($tB->mt_comment));?></td>
					</tr>
				</table>
<?php
		} //end of if match SQL

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
		//Top players in match
		$topplayerssql = 'SELECT P.post_title, P.guid, T.mp_spp AS value FROM '.$wpdb->prefix.'match_player T, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE T.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = P.ID AND T.mp_spp > 0 AND T.m_id = '.$m->m_id.' ORDER BY value DESC LIMIT 5';

		//scorers
		$topscorerssql = 'SELECT P.post_title, P.guid, T.mp_td AS value FROM '.$wpdb->prefix.'match_player T, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE T.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = P.ID AND T.mp_td > 0 AND T.m_id = '.$m->m_id.' ORDER BY value DESC LIMIT 10';

		$compsql = 'SELECT B.post_title AS Comp, B.guid AS CompLink, D.div_name, C.sea_id FROM '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp V, '.$wpdb->posts.' B, '.$wpdb->prefix.'division D WHERE C.c_id = V.tid AND V.prefix = \'c_\' AND V.pid = B.ID AND D.div_id = '.$m->div_id.' AND C.c_id = '.$m->c_id.' LIMIT 1';
		$comp = $wpdb->get_row($compsql);
?>

	<div id="subcontent">
		<ul>
			<li class="sideinfo"><h2>Match Information</h2>
			  <ul>
				<li><strong>Date:</strong> <?php print(date("d.m.25y", $m->mdate));?></li>
				<li><strong>Competition:</strong> <a href="<?php print($comp->CompLink); ?>" title="View more about this Competition"><?php print($comp->Comp);?></a></li>
				<li><strong>
<?php
		if ($m->div_id > 7) {
			print("Division");
		}
		else {
			print("Stage");
		}
?>
				</strong> <?php print($comp->div_name);?></li>
				<li><strong>Season:</strong> <?php echo bblm_get_season_link( $comp->sea_id ); ?></li>
				<li><strong>Attendance:</strong> <?php print(number_format($m->m_gate));?></li>
				<li><strong>Stadium:</strong> <?php echo bblm_get_stadium_link( $m->stad_id ); ?></li>
			  </ul>
			 </li>
<?php
	if ($playeractions) {
?>
			 <li><h2>Top Players of the Match</h2>
<?php
			if ($topplayers = $wpdb->get_results($topplayerssql)) {
				print("					<ul>\n");
					foreach ($topplayers as $ts) {
						print("						<li><a href=\"".$ts->guid."\" title=\"Read more on this player\">".$ts->post_title."</a> - ".$ts->value." spp</li>");
					}
				print("					</ul>\n");
			}
			else {
				print("					<p>None!</p>\n");
			}
?>
			 </li>
			 <li><h2>Top Scorers of the Match</h2>
<?php
			if ($topscorers = $wpdb->get_results($topscorerssql)) {
				print("					<ul>\n");
					foreach ($topscorers as $ts) {
						print("						<li><a href=\"".$ts->guid."\" title=\"Read more on this player\">".$ts->post_title."</a> - ".$ts->value."</li>");
					}
				print("					</ul>\n");
			}
			else {
				print("					<p>None!</p>\n");
			}
?>
			 </li>
<?php
	} //end of if $playeractions

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
