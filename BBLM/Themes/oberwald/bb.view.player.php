<?php
/**
 * BBowlLeagueMan Teamplate View Player
 *
 * Page Template to view Players details
 *
 * @author 		Blacksnotling
 * @category 	Template
 * @package 	BBowlLeagueMan/Templates
 */
/*
 * Template Name: View Player
 */
get_header(); ?>
	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
		<?php
			/*
			Gather Information for page
			*/
			$playersql = 'SELECT P.*, T.WPID, E.pos_name FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' X, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'position E WHERE P.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = X.ID AND X.ID = '.$post->ID.' AND P.t_id = T.t_id AND P.pos_id = E.pos_id';
			//if ($player = $wpdb->get_results($playersql)) {
			if ($pd = $wpdb->get_row($playersql)) {
				$pspp = $pd->p_spp;

			} //end of if playersql

				switch ($pspp) {
					case 0:
				    	$plevel = "Rookie";
					    break;
					case ($pspp < 6):
				    	$plevel = "Rookie";
					    break;
					case ($pspp < 16):
					    $plevel = "Experienced";
					    break;
					case ($pspp < 31):
					    $plevel = "Veteran";
					    break;
					case ($pspp < 51):
					    $plevel = "Emerging Star";
					    break;
					case ($pspp < 76):
					    $plevel = "Star";
					    break;
					case ($pspp < 176):
					    $plevel = "Super Star";
					    break;
					case ($pspp > 175):
					    $plevel = "Legend";
					    break;
					default:
				    	$plevel = "Rookie";
					    break;
				}

				if (0 == $pd->p_status) {
					$status = "Inactive";
				}
				else {
					$status = "Active";
				}
		?>

		<div class="entry">
			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<h2 class="entry-title"><?php the_title(); ?></h2>

					<table>
						<tr>
							<th class="tbl_name">Position</th>
							<th class="tbl_stat">MA</th>
							<th class="tbl_stat">ST</th>
							<th class="tbl_stat">AG</th>
							<th class="tbl_stat">AV</th>
							<th>Skills</th>
							<th>Injuries</th>
							<th>Cost</th>
						</tr>
						<tr>
							<td><?php print( esc_html( $pd->pos_name ) ); ?></td>
							<td><?php print($pd->p_ma); ?></td>
							<td><?php print($pd->p_st); ?></td>
							<td><?php print($pd->p_ag); ?></td>
							<td><?php print($pd->p_av); ?></td>
							<td class="tbl_skills"><?php print($pd->p_skills); ?></td>
							<td><?php print($pd->p_injuries); ?></td>
							<td><?php print(number_format($pd->p_cost)); ?>gp</td>
						</tr>
					</table>
<!-- <?php print($pspp); ?> -->
					<div class="details playerdet">
					<?php the_content(); ?>
					</div>

