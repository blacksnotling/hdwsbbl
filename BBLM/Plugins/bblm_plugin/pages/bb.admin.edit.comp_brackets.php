<?php
/**
 * BBowlLeagueMan Edit Competition brackets
 *
 * Page used to set up the brackerts for a knock out tournament (or final of a standard competition)
 *
 * @author 		Blacksnotling
 * @category 	Core
 * @package 	BBowlLeagueMan/Pages
 */

//Check the file is not being accessed directly
if (!function_exists('add_action')) die('You cannot run this file directly. Naughty Person');

function bblm_return_div_id($games) {
//function takes in the number of games this round and returns the matching ID from the database.
	switch ($games) {
		case (1 == $games):
	    	return 1;
		    break;
		case (2 == $games):
	    	return 3;
		    break;
		case (4 == $games):
	    	return 4;
		    break;
		case (8 == $games):
	    	return 5;
		    break;
		case (16 == $games):
	    	return 7;
		    break;
	}
}
function bblm_return_div_name($games) {
//function takes in the number of games this round and returns the matching name from the database.
	switch ($games) {
		case (1 == $games):
	    	return "Final";
		    break;
		case (2 == $games):
	    	return "Quarter Final";
		    break;
		case (4 == $games):
	    	return "Semi-Final";
		    break;
		case (8 == $games):
	    	return "Second Round";
		    break;
		case (16 == $games):
	    	return "Opening Round";
		    break;
	}
}



?>
<div class="wrap">
	<h2>Edit Tournament brackets.</h2>
	<p>The following page can be used to Edit the brackets for a Knock-Out Tournemant, or final phase of an open season.</p>

<?php

if (isset($_POST['bblm_update_bracket'])) {
  //////////////////////////
 // Update Bracket in DB //
//////////////////////////
/*	print("<pre>");
	print_r($_POST);
	print("</pre>");
	print("<hr />");*/

	$options = get_option('bblm_config');
	$bblm_tbd_team = htmlspecialchars($options['team_tbd'], ENT_QUOTES);

	//Generate text for bracket
	if (F== $_POST['bblm_game']) {
		$match_id = 0;
		$fixture_id = $_POST['bblm_fixture'];
		if (0 == $fixture_id) {
			$match_text = "To Be Determined";
		}
		else {
			$fixturesql = 'SELECT T.t_name AS TA, T.t_id AS TAid, O.guid AS TAlink, R.t_name AS TB, R.t_id AS TBid, V.guid AS TBlink FROM '.$wpdb->prefix.'fixture F, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'team R, '.$wpdb->prefix.'bb2wp U, '.$wpdb->posts.' V, '.$wpdb->prefix.'bb2wp P, '.$wpdb->posts.' O WHERE R.t_id = U.tid AND U.prefix = \'t_\' AND U.pid = V.ID AND T.t_id = P.tid AND P.prefix = \'t_\' AND P.pid = O.ID AND F.f_teamA = T.t_id AND F.f_teamB = R.t_id AND F.f_id = '.$fixture_id.' AND F.f_complete = 0 ORDER BY F.div_id LIMIT 0, 30 ';
			$fd = $wpdb->get_row($fixturesql, ARRAY_A);
			//check to see if either team_id matches the default TBD and build the link string.
			if ($bblm_tbd_team == $fd[TAid]) {
				$tAlink = $fd[TA];
			}
			else {
				$tAlink = "<a href=\"".$fd[TAlink]."\" title=\"View more information on this team\">".$fd[TA]."</a>";
			}
			if ($bblm_tbd_team == $fd[TBid]) {
				$tBlink = $fd[TB];
			}
			else {
				$tBlink = "<a href=\"".$fd[TBlink]."\" title=\"View more information on this team\">".$fd[TB]."</a>";
			}
			$match_text = $tAlink." vs<br />".$tBlink;
			$match_text = esc_sql( $match_text );
		}
	}
	else {
		$match_id = $_POST['bblm_match'];
		$fixture_id = 0;
		if (x == $match_id) {
			$match_text = "To Be Determined";
		}
		else {
			//$matchsql = 'SELECT T.t_name AS TA, M.m_teamAtd, R.t_name as TB, M.m_teamBtd FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'team R WHERE M.m_teamA = T.t_id AND M.m_teamB = R.t_id AND M.m_id = '.$match_id.' ORDER BY M.div_id DESC';
			$matchsql = 'SELECT M.m_teamAtd, M.m_teamBtd, T.t_name AS TA, T.t_id AS TAid, O.guid AS TAlink, R.t_name AS TB, R.t_id AS TBid, V.guid AS TBlink FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'team R, '.$wpdb->prefix.'bb2wp U, '.$wpdb->posts.' V, '.$wpdb->prefix.'bb2wp P, '.$wpdb->posts.' O WHERE M.m_teamA = T.t_id AND M.m_teamB = R.t_id AND M.m_id = '.$match_id.' AND R.t_id = U.tid AND U.prefix = \'t_\' AND U.pid = V.ID AND T.t_id = P.tid AND P.prefix = \'t_\' AND P.pid = O.ID ORDER BY M.div_id DESC';
			$md = $wpdb->get_row($matchsql, ARRAY_A);
			//check to see if either team_id matches the default TBD and build the link string.
			if ($bblm_tbd_team == $md[TAid]) {
				$tAlink = $md[TA];
			}
			else {
				$tAlink = "<a href=\"".$md[TAlink]."\" title=\"View more information on this team\">".$md[TA]."</a>";
			}
			if ($bblm_tbd_team == $md[TBid]) {
				$tBlink = $md[TB];
			}
			else {
				$tBlink = "<a href=\"".$md[TBlink]."\" title=\"View more information on this team\">".$md[TB]."</a>";
			}
			$match_text = $tAlink." <strong>".$md[m_teamAtd]."</strong><br />".$tBlink." <strong>".$md[m_teamBtd]."</strong>";
			$match_text = esc_sql( $match_text );
		}
	}
	//end of text generation

	$updatebracketsql = 'UPDATE `'.$wpdb->prefix.'comp_brackets` SET `m_id` = \''.$_POST['bblm_match'].'\', `f_id` = \''.$_POST['bblm_fixture'].'\', `cb_text` = \''.$match_text.'\' WHERE `cb_id` = '.$_POST['bblm_bid'].' LIMIT 1';
	//print($updatebracketsql);

	if (FALSE !== $wpdb->query($updatebracketsql)) {
		$sucess = TRUE;
	}
?>
	<div id="updated" class="updated fade">
		<p>
<?php
	if ($sucess) {
		print("The Bracket in question has been updated.");
	}
	else {
		print("Something went wrong");
	}
?>
		</p>
	</div>
<?php

}//end of update brackets

  ////////////////////
 // $_GET checking //
