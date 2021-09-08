<?php
/**
 * BBowlLeagueMan Teamplate View Roster
 *
 * Page Template to view a Roster
 *
 * @author 		Blacksnotling
 * @category 	Template
 * @package 	BBowlLeagueMan/Templates
 */
/*
 * Template Name: View Roster
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
<?php wp_head(); ?>
<style type="text/css">
html * {
	margin:0;
	padding:0;
}
@media screen {
body {
	font-size: 0.8em;
}
h1, div, table, p {
	margin-bottom: 0.8em
}
h1 {
	font-size: 1.6em;
}
table {
	margin-left: auto;
	margin-right: auto;
	background-color: #fff;
	border: 1px solid #000;
	border-collapse: collapse;
}
table td, table td:first-child, table td:last-child {
	text-align: center;
	border-left-style: dotted;
	border-bottom-style: solid;
	border-color: #000000;
	border-width: 1px;
	padding: 0.3em;
	vertical-align: middle;
}
table th, table th:first-child, table th:last-child {
	border-bottom: 4px solid #000;
	border-top: 4px solid #d9101d;
	vertical-align: middle;
}
table th.bblm_tbl_enchance, table th.bblm_tbl_title {
	border-left-style: dotted;
	border-bottom-style: solid;
	border-color: #000;
	border-width: 1px;
}
table th, table td.bblm_tbl_label {
	background-color: #163964;
	color: #fff;
	font-weight: bold;
	text-align: center;
	text-transform: uppercase;
}
td.bblm_tbl_image {
	vertical-align: middle;
}
.bblm_tbl_stat {
	width:25px;
}
.bblm_tbl_skills {
	width:300px;
	text-align:left;
	font-size: smaller;
}
.bblm_tbl_name {
	width:200px;
}
.bblm_tbl_value {
	width:50px;
	text-align:right;
}
#footer {
	color: #666;
}
a, a:link, a:visited {
	color: #163964;
	text-decoration: none;
}

a:hover, a:active {
	color: #d9101d;
	text-decoration: underline;
}
} /* end of media screen */
@media print {
	#footer {
		display: none;
	}
	#maincontent {
		width: 100%;
	}
	body {
		font-size: 0.7em;
	}
	td.bblm_tbl_image {
		vertical-align: middle;
	}
	.bblm_tbl_skills {
		text-align:left;
		font-size: smaller;
	}

}
</style>
</head>
<body>

<div id="wrapper">
	<div id="pagecontent">
		<div id="maincontent">
			<?php if (have_posts()) : ?>
				<?php while (have_posts()) : the_post(); ?>

		<?php
				$teaminfosql = 'SELECT T.*, J.tid AS teamid, T.r_id, T.stad_id, T.WPID AS TWPID FROM '.$wpdb->prefix.'team T, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE T.t_id = J.tid AND T.t_id = J.tid AND J.prefix = \'roster\' AND J.pid = P.ID AND P.ID = ' . $post->ID;
				if ( $ti = $wpdb->get_row( $teaminfosql ) ) {
					$tid = $ti->teamid;
					$team_link = bblm_get_team_link( $ti->TWPID );

					//determine Team Captain
					$teamcap = 0;
					$teamcaptainsql = 'SELECT * FROM '.$wpdb->prefix.'team_captain WHERE tcap_status = 1 and t_id = ' . $tid;
					if ( $tcap = $wpdb->get_row( $teamcaptainsql ) ) {
						$teamcap = $tcap->p_id;
					}

					$rr_cost = (int) BBLM_CPT_Race::get_reroll_cost( $ti->r_id );
				}
		?>
			<table border="0">
				<thead>
					<tr>
						<th class="bblm_tbl_stat"><?php echo __('No.', 'bblm'); ?></th>
						<th>Name</th>
						<th>Position</th>
						<th class="bblm_tbl_stat"><?php echo __('MA', 'bblm'); ?></th>
						<th class="bblm_tbl_stat"><?php echo __('ST', 'bblm'); ?></th>
						<th class="bblm_tbl_stat"><?php echo __('AG', 'bblm'); ?></th>
						<th class="bblm_tbl_stat"><?php echo __('PA', 'bblm'); ?></th>
						<th class="bblm_tbl_stat"><?php echo __('AV', 'bblm'); ?></th>
						<th><?php echo __('Skills / Injuries', 'bblm'); ?></th>
						<th class="bblm_tbl_stat"><?php echo __('TD', 'bblm'); ?></th>
						<th class="bblm_tbl_stat"><?php echo __('CAS', 'bblm'); ?></th>
						<th class="bblm_tbl_stat"><?php echo __('COMP', 'bblm'); ?></th>
						<th class="bblm_tbl_stat"><?php echo __('INT', 'bblm'); ?></th>
						<th class="bblm_tbl_stat"><?php echo __('DEF', 'bblm'); ?></th>
						<th class="bblm_tbl_stat"><?php echo __('MVP', 'bblm'); ?></th>
						<th class="bblm_tbl_stat"><?php echo __('INJ', 'bblm'); ?></th>
						<th class="bblm_tbl_stat"><?php echo __('TR', 'bblm'); ?></th>
						<th class="bblm_tbl_stat"><?php echo __('Unspent SPP', 'bblm'); ?></th>
						<th><?php echo __('VALUE', 'bblm'); ?></th>
					</tr>
				</thead>
				<tbody>