<?php
					if (0 == $pd->p_status) {
						//If the player is inactive, see if they were killed.
						$fatesql = 'SELECT pf_killer, f_id, pf_desc FROM `'.$wpdb->prefix.'player_fate` WHERE (f_id = 1 OR f_id = 6) AND p_id = '.$pd->p_id.' LIMIT 1';
						if ($fate = $wpdb->get_row($fatesql)) {
							print("						<h3>Obituary</h3>\n							<p>This player is Dead! They were killed by ");
							if ("0" == $fate->pf_killer) {
								print("an unkown player.</p>\n");
							}
							else if ("C" == $fate->pf_killer) {
								print("the crowd!</p>\n");
							}
							else if ("W" == $fate->pf_killer) {
								print("a wizard!</p>\n");
							}
							else if ("$pd->p_id" == $fate->pf_killer) {
								print("Themself!!</p>\n");
							}
							else {
								//It must be a player
								$killersql = 'SELECT P.post_title AS PLAYER, P.guid AS PLAYERLink, T.WPID FROM '.$wpdb->prefix.'player_fate F, '.$wpdb->prefix.'player X, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE X.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = P.ID AND F.pf_killer = X.p_id AND X.t_id = T.t_id AND F.p_id = '.$pd->p_id.' LIMIT 1';
								if ($killer = $wpdb->get_row($killersql)) {
									print("<a href=\"".$killer->PLAYERLink."\" title=\"Read more about this player\">".$killer->PLAYER."</a> from <a href=\"" . get_post_permalink( $killer->WPID ) . "\" title=\"Read more about this team\">" . esc_html( get_the_title( $killer->WPID ) ) . "</a>");
								}
								else {
									print("an unkown player.</p>\n");
								}
							}
							print("							<div class=\"details obit\">\n							<p>".$fate->pf_desc."</p>\n							</div>\n");
						}
					}

					// -- KILLER --
					$killersql = 'SELECT O.post_title AS PLAYER, O.guid AS PLAYERLink, T.WPID, X.pos_name FROM '.$wpdb->prefix.'player_fate F, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' O, '.$wpdb->prefix.'position X WHERE F.p_id = P.p_id AND P.t_id = T.t_id AND P.pos_id = X.pos_id AND F.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = O.ID AND F.pf_killer = '.$pd->p_id.' AND F.p_id != '.$pd->p_id.' ORDER BY F.m_id ASC';
					if ($killer = $wpdb->get_results($killersql)) {
						//If the player has killed people
?>
						<h3>Killer!</h3>
						<p>This player has killed another player in the course of their career. They have killed the following players:</p>
						<ul>
<?php
						foreach ($killer as $k) {
							print ("							<li><a href=\"".$k->PLAYERLink."\" title=\"Read more about ".$k->PLAYER."\">".$k->PLAYER."</a> (" . esc_html( $k->pos_name ) . " for <a href=\"" . get_post_permalink( $k->WPID ) . "\" title=\"Read more about this team\">" . esc_html( get_the_title( $k->WPID ) ) . "</a>)</li>\n");
						}
?>
						</ul>
<?php
					}


					$statssql = 'SELECT O.guid, O.post_title, COUNT(*) AS GAMES, SUM(M.mp_td) AS TD, SUM(M.mp_cas) AS CAS, SUM(M.mp_comp) AS COMP, SUM(M.mp_int) AS MINT, SUM(M.mp_mvp) AS MVP, SUM(M.mp_spp) AS SPP FROM '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'position Y, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'match X, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' O WHERE J.tid = T.t_id AND J.prefix = \'t_\' AND J.pid = O.ID AND P.t_id = T.t_id AND M.m_id = X.m_id AND X.c_id = C.c_id AND C.c_counts = 1 AND C.c_show = 1 AND M.p_id = P.p_id AND P.pos_id = Y.pos_id AND M.p_id = '.$pd->p_id.' GROUP BY P.p_id';
					if ($stats = $wpdb->get_results($statssql)) {
			?>
						<h3>Player Statistics</h3>
						<table>
 							<tr>
 								<th class="tbl_title">Playing for</th>
 								<th class="tbl_stat">P</th>
 								<th class="tbl_stat">TD</th>
 								<th class="tbl_stat">CAS</th>
 								<th class="tbl_stat">COMP</th>
 								<th class="tbl_stat">INT</th>
 								<th class="tbl_stat">MVP</th>
 								<th class="tbl_stat">SPP</th>
 							</tr>

			<?php
						foreach ($stats as $s) {
							print (" <tr>\n  	<td><a href=\"".$s->guid."\" title=\"Read more about ".$s->post_title."\">".$s->post_title."</a></td>\n  	<td>".$s->GAMES."</td>\n  	<td>".$s->TD."</td>\n  	<td>".$s->CAS."</td>\n  	<td>".$s->COMP."</td>\n  	<td>".$s->MINT."</td>\n  	<td>".$s->MVP."</td>\n  	<td>".$s->SPP."</td>\n </tr>\n");
						}
						print("</table>\n");
			?>
						<h3>Breakdown by Competition</h3>
						<table>
							<tr>
								<th class="tbl_title">Competition</th>
								<th class="tbl_stat">P</th>
								<th class="tbl_stat">TD</th>
								<th class="tbl_stat">CAS</th>
								<th class="tbl_stat">INT</th>
								<th class="tbl_stat">COMP</th>
								<th class="tbl_stat">MVP</th>
								<th class="tbl_stat">SPP</th>
							</tr>
<?php
					$playercompsql = 'SELECT COUNT(*) AS GAMES, SUM(M.mp_td) AS TD, SUM(M.mp_cas) AS CAS, SUM(M.mp_comp) AS COMP, SUM(M.mp_int) AS MINT, SUM(M.mp_mvp) AS MVP, SUM(M.mp_spp) AS SPP, S.guid, S.post_title FROM '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'match Q, '.$wpdb->prefix.'bb2wp R, '.$wpdb->posts.' S WHERE C.c_id = R.tid AND R.pid = S.ID AND R.prefix = \'c_\' AND M.m_id = Q.m_id AND Q.c_id = C.c_id AND C.c_counts = 1 AND C.c_show = 1 AND M.p_id = P.p_id AND M.p_id = '.$pd->p_id.' GROUP BY C.c_id ORDER BY C.c_id DESC';
					if ($playercomp = $wpdb->get_results($playercompsql)) {
						$zebracount = 1;
						foreach ($playercomp as $pc) {
							if ($zebracount % 2) {
								print("		<tr>\n");
							}
							else {
								print("		<tr class=\"tbl_alt\">\n");
							}
							print("			<td><a href=\"".$pc->guid."\" title=\"View more details about this competition\">".$pc->post_title."</a></td>\n		<td>".$pc->GAMES."</td>\n		<td>".$pc->TD."</td>\n		<td>".$pc->CAS."</td>\n		<td>".$pc->MINT."</td>\n			<td>".$pc->COMP."</td>\n		<td>".$pc->MVP."</td>\n		<td>".$pc->SPP."</td>\n	</tr>\n");
							$zebracount++;
						}
					}
					print("</table>\n");
?>
						<h3>Breakdown by Season</h3>
						<table>
							<tr>
								<th class="tbl_title">Season</th>
								<th class="tbl_stat">P</th>
								<th class="tbl_stat">TD</th>
								<th class="tbl_stat">CAS</th>
								<th class="tbl_stat">INT</th>
								<th class="tbl_stat">COMP</th>
								<th class="tbl_stat">MVP</th>
								<th class="tbl_stat">SPP</th>
							</tr>
<?php
					$playerseasql = 'SELECT S.post_title, S.guid, COUNT(*) AS GAMES, SUM(M.mp_td) AS TD, SUM(M.mp_cas) AS CAS, SUM(M.mp_comp) AS COMP, SUM(M.mp_int) AS MINT, SUM(M.mp_mvp) AS MVP, SUM(M.mp_spp) AS SPP FROM '.$wpdb->prefix.'season X, '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'match Q, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' S WHERE C.sea_id = J.tid AND J.prefix = \'sea_\' AND J.pid = S.ID AND C.c_counts = 1 AND C.c_show = 1 AND X.sea_id = C.sea_id AND M.m_id = Q.m_id AND Q.c_id = C.c_id AND M.p_id = P.p_id AND M.p_id = '.$pd->p_id.' GROUP BY C.sea_id ORDER BY C.sea_id DESC';
					if ($playersea = $wpdb->get_results($playerseasql)) {
					$zebracount = 1;
						foreach ($playersea as $pc) {
							if ($zebracount % 2) {
								print("		<tr>\n");
							}
							else {
								print("		<tr class=\"tbl_alt\">\n");
							}
							print("			<td><a href=\"".$pc->guid."\" title=\"View more information about this Season\">".$pc->post_title."</a></td>\n		<td>".$pc->GAMES."</td>\n		<td>".$pc->TD."</td>\n		<td>".$pc->CAS."</td>\n			<td>".$pc->MINT."</td>\n		<td>".$pc->COMP."</td>\n			<td>".$pc->MVP."</td>\n		<td>".$pc->SPP."</td>\n	</tr>\n");
							$zebracount++;
						}
					}
					print("</table>\n");
?>
					<h3>Recent Matches</h3>
						<table class="sortable expandable">
							<thead>
							<tr>
								<th>Date</th>
								<th>Opponant</th>
								<th>TD</th>
								<th>CAS</th>
								<th>INT</th>
								<th>COMP</th>
								<th>MVP</th>
								<th>SPP</th>
								<th>MNG?</th>
								<th>Increase</th>
								<th>Injury</th>
							</tr>
							</thead>
							<tbody>
<?php
						$playermatchsql = 'SELECT M.*, P.p_name, UNIX_TIMESTAMP(X.m_date) AS mdate, G.post_title AS TA, T.t_id AS TAid, G.guid AS TAlink, B.post_title AS TB, B.guid AS TBlink, R.t_id AS TBid, Z.guid FROM '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'match X, '.$wpdb->prefix.'bb2wp Y, '.$wpdb->posts.' Z, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'team R, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp F, '.$wpdb->posts.' G, '.$wpdb->prefix.'bb2wp V, '.$wpdb->posts.' B WHERE T.t_id = F.tid AND F.prefix = \'t_\' AND F.pid = G.ID AND R.t_id = V.tid AND V.prefix = \'t_\' AND V.pid = B.ID AND C.c_id = X.c_id AND C.c_counts = 1 AND C.c_show = 1 AND X.m_teamA = T.t_id AND X.m_teamB = R.t_id AND M.p_id = P.p_id AND M.m_id = X.m_id AND X.m_id = Y.tid AND Y.prefix = \'m_\' AND Y.pid = Z.ID AND M.p_id = '.$pd->p_id.' ORDER BY X.m_date DESC';
						if ($playermatch = $wpdb->get_results($playermatchsql)) {
						$zebracount = 1;
							foreach ($playermatch as $pm) {
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
								print("			<td><a href=\"".$pm->guid."\" title=\"View the match in more detail\">".date("d.m.y", $pm->mdate)."</a></td>\n");
								if ($pm->TAid == $pd->t_id) {
									print("		<td><a href=\"".$pm->TBlink."\" title=\"Read up on the opponants\">".$pm->TB."</a></td>\n");
								}
								else {
									print("		<td><a href=\"".$pm->TAlink."\" title=\"Read up on the opponants\">".$pm->TA."</a></td>\n");
								}
								print("		<td>");
								if (0 == $pm->mp_td) {
									print("0");
								}
								else {
									print("<strong>".$pm->mp_td."</strong>");
								}
								print("</td>\n		<td>");
								if (0 == $pm->mp_cas) {
									print("0");
								}
								else {
									print("<strong>".$pm->mp_cas."</strong>");
								}
								print("</td>\n		<td>");
								if (0 == $pm->mp_int) {
									print("0");
								}
								else {
									print("<strong>".$pm->mp_int."</strong>");
								}
								print("</td>\n		<td>");
								if (0 == $pm->mp_comp) {
									print("0");
								}
								else {
									print("<strong>".$pm->mp_comp."</strong>");
								}
								print("</td>\n		<td>");
								if (0 == $pm->mp_mvp) {
									print("0");
								}
								else {
									print("<strong>".$pm->mp_mvp."</strong>");
								}
								print("</td>\n		<td>");
								if (0 == $pm->mp_spp) {
									print("0");
								}
								else {
									print("<strong>".$pm->mp_spp."</strong>");
								}
								print("</td>\n		<td>");
								if (0 == $pm->mp_mng) {
									print("0");
								}
								else {
									print("<strong>Y</strong>");
								}
								print("</td>\n		<td>");
								if ("none" == $pm->mp_inc) {
									print("-");
								}
								else {
									print("<strong>".$pm->mp_inc."</strong>");
								}
								print("</td>\n		<td>");
								if ("none" == $pm->mp_inj) {
									print("-");
								}
								else {
									print("<strong>".$pm->mp_inj."</strong>");
								}
								print("</td>\n	</tr>\n");
								$zebracount++;
							}
							print("					</tbody>\n				</table>\n");
						}
?>
						<h3 id="awardsfull">Awards list in full</h3>
<?php
						//$championshipssql = 'SELECT A.a_name, P.post_title, P.guid FROM '.$wpdb->prefix.'awards A, '.$wpdb->prefix.'awards_team_comp B, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE A.a_id = B.a_id AND a_cup = 1 AND B.c_id = C.c_id AND C.c_id = J.tid AND J.prefix = \'c_\' AND J.pid = P.ID AND B.t_id = '.$tid.' ORDER BY A.a_id ASC';
						$championshipssql = 'SELECT A.a_name, P.post_title, P.guid FROM '.$wpdb->prefix.'player X, '.$wpdb->prefix.'awards A, '.$wpdb->prefix.'awards_team_comp B, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P, '.$wpdb->prefix.'match_player Z, '.$wpdb->prefix.'match V WHERE X.p_id = Z.p_id AND V.m_id = Z.m_id AND V.c_id = C.c_id AND X.t_id = B.t_id AND A.a_id = B.a_id AND a_cup = 1 AND B.c_id = C.c_id AND C.c_id = J.tid AND J.prefix = \'c_\' AND J.pid = P.ID AND X.p_id = '.$pd->p_id.' GROUP BY C.c_id ORDER BY A.a_id ASC LIMIT 0, 30 ';
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

						$seasonsql = 'SELECT A.a_name, P.post_title, P.guid, B.aps_value FROM '.$wpdb->prefix.'awards A, '.$wpdb->prefix.'awards_player_sea B, '.$wpdb->prefix.'season C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE A.a_id = B.a_id AND B.sea_id = C.sea_id AND C.sea_id = J.tid AND J.prefix = \'sea_\' AND J.pid = P.ID AND B.p_id = '.$pd->p_id.' ORDER BY A.a_id ASC';
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
								print("		<td>".$sa->a_name."</td>\n		<td><a href=\"".$sa->guid."\" title=\"View full details about ".$sa->post_title."\">".$sa->post_title."</a></td>\n		<td>".$sa->aps_value."</td>\n	</tr>\n");
								$zebracount++;
							}
							print("</table>\n");
						}
						else {
							$safail = 1;
						}

						$cafail = 0;
						$compawardssql = 'SELECT A.a_name, P.post_title, P.guid, B.apc_value FROM '.$wpdb->prefix.'awards A, '.$wpdb->prefix.'awards_player_comp B, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE A.a_id = B.a_id AND a_cup = 0 AND B.c_id = C.c_id AND C.c_id = J.tid AND J.prefix = \'c_\' AND J.pid = P.ID AND B.p_id = '.$pd->p_id.' ORDER BY A.a_id ASC';
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
								print("		<td>".$ca->a_name."</td>\n		<td><a href=\"".$ca->guid."\" title=\"View full details about ".$ca->post_title."\">".$ca->post_title."</a></td>\n		<td>".$ca->apc_value."</td>\n	</tr>\n");
								$zebracount++;
							}
						print("</table>\n");
						}
						else {
							$cafail = 1;
						}

						if ($cafail && $safail && $ccfail) {
							//no awards at all
							print("	<div class=\"info\">\n		<p>This player has not any awards as of yet.</p>\n	</div>\n");
						}


						$has_played = 1;
					}//end of if player has played a game
					else {
						//Player has not made debut yet
						print("	<div class=\"info\">\n	 <p>This player has not made their Debut yet. Stay tuned for further developments.</p>\n	</div>\n");
					}

