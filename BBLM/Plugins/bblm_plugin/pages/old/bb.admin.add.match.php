<?php
/*
*	Filename: bb.admin.add,match.php
*	Version: 1.0
*	Description: Page used to record a matches details.
*/
/* -- Change History --
20080311 - 0.1b - Initial creation of file. got as far as working out the layout
20080312 - 1.0b - After 4+ hours of coding it is working shape. there are two bugs that can wait until tommrrow!
20080314 - 1.1b - Fixed he remaining bugs. one was he date was he wrong way round, the oher was a mispelling in the SQL?!
20080316 - 1.2b - Updated a query to account for a DB change.
20080318 - 1.3b - Fixed the error where the join table was not getting filled out correctly
20080402 - 1.4b - Edited page so you can select a location/stadium that this match took place in.
		   1.5b - Match result for teams (W,L or D) is automaticlly generated!
20080409 - 1.6b - changed it so todays date is the default.
20080411 - 1.7b - Modified bb_match sql string to take into account db modification (cas totals for easy ref)
20080427 - 1.8b - Added the option to select the match from a fixture (note fixture is marked s complete but if there is a bracket then it doesn't show!).
20080428 - 1.8.1b - Corrected small bug that stopped matches being submotted manually!
		 - 1.9b - Finally fixed the calculate league points bug. it was a lot more complicated then I thought
		 - 1.9.1b - extended the size of the number that can be entered into winnings
20080730 - 1.0 - bump to Version 1 for public release.

*/


//Check the file is not being accessed directly
if (!function_exists('add_action')) die('You cannot run this file directly. Naughty Person');
?>
<div class="wrap">
	<h2>Record a match</h2>
	<p>Use the following page to Record a match that took place in the league.</p>

