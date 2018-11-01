<?php
/**
 * BBowlLeagueMan Add Position
 *
 * Used to add a position to a Blood Bowl race
 *
 * @author 		Blacksnotling
 * @category 	Core
 * @package 	BBowlLeagueMan/Pages
 */

//Check the file is not being accessed directly
if (!function_exists('add_action')) die('You cannot run this file directly. Naughty Person');

?>
<div class="wrap">
	<h2>Add a Position</h2>
	<p>Use the following form to add a new Position to a Race.</p>

<?php



if (isset($_POST['bblm_position_add'])) {
/*	print("<pre>");
	print_r($_POST);
	print("</pre>");
*/

	$bblm_safe_input = array();

	//think about a for each
	if (get_magic_quotes_gpc()) {
		$_POST['bblm_pname'] = stripslashes($_POST['bblm_pname']);
		$_POST['bblm_pskills'] = stripslashes($_POST['pskills']);
	}
	$bblm_safe_input['pname'] = esc_sql( $_POST['bblm_pname'] );
	$bblm_safe_input['pskills'] = esc_sql( $_POST['bblm_pskills'] );

	//sanitise vars
	$bblm_race = $_POST['bblm_rid'];


$addsql = 'INSERT INTO `'.$wpdb->prefix.'position` (`pos_id`, `pos_name`, `r_id`, `pos_limit`, `pos_ma`, `pos_st`, `pos_ag`, `pos_av`, `pos_skills`, `pos_cost`, `pos_freebooter`, `pos_status`) VALUES (\'\', \''.$bblm_safe_input['pname'].'\', \''.$bblm_race.'\', \''.$_POST['bblm_plimit'].'\', \''.$_POST['bblm_pma'].'\', \''.$_POST['bblm_pst'].'\', \''.$_POST['bblm_pag'].'\', \''.$_POST['bblm_pav'].'\', \''.$bblm_safe_input['pskills'].'\', \''.$_POST['bblm_pcost'].'\', \'0\', \'1\')';



	if (FALSE !== $wpdb->query($addsql)) {
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
		print("Position was Added.");
	}
	else {
		print("Something went wrong");
	}
	?>
</p>
	</div>
<?php

} //end of submit if




if(isset($_POST['bblm_position_select'])) {
	//actual page content goes here.
/*	print("<h3>We have selected something?!</h3>");
	print("<pre>");
	print_r($_POST);
	print("</pre>"); */

$racesql = "SELECT r_name FROM '.$wpdb->prefix.'race WHERE r_id = ".$_POST['bblm_rname'];
if ($result = $wpdb->get_results($racesql)) {
	foreach ($result as $res) {
		$race_name = $res->r_name;
	}
}
?>
	<ul>
	  <li><strong>Race</strong>: <?php print($race_name); ?></li>
	</ul>

	<form name="bblm_editcompteam" method="post" id="post">
	<fieldset><legend>Position Details</legend>
	<input type="hidden" name="bblm_rid" size="3" value="<?php print($_POST['bblm_rname']); ?>">

		  <label for="bblm_pname" class="selectit">Position Name: </label>
		  <input type="text" name="bblm_pname" size="20" maxlength="20" tabindex="2" value="" id="bblm_pname">

		  <label for="bblm_plimit" class="selectit">limit: </label>
		  <input type="text" name="bblm_plimit" size="3" maxlength="2" tabindex="3" value="0" id="bblm_plimit">

		  <label for="bblm_pma" class="selectit">MA: </label>
		  <input type="text" name="bblm_pma" size="3" maxlength="2" tabindex="4" value="4" id="bblm_pma">

		  <label for="bblm_pst" class="selectit">ST: </label>
		  <input type="text" name="bblm_pst" size="3" maxlength="2" tabindex="5" value="4" id="bblm_pst">

		  <label for="bblm_pag" class="selectit">AG: </label>
		  <input type="text" name="bblm_pag" size="3" maxlength="2" tabindex="6" value="4" id="bblm_pag">

		  <label for="bblm_pav" class="selectit">AV: </label>
		  <input type="text" name="bblm_pav" size="3" maxlength="2" tabindex="7" value="4" id="bblm_pav">

		  <label for="bblm_pskills" class="selectit">Skills: </label><textarea name="pskills" cols="100" rows="3" tabindex="8">none</textarea>

		  <label for="bblm_pcost" class="selectit">Cost: </label>
		  <input type="text" name="bblm_pcost" size="7" maxlength="6" tabindex="9" value="50000" id="bblm_pcost">

	</fieldset>

	<p class="submit">
	<input type="submit" name="bblm_position_add" tabindex="10" value="Add Position to Race" title="Add position to Race"/>
	</p>


	</form>
<?php

}
else if (!isset($finished)) {
?>
	<form name="bblm_addposition" method="post" id="post">

	<p>Before we can begin, you must first select a Race to add the position to:</p>
	<fieldset id='addpositiondiv'><legend>Select a Race</legend>

	  <label for="bblm_ctcomp" class="selectit">Competition</label>
	  <select name="bblm_rname" id="bblm_rname">
	<?php
	$racesql = "SELECT R.r_id, R.r_name FROM ".$wpdb->prefix."race R, ".$wpdb->prefix."bb2wp J, ".$wpdb->posts." P WHERE R.r_id = J.tid AND J.pid = P.ID AND J.prefix = 'r_' ORDER BY R.r_name ASC";
	if ($races = $wpdb->get_results($racesql)) {
		foreach ($races as $race) {
			print("<option value=\"$race->r_id\">".$race->r_name."</option>\n");
		}
	}
	?>
	</select>



	</fieldset>
	<p class="submit">
	<input type="submit" name="bblm_position_select" tabindex="4" value="Select above Race" title="Select the above Race"/>
	</p>
	</form>
<?php
} //end of else section
?>

</div>