?>
					<p class="postmeta"><?php edit_post_link( __( 'Edit', 'oberwald' ), ' <strong>[</strong> ', ' <strong>]</strong> '); ?></p>

				</div>
			</div>
		<?php
		endwhile; ?>



	<?php endif; ?>

<?php
	//Gathering data for the sidebar
	//determine player race
	$racesql = 'SELECT B.guid, R.r_name, R.r_id FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'position O, '.$wpdb->prefix.'race R, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' B WHERE R.r_id = J.tid AND J.prefix = \'r_\' AND J.pid = B.ID AND O.pos_id = P.pos_id AND O.r_id = R.r_id AND P.p_id = '.$pd->p_id;
	$rd = $wpdb->get_row($racesql);

	//determine debut season
	//$seasondebutsql = 'SELECT O.guid, O.post_title FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'match_team T, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'season S, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' O WHERE S.sea_id = J.tid AND J.prefix = \'sea_\' AND J.pid = O.ID AND C.sea_id = S.sea_id AND C.c_id = M.c_id AND C.c_show = 1 AND C.c_counts = 1 AND M.m_id = T.m_id AND T.t_id = P.t_id AND P.p_id = '.$pd->p_id.' ORDER BY M.m_date ASC LIMIT 1';
	$seasondebutsql = 'SELECT O.guid, O.post_title FROM '.$wpdb->prefix.'match_player P, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' O WHERE C.sea_id = J.tid AND J.prefix = \'sea_\' AND J.pid = O.ID AND P.m_id = M.m_id AND M.c_id = C.c_id AND C.c_counts = 1 AND C.c_show = 1 AND C.type_id = 1 AND P.p_id = '.$pd->p_id.' ORDER BY C.sea_id ASC LIMIT 1';
	$sd = $wpdb->get_row($seasondebutsql);

	//grab list of other players on the team
	$otherplayerssql = 'SELECT O.post_title, O.guid FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' O WHERE P.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = O.ID AND P.p_id != '.$pd->p_id.' AND P.t_id = '.$pd->t_id.' ORDER BY RAND() LIMIT 5';
	$otherplayers = $wpdb->get_results($otherplayerssql);

	//SQL for chapsionships won. like above but restricted to Winner Only!
	$playerchampionshipssql = 'SELECT A.a_name, P.post_title, P.guid FROM '.$wpdb->prefix.'player X, '.$wpdb->prefix.'awards A, '.$wpdb->prefix.'awards_team_comp B, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P, '.$wpdb->prefix.'match_player Z, '.$wpdb->prefix.'match V WHERE X.p_id = Z.p_id AND V.m_id = Z.m_id AND V.c_id = C.c_id AND X.t_id = B.t_id AND A.a_id = B.a_id AND a_cup = 1 AND B.c_id = C.c_id AND C.c_id = J.tid AND J.prefix = \'c_\' AND J.pid = P.ID AND A.a_id = 1 AND X.p_id = '.$pd->p_id.' GROUP BY C.c_id ORDER BY A.a_id ASC LIMIT 0, 30 ';
