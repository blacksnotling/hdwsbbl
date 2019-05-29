<?php
/**
 * BBowlLeagueMan Add Player match record
 *
 * Page used to add a ecord of a players performance during a match
 *
 * @author 		Blacksnotling
 * @category 	Core
 * @package 	BBowlLeagueMan/Pages
 */

//Check the file is not being accessed directly
if (!function_exists('add_action')) die('You cannot run this file directly. Naughty Person');
?>
<div class="wrap">
	<h2>Record Player Actions for a Match</h2>
	<p>From this page you can record players actions during a match and also update any player profiles. <strong>Warning</strong>: This may take some time to process all the information! Please <strong>don't</strong> hit submit multiple times!!</p>

<?php

if (isset($_POST['bblm_player_increase'])) {
	  /////////////////////////////////////////////////////////////////
	 // Step 4: Updating the Player records and recording increases //
	/////////////////////////////////////////////////////////////////


	//Set initil values for loop
	$p = 1;
	$pmax = $_POST['bblm_numofplayers'];
	//define array to hold playerupdate sql
	$playersqla = array();


while ($p <= $pmax){

	//if  "on" result in "changed" then generate SQL
	if (on == $_POST['bblm_pcng'.$p]) {


		$updatesql = 'UPDATE `'.$wpdb->prefix.'player` SET `p_ma` = \''.$_POST['bblm_pma'.$p].'\', `p_st` = \''.$_POST['bblm_pst'.$p].'\', `p_ag` = \''.$_POST['bblm_pag'.$p].'\', `p_av` = \''.$_POST['bblm_pav'.$p].'\', `p_skills` = \''.$_POST['bblm_pskills'.$p].'\', `p_injuries` = \''.$_POST['bblm_pinjuries'.$p].'\', `p_cost` = \''.$_POST['bblm_pcost'.$p].'\'';

		if ('1' !== $_POST['bblm_mng'.$p]) {
			$updatesql .= ', `p_cost_ng` = \''.$_POST['bblm_pcost'.$p].'\'';
		}

		$updatesql .= ' WHERE `p_id` = '.$_POST['bblm_pid'.$p].' LIMIT 1';

	$playersqla[$p] = $updatesql;

	} //end of if changed

	$p++;

} //end of player loop

//Now we must set the match to complete
$updatematchsql = 'UPDATE `'.$wpdb->prefix.'match` SET `m_complete` = \'1\' WHERE `m_id` = '.$_POST['bblm_mid'].' LIMIT 1';


//for debugging
/*
foreach ($playersqla as $ps) {
	print("<p>".$ps."</p>");
}
print("<p>".$updateteamAsql."</p>");
print("<p>".$updateteamBsql."</p>");
print("<p>".$updatematchsql."</p>");
*/


//right, we have the built up sql string so it is time to insert into the DB One last time!

foreach ($playersqla as $ps) {
	if (FALSE !== $wpdb->query($ps)) {
		$sucess = TRUE;
	}
}
/*
if (FALSE !== $wpdb->query($updateteamAsql)) {
	$sucess = TRUE;
}
if (FALSE !== $wpdb->query($updateteamBsql)) {
	$sucess = TRUE;
}*/
bblm_update_tv($_POST['bblm_teamA']);
bblm_update_tv($_POST['bblm_teamB']);
if (FALSE !== $wpdb->query($updatematchsql)) {
	$sucess = TRUE;
}

if ($sucess) {
	print("<div id=\"updated\" class=\"updated fade\"><p>Increases and details have all been recorded. All done!</p></div>");
}

  //////////////////////////
 //  !!END OF PROCESS!!  //
//////////////////////////






}
else if (isset($_POST['bblm_player_actions'])) {
	//3rd Step: Recording the players actions for the match
	  ///////////////////////////////////////////////////////////////////////////
	 // Step 3: Updating bb_match_player table and recording changes to stats //
	///////////////////////////////////////////////////////////////////////////
/*	print("<hr>\n<pre>\n");
	print_r($_POST);
	print("</pre>\n<hr>\n");*/

	$compcounts = $_POST['bblm_ccounts'];


$playermatchsql = "INSERT INTO `".$wpdb->prefix."match_player` (`m_id`, `p_id`, `t_id`, `mp_td`, `mp_cas`, `mp_comp`, `mp_int`, `mp_mvp`, `mp_spp`, `mp_mng`, `mp_inj`, `mp_inc`, `mp_counts`) VALUES ";
	//Set initial values for loop
	$p = 1;
	$pmax = $_POST['bblm_numofplayers'];
	//define array to hold playerupdate sql
	$playersqla = array();
	//define array to hold injured sql
	$reactivatesql = array();

	//before we begin the main loop, we re-activate all the players who missed the game
	if ($compcounts)  {
		$selectinjplayer = 'SELECT p_id FROM '.$wpdb->prefix.'player WHERE p_mng = 1 AND (t_id = '.$_POST['bblm_teamA'].' or t_id = '.$_POST['bblm_teamB'].')';

		if ($injplayer = $wpdb->get_results($selectinjplayer)) {
			foreach ($injplayer as $ip) {
				$reactivatesql[] .= 'UPDATE `'.$wpdb->prefix.'player` SET `p_mng` = \'0\', `p_cost_ng` = p_cost  WHERE `p_id` = '.$ip->p_id.' LIMIT 1';
			}
		}

	} //end of compcounts


//Initialize var to capture first input
$is_first_player = 1;

//Beginning of main loop.
while ($p <= $pmax){

//before we go any further, we should see if the player in question ctually took part in the match!
if (on == $_POST['bblm_plyd'.$p]) {


	//we only want a comma added for all but the first
	if (1 !== $is_first_player) {
		$playermatchsql .= ", ";
	}

	//Set the default "on" result from a checkbox to a 1
	if (on == $_POST['mng'.$p]) {
		$_POST['mng'.$p] = 1;
	}
	//Fill in blanks for Injuries and Increases
	if (empty($_POST['bblm_injury'.$p])) {
		$_POST['bblm_injury'.$p] = "none";
	}
	if (empty($_POST['bblm_increase'.$p])) {
		$_POST['bblm_increase'.$p] = "none";
	}

	//generate the SQL
	$playermatchsql .= '(\''.$_POST['bblm_mid'].'\', \''.$_POST['bblm_pid'.$p].'\', \''.$_POST['bblm_tid'.$p].'\', \''.$_POST['bblm_td'.$p].'\', \''.$_POST['bblm_cas'.$p].'\', \''.$_POST['bblm_comp'.$p].'\', \''.$_POST['bblm_int'.$p].'\', \''.$_POST['bblm_mvp'.$p].'\', \''.$_POST['bblm_spp'.$p].'\', \''.$_POST['mng'.$p].'\', \''.$_POST['bblm_injury'.$p].'\', \''.$_POST['bblm_increase'.$p].'\', \'1\')';

	//if the comp counts, update the bb_player table with new spp values and p_mng if player is injured.
	if ($compcounts)  {
		$playerupdatesql = 'UPDATE `'.$wpdb->prefix.'player` SET `p_spp` = p_spp+\''.$_POST['bblm_spp'.$p].'\'';
			if ($_POST['mng'.$p])  {
				$playerupdatesql .= ', `p_cost_ng` = \'0\', `p_mng` = \'1\'';
			}
		$playerupdatesql .= ' WHERE `p_id` = \''.$_POST['bblm_pid'.$p].'\' LIMIT 1';
		//once we have the sql generated, we can insert into the array to insert later on
		$playersqla[$p] = $playerupdatesql;

		//change flag so that comma's are added to the insert sql string.
		//We do this here because we don't want the flag to change until someone has been entered into the db
	$is_first_player = 0;

	}

}//end of bblm_plyd checking
	// increment player and go onto the next one.
	$p++;

}

//Generate SQL to update the match (if it doesn't count then we end here!
$updatematchsql = 'UPDATE `'.$wpdb->prefix.'match` SET `m_complete` = \'1\' WHERE `m_id` = '.$_POST['bblm_mid'].' LIMIT 1';


//By This point we should have all the SQL Generated. Lets get inserting!
//Regardless of if the comp counts, we add the player records to the match_player table
if (FALSE !== $wpdb->query($playermatchsql)) {
	$sucess = TRUE;
	do_action( 'bblm_post_submission' );
}
//then if the comp counts, reset the injured players to active and then update the partivipating players.
if ($compcounts)  {
	foreach ($reactivatesql as $rs) {
		if (FALSE !== $wpdb->query($rs)) {
			$sucess = TRUE;
		}
	}
	foreach ($playersqla as $ps) {
		if (FALSE !== $wpdb->query($ps)) {
			$sucess = TRUE;
		}
	}
}
else {
	//If the comp doesn't count the then update the match table to show it is complete and exit.
	if (FALSE !== $wpdb->query($updatematchsql)) {
		$sucess = TRUE;
		$finished = 1;
		do_action( 'bblm_post_submission' );
	}
}


// For Debug purposes only!
/*
print("<p>".$updatematchsql."</p>");
print("<p>".$playermatchsql."</p>");
foreach ($reactivatesql as $rs) {
	print("<p>".$rs."</p>");
}

foreach ($playersqla as $ps) {
	print("<p>".$ps."</p>");
}
*/

//The SQL insertion is done. Now we determine if we continue or exit
if ($finished)  {
	print("<div id=\"updated\" class=\"updated fade\"><p>Match was updated. Thanks</p></div>\n");
}
else {
	//We Now carry on with the final step. Recording increases.
?>
	<form name="bblm_recordincreases" method="post" id="post">
	<input type="hidden" name="bblm_mid" size="3" value="<?php print($_POST['bblm_mid']); ?>">
	<input type="hidden" name="bblm_ccounts" size="3" value="<?php print($_POST['bblm_ccounts']); ?>">
	<input type="hidden" name="bblm_teamA" size="3" value="<?php print($_POST['bblm_teamA']); ?>">
	<input type="hidden" name="bblm_teamB" size="3" value="<?php print($_POST['bblm_teamB']); ?>">

	<h3>Record Changes to Players</h3>
	<p>Below are all the players who took part in the match. If they had an increase, please ensure that the "Chnged?" box is ticked. The Skills and Injuries boxs should be automatically filled but you will have to update any changes to Stats manually.</p>

<?php
$playersql = 'SELECT P.*, M.mp_inj, M.mp_inc, T.t_name FROM '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T WHERE P.p_id = M.p_id AND m_id = '.$_POST['bblm_mid'].' AND M.t_id = T.t_id ORDER BY P.t_id, P.p_num';
	if ($playerlist = $wpdb->get_results($playersql)) {
		//initiate var for count
		$p = 1;
		$is_first = 1;
		$current_team = "";

		foreach ($playerlist as $pl) {
			if ($pl->t_name !== $current_team) {
				$current_team = $pl->t_name;
				if (1 !== $is_first) {
					print("</table>\n");
				}
				$is_first = 1;
			}

			if ($is_first) {
				print("<h3>".$pl->t_name."</h3>\n<table cellspacing=\"0\" class=\"widefat\">\n <thead>\n <tr>\n   <th>#</th>\n   <th>Name</th>\n   <th>MA</th>\n   <th>ST</th>\n   <th>AG</th>\n   <th>AV</th>\n   <th>SPP</th>\n   <th>COST</th>\n   <th>Skills</th>\n   <th>Injuries</th>\n   <th>Changed?</th>\n </tr>\n </thead>\n");
				$is_first = 0;
			}
			print("<tr>\n");
			if ($p % 2) {
				print("  <tr>\n");
			}
			else {
				print("  <tr class=\"alternate\">\n");
			}

			//Determine if player has had an increase or injury
			if ("none" !== $pl->mp_inc) {
				$incmade = 1;
			}
			else if ("none" !== $pl->mp_inj) {
				$injmade = 1;
			}

			print("   <td><input type=\"hidden\" name=\"bblm_pid".$p."\" size=\"3\" value=\"".$pl->p_id."\"><input type=\"hidden\" name=\"bblm_mng".$p."\" size=\"3\" value=\"".$pl->p_mng."\">".$pl->p_num."</td>\n   <td>".$pl->p_name."</td>\n   <td><input type=\"text\" name=\"bblm_pma".$p."\" size=\"3\" value=\"".$pl->p_ma."\" maxlength=\"2\"></td>\n   <td><input type=\"text\" name=\"bblm_pst".$p."\" size=\"3\" value=\"".$pl->p_st."\" maxlength=\"2\"></td>\n   <td><input type=\"text\" name=\"bblm_pag".$p."\" size=\"3\" value=\"".$pl->p_ag."\" maxlength=\"2\"></td>\n   <td><input type=\"text\" name=\"bblm_pav".$p."\" size=\"3\" value=\"".$pl->p_av."\" maxlength=\"2\"></td>\n   <td><input type=\"text\" name=\"bblm_pspp".$p."\" size=\"3\" value=\"".$pl->p_spp."\" maxlength=\"2\"></td>\n   <td><input type=\"text\" name=\"bblm_pcost".$p."\" size=\"7\" value=\"".$pl->p_cost."\" maxlength=\"7\"");
			if ($incmade) {
				print(" style=\"background-color:#5EFB6E\"");
			}
			print("></td>\n   <td><input type=\"text\" name=\"bblm_pskills".$p."\" size=\"20\" value=\"".$pl->p_skills);
			if ($incmade) {
				print (", ".$pl->mp_inc."\" style=\"background-color:#5EFB6E");
			}
			print("\"></td>\n   <td><input type=\"text\" name=\"bblm_pinjuries".$p."\" size=\"20\" value=\"".$pl->p_injuries);
			if ($injmade) {
				print (", ".$pl->mp_inj."\" style=\"background-color:#5EFB6E");
			}
			print ("\"></td></td>\n  <td><input type=\"checkbox\" name=\"bblm_pcng".$p."\"");
			if ($incmade || $injmade) {
				print("  checked=\"checked\"");
			}
			print("></td>\n </tr>\n");

			$p++;
			$incmade = 0;
			$injmade = 0;
		}
		print("</table>\n");
	}
	else {
		print("<p><strong>It appears that no players actually took part in this match! I am no too sure what to suggest as we are pretty fucked at this point.</strong></p>");
	}


?>

<h3>Cost Reference</h3>

<table cellspacing="0" class="widefat" style="width:360px;">
<thead>
	<tr>
		<th>Cost</th>
		<th>Description</th>
	</tr>
</thead>
<tbody>
	<tr>
		<td>20,000</td>
		<td>New Skill</td>
	</tr>
	<tr class="alternate">
		<td>30,000</td>
		<td>Doube Skill</td>
	</tr>
	<tr>
		<td>30,000</td>
		<td>+MA or +AV</td>
	</tr>
	<tr class="alternate">
		<td>40,000</td>
		<td>+AG</td>
	</tr>
	<tr>
		<td>50,000</td>
		<td>+ST</td>
	</tr>
</tbody>
</table>


	<input type="hidden" name="bblm_numofplayers" size="2" value="<?php print($p-1); ?>">
	<p class="submit"><input type="submit" name="bblm_player_increase" tabindex="4" value="Submit These Details" title="Submit These Details "/ class="button-primary"/></p>
</form>

<?php
}



}

