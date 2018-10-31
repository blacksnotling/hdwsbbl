<?php
/**
 * BBowlLeagueMan Add Award
 *
 * Page used to add a new award to the league
 *
 * @author 		Blacksnotling
 * @category 	Core
 * @package 	BBowlLeagueMan/Pages
 */

//Check the file is not being accessed directly
if (!function_exists('add_action')) die('You cannot run this file directly. Naughty Person');

?>
<div class="wrap">
	<h2>Create an Award</h2>
	<p>Use the following form to create a new award for the league.</p>

<?php



if (isset($_POST['bblm_award_add'])) {
/*	print("<pre>");
	print_r($_POST);
	print("</pre>");
	print("<hr />"); */


	$bblm_safe_input = array();

	//think about a for each
	if (get_magic_quotes_gpc()) {
		$_POST['bblm_aname'] = stripslashes($_POST['bblm_aname']);
		$_POST['bblm_adesc'] = stripslashes($_POST['bblm_adesc']);
	}
	$bblm_safe_input['aname'] = esc_sql( $_POST['bblm_aname'] );
	$bblm_safe_input['adesc'] = esc_sql( $_POST['bblm_adesc'] );


$addsql = 'INSERT INTO `'.$wpdb->prefix.'awards` (`a_id`, `a_name`, `a_desc`, `a_cup`) VALUES (\'\', \''.$bblm_safe_input['aname'].'\', \''.$bblm_safe_input['adesc'].'\', \'0\')';

//print("<p>".$addsql."</p>");


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
		print("Award was Added.");
	}
	else {
		print("Something went wrong");
	}
	?>
</p>
	</div>
<?php

} //end of submit if


?>
	<form name="bblm_addaward" method="post" id="post">

	<p>Please enter the details of the award you wish to create:</p>

    <label for="bblm_aname" class="selectit">Award Name</label>
    <input type="text" name="bblm_aname" size="30" tabindex="1" value="" id="bblm_cname" maxlength="30">

	<fieldset id="postdivrich"><legend>Award Description</legend>
	<script type="text/javascript" src="../wp-includes/js/tinymce/tiny_mce.js"></script>
	<script type="text/javascript">
	<!--
		tinyMCE.init({
		theme : "advanced",
		mode : "exact",
		elements : "bblm_adesc",
		width : "565",
		height : "200"
		});
	-->
	</script>

	<div>
	  <textarea class='mceEditor' rows='10' cols='40' name='bblm_adesc' tabindex='3' id='bblm_adesc'></textarea>
	</div>
	</fieldset>


	<p class="submit">
	<input type="submit" name="bblm_award_add" tabindex="4" value="Create the award" title="Create the award"/>
	</p>
	</form>

</div>
