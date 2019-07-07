<?php
/**
 * BBowlLeagueMan Edit Player Admin page
 *
 * The core edit player screen.
 *
 * @author 		Blacksnotling
 * @category 	Core
 * @package 	BBowlLeagueMan/Pages
 */

//Check the file is not being accessed directly
if (!function_exists('add_action')) die('You cannot run this file directly. Naughty Person');

?>
<div class="wrap">
	<h2>Manage Players</h2>

<?php
if (isset($_POST['bblm_remove_player'])) {
  ////////////////////////////////
 // Reove the player //
////////////////////////////////
/*	print("<pre>");
	print_r($_POST);
	print("</pre>");
	print("<hr />");*/

//update bb_player.p_status to 0
//if NOT injured (check p_mng) then remove players cost from team_tv

	$bblm_safe_input = array();

	if (get_magic_quotes_gpc()) {
		$_POST['bblm_fdesc'] = stripslashes($_POST['bblm_fdesc']);
	}
	$bblm_safe_input['fdesc'] = esc_sql( $_POST['bblm_fdesc'] );
	$bblm_player = $_POST['bblm_pid'];
	$bblm_match = $_POST['bblm_fmatch'];
	$bblm_team = $_POST['bblm_tid'];
	$bblm_fate = $_POST['bblm_fate'];

	//filter reason for removal
	$bblm_removable = array("<pre>","</pre>","<p>","&nbsp;");
	$bblm_page_content = $bblm_safe_input['fdesc'];
	$bblm_page_content = str_replace($bblm_removable,"",$bblm_page_content);
	$bblm_page_content = str_replace("</p>","\n\n",$bblm_page_content);

	/*
	Start of SQL Generation
	*/

$playerupdatesql = 'UPDATE `'.$wpdb->prefix.'player` SET `p_status` = \'0\' WHERE `p_id` = \''.$bblm_player.'\' LIMIT 1';

//$teamupdatesql = 'UPDATE `'.$wpdb->prefix.'team` SET `t_tv` = t_tv-\''.$bblm_cost.'\' WHERE `t_id` = \''.$bblm_team.'\' LIMIT 1';

$fateinsertsql = 'INSERT INTO `'.$wpdb->prefix.'player_fate` (`p_id`, `f_id`, `m_id`, `pf_desc`, `pf_killer`) VALUES (\''.$bblm_player.'\', \''.$bblm_fate.'\', \''.$bblm_match.'\', \''.$bblm_page_content.'\', \''.$_POST['bblm_fkiller'].'\')';

/*	//For Debugging only
print("<p>".$playerupdatesql."</p>");
print("<p>".$fateinsertsql."</p>");*/



if (FALSE !== $wpdb->query($playerupdatesql)) {
	$sucess = TRUE;
	do_action( 'bblm_post_submission' );
}
else {
	$wpdb->print_error();
}
/*if (FALSE !== $wpdb->query($teamupdatesql)) {
	$sucess = TRUE;
}
else {
	$wpdb->print_error();
}*/
bblm_update_tv($bblm_team);

if (FALSE !== $wpdb->query($fateinsertsql)) {
	$sucess = TRUE;
}
else {
	$wpdb->print_error();
}


?>
	<div id="updated" class="updated fade">
	<p>
	<?php
	if ($sucess) {
		if (1 == $bblm_fate) {
			print("Another life claimed by the Finger of Death.");
		}
		else if (2 == $bblm_fate) {
			print("Best of luck in the future");
		}
		else if (3 == $bblm_fate) {
			print("Shame you had to go.");
		}
		else if (4 == $bblm_fate) {
			print("Your Fired!!!.");
		}
		else if (5 == $bblm_fate) {
			print("Where we not good enough for you?.");
		}
		else if (6 == $bblm_fate) {
			print("Do not worry, they will be back!");
		}
		else if (7 == $bblm_fate) {
			print("Do not worry, they will be back (but very smelly)!");
		}
		else {
			print("Player has been Removed from the team. Farewell.");
		}
 		print(" <a href=\"");
		bloginfo('url');
		print("/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.edit.player.php&action=select&item=none&id=".$bblm_player."\" title=\"View the list of players on the team\">Select another player</a> from this team");
	}
	else {
		print("Something went wrong");
	}
	?>
</p>
	</div>
<?php
}
else if (isset($_POST['bblm_rename_player'])) {
  ////////////////////////////////
 // Submit Player Name Change //
////////////////////////////////
/*	print("<pre>");
	print_r($_POST);
	print("</pre>");
	print("<hr />");*/

	/*
	Start of SQL Generation
	*/

$playerupdatewpsql = 'UPDATE `'.$wpdb->prefix.'player` SET `p_status` = \'0\' WHERE `p_id` = \''.$bblm_player.'\' LIMIT 1';

$playerupdatewpsql = 'UPDATE `'.$wpdb->posts.'` SET `post_title` = \''.$_POST['bblm_pname'].'\', `post_name` = \''.$_POST['bblm_pslug'].'\', `guid` = \''.$_POST['bblm_purl'].'\' WHERE `ID` = '.$_POST['bblm_postid'].' LIMIT 1';
$playerupdatebbsql = 'UPDATE `'.$wpdb->prefix.'player` SET `p_name` = \''.$_POST['bblm_pname'].'\' WHERE `p_id` = '.$_POST['bblm_pid'].' LIMIT 1';

if (FALSE !== $wpdb->query($playerupdatewpsql)) {
	if (FALSE !== $wpdb->query($playerupdatebbsql)) {
		$sucess = TRUE;
		do_action( 'bblm_post_submission' );
	}
}


?>
	<div id="updated" class="updated fade">
	<p>
	<?php
	if ($sucess) {
		print("Player has been renamed. Don't forget to <a href=\"".home_url()."/wp-admin/post.php?post=".$_POST['bblm_postid']."&action=edit\" title=\"edit the players description\">edit the players description</a>!");
	}
	else {
		print("Something went wrong");
	}
	?>
</p>
	</div>
<?php

} //end of rename payer


