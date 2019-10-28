<?php
/**
 * BBowlLeagueMan Edit Fixture
 *
 * Page used to Edit a Fixture
 *
 * @author 		Blacksnotling
 * @category 	Core
 * @package 	BBowlLeagueMan/Pages
 */

//Check the file is not being accessed directly
if (!function_exists('add_action')) die('You cannot run this file directly. Naughty Person');
?>
<div class="wrap">
	<h2>Edit Fixtures</h2>
	<p>Below is a list of all fixtures that have not been played yet. Ammend the details and tick the "changed" box to update one.</p>

<?php
	if(isset($_POST['bblm_edit_fixture'])) {
	  ///////////////////////
	 // Step 3: Update DB //
	///////////////////////

		//Set initil values for loop
		$p = 1;
		$pmax = $_POST['bblm_fgames'];
		//define array to hold playerupdate sql
		$fixturesqla = array();

		while ($p <= $pmax){
			//if  "on" result in "changed" then generate SQL
			if (on == $_POST['bblm_fchng'.$p]) {
				$updatesql = 'UPDATE `'.$wpdb->prefix.'fixture` SET `f_date` = \''.$_POST['fdate'.$p].'\', `f_teamA` = \''.$_POST['bblm_fta'.$p].'\', `f_teamB` = \''.$_POST['bblm_ftb'.$p].'\' WHERE `f_id` = '.$_POST['fid'.$p].' LIMIT 1';
				$fixturesqla[$p] = $updatesql;
			}
			$p++;
		}

		foreach ($fixturesqla as $fs) {
			if (FALSE !== $wpdb->query($fs)) {
				$sucess = TRUE;
				do_action( 'bblm_post_submission' );
			}
		}

		if ($sucess) {
			print("<div id=\"updated\" class=\"updated fade\"><p>Fixtures have been updated.</p></div>");
		}
		else {
			print("<div id=\"updated\" class=\"updated fade\"><p>Something has gone wrong. Try again later</p></div>");
		}
		  /////////////////
		 // End of File //
		/////////////////
	}
	else if(isset($_POST['bblm_select_fixture'])) {
	  ////////////////////////////
	 // Step 2: Modfy fixtures //
	////////////////////////////
?>
	<form name="bblm_editfixture" method="post" id="post">
<?php

	$compnamesql = 'SELECT P.post_name FROM '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE P.ID = J.pid AND J.prefix = \'c_\' AND J.tid = '.$_POST['bblm_fcomp'].' LIMIT 1';
	$divnamesql = 'SELECT div_name FROM '.$wpdb->prefix.'division WHERE div_id = '.$_POST['bblm_fdiv'];
	$fixturessql = 'SELECT * FROM '.$wpdb->prefix.'fixture F WHERE F.f_complete = 0 AND F.c_id = '.$_POST['bblm_fcomp'].' AND F.div_id = '.$_POST['bblm_fdiv'].' ORDER BY F.f_date ASC';
	//If the "Cross Division" has been selected, pull all the teams taking part in that comp
	if ( 13 == $_POST['bblm_fdiv'] ) {
		$teamssql = 'SELECT P.post_title, C.t_id FROM '.$wpdb->prefix.'team_comp C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE C.t_id = J.tid AND J.prefix = \'t_\' AND J.pid = P.ID AND C.c_id = '.$_POST['bblm_fcomp'].' ORDER BY P.post_title ASC';
	}
	else {
		$teamssql = 'SELECT P.post_title, C.t_id FROM '.$wpdb->prefix.'team_comp C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE C.t_id = J.tid AND J.prefix = \'t_\' AND J.pid = P.ID AND C.c_id = '.$_POST['bblm_fcomp'].' AND C.div_id = '.$_POST['bblm_fdiv'].' ORDER BY P.post_title ASC';
	}

	$comp_name = $wpdb->get_var($compnamesql);
	$div_name = $wpdb->get_var($divnamesql);
?>
	<p>Below are the fixtures for <strong><?php print($comp_name); ?></strong>, <strong><?php print($div_name); ?></strong>.</p>
<?php

	if ($fixtures = $wpdb->get_results($fixturessql)) {
		$fcount = 1;
		print("<table class=\"form-table\">\n	<tr>	<th>ID</th>\n	<th>Date</th>\n	<th>Team A</th>\n	<th>&nbsp;</th>\n	<th>Team B</th>\n	<th>Changed</th>\n</tr>\n");
		foreach ($fixtures as $f) {
			print("	<tr class=\"form-field form-required\">		<th scope=\"row\" valign=\"top\">".$f->f_id."</th>\n");
			print("		<td><input name=\"fid".$fcount."\" type=\"hidden\" size=\"4\" maxlength=\"4\" value=\"".$f->f_id."\"><input name=\"fdate".$fcount."\" type=\"text\" size=\"20\" maxlength=\"20\" value=\"".$f->f_date."\"></td>\n");
			print("		<td><select name=\"bblm_fta".$fcount."\" id=\"bblm_fta".$fcount."\">\n");
			if ($teams = $wpdb->get_results($teamssql)) {
				foreach ($teams as $t) {
					print("			<option value=\"".$t->t_id."\"");
					if ($f->f_teamA == $t->t_id) {
						print(" selected=\"selected\"");
					}
					print(">".$t->post_title."</option>\n");
				}
			}
			print("		</select></td>\n");
			print("		<td>vs</td>\n");
			print("		<td><select name=\"bblm_ftb".$fcount."\" id=\"bblm_ftb".$fcount."\">\n");
			foreach ($teams as $t) {
				print("			<option value=\"".$t->t_id."\"");
				if ($f->f_teamB == $t->t_id) {
					print(" selected=\"selected\"");
				}
				print(">".$t->post_title."</option>\n");
			}
			print("		</select></td>\n");
			print("		<td><input type=\"checkbox\" name=\"bblm_fchng".$fcount."\"></td>	</tr>\n");
			$fcount++;
		}
		print("</table>\n");

		print("<input type=\"hidden\" name=\"bblm_fgames\" size=\"2\" value=\"".--$fcount."\">\n");
		print("<input type=\"hidden\" name=\"bblm_fcomp\" size=\"2\" value=\"".$_POST['bblm_fcomp']."\">\n");
		print("<input type=\"hidden\" name=\"bblm_fdiv\" size=\"2\" value=\"".$_POST['bblm_fdiv']."\">\n");
	}
	else {
		print("<div class=\"bblm_info\">\n	<p>There are currently no fixtures scheduled.</p>\n	</div>");
	}
?>
	<p class="submit">
	<input type="submit" name="bblm_edit_fixture" value="Submit changes" title="Submit changes" class="button-primary" />
	</p>
	</form>
<?php
}
else {
	  ///////////////////////////////
	 // Step 1: Select comp + div //
	///////////////////////////////
?>
<form name="bblm_editfixture" method="post" id="post">

	<p>Before we can begin, you must first select the Competition and Division that this the fixture is set up in:</p>
	<table class="form-table">
	<tr valign="top">
		<th scope="row"><label for="bblm_fcomp">Competition</label></th>
		<td><select name="bblm_fcomp" id="bblm_fcomp">
<?php
		//$compsql = 'SELECT c_id, c_name FROM '.$wpdb->prefix.'comp WHERE c_active = 1 order by c_name';
		$compsql = 'SELECT DISTINCT J.tid AS c_id, P.post_title AS c_name FROM '.$wpdb->prefix.'fixture F, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE J.tid = F.c_id AND J.prefix = \'c_\' and J.pid = P.ID AND F.f_complete = 0 ORDER BY P.post_title ASC';
		if ($comps = $wpdb->get_results($compsql)) {
			foreach ($comps as $comp) {
				print("<option value=\"$comp->c_id\">".$comp->c_name."</option>\n");
			}
		}
?>
		</select>
           <br />Only Competitions with Fixtures lined up will be displayed here</td>
	</tr>
	<tr valign="top">
		<th scope="row"><label for="bblm_fdiv">Division</label></th>
		<td><select name="bblm_fdiv" id="bblm_fdiv">
<?php
		$divsql = 'SELECT div_id, div_name FROM '.$wpdb->prefix.'division ORDER BY div_id';
		if ($divs = $wpdb->get_results($divsql)) {
			foreach ($divs as $div) {
				print("<option value=\"$div->div_id\">".$div->div_name."</option>\n");
			}
		}
?>
		</select></td>
	</tr>
	</table>


	<p class="submit"><input type="submit" name="bblm_select_fixture" value="Display Fixtures" title="Display FIxtures for this selection" class="button-primary" /></p>
</form>
<?php

}
?>

</div>
