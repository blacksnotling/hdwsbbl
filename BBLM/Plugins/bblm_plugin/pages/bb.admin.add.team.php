<?php
/*
*	Filename: bb.admin.add.team.php
*	Description: This page is used to add a new team to the BBLM.
*/

//Check the file is not being accessed directly
if (!function_exists('add_action')) die('You cannot run this file directly. Naughty Person');
?>
<div class="wrap">
	<h2>Create a new Team</h2>

<?php
if(isset($_POST['bblm_team_submit'])) {

	//Determine the parent page
	$options = get_option('bblm_config');
	$bblm_page_parent = htmlspecialchars($options['page_team'], ENT_QUOTES);

	$my_post = array(
		'post_title' => wp_filter_nohtml_kses($_POST['bblm_tname']),
		'post_content' => wp_filter_kses($_POST['bblm_tdesc']),
		'post_type' => 'page',
		'post_status' => 'publish',
		'comment_status' => 'closed',
		'ping_status' => 'closed',
		'post_parent' => $bblm_page_parent
	);
	if ($bblm_submission = wp_insert_post( $my_post )) {
		add_post_meta($bblm_submission, '_wp_page_template', 'bb.view.team.php');

		//Determine permlink for this page
		$bblmpageguid = get_permalink($bblm_submission);

		$bblmdatasql = 'INSERT INTO `'.$wpdb->prefix.'team` (`t_id`, `t_name`, `r_id`, `ID`, `t_hcoach`, `t_ff`, `t_rr`, `t_apoc`, `t_cl`, `t_ac`, `t_bank`, `t_tv`, `t_active`, `t_show`, `type_id`, `t_sname`, `stad_id`, `t_img`, `t_roster`, `t_guid`) VALUES (\'\', \''.wp_filter_nohtml_kses($_POST['bblm_tname']).'\', \''.$_POST['bblm_trace'].'\', \''.$_POST['bblm_tuser'].'\', \''.$_POST['bblm_thcoach'].'\', \''.$_POST['bblm_tff'].'\', \''.$_POST['bblm_trr'].'\', \''.$_POST['bblm_tapoc'].'\', \''.$_POST['bblm_tcl'].'\', \''.$_POST['bblm_tac'].'\', \''.$_POST['bblm_tbank'].'\', \''.$_POST['bblm_ttv'].'\', \'1\', \'1\', \''.$_POST['bblm_ttype'].'\', \''.wp_filter_nohtml_kses($_POST['bblm_sname']).'\', \''.$_POST['bblm_tstad'].'\', \'\', \''.$_POST['bblm_roster'].'\', \''.$bblmpageguid.'\')';
		$wpdb->query($bblmdatasql);

		$team_id = $wpdb->insert_id;

		$bblmmappingsql = 'INSERT INTO `'.$wpdb->prefix.'bb2wp` (`bb2wp_id`, `tid`, `pid`, `prefix`) VALUES (\'\',\''.$team_id.'\', \''.$bblm_submission.'\', \'t_\')';
		$wpdb->query($bblmmappingsql);


		//Add a new Term to the database
		wp_insert_term(
			wp_filter_nohtml_kses($_POST['bblm_tname']), // the term
			'post_teams' // the taxonomy
		);

		//Check to see if  roster needs generating, if it does then insert an additionl page into the database
		if ($_POST['bblm_roster']) {
			$my_post = array(
				'post_title' => 'Roster',
				'post_content' => '',
				'post_type' => 'page',
				'post_status' => 'publish',
				'comment_status' => 'closed',
				'ping_status' => 'closed',
				'post_parent' => $bblm_submission
			);
			if ($bblm_submission = wp_insert_post( $my_post )) {
				add_post_meta($bblm_submission, '_wp_page_template', 'bb.view.roster.php');

				$bblmmappingsql = 'INSERT INTO `'.$wpdb->prefix.'bb2wp` (`bb2wp_id`, `tid`, `pid`, `prefix`) VALUES (\'\',\''.$team_id.'\', \''.$bblm_submission.'\', \'roster\')';
				$wpdb->query($bblmmappingsql);

				//correct the roster post title
				$my_post = array();
				$my_post['ID'] = $bblm_submission;
				$my_post['post_title'] = 'Roster - '.wp_filter_nohtml_kses($_POST['bblm_tname']);

				// Update the post into the database
				wp_update_post( $my_post );

				$roster_added = 1;
			}
		}

		$success = 1;
		$addattempt = 1;

	} //end of if post insertion was successful


?>
	<div id="updated" class="updated fade">
		<p>
	<?php
	if ($success) {
		print("Team has been created. <a href=\"".$bblmpageguid."\" title=\"View the new Team\">View page</a>.");
		if ($roster_added) {
			print(" A roster has also been added. <a href=\"".get_permalink($bblm_submission)."\" title=\"View the new Teams roster\">View Roster</a>");
		}
		print("</p>\n<p>You can now <a href=\"".site_url()."/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.add.player.php&action=add&item=none&id=".$team_id."\" title=\"Add some players to this team\">add players to this team</a> or <a href=\"".site_url()."/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.add.team.php\" title=\"Add another new team\">add another new team</a>.</p>");
	}
	else {
		print("Something went wrong! Please try again.");
	}


?>
		</p>
	</div>
<?php
//end of submit if
}
else if(isset($_POST['bblm_race_select'])) {
	  ///////////////////////////////
	 // Begin Output of main form //
	///////////////////////////////
?>

	<form name="bblm_addteam" method="post" id="post">

	<table class="form-table">
	<tr valign="top">
		<th scope="row" valign="top"><label for="bblm_tname">Team Name</label></th>
		<td><input type="text" name="bblm_tname" size="50" value="" id="bblm_tname" maxlength="50" class="large-text"/></td>
	</tr>
	<tr valign="top">
		<th scope="row"><label for="bblm_tdesc">Bio</label></th>
		<td><p><textarea rows="10" cols="50" name="bblm_tdesc" id="bblm_tdesc" class="large-text"></textarea></p></td>
	</tr>
	<tr valign="top">
		<th scope="row" valign="top"><label for="bblm_sname">Short Name</label></th>
		<td><input type="text" name="bblm_sname" size="3" value="" id="bblm_sname" maxlength="5" class="small-text"/><br />
		This will be displayed on various reports and pages.</td>
	</tr>
	<tr valign="top">
		<th scope="row" valign="top"><label for="bblm_tuser">Coach</label></th>
		<td><select name="bblm_tuser" id="bblm_tuser">
<?php
		$usersql = 'SELECT ID, display_name FROM '.$wpdb->users.' order by display_name';
		if ($users = $wpdb->get_results($usersql)) {
			foreach ($users as $user) {
				print("<option value=\"".$user->ID."\">".$user->display_name."</option>\n");
			}
		}
?>
		</select><br />
		Forgotten? - <a href="<?php bloginfo('url');?>/wp-admin/user-new.php" title="Add a new user now">Add  new user to the site!</a> - You will have to reload this page after adding a new one.</td>
	</tr>
	<tr valign="top">
		<th scope="row" valign="top"><label for="bblm_thcoach">Head Coach</label></th>
		<td><input type="text" name="bblm_thcoach" size="25" value="Unkown" maxlength="25" class="large-text"/></td>
	</tr>
	<tr valign="top">
		<th scope="row" valign="top"><label for="bblm_tstad">Home Stadium</label></th>
		<td><select name="bblm_tstad" id="bblm_tstad">
<?php
		$stadsql = 'SELECT S.* FROM '.$wpdb->prefix.'stadium S, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE S.stad_id = J.tid AND J.prefix = \'stad_\' AND J.pid = P.ID ORDER BY S.stad_name';
		if ($stadiums = $wpdb->get_results($stadsql)) {
			foreach ($stadiums as $stad) {
				print("<option value=\"".$stad->stad_id."\">".$stad->stad_name."</option>\n");
			}
		}
?>
		</select><br />
		Forgotten? - <a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.add.stadium.php" title="Add a new Stadium now">Add  new Stadium to the site!</a> - You will have to reload this page after adding a new one.</td>
	</tr>
	<tr valign="top">
		<th scope="row" valign="top"><label for="bblm_roster">Generate Roster?</label></th>
		<td><select name="bblm_roster" id="bblm_roster">
			<option value="1">Yes</option>
			<option value="0">No</option>
		</select></td>
	</tr>
	<tr valign="top">
		<th scope="row" valign="top"><label for="bblm_ttype">Team Type</label></th>
		  <td><select name="bblm_ttype" id="bblm_tuser">
<?php
		$typesql = 'SELECT type_id, type_name FROM '.$wpdb->prefix.'team_type ORDER BY type_id';
		if ($types = $wpdb->get_results($typesql)) {
			foreach ($types as $type) {
				print("<option value=\"$type->type_id\">".$type->type_name."</option>\n");
			}
		}
?>
		</select></td>
	</tr>

	</table


	<h3>Initial Purchases</h3>

<script type="text/javascript">
function UpdateBankTv() {
	var tot_rr = document.getElementById('bblm_trr').value * document.getElementById('bblm_trrcost').value;

	var tot_ff = document.getElementById('bblm_tff').value * 10000;

	var tot_cl = document.getElementById('bblm_tcl').value * 10000;

	var tot_ac = document.getElementById('bblm_tac').value * 10000;

	var tot_apoc = document.getElementById('bblm_tapoc').value * 50000;

	var tot_tv = tot_rr + tot_ff + tot_cl + tot_ac + tot_apoc;
	document.getElementById('bblm_ttv').value = tot_tv;

	var tot_bank = 1000000 - tot_tv;
	document.getElementById('bblm_tbank').value = tot_bank;
}
</script>

<?php
		$racesql = "SELECT r_id, r_name, r_rrcost FROM ".$wpdb->prefix."race WHERE r_id = ".$_POST['bblm_rid'];
		if ($races = $wpdb->get_results($racesql)) {
			foreach ($races as $race) {
				$rid = $race->r_id;
				$rrcost = $race->r_rrcost;
			}
		}
?>

	<table class="form-table">
	<tr valign="top">
		<th scope="row" valign="top"><label for="bblm_trr">Re-Rolls</label></th>
		<td><input type="text" name="bblm_trr" size="2" value="0" maxlength="1" id="bblm_trr" class="small-text"/><br />
		@ <?php print($rrcost); ?> each</td>
	</tr>
	<tr valign="top">
		<th scope="row" valign="top"><label for="bblm_tff">Fan Factor</label></th>
		  <td><input type="text" name="bblm_tff" size="2" value="0" maxlength="2" id="bblm_tff" class="small-text"/><br />
		  @ 10,000 each</td>
	</tr>
	<tr valign="top">
		<th scope="row" valign="top"><label for="bblm_tcl">Cheerleaders</label></th>
		  <td><input type="text" name="bblm_tcl" size="2" value="0" maxlength="2" id="bblm_tcl" class="small-text"/><br />
		  @ 10,000 each</td>
	</tr>
	<tr valign="top">
		<th scope="row" valign="top"><label for="bblm_tac">Assistant Coaches</label></th>
		  <td><input type="text" name="bblm_tac" size="2" value="0" maxlength="2" id="bblm_tac" class="small-text"/><br />
		  @ 10,000 each</td>
	</tr>
	<tr valign="top">
		<th scope="row" valign="top"><label for="bblm_tapoc">Apothecary</label></th>
		  <td><input type="text" name="bblm_tapoc" size="1" value="0" maxlength="1" id="bblm_tapoc" class="small-text"/><br />
		  @ 10,000 each</td>
	</tr>
	<tr valign="top">
		<th scope="row" valign="top">&nbsp;</th>
		  <td><input type="button" value="Update Bank + TV" onClick="UpdateBankTv();"/></td>
	</tr>

	<tr valign="top">
		<th scope="row" valign="top"><label for="bblm_tbank">Remaining Bank</label></th>
		  <td><input type="text" name="bblm_tbank" size="7" value="1000000" maxlength="7" id="bblm_tbank"/>gp</td>
	</tr>
	<tr valign="top">
		<th scope="row" valign="top"><label for="bblm_ttv">Team Value (initial)</label></th>
		  <td><input type="text" name="bblm_ttv" size="7" value="0" maxlength="7" id="bblm_ttv"/>gp</td>
	</tr>
	</table>

	<input type="hidden" name="bblm_trace" size="3" value="<?php print($rid); ?>"/>
	<input type="hidden" name="bblm_trrcost" id="bblm_trrcost" maxlength="6" value="<?php print($rrcost); ?>"/>

	<p class="submit"><input type="submit" name="bblm_team_submit" value="Create Team" title="Add the Team" class="button-primary"/></p>
</form>

<?php
}//end of elseIF
else {
?>
	<form name="bblm_addposition" method="post" id="post">

	<p>The following page is used to add a new team to the league.</p>
	<p>Before you continue, please ensure that you have created a user ID for the coach and set up a stadium.</p>
	<p>Before you can begin creating the new team, you must first select the Race of the new team:</p>


	<table class="form-table">
	<tr valign="top">
		<th scope="row"><label for="bblm_rid">Race</label></th>
		<td><select name="bblm_rid" id="bblm_rid">
<?php
		$racesql = "SELECT R.r_id, R.r_name FROM ".$wpdb->prefix."race R, ".$wpdb->prefix."bb2wp J, ".$wpdb->posts." P WHERE R.r_id = J.tid AND J.pid = P.ID AND J.prefix = 'r_' ORDER BY R.r_name ASC";
			if ($races = $wpdb->get_results($racesql)) {
				foreach ($races as $race) {
					print("			<option value=\"$race->r_id\">".$race->r_name."</option>\n");
				}
			}
?>
		</select></td>
	</tr>
	</table>

	<p class="submit"><input type="submit" name="bblm_race_select" value="Select above Race" title="Select the above Race" class="button-primary"/></p>
	</form>
<?php
} //end of else section
?>
</div>