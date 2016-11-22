<?php
/*
*	Filename: bb.admin.edit.comp_team.php
*	Version: 1.1
*	Description: Page used to manage teams assigned to a league.
*/
/* -- Change History --
20080311 - 1.0b - Initial creation of file.
20080730 - 1.0 - bump to Version 1 for public release.
20100308 - 1.1 - Updated the prefix for the custom bb tables in the Database (tracker [224])

*/


//Check the file is not being accessed directly
if (!function_exists('add_action')) die('You cannot run this file directly. Naughty Person');
?>
<div class="wrap">
	<h2>Manage teams in a Competition</h2>
	<p>Use the following page to assign and remove teams from competitions.</p>

<?php
if ((isset($_POST['bblm_teamcomp_add'])) || (isset($_POST['bblm_teamcomp_remove']))) {
/*	print("<pre>");
	print_r($_POST);
	print("</pre>"); */

	//sanitise vars
	$bblm_comp = $_POST['bblm_comp'];
	$bblm_div = $_POST['bblm_div'];
	$bblm_teams = $_POST['bblm_team'];
}

if (isset($_POST['bblm_teamcomp_add'])) {

	foreach ($bblm_teams as $team) {

$addsql = 'INSERT INTO `'.$wpdb->prefix.'team_comp` (`tc_id`, `t_id`, `c_id`, `div_id`, `tc_played`, `tc_W`, `tc_L`, `tc_D`, `tc_tdfor`, `tc_tdagst`, `tc_casfor`, `tc_casagst`, `tc_int`, `tc_comp`, `tc_points`) VALUES (\'\', \''.$team.'\', \''.$bblm_comp.'\', \''.$bblm_div.'\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\')';

	if (FALSE !== $wpdb->query($addsql)) {
		$sucess = TRUE;
	}
	else {
		$wpdb->print_error();
	}


	}//end of for each

	//set a flag so form doesn't repeat at the bottom
	$finished = 1;
?>
	<div id="updated" class="updated fade">
	<p>
	<?php
	if ($sucess) {
		print("Competition Updated.");
	}
	else {
		print("Something went wrong");
	}
	?>
</p>
	</div>
<?php

} //end of submit if

if (isset($_POST['bblm_teamcomp_remove'])) {

	foreach ($bblm_teams as $team) {

$removesql = "DELETE FROM ".$wpdb->prefix."team_comp WHERE t_id = ".$team." AND c_id = ".$bblm_comp." AND div_id = ".$bblm_div;

	if (FALSE !== $wpdb->query($removesql)) {
		$sucess = TRUE;
	}
	else {
		$wpdb->print_error();
	}


	}//end of for each

	//set a flag so form doesn't repeat at the bottom
	$finished = 1;
?>
	<div id="updated" class="updated fade">
	<p>
	<?php
	if ($sucess) {
		print("Competition Updated.");
	}
	else {
		print("Something went wrong");
	}
	?>
</p>
	</div>
<?php

} //end of submit if


if(isset($_POST['bblm_teamcomp_select'])) {
	//actual page content goes here.
/*	print("<h3>We have selected something?!</h3>");
	print("<pre>");
	print_r($_POST);
	print("</pre>"); */
?>

<?php
$compsql = "SELECT c_name FROM ".$wpdb->prefix."comp WHERE c_id = ".$_POST['bblm_ctcomp'];
if ($result = $wpdb->get_results($compsql)) {
	foreach ($result as $res) {
		$comp_name = $res->c_name;
	}
}
$divsql = "SELECT div_name FROM ".$wpdb->prefix."division WHERE div_id = ".$_POST['bblm_ctdiv'];
if ($result = $wpdb->get_results($divsql)) {
	foreach ($result as $res) {
		$div_name = $res->div_name;
	}
}
?>
	<ul>
	  <li><strong>Competition</strong>: <?php print($comp_name); ?></li>
	  <li><strong>Division</strong>: <?php print($div_name); ?></li>
	</ul>

	<?php
	$existingteamssql = "SELECT T.t_name FROM ".$wpdb->prefix."team T, ".$wpdb->prefix."team_comp C WHERE T.t_id = C.t_id AND C.c_id = ".$_POST['bblm_ctcomp']." AND C.div_id = ".$_POST['bblm_ctdiv'];
	if ($extteam = $wpdb->get_results($existingteamssql)) {
		print("<p>The Following teams are already in this competition:</p>\n<ul>\n");
		foreach ($extteam as $et) {
			print ("<li>".$et->t_name."</li>\n");
		}
		print("</ul>\n");
	}
	else {
		print("<p>There are currently no teams in this competition!</p>");
	}
	?>

	<form name="bblm_editcompteam" method="post" id="post">
	<input type="hidden" name="bblm_comp" size="3" value="<?php print($_POST['bblm_ctcomp']); ?>">
	<input type="hidden" name="bblm_div" size="2" value="<?php print($_POST['bblm_ctdiv']); ?>">

	<fieldset><legend>Add another team to this competition</legend>
	<?php
	$teamsql = "SELECT t_id, t_name FROM ".$wpdb->prefix."team WHERE t_active = 1 ORDER BY t_name ASC";
	if ($teams = $wpdb->get_results($teamsql)) {
		print("<p><ul>\n");
		foreach ($teams as $team) {
			print ("<li><input type=\"checkbox\" value=\"".$team->t_id."\" name=\"bblm_team[]\"> ".$team->t_name."</li>\n");
		}
		print("</ul>\n");
	}
	?>
	<p>Action for selected Teams:</p>
	</fieldset>

	<p class="submit">
	<input type="submit" name="bblm_teamcomp_add" tabindex="4" value="Add to Competition" title="Add to Competition"/>
	</p>

	<p class="submit">
	<input type="submit" name="bblm_teamcomp_remove" tabindex="5" value="Remove from Competition" title="Remove from Competition"/>
	</p>

	</form>
<?php

}
else if (!isset($finished)) {
?>
	<form name="bblm_editcompteam" method="post" id="post">

	<p>Before we can begin, you must first select a competition and a division that these teams will be in:</p>
	<fieldset id='editcompteamdiv'><legend>Select a Competition</legend>

	  <label for="bblm_ctcomp" class="selectit">Competition</label>
	  <select name="bblm_ctcomp" id="bblm_ctcomp">
	<?php
	$compsql = 'SELECT c_id, c_name FROM '.$wpdb->prefix.'comp WHERE c_active = 1 order by c_name';
	if ($comps = $wpdb->get_results($compsql)) {
		foreach ($comps as $comp) {
			print("<option value=\"$comp->c_id\">".$comp->c_name."</option>\n");
		}
	}
	?>
	</select>

	  <label for="bblm_ctdiv" class="selectit">Division</label>
	  <select name="bblm_ctdiv" id="bblm_ctdiv">
	<?php
	$divsql = 'SELECT div_id, div_name FROM '.$wpdb->prefix.'division ORDER BY div_id';
	if ($divs = $wpdb->get_results($divsql)) {
		foreach ($divs as $div) {
			print("<option value=\"$div->div_id\">".$div->div_name."</option>\n");
		}
	}
	?>
	</select>

	</fieldset>
	<p class="submit">
	<input type="submit" name="bblm_teamcomp_select" tabindex="4" value="Select above Competition" title="Select the above Competition"/>
	</p>
	</form>
<?php
} //end of else section
?>

</div>