else if (isset($_POST['bblm_match_select'])) {

	  ///////////////////////////////////////////////////////////////////
	 // Step 2: Generate a list of payers based on the match selected //
	///////////////////////////////////////////////////////////////////

$matchsql2 = "SELECT M.m_id, UNIX_TIMESTAMP(M.m_date), M.m_teamA AS tAid, M.m_teamB AS tBid, T.t_name AS tA, Q.t_name AS tB, M.m_teamAtd, M.m_teamBtd, A.mt_cas AS tAcas, B.mt_cas AS tBcas, A.mt_int AS tAint, B.mt_int AS tBint, A.mt_comp AS tAcomp, B.mt_comp AS tBcomp, C.c_counts FROM ".$wpdb->prefix."match M, ".$wpdb->prefix."team T, ".$wpdb->prefix."team Q, ".$wpdb->prefix."comp C, ".$wpdb->prefix."match_team A, ".$wpdb->prefix."match_team B WHERE C.c_id = M.c_id AND M.m_teamA = T.t_id AND M.m_teamB = Q.t_id AND M.m_complete = 0 AND A.m_id = M.m_id AND A.t_id = M.m_teamA AND B.m_id = M.m_id AND B.t_id = M.m_teamB AND M.m_id = ".$_POST['bblm_mid'];
	if ($md = $wpdb->get_row($matchsql2)) {
?>
	<h3>Match Reference</h3>
	<table cellspacing="0" class="widefat" style="width:360px;">
		<thead>
		<tr>
			<th scope="col"><?php print($md->tA); ?></th>
			<th scope="col" class="column-comments">VS</th>
			<th scope="col"><?php print($md->tB); ?></th>
		</thead
		<tr>
			<th colspan="3">Date: <?php print(date("d.m.Y", $md->m_date)); ?></th>
		</tr>
		<tr class="alternate">
			<td><?php print($md->m_teamAtd); ?></td>
			<th class="column-comments">TD</th>
			<td><?php print($md->m_teamBtd); ?></td>
		</tr>
		<tr>
			<td><?php print($md->tAcas); ?></td>
			<th class="column-comments">CAS</th>
			<td><?php print($md->tBcas); ?></td>
		</tr>
		<tr class="alternate">
			<td><?php print($md->tAcomp); ?></td>
			<th class="column-comments">COMP</th>
			<td><?php print($md->tBcomp); ?></td>
			</tr>
		<tr>
			<td><?php print($md->tAint); ?></td>
			<th class="column-comments">INT</th>
			<td><?php print($md->tBint); ?></td>
			</tr>
	</table>
<?php
			$tAid = $md->tAid;
			$tBid = $md->tBid;
			$teamA = $md->tA;
			$teamB = $md->tB;
			$ccounts = $md->c_counts;
	}
?>

	<form name="bblm_recordparticipation" method="post" id="post">
	<input type="hidden" name="bblm_mid" size="3" value="<?php print($_POST['bblm_mid']); ?>">
	<input type="hidden" name="bblm_ccounts" size="3" value="<?php print($ccounts); ?>">
	<input type="hidden" name="bblm_teamA" size="3" value="<?php print($tAid); ?>">
	<input type="hidden" name="bblm_teamB" size="3" value="<?php print($tBid); ?>">

	<h3>Please Detail Participation</h3>
	<p>Below are all the players who where available to take part in this match. If they took part in this match please ensure that the &quot;Played?&quot; tickbox is selected.</p>
	<p>There will be a chance to record the actions of any Star Players at the bottom of the page.</p>


	<script type="text/javascript">
	function UpdateSPP(theId) {
		/*		Calcuate the players SPP		*/
		var tot_td = document.getElementById('bblm_td' + theId).value * 3;
		var tot_cas = document.getElementById('bblm_cas' + theId).value * 2;
		var tot_comp = document.getElementById('bblm_comp' + theId).value * 1;
		var tot_int = document.getElementById('bblm_int' + theId).value * 2;
		var tot_mvp = document.getElementById('bblm_mvp' + theId).value * 5;
		var tot_spp = tot_td + tot_cas + tot_comp + tot_int + tot_mvp;
		document.getElementById('bblm_spp'+ theId).value = tot_spp;

		/*		Highlight and fill Increase Box		*/
		var inc_col = "#5EFB6E"
		var old_spp = document.getElementById('bblm_oldspp' + theId).value;
		var new_SPP = Number(old_spp) + Number(tot_spp);
		if (((old_spp) <= 5) && new_SPP > 5) {
			document.getElementById('bblm_increase' + theId).style.backgroundColor = inc_col;
			document.getElementById('bblm_increase' + theId).value = "[Skill]";
		}
		else if (((old_spp) <= 15) && new_SPP > 15) {
			document.getElementById('bblm_increase' + theId).style.backgroundColor = inc_col;
			document.getElementById('bblm_increase' + theId).value = "[Skill]";
		}
		else if (((old_spp) <= 30) && new_SPP > 30) {
			document.getElementById('bblm_increase' + theId).style.backgroundColor = inc_col;
			document.getElementById('bblm_increase' + theId).value = "[Skill]";
		}
		else if (((old_spp) <= 50) && new_SPP > 50) {
			document.getElementById('bblm_increase' + theId).style.backgroundColor = inc_col;
			document.getElementById('bblm_increase' + theId).value = "[Skill]";
		}
		else if (((old_spp) <= 75) && new_SPP > 75) {
			document.getElementById('bblm_increase' + theId).style.backgroundColor = inc_col;
			document.getElementById('bblm_increase' + theId).value = "[Skill]";
		}
		else if (((old_spp) <= 150) && new_SPP > 175) {
			document.getElementById('bblm_increase' + theId).style.backgroundColor = inc_col;
			document.getElementById('bblm_increase' + theId).value = "[Skill]";
	}


	}
	</script>

	<?php
$playersql = 'SELECT P.p_id, P.t_id, P.p_spp, X.post_title AS p_name, P.p_num, T.t_name from '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' X where P.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = X.ID AND P.t_id = T.t_id AND (P.t_id = '.$tAid.' OR P.t_id = '.$tBid.') AND P.p_status = 1 AND P.p_mng = 0 ORDER BY P.t_id, P.p_num';
	if ($playerlist = $wpdb->get_results($playersql)) {
		//initiate var for count
		$p = 1;
		$is_first = 1;
		$current_team = "";

		foreach ($playerlist as $pl) {
			if ($pl->t_name !== $current_team) {
				$current_team = $pl->t_name;
				if (1 !== $is_first) {
					print("</table>\n");
				}
				$is_first = 1;
			}

			if ($is_first) {
				print("<h3>".$pl->t_name."</h3>\n<table cellspacing=\"0\" class=\"widefat\">\n <thead>\n <tr>\n   <th>#</th>\n   <th>Name</th>\n   <th>TD</th>\n   <th>COMP</th>\n   <th>CAS</th>\n   <th>INT</th>\n   <th>MVP</th>\n   <th>Gained SPP</th>\n      <th>Prev SPP</th>\n   <th>Played?</th>\n   <th>MNG?</th>\n   <th>Increase</th>\n   <th>Injury</th>\n  </tr>\n </thead>\n");
				$is_first = 0;
			}
			if ($p % 2) {
				print("  <tr>\n");
			}
			else {
				print("  <tr class=\"alternate\">\n");
			}

			print("   <td><input type=\"hidden\" name=\"bblm_tid".$p."\" id=\"bblm_tid".$p."\" size=\"3\" value=\"".$pl->t_id."\"><input type=\"hidden\" name=\"bblm_pid".$p."\" size=\"3\" value=\"".$pl->p_id."\">".$pl->p_num."</td>\n");
			print("   <td>".$pl->p_name."</td><td><input type=\"text\" name=\"bblm_td".$p."\" id=\"bblm_td".$p."\" size=\"3\" value=\"0\" maxlength=\"2\" onChange=\"UpdateSPP(".$p.")\"></td>\n");
			print("   <td><input type=\"text\" name=\"bblm_comp".$p."\" id=\"bblm_comp".$p."\" size=\"3\" value=\"0\" maxlength=\"2\" onChange=\"UpdateSPP(".$p.")\"></td>\n");
			print("   <td><input type=\"text\" name=\"bblm_cas".$p."\" id=\"bblm_cas".$p."\" size=\"3\" value=\"0\" maxlength=\"2\" onChange=\"UpdateSPP(".$p.")\"></td>\n");
			print("   <td><input type=\"text\" name=\"bblm_int".$p."\" id=\"bblm_int".$p."\" size=\"3\" value=\"0\" maxlength=\"2\" onChange=\"UpdateSPP(".$p.")\"></td>\n");
			print("   <td><input type=\"text\" name=\"bblm_mvp".$p."\" id=\"bblm_mvp".$p."\" size=\"3\" value=\"0\" maxlength=\"1\" onChange=\"UpdateSPP(".$p.")\"></td>\n");
			print("   <td style=\"background-color:#ddd;\"><input type=\"text\" name=\"bblm_spp".$p."\" id=\"bblm_spp".$p."\" size=\"3\" value=\"0\" maxlength=\"2\"></td>\n");
			print("   <td><input type=\"hidden\" name=\"bblm_oldspp".$p."\" id=\"bblm_oldspp".$p."\" size=\"3\" value=\"".$pl->p_spp."\">".$pl->p_spp."</td>\n");
			print("   <td><input type=\"checkbox\" name=\"bblm_plyd".$p."\" checked=\"checked\"></td>\n   <td><input type=\"checkbox\" name=\"mng".$p."\"></td>\n ");
			print("   <td><input type=\"text\" name=\"bblm_increase".$p."\" id=\"bblm_increase".$p."\" size=\"10\" value=\"\" maxlength=\"30\"></td>\n   <td><input type=\"text\" name=\"bblm_injury".$p."\" size=\"10\" value=\"\" maxlength=\"30\"></td>\n");
			print("  </tr>\n");

			$p++;
		}
		print("</table>\n");

		//Once the teams have been outputted - now come the Star Players

		//Determine the Star Player Team (to get the star players)
		$bblm_team_star = bblm_get_star_player_team();

		$starssql = 'SELECT P.post_name, X.p_id FROM '.$wpdb->prefix.'player X, '.$wpdb->prefix.'posts P, '.$wpdb->prefix.'bb2wp J WHERE J.prefix = \'p_\' AND J.pid = P.ID AND J.tid = X.p_id AND X.t_id = '.$bblm_team_star.' order by P.post_name ASC';
		if ($stars = $wpdb->get_results($starssql)) {
			$starlist = "";
			foreach ($stars as $star) {
				$starlist .= "<option value=\"".$star->p_id."\">".$star->post_name."</option>\n";
			}
			//create a drop down containing the two teams.
			$teamlist = "	<option value=\"".$tAid."\">".$teamA."</option>\n	<option value=\"".$tBid."\">".$teamB."</option>\n";

			$pmax = $p+4;
			print("	<h3>Star Players</h3>\n	<p>If Any Star Players took part in the match enter their details below and tick the played box.</p>\n	<table cellspacing=\"0\" class=\"widefat\">\n		<tr>\n			<th>Team</th>\n			<th>Star</th>\n			<th>TD</th>\n			<th>COMP</th>\n			<th>CAS</th>\n			<th>INT</th>\n			<th>MVP</th>\n			<th>SPP</th>\n			<th>Played?</th>\n			<th>Increase? (keep blank)</th>\n</tr>\n");
			while ($p < $pmax){

				if ($p % 2) {
					print("		<tr>\n");
				}
				else {
					print("		<tr class=\"alternate\">\n");
				}
				print("			<td><select id=\"bblm_tid".$p."\" name=\"bblm_tid".$p."\">".$teamlist."</select></td>\n");
				print("			<td><select id=\"bblm_pid".$p."\" name=\"bblm_pid".$p."\">".$starlist."</select></td>\n");
				print("			<td><input type=\"text\" name=\"bblm_td".$p."\" id=\"bblm_td".$p."\" size=\"3\" value=\"0\" maxlength=\"2\" onChange=\"UpdateSPP(".$p.")\"></td>\n");
				print("			<td><input type=\"text\" name=\"bblm_comp".$p."\" id=\"bblm_comp".$p."\" size=\"3\" value=\"0\" maxlength=\"2\" onChange=\"UpdateSPP(".$p.")\"></td>\n");
				print("			<td><input type=\"text\" name=\"bblm_cas".$p."\" id=\"bblm_cas".$p."\" size=\"3\" value=\"0\" maxlength=\"2\" onChange=\"UpdateSPP(".$p.")\"></td>\n");
				print("			<td><input type=\"text\" name=\"bblm_int".$p."\" id=\"bblm_int".$p."\" size=\"3\" value=\"0\" maxlength=\"2\" onChange=\"UpdateSPP(".$p.")\"></td>\n");
				print("			<td><input type=\"text\" name=\"bblm_mvp".$p."\" id=\"bblm_mvp".$p."\" size=\"3\" value=\"0\" maxlength=\"1\" onChange=\"UpdateSPP(".$p.")\"></td>\n");
				print("			<td style=\"background-color:#ddd;\"><input type=\"text\" name=\"bblm_spp".$p."\" id=\"bblm_spp".$p."\" size=\"3\" value=\"0\" maxlength=\"2\"></td>\n");
				print("			<td><input type=\"checkbox\" name=\"bblm_plyd".$p."\"></td>\n    <input type=\"hidden\" name=\"bblm_oldspp".$p."\" id=\"bblm_oldspp".$p."\" size=\"3\" value=\"0\">   <input type=\"hidden\" name=\"mng".$p."\" id=\"mng".$p."\" size=\"3\" value=\"0\">   <td>   <input type=\"text\" name=\"bblm_increase".$p."\" id=\"bblm_increase".$p."\" size=\"10\" value=\"\" maxlength=\"30\"></td>\n<input type=\"hidden\" name=\"bblm_injury".$p."\" size=\"10\" value=\"\" maxlength=\"30\">\n");
				print("		</tr>\n");

				$p++;
			}//end of while
			print("	</table>\n");

		}//end of if stars
	}
	else {
		print("<p><strong>These teams do not have any players registered with them! Please add some to the teams before you can continue.</strong></p>\n");
		$noplayers = 1;
	}
	?>
	<input type="hidden" name="bblm_numofplayers" size="2" value="<?php print($p-1); ?>">


<h3>Increase Reference</h3>

<table cellspacing="0" class="widefat" style="width:360px;">
<thead>
	<tr>
		<th>SPPs</th>
		<th>Title</th>
		<th>Increases</th>
	</tr>
</thead>
<tbody>
	<tr>
		<td>0 - 5</td>
		<td>Rookie</td>
		<td>None</td>
	</tr>
	<tr class="alternate">
		<td>6 - 15</td>
		<td>Experienced</td>
		<td>One</td>
	</tr>
	<tr>
		<td>16 - 30</td>
		<td>Veteran</td>
		<td>Two</td>
	</tr>
	<tr class="alternate">
		<td>31 - 50</td>
		<td>Emerging Star</td>
		<td>Three</td>
	</tr>
	<tr>
		<td>51 - 75</td>
		<td>Star</td>
		<td>Four</td>
	</tr>
	<tr class="alternate">
		<td>76 - 175</td>
		<td>Super Star</td>
		<td>Five</td>
	</tr>
	<tr>
		<td>175+</td>
		<td>Legend</td>
		<td>Six</td>
	</tr>
</tbody>
</table>

<h3>SPP Reference</h3>

<table cellspacing="0" class="widefat" style="width:360px;">
<thead>
	<tr>
		<th>Action</th>
		<th>SPP</th>
	</tr>
</thead>
<tbody>
	<tr>
		<td>Passing Completion</td>
		<td>1</td>
	</tr>
	<tr class="alternate">
		<td>Casuality</td>
		<td>2</td>
	</tr>
	<tr>
		<td>Interception</td>
		<td>2</td>
	</tr>
	<tr class="alternate">
		<td>TouchDown</td>
		<td>3</td>
	</tr>
	<tr>
		<td>Most Valued Player (MVP)</td>
		<td>5</td>
	</tr>
</tbody>
</table>

<?php
	if (1 !== $noplayers) {
?>
	<p class="submit"><input type="submit" name="bblm_player_actions" value="Submit These Details" title="Submit These Details" class="button-primary"/></p>
	</form>
<?php
	} // end of no players check


}//end of 2nd step