else if(isset($_POST['bblm_journeyman_add'])) {
  ////////////////////////////////
 // Hire Journemay to the team //
////////////////////////////////
/*	print("<pre>");
	print_r($_POST);
	print("</pre>"); */

$teamsql = "UPDATE `".$wpdb->prefix."team` SET `t_bank` = t_bank-'".$_POST['bblm_cost']."' WHERE `t_id` = ".$_POST['bblm_team'];
$playersql = "UPDATE `".$wpdb->prefix."player` SET `pos_id` = ".$_POST['bblm_position']." WHERE `p_id` = ".$_POST['bblm_player'];

	if (FALSE !== $wpdb->query($teamsql)) {
		$sucess = TRUE;
	}
	if (FALSE !== $wpdb->query($playersql)) {
		$sucess = TRUE;
		do_action( 'bblm_post_submission' );
	}

?>
	<div id="updated" class="updated fade">
	<p>
	<?php
	if ($sucess) {

		$linksql = "SELECT J.pid FROM ".$wpdb->prefix."player P, ".$wpdb->prefix."bb2wp J WHERE P.p_id = J.tid AND J.prefix = 'p_' AND P.p_id = ".$_POST['bblm_player'];
	$wppage_id = $wpdb->get_var($linksql);
		print("Journeyman has been hired. You may wish to <a href=\"".home_url()."/wp-admin/post.php?post=".$wppage_id."&action=edit\" title=\"Edit the players description\">edit the players description</a> to take this into account!");
	}
	else {
		print("Something went wrong");
	}
	?>
</p>
	</div>
<?php
} //end of hire Journeyman

  //////////////////////
 // Set Player Image //
//////////////////////
else if (isset($_POST['bblm_pimg_update'])) {

/*	print("<pre>");
	print_r($_POST);
	print("</pre>");
	print("<hr />");*/

	$updatepimgsql = 'UPDATE `'.$wpdb->prefix.'player` SET `p_img` = \''.$_POST['bblm_pimg'].'\' WHERE `p_id` = '.$_POST['bblm_pid'].' LIMIT 1';
	if (FALSE !== $wpdb->query($updatepimgsql)) {
		$sucess = TRUE;
		do_action( 'bblm_post_submission' );
	}
?>
		<div id="updated" class="updated fade">
		<p>
<?php
	if ($sucess) {
?>
			Player Image has been set. <a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.edit.player.php" title="Edit player details">Back to the team select screen</a>
<?php
	}
	else {
		print("Something went wrong");
	}
?>
	</p>
		</div>
<?php

}//end of setting player image

  ////////////////////////////////////
 // Submit changes to Match History //
////////////////////////////////////
else if (isset($_POST['bblm_mhistory_update'])) {

/*	print("<pre>");
	print_r($_POST);
	print("</pre>");
	print("<hr />");*/

	//initialise array to hold sql
	$mhistoryupdatesql = array();

	//Set initil values for loop
	$p = 1;
	$pmax = $_POST['bblm_pmcount'];

	while ($p <= $pmax){

		if ("on" == $_POST['bblm_pchng'.$p]) {
			$mhistoryupdatesql[$p] = 'UPDATE `'.$wpdb->prefix.'match_player` SET `mp_td` = \''.$_POST['bblm_ptd'.$p].'\', `mp_cas` = \''.$_POST['bblm_pcas'.$p].'\', `mp_comp` = \''.$_POST['bblm_pcomp'.$p].'\', `mp_int` = \''.$_POST['bblm_pint'.$p].'\', `mp_mvp` = \''.$_POST['bblm_pmvp'.$p].'\', `mp_spp` = \''.$_POST['bblm_pspp'.$p].'\', `mp_mng` = \''.$_POST['bblm_pmng'.$p].'\', `mp_inj` = \''.$_POST['bblm_pinj'.$p].'\', `mp_inc` = \''.$_POST['bblm_pinc'.$p].'\' WHERE `m_id` = '.$_POST['bblm_mid'.$p].' AND `p_id` = '.$_POST['bblm_pid'].' LIMIT 1';
		} //end of if changed
		$p++;
	}

	foreach ($mhistoryupdatesql as $pmh) {
		if (FALSE !== $wpdb->query($pmh)) {
			$sucess = TRUE;
		}
		bblm_update_player($_POST['bblm_pid'], 1);
	}

	if ($sucess) {
		print("<div id=\"updated\" class=\"updated fade\"><p>Changes have been made. you may wish to check the SPP points held on the player profile.</p></div>");
	}
	else {
	print("<div id=\"updated\" class=\"updated fade\"><p>Something fucked up, try again!</p></div>");
	}


}//end of submit match history


  ////////////////////////////////////
 // Submit changes to Player Stats //