<?php
if (isset($_POST['bblm_match_add'])) {
/*	print("<pre>");
	print_r($_POST);
	print("</pre>");*/

	$bblm_safe_input = array();

	//think about a for each
	if (get_magic_quotes_gpc()) {
		$_POST['matchtrivia'] = stripslashes($_POST['matchtrivia']);
		$_POST['tAnotes'] = stripslashes($_POST['tAnotes']);
		$_POST['tBnotes'] = stripslashes($_POST['tBnotes']);
	}

	$bblm_safe_input['mtrivia'] = $wpdb->escape($_POST['matchtrivia']);
	$bblm_safe_input['tAnotes'] = $wpdb->escape($_POST['tAnotes']);
	$bblm_safe_input['tBnotes'] = $wpdb->escape($_POST['tBnotes']);

	$bblm_safe_input['mdate'] = $_POST['mdate'] ." 00:00:00";
	$bblm_safe_input['mstad'] = $_POST['mstad'];
	$bblm_safe_input['mcomp'] = $_POST['bblm_comp'];
	$bblm_safe_input['mdiv'] = $_POST['bblm_div'];
	$bblm_safe_input['mweather1'] = $_POST['mweather'];
	$bblm_safe_input['mweather2'] = $_POST['mweather2'];
	$bblm_safe_input['mgate'] = $_POST['gate'];

	$bblm_safe_input['tAid'] = $_POST['bblm_teama'];
	$bblm_safe_input['tBid'] = $_POST['bblm_teamb'];
	$bblm_safe_input['tAtv'] = $_POST['tAtr'];
	$bblm_safe_input['tBtv'] = $_POST['tBtr'];
	$bblm_safe_input['tAtd'] = $_POST['tAtd'];
	$bblm_safe_input['tBtd'] = $_POST['tBtd'];
	$bblm_safe_input['tAcas'] = $_POST['tAcas'];
	$bblm_safe_input['tBcas'] = $_POST['tBcas'];
	$bblm_safe_input['tAint'] = $_POST['tAint'];
	$bblm_safe_input['tBint'] = $_POST['tBint'];
	$bblm_safe_input['tAcomp'] = $_POST['tAcomp'];
	$bblm_safe_input['tBcomp'] = $_POST['tBcomp'];
	$bblm_safe_input['tAatt'] = $_POST['tAatt'];
	$bblm_safe_input['tBatt'] = $_POST['tBatt'];
	$bblm_safe_input['tAwin'] = $_POST['tAwin'];
	$bblm_safe_input['tBwin'] = $_POST['tBwin'];
	$bblm_safe_input['tAff'] = $_POST['tAff'];
	$bblm_safe_input['tBff'] = $_POST['tBff'];
//	$bblm_safe_input['tAres'] = $_POST['teamAres']; //no longer used
//	$bblm_safe_input['tBres'] = $_POST['teamBres']; //no longer used


	/*
	 -- Obtain Team Information --
	*/
	$teamA = array(); //array to hold Team A information
	$teamB = array(); //array to hold Team B information

	$teamAsql = "SELECT t_id, t_name, t_sname, t_bank, t_tv, t_ff FROM bb_team WHERE t_id=".$bblm_safe_input['tAid'];
	if ($teamAdetails = $wpdb->get_results($teamAsql)) {
		foreach ($teamAdetails as $teamAd) {
			$teamA['id'] = $teamAd->t_id;
			$teamA['name'] = $teamAd->t_name;
			$teamA['sname'] = $teamAd->t_sname;
			$teamA['bank'] = $teamAd->t_bank;
			$teamA['tv'] = $teamAd->t_tv;
			$teamA['ff'] = $teamAd->t_ff;

		}
	}
	$teamBsql = "SELECT t_id, t_name, t_sname, t_bank, t_tv, t_ff FROM bb_team WHERE t_id=".$bblm_safe_input['tBid'];
	if ($teamBdetails = $wpdb->get_results($teamBsql)) {
		foreach ($teamBdetails as $teamBd) {
			$teamB['id'] = $teamBd->t_id;
			$teamB['name'] = $teamBd->t_name;
			$teamB['sname'] = $teamBd->t_sname;
			$teamB['bank'] = $teamBd->t_bank;
			$teamB['tv'] = $teamBd->t_tv;
			$teamB['ff'] = $teamBd->t_ff;

		}
	}

	/*
	 -- Generate WP Page information --
	*/
	//generate time NOW.
	$bblm_date_now = date('Y-m-j H:i:59');

	//get page # for parent from DB
	$options = get_option('bblm_config');
	$bblm_page_match = htmlspecialchars($options['page_match'], ENT_QUOTES);

	//Filter trivia and generate
	$bblm_removable = array("<pre>","</pre>","<p>","&nbsp;");
	$bblm_trivia_content = $bblm_safe_input['mtrivia'];
	$bblm_trivia_content = str_replace($bblm_removable,"",$bblm_trivia_content);
	$bblm_trivia_content = str_replace("</p>","\n\n",$bblm_trivia_content);

	$bblm_page_content = "No Report Filed Yet";

	//sanitise page title
	$bblm_page_title = $teamA['name']." vs ".$teamB['name'];

	//convert page title to slug
	$bblm_page_slug_raw = $_POST['mdate']."-".$teamA['sname']."-vs-".$teamB['sname'];
	$bblm_page_slug = sanitize_title($bblm_page_slug_raw);

	//generate GUID
	$bblm_guid = get_bloginfo('wpurl');
	$bblm_guid .= "/matches/";
	$bblm_guid .= $bblm_page_slug;

	/*
	 -- Generate stat totals --
	*/
	$mtottd = $bblm_safe_input['tAtd'] + $bblm_safe_input['tBtd'];
	$mtotcas = $bblm_safe_input['tAcas'] + $bblm_safe_input['tBcas'];
	$mtotint = $bblm_safe_input['tAint'] + $bblm_safe_input['tBint'];
	$mtotcomp = $bblm_safe_input['tAcomp'] + $bblm_safe_input['tBcomp'];

	/*
		 -- Determine Winner --
	*/
	if ($bblm_safe_input['tAtd'] > $bblm_safe_input['tBtd']) {
		$bblm_safe_input['tAres'] = "W";
		$bblm_safe_input['tBres'] = "L";
	}
	else if ($bblm_safe_input['tAtd'] < $bblm_safe_input['tBtd']) {
		$bblm_safe_input['tAres'] = "L";
		$bblm_safe_input['tBres'] = "W";
	}
	else {
		$bblm_safe_input['tAres'] = "D";
		$bblm_safe_input['tBres'] = "D";
	}

	/*
	 -- Work out changed team details --
	*/
	$teamA['bank'] = $teamA['bank'] + $bblm_safe_input['tAwin'];
	$teamA['ff'] = $teamA['ff'] + $bblm_safe_input['tAff'];
	//change in tv will be ff change x 10,000gp
	$teamA_ffinc = $bblm_safe_input['tAff']*10000;
	$teamA['tv'] = $teamA['tv'] + $teamA_ffinc;

	$teamB['bank'] = $teamB['bank'] + $bblm_safe_input['tBwin'];
	$teamB['ff'] = $teamB['ff'] + $bblm_safe_input['tBff'];
	//change in tv will be ff change x 10,000gp
	$teamB_ffinc = $bblm_safe_input['tBff']*10000;
	$teamB['tv'] = $teamB['tv'] + $teamB_ffinc;

	/*
	 -- Gather Information about Comp --
	*/
	$comp = array();
	$compdatasql = "SELECT c_counts, c_pW, c_pL, c_pD, c_ptd, c_pcas, c_pround FROM bb_comp WHERE c_id = ".$bblm_safe_input['mcomp'];
	if ($compdetails = $wpdb->get_results($compdatasql)) {
		foreach ($compdetails as $compd) {
			$comp['counts'] = $compd->c_counts;
			$comp['pW'] = $compd->c_pW;
			$comp['pL'] = $compd->c_pL;
			$comp['pD'] = $compd->c_pD;
			$comp['ptd'] = $compd->c_ptd;
			$comp['pcas'] = $compd->c_pcas;
			$comp['round'] = $compd->c_pround;

		}
	}
if ($comp['round']) {
	$tAcomp = array();
	$tBcomp = array();
	$teamAcompsql = "SELECT * FROM bb_team_comp WHERE t_id = ".$teamA['id']." AND c_id = ".$bblm_safe_input['mcomp']." AND div_id = ".$bblm_safe_input['mdiv'];
	if ($teamAcomp = $wpdb->get_results($teamAcompsql)) {
		foreach ($teamAcomp as $tAcompd) {
			$tAcomp['played'] = $tAcompd->tc_played;
			$tAcomp['points'] = $tAcompd->tc_points;
			$tAcomp['win'] = $tAcompd->tc_W;
			$tAcomp['lose'] = $tAcompd->tc_L;
			$tAcomp['draw'] = $tAcompd->tc_D;
		}
	}
	$teamBcompsql = "SELECT * FROM bb_team_comp WHERE t_id = ".$teamB['id']." AND c_id = ".$bblm_safe_input['mcomp']." AND div_id = ".$bblm_safe_input['mdiv'];
	if ($teamBcomp = $wpdb->get_results($teamBcompsql)) {
		foreach ($teamBcomp as $tBcompd) {
			$tBcomp['played'] = $tBcompd->tc_played;
			$tBcomp['points'] = $tBcompd->tc_points;
			$tBcomp['win'] = $tBcompd->tc_W;
			$tBcomp['lose'] = $tBcompd->tc_L;
			$tBcomp['draw'] = $tBcompd->tc_D;
		}
	}
}//end if $comp['round']


$postsql = 'INSERT INTO '.$wpdb->posts.' (`ID`, `post_author`, `post_date`, `post_date_gmt`, `post_content`, `post_title`, `post_category`, `post_excerpt`, `post_status`, `comment_status`, `ping_status`, `post_password`, `post_name`, `to_ping`, `pinged`, `post_modified`, `post_modified_gmt`, `post_content_filtered`, `post_parent`, `guid`, `menu_order`, `post_type`, `post_mime_type`, `comment_count`) VALUES (\'\', \'1\', \''.$bblm_date_now.'\', \''.$bblm_date_now.'\', \''.$bblm_page_content.'\', \''.$bblm_page_title.'\', \'0\', \'\', \'publish\', \'closed\', \'closed\', \'\', \''.$bblm_page_slug.'\', \'\', \'\', \''.$bblm_date_now.'\', \''.$bblm_date_now.'\', \'\', \''.$bblm_page_match.'\', \''.$bblm_guid.'\', \'0\', \'page\', \'\', \'0\')';



if (FALSE !== $wpdb->query($postsql)) {
	$bblm_post_number = $wpdb->insert_id;//captured from SQL string
}
else {
	$wpdb->print_error();
}

$postmetasql = 'INSERT INTO '.$wpdb->postmeta.' (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES (\'\', \''.$bblm_post_number.'\', \'_wp_page_template\', \'bb.view.match.php\')';

if (FALSE !== $wpdb->query($postmetasql)) {
	$sucess = TRUE;
}
else {
	$wpdb->print_error();
}


//$matchsql = 'INSERT INTO `bb_match` (`m_id`, `c_id`, `div_id`, `m_date`, `m_gate`, `m_teamA`, `m_teamB`, `m_teamAtd`, `m_teamBtd`, `m_tottd`, `m_totcas`, `m_totint`, `m_totcomp`, `weather_id`, `weather_id2`, `m_trivia`, `m_complete`, `stad_id`) VALUES (\'\', \''.$bblm_safe_input['mcomp'].'\', \''.$bblm_safe_input['mdiv'].'\', \''.$bblm_safe_input['mdate'].'\', \''.$bblm_safe_input['mgate'].'\', \''.$teamA['id'].'\', \''.$teamB['id'].'\', \''.$bblm_safe_input['tAtd'].'\', \''.$bblm_safe_input['tBtd'].'\', \''.$mtottd.'\', \''.$mtotcas.'\', \''.$mtotint.'\', \''.$mtotcomp.'\', \''.$bblm_safe_input['mweather1'].'\', \''.$bblm_safe_input['mweather2'].'\', \''.$bblm_trivia_content.'\', \'0\', \''.$bblm_safe_input['mstad'].'\')';
$matchsql = 'INSERT INTO `bb_match` (`m_id`, `c_id`, `div_id`, `m_date`, `m_gate`, `m_teamA`, `m_teamB`, `m_teamAtd`, `m_teamBtd`, `m_teamAcas`, `m_teamBcas`, `m_tottd`, `m_totcas`, `m_totint`, `m_totcomp`, `weather_id`, `weather_id2`, `m_trivia`, `m_complete`, `stad_id`) VALUES (\'\', \''.$bblm_safe_input['mcomp'].'\', \''.$bblm_safe_input['mdiv'].'\', \''.$bblm_safe_input['mdate'].'\', \''.$bblm_safe_input['mgate'].'\', \''.$teamA['id'].'\', \''.$teamB['id'].'\', \''.$bblm_safe_input['tAtd'].'\', \''.$bblm_safe_input['tBtd'].'\', \''.$bblm_safe_input['tAcas'].'\', \''.$bblm_safe_input['tBcas'].'\', \''.$mtottd.'\', \''.$mtotcas.'\', \''.$mtotint.'\', \''.$mtotcomp.'\', \''.$bblm_safe_input['mweather1'].'\', \''.$bblm_safe_input['mweather2'].'\', \''.$bblm_trivia_content.'\', \'0\', \''.$bblm_safe_input['mstad'].'\')';

if (FALSE !== $wpdb->query($matchsql)) {
	$bblm_match_number = $wpdb->insert_id;//captured from SQL string
}

$matchteamsql = 'INSERT INTO `bb_match_team` (`m_id`, `t_id`, `mt_td`, `mt_cas`, `mt_int`, `mt_comp`, `mt_winnings`, `mt_att`, `mt_ff`, `mt_result`, `mt_tv`, `mt_comment`) VALUES (\''.$bblm_match_number.'\', \''.$teamA['id'].'\', \''.$bblm_safe_input['tAtd'].'\', \''.$bblm_safe_input['tAcas'].'\', \''.$bblm_safe_input['tAint'].'\', \''.$bblm_safe_input['tAcomp'].'\', \''.$bblm_safe_input['tAatt'].'\', \''.$bblm_safe_input['tAwin'].'\', \''.$bblm_safe_input['tAff'].'\', \''.$bblm_safe_input['tAres'].'\', \''.$bblm_safe_input['tAtv'].'\', \''.$bblm_safe_input['tAnotes'].'\'), (\''.$bblm_match_number.'\', \''.$teamB['id'].'\', \''.$bblm_safe_input['tBtd'].'\', \''.$bblm_safe_input['tBcas'].'\', \''.$bblm_safe_input['tBint'].'\', \''.$bblm_safe_input['tBcomp'].'\', \''.$bblm_safe_input['tBatt'].'\', \''.$bblm_safe_input['tBwin'].'\', \''.$bblm_safe_input['tBff'].'\', \''.$bblm_safe_input['tBres'].'\', \''.$bblm_safe_input['tBtv'].'\', \''.$bblm_safe_input['tBnotes'].'\')';

if (FALSE !== $wpdb->query($matchteamsql)) {
	$sucess = TRUE;
}


if (FALSE !== $comp['counts']) {
//team update (only if comp counts towards stats)
$teamAupdatesql = 'UPDATE `bb_team` SET `t_ff` = \''.$teamA['ff'].'\', `t_bank` = \''.$teamA['bank'].'\', `t_tv` = \''.$teamA['tv'].'\' WHERE `t_id` = \''.$teamA['id'].'\' LIMIT 1';

if (FALSE !== $wpdb->query($teamAupdatesql)) {
	$sucess = TRUE;
}

$teamBupdatesql = 'UPDATE `bb_team` SET `t_ff` = \''.$teamB['ff'].'\', `t_bank` = \''.$teamB['bank'].'\', `t_tv` = \''.$teamB['tv'].'\' WHERE `t_id` = \''.$teamB['id'].'\' LIMIT 1';
//teamcomp update

if (FALSE !== $wpdb->query($teamBupdatesql)) {
	$sucess = TRUE;
}

//work out points values
$tApointsinc = $comp['p'.$bblm_safe_input['tAres']];
$tBpointsinc = $comp['p'.$bblm_safe_input['tBres']];

if ($comp['round']) {
	//work out existing points:
	$tApointsnow = (($tAcomp['win']*$comp['pW'])+($tAcomp['lose']*$comp['pL'])+($tAcomp['draw']*$comp['pD']));
	$tBpointsnow = (($tBcomp['win']*$comp['pW'])+($tBcomp['lose']*$comp['pL'])+($tBcomp['draw']*$comp['pD']));

	$tApointsinc = (($tApointsnow+$comp['p'.$bblm_safe_input['tAres']])/($tAcomp['played']+1));
	$tBpointsinc = (($tBpointsnow+$comp['p'.$bblm_safe_input['tBres']])/($tBcomp['played']+1));
}


//Generate the team_comp update queries
if ($comp['round']) {
	//If the comp rounds the points then there is a slightly differnt sql string (in determining points values)
	$tAcompsql = 'UPDATE `bb_team_comp` SET `tc_played` = tc_played+1, `tc_'.$bblm_safe_input['tAres'].'` = tc_'.$bblm_safe_input['tAres'].'+1, `tc_tdfor` = tc_tdfor+'.$bblm_safe_input['tAtd'].', `tc_tdagst` = tc_tdagst+'.$bblm_safe_input['tBtd'].', `tc_casfor` = tc_casfor+'.$bblm_safe_input['tAcas'].', `tc_casagst` = tc_casagst+'.$bblm_safe_input['tBcas'].', `tc_int` = tc_int+'.$bblm_safe_input['tAint'].', `tc_comp` = tc_comp+'.$bblm_safe_input['tAcomp'].', `tc_points` = '.$tApointsinc.' WHERE t_id = '.$teamA['id'].' AND c_id = '.$bblm_safe_input['mcomp'].' AND div_id = '.$bblm_safe_input['mdiv'];
	$tBcompsql = 'UPDATE `bb_team_comp` SET `tc_played` = tc_played+1, `tc_'.$bblm_safe_input['tBres'].'` = tc_'.$bblm_safe_input['tBres'].'+1, `tc_tdfor` = tc_tdfor+'.$bblm_safe_input['tBtd'].', `tc_tdagst` = tc_tdagst+'.$bblm_safe_input['tAtd'].', `tc_casfor` = tc_casfor+'.$bblm_safe_input['tBcas'].', `tc_casagst` = tc_casagst+'.$bblm_safe_input['tAcas'].', `tc_int` = tc_int+'.$bblm_safe_input['tBint'].', `tc_comp` = tc_comp+'.$bblm_safe_input['tBcomp'].', `tc_points` = '.$tBpointsinc.' WHERE t_id = '.$teamB['id'].' AND c_id = '.$bblm_safe_input['mcomp'].' AND div_id = '.$bblm_safe_input['mdiv'];
}
else {
	$tAcompsql = 'UPDATE `bb_team_comp` SET `tc_played` = tc_played+1, `tc_'.$bblm_safe_input['tAres'].'` = tc_'.$bblm_safe_input['tAres'].'+1, `tc_tdfor` = tc_tdfor+'.$bblm_safe_input['tAtd'].', `tc_tdagst` = tc_tdagst+'.$bblm_safe_input['tBtd'].', `tc_casfor` = tc_casfor+'.$bblm_safe_input['tAcas'].', `tc_casagst` = tc_casagst+'.$bblm_safe_input['tBcas'].', `tc_int` = tc_int+'.$bblm_safe_input['tAint'].', `tc_comp` = tc_comp+'.$bblm_safe_input['tAcomp'].', `tc_points` = tc_points+'.$tApointsinc.' WHERE t_id = '.$teamA['id'].' AND c_id = '.$bblm_safe_input['mcomp'].' AND div_id = '.$bblm_safe_input['mdiv'];
	$tBcompsql = 'UPDATE `bb_team_comp` SET `tc_played` = tc_played+1, `tc_'.$bblm_safe_input['tBres'].'` = tc_'.$bblm_safe_input['tBres'].'+1, `tc_tdfor` = tc_tdfor+'.$bblm_safe_input['tBtd'].', `tc_tdagst` = tc_tdagst+'.$bblm_safe_input['tAtd'].', `tc_casfor` = tc_casfor+'.$bblm_safe_input['tBcas'].', `tc_casagst` = tc_casagst+'.$bblm_safe_input['tAcas'].', `tc_int` = tc_int+'.$bblm_safe_input['tBint'].', `tc_comp` = tc_comp+'.$bblm_safe_input['tBcomp'].', `tc_points` = tc_points+'.$tBpointsinc.' WHERE t_id = '.$teamB['id'].' AND c_id = '.$bblm_safe_input['mcomp'].' AND div_id = '.$bblm_safe_input['mdiv'];
}

//Run the team_comp update queries
if (FALSE !== $wpdb->query($tAcompsql)) {
	$sucess = TRUE;
}



if (FALSE !== $wpdb->query($tBcompsql)) {
	$sucess = TRUE;
}


} //end of if (FALSE !== $comp['counts'])



$joinsql = 'INSERT INTO `bb2wp` (`bb2wp_id`, `tid`, `pid`, `prefix`) VALUES (\'\', \''.$bblm_match_number.'\', \''.$bblm_post_number.'\', \'m_\')';

if (FALSE !== $wpdb->query($joinsql)) {
	$sucess = TRUE;
	$finished = 1;
}
else {
	$wpdb->print_error();
}

//  Fixture completion stuff //
if ($_POST['bblm_fid']) {
	$completefixturesql = 'UPDATE `bb_fixture` SET `f_complete` = \'1\' WHERE `f_id` = '.$_POST['bblm_fid'].' LIMIT 1';
	$wpdb->query($completefixturesql);
	//now we check to see if it was part of a tournament
	$checkbracketssql = 'SELECT cb_order FROM bb_comp_brackets WHERE f_id = '.$_POST['bblm_fid'];
	$cb_order = $wpdb->get_var($checkbracketssql);
	if (!empty($cb_order)) {
		print("<p>We have a bracket on our hands</p>");
		$updatebracketsql = 'UPDATE `bb_comp_brackets` SET `m_id` = \''.$bblm_match_number.'\', `f_id` = \'0\', `cb_text` = \''.$teamA['name'].' <strong>'.$bblm_safe_input['tAtd'].'</strong><br />'.$teamB['name'].' <strong>'.$bblm_safe_input['tBtd'].'</strong>\' WHERE `c_id` = \''.$bblm_safe_input['mcomp'].'\' AND `div_id` = \''.$bblm_safe_input['mdiv'].'\' AND `cb_order` = '.$cb_order.' LIMIT 1';
		$wpdb->query($updatebracketsql);
	}
}

// Now we flush the re-write rules to make them regenerate the rules to include our new page.
	$wp_rewrite->flush_rules();


?>
	<div id="updated" class="updated fade">
	<p>
	<?php
	if ($sucess) {
		print("Match has been recorded. <a href=\"".$bblm_guid."\" title=\"View the new team\">View page</a>");
	}
	else {
		print("Something went wrong");
	}
	?>
</p>
	</div>
<?php

//end of submit if
}

  //////////////////////////////////
 //  Main Stage - Match Details  //
