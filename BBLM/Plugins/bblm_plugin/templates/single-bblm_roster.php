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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/favicon.ico" />
<?php wp_head(); ?>
<style type="text/css">
html * {
	margin:0;
	padding:0;
}
body {
	font-family: 'Lucida Grande', Verdana, Arial, Sans-Serif;
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
table td {
	text-align: center;
	border-left-style: dotted;
	border-bottom-style: solid;
	border-color: #000000;
	border-width: 1px;
	padding: 0.3em;
}
table th {
	border-bottom: 4px solid #000;
	background-color: #548ac3;
	color: #fff;
	font-weight: bold;
	text-align: center;
}
table th.tbl_enchance, table th.tbl_title {
	border-left-style: dotted;
	border-bottom-style: solid;
	border-color: #000000;
	border-width: 1px;
}
.tbl_stat {
	width:25px;
}
.tbl_skills {
	width:300px;
	text-align:left;
	font-size: smaller;
}
.tbl_name {
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
	color: #3366FF;
	text-decoration: none;
}

a:hover, a:active {
	color: #D7651B;
	text-decoration: underline;
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
		$teaminfosql = 'SELECT T.*, J.tid AS teamid, R.r_name, R.r_rrcost, L.guid AS racelink, T.stad_id, T.WPID FROM '.$wpdb->prefix.'team T, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P, '.$wpdb->prefix.'race R, '.$wpdb->prefix.'bb2wp K, '.$wpdb->posts.' L WHERE T.t_id = J.tid AND T.r_id = K.tid AND K.prefix = \'r_\' AND K.pid = L.ID AND R.r_id = T.r_id AND T.t_id = J.tid AND J.prefix = \'roster\' AND J.pid = P.ID AND P.ID = '.$post->ID;
		if ($ti = $wpdb->get_row($teaminfosql)) {
				$tid = $ti->teamid;
				$team_name = esc_html( get_the_title( $ti->WPID ) );
				$team_link = get_post_permalink( $ti->WPID );

			//determine Team Captain
			$teamcap = 0;
			$teamcaptainsql = 'SELECT * FROM '.$wpdb->prefix.'team_captain WHERE tcap_status = 1 and t_id = '.$tid;
			if ($tcap = $wpdb->get_row($teamcaptainsql)) {
				$teamcap = $tcap->p_id;
			}
?>
		<h1>Roster for <a href="<?php print( $team_link ); ?>" title="Read more about <?php print( $team_name ); ?>"><?php print( $team_name ); ?></a></h1>

<?php
		}
?>
<table border="0">
 <tr>
  <th class="bblm_tbl_stat">No.</th>
  <th class="bblm_tbl_name">Player Name</th>
  <th class="bblm_tbl_pos">Position</th>
  <th class="bblm_tbl_stat">MA</th>
  <th class="bblm_tbl_stat">ST</th>
  <th class="bblm_tbl_stat">AG</th>
  <th class="bblm_tbl_stat">AV</th>
  <th class="bblm_tbl_skills">Skills / Injuries</th>
  <th class="bblm_tbl_stat">INJ</th>
  <th class="bblm_tbl_stat">COMP</th>
  <th class="bblm_tbl_stat">TD</th>
  <th class="bblm_tbl_stat">INT</th>
  <th class="bblm_tbl_stat">CAS</th>
  <th class="bblm_tbl_stat">MVP</th>
  <th class="bblm_tbl_stat">SPP</th>
  <th class="bblm_tbl_value">Value</th>
 </tr>
<?php
		$playersql = 'SELECT K.post_title, K.guid, L.pos_name, P.* FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' K, '.$wpdb->prefix.'position L WHERE P.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = K.ID AND P.pos_id = L.pos_id AND P.p_status = 1 AND P.t_id = '.$tid.' ORDER BY P.p_num ASC';
		$pcount = 1;
		if ($players = $wpdb->get_results($playersql)) {
			foreach ($players as $pl) {
				while ($pcount < $pl->p_num) {
					//print a generic row
?>
 <tr>
  <td><?php print($pcount); ?></td>
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
 </tr>
<?php
					$pcount++;

				}
				//checks to see the player belongs in this position
				if ($pcount == $pl->p_num) {

				$playerdetailssql = 'SELECT SUM(M.mp_td) AS PTD, SUM(M.mp_cas) AS PCAS, SUM(M.mp_comp) AS PCOMP, SUM(M.mp_int) AS PINT, SUM(M.mp_MVP) AS PMVP, SUM(M.mp_spp) AS PSPP FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'match N, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' O WHERE M.m_id = N.m_id AND N.c_id = C.c_id AND C.c_counts = 1 AND C.c_show = 1 AND P.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = O.ID AND M.p_id = P.p_id AND M.mp_spp > 0 AND P.p_id = '.$pl->p_id;
				$pd = $wpdb->get_row($playerdetailssql)
?>
<tr>
  <td><?php print($pcount); ?></td>
  <td><?php print("<a href=\"".$pl->guid."\" title=\"View more information about ".$pl->post_title."\">".$pl->post_title."</a>"); if ($teamcap == $pl->p_id) { print(" (C)");} ?></td>
  <td><?php print( esc_html( $pl->pos_name ) ); ?></td>
  <td><?php print($pl->p_ma); ?></td>
  <td><?php print($pl->p_st); ?></td>
  <td><?php print($pl->p_ag); ?></td>
  <td><?php print($pl->p_av); ?></td>
  <td class="bblm_tbl_skills"><?php print($pl->p_skills); ?>
<?php
	if ("none" !== $pl->p_injuries) {
		print(", <em>".$pl->p_injuries."</em>");
	}
?></td>
<?php
	if ($pl->p_mng) {
		print("  <td>Y</td>\n");
	}
	else {
		print("  <td>&nbsp;</td>\n");
	}

	if ($pd->PCOMP == 0) {
		print("  <td>&nbsp;</td>\n");
	}
	else {
		print("  <td>".$pd->PCOMP."</td>\n");
	}
	if ($pd->PTD == 0) {
		print("  <td>&nbsp;</td>\n");
	}
	else {
		print("  <td>".$pd->PTD."</td>\n");
	}
	if ($pd->PINT == 0) {
		print("  <td>&nbsp;</td>\n");
	}
	else {
		print("  <td>".$pd->PINT."</td>\n");
	}
	if ($pd->PCAS == 0) {
		print("  <td>&nbsp;</td>\n");
	}
	else {
		print("  <td>".$pd->PCAS."</td>\n");
	}
	if ($pd->PMVP == 0) {
		print("  <td>&nbsp;</td>\n");
	}
	else {
		print("  <td>".$pd->PMVP."</td>\n");
	}
?>
  <td><?php echo $pd->PSPP; ?></td>
  <td class="bblm_tbl_value"><?php print(number_format($pl->p_cost_ng)); ?>gp</td>
 </tr>
<?php
				}
				$pcount++;
			}
		//now the sql has been completed, we have to print emtpy lines to ensure that 16 places are displayed!
			while (17 > $pcount) {
				//print a generic row
?>
 <tr>
  <td><?php print($pcount); ?></td>
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
 </tr>
<?php
				$pcount++;

				}
		}
?>
 <!-- End of player listing -->
 <tr>
<?php
	$filename = $_SERVER['DOCUMENT_ROOT']."/images/teams/".$ti->t_sname."_big.gif";
	if (file_exists($filename)) {
?>
  <td colspan="3" rowspan="7" class="bblm_tbl_logo"><img src="<?php print(home_url()); ?>/images/teams/<?php print($ti->t_sname); ?>_big.gif" alt="Team Logo" /></td>
<?php
	}
	else {
?>
  <td colspan="3" rowspan="7" class="bblm_tbl_logo"><img src="<?php print(home_url()); ?>/images/races/race<?php print($ti->r_id); ?>.gif" alt="<?php print($ti->r_name); ?> Logo" /></td>
<?php
	}
?>
  <th colspan="4" rowspan="2" class="bblm_tbl_title">Team Name:</th>
  <td rowspan="2"><a href="<?php print( $team_link ); ?>" title="Read more about <?php print( $team_name ); ?>"><?php print( $team_name ); ?></a></td>
  <th colspan="3" class="bblm_tbl_title">Re-Rolls:</th>
  <td><?php print($ti->t_rr); ?></td>
  <th class="bblm_tbl_enchance">X</th>
  <th class="bblm_tbl_enchance" colspan="2"><?php print(number_format($ti->r_rrcost)); ?>gp</th>
  <td class="bblm_tbl_value"><?php print(number_format($ti->t_rr*$ti->r_rrcost)); ?>gp</td>
 </tr>
 <tr>
  <th colspan="3" class="bblm_tbl_title">Fan Factor:</th>
  <td><?php print($ti->t_ff); ?></td>
  <th class="bblm_tbl_enchance">X</th>
  <td colspan="2">10,000gp</td>
  <td class="bblm_tbl_value"><?php print(number_format($ti->t_ff*10000)); ?>gp</td>
 </tr>
 <tr>
  <th colspan="4" rowspan="2" class="bblm_tbl_title">Race:</th>
  <td rowspan="2"><a href="<?php print($ti->racelink); ?>" title="Read more about <?php print($ti->r_name); ?> teams"><?php print($ti->r_name); ?></a></td>
  <th colspan="3" class="bblm_tbl_title">Assistant Coaches:</th>
  <td><?php print($ti->t_ac); ?></td>
  <th class="bblm_tbl_enchance">X</th>
  <td colspan="2">10,000gp</td>
  <td class="bblm_tbl_value"><?php print(number_format($ti->t_ac*10000)); ?>gp</td>
 </tr>
 <tr>
  <th colspan="3" class="bblm_tbl_title">Cheerleaders:</th>
  <td><?php print($ti->t_cl); ?></td>
  <th class="bblm_tbl_enchance">X</th>
  <td colspan="2">10,000gp</td>
  <td class="bblm_tbl_value"><?php print(number_format($ti->t_cl*10000)); ?>gp</td>
 </tr>
 <tr>
  <th colspan="4" rowspan="2" class="bblm_tbl_title">Treasury:</th>
  <td rowspan="2"><?php print(number_format($ti->t_bank)); ?>gp</td>
  <th colspan="3" class="bblm_tbl_title">Apothecary:</th>
  <td><?php print($ti->t_apoc); ?></td>
  <th class="bblm_tbl_enchance">X</th>
  <td colspan="2">50,000gp</td>
  <td class="bblm_tbl_value"><?php print(number_format($ti->t_apoc*50000)); ?>gp</td>
 </tr>
 <tr>
	<th colspan="3" class="bblm_tbl_title">Stadium:</th>
	<td height="24" colspan="5"><?php echo bblm_get_stadium_link( $ti->stad_id ); ?></td>
 </tr>
 <tr>
  <th colspan="4" class="bblm_tbl_title">Head Coach:</th>
  <td><?php print($ti->t_hcoach); ?> (<?php echo esc_html( get_the_title( $tid = $ti->ID ) ); ?>)</td>
  <th colspan="7" class="bblm_tbl_title">Total Value of Team (TV):</th>
  <td class="bblm_tbl_value"><?php print(number_format($ti->t_tv)); ?>gp</td>
 </tr>
</table>

		<?php endwhile;?>
	<?php endif; ?>

		</div> <!-- End of #maincontent -->
	</div> <!-- End of #pagecontent -->
	<div id="footer">
				<p>Unique content is &copy; <a href="<?php echo home_url(); ?>" title="Visit the homepage of the <?php echo bblm_get_league_name(); ?>"><?php echo bblm_get_league_name(); ?></a> 2006 - present.</p>
				<p>Blood Bowl concept and miniatures are &copy; Games Workshop LTD used without permission.</p>
				<?php wp_footer(); ?>
	</div> <!-- End of #footer -->
</div> <!-- End of #wrapper -->
</body>
</html>