////////////////////////////////////
else if (isset($_POST['bblm_stat_update'])) {

/*	print("<pre>");
	print_r($_POST);
	print("</pre>");
	print("<hr />");*/

	//Generate SQL
	$pstatupdatesql = 'UPDATE `'.$wpdb->prefix.'player` SET `p_num` = \''.$_POST['bblm_posnum'].'\', `p_ma` = \''.$_POST['bblm_pma'].'\', `p_st` = \''.$_POST['bblm_pst'].'\', `p_ag` = \''.$_POST['bblm_pag'].'\', `p_av` = \''.$_POST['bblm_pav'].'\', `p_spp` = \''.$_POST['bblm_pspp'].'\', `p_skills` = \''.$_POST['bblm_pskills'].'\', `p_mng` = \''.$_POST['bblm_mng'].'\', `p_injuries` = \''.$_POST['bblm_pinj'].'\', `p_cost` = \''.$_POST['bblm_pcost'].'\', `p_cost_ng` = \''.$_POST['bblm_pcostng'].'\', `p_status` = \''.$_POST['bblm_status'].'\' WHERE `p_id` = \''.$_POST['bblm_pid'].'\' LIMIT 1';
	//print("<p>".$pstatupdatesql."</p>");

	if (FALSE !== $wpdb->query($pstatupdatesql)) {
		$sucess = TRUE;
		do_action( 'bblm_post_submission' );
	}
?>
		<div id="updated" class="updated fade">
		<p>
<?php
	if ($sucess) {
?>
			Player has been updated. <a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.edit.player.php" title="Edit player details">Back to the team select screen</a>
<?php
	}
	else {
		print("Something went wrong");
	}
?>
	</p>
		</div>
<?php

} //end of changes to player stats
  ////////////////////
 // $_GET checking //
