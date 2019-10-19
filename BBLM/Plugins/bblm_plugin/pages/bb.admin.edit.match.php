<?php
/**
 * BBowlLeagueMan Edit Match Admin page
 *
 * Link page to add/edit match reports, coachs comments, and match trivia.
 *
 * @author 		Blacksnotling
 * @category 	Core
 * @package 	BBowlLeagueMan/Pages
 */
?>
<div class="wrap">
	<h2>Edit Matches</h2>
<?php
	//Check the file is not being accessed directly
if (!function_exists('add_action')) die('You cannot run this file directly. Naughty Person');
?>

<?php
  ////////////////////////////////////
 // Submit changes to Match Trivia //
////////////////////////////////////
if (isset($_POST['bblm_trivia_edit'])) {

	$bblm_safe_input['mtrivia'] = esc_sql( $_POST['matchtrivia'] );

	$bblm_removable = array("<pre>","</pre>","<p>","&nbsp;", "<br>", "<br />");
	$bblm_trivia_content = $bblm_safe_input['mtrivia'];
	$bblm_trivia_content = str_replace($bblm_removable,"",$bblm_trivia_content);
	$bblm_trivia_content = str_replace("</p>","\n\n",$bblm_trivia_content);

	$updatesql = 'UPDATE `'.$wpdb->prefix.'match` SET `m_trivia` = \''.$bblm_trivia_content.'\' WHERE `m_id` = '.$_POST['mid'].' LIMIT 1';

	if (FALSE !== $wpdb->query($updatesql)) {
		$sucess = TRUE;
		do_action( 'bblm_post_submission' );
	}

	?>
		<div id="updated" class="updated fade">
		<p>
		<?php
		if ($sucess) {
?>
			Trivia has been updated. <a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.edit.match.php" title="Edit match details">Back to the match edit screen</a>
<?php
		}
		else {
			print("Something went wrong");
		}
		?>
	</p>
		</div>
	<?php

	//end of submit trivia
}
if (isset($_POST['bblm_comment_edit'])) {
  //////////////////////////////////////
 // Submit changes to Coach Comments //
//////////////////////////////////////
	$bblm_safe_input['mcA'] = esc_sql( $_POST['matchcomment1'] );
	$bblm_safe_input['mcB'] = esc_sql( $_POST['matchcomment2'] );

	//$updatesql = 'UPDATE `'.$wpdb->prefix.'match` SET `m_trivia` = \''.$bblm_trivia_content.'\' WHERE `m_id` = '.$_POST['mid'].' LIMIT 1';
	$updatesql = 'UPDATE `'.$wpdb->prefix.'match_team` SET `mt_comment` = \''.$bblm_safe_input['mcA'].'\' WHERE `m_id` = '.$_POST['mid'].' AND `t_id` = '.$_POST['team_a'].' LIMIT 1';
	$updatesql2 = 'UPDATE `'.$wpdb->prefix.'match_team` SET `mt_comment` = \''.$bblm_safe_input['mcB'].'\' WHERE `m_id` = '.$_POST['mid'].' AND `t_id` = '.$_POST['team_b'].' LIMIT 1';

	if (FALSE !== $wpdb->query($updatesql)) {
		if (FALSE !== $wpdb->query($updatesql2)) {
			$sucess = TRUE;
		}
		$sucess = TRUE;
	}

	?>
		<div id="updated" class="updated fade">
		<p>
		<?php
		if ($sucess) {
?>
			Trivia has been updated. <a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.edit.match.php" title="Edit match details">Back to the match edit screen</a>
<?php
		}
		else {
			print("Something went wrong - ".$updatesql." - ".$updatesql2);
		}
		?>
	</p>
		</div>
	<?php

	//end of submit comments
}
if (isset($_POST['bblm_stats_edit'])) {
  /////////////////////////////////////////////
 // Submit changes to match stats / results //
/////////////////////////////////////////////

$sucess = false;


	//Sanitise and organise vars
	$match = array(
		'id' => absint( $_POST['bblm_mid'] ),
		'ta' => absint( $_POST['bblm_mteama'] ),
		'tb' => absint( $_POST['bblm_mteamb'] ),
		'comp' => absint( $_POST['bblm_cid'] ),
		'div' => absint( $_POST['bblm_divid'] ),
		'gate' => absint( $_POST['gate'] ),
		'tAtd' => absint( $_POST['tAtd'] ),
		'tBtd' => absint( $_POST['tBtd'] ),
		'tAcas' => absint( $_POST['tAcas'] ),
		'tBcas' => absint( $_POST['tBcas'] ),
		'ttd' => absint( $_POST['tAtd'] ) + absint( $_POST['tBtd'] ),
		'tcas' => absint( $_POST['tAcas'] ) + absint( $_POST['tBcas'] ),
		'tint' => absint( $_POST['tAint'] ) + absint( $_POST['tBint'] ),
		'tcomp' => absint( $_POST['tAcomp'] ) + absint( $_POST['tBcomp'] ),
		'stad' => absint( $_POST['mstad'] )
	);
	$mteamA = array(
		'id' => absint( $_POST['bblm_mteama'] ),
		'mid' => absint( $_POST['bblm_mid'] ),
		'compid' => absint( $_POST['bblm_cid'] ),
		'div' => absint( $_POST['bblm_divid'] ),
		'td' => absint( $_POST['tAtd'] ),
		'cas' => absint( $_POST['tAcas'] ),
		'inter' => absint( $_POST['tAint'] ),
		'comp' => absint( $_POST['tAcomp'] ),
		'td' => absint( $_POST['tAtd'] ),
		'att' => absint( $_POST['tAwin'] ), //deliberatly swapped over due to database issue...
		'winnings' => absint( $_POST['tAatt'] ), //deliberatly swapped over due to database issue...
		'result' => $_POST['teamAres']
	);
	$mteamB = array(
		'id' => absint( $_POST['bblm_mteamb'] ),
		'mid' => absint( $_POST['bblm_mid'] ),
		'compid' => absint( $_POST['bblm_cid'] ),
		'div' => absint( $_POST['bblm_divid'] ),
		'td' => absint( $_POST['tBtd'] ),
		'cas' => absint( $_POST['tBcas'] ),
		'inter' => absint( $_POST['tBint'] ),
		'comp' => absint( $_POST['tBcomp'] ),
		'td' => absint( $_POST['tBtd'] ),
		'att' => absint( $_POST['tBwin'] ), //deliberatly swapped over due to database issue...
		'winnings' => absint( $_POST['tBatt'] ), //deliberatly swapped over due to database issue...
		'result' => $_POST['teamBres']
	);

	//Update of Team_comp records
	//First we pull the existing record
	if ( 13 == $match['div'] ) {
		//We have a cross divisional game - We need to pull all the records from the team_comp table for that team / comp
		$teamAcompperfsql = "SELECT * FROM ".$wpdb->prefix."team_comp C WHERE C.t_id = ".$mteamA['id']." AND C.c_id = ".$mteamA['compid'];
		$teamBcompperfsql = "SELECT * FROM ".$wpdb->prefix."team_comp C WHERE C.t_id = ".$mteamB['id']." AND C.c_id = ".$mteamB['compid'];
	}
	else {
		//We have a 'normal' match - pull just the specific match results for that team
		$teamAcompperfsql = "SELECT * FROM ".$wpdb->prefix."team_comp C WHERE C.t_id = ".$mteamA['id']." AND C.c_id = ".$mteamA['compid']." AND div_id = ".$mteamA['div'];
		$teamBcompperfsql = "SELECT * FROM ".$wpdb->prefix."team_comp C WHERE C.t_id = ".$mteamB['id']." AND C.c_id = ".$mteamB['compid']." AND div_id = ".$mteamB['div'];
	}
	$tAcompp = $wpdb->get_row($teamAcompperfsql);
	$tBcompp = $wpdb->get_row($teamBcompperfsql);

	//pull the existing match record for the team (pre edit)
	$teamAmsql = "SELECT M.*, T.t_name FROM ".$wpdb->prefix."match_team M, ".$wpdb->prefix."team T WHERE M.t_id = T.t_id AND M.m_id = ".$match['id']." AND M.t_id = ".$mteamA['id'];
	$teamBmsql = "SELECT M.*, T.t_name FROM ".$wpdb->prefix."match_team M, ".$wpdb->prefix."team T WHERE M.t_id = T.t_id AND M.m_id = ".$match['id']." AND M.t_id = ".$mteamB['id'];
	$tAm = $wpdb->get_row($teamAmsql);
	$tBm = $wpdb->get_row($teamBmsql);
	//Calculate the difference - This will NOT change the number of W/L/D or the points -that is for a future version!!
	$taTDfor = $mteamA['td'] - $tAm->mt_td;
	$taTDagst = $mteamB['td'] - $tBm->mt_td;
	$taCASfor = $mteamA['cas'] - $tAm->mt_cas;
	$taCASagst = $mteamB['cas'] - $tBm->mt_cas;
	$taint = $mteamA['inter'] - $tAm->mt_int;
	$tacomp = $mteamA['comp'] - $tAm->mt_comp;

	$tbTDfor = $mteamB['td'] - $tBm->mt_td;
	$tbTDagst = $mteamA['td'] - $tAm->mt_td;
	$tbCASfor = $mteamB['cas'] - $tBm->mt_cas;
	$tbCASagst = $mteamA['cas'] - $tAm->mt_cas;
	$tbint = $mteamB['inter'] - $tBm->mt_int;
	$tbcomp = $mteamB['comp'] - $tBm->mt_comp;

	//update teams competition record

	$tAmcupdatesql = "UPDATE `".$wpdb->prefix."team_comp` SET `tc_tdfor` = tc_tdfor+".$taTDfor.", `tc_tdagst` = tc_tdagst+".$taTDagst.", `tc_casfor` = tc_casfor+".$taCASfor.", `tc_casagst` = tc_casagst+".$taCASagst.", `tc_int` = tc_int+".$taint.", `tc_comp` = tc_comp+".$tacomp." WHERE `".$wpdb->prefix."team_comp`.`tc_id` = ".$tAcompp->tc_id.";";
	$tBmcupdatesql = "UPDATE `".$wpdb->prefix."team_comp` SET `tc_tdfor` = tc_tdfor+".$tbTDfor.", `tc_tdagst` = tc_tdagst+".$tbTDagst.", `tc_casfor` = tc_casfor+".$tbCASfor.", `tc_casagst` = tc_casagst+".$tbCASagst.", `tc_int` = tc_int+".$tbint.", `tc_comp` = tc_comp+".$tbcomp." WHERE `".$wpdb->prefix."team_comp`.`tc_id` = ".$tBcompp->tc_id.";";
	if ( ( FALSE !== $wpdb->query($tAmcupdatesql) ) && (FALSE !== $wpdb->query($tBmcupdatesql) ) ) {
		$sucess = TRUE;
	}

	//Update Match record
	$matchupdatesql = "UPDATE `".$wpdb->prefix."match` SET `m_gate` = \"".$match['gate']."\", `m_teamAtd` = \"".$match['tAtd']."\", `m_teamBtd` = \"".$match['tBtd']."\", `m_teamAcas` = \"".$match['tAcas']."\", `m_teamBcas` = \"".$match['tBcas']."\", `m_tottd` = \"".$match['ttd']."\", `m_totcas` = \"".$match['tcas']."\", `m_totcomp` = \"".$match['tcomp']."\", `m_totint` = \"".$match['tint']."\", `stad_id` = \"".$match['stad']."\" WHERE `".$wpdb->prefix."match`.`m_id` = ".$match['id'].";";
	if (FALSE !== $wpdb->query($matchupdatesql)) {
		$sucess = TRUE;
	}

	//Check to see if this match is part of a tournament, if it us update the bracket
	$checkbracketssql = 'SELECT cb_id FROM '.$wpdb->prefix.'comp_brackets WHERE m_id = '.absint( $_POST['bblm_mid'] );
	$cb_order = $wpdb->get_var($checkbracketssql);
	if ( !empty($cb_order) ) {
		//grab the team names and links from the DB
		$teamAdeetssql = "SELECT T.t_name, T.t_guid FROM ".$wpdb->prefix."team T WHERE t_id = ".$match['ta'];
		$teamBdeetssql = "SELECT T.t_name, T.t_guid FROM ".$wpdb->prefix."team T WHERE t_id = ".$match['tb'];
		$detailsA = $wpdb->get_row($teamAdeetssql);
		$detailsB = $wpdb->get_row($teamBdeetssql);
		//generate the HTML for the bracket (remembering to escape it)
		$cb_text = esc_sql( "<a href=\"".$detailsA->t_guid."\" title=\"Read more about this team\">".$detailsA->t_name." </a><strong>".$match['tAtd']."</strong><br /><a href=\"".$detailsB->t_guid."\" title=\"Read more about this team\">".$detailsB->t_name." </a><strong>".$match['tBtd']."</strong>" );
		//Generate SQL
		$cb_updatesql = "UPDATE `".$wpdb->prefix."comp_brackets` SET `cb_text` = \"".$cb_text."\" WHERE `".$wpdb->prefix."comp_brackets`.`cb_id` = ".$cb_order.";";
		$wpdb->query($cb_updatesql);
	}
	// Update the team record for the match (for each team)
	$teamAupdatesql = "UPDATE `".$wpdb->prefix."match_team` SET `mt_td` = ".$mteamA['td'].", `mt_cas` = ".$mteamA['cas'].", `mt_int` = ".$mteamA['inter'].", `mt_comp` = ".$mteamA['comp'].", `mt_winnings` = ".$mteamA['winnings'].", `mt_att` = ".$mteamA['att'].", `mt_result` = '".$mteamA['result']."' WHERE `".$wpdb->prefix."match_team`.`m_id` = ".$mteamA['mid']." AND `".$wpdb->prefix."match_team`.`t_id` = ".$mteamA['id'].";";
	$teamBupdatesql = "UPDATE `".$wpdb->prefix."match_team` SET `mt_td` = ".$mteamB['td'].", `mt_cas` = ".$mteamB['cas'].", `mt_int` = ".$mteamB['inter'].", `mt_comp` = ".$mteamB['comp'].", `mt_winnings` = ".$mteamB['winnings'].", `mt_att` = ".$mteamB['att'].", `mt_result` = '".$mteamB['result']."' WHERE `".$wpdb->prefix."match_team`.`m_id` = ".$mteamB['mid']." AND `".$wpdb->prefix."match_team`.`t_id` = ".$mteamB['id'].";";

	if ( ( FALSE !== $wpdb->query($teamAupdatesql) ) && (FALSE !== $wpdb->query($teamBupdatesql) ) ) {
		$sucess = TRUE;
		do_action( 'bblm_post_submission' );
	}

	//if it was all a success, them display the message to the user
	echo '<div id="updated" class="updated fade">';

	if ( $sucess ) {
		echo '<p>'.__( 'The match has been updated', 'bblm' );
?>
		<a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.edit.match.php" title="Edit match details">Back to the match edit screen</a></p></div>
<?php
	}
	else {
		echo '<p>'.__( 'Something went wrong, please try again!', 'bblm' ).'</p></div>';
	}


} //end of submit stats



	  ////////////////////
	 // $_GET checking //
	////////////////////
	if ("edit" == $_GET['action']) {
		if ("trivia" == $_GET['item']) {
			//Editing match trivia
?>
	<h3>Edit Match Trivia</h3>
<?php
	$match_id = $_GET['id'];
	$matchsql = 'SELECT M.m_id, P.post_title, M.m_trivia FROM '.$wpdb->prefix.'match M, '.$wpdb->posts.' P, '.$wpdb->prefix.'bb2wp J WHERE M.m_id = J.tid AND J.prefix = \'m_\' AND J.pid = P.ID AND M.m_id = '.$match_id.' LIMIT 1';
	if ($m = $wpdb->get_row($matchsql)) {
			print("<p>You are editing the match trivia for <strong>".$m->post_title."</strong>:</p>\n");
			$trivia = $m->m_trivia;
			$trivia = str_replace("\n\n","</p>",$trivia);

?>
			<form name="bblm_editmatchtrivia" method="post" id="post">
				<h3>Match Trivia</h3>
				<p>Please ensure that the items below are formatted in a <strong>list</strong>.</p>
				<input type="hidden" name="mid" value="<?php print($match_id); ?>">
				<textarea name="matchtrivia" cols="80" rows="6"><?php print($trivia); ?></textarea>
				<input type="submit" name="bblm_trivia_edit" value="Save Changes"/>
			</form>
<?php
			}
		} //end of if item ==trivia
		else if ("comment" == $_GET['item']) {
			//Editing match Comments
			$match_id = $_GET['id'];
			$matchsql = 'SELECT M.m_id, UNIX_TIMESTAMP(M.m_date) AS mdate, M.c_id, M.m_teamA, M.m_teamB, D.div_name, C.c_name, T.t_name AS TA, V.t_name AS TB, R.mt_comment AS TAcomm, S.mt_comment AS TBcomm FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'team V, '.$wpdb->prefix.'match_team R, '.$wpdb->prefix.'match_team S, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'division D WHERE M.c_id = C.c_id AND M.div_id = D.div_id AND M.m_id = R.m_id AND M.m_id = S.m_id AND M.m_teamA = R.t_id AND M.m_teamB = S.t_id AND M.m_teamA = T.t_id AND M.m_teamB = V.t_id AND M.m_id = '.$match_id;
			if ($m = $wpdb->get_row($matchsql)) {
				print("<h3>Edit Coaches Comments</h3>");
				print("<p>You are editing the Match Comments for <strong>".$m->TA." vs ".$m->TB."</strong> (".$m->c_name.", ".$m->div_name."):</p>\n");
?>
				<form name="bblm_editmatchcomments" method="post" id="post">
					<h3>Match Comment</h3>
					<p>please note that the comments below do <strong>not</strong> have a spell checker! Do it manually.</p>
					<input type="hidden" name="mid" value="<?php print($match_id); ?>">
					<input type="hidden" name="team_a" value="<?php print($m->m_teamA); ?>">
					<input type="hidden" name="team_b" value="<?php print($m->m_teamB); ?>">
					<table>
					<tr>
						<th><?php print($m->TA); ?></th>
						<th>Vs</th>
						<th><?php print($m->TB); ?></th>
					</tr>
					<tr>
						<td><textarea name="matchcomment1" cols="50" rows="6"><?php print(stripslashes($m->TAcomm)); ?></textarea></td>
						<td>&nbsp;</td>
						<td><textarea name="matchcomment2" cols="50" rows="6"><?php print(stripslashes($m->TBcomm)); ?></textarea></td>
					</tr>
					</table>
					<input type="submit" name="bblm_comment_edit" value="Save Changes"/>
				</form>
<?php
			}
		}//end of if item == comment
		else if ("stats" == $_GET['item']) {
			echo '<p>'.__( 'Here you can edit the match results. This page will automatically update the match records and competition performance of a team', 'bblm' ).'</p>';
			echo '<p>'.__( 'it will NOT update a teams treasurey or TV, you will need to update those on the edit team page', 'bblm' ).'</p>';
			$match_id = $_GET['id'];

			$matchsql = "SELECT M.m_id, M.m_gate, UNIX_TIMESTAMP(M.m_date) AS mdate, M.m_teamA, M.m_teamB, M.stad_id, C.c_name, C.c_id, D.div_name, D.div_id FROM ".$wpdb->prefix."match M, ".$wpdb->prefix."comp C, ".$wpdb->prefix."division D WHERE M.c_id = C.c_id AND M.div_id = D.div_id AND M.m_id = ".$match_id;
			$m = $wpdb->get_row($matchsql);
			$teamAsql = "SELECT M.*, T.t_name FROM ".$wpdb->prefix."match_team M, ".$wpdb->prefix."team T WHERE M.t_id = T.t_id AND M.m_id = ".$match_id." AND M.t_id = ".$m->m_teamA;
			$mA = $wpdb->get_row($teamAsql);
			$teamBsql = "SELECT M.*, T.t_name FROM ".$wpdb->prefix."match_team M, ".$wpdb->prefix."team T WHERE M.t_id = T.t_id AND M.m_id = ".$match_id." AND M.t_id = ".$m->m_teamB;
			$mB = $wpdb->get_row($teamBsql);

			echo '<h3>'.__( 'Match Results', 'bblm' ).'</h3>';
?>

<script type="text/javascript">
function BBLM_UpdateGate() {
	/*		Calcuate the players SPP		*/
	var tot_a = document.getElementById('tAatt').value;
	var tot_b = document.getElementById('tBatt').value;
	var tot_att = Number(tot_a) + Number(tot_b);
	document.getElementById('gate').value = tot_att;

}
</script>

		<ul>
			<li><strong>Date</strong>: <?php echo date("d.m.y", $m->mdate); ?></li>
			<li><strong>Competition</strong>: <?php echo $m->c_name; ?></li>
			<li><strong>Division</strong>: <?php echo $m->div_name; ?></li>
		</ul>

		<form name="bblm_editmatchstats" method="post" id="post">

			<input name="bblm_mteama" type="hidden" value="<?php echo $mA->t_id; ?>">
			<input name="bblm_mteamb" type="hidden" value="<?php echo $mB->t_id; ?>">
			<input name="bblm_mid" type="hidden" value="<?php echo $match_id; ?>">
			<input name="bblm_cid" type="hidden" value="<?php echo $m->c_id;; ?>">
			<input name="bblm_divid" type="hidden" value="<?php echo $m->div_id;; ?>">

			<table>
				<tr>
					<th>&nbsp;</th>
					<th><?php echo $mA->t_name; ?></th>
					<th>&nbsp;</th>
					<th><?php echo $mB->t_name; ?></th>
					<th>&nbsp;</th>
				</tr>
				<tr>
					<td>Location:</td>
					<td colspan="3">
						<select name="mstad" id="mstad">
							<?php
							//Grabs a list of 'posts' from the Stadiums CPT
							$oposts = get_posts(
								array(
									'post_type' => 'bblm_stadium',
									'numberposts' => -1,
									'orderby' => 'post_title',
									'order' => 'ASC'
								)
							);
							if( ! $oposts ) return;
							foreach( $oposts as $o ) {
								echo '<option value="' . $o->ID . '"';
								if ( $o->ID == $m->stad_id ) {
									echo ' selected="selected"';
								}
								echo '>' . bblm_get_stadium_name( $o->ID ) . '</option>';
							}
							?>
						</select>
					</td>
					<td class="comment"><?php echo __( 'The location of the match.', 'bblm' ); ?></td></tr></td>
				</tr>
				<tr><td>Score:</td><td><input name="tAtd" type="text" size="3" maxlength="2" value="<?php echo $mA->mt_td; ?>"></td><td>Vs</td><td><input name="tBtd" type="text" size="3" maxlength="2" value="<?php echo $mB->mt_td; ?>"></td><td class="comment"><?php echo __( 'The Final Score', 'bblm' ); ?></td></tr>
				<tr><td>Casualties:</td><td><input name="tAcas" type="text" size="3" maxlength="2" value="<?php echo $mA->mt_cas; ?>"></td><td>Vs</td><td><input name="tBcas" type="text" size="3" maxlength="2" value="<?php echo $mB->mt_cas; ?>"></td><td class="comment">&nbsp;</td></tr>
				<tr><td>Interceptions:</td><td><input name="tAint" type="text" size="3" maxlength="2" value="<?php echo $mA->mt_int; ?>"></td><td>Vs</td><td><input name="tBint" type="text" size="3" maxlength="2" value="<?php echo $mB->mt_int; ?>"></td><td class="comment">&nbsp;</td></tr>
				<tr><td>Completions:</td><td><input name="tAcomp" type="text" size="3" maxlength="2" value="<?php echo $mA->mt_comp; ?>"></td><td>Vs</td><td><input name="tBcomp" type="text" size="3" maxlength="2" value="<?php echo $mB->mt_comp; ?>"></td><td class="comment">&nbsp;</td></tr>
				<tr><td>Attendance:</td><td><input name="tAatt" id="tAatt" type="text" size="6" maxlength="6" value="<?php echo $mA->mt_winnings; ?>" onChange="BBLM_UpdateGate()"></td><td>Vs</td><td><input name="tBatt" id="tBatt"  type="text" size="6" maxlength="6" value="<?php echo $mB->mt_winnings; ?>" onChange="BBLM_UpdateGate()"></td><td class="comment"><?php echo __( 'Number of fans of each team.', 'bblm' ); ?></td></tr>
				<tr><td>Gate:</td><td colspan="3"><input name="gate" id="gate" type="text" size="6" maxlength="6" value="<?php echo $m->m_gate; ?>"></td><td class="comment"><?php echo __( 'Total number of fans in attendence.', 'bblm' ); ?></td></tr>
				<tr><td>Winnings:</td><td><input name="tAwin" type="text" size="6" maxlength="6" value="<?php echo $mA->mt_att; ?>"></td><td>Vs</td><td><input name="tBwin" type="text" size="6" maxlength="6" value="<?php echo $mB->mt_att; ?>"></td><td class="comment"><?php echo __( 'This will NOT change the teams roster or bank, it will only change the value on the match results page', 'bblm' ); ?></td></tr>
				<tr><td>Result:</td>
					<td>
						<select id="tAres" name="teamAres">
							<option value="W"<?php if ( "W" == $mA->mt_result ) { echo ' selected="selected"';} ?>>Win</option>
							<option value="L"<?php if ( "L" == $mA->mt_result ) { echo ' selected="selected"';} ?>>Loss</option>
							<option value="D"<?php if ( "D" == $mA->mt_result ) { echo ' selected="selected"';} ?>>Draw</option>
						</select>
					</td>
					<td>Vs</td><td>
						<select id="tBres" name="teamBres">
							<option value="W"<?php if ( "W" == $mB->mt_result ) { echo ' selected="selected"';} ?>>Win</option>
							<option value="L"<?php if ( "L" == $mB->mt_result ) { echo ' selected="selected"';} ?>>Loss</option>
							<option value="D"<?php if ( "D" == $mB->mt_result ) { echo ' selected="selected"';} ?>>Draw</option>
						</select>
					</td><td class="comment">&nbsp;</td></tr>
			</table>

			<p class="submit">
				<input type="submit" name="bblm_stats_edit" value="Submit match details" title="submit match details" class="button-primary"/>
			</p>

		</form>
<?php
		}//end of if item == stats
		else {
			//Catch all
			print("<p>That request was not recognised. Please try again.</p>");
		}
	}
	else {
		//Display main form
?>
	<p>Below is a list of matches that have taken place in the League. Select the match title to edit the report or use the other links to edit the Coaches comments or match trivia.</p>

<?php
				$matchsql = 'SELECT UNIX_TIMESTAMP(M.m_date) AS mdate, M.m_id, M.m_gate, M.m_teamAtd, M.m_teamBtd, M.m_teamAcas, M.m_teamBcas, P.guid, P.post_title, C.c_name, Z.guid AS cguid, D.div_name, P.ID, M.m_id FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P, '.$wpdb->prefix.'division D, '.$wpdb->prefix.'bb2wp Y, '.$wpdb->posts.' Z WHERE C.c_id = Y.tid AND Y.prefix = \'c_\' AND Y.pid = Z.ID AND M.div_id = D.div_id AND M.c_id = C.c_id AND M.m_id = J.tid AND J.prefix = \'m_\' AND J.pid = P.ID ORDER BY C.sea_id DESC, M.c_id DESC, D.div_id ASC, M.m_date DESC';
		if ($match = $wpdb->get_results($matchsql)) {
			$zebracount = 1;

			print("<table class=\"widefat\">\n	<thead>\n		 <tr>\n		   		<th scope=\"row\">ID</th>\n		   <th scope=\"col\">Match Details</th>\n		   <th scope=\"col\">Results</th>\n		   <th scope=\"col\">Comments</th>\n		   <th scope=\"col\">Facts</th>\n		   <th scope=\"col\">View Match</th>\n		 </tr>\n	</thead>\n	<tbody>\n");
			foreach ($match as $m) {

				if ($zebracount % 2) {
					print("					<tr class=\"alternate\">\n");
				}
				else {
					print("					<tr>\n");
				}

				print("		   <td>".$m->m_id."</a></td>\n		   <td><a href=\"");

				bloginfo('url');
				print("/wp-admin/post.php?post=".$m->ID."&action=edit\">".date("d.m.y", $m->mdate)." ".$m->post_title."</a> (".$m->c_name." - ".$m->div_name.") [ ".$m->m_teamAtd." - ".$m->m_teamBtd." (".$m->m_teamAcas." - ".$m->m_teamBcas.")]</td>\n");

				print("<td><a href=\"");
				bloginfo('url');
				print("/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.edit.match.php&action=edit&item=stats&id=".$m->m_id."\" title=\"Edit the Match results\">Edit Results</a></td>\n");

				print("<td><a href=\"");
				bloginfo('url');
				print("/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.edit.match.php&action=edit&item=comment&id=".$m->m_id."\" title=\"Edit the Coaches Comments\">Edit Comments</a></td>\n");

				print("<td><a href=\"");
				bloginfo('url');
				print("/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.edit.match.php&action=edit&item=trivia&id=".$m->m_id."\" title=\"Edit the Coaches Comments\">Edit Trivia</a></td>\n");

/*			print("<td><form method=\"post\" action=\"");
			bloginfo('url');
			print("/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.edit.match_comment.php\"><input type=\"hidden\" name=\"mtrivia\" value=\"".$m->m_id."\"><input type=\"submit\" name=\"bblm_comment_select\" class=\"bblm_table_submit\" value=\"Edit\"/></form></td>\n");*/

/*			print("<td><form method=\"post\" action=\"");
			bloginfo('url');
			print("/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.edit.match_trivia.php\"><input type=\"hidden\" name=\"mtrivia\" value=\"".$m->m_id."\"><input type=\"submit\" name=\"bblm_trivia_select\" class=\"bblm_table_submit\" value=\"Edit\"/></form></td>\n");*/

				print("<td><a href=\"".$m->guid."\" title=\"View the match page\">View</a></td>		 </tr>\n");
				$zebracount++;
			}
			print("	</tbody>\n</table>\n");
		}
	}//end of else action != edit
?>

</div>