?>

<?php get_sidebar('content'); ?>

</div><!-- end of #maincontent -->
	<div id="subcontent">
		<ul>
<?php
			if (!empty($pd->p_img)) {
				//if the player has an image set, display it.
?>
			<li class="sidelogo"><img src="<?php print(home_url()); ?>/images/players/<?php print($pd->p_img); ?>" alt="Picture of <?php the_title(); ?>" /></li>
<?php
			}
?>
			<li class="sideinfo"><h2>Player Information</h2>
			  <ul>
<?php
				//Check to see if the Player is the Captain
				$captainsql = 'SELECT tcap_status FROM '.$wpdb->prefix.'team_captain WHERE p_id = '.$pd->p_id.' ORDER BY P_id ASC LIMIT 1';
				if ($pcap = $wpdb->get_var($captainsql)) {
					if ($pcap) {
						print(			   "<li><strong>Current Captain</strong></li>\n");
					}
					else if (0 == $pcap){
						print(			   "<li><strong>Former Captain</strong></li>\n");
					}
				}
?>
			   <li><strong>Status:</strong> <?php print($status); ?></li>
			   <li><strong>Rank:</strong> <?php print($plevel); ?></li>
			   <li><strong>Team:</strong> <a href="<?php print( get_post_permalink( $pd->WPID ) ); ?>" title="Read more on this team"><?php print( esc_html( get_the_title( $pd->WPID ) ) ); ?></a></li>
			   <li><strong>Position Number:</strong> #<?php print($pd->p_num); ?></li>