////////////////////
else if ("edit" == $_GET['action']) {
	if ("stats" == $_GET['item']) {
		  //////////////////////////
		 // Editing Player Stats //
		//////////////////////////
		$pid = $_GET['id'];

		$playerstatsql = 'SELECT P.*, O.post_title, I.pos_name, P.t_id FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' O, '.$wpdb->prefix.'position I WHERE P.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = O.ID AND I.pos_id = P.pos_id AND P.p_id = '.$pid.' LIMIT 1';
		if ($p = $wpdb->get_row($playerstatsql)) {
			$playersppsql = 'SELECT SUM(M.mp_spp) FROM '.$wpdb->prefix.'match_player M WHERE M.p_id = '.$pid.' AND M.mp_spp > 0';
			$psppfdb = $wpdb->get_var($playersppsql);
?>
		<h3>Edit Player Stats - <?php print($p->post_title); ?></h3>
		<form name="bblm_updatestats" method="post" id="post">
		<p>Below are the statistics for this player. make any changes and press the update button to save. For the MNG? and Status fields, 0 = No and 1 = Yes</p>

		<table cellspacing="0" class="widefat">
			<thead>
			<tr>
				<th>Name</th>
				<th>Position</th>
				<th><label for="bblm_posnum">Num</label></th>
				<th><label for="bblm_pma">MA</label></th>
				<th><label for="bblm_pst">ST</label></th>
				<th><label for="bblm_pag">AG</label></th>
				<th><label for="bblm_pav">AV</label></th>
				<th><label for="bblm_psp">SPP</label></th>
				<th>SPP (from DB)</th>
				<th><label for="bblm_pcost">Cost</label></th>
				<th><label for="bblm_pcostng">Cost NG</label></th>
				<th><label for="bblm_mng">MNG?</label></th>
				<th><label for="bblm_status">Status</label></th>
			</tr>
			</thead>
			<tbody>
			<tr>
				<td><?php print($p->post_title); ?></td>
				<td><?php print($p->pos_name); ?></td>
				<td><input type="text" name="bblm_posnum" size="2" value="<?php print($p->p_num); ?>" id="bblm_posnum" maxlength="2"></td>
				<td><input type="text" name="bblm_pma" size="2" value="<?php print($p->p_ma); ?>" id="bblm_pma" maxlength="2"></td>
				<td><input type="text" name="bblm_pst" size="2" value="<?php print($p->p_st); ?>" id="bblm_pst" maxlength="2"></td>
				<td><input type="text" name="bblm_pag" size="2" value="<?php print($p->p_ag); ?>" id="bblm_pag" maxlength="2"></td>
				<td><input type="text" name="bblm_pav" size="2" value="<?php print($p->p_av); ?>" id="bblm_pav" maxlength="2"></td>
				<td><input type="text" name="bblm_pspp" size="2" value="<?php print($p->p_spp); ?>" id="bblm_pspp" maxlength="3"></td>
				<td><?php print($psppfdb); ?></td>
				<td><input type="text" name="bblm_pcost" size="5" value="<?php print($p->p_cost); ?>" id="bblm_pcost" maxlength="7"></td>
				<td><input type="text" name="bblm_pcostng" size="5" value="<?php print($p->p_cost_ng); ?>" id="bblm_costng" maxlength="7"></td>
				<td><input type="text" name="bblm_mng" size="2" value="<?php print($p->p_mng); ?>" id="bblm_pmng" maxlength="1"></td>
				<td><input type="text" name="bblm_status" size="2" value="<?php print($p->p_status); ?>" id="bblm_pstatus" maxlength="1"></td>
			</tr>
			</tbody>
		</table>

		<dl>
			<dt>Skills</dt>
			<dd><textarea name="bblm_pskills" cols="60" rows="3"><?php print($p->p_skills); ?></textarea></dd>
			<dt>Injuries</dt>
			<dd><textarea name="bblm_pinj" cols="60" rows="3"><?php print($p->p_injuries); ?></textarea></dd>
		</dl>
		<input type="hidden" name="bblm_pid" size="5" value="<?php print($pid); ?>" id="bblm_pid" maxlength="5">
		<p class="submit">
		<input type="submit" name="bblm_stat_update" value="Update Player" title="Update Player"/> or <a href="<?php bloginfo('url'); ?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.edit.player.php&action=select&item=none&id=<?php print($p->t_id); ?>" title="Cancel this and select another player">Cancel</a>
		</p>
	</form>
<?php
		}
		else {
			//player note found
			print("<p>That player has not been found. Please go back to the edit player screen and try again.</p>\n");
		}
	}//end of if item == stats
	else if ("mhistory" == $_GET['item']) {
		  //////////////////////////
		 // Editing Match History //
		//////////////////////////
		$pid = $_GET['id'];

		$playermatchsql = 'SELECT M.*, P.p_name, P.t_id, UNIX_TIMESTAMP(X.m_date) AS mdate, G.post_title AS TA, T.t_id AS TAid, G.guid AS TAlink, B.post_title AS TB, B.guid AS TBlink, R.t_id AS TBid, Z.guid FROM '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'match X, '.$wpdb->prefix.'bb2wp Y, '.$wpdb->posts.' Z, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'team R, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp F, '.$wpdb->posts.' G, '.$wpdb->prefix.'bb2wp V, '.$wpdb->posts.' B WHERE T.t_id = F.tid AND F.prefix = \'t_\' AND F.pid = G.ID AND R.t_id = V.tid AND V.prefix = \'t_\' AND V.pid = B.ID AND C.c_id = X.c_id AND C.c_show = 1 AND X.m_teamA = T.t_id AND X.m_teamB = R.t_id AND M.p_id = P.p_id AND M.m_id = X.m_id AND X.m_id = Y.tid AND Y.prefix = \'m_\' AND Y.pid = Z.ID AND M.p_id = '.$pid.' ORDER BY X.m_date DESC';
		if ($playermatch = $wpdb->get_results($playermatchsql)) {
			$count = 1;
?>
			<h3>Edit Player Match History</h3>
			<form name="bblm_updatemhistory" method="post" id="post">
			<table cellspacing="0" class="widefat">
				<thead>
				<tr>
					<th>Opponant</th>
					<th>TD</th>
					<th>CAS</th>
					<th>INT</th>
					<th>COMP</th>
					<th>MVP</th>
					<th>SPP</th>
					<th>MNG?</th>
					<th>Increases</th>
					<th>Injuries</th>
					<th>Changed?</th>
					<th>View</th>
				</tr>
				</thead>
				</tbody>
<?php
			foreach ($playermatch as $pm) {
?>
				<tr>
<?php
					if ($pm->TAid == $pm->t_id) {
							print("					<td>vs <strong>".$pm->TB."</strong> - (".date("d.m.y", $pm->mdate).")</td>\n");
					}
					else {
						print("					<td>vs <strong>".$pm->TA."</strong> - (".date("d.m.y", $pm->mdate).")</td>\n");
					}
?>
					<td><input type="text" name="bblm_ptd<?php print($count); ?>" size="3" value="<?php print($pm->mp_td); ?>" id="bblm_ptd" maxlength="2"></td>
					<td><input type="text" name="bblm_pcas<?php print($count); ?>" size="3" value="<?php print($pm->mp_cas); ?>" id="bblm_pcas" maxlength="2"></td>
					<td><input type="text" name="bblm_pint<?php print($count); ?>" size="3" value="<?php print($pm->mp_int); ?>" id="bblm_pint" maxlength="2"></td>
					<td><input type="text" name="bblm_pcomp<?php print($count); ?>" size="3" value="<?php print($pm->mp_comp); ?>" id="bblm_pma" maxlength="2"></td>
					<td><input type="text" name="bblm_pmvp<?php print($count); ?>" size="3" value="<?php print($pm->mp_mvp); ?>" id="bblm_pmvp" maxlength="2"></td>
					<td><input type="text" name="bblm_pspp<?php print($count); ?>" size="3" value="<?php print($pm->mp_spp); ?>" id="bblm_pspp" maxlength="2"></td>
					<td><input type="text" name="bblm_pmng<?php print($count); ?>" size="3" value="<?php print($pm->mp_mng); ?>" id="bblm_pmng" maxlength="2"></td>
					<td><input type="text" name="bblm_pinc<?php print($count); ?>" size="10" value="<?php print($pm->mp_inc); ?>" id="bblm_pinc"></td>
					<td><input type="text" name="bblm_pinj<?php print($count); ?>" size="10" value="<?php print($pm->mp_inj); ?>" id="bblm_pinj"></td>
					<td><input type="checkbox" name="bblm_pchng<?php print($count); ?>"></td>
					<td><a href="<?php print($pm->guid); ?>" title="View the match in more detail">View</a><input type="hidden" name="bblm_mid<?php print($count); ?>" size="5" value="<?php print($pm->m_id); ?>" id="bblm_mid" maxlength="5"></td>
				</tr>
<?php
				$count++;
			}
			print("				</thody>\n				</table>\n");
		}
?>
		<input type="hidden" name="bblm_pid" size="5" value="<?php print($pid); ?>" id="bblm_pid" maxlength="5">
		<input type="hidden" name="bblm_pmcount" size="5" value="<?php print($count-1); ?>" id="bblm_pmcount" maxlength="3">
		<p class="submit">
			<input type="submit" name="bblm_mhistory_update" value="Update Player" title="Update Player"/>
		</p>
	</form>
<?php



	}//end of if item == mhistory
	else if ("image" == $_GET['item']) {
		  ////////////////////////////
		 // Setting a Player Image //
		////////////////////////////
		$pid = $_GET['id'];
		print("<h3>Set a Player Image</h3>\n");

		$existingimgsql = 'SELECT p_img FROM '.$wpdb->prefix.'player WHERE p_id = '.$pid;
		$eximg = $wpdb->get_var($existingimgsql);

		if (empty($eximg)) {
			print("<p><strong>This player currently has no image set. Use the form below to set one if you wish.</strong></p>\n");
		}
		else {
			print("<p>The current payer image is below. You can use the form below it to change this if you wish. If you are happy with the current image then navigate away from the page.</p>\n");
?>
			<p><img src="<?php bloginfo('url'); ?>/images/players/<?php print($eximg); ?>" alt="The Players Custom Image" /></p>
<?php
		}

		print("<h4>Set a new image for this player</h4>\n");
?>
	<form name="bblm_pimgform" method="post" id="post">
		<table class="form-table">
			<tr class="form-field form-required">
				<th scope="row" valign="top"><label for="bblm_pimg">Select an Image</label></th>
				<td><select name="bblm_pimg" id="bblm_pimg">
<?php
		$dir = "../images/players/";
		if (is_dir($dir)) {
		    if ($dh = opendir($dir)) {
		        while (($file = readdir($dh)) !== false) {
					if (is_file($dir.$file)) {
						print("<option value=\"".$file."\"");
						if ($file == $eximg) {
							print(" selected=\"selected\"");
						}
						print(">".$file."</option>\n");
					}
		        }
		        closedir($dh);
		    }
		}
?>
				</select></td>
			</tr>
		</table>

		<input type="hidden" name="bblm_pid" size="5" value="<?php print($pid); ?>" id="bblm_pid" maxlength="5">
		<p class="submit">
			<input type="submit" name="bblm_pimg_update" value="Set Image" title="Set Image"/>
		</p>
<?php

	}//end if item == image
	else if ("jmstatus" == $_GET['item']) {
		  ///////////////////////
		 // Hire a Journeyman //
		///////////////////////
		$bblm_player = $_GET['id'];
?>
<h3>Permanently Add a Journeyman To This Team</h3>
<?php
		$playerdetailssql = "SELECT P.p_id, P.p_name, P.p_cost, T.t_name, T.t_bank, T.r_id, P.t_id, P.pos_id FROM ".$wpdb->prefix."player P, ".$wpdb->prefix."team T WHERE P.t_id = T.t_id AND P.p_id = ".$bblm_player;
		$jm = $wpdb->get_row($playerdetailssql);
		//Check to see if the player is actually a Journeyman
		if (1 == $jm->pos_id) {
?>
<h4>Player Details<h4>
<table cellspacing="0" class="widefat" style="width:400px;">
	<tr>
		<th>Player Name</th>
		<td><?php print($jm->p_name); ?></td>
	</tr>
	<tr>
		<th>Hiring Team</th>
		<td><?php print($jm->t_name); ?></td>
	</tr>
	<tr>
		<th>Funds available</th>
		<td><?php print(number_format($jm->t_bank)); ?></td>
	</tr>
	<tr>
		<th>Cost to hire</th>
		<td><?php print(number_format($jm->p_cost)); ?> gp</td>
	</tr>
</table>

<table cellspacing="0" class="widefat">
<thead>
 <tr>
 	<th>Pld</th>
 	<th>TD</th>
 	<th>CAS</th>
 	<th>COMP</th>
	<th>INT</th>
 	<th>MVP</th
 	<th>SPP</th>
 </tr>
 </thead>
 <tbody>
<?php
				$statssql = 'SELECT P.p_name, P.p_cost_ng, Y.pos_name, P.t_id, COUNT(*) AS GAMES, SUM(M.mp_td) AS TD, SUM(M.mp_cas) AS CAS, SUM(M.mp_comp) AS COMP, SUM(M.mp_int) AS MINT, SUM(M.mp_mvp) AS MVP, SUM(M.mp_spp) AS SPP FROM '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'position Y WHERE M.p_id = P.p_id AND P.pos_id = Y.pos_id AND M.mp_counts = 1 AND M.p_id = '.$jm->p_id.' GROUP BY P.p_id';
				if ($stats = $wpdb->get_results($statssql)) {
					foreach ($stats as $s) {
						print (" <tr>\n		<td><strong>".$s->GAMES."<strong></td>\n	<td>".$s->TD."</td>\n	<td>".$s->CAS."</td>\n	<td>".$s->COMP."</td>\n	<td>".$s->MINT."</td>\n	<td>".$s->MVP."</td>\n	<td>".$s->SPP."</td>\n </tr>\n");
					}
					print(" </tbody>\n </table>\n");
				}
				else {
					print("	<tr>\n	<td colspan=\"7\">This player has not played any games yet for this team.</td>\n </tr>\n </tbody>\n </table>\n");
				}

				$jmposnumsql = "SELECT pos_id from ".$wpdb->prefix."position WHERE pos_freebooter = 1 AND r_id = ".$jm->r_id;
				$position_id = $wpdb->get_var($jmposnumsql);

				$cashlevel = 0;
				if (($jm->t_bank-$jm->p_cost)>=0) {
					$cashlevel = 1;
				}

				//Check that the team has enough money
				if (0 !== $cashlevel) {
					print("<p>Please review the above details and if you wish to add the player to the team permantly, press the hire below button.</p>\n");
				}
				else {
					//Not enough cash in the bank to hire the player
					print("<div id=\"updated\" class=\"updated fade\">\n<p>There is not enough money in the teams bank to add this Journeyman to the team. If you press the Hire button below, the bank will be left in the negative!</p>\n</div>\n");
				}

?>
	<form name="bblm_addjourneyman" method="post" id="post">

	<input type="hidden" name="bblm_player" size="6" value="<?php print($jm->p_id); ?>">
	<input type="hidden" name="bblm_position" size="6" value="<?php print($position_id); ?>">
	<input type="hidden" name="bblm_team" size="6" value="<?php print($jm->t_id); ?>">
	<input type="hidden" name="bblm_cost" size="6" value="<?php print($jm->p_cost); ?>">

	<p class="submit">
		<input type="submit" name="bblm_journeyman_add" tabindex="4" value="Hire Journeyman" title="Hire Journeyman"/> or <a href="<?php bloginfo('url'); ?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.edit.player.php&action=select&item=none&id=<?php print($jm->t_id); ?>" title="Cancel this and select another player">Cancel</a>
	</p>

	</form>
<?php
		}
		//Player is not actually a Journeyman!
		else {
			print("<div id=\"updated\" class=\"updated fade\">\n<p>This Player isn't actually a Journeyman! Please try again by <a href=\"");
			bloginfo('url');
			print("/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.edit.player.php&action=select&item=none&id=".$jm->t_id."\" title=\"View the list of players on the team\">selecting a different player</a>.</p>\n</div>\n");
		}


	}//end of hire a Journeyman

	else if ("rename" == $_GET['item']) {
		  ///////////////////////
		 // Rename a Player //
		///////////////////////
		$pid = $_GET['id'];
		print("<h3>Rename a Player</h3>\n");
?>
	<form name="bblm_renameplayer" method="post" id="post">
<?php
		//Gather exsting player name details
		$playersql = 'SELECT P.ID, P.post_title, P.guid, P.post_name FROM '.$wpdb->prefix.'player X, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE X.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = P.id AND X.p_id = '.$pid;
		if ($p = $wpdb->get_row($playersql)) {
?>
			<p>Use the fields below to rename a player. If you wish to change your mind, just navigate away from the page.</p>
			<p><label for="bblm_pname" class="selectit">Player Name:</label>
			<input type="text" name="bblm_pname" size="25" tabindex="1" value="<?php print($p->post_title); ?>" maxlength="30"></p>
			<p>Be Very Careful with updting these two. If you change the last part of the URL then you <strong>Must</strong> change the below slug to match (and vise versa). No spaces or special chars. if you need a gap, use "-"</p>
			<p><label for="bblm_purl" class="selectit">Player URL:</label>
			<input type="text" name="bblm_purl" size="70" tabindex="1" value="<?php print($p->guid); ?>"></p>
			<p>As above, if you change this then you <strong>must</strong> change the last part of the above url to match.</p>
			<p><label for="bblm_pslug" class="selectit">Player Slug:</label>
			<input type="text" name="bblm_pslug" size="25" tabindex="1" value="<?php print($p->post_name); ?>" maxlength="30"></p>

			<input type="hidden" name="bblm_pid" value="<?php print($pid); ?>">
			<input type="hidden" name="bblm_postid" value="<?php print($p->ID); ?>">
			<p class="submit">
				<input type="submit" name="bblm_rename_player" tabindex="4" value="Confirm new name" title="Confirm new name"/>
			</p>
		</form>
<?php
		} //end of if SQL
	}//end of rename a player

	else if ("remove" == $_GET['item']) {
		  ///////////////////////
		 // Remove a Player //
		///////////////////////
		$pid = $_GET['id'];
		print("<h3>Remove a Player</h3>\n");

?>
		<p>The Players career record is shown below. If you wish to continue with the removal please select a reason for their departure and any other required information and press the continue button.</p>

		<table cellspacing="0" class="widefat">
		<thead>
		 <tr>
		 	<th>Player Name</th>
		 	<th>Position</th>
		 	<th>Pld</th>
		 	<th>TD</th>
		 	<th>CAS</th>
		 	<th>COMP</th>
		 	<th>INT</th>
		 	<th>MVP</th
		 	<th>SPP</th>
		 </tr>
		 </thead>
		 <tbody>
<?php
		$statssql = 'SELECT P.p_name, P.p_cost_ng, Y.pos_name, P.t_id, COUNT(*) AS GAMES, SUM(M.mp_td) AS TD, SUM(M.mp_cas) AS CAS, SUM(M.mp_comp) AS COMP, SUM(M.mp_int) AS MINT, SUM(M.mp_mvp) AS MVP, SUM(M.mp_spp) AS SPP FROM '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'position Y WHERE M.p_id = P.p_id AND P.pos_id = Y.pos_id AND M.mp_counts = 1 AND M.p_id = '.$pid.' GROUP BY P.p_id';
			if ($stats = $wpdb->get_results($statssql)) {
				foreach ($stats as $s) {
					print (" <tr>\n  	<td>".$s->p_name."</td>\n  	<td>".$s->pos_name."</td>\n  	<td>".$s->GAMES."</td>\n  	<td>".$s->TD."</td>\n  	<td>".$s->CAS."</td>\n  	<td>".$s->COMP."</td>\n  	<td>".$s->MINT."</td>\n  	<td>".$s->MVP."</td>\n  	<td>".$s->SPP."</td>\n </tr>\n");
					$t_id = $s->t_id;
				}
				print("			</tbody>\n		</table>\n");
			}
			else {
				//No match result was returned. We still need to establish team id
				$playerdetailssql = 'SELECT Z.post_title, Y.pos_name, P.t_id FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'position Y, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' Z WHERE P.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = Z.ID AND Y.pos_id = P.pos_id AND P.p_id = '.$pid;
				$pd = $wpdb->get_row($playerdetailssql);
				print(" <tr>\n  	<td>".$pd->post_title."</td>\n  	<td>".$pd->pos_name."</td>\n  	<td colspan=\"7\">According to the Leagues Archives, this player has done Nothing!</td>\n </tr>\n			</tbody>\n		</table>\n");
				$t_id = $pd->t_id;

			}
?>
		<form name="bblm_removeplayer" method="post" id="post">

		<p><label for="bblm_fate">Reason for Removal</label>
		<select name="bblm_fate" id="bblm_fate">
<?php
			$fatessql = "SELECT f_id, f_name FROM ".$wpdb->prefix."fate ORDER BY f_name";
			if ($fates = $wpdb->get_results($fatessql)) {
				foreach ($fates as $fate) {
					print("<option value=\"$fate->f_id\">".$fate->f_name."</option>\n");
				}
			}
?>
		</select></p>

		<fieldset><legend>Description of Death / Whitty Comment</legend>

		<div>
		  <textarea rows='10' cols='40' name='bblm_fdesc' tabindex='2' id='bblm_fdesc'></textarea>
		</div>
		</fieldset>


			<p><label for="bblm_fmatch">Last Match</label>
			<select name="bblm_fmatch" id="bblm_fmatch">
				<option value="0" selected="selected">N/A</option>
<?php
			$lastmatchsql = 'SELECT M.m_id, UNIX_TIMESTAMP(M.m_date) AS MDATE, P.post_title, M.m_teamAtd, M.m_teamBtd, C.c_name FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P, '.$wpdb->prefix.'match_team X, '.$wpdb->prefix.'comp C WHERE M.m_id = J.tid AND J.prefix = \'m_\' AND J.pid = P.ID AND M.m_id = X.m_id AND C.c_id = M.c_id AND X.t_id = '.$t_id.' ORDER BY M.m_date DESC LIMIT 8';
			if ($lastmatch = $wpdb->get_results($lastmatchsql)) {
				foreach ($lastmatch as $lm) {
					print("<option value=\"$lm->m_id\">".date("d.m.Y", $lm->MDATE)." - ".$lm->post_title." (".$lm->m_teamAtd." - ".$lm->m_teamBtd.")</option>\n");
				}
			}
?>
		</select></p>

			<p><label for="bblm_fkiller">Killer</label>
			<select name="bblm_fkiller" id="bblm_fkiller">
				<option value="0" selected="selected">Unknown</option>
				<option value="C">Crowd</option>
				<option value="<?php print($pid); ?>">Themself!</option>
				<option value="W">Wizard</option>
<?php
		//generate the list of players
		$playersql = $sql = 'SELECT X.post_title, P.p_id, L.post_title AS Team, P.p_num FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' X, '.$wpdb->prefix.'bb2wp K, '.$wpdb->posts.' L WHERE P.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = X.ID AND P.t_id = K.tid AND K.prefix = \'t_\' AND K.pid = L.ID ORDER BY Team ASC, P.p_num ASC';
		//initialise vars
		$last_team = "";
		$is_first = 1;
		if ($players = $wpdb->get_results($playersql)) {
			//we have some teams so build the repeated input
			$playerlisting = "			<option value=\"N\">Not Applicable / Unkown</option>\n";
			foreach ($players as $pd) {
				if ($last_team !== $pd->Team) {
					if (0 == $is_first) {
						$playerlisting .= "			</optgroup>";
					}
					$playerlisting .= "			<optgroup label=\"".$pd->Team."\">";
					$last_team = $pd->Team;
					$is_first = 0;
				}
				$playerlisting .= "			<option value=\"".$pd->p_id."\"> -- #".$pd->p_num." - ".$pd->post_title."</option>\n";
			}
		}
		print($playerlisting);

?>
		</select><br/>
		If the player killed themself then please select the "Themself" option from the list above.</p>

		<input type="hidden" name="bblm_pid" value="<?php print($pid); ?>">
		<input type="hidden" name="bblm_tid" value="<?php print($t_id); ?>">

		<p class="submit">
			<input type="submit" name="bblm_remove_player" tabindex="4" value="Remove Player" title="Remove Player"/>
		</p>
		</form>


















<?php
	}//end of remove a player

}//end of $_GET checking

