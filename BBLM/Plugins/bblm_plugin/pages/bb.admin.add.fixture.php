<?php
/**
 * BBowlLeagueMan page to add Fixtures
 *
 * Allows an admin to set new fixtures for specific competiions and devisions.
 *
 * @author 		Blacksnotling
 * @category 	Core
 * @package 	BBowlLeagueMan/Pages
 */

//Check the file is not being accessed directly
if (!function_exists('add_action')) die('You cannot run this file directly. Naughty Person');

?>
<div class="wrap">
<?php
	echo '<h2>' . __( 'Add a Fixture', 'bblm' ) . '</h2>';
	echo '<p>' . __( 'The Following page allows you to add fixtures to the league.', 'bblm' ) . '</p>';

$addattempt = 0;
$sucess = 0;


if (isset($_POST['bblm_add_fixture'])) {
/*	print("<pre>");
	print_r($_POST);
	print("</pre>");
	print("<hr />");*/


	$bblm_safe_input = array();

	$insertsql = 'INSERT INTO `'.$wpdb->prefix.'fixture` (`f_id`, `c_id`, `div_id`, `f_date`, `f_teamA`, `f_teamB`, `f_complete`) VALUES ';
	$p = 1;
	$is_first_fixture = 1;
	while ($p < ($_POST['bblm_fgames']+1)) {
		//we only want a comma added for all but the first
		if ($_POST['bblm_fadd'.$p]) {
			if (1 !== $is_first_fixture) {
				$insertsql .= ", ";
			}
			$insertsql .= '(\'\', \''.intval( $_POST['bblm_fcomp'] ).'\', \''.intval( $_POST['bblm_fdiv'] ).'\', \''.$_POST['fdate'.$p].' 19:00:01\', \''.$_POST['bblm_teamA'.$p].'\', \''.$_POST['bblm_teamB'.$p].'\', \'0\')';
		}

		$p++;
		$is_first_fixture = 0;
	}

	//print("<p>".$insertsql."</p>");

	if (FALSE !== $wpdb->query($insertsql)) {
		$sucess = TRUE;
		do_action( 'bblm_post_submission' );
	}
	else {
		$sucess = FALSE;
	}
	$addattempt = 1;


} //end of submit if
if (isset($_POST['bblm_comp_select'])) {
/*	print("<pre>");
	print_r($_POST);
	print("</pre>");
	print("<hr />");*/

	$countmax = intval( $_POST['bblm_fgames'] );

?>
	<form name="bblm_addfixture" method="post" id="post">

	<p>Please enter the details of the fixtures below:</p>

	<input type="hidden" name="bblm_fgames" size="2" value="<?php print($countmax); ?>">
	<input type="hidden" name="bblm_fcomp" size="2" value="<?php print( intval( $_POST['bblm_fcomp'] ) ); ?>">
	<input type="hidden" name="bblm_fdiv" size="2" value="<?php print( intval( $_POST['bblm_fdiv'] ) ); ?>">

<?php
		//before we generate the list of fixtures, we need to grab the teams into an array
		if (13 == $_POST['bblm_fdiv']) {
			//Cross Division has been selected, All the teams in the compeition are slected
			$teamsql = "SELECT T.t_name, T.t_id FROM ".$wpdb->prefix."team T, ".$wpdb->prefix."team_comp C WHERE T.t_id = C.t_id AND C.c_id = ".intval( $_POST['bblm_fcomp'] );
		}
		else {
			//Just select the temas in this division
			$teamsql = "SELECT T.t_name, T.t_id FROM ".$wpdb->prefix."team T, ".$wpdb->prefix."team_comp C WHERE T.t_id = C.t_id AND C.c_id = ".intval( $_POST['bblm_fcomp'] )." AND C.div_id = ".intval( $_POST['bblm_fdiv'] );
		}
		$teams = $wpdb->get_results($teamsql, ARRAY_A);
		if (empty($teams)) {
					print("<p>No Teams have been entered into this stage of the Competition. You will need to add some first!</p>");
		}
		else {
			//generate output into a static string

			//Grab the ID of the 'To Be Determined Team'
			$bblm_tbd_team = bblm_get_tbd_team();

			$teamlist = "";
			$teamlist .= "<option value=\"".$bblm_tbd_team."\">To be Determined</option>\n";
			foreach ($teams as $t) {
					$teamlist .= "<option value=\"".$t['t_id']."\">".$t['t_name']."</option>\n";
			}
			//Now we set our counter up
			$p = 1;
?>
<?php /*print($p);*/ ?>
	<table class="widefat">
		<thead>
		<tr>
			<th>Add</th>
			<th>Home</th>
			<th>Away</th>
			<th>Date</th>
		</tr>
		</thead>
		<tbody>
<?php
		while ($p < ($countmax+1)) {
?>
		<tr>
			<td><input type="checkbox" checked="checked" name="bblm_fadd<?php print($p); ?>"></td>
			<td><select name="bblm_teamA<?php print($p); ?>" id="bblm_teamA<?php print($p); ?>"><?php print($teamlist); ?></select></td>
			<td><select name="bblm_teamB<?php print($p); ?>" id="bblm_teamB<?php print($p); ?>"><?php print($teamlist); ?></select></td>
			<td><input name="fdate<?php print($p); ?>" type="text" size="12" maxlength="10" value="<?php print(date('Y-m-d', strtotime('next thursday'))); ?>"></td>
		</tr>
<?php
			$p++;
		} //emd of while
?>
		</tbody>
	</table>

	<p class="submit">
	<input type="submit" name="bblm_add_fixture" value="Create Fixtures" title="Create Fixtures" class="button-primary"/>
	</p>
	</form>

<?php
	}//end of if $teams is empty
} //end of else if
else {
?>
<?php
	if ($addattempt) {
?>
	<div id="updated" class="updated fade">
	<p>
	<?php
	if ($sucess) {
		print("Fixture(s) where Added. You can add some more below if you want ");
	}
	else {
		print("Something went wrong. Please try again");
	}
	?>
</p>
	</div>
<?php
	} //if something has been added
?>


	<form name="bblm_selectcomp" method="post" id="post">

		<p>Before we can begin, you must first select the competition and division that these matches will take place in:</p>
		<h3>Select a Competition</h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="bblm_fcomp">Competition</label></th>
				<td><select name="bblm_fcomp" id="bblm_fcomp">
	<?php
	$oposts = get_posts(
		array(
			'post_type' => 'bblm_comp',
			'numberposts' => -1,
			'orderby' => 'ID',
			'order' => 'DESC'
		)
	);
	if( ! $oposts ) return;
	foreach( $oposts as $o ) {
		echo '<option value="' . $o->ID . '">' . bblm_get_competition_name( $o->ID ) . '</option>';
	}
	?>
				</select></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="bblm_fdiv">Division</label></th>
				<td><select name="bblm_fdiv" id="bblm_fdiv">
	<?php
	$divsql = 'SELECT div_id, div_name FROM '.$wpdb->prefix.'division ORDER BY div_id';
	if ($divs = $wpdb->get_results($divsql)) {
		foreach ($divs as $div) {
			print("					<option value=\"$div->div_id\">".$div->div_name."</option>\n");
		}
	}
	?>
				</select></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="bblm_fgames">Number of fixtures you wish to add:</label></th>
				<td><input type="text" name="bblm_fgames" size="3" value="1" id="bblm_fgames" maxlength="2"></td>
			</tr>
		</table>

		<p class="submit"><input type="submit" name="bblm_comp_select" id="bblm_comp_select" class="button-primary" value="Continue" title="Continue to set-up new Fixtures"  /></p>

	</form>
<?php
} //end of else
?>

</div>