<?php
					$playersql = 'SELECT K.post_title, K.guid, K.ID AS PWPID, L.pos_name, P.* FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' K, '.$wpdb->prefix.'position L WHERE P.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = K.ID AND P.pos_id = L.pos_id AND P.p_status = 1 AND P.t_id = '.$tid.' ORDER BY P.p_num ASC';
					$pcount = 1;
					if ( $players = $wpdb->get_results( $playersql ) ) {
						foreach ( $players as $pl ) {
							while ( $pcount < $pl->p_num ) {
								//print a generic row
?>
					<tr>
						<td><?php echo $pcount; ?></td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td class="bblm_tbl_skills">&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<?php
							$pcount++;
						}
						//checks to see the player belongs in this position
						if ( $pcount == $pl->p_num ) {

						$playerdetailssql = 'SELECT SUM(M.mp_td) AS PTD, SUM(M.mp_cas) AS PCAS, SUM(M.mp_comp) AS PCOMP, SUM(M.mp_ttm) AS PTTM, SUM(M.mp_int) AS PINT, SUM(M.mp_def) AS PDEF, SUM(M.mp_MVP) AS PMVP, SUM(M.mp_spp) AS PSPP FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'match N, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' O WHERE M.m_id = N.WPID AND N.c_id = C.WPID AND C.c_counts = 1 AND P.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = O.ID AND M.p_id = P.p_id AND M.mp_spp > 0 AND P.p_id = '.$pl->p_id;
						$pd = $wpdb->get_row( $playerdetailssql );
					?>
					<tr>
						<td><?php echo $pcount; ?></td>
						<td><?php echo bblm_get_player_link( $pl->PWPID ); if ($teamcap == $pl->p_id) { echo ' (C)';} ?></td>
						<td><?php echo esc_html( $pl->pos_name ); ?></td>
					  <td><?php echo $pl->p_ma; ?></td>
						<td><?php echo $pl->p_st; ?></td>
						<td><?php echo $pl->p_ag; ?>+</td>
						<td><?php echo $pl->p_pa; ?>+</td>
						<td><?php echo $pl->p_av; ?>+</td>
						<td class="bblm_tbl_skills"><?php echo $pl->p_skills; ?>
					<?php
						if ("none" !== $pl->p_injuries) {
							echo ', <em>'.$pl->p_injuries.'</em>';
						}
					?></td>
<?php
						//TouchDowns
						if ($pd->PTD == 0) {
							echo '<td>&nbsp;</td>';
						}
						else {
							echo '<td>' . $pd->PTD . '</td>';
						}

						//Casualities
						if ($pd->PCAS == 0) {
							echo '<td>&nbsp;</td>';
						}
						else {
							echo '<td>' . $pd->PCAS . '</td>';
						}

						//Completions + Throw Team mates
						if ($pd->PCOMP == 0) {
							echo '<td>&nbsp;</td>';
						}
						else {
							//Completions combines completions + Throw Team mates
							echo '<td>' . ( (int) $pd->PCOMP + (int) $pd->PTTM ) . '</td>';
						}

						//Interceptions
						if ($pd->PINT == 0) {
							echo '<td>&nbsp;</td>';
						}
						else {
							echo '<td>' . $pd->PINT . '</td>';
						}

						//Deflections
						if ($pd->PDEF == 0) {
							echo '<td>&nbsp;</td>';
						}
						else {
							echo '<td>' . $pd->PDEF . '</td>';
						}

						//MVP
						if ($pd->PMVP == 0) {
							echo '<td>&nbsp;</td>';
						}
						else {
							echo '<td>' . $pd->PMVP . '</td>';
						}

						//Miss Next Game Flag
						if ($pl->p_mng) {
							echo '<td>Y</td>';
						}
						else {
							echo '<td>&nbsp;</td>';
						}

						//Temporary Retired
						if ($pl->p_tr) {
							echo '<td>Y</td>';
						}
						else {
							echo '<td>&nbsp;</td>';
						}
?>
						<td><?php echo $pl->p_cspp; ?></td>
						<td><?php echo number_format( $pl->p_cost_ng ); ?>gp</td>
					</tr>
					<?php
									}
									$pcount++;
								}
							//now the sql has been completed, we have to print emtpy lines to ensure that 16 places are displayed!
								while ( 17 > $pcount ) {
									//print a generic row
					?>
					<tr>
						<td><?php echo $pcount; ?></td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td class="bblm_tbl_skills">&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
<?php
									$pcount++;

									}
							}
					?>
					 <!-- End of player listing -->
					<tr>
						<td colspan="3" rowspan="6" class="bblm_tbl_image">
