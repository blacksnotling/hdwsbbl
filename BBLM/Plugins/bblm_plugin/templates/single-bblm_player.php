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
 ?>
 <?php get_header(); ?>
  <?php do_action( 'bblm_template_before_posts' ); ?>
	<?php if (have_posts()) : ?>
		<?php do_action( 'bblm_template_before_loop' ); ?>
		<?php while (have_posts()) : the_post(); ?>
			<?php do_action( 'bblm_template_before_content' ); ?>
		<?php
			/*
			Gather Information for page
			*/
			$playersql = 'SELECT P.*, T.WPID, E.pos_name FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' X, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'position E WHERE P.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = X.ID AND X.ID = '.$post->ID.' AND P.t_id = T.t_id AND P.pos_id = E.pos_id';
			//if ($player = $wpdb->get_results($playersql)) {
			if ( $pd = $wpdb->get_row( $playersql ) ) {
				$pspp = $pd->p_spp;

			} //end of if playersql

				switch ( $pspp ) {
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

				if ( 0 == $pd->p_status ) {
					$status = "Inactive";
				}
				else {
					$status = "Active";
				}
		?>

		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

			<header class="entry-header">
				<h2 class="entry-title"><?php the_title(); ?></h2>
			</header><!-- .entry-header -->

				<div class="entry-content">

					<table class="bblm_table">
						<thead>
							<tr>
								<th class="bblm_tbl_name"><?php echo __( 'Position', 'bblm' ); ?></th>
								<th class="bblm_tbl_stat"><?php echo __( 'MA', 'bblm' ); ?></th>
								<th class="bblm_tbl_stat"><?php echo __( 'ST', 'bblm' ); ?></th>
								<th class="bblm_tbl_stat"><?php echo __( 'AG', 'bblm' ); ?></th>
								<th class="bblm_tbl_stat"><?php echo __( 'AV', 'bblm' ); ?></th>
								<th><?php echo __( 'Skills', 'bblm' ); ?></th>
								<th><?php echo __( 'Injuries', 'bblm' ); ?></th>
								<th><?php echo __( 'Cost', 'bblm' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><?php echo esc_html( $pd->pos_name ); ?></td>
								<td><?php echo $pd->p_ma; ?></td>
								<td><?php echo $pd->p_st; ?></td>
								<td><?php echo $pd->p_ag; ?></td>
								<td><?php echo $pd->p_av; ?></td>
								<td class="bblm_tbl_skills"><?php echo $pd->p_skills; ?></td>
								<td><?php echo $pd->p_injuries; ?></td>
								<td><?php echo number_format( $pd->p_cost ); ?>gp</td>
							</tr>
						</tbody>
					</table>

					<div class="bblm_details bblm_player_description">
						<?php the_content(); ?>
					</div>

<?php
					if (0 == $pd->p_status) {
						//If the player is inactive, see if they were killed.
						$fatesql = 'SELECT pf_killer, f_id, pf_desc FROM `'.$wpdb->prefix.'player_fate` WHERE ( f_id = 1 OR f_id = 6 OR f_id = 7 ) AND p_id = '.$pd->p_id.' LIMIT 1';
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
							print("							<div class=\"bblm_details bblm_obit\">\n							<p>".$fate->pf_desc."</p>\n							</div>\n");
						}
					}

					// -- KILLER --
					$killersql = 'SELECT O.post_title AS PLAYER, O.guid AS PLAYERLink, T.WPID, X.pos_name FROM '.$wpdb->prefix.'player_fate F, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' O, '.$wpdb->prefix.'position X WHERE F.p_id = P.p_id AND P.t_id = T.t_id AND P.pos_id = X.pos_id AND F.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = O.ID AND F.pf_killer = '.$pd->p_id.' AND F.p_id != '.$pd->p_id.' ORDER BY F.m_id ASC';
					if ( $killer = $wpdb->get_results( $killersql ) ) {
						//If the player has killed people
?>
						<h3><?php echo __( 'Killer!', 'bblm' ); ?></h3>
						<p><?php echo __( 'This player has killed another player in the course of their career. They have killed the following players:', 'bblm' ); ?></p>
						<ul>
<?php
						foreach ( $killer as $k ) {

							echo '<li><a href="' . $k->PLAYERLink . '" title="Read more about ' . $k->PLAYER . '">' . $k->PLAYER . '</a> (' . esc_html( $k->pos_name ) . ' for <a href="' . get_post_permalink( $k->WPID ) . '" title="Read more about this team">' . esc_html( get_the_title( $k->WPID ) ) . '</a>)</li>';
						}
?>
						</ul>
<?php
					}


					$statssql = 'SELECT COUNT(*) AS GAMES, SUM(M.mp_td) AS TD, SUM(M.mp_cas) AS CAS, SUM(M.mp_comp) AS COMP, SUM(M.mp_int) AS MINT, SUM(M.mp_mvp) AS MVP, SUM(M.mp_spp) AS SPP, T.WPID, M.mp_counts FROM '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'team T WHERE M.t_id = T.t_id AND M.p_id = '.$pd->p_id.' GROUP BY M.mp_counts, T.t_id ORDER BY M.mp_counts DESC, T.t_id DESC';
					if ( $stats = $wpdb->get_results( $statssql ) ) {
						//This player has played at least one game

						//Transfers
						//Before we display the stats, we check to see if the player has been transfered, and display the transfer history for them
						$trans = new BBLM_CPT_Transfer;
						$trans->display_player_transfer_history();
	?>

						<h3><?php echo __( 'Player Statistics', 'bblm' ); ?></h3>
						<table class="bblm_table">
							<thead>
	 							<tr>
	 								<th class="bblm_tbl_title"><?php echo __( 'Performance', 'bblm' ); ?></th>
	 								<th class="bblm_tbl_stat"><?php echo __( 'P', 'bblm' ); ?></th></th>
	 								<th class="bblm_tbl_stat"><?php echo __( 'TD', 'bblm' ); ?></th></th>
	 								<th class="bblm_tbl_stat"><?php echo __( 'CAS', 'bblm' ); ?></th></th>
	 								<th class="bblm_tbl_stat"><?php echo __( 'COMP', 'bblm' ); ?></th></th>
	 								<th class="bblm_tbl_stat"><?php echo __( 'INT', 'bblm' ); ?></th></th>
	 								<th class="bblm_tbl_stat"><?php echo __( 'MVP', 'bblm' ); ?></th></th>
	 								<th class="bblm_tbl_stat"><?php echo __( 'SPP', 'bblm' ); ?></th></th>
	 							</tr>
							</thead>
							<tbody>
<?php
						foreach ( $stats as $s ) {
							if ( $s->mp_counts ) {
								echo '<tr>';
								echo '<td><a href="' . get_post_permalink( $s->WPID ) . '" title="Read more about this team">' . esc_html( get_the_title( $s->WPID ) ) . '</a></td>';
							}
							else {
								echo '<tr>';
								echo '<td>' . __('Exhibition Record', 'bblm') . '</td>';
							}

							echo '<td>' . $s->GAMES . '</td>';
							echo '<td>' . $s->TD . '</td>';
							echo '<td>' . $s->CAS . '</td>';
							echo '<td>' . $s->COMP . '</td>';
							echo '<td>' . $s->MINT . '</td>';
							echo '<td>' . $s->MVP . '</td>';
							echo '<td>' . $s->SPP . '</td>';
							echo '</tr>';
						}
	?>
							</tbody>
						</table>


						<h3><?php echo __( 'Breakdown by Competition', 'bblm' ); ?></h3>
						<table class="bblm_table">
							<thead>
								<tr>
									<th class="bblm_tbl_title"><?php echo __( 'Competition', 'bblm' ); ?></th>
									<th class="bblm_tbl_stat"><?php echo __( 'P', 'bblm' ); ?></th>
									<th class="bblm_tbl_stat"><?php echo __( 'TD', 'bblm' ); ?></th>
									<th class="bblm_tbl_stat"><?php echo __( 'CAS', 'bblm' ); ?></th>
									<th class="bblm_tbl_stat"><?php echo __( 'INT', 'bblm' ); ?></th>
									<th class="bblm_tbl_stat"><?php echo __( 'COMP', 'bblm' ); ?></th>
									<th class="bblm_tbl_stat"><?php echo __( 'MVP', 'bblm' ); ?></th>
									<th class="bblm_tbl_stat"><?php echo __( 'SPP', 'bblm' ); ?></th>
								</tr>
							</thead>
							<tbody>
<?php
					$playercompsql = 'SELECT COUNT(*) AS GAMES, SUM(M.mp_td) AS TD, SUM(M.mp_cas) AS CAS, SUM(M.mp_comp) AS COMP, SUM(M.mp_int) AS MINT, SUM(M.mp_mvp) AS MVP, SUM(M.mp_spp) AS SPP, S.guid, S.post_title FROM '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'match Q, '.$wpdb->prefix.'bb2wp R, '.$wpdb->posts.' S WHERE C.c_id = R.tid AND R.pid = S.ID AND R.prefix = \'c_\' AND M.m_id = Q.m_id AND Q.c_id = C.c_id AND C.c_show = 1 AND M.p_id = P.p_id AND M.p_id = '.$pd->p_id.' GROUP BY C.c_id ORDER BY C.c_id DESC';
					if ( $playercomp = $wpdb->get_results( $playercompsql ) ) {
						$zebracount = 1;
						foreach ( $playercomp as $pc ) {
							if ( $zebracount % 2 ) {
								echo '<tr>';
							}
							else {
								echo '<tr class="bblm_tbl_alt">';
							}
							echo '<td><a href="' . $pc->guid .'" title="View more details about this competition">' . $pc->post_title . '</a></td>';
							echo '<td>' . $pc->GAMES . '</td>';
							echo '<td>' . $pc->TD . '</td>';
							echo '<td>' . $pc->CAS . '</td>';
							echo '<td>' . $pc->MINT . '</td>';
							echo '<td>' . $pc->COMP . '</td>';
							echo '<td>' . $pc->MVP . '</td>';
							echo '<td>' . $pc->SPP . '</td>';
							echo '</tr>';
							$zebracount++;
						}
					}
?>
							</tbody>
						</table>

						<h3><?php echo __( 'Breakdown by Season', 'bblm' ); ?></h3>
						<table class="bblm_table">
							<thead>
								<tr>
									<th class="bblm_tbl_title"><?php echo __( 'Season', 'bblm' ); ?></th>
									<th class="bblm_tbl_stat"><?php echo __( 'P', 'bblm' ); ?></th>
									<th class="bblm_tbl_stat"><?php echo __( 'TD', 'bblm' ); ?></th>
									<th class="bblm_tbl_stat"><?php echo __( 'CAS', 'bblm' ); ?></th>
									<th class="bblm_tbl_stat"><?php echo __( 'INT', 'bblm' ); ?></th>
									<th class="bblm_tbl_stat"><?php echo __( 'COMP', 'bblm' ); ?></th>
									<th class="bblm_tbl_stat"><?php echo __( 'MVP', 'bblm' ); ?></th>
									<th class="bblm_tbl_stat"><?php echo __( 'SPP', 'bblm' ); ?></th>
								</tr>
							</thead>
							<tbody>
<?php
					$playerseasql = 'SELECT C.sea_id, COUNT(*) AS GAMES, SUM(M.mp_td) AS TD, SUM(M.mp_cas) AS CAS, SUM(M.mp_comp) AS COMP, SUM(M.mp_int) AS MINT, SUM(M.mp_mvp) AS MVP, SUM(M.mp_spp) AS SPP FROM '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'match Q WHERE C.c_counts = 1 AND C.c_show = 1 AND M.m_id = Q.m_id AND Q.c_id = C.c_id AND M.p_id = P.p_id AND M.p_id = '.$pd->p_id.' GROUP BY C.sea_id ORDER BY C.sea_id DESC';
					if ( $playersea = $wpdb->get_results( $playerseasql ) ) {
					$zebracount = 1;
						foreach ( $playersea as $pc ) {
							if ( $zebracount % 2 ) {
								echo '<tr>';
							}
							else {
								echo '<tr class="bblm_tbl_alt">';
							}
							echo '<td>' . bblm_get_season_link( $pc->sea_id ) . '</td>';
							echo '<td>' . $pc->GAMES . '</td>';
							echo '<td>' . $pc->TD . '</td>';
							echo '<td>' . $pc->CAS . '</td>';
							echo '<td>' . $pc->MINT . '</td>';
							echo '<td>' . $pc->COMP . '</td>';
							echo '<td>' . $pc->MVP . '</td>';
							echo '<td>' . $pc->SPP . '</td>';
							echo '</tr>';

							$zebracount++;
						}
					}
?>
						</tbody>
					</table>

					<h3><?php echo __( 'Recent Matches', 'bblm' ); ?></h3>
						<table class="bblm_table bblm_sortable bblm_expandable">
							<thead>
								<tr>
									<th><?php echo __( 'Date', 'bblm' ); ?></th>
									<th><?php echo __( 'Opponant', 'bblm' ); ?></th>
									<th><?php echo __( 'TD', 'bblm' ); ?></th>
									<th><?php echo __( 'CAS', 'bblm' ); ?></th>
									<th><?php echo __( 'INT', 'bblm' ); ?></th>
									<th><?php echo __( 'COMP', 'bblm' ); ?></th>
									<th><?php echo __( 'MVP', 'bblm' ); ?></th>
									<th><?php echo __( 'SPP', 'bblm' ); ?></th>
									<th><?php echo __( 'MNG?', 'bblm' ); ?></th>
									<th><?php echo __( 'Increase', 'bblm' ); ?></th>
									<th><?php echo __( 'Injury', 'bblm' ); ?></th>
								</tr>
							</thead>
							<tbody>
<?php
						$playermatchsql = 'SELECT M.*, P.p_name, UNIX_TIMESTAMP(X.m_date) AS mdate, G.post_title AS TA, T.t_id AS TAid, G.guid AS TAlink, B.post_title AS TB, B.guid AS TBlink, R.t_id AS TBid, Z.guid FROM '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'match X, '.$wpdb->prefix.'bb2wp Y, '.$wpdb->posts.' Z, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'team R, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp F, '.$wpdb->posts.' G, '.$wpdb->prefix.'bb2wp V, '.$wpdb->posts.' B WHERE T.t_id = F.tid AND F.prefix = \'t_\' AND F.pid = G.ID AND R.t_id = V.tid AND V.prefix = \'t_\' AND V.pid = B.ID AND C.c_id = X.c_id AND C.c_show = 1 AND X.m_teamA = T.t_id AND X.m_teamB = R.t_id AND M.p_id = P.p_id AND M.m_id = X.m_id AND X.m_id = Y.tid AND Y.prefix = \'m_\' AND Y.pid = Z.ID AND M.p_id = '.$pd->p_id.' ORDER BY X.m_date DESC';
						if ( $playermatch = $wpdb->get_results( $playermatchsql ) ) {
						$zebracount = 1;
							foreach ( $playermatch as $pm ) {
								if ( ( $zebracount % 2 ) && ( 10 < $zebracount ) ) {
									echo '<tr class="bblm_tbl_hide">';
								}
								else if ( ( $zebracount % 2 ) && ( 10 >= $zebracount ) ) {
									echo '<tr>';
								}
								else if ( 10 < $zebracount ) {
									echo '<tr class="bblm_tbl_alt bblm_tbl_hide">';
								}
								else {
									echo '<tr class="bblm_tbl_alt">';
								}
								echo '<td><a href="' . $pm->guid . '" title="View the match in more detail">' . date( "d.m.y", $pm->mdate ). '</a></td>';
								if ( $pm->TAid == $pd->t_id ) {
									echo '<td><a href="' . $pm->TBlink . '" title="View more about this match">' . $pm->TB . '</a></td>';
								}
								else {
									echo '<td><a href="' . $pm->TAlink . '" title="View more about this match">' . $pm->TA . '</a></td>';
								}
								echo '<td>';
								if ( 0 == $pm->mp_td ) {
									echo '0';
								}
								else {
									echo '<strong>' . $pm->mp_td . '</strong>';
								}
								echo '</td>';
								echo '<td>';
								if ( 0 == $pm->mp_cas ) {
									echo '0';
								}
								else {
									echo '<strong>' . $pm->mp_cas . '</strong>';
								}
								echo '</td>';
								echo '<td>';
								if ( 0 == $pm->mp_int ) {
									echo '0';
								}
								else {
									echo '<strong>' . $pm->mp_int . '</strong>';
								}
								echo '</td>';
								echo '<td>';
								if ( 0 == $pm->mp_comp ) {
									echo '0';
								}
								else {
									echo '<strong>' . $pm->mp_comp. '</strong>';
								}
								echo '</td>';
								echo '<td>';
								if ( 0 == $pm->mp_mvp ) {
									echo '0';
								}
								else {
									echo '<strong>' . $pm->mp_mvp . '</strong>';
								}
								echo '</td>';
								echo '<td>';
								if ( 0 == $pm->mp_spp ) {
									echo '0';
								}
								else {
									echo '<strong>' . $pm->mp_spp . '</strong>';
								}
								echo '</td>';
								echo '<td>';
								if ( 0 == $pm->mp_mng ) {
									echo '0';
								}
								else {
									echo '<strong>Y</strong>';
								}
								echo '</td>';
								echo '<td>';
								if ( "none" == $pm->mp_inc ) {
									echo '-';
								}
								else {
									echo '<strong>' . $pm->mp_inc . '</strong>';
								}
								echo '</td>';
								echo '<td>';
								if ( "none" == $pm->mp_inj ) {
									echo '-';
								}
								else {
									echo '<strong>' . $pm->mp_inj . '</strong>';
								}
								echo '</td>';
								$zebracount++;
							}
						}
?>
							</tbody>
						</table>

						<h3><?php echo __( 'Awards list in full', 'bblm' ); ?></h3>
<?php
						$championshipssql = 'SELECT A.a_name, P.post_title, P.guid FROM '.$wpdb->prefix.'player X, '.$wpdb->prefix.'awards A, '.$wpdb->prefix.'awards_team_comp B, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P, '.$wpdb->prefix.'match_player Z, '.$wpdb->prefix.'match V WHERE X.p_id = Z.p_id AND V.m_id = Z.m_id AND V.c_id = C.c_id AND X.t_id = B.t_id AND A.a_id = B.a_id AND a_cup = 1 AND B.c_id = C.c_id AND C.c_id = J.tid AND J.prefix = \'c_\' AND J.pid = P.ID AND X.p_id = '.$pd->p_id.' GROUP BY C.c_id ORDER BY A.a_id ASC LIMIT 0, 30 ';
						if ($champs = $wpdb->get_results($championshipssql)) {
							$has_cups = 1;
							$zebracount = 1;
							print("<h4>Championships</h4>\n");
							print("<table class=\"bblm_table\">\n	<tr>\n		<th class=\"bblm_tbl_name\">Title</th>\n		<th class=\"bblm_tbl_name\">Competition</th>\n	</tr>\n");
							foreach ($champs as $cc) {
								if ($zebracount % 2) {
									print("		<tr>\n");
								}
								else {
									print("		<tr class=\"bblm_tbl_alt\">\n");
								}
								print("		<td>".$cc->a_name."</td>\n		<td><a href=\"".$cc->guid."\" title=\"View full details about ".$cc->post_title."\">".$cc->post_title."</a></td>\n	</tr>\n");
								$zebracount++;
							}
							print("</table>\n");
						}
						else {
							$ccfail = 1;
						}

						$seasonsql = 'SELECT A.a_name, B.sea_id, B.aps_value FROM '.$wpdb->prefix.'awards A, '.$wpdb->prefix.'awards_player_sea B WHERE A.a_id = B.a_id AND B.p_id = '.$pd->p_id.' ORDER BY A.a_id ASC';
						if ($sawards = $wpdb->get_results($seasonsql)) {
							$zebracount = 1;
							print("<h4>Awards from Seasons</h4>\n");
							print("<table class=\"bblm_table\">\n	<tr>\n		<th class=\"bblm_tbl_name\">Award</th>\n		<th class=\"bblm_tbl_name\">Competition</th>\n		<th class=\"bblm_tbl_stat\">Value</th>\n	</tr>\n");
							foreach ($sawards as $sa) {
								if ($zebracount % 2) {
									print("		<tr>\n");
								}
								else {
									print("		<tr class=\"bblm_tbl_alt\">\n");
								}
								print("		<td>".$sa->a_name."</td>\n		<td>" . bblm_get_season_link( $sa->sea_id ) . "</td>\n		<td>".$sa->aps_value."</td>\n	</tr>\n");
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
							print("<table class=\"bblm_table\">\n	<tr>\n		<th class=\"bblm_tbl_name\">Award</th>\n		<th class=\"bblm_tbl_name\">Competition</th>\n		<th class=\"bblm_tbl_stat\">Value</th>\n	</tr>\n");
							foreach ($cawards as $ca) {
								if ($zebracount % 2) {
									print("		<tr>\n");
								}
								else {
									print("		<tr class=\"bblm_tbl_alt\">\n");
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
							print("	<div class=\"bblm_info\">\n		<p>This player has not any awards as of yet.</p>\n	</div>\n");
						}


						$has_played = 1;
					}//end of if player has played a game
					else {
						//Player has not made debut yet
						print("	<div class=\"bblm_info\">\n	 <p>This player has not made their Debut yet. Stay tuned for further developments.</p>\n	</div>\n");
					}

?>
				<footer class="entry-footer">
					<p class="postmeta"><?php bblm_display_page_edit_link(); ?></p>
				</footer><!-- .entry-footer -->

				</div><!-- .entry-content -->
			</article>
		<?php
		endwhile; ?>



	<?php endif; ?>

<?php
	//Gathering data for the sidebar
	//determine player race
	$racesql = 'SELECT B.guid, R.r_name, R.r_id FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'position O, '.$wpdb->prefix.'race R, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' B WHERE R.r_id = J.tid AND J.prefix = \'r_\' AND J.pid = B.ID AND O.pos_id = P.pos_id AND O.r_id = R.r_id AND P.p_id = '.$pd->p_id;
	$rd = $wpdb->get_row($racesql);

	//determine debut season
	$seasondebutsql = 'SELECT C.sea_id FROM '.$wpdb->prefix.'match_player P, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C WHERE P.m_id = M.m_id AND M.c_id = C.c_id AND C.c_counts = 1 AND C.c_show = 1 AND C.type_id = 1 AND P.p_id = '.$pd->p_id.' ORDER BY C.sea_id ASC LIMIT 1';
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
			<li class="widget_bblm_playerdetails"><h2>Player Information</h2>
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
			   <li><strong>Debut:</strong> <?php echo bblm_get_season_link( $sd->sea_id ); ?></li>
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
					print("		<li><strong>".$sa->a_name."</strong> - " . bblm_get_season_link( $sa->sea_id ) . "</li>\n");
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
			$currentcompssql = 'SELECT O.post_title, O.guid FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'team_comp M, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' O WHERE P.t_id = M.t_id AND C.c_id = J.tid AND J.prefix = \'c_\' AND J.pid = O.ID AND M.c_id = C.c_id AND C.c_show = 1 AND C.c_active = 1 AND T.t_id = M.t_id AND P.p_id = '.$pd->p_id.' GROUP BY C.c_id LIMIT 0, 30 ';
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