else {
	  ////////////////////////////////////////////
	 // Step 1: Select a match to be completed //
	////////////////////////////////////////////

	//no other form has been submitted so ask for a match to be selected
?>
	<form name="bblm_selectteam" method="post" id="post">

		<p>Below is a list of all the matches that have not yet had their player actions filled out. Please select one and press the continue button to complete the details for the match.</p>

		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="bblm_mid" >Match: </label></th>
				<td><select name="bblm_mid" id="bblm_mid">
	<?php
$matchsql = "SELECT M.m_id, M.m_date, T.t_name AS tA, Q.t_name AS tB, M.m_teamAtd, M.m_teamBtd, M.m_gate, P.guid, C.c_name FROM ".$wpdb->prefix."match M, ".$wpdb->prefix."bb2wp J, ".$wpdb->posts." P, ".$wpdb->prefix."team T, ".$wpdb->prefix."team Q, ".$wpdb->prefix."comp C WHERE C.c_id = M.c_id AND M.m_id = J.tid AND J.pid = P.ID AND J.prefix = 'm_' AND M.m_teamA = T.t_id AND M.m_teamB = Q.t_id AND M.m_complete = 0 ORDER BY m_date DESC, m_id DESC";
	if ($matches = $wpdb->get_results($matchsql)) {
		foreach ($matches as $match) {
			print("					<option value=\"$match->m_id\">".$match->c_name." - ".$match->tA." ".$match->m_teamAtd." vs ".$match->m_teamBtd." ".$match->tB."</option>\n");
		}
	}
	?>
				</select></td>
			</tr>
		</table>
		<p class="submit"><input type="submit" name="bblm_match_select" value="Enter Details" title="Select the above Match" class="button-primary"/></p>
	</form>

<?php
}//end of final else if

?>
</div>