////////////////////

else if ( "edit" == $_GET['action'] ) {
	if ( "cbracket" == $_GET['item'] ) {
		  //////////////////////////
		 // Editing Comp Brackey //
		//////////////////////////
		$bid = $_GET['id'];
		print("<h3>Editing Bracket</h3>\n");
		print("<p>Below is the bracket as it will be saved. <strong>Note</strong>: If you are updting a fixture, it may be displayed differently below than on the site. if in doubt, hit save!</p>\n");
		$cbdetailssql = 'SELECT * FROM '.$wpdb->prefix.'comp_brackets WHERE cb_id = '.$bid.' LIMIT 1';
		if ($cb = $wpdb->get_row($cbdetailssql)) {
?>
	<form name="bblm_editbrackets" method="post" id="post">

		<ul>
			<li><input type="radio" value="M" name="bblm_game"<?php if (0 == $cb->f_id) { print(" checked=\"yes\""); } ?>>Match:
			<select name="bblm_match" id="bblm_match">
<?php
		$matchsql = 'SELECT M.m_id, UNIX_TIMESTAMP(M.m_date) AS mdate, T.t_name AS TA, M.m_teamAtd, R.t_name as TB, M.m_teamBtd, M.div_id from '.$wpdb->prefix.'match M, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'team R WHERE M.m_teamA = T.t_id AND M.m_teamB = R.t_id AND M.c_id = '.$cb->c_id.' ORDER BY M.div_id DESC';
		$matches = $wpdb->get_results($matchsql, ARRAY_A);
		if (empty($matches)) {
			print("				<option value=\"0\">To Be Determined</option>\n");
		}
		else {
			//generate output into a static string
			print("				<option value=\"0\">Not Applicable</option>\n");
			foreach ($matches as $m) {
					print("				<option value=\"".$m[m_id]."\"");
					if ($m[m_id] == $cb->m_id) {
						print(" selected=\"selected\"");
					}
					print(">".date("d.m.Y", $m[mdate])." - ".$m[TA]." (".$m[m_teamAtd].") vs ".$m[TB]." (".$m[m_teamBtd].")</option>\n");
			}
		}
?>
			</select></li>

			<li><input type="radio" value="F" name="bblm_game"<?php if (0 == $cb->m_id) { print(" checked=\"yes\""); } ?>>Fixture:
			<select name="bblm_fixture" id="bblm_fixture">
<?php
		$fixturesql = 'SELECT F.f_id, F.div_id, T.t_name AS TA, R.t_name AS TB FROM '.$wpdb->prefix.'fixture F, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'team R WHERE F.f_teamA = T.t_id AND F.f_teamB = R.t_id AND F.c_id = '.$cb->c_id.' AND F.f_complete = 0 ORDER BY F.div_id';
		$fixtures = $wpdb->get_results($fixturesql, ARRAY_A);
		if (empty($fixtures)) {
			print("				<option value=\"0\">To Be Determined</option>\n");
		}
		else {
			//generate output into a static string
			print("				<option value=\"0\">To Be Determined</option>\n");
			foreach ($fixtures as $f) {
					print("				<option value=\"".$f['f_id']."\"");
					if ($f['f_id'] == $cb->f_id) {
						print(" selected=\"selected\"");
					}
					print(">".$f['TA']." vs ".$f['TB']."</option>\n");
			}
		}
?>
			</select></li>
		</ul>
		<input type="hidden" name="bblm_bid" size="2" value="<?php print($bid); ?>">

		<p class="submit">
			<input type="submit" name="bblm_update_bracket" value="Update Bracket" title="Update Bracket"/>
		</p>
		</form>

<?php
		}//end of if bracket exists
		else {
			print("<p>This bracket does not appear to exist. Pease try again!</p>\n");
		}
	}// end of item == cbracket
}//end of $_GET checking