//checks for direct link from edit.player ($_POST) or from edit.team ($_GET)
else if ((isset($_POST['bblm_team_select'])) || ("select" == $_GET['action'])) {
  ///////////////////////////////////////////
 // Step 2: Team selected - Select Player //
///////////////////////////////////////////
/*	print("<pre>");
	print_r($_POST);
	print("</pre>");
	print("<hr />"); */

if (isset($_POST['bblm_team_select'])) {
	$tid = $_POST['bblm_tname'];
}
else {
	$tid = $_GET['id'];
}


?>
<form name="bblm_playeroptions" method="post" id="post">
	<input type="hidden" name="bblm_tid" size="3" value="<?php print($tid); ?>">
	<p>Below are all the players on this team. Please select one of the options below.</p>

<?php
  /////////////////////////
 // List Active Players //
/////////////////////////
	$playersql = 'SELECT P.p_id, P.p_num, X.post_title, Y.pos_name, X.ID, X.guid, P.t_id, P.p_status, P.pos_id FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' X, '.$wpdb->prefix.'position Y WHERE P.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = X.ID AND P.pos_id = Y.pos_id AND P.t_id = '.$tid.' ORDER BY P.p_num';
	if ($players = $wpdb->get_results($playersql)) {
		$zebracount = 1;
		print("<table class=\"widefat\">\n	<thead>\n		 <tr>\n		   		<th scope=\"row\">ID</th>\n		   <th scope=\"col\">Player Name</th>\n		   <th scope=\"col\">Edit Stats</th>\n		   <th scope=\"col\">Match History</th>\n		   <th scope=\"col\">Image</th>\n		   <th scope=\"col\">Rename</th>\n		   <th scope=\"col\">Remove</th>\n		   <th scope=\"col\">Hire</th>\n		   <th scope=\"col\">View</th>\n		 </tr>\n	</thead>\n	<tbody>\n");

		foreach ($players as $p) {

				if ($zebracount % 2) {
					print("					<tr class=\"alternate\">\n");
				}
				else {
					print("					<tr>\n");
				}

				print("		   <td>".$p->p_id."</a></td>\n		   <td><a href=\"");

				bloginfo('url');
				print("/wp-admin/post.php?post=".$p->ID."&action=edit\">#".$p->p_num." - ".$p->post_title."</a> - ".$p->pos_name."</td>\n");

				print("							<td><a href=\"");
				bloginfo('url');
				print("/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.edit.player.php&action=edit&item=stats&id=".$p->p_id."\" title=\"Edit the Player Stats\">Edit Stats</a></td>\n");

				//we now check to see how many games this player has played
				$pgamessql = 'SELECT COUNT(*) from '.$wpdb->prefix.'match_player where p_id = '.$p->p_id;
				$pgames = $wpdb->get_var($pgamessql);
				if (0 < $pgames) {
					//Player had been in a match so we can edit the match history
					print("							<td><a href=\"");
					bloginfo('url');
					print("/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.edit.player.php&action=edit&item=mhistory&id=".$p->p_id."\" title=\"Edit the Player Stats\">Match History</a></td>\n");
				}
				else {
					print("							<td>-</td>");
				}

				print("							<td><a href=\"");
				bloginfo('url');
				print("/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.edit.player.php&action=edit&item=image&id=".$p->p_id."\" title=\"Set a custom image for this player\">Set Image</a></td>\n");

				print("							<td><a href=\"");
				bloginfo('url');
				print("/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.edit.player.php&action=edit&item=rename&id=".$p->p_id."\" title=\"Rename this player\">Rename</a></td>\n");


				if ($p->p_status) {
					//player is still active so this link needs to be shown
					print("							<td><a href=\"");
					bloginfo('url');
					print("/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.edit.player.php&action=edit&item=remove&id=".$p->p_id."\" title=\"Remove this player\">Remove</a></td>\n");
				}
				else {
					print("							<td>-</td>");
				}

				if ((1 == $p->pos_id) && ($p->p_status)) {
					//player is a Journeyman so a hire link can be dispayed if they are active
					print("							<td><a href=\"");
					bloginfo('url');
					print("/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.edit.player.php&action=edit&item=jmstatus&id=".$p->p_id."\" title=\"Hire this player\">Hire</a></td>\n");
				}
				else {
					print("							<td>-</td>");
				}

				print("<td><a href=\"".$p->guid."\" title=\"View the player page\">View</a></td>		 </tr>\n");


			$zebracount++;
		}
		print("	</tbody>\n</table>\n");
?>
		<h3>Related Links</h3>
		<ul>
			<li><a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.add.player.php&action=add&item=none&id=<?php print($p->t_id); ?>" title="Add a new player to the team">Add a Player to this team</a></li>
			<li><a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.edit.player.php" title="Edit Select another team">Select another team</a></li>
		</ul>
<?php
	}
	else {
		print("<p><strong>There are no known players on this team!</strong> ");
?>		<a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.edit.player.php" title="Edit Select another team">Select another team</a></li>
<?php
	}

}
else {
  ///////////////////////////
 // Step 1: Select a team //
///////////////////////////
?>
	<form name="bblm_seectteam" method="post" id="post">

	<p>Select a team below. All the players from that team will then be displayed.</p>

	<p><label for="bblm_tname" class="selectit">Select a Team:</label>
	<select name="bblm_tname" id="bblm_tname">
<?php

		$teamsql = 'SELECT T.t_id, T.t_name FROM '.$wpdb->prefix.'team T, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE T.t_id = J.tid AND J.prefix = \'t_\' AND J.pid = P.ID ORDER BY T.t_name ASC';
		if ($teams = $wpdb->get_results($teamsql)) {
			foreach ($teams as $team) {
				print("		<option value=\"$team->t_id\">".$team->t_name."</option>\n");
			}
		}
?>
		</select></p>

	<p class="submit">
	<input type="submit" name="bblm_team_select" value="Display Players" title="Display the players from the above team"/>
	</p>
	</form>
<?php
} //end of else section
?>

</div>
