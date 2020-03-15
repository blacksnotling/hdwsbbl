<?php
/**
 * BBowlLeagueMan Add Competition brackets
 *
 * Page used to set up brackets for a knock out tournament (or final of a standard competition)
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
<?php
	echo '<h2>' . __( 'Set-up Tournament brackets..', 'bblm' ) . '</h2>';
	echo '<p>' . __( 'The following page can be used to set up the brackets for a Knock-Out Tournemant, or final phase of an open season.', 'bblm' ) . '</p>';


if (isset($_POST['bblm_create_brackets'])) {
/*	print("<pre>");
	print_r($_POST);
	print("</pre>");
	print("<hr />");*/

	$insertsql = 'INSERT INTO `'.$wpdb->prefix.'comp_brackets` (`cb_id`, `c_id`, `div_id`, `m_id`, `f_id`, `cb_text`, `cb_order`) VALUES';
	//Initialize var to capture first input
	$is_first_bracket = 1;

	$games_this_round = ($_POST['bblm_cbteams'] / 2);

	  ////////////////////////////
	 // main loop - add to DB, //
	////////////////////////////
	while ($games_this_round >= 1) {
					$div_id = bblm_return_div_id($games_this_round);
					$bblm_tbd_team = bblm_get_tbd_team();

					//we want to loop through this p times for each division (round)
					$p = 1;
					while ($p <= $games_this_round) {
						$match_text = "x";
						//check to see if a match_id was submitted
						if ("F"== $_POST['bblm_game-'.$div_id.'-'.$p]) {
							$match_id = 0;
							$fixture_id = $_POST['bblm_fixture-'.$div_id.'-'.$p];
							if (0 == $fixture_id) {
								$match_text = "To Be Determined";
							}
							else {
								$fixturesql = 'SELECT T.t_name AS TA, T.t_id AS TAid, O.guid AS TAlink, R.t_name AS TB, R.t_id AS TBid, V.guid AS TBlink FROM '.$wpdb->prefix.'fixture F, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'team R, '.$wpdb->prefix.'bb2wp U, '.$wpdb->posts.' V, '.$wpdb->prefix.'bb2wp P, '.$wpdb->posts.' O WHERE R.t_id = U.tid AND U.prefix = \'t_\' AND U.pid = V.ID AND T.t_id = P.tid AND P.prefix = \'t_\' AND P.pid = O.ID AND F.f_teamA = T.t_id AND F.f_teamB = R.t_id AND F.f_id = '.$fixture_id.' AND F.f_complete = 0 ORDER BY F.div_id LIMIT 0, 30 ';
								$fd = $wpdb->get_row($fixturesql, ARRAY_A);
								//check to see if either team_id matches the default TBD and build the link string.
								if ($bblm_tbd_team == $fd['TAid']) {
									$tAlink = $fd['TA'];
								}
								else {
									$tAlink = "<a href=\"".$fd['TAlink']."\" title=\"View more information on this team\">".$fd['TA']."</a>";
								}
								if ($bblm_tbd_team == $fd['TBid']) {
									$tBlink = $fd['TB'];
								}
								else {
									$tBlink = "<a href=\"".$fd['TBlink']."\" title=\"View more information on this team\">".$fd['TB']."</a>";
								}
								$match_text = $tAlink." vs<br />".$tBlink;
								$match_text = esc_sql( $match_text );
							}

						}
						else {
							$match_id = $_POST['bblm_match-'.$div_id.'-'.$p];
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
						//we only want a comma added for all but the first
						if (1 !== $is_first_bracket) {
							$insertsql .= ",";
						}

						$insertsql .= ' (\'\', \''.intval( $_POST['bblm_cbcomp'] ).'\', \''.$div_id.'\', \''.$match_id.'\', \''.$fixture_id.'\', \''.$match_text.'\', \''.$p.'\')';

						$p++;
						$is_first_bracket = 0;
					} //end while $p


					$games_this_round = ($games_this_round/2);
			}

			if (FALSE !== $wpdb->query($insertsql)) {
				$sucess = TRUE;
				do_action( 'bblm_post_submission' );
			}
			else {
				$wpdb->print_error();
			}



?>
	<div id="updated" class="updated fade">
	<p>
	<?php
	if ($sucess) {
		print("The Brackets for this Competion have been set-up.");
	}
	else {
		print("Something went wrong");
	}
	?>
</p>
	</div>
<?php

} //end of submit if
  ////////////////
 // All done!! //