else if (isset($_POST['bblm_select_comp'])) {
  /////////////////////////////////
 // Display the brackets so far //
/////////////////////////////////
	$cid = $_POST['bblm_cid'];
	print("<h3>Brackets for this competition</h3>\n");
	print("<p>Below are the brackets for this competition. Press the edit button in the relevent bracket to change it.</p>\n");

	//Following code copied from view.comp (modified slightly)
	//end of copy and past code

	//gather brackets from data base, they MUST be sorted by div, order. blanks must be present if there are any byes
	$bracketssql = 'SELECT C.cb_text, D.div_name, C.cb_id FROM '.$wpdb->prefix.'comp_brackets C, '.$wpdb->prefix.'division D WHERE C.div_id = D.div_id AND C.c_id = '.$cid.' ORDER BY C.div_ID DESC, C.cb_order ASC';
	$brackets = $wpdb->get_results($bracketssql, ARRAY_N);
	//determine number of games (which determines the layout to be used
	$numgames = count($brackets);
	if (7 == $numgames) {
?>
	<table border="1" cellspacing="2" cellpadding="2">
		<tr>
			<th><?php print($brackets[0][1]); ?></th>
			<th><?php print($brackets[4][1]); ?></th>
			<th><?php print($brackets[6][1]); ?></th>
		</tr>
		<tr>
			<td><?php print($brackets[0][0]); ?><br />[*<a href="admin.php?page=bblm_plugin/pages/bb.admin.edit.comp_brackets.php&action=edit&item=cbracket&id=<?php print($brackets[0][2]); ?>" title="Edit this bracket">Edit</a>*]</td>
			<td rowspan="2"><?php print($brackets[4][0]); ?><br />[*<a href="admin.php?page=bblm_plugin/pages/bb.admin.edit.comp_brackets.php&action=edit&item=cbracket&id=<?php print($brackets[4][2]); ?>" title="Edit this bracket">Edit</a>*]</td>
			<td rowspan="4"><?php print($brackets[6][0]); ?><br />[*<a href="admin.php?page=bblm_plugin/pages/bb.admin.edit.comp_brackets.php&action=edit&item=cbracket&id=<?php print($brackets[6][2]); ?>" title="Edit this bracket">Edit</a>*]</td>
      		</tr>
      		<tr>
			<td><?php print($brackets[1][0]); ?><br />[*<a href="admin.php?page=bblm_plugin/pages/bb.admin.edit.comp_brackets.php&action=edit&item=cbracket&id=<?php print($brackets[1][2]); ?>" title="Edit this bracket">Edit</a>*]</td>
		</tr>
		<tr>
			<td><?php print($brackets[2][0]); ?><br />[*<a href="admin.php?page=bblm_plugin/pages/bb.admin.edit.comp_brackets.php&action=edit&item=cbracket&id=<?php print($brackets[2][2]); ?>" title="Edit this bracket">Edit</a>*]</td>
			<td rowspan="2"><?php print($brackets[5][0]); ?><br />[*<a href="admin.php?page=bblm_plugin/pages/bb.admin.edit.comp_brackets.php&action=edit&item=cbracket&id=<?php print($brackets[5][2]); ?>" title="Edit this bracket">Edit</a>*]</td>
		</tr>
		<tr>
			<td><?php print($brackets[3][0]); ?><br />[*<a href="admin.php?page=bblm_plugin/pages/bb.admin.edit.comp_brackets.php&action=edit&item=cbracket&id=<?php print($brackets[3][2]); ?>" title="Edit this bracket">Edit</a>*]</td>
		</tr>
	</table>
<?php
	} //end of if 7 games
	else if (3 == $numgames) {
?>
	<table border="1" cellspacing="2" cellpadding="2">
		<tr>
			<th><?php print($brackets[0][1]); ?></th>
			<th><?php print($brackets[2][1]); ?></th>
		</tr>
		<tr>
			<td><?php print($brackets[0][0]); ?><br />[*<a href="admin.php?page=bblm_plugin/pages/bb.admin.edit.comp_brackets.php&action=edit&item=cbracket&id=<?php print($brackets[0][2]); ?>" title="Edit this bracket">Edit</a>*]</td>
			<td rowspan="2"><?php print($brackets[2][0]); ?><br />[*<a href="admin.php?page=bblm_plugin/pages/bb.admin.edit.comp_brackets.php&action=edit&item=cbracket&id=<?php print($brackets[2][2]); ?>" title="Edit this bracket">Edit</a>*]</td>
		</tr>
		<tr>
			<td><?php print($brackets[1][0]); ?><br />[*<a href="admin.php?page=bblm_plugin/pages/bb.admin.edit.comp_brackets.php&action=edit&item=cbracket&id=<?php print($brackets[1][2]); ?>" title="Edit this bracket">Edit</a>*]</td>
		</tr>
	</table>
<?php
	} //end of else if 3 games
						else if (15 == $numgames) {
?>
	<table border="1" cellspacing="2" cellpadding="2">
		<tr>
			<th><?php print($brackets[0][1]); ?></th>
			<th><?php print($brackets[8][1]); ?></th>
			<th><?php print($brackets[12][1]); ?></th>
			<th><?php print($brackets[14][0]); ?></th>
		</tr>
		<tr>
			<td><?php print($brackets[0][0]); ?><br />[*<a href="admin.php?page=bblm_plugin/pages/bb.admin.edit.comp_brackets.php&action=edit&item=cbracket&id=<?php print($brackets[0][2]); ?>" title="Edit this bracket">Edit</a>*]</td>
			<td rowspan="2"><?php print($brackets[8][0]); ?><br />[*<a href="admin.php?page=bblm_plugin/pages/bb.admin.edit.comp_brackets.php&action=edit&item=cbracket&id=<?php print($brackets[8][2]); ?>" title="Edit this bracket">Edit</a>*]</td>
			<td rowspan="4"><?php print($brackets[12][0]); ?><br />[*<a href="admin.php?page=bblm_plugin/pages/bb.admin.edit.comp_brackets.php&action=edit&item=cbracket&id=<?php print($brackets[12][2]); ?>" title="Edit this bracket">Edit</a>*]</td>
			<td rowspan="8"><?php print($brackets[14][0]); ?><br />[*<a href="admin.php?page=bblm_plugin/pages/bb.admin.edit.comp_brackets.php&action=edit&item=cbracket&id=<?php print($brackets[14][2]); ?>" title="Edit this bracket">Edit</a>*]</td>
		</tr>
		<tr>
			<td><?php print($brackets[1][0]); ?><br />[*<a href="admin.php?page=bblm_plugin/pages/bb.admin.edit.comp_brackets.php&action=edit&item=cbracket&id=<?php print($brackets[1][2]); ?>" title="Edit this bracket">Edit</a>*]</td>
		</tr>
		<tr>
			<td><?php print($brackets[2][0]); ?><br />[*<a href="admin.php?page=bblm_plugin/pages/bb.admin.edit.comp_brackets.php&action=edit&item=cbracket&id=<?php print($brackets[2][2]); ?>" title="Edit this bracket">Edit</a>*]</td>
			<td rowspan="2"><?php print($brackets[9][0]); ?><br />[*<a href="admin.php?page=bblm_plugin/pages/bb.admin.edit.comp_brackets.php&action=edit&item=cbracket&id=<?php print($brackets[9][2]); ?>" title="Edit this bracket">Edit</a>*]</td>
		</tr>
		<tr>
			<td><?php print($brackets[3][0]); ?><br />[*<a href="admin.php?page=bblm_plugin/pages/bb.admin.edit.comp_brackets.php&action=edit&item=cbracket&id=<?php print($brackets[3][2]); ?>" title="Edit this bracket">Edit</a>*]</td>
		</tr>
		<tr>
			<td><?php print($brackets[4][0]); ?><br />[*<a href="admin.php?page=bblm_plugin/pages/bb.admin.edit.comp_brackets.php&action=edit&item=cbracket&id=<?php print($brackets[4][2]); ?>" title="Edit this bracket">Edit</a>*]</td>
			<td rowspan="2"><?php print($brackets[10][0]); ?><br />[*<a href="admin.php?page=bblm_plugin/pages/bb.admin.edit.comp_brackets.php&action=edit&item=cbracket&id=<?php print($brackets[10][2]); ?>" title="Edit this bracket">Edit</a>*]</td>
			<td rowspan="4"><?php print($brackets[13][0]); ?><br />[*<a href="admin.php?page=bblm_plugin/pages/bb.admin.edit.comp_brackets.php&action=edit&item=cbracket&id=<?php print($brackets[13][2]); ?>" title="Edit this bracket">Edit</a>*]</td>
		</tr>
		<tr>
			<td><?php print($brackets[5][0]); ?><br />[*<a href="admin.php?page=bblm_plugin/pages/bb.admin.edit.comp_brackets.php&action=edit&item=cbracket&id=<?php print($brackets[5][2]); ?>" title="Edit this bracket">Edit</a>*]</td>
		</tr>
		<tr>
			<td><?php print($brackets[6][0]); ?><br />[*<a href="admin.php?page=bblm_plugin/pages/bb.admin.edit.comp_brackets.php&action=edit&item=cbracket&id=<?php print($brackets[6][2]); ?>" title="Edit this bracket">Edit</a>*]</td>
			<td rowspan="2"><?php print($brackets[11][0]); ?><br />[*<a href="admin.php?page=bblm_plugin/pages/bb.admin.edit.comp_brackets.php&action=edit&item=cbracket&id=<?php print($brackets[11][2]); ?>" title="Edit this bracket">Edit</a>*]</td>
		</tr>
		<tr>
			<td><?php print($brackets[7][0]); ?><br />[*<a href="admin.php?page=bblm_plugin/pages/bb.admin.edit.comp_brackets.php&action=edit&item=cbracket&id=<?php print($brackets[7][2]); ?>" title="Edit this bracket">Edit</a>*]</td>
		</tr>
	</table>
<?php
	} //end of else if 15 games
	else {
		//something has gone wrong (not one a 4,8 or 16 team tourney!)
		print("<p>something has gone wrong - more fixtures than expected.</p>");
	}
} //end of select_comp
else {
  //////////////////////////
 // Select a Competition //
//////////////////////////
?>
	<form name="bblm_selectcomp" method="post" id="post">

	<p>Before we can begin, you must first select the competition that you wish to modify. <strong>Note</strong>: If the Competition isn't listed, not brackets have been set up!</p>

	<table class="form-table">
	<tr class="form-field form-required">
		<th scope="row" valign="top"><label for="bblm_cid">Competition</label></th>
		<td><select name="bblm_cid" id="bblm_cid">
<?php
		$compbsql = 'SELECT DISTInCT(C.c_id), P.post_title FROM '.$wpdb->prefix.'comp_brackets C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE C.c_id = J.tid AND J.prefix = \'c_\' AND J.pid = P.ID ORDER BY C.c_id DESC';
			if ($compb = $wpdb->get_results($compbsql)) {
				foreach ($compb as $c) {
					print("			<option value=\"".$c->c_id."\">".$c->post_title."</option>\n");
				}
			}
?>
		</select></td>
	</tr>
	</table>


	<p class="submit">
		<input type="submit" name="bblm_select_comp" value="Continue" title="Continue with selection"/>
	</p>
	</form>
<?php
} //end of else
?>

</div>