<?php
							$filename = $_SERVER['DOCUMENT_ROOT']."/images/teams/".$ti->t_sname."_big.gif";
							if (file_exists($filename)) {
								echo "<img src=\"".home_url()."/images/teams/".$ti->t_sname."_big.gif\" alt=\"".$ti->t_sname." Logo\" />";
							}
							else {
								BBLM_CPT_Race::display_race_icon( $ti->r_id, 'medium' );
							}
?>
						</td>
						<td colspan="5" class="bblm_tbl_title bblm_tbl_label"><?php echo __('Team Name', 'bblm'); ?></td>
						<td colspan="3"><?php echo $team_link; ?></td>
						<td colspan="4" class="bblm_tbl_title bblm_tbl_label"><?php echo __('ReRolls', 'bblm'); ?></td>
						<td><?php echo $ti->t_rr; ?></td>
						<td class="bblm_tbl_enchance bblm_tbl_label">X</td>
						<td class="bblm_tbl_enchance"><?php echo number_format( $rr_cost ); ?></td>
						<td cclass="bblm_tbl_value"><?php echo number_format( $ti->t_rr*$rr_cost ); ?></td>
					</tr>
					<tr>
						<td colspan="5" class="bblm_tbl_label"><?php echo __('Race', 'bblm'); ?></td>
						<td colspan="3"><?php echo bblm_get_race_link( $ti->r_id ); ?></td>
						<td colspan="4" class="bblm_tbl_label"><?php echo __('Assistant Coaches', 'bblm'); ?></td>
						<td><?php echo $ti->t_ac; ?></td>
						<td class="bblm_tbl_enchance bblm_tbl_label">X</td>
						<td>10,000gp</td>
						<td class="bblm_tbl_value"><?php echo number_format( $ti->t_ac*10000 ); ?>gp</td>
					</tr>
					<tr>
						<td colspan="5" class="bblm_tbl_title bblm_tbl_label"><?php echo __('Dedicated fans', 'bblm'); ?></td>
						<td colspan="3"><?php echo $ti->t_ff; ?></td>
						<td colspan="4" class="bblm_tbl_label"><?php echo __('Cheerleaders', 'bblm'); ?></td>
						<td><?php echo $ti->t_cl; ?></td>
						<td class="bblm_tbl_enchance bblm_tbl_label">X</td>
						<td>10,000gp</td>
						<td class="bblm_tbl_value"><?php echo number_format( $ti->t_cl*10000 ); ?>gp</td>
					</tr>
					<tr>
						<td colspan="5" class="bblm_tbl_label"><?php echo __('Treasury', 'bblm'); ?></td>
						<td colspan="3"><?php echo number_format( $ti->t_bank ); ?>gp</td>
						<td colspan="4" class="bblm_tbl_label"><?php echo __('Apothecary', 'bblm'); ?></td>
						<td><?php echo $ti->t_apoc; ?></td>
						<td class="bblm_tbl_enchance bblm_tbl_label">X</td>
						<td>50,000gp</td>
						<td class="bblm_tbl_value"><?php echo number_format( $ti->t_apoc*10000 ); ?>gp</td>
					</tr>
					<tr>
						<td colspan="5" class="bblm_tbl_label"><?php echo __('Head Coach', 'bblm'); ?></td>
						<td colspan="3"><?php echo esc_textarea( $ti->t_hcoach ); ?> (<?php echo bblm_get_owner_link( $tid = $ti->ID ); ?>)</td>
						<td colspan="6" class="bblm_tbl_label"><?php echo __('Team value', 'bblm'); ?></td>
						<td colspan=2 class="bblm_tbl_value"><?php echo number_format( $ti->t_tv ); ?>gp</td>
					</tr>
					<tr><td colspan="5" class="bblm_tbl_label"><?php echo __('Stadium', 'bblm'); ?></td>
						<td colspan="3"><?php echo bblm_get_stadium_link( $ti->stad_id ); ?></td>
						<td colspan="6" class="bblm_tbl_label"><?php echo __('Current Team Value', 'bblm'); ?></td>
						<td colspan=2 class="bblm_tbl_value"><?php echo number_format( $ti->t_ctv ); ?>gp</td>
					</tr>
				</tbody>
			</table>

		<?php endwhile;?>
	<?php endif; ?>

		</div> <!-- End of #maincontent -->
	</div> <!-- End of #pagecontent -->
	<div id="footer">
		<p>Unique content is &copy; <a href="<?php echo home_url(); ?>" title="Visit the homepage of the <?php echo bblm_get_league_name(); ?>"><?php echo bblm_get_league_name(); ?></a> 2006 - present.</p>
		<p>Blood Bowl concept and miniatures are &copy; Games Workshop LTD used without permission.</p>
	</div> <!-- End of #footer -->
</div> <!-- End of #wrapper -->
<?php wp_footer(); ?>
</body>
</html>
