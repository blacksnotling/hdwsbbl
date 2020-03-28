<?php
/**
 * BBowlLeagueMan Add Player
 *
 * Page used to add a new player to an existing team
 *
 * @author 		Blacksnotling
 * @category 	Core
 * @package 	BBowlLeagueMan/Pages
 */

//Check the file is not being accessed directly
if (!function_exists('add_action')) die('You cannot run this file directly. Naughty Person');

?>
<div class="wrap">
	<h2>Add a Player</h2>
	<p>Use the following form to add a new Player to an existing team.</p>

<?php
if (isset($_POST['bblm_team_add'])) {

	$bblm_safe_input = array();

	$bblm_safe_input['pname'] = wp_filter_nohtml_kses($_POST['bblm_pname']);

	if ($_POST['bblm_free'] > 0) {
		//Player is a JM or a merc and so does not cost the team anything!
		if ("2" == $_POST['bblm_free']) {
			$freebooter_type = "merc";
		}
		else {
			$freebooter_type = "jm";
		}
		$freebooter = 1;
	}
	else {
		//Player is payed for
		$freebooter = 0;
	}

	/*
	Gather information about selected team, race and positions
	*/
	$postdetailssql = "SELECT P.ID, P.guid, T.t_name FROM ".$wpdb->prefix."team T, ".$wpdb->prefix."bb2wp J, ".$wpdb->posts." P WHERE T.t_id = J.tid AND J.pid = P.ID AND J.prefix = 't_' AND T.t_id = ".$_POST['bblm_tid'];
	if ($pdetails = $wpdb->get_results($postdetailssql)) {
		foreach ($pdetails as $pd) {
			$bblm_pid = $pd->ID;
			$bblm_guid = $pd->guid;
			$bblm_tname = $pd->t_name;
		}
	}

	//call the options from the table
	$options = get_option('bblm_config');
	$merc_pos = htmlspecialchars($options['player_merc'], ENT_QUOTES);


	$posnamesql = "SELECT * FROM ".$wpdb->prefix."position ";
	if ($freebooter) {
		if ("jm" == $freebooter_type) {
			$posnamesql .= "WHERE pos_freebooter = 1 AND r_id = ".$_POST['bblm_rid'];
		}
		else {
			//we are looking for the specific merc position that was selected
			$posnamesql .= "WHERE pos_id = ".$_POST['bblm_pos'];
		}
	}
	else {
		$posnamesql .= "WHERE pos_id = ".$_POST['bblm_pos'];
	}


	if ($posdetails = $wpdb->get_results($posnamesql)) {
		foreach ($posdetails as $posd) {
			$bblm_posid = $posd->pos_id;
			$bblm_posname = $posd->pos_name;
			$bblm_posma = $posd->pos_ma;
			$bblm_posst = $posd->pos_st;
			$bblm_posag = $posd->pos_ag;
			$bblm_posav = $posd->pos_av;
			$bblm_posskills = $posd->pos_skills;
			$bblm_poscost = $posd->pos_cost;
		}
	}
	if ($freebooter) {
		if ("jm" == $freebooter_type) {
			$bblm_posid = 1;
		}
		else {
			//we have a mer so we need to verride the skills and cost parts
			$bblm_posid = $merc_pos;
			$bblm_posskills = $_POST['bblm_mskills'];
			$bblm_poscost = $_POST['bblm_mcost'];
		}
	}

	/*
	Begin Generation of Wp page information
	*/

	//filter page body
	$bblm_page_content = "&quot;".$bblm_safe_input['pname']."&quot; is a ";
	if ($freebooter) {
		if ("jm" == $freebooter_type) {
			$bblm_page_content .= "Journeyman";
		}
		else {
			$bblm_page_content .= "Mercenary ".$posd->pos_name;;
		}
	}
	else {
		$bblm_page_content .= $bblm_posname;
	}
	$bblm_page_content .= " for ".$bblm_tname." playing as number ".$_POST['bblm_pnum'].".";
	$bblm_page_content = esc_sql( $bblm_page_content );

	//snaitise page title
	$bblm_page_title = $bblm_safe_input['pname'];

	//convert page title to slug
	$bblm_page_slug = sanitize_title($bblm_page_title);

	//generate GUID
	$bblm_guid = $bblm_guid."/";
	$bblm_guid .= $bblm_page_slug;

	/*
	Start of SQL Generation
	*/

	$teamupdatesql = 'UPDATE `'.$wpdb->prefix.'team` SET `t_tv` = t_tv+\''.$bblm_poscost.'\'';

	if (0 == $freebooter) {
		$teamupdatesql .= ', `t_bank` = t_bank-\''.$bblm_poscost.'\' ';
	}
        $teamupdatesql .= ' WHERE `t_id` = '.$_POST['bblm_tid'].' LIMIT 1';

	$my_post = array(
		'post_title' => wp_filter_nohtml_kses($bblm_page_title),
		'post_content' => $bblm_page_content,
		'post_type' => 'page',
		'post_status' => 'publish',
		'comment_status' => 'closed',
		'ping_status' => 'closed',
		'post_parent' => $bblm_pid
	);
	if ($bblm_submission = wp_insert_post( $my_post )) {
		add_post_meta($bblm_submission, '_wp_page_template', BBLM_TEMPLATE_PATH . 'single-bblm_player.php');

		$playersql = 'INSERT INTO `'.$wpdb->prefix.'player` (`p_id`, `t_id`, `pos_id`, `p_name`, `p_num`, `p_ma`, `p_st`, `p_ag`, `p_av`, `p_spp`, `p_skills`, `p_mng`, `p_injuries`, `p_cost`, `p_cost_ng`, `p_status`, `p_img`, `p_former`, `WPID`) VALUES (\'\', \''.$_POST['bblm_tid'].'\', \''.$bblm_posid.'\', \''.$bblm_page_title.'\', \''.$_POST['bblm_pnum'].'\', \''.$bblm_posma.'\', \''.$bblm_posst.'\', \''.$bblm_posag.'\', \''.$bblm_posav.'\', \'0\', \''.$bblm_posskills.'\', \'0\', \'none\', \''.$bblm_poscost.'\', \''.$bblm_poscost.'\', \'1\', \'\', \'0\', \''.$bblm_submission.'\')';

		//Insert into the Player table
		$wpdb->query($playersql);

		$bblmmappingsql = 'INSERT INTO `'.$wpdb->prefix.'bb2wp` (`bb2wp_id`, `tid`, `pid`, `prefix`) VALUES (\'\',\''.$wpdb->insert_id.'\', \''.$bblm_submission.'\', \'p_\')';
		$wpdb->query($bblmmappingsql);

		//Update the team
		$wpdb->query($teamupdatesql);

		$success = 1;
		$addattempt = 1;
		do_action( 'bblm_post_submission' );

	}


?>
	<div id="updated" class="updated fade">
	<p>
	<?php
	if ($success) {
		print("Player has been added. <a href=\"".get_permalink($bblm_submission)."\" title=\"View the new Player\">View page</a>");
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
else if ((isset($_POST['bblm_team_select'])) || ("add" == $_GET['action'])) {
  ////////////////////////////////////////
 // Team Selected - add player details //
////////////////////////////////////////
	//actual page content goes here.
/*	print("<pre>");
	print_r($_POST);
	print("</pre>"); */

	if (isset($_POST['bblm_team_select'])) {
		$tid = $_POST['bblm_tname'];
	}
	else {
		$tid = $_GET['id'];
	}


	//Select Team Details
	$teamsql = "SELECT t_id, t_name, t_bank, t_tv, r_id FROM ".$wpdb->prefix."team WHERE t_id = ".$tid;
	if ($result = $wpdb->get_results($teamsql)) {
		foreach ($result as $res) {
			$team_name = $res->t_name;
			$team_id = $res->t_id;
			$team_bank = $res->t_bank;
			$team_tv = $res->t_tv;
			$team_race = $res->r_id;
		}
	}

	//Select number of players on the team
	$numplayerssql = "SELECT COUNT(*) AS numplay FROM ".$wpdb->prefix."player WHERE p_status = 1 AND t_id = ".$team_id;

	$player_count = $wpdb->get_var($numplayerssql);

	//caluculate available "shirt" numbers on the team
	//Create array of numbers 1 through 16
	$num = Array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16);
	//Sql to determin position numbers in user
	$usedsql = 'SELECT DISTINCT p_num AS used from '.$wpdb->prefix.'player WHERE t_id = '.$team_id.' AND p_status = "1" ORDER BY used ASC ';
	if ($unused = $wpdb->get_results($usedsql, ARRAY_A)) {
		foreach ($unused as $un) {
			//checks to see if a number in the array is already being used.
			if (in_array($un['used'], $num)) {
				//remove the used number from the array
				$num[$un['used']-1] = 0;
			}
		}
	}
	//remove any blank entries from array
	$num = array_filter($num);

?>
	<ul>
	  <li><strong>Team</strong>: <?php print($team_name); ?></li>
	  <li><strong>Bank</strong>: <?php print(number_format($team_bank)); ?> gp</li>
	  <li><strong>Current TV</strong>: <?php print(number_format($team_tv)); ?> gp</li>
	  <li><strong>Number of</strong> (available) <strong>players</strong>: <?php print($player_count); ?></li>
	</ul>

	<form name="bblm_addplayer" method="post" id="post">
	<input type="hidden" name="bblm_tid" size="3" value="<?php print($team_id); ?>">
	<input type="hidden" name="bblm_rid" size="3" value="<?php print($team_race); ?>">
	<input type="hidden" name="bblm_bank" size="3" value="<?php print($team_bank); ?>">

	<h3>Key Details</h3>

	<label for="bblm_pname" class="selectit">Player Name</label>
	<input type="text" name="bblm_pname" size="25" tabindex="1" value="" maxlength="30">

	<label for="bblm_pnum" class="selectit">Position Number</label>
	  <select name="bblm_pnum" id="bblm_pnum">
	<?php

		foreach ($num as $value) {
			print("<option value=\"$value\">".$value."</option>\n");
		}

	?>
	</select>

<table class="form-table">
	<tr>
		<th scope="row">Player Type</th>
		<td>
			<fieldset><legend class="screen-reader-text"><span>Player Type</span></legend>
			<label title='JM'><input type="radio" value="1" name="bblm_free"> <strong>Journeyman</strong> - You are done, hit submit!</label><br />
			<label title='Norm'><input type="radio" value="0" name="bblm_free" checked="yes"> <strong>Normal Permanent Player</strong> - Please fill out section (1) below</label><br />
			<label title='Merc'><input type="radio" value="2" name="bblm_free"> <strong>Mercenary</strong> - Please fill out sections (1) and (2) below</label><br />
			</fieldset>
		</td>
	</tr>
</table>


<?php
$positionsql = 'SELECT * FROM '.$wpdb->prefix.'position WHERE r_id = '.$team_race.' AND pos_status = 1 ORDER by pos_limit DESC';
	if ($positions = $wpdb->get_results($positionsql)) {
		//copy for later. should be replaced with a reset object or something.
		$positions2 = $positions;
?>
<h3>Positions available</h3>
<p>Positions marked with a <strong>*</strong> means that there is not enough money in the bank to add one permanently.<br/>
<strong>NOTE</strong>: The system will <strong>NOT</strong> stop you from continuing, it will simply put the bank value in negative numbers!</p>
<table cellspacing="0" class="widefat">
<thead>
	<tr>
		<th>Name</th>
		<th>Limit</th>
		<th>MA</th>
		<th>ST</th>
		<th>AG</th>
		<th>AV</th>
		<th>Skills</th>
		<th>Cost</th>
	</tr>
</thead>
<tbody>
<?php
	foreach ($positions as $pos) {
?>
	<tr>
		<td><?php print($pos->pos_name); if ($pos->pos_cost > $team_bank) { print(" <strong>*</strong>"); } ?></td>
		<td>0 - <?php print($pos->pos_limit); ?></td>
		<td><?php print($pos->pos_ma); ?></td>
		<td><?php print($pos->pos_st); ?></td>
		<td><?php print($pos->pos_ag); ?></td>
		<td><?php print($pos->pos_av); ?></td>
		<td><?php print($pos->pos_skills); ?></td>
		<td><?php if ($pos->pos_cost > $team_bank) { print(number_format($pos->pos_cost)."<strong>*</strong>"); } else { print(number_format($pos->pos_cost)); } ?> gp</td>
	</tr>
<?php
		}
		print("</tbody>\n</table>");
	}

?>

	<h3>Section (1) - For Permanent Players and Mercenaries</h3>
	  <label for="bblm_pos" class="selectit">Select Position</label>
	  <select name="bblm_pos" id="bblm_pos">
	<?php

	foreach ($positions2 as $po) {
		print("<option value=\"$po->pos_id\">".$po->pos_name."</option>\n");
	}
	?>
	</select>


	<h3>Section (2) - For Mercenaries</h3>
	<p>All Mercenaries have the Loner Skill. They may take 1 additional skill choice.<br />
	Cost is equal to the cost of the base player + 30,000 + 50,000 if a skill is taken</p>

	<label for="bblm_mskills">Skills</label>
	<input type="text" name="bblm_mskills" size="25" value="Loner" maxlength="100">

	<label for="bblm_mcost">Player Value</label>
	<input type="text" name="bblm_mcost" size="10" value="30000" maxlength="6">

	<p class="submit">
	<input type="submit" name="bblm_team_add" tabindex="4" value="Add the player to the team" title="Ass the player to the team"/>
	</p>
	</form>

<?php
}
else if (!isset($finished)) {
?>
	<form name="bblm_addplayer" method="post" id="post">

	<p>Before we can begin, you must first select a Team to add the player to:</p>

	<table class="form-table">
	<tr class="form-field form-required">
		<th scope="row" valign="top"><label for="bblm_tname">Team</label></th>
		<td><select name="bblm_tname" id="bblm_tname">
	<?php

$teamsql = 'SELECT T.t_id, T.t_name FROM '.$wpdb->prefix.'team T, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE T.t_id = J.tid AND J.prefix = \'t_\' AND J.pid = P.ID AND T.t_active = 1 AND T.t_show = 1 ORDER BY T.t_name ASC LIMIT 0, 30 ';
	if ($teams = $wpdb->get_results($teamsql)) {
		foreach ($teams as $team) {
			print("<option value=\"$team->t_id\">".$team->t_name."</option>\n");
		}
	}
	?>
	</select></td>
		</tr>
	</table>


	<p class="submit">
		<input type="submit" name="bblm_team_select" tabindex="4" value="Select above Team" title="Select the above Team"/>
	</p>
	</form>
<?php
} //end of else section
?>
</div>