////////////////
else if (isset($_POST['bblm_comp_select'])) {

	$numteams = intval($_POST['bblm_cbteams']);
	$comp_id = intval($_POST['bblm_cbcomp']);

?>
	<form name="bblm_addbrackets" method="post" id="post">

	<input type="hidden" name="bblm_cbteams" size="2" value="<?php print($numteams); ?>">
	<input type="hidden" name="bblm_cbcomp" size="2" value="<?php print($comp_id); ?>">

<?php
		//before we generate the list of fixtures, we need to grab the teams into an array
		$fixturesql = 'SELECT F.f_id, F.div_id, T.WPID AS TA, R.WPID AS TB FROM '.$wpdb->prefix.'fixture F, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'team R WHERE F.f_teamA = T.t_id AND F.f_teamB = R.t_id AND F.c_id = '.$comp_id.' AND F.f_complete = 0 ORDER BY F.div_id';
		$fixtures = $wpdb->get_results($fixturesql, ARRAY_A);
		if (empty($fixtures)) {
			$fixturelist = "<option value=\"0\">To Be Determined</option>\n";
		}
		else {
			//generate output into a static string
			$fixturelist = "<option value=\"0\">To Be Determined</option>\n";
			foreach ($fixtures as $f) {
					$fixturelist .= "<option value=\"".$f['f_id']."\">".bblm_get_team_name( $f['TA'] )." vs ".bblm_get_team_name( $f['TB'] )."</option>\n";
			}
		}

		$matchsql = 'SELECT M.m_id, UNIX_TIMESTAMP(M.m_date) AS mdate, T.WPID AS TA, M.m_teamAtd, R.WPID as TB, M.m_teamBtd, M.div_id from '.$wpdb->prefix.'match M, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'team R WHERE M.m_teamA = T.t_id AND M.m_teamB = R.t_id AND M.c_id = '.$comp_id.' ORDER BY M.div_id DESC';
		$matches = $wpdb->get_results($matchsql, ARRAY_A);
		if (empty($matches)) {
			$matchlist = "<option value=\"x\">No matches have been played, Please select a fixture</option>\n";
		}
		else {
			//generate output into a static string
			$matchlist = "<option value=\"0\">Not Appliccable</option>\n";
			foreach ($matches as $m) {
					$matchlist .= "<option value=\"".$m['m_id']."\">".date("d.m.Y", $m['mdate'])." - ".bblm_get_team_name( $m['TA'] )." (".$m['m_teamAtd'].") vs ".bblm_get_team_name( $m['TB'] )." (".$m['m_teamBtd'].")</option>\n";
			}
		}
		//if there are no fixtures and no matches then instruct the user to go and set some up
		if ((empty($matches)) && (empty($fixtures))) {
			print("<p>There are no matches or fixtures set up for this stage of the competition. Set some up frst and then return here.</p>");
			  /////////////////////////
			 // End processing here //
			/////////////////////////
		}
		else {
			//the number of games in a round will always be half the number of teams
			$games_this_round = ($numteams / 2);

			  ////////////////
			 // main loop, //
			////////////////
			//while there are at least two teams, we can carry on,
			while ($games_this_round >= 1) {
				$div_id = bblm_return_div_id($games_this_round);
				print("<h3>".bblm_return_div_name($games_this_round)."</h3>");

				//we want to loop through this p times for each division (round)
				$p = 1;
				while ($p <= $games_this_round) {

?>
		<h4>Match <?php print($p); ?></h4>
		<ul>
			<li><input type="radio" value="M" name="bblm_game-<?php print($div_id); ?>-<?php print($p); ?>">Match: <select name="bblm_match-<?php print($div_id); ?>-<?php print($p); ?>" id="bblm_match-<?php print($div_id); ?>-<?php print($p); ?>"><?php print($matchlist); ?></select></li>
			<li><input type="radio" value="F" name="bblm_game-<?php print($div_id); ?>-<?php print($p); ?>" checked="yes">Fixture: <select name="bblm_fixture-<?php print($div_id); ?>-<?php print($p); ?>" id="bblm_fixture<?php print($div_id); ?>-<?php print($p); ?>"><?php print($fixturelist); ?></select></li>
		</ul>
<?php
					$p++;
				} //end while $p


				$games_this_round = ($games_this_round/2);
			}

	//now we go through each round and list the available matches / fixtures
	//we know the number of matches in each round (#teams / 2) so we need to use a count to print out that man before moving onto the next comp
	//The only issue there is assigning the match to the correct div_id
	//One way may be: if (#teams/2 >= 8 { div_id = 5)
	//could tie this in with printing the name to the screen. this would requre having a (numbered) array of the divisions handy

	?>

		<p class="submit">
		<input type="submit" name="bblm_create_brackets" value="Commit Brackets" title="Commit Brackets"/>
		</p>
		</form>


<?php
	}//end of "if there are no matches or fixtures"
} //end of else if
else {
?>
	<form name="bblm_selectcomp" method="post" id="post">

	<p>Before we can begin, you must first select the competition:</p>
	<fieldset id='addbracketsdiv'><legend>Select a Competition</legend>

	  <label for="bblm_cbcomp" class="selectit">Competition</label>
	  <select name="bblm_cbcomp" id="bblm_cbcomp">
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
	</select>

	  <label for="bblm_cbteams" class="selectit">Number of teams taking part</label>
	  <select name="bblm_cbteams" id="bblm_cbteams">
	  	<option value="4">0-4</option>
	  	<option value="8">5-8</option>
	  	<option value="16">9-16</option>
	  	<option value="32">17-32</option>
	  </select>


	</fieldset>
	<p class="submit">
	<input type="submit" name="bblm_comp_select" value="Continue" title="Continue with selection"/>
	</p>
	</form>
<?php
} //end of else
?>

</div>