<?php
				 $race_check = (array)$rd; //cast the object to an array so we can check to see if something was returned
				 if ( !empty($race_check) ) {
					 //Mercs and Journeymen will not return a race ID as the race positions are not assigned to a race
					 //Only display the players race if they are a pernament race position
?>
			   <li><strong>Race:</strong> <a href="<?php print($rd->guid); ?>" title="Learn more about <?php print($rd->r_name); ?> teams"><?php print($rd->r_name); ?></a></li>
<?php
				 }

			if ($has_played) {
?>
			   <li><strong>Debut:</strong> <a href="<?php print($sd->guid); ?>" title="Read more on <?php print($sd->post_title); ?>"><?php print($sd->post_title); ?></a></li>
<?php
			}
?>
			  </ul>
			</li>
<?php
			if ($has_played) {
?>
			<li class="sideawards"><h2>Major Awards</h2>
<?php
			//note that both SQL strings are above
			if (($sawards = $wpdb->get_results($seasonsql)) || ($cawards = $wpdb->get_results($playerchampionshipssql))) {
				print("<ul>\n");
				foreach ($cawards as $ca) {
					print("		<li><strong>".$ca->a_name."</strong> - <a href=\"".$ca->guid."\" title=\"View full details about ".$ca->post_title."\">".$ca->post_title."</a></li>\n");
				}
				foreach ($sawards as $sa) {
					print("		<li><strong>".$sa->a_name."</strong> - <a href=\"".$sa->guid."\" title=\"View full details about ".$sa->post_title."\">".$sa->post_title."</a></li>\n");
				}
				print("</ul>\n");
			}
		else {
			print("<p>This player has not won any major awards yet</p>\n");
		}
		print("<p><a href=\"#awardsfull\" title=\"View all awards this player has won\">View all awards this player has won &gt;&gt;</a></p>");
?>


			</li>
			<li><h2>Currently Participating in</h2>
<?php
			//current competitions
			$currentcompssql = 'SELECT O.post_title, O.guid FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'team_comp M, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' O WHERE P.t_id = M.t_id AND C.c_id = J.tid AND J.prefix = \'c_\' AND J.pid = O.ID AND M.c_id = C.c_id AND C.c_counts = 1 AND C.c_show = 1 AND C.c_active = 1 AND T.t_id = M.t_id AND P.p_id = '.$pd->p_id.' GROUP BY C.c_id LIMIT 0, 30 ';
				if ($currentcomps = $wpdb->get_results($currentcompssql)) {
					print("			  <ul>\n");
						foreach ($currentcomps as $curc) {
							print("					<li><a href=\"".$curc->guid."\" title=\"Read more about ".$curc->post_title."\">".$curc->post_title."</a></li>\n");
						}
					print("			  </ul>\n");
				}
				else {
					print("<p>This player is currently not taking part in any Competitions.</p>\n");
				}
?>
			</li>
<?php
			}//end of if played
?>
			<li><h2>Other Players on this team (random)</h2>
				<ul>
<?php
				foreach ($otherplayers as $op) {
					print("					<li><a href=\"".$op->guid."\" title=\"Read more about ".$op->post_title."\">".$op->post_title."</a></li>\n");
				}
?>
				</ul>
			</li>
			<?php if ( !dynamic_sidebar('sidebar-common') ) : ?>
				<li><h2 class="widgettitle">Search</h2>
				  <ul>
				   <li><?php get_search_form(); ?></li>
				  </ul>
				</li>
<?php endif;?>

		</ul>
	</div><!-- end of #subcontent -->
<?php get_footer(); ?>