//////////////////////////////////
if(isset($_POST['bblm_matchcomp_select'])) {
	//actual page content goes here.

/*	print("<pre>");
	print_r($_POST);
	print("</pre>");
	print("<hr />");*/

	//  Common elements of form  //
?>
	<form name="bblm_editcompteam" method="post" id="post">
		<script type="text/javascript" src="../wp-includes/js/tinymce/tiny_mce.js"></script>
		<script type="text/javascript">
		<!--
			tinyMCE.init({
			theme : "advanced",
			mode : "exact",
			elements : "matchtrivia",
			width : "565",
			height : "200"
			});
		-->
	</script>

	<fieldset><legend>Enter match details</legend>

<?php
	$f_id = $_POST['bblm_fid'];

	if ("F" == $_POST['bblm_mtype']) {
		$fixturedetailsql = 'SELECT F.f_id, UNIX_TIMESTAMP(F.f_date) AS mdate, C.c_name, F.c_id, D.div_name, D.div_id, T.t_name AS TA, R.t_name AS TB, T.t_tv AS TAtv, R.t_tv AS TBtv, T.stad_id, F.f_teamA, F.f_teamB FROM bb_fixture F, bb_comp C, bb_division D, bb_team T, bb_team R WHERE F.f_teamA = T.t_id AND F.f_teamB = R.t_id AND F.c_id = C.c_id AND F.div_id = D.div_id AND F.f_complete = 0 AND F.f_id = '.$f_id;
		if ($fixturedt = $wpdb->get_results($fixturedetailsql)) {
				foreach ($fixturedt as $fd) {
					$comp_name = $fd->c_name;
					$div_name = $fd->div_name;
					$comp_id = $fd->c_id;
					$div_id = $fd->div_id;


?>
	<ul>
	  <li><strong>Competition</strong>: <?php print($comp_name); ?></li>
	  <li><strong>Division</strong>: <?php print($div_name); ?></li>
	</ul>

	<table>
		<tr>
			<th>&nbsp;</th>
			<th>Team A</th>
			<th>&nbsp;</th>
			<th>Team B</th>
			<th>&nbsp;</th>
		</tr>
		<tr>
			<td>Teams</td>
			<td><strong><?php print($fd->TA); ?></strong><input name="bblm_teama" type="hidden" value="<?php print($fd->f_teamA); ?>"></td>
			<td>&nbsp;</td>
			<td><strong><?php print($fd->TB); ?></strong><input name="bblm_teamb" type="hidden" value="<?php print($fd->f_teamB); ?>"></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>Team Value:</td>
			<td><input name="tAtr" type="text" size="7" maxlength="7" value="<?php print($fd->TAtv); ?>"></td>
			<td>Vs</td>
			<td><input name="tBtr" type="text" size="7" maxlength="7" value="<?php print($fd->TBtv); ?>"></td>
			<td class="comment">The team value <em>before</em> the game.</td>
		</tr>
		<tr>
			<td>Date:</td>
			<td colspan="3"><input name="mdate" type="text" size="12" maxlength="10" value="<?php print(date('Y-m-d', $fd->mdate)); ?>"></td>
			<td class="comment">This is the scheduled date, feel free to change it..</td>
		</tr>
		<tr>
			<td>Location:</td>
			<td colspan="3">
				<select name="mstad" id="mstad">
<?php
				$stadsql = 'SELECT S.* FROM bb_stadium S, bb2wp J, '.$wpdb->posts.' P WHERE S.stad_id = J.tid AND J.prefix = \'stad_\' AND J.pid = P.ID ORDER BY S.stad_name';
				if ($stadiums = $wpdb->get_results($stadsql)) {
					foreach ($stadiums as $stad) {
						print("<option value=\"".$stad->stad_id."\">".$stad->stad_name."</option>\n");
					}
				}
				?>
				</select></td>
			<td class="comment">The Stadium where this game took place.</td>
		</tr>
<?php
				}//end of for each fixture detail
		}//end of sql statement
		else {
			//sql failed
			print("<p>".$fixturedetailsql."</p>");
		}



		$is_fixture = 1;
	} //end of is_fixture
	else {
		//else it is a new match selection
		//SQL to gather names and IDs of Competitions and Division
		$compsql = "SELECT c_name, c_id FROM bb_comp WHERE c_id = ".$_POST['bblm_mcomp'];
		$divsql = "SELECT div_name, div_id FROM bb_division WHERE div_id = ".$_POST['bblm_mdiv'];


		//Detemining the comp / div name and ID
		if ($result = $wpdb->get_results($compsql)) {
			foreach ($result as $res) {
				$comp_name = $res->c_name;
				$comp_id = $res->c_id;
			}
		}
		if ($result = $wpdb->get_results($divsql)) {
			foreach ($result as $res) {
				$div_name = $res->div_name;
				$div_id = $res->div_id;
			}
		}
?>
		<ul>
		  <li><strong>Competition</strong>: <?php print($comp_name); ?></li>
		  <li><strong>Division</strong>: <?php print($div_name); ?></li>
		</ul>

<table>
<tr><th>&nbsp;</th><th>Team A</th><th>&nbsp;</th><th>Team B</th><th>&nbsp;</th></tr>

<tr><td>Teams</td>
	<?php
	$existingteamssql = "SELECT T.t_name, T.t_id FROM bb_team T, bb_team_comp C WHERE T.t_id = C.t_id AND C.c_id = ".$_POST['bblm_mcomp']." AND C.div_id = ".$_POST['bblm_mdiv'];
	if ($extteam = $wpdb->get_results($existingteamssql)) {
		//copy for later. should be replaced with a reset object or something.
		$extteam2 = $extteam;

		print("<td><select name=\"bblm_teama\" id=\"bblm_teama\">");
		foreach ($extteam as $et) {
			print ("<option value=\"".$et->t_id."\">".$et->t_name."</option>\n");
		}
		print("</select>\n");

		print("</td><td>&nbsp;</td><td>");

		print("<select name=\"bblm_teamb\" id=\"bblm_teamb\">");
		foreach ($extteam2 as $et2) {
			print ("<option value=\"".$et2->t_id."\">".$et2->t_name."</option>\n");
		}
		print("</select></td><td>&nbsp;</td></tr>");
	}
	else {
		?>
		<td colspan="3">There are currently no teams in this competition! Please <a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.edit.comp_team.php" title="Assign teams to a Competition">Add some first</a></td></tr>
		<?php

	}
	?>
<tr><td>Team Value:</td><td><input name="tAtr" type="text" size="7" maxlength="7" value="1000000"></td><td>Vs</td><td><input name="tBtr" type="text" size="7" maxlength="7" value="1000000"></td><td class="comment">The team value <em>before</em> the game.</td></tr>

<tr><td>Date:</td><td colspan="3"><input name="mdate" type="text" size="12" maxlength="10" value="<?php print(date('Y-m-d')); ?>"></td><td class="comment">The date the game took place.</td></tr>

<tr><td>Location:</td><td colspan="3">
		  <select name="mstad" id="mstad">
		<?php
		$stadsql = 'SELECT S.* FROM bb_stadium S, bb2wp J, '.$wpdb->posts.' P WHERE S.stad_id = J.tid AND J.prefix = \'stad_\' AND J.pid = P.ID ORDER BY S.stad_name';

		if ($stadiums = $wpdb->get_results($stadsql)) {
			foreach ($stadiums as $stad) {
				print("<option value=\"".$stad->stad_id."\">".$stad->stad_name."</option>\n");
			}
		}
		?>
		</select></td><td class="comment">The Stadium where this game took place.</td></tr>
<?php
} //end of if fixture, else match
//now normal flow of page resumes.
?>



<tr><td>Score:</td><td><input name="tAtd" type="text" size="3" maxlength="2" value="0"></td><td>Vs</td><td><input name="tBtd" type="text" size="3" maxlength="2" value="0"></td><td class="comment">The FInal Score of the teams</td></tr>
<tr><td>Casualties:</td><td><input name="tAcas" type="text" size="3" maxlength="2" value="0"></td><td>Vs</td><td><input name="tBcas" type="text" size="3" maxlength="2" value="0"></td><td class="comment">casualties caused by each team (Players only).</td></tr>

<tr><td>Interceptions:</td><td><input name="tAint" type="text" size="3" maxlength="2" value="0"></td><td>Vs</td><td><input name="tBint" type="text" size="3" maxlength="2" value="0"></td><td class="comment">Number of Interceptions for each team.</td></tr>
<tr><td>Completions:</td><td><input name="tAcomp" type="text" size="3" maxlength="2" value="0"></td><td>Vs</td><td><input name="tBcomp" type="text" size="3" maxlength="2" value="0"></td><td class="comment">Number of Completions for each team.</td></tr>

<tr><td>Attendance:</td><td><input name="tAatt" type="text" size="6" maxlength="6" value="2000"></td><td>Vs</td><td><input name="tBatt" type="text" size="6" maxlength="6" value="2000"></td><td class="comment">Number of fans from each team.</td></tr>

<tr><td>Gate:</td><td colspan="3"><input name="gate" type="text" size="6" maxlength="6" value="4000"></td><td class="comment">The <strong>total</strong> number of fans in attendence.</td></tr>

<tr><td>Winnings:</td><td><input name="tAwin" type="text" size="6" maxlength="6" value="10000"></td><td>Vs</td><td><input name="tBwin" type="text" size="6" maxlength="6" value="10000"></td><td class="comment">Include extras for winning, tornament finals etc.</td></tr>
<tr><td>Match Trivia:</td><td colspan="3"><textarea name="matchtrivia" cols="80" rows="6"></textarea></td><td class="comment">Points of note, injuries, debuts, etc.</td></tr>
<tr><td>Weather:</td><td><select id="mweather" name="mweather"><option value="1">Nice</option><option value="2">Very Sunny</option><option value="3">Blizzard</option><option value="4">Pouring Rain</option><option value="5">Sweltering Heat</option>
</select></td><td> - </td><td><select id="mweather2" name="mweather2"><option value="1">Nice</option><option value="2">Very Sunny</option><option value="3">Blizzard</option><option value="4">Pouring Rain</option><option value="5">Sweltering Heat</option>
</select></td><td class="comment">The weather for the match. Please enter values for 1<sup>st</sup> and 2<sup>nd</sup> half.</td></tr>
<tr><td>Fan Factor:</td><td><input name="tAff" type="text" size="3" maxlength="2" value="0"></td><td>Vs</td><td><input name="tBff" type="text" size="3" maxlength="2" value="0"></td><td class="comment">The change in FF (if any).</td></tr>

<!-- <tr><td>Result:</td><td><select id="tAres" name="teamAres">
<option value="W">Win</option><option value="L">Loss</option><option value="D" selected="selected">Draw</option></select></td><td>Vs</td><td><select id="tBres" name="teamBres">
<option value="W">Win</option><option value="L">Loss</option><option value="D" selected="selected">Draw</option></select></td><td class="comment">No Comment</td></tr> -->
<tr><td>Comments:</td><td><textarea name="tAnotes" cols="40" rows="8">No comment</textarea></td><td>Vs</td><td><textarea name="tBnotes" cols="40" rows="8">No Comment</textarea></td><td class="comment">Any team specific comments, coach comments etc</td></tr>
</table>
</fieldset>



	<input type="hidden" name="bblm_comp" size="3" value="<?php print($comp_id); ?>">
	<input type="hidden" name="bblm_div" size="2" value="<?php print($div_id); ?>">
<?php
	if ($is_fixture) {
		print("<input type=\"hidden\" name=\"bblm_fid\" size=\"3\" value=\"".$f_id."\">\n");
	}

?>

	<p class="submit">
	<input type="submit" name="bblm_match_add" tabindex="4" value="Submit match details" title="submit match details"/>
	</p>

	</form>
<?php

}
else if (!isset($finished)) {
  ////////////////////////////////////////////
 // First Stage - Comp / Fixture Selection //
////////////////////////////////////////////
?>
	<form name="bblm_addmatch" method="post" id="post">

	<p>There are two ways of recording a match details, you can select the details from a fixture, or select the Competition and division that the match took place in:</p>

	<p><input type="radio" value="M" name="bblm_mtype" checked=\"yes\"> New Match - Select a Competition and Division below:</p>
	<fieldset id='addmatchdiv'><legend>Select a Competition</legend>

	  <label for="bblm_mcomp" class="selectit">Competition:</label>
	  <select name="bblm_mcomp" id="bblm_mcomp">
	<?php
	$compsql = 'SELECT c_id, c_name FROM bb_comp WHERE c_active = 1 order by c_name';
	//This line should work but for some reason prpduces blanks!
	//$compsql = 'SELECT C.c_id, C.c_name FROM bb_comp C, bb2wp J, dev_posts P WHERE C.c_id = J.tid AND J.prefix = \'c_\' AND J.pid = P.ID AND C.c_active = 1 ORDER BY C.c_name ASC LIMIT';
	if ($comps = $wpdb->get_results($compsql)) {
		foreach ($comps as $comp) {
			print("<option value=\"".$comp->c_id."\">".$comp->c_name."</option>\n");
		}
	}
	?>
	</select>

	  <label for="bblm_mdiv" class="selectit">Division:</label>
	  <select name="bblm_mdiv" id="bblm_mdiv">
	<?php
	$divsql = 'SELECT div_id, div_name FROM bb_division ORDER BY div_id';
	if ($divs = $wpdb->get_results($divsql)) {
		foreach ($divs as $div) {
			print("<option value=\"".$div->div_id."\">".$div->div_name."</option>\n");
		}
	}
	?>
	</select>
	</fieldset>

<p><input type="radio" value="F" name="bblm_mtype"> Fixture - Please select the fixture from the below list:</p>
	<fieldset id='selectfixturediv'><legend>Select a Fixture</legend>
	  <label for="bblm_fid" class="selectit">Fixture:</label>
	  <select name="bblm_fid" id="bblm_fid">
	<?php
	$fixturesql = 'SELECT F.f_id, UNIX_TIMESTAMP(F.f_date) AS mdate, C.c_name, D.div_name, T.t_name AS TA, R.t_name AS TB FROM bb_fixture F, bb_comp C, bb_division D, bb_team T, bb_team R WHERE F.f_teamA = T.t_id AND F.f_teamB = R.t_id AND F.c_id = C.c_id AND F.div_id = D.div_id AND F.f_complete = 0 ORDER BY F.c_id DESC, F.div_id DESC, F.f_date ASC';
	if ($fixtures = $wpdb->get_results($fixturesql)) {
		foreach ($fixtures as $fd) {
			print("<option value=\"".$fd->f_id."\">".$fd->TA." vs ".$fd->TB." (".$fd->c_name."/".$fd->div_name." - ".date("d.m.y", $fd->mdate).")</option>\n");
		}
	}
	else {
		print("<option vlaue=\"x\">There are currently no fixtures lined up, please use the above option</option>\n");
	}
	?>
	</select>

	</fieldset>
	<p class="submit">
	<input type="submit" name="bblm_matchcomp_select" tabindex="4" value="Continue" title="Continue with selection"/>
	</p>
	</form>
<?php
} //end of else section
?>

</div>
