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
 <?php do_action( 'bblm_template_before_posts' ); ?>
 <?php if (have_posts()) : ?>
   <?php do_action( 'bblm_template_before_loop' ); ?>
   <?php while (have_posts()) : the_post(); ?>
     <?php do_action( 'bblm_template_before_content' ); ?>

			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>


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
			<header class="entry-header">
				<h2 class="entry-title"><a href="<?php print( get_post_permalink( $tA->WPID ) ); ?>" title="Read more on this team"><?php print( $teamA ); ?></a> vs <a href="<?php print( get_post_permalink( $tB->WPID ) ); ?>" title="Read more on this team"><?php print( $teamB ); ?></a></h2>
			</header><!-- .entry-header -->

			<div class="entry-content">

				<table class="bblm_table">
					<thead>
						<tr>
							<th class="bblm_tbl_name"><?php print( $teamA );?></th>
							<th class="bblm_tbl_name">VS</th>
							<th class="bblm_tbl_name"><?php print( $teamB );?></th>
						</tr>
					</thead>
					<tbody>
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
					</tbody>
				</table>

				<h3><?php echo __( 'Match Report', 'bblm' ); ?></h3>
				<div class="bblm_details bblm_match_report">
					<?php the_content(); ?>
				</div>


<?php
				//Display match Trivia if something is present
				if ("" !== $m->m_trivia) {
?>
					<h3><?php echo __( 'Match Trivia', 'bblm' ); ?></h3>
					<div class="bblm_details bblm_match_trivia">
						<p><?php echo esc_html($m->m_trivia); ?></p>
					</div>
<?php
} // end of if ("" !== $m->m_trivia) {
?>
			<h3><?php echo __( 'Player Actions', 'bblm' ); ?></h3>
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
						<th class="bblm_tottux">MVP</th>
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
						<th class="bblm_tottux">Inj</th>
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
						<th class="bblm_tottux">Inc</th>
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
						<th class="bblm_tottux">Comms</th>
						<td><?php print(stripslashes($tB->mt_comment));?></td>
					</tr>
				</table>
<?php
		} //end of if match SQL

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
<?php get_sidebar(); ?>
<?php get_footer(); ?>
