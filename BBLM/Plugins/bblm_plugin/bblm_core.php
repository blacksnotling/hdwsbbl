<?php
/*
Plugin Name: Blood Bowl League Manager System (BBLM)
Plugin URI: http://www.hdwsbbl.co.uk/
Description: Everthing you need to run a Blood Bowl League via Wordpress!
Version: 1.1.1
Author: Blacksnotling
Author URI: http://www.hdwsbbl.co.uk/
*/
//stop people from accessing the file directly and causing errors.
if (!function_exists('add_action')) die('You cannot run this file directly. Naughty Person');

/************ Declaration / insertion of Admin Pages **********/
function bblm_insert_admin_pages() {
	//Addition of Top level admin pages

	add_menu_page('League Admin', 'BB: League Admin', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.core.welcome.php');
	add_menu_page('Match Management', 'BB: Match Admin', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.core.matchmanagement.php');
	add_menu_page('Team Management', 'BB: Team Admin', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.core.teamm.php');

	//Adds the subpages to the master heading - League Admin Pages
add_submenu_page('bblm_plugin/pages/bb.admin.core.welcome.php', 'BB Settings', 'BB Settings', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.edit.options.php');
add_submenu_page('bblm_plugin/pages/bb.admin.core.welcome.php', 'Did You Know', 'Did You Know', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.manage.dyk.php');
add_submenu_page('bblm_plugin/pages/bb.admin.core.welcome.php', 'New Season', 'New Season', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.add.season.php');
add_submenu_page('bblm_plugin/pages/bb.admin.core.welcome.php', 'Add Cup', 'Add Cup', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.add.series.php');
add_submenu_page('bblm_plugin/pages/bb.admin.core.welcome.php', 'Add Competition', 'Add Competition', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.add.comp.php');
add_submenu_page('bblm_plugin/pages/bb.admin.core.welcome.php', 'Manage Comps', 'Manage Comps', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.manage.comps.php');
add_submenu_page('bblm_plugin/pages/bb.admin.core.welcome.php', 'Assign teams (comp)', 'Assign teams (comp)', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.edit.comp_team.php');
add_submenu_page('bblm_plugin/pages/bb.admin.core.welcome.php', 'Set-up Brackets (comp)', 'Set-up Brackets', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.add.comp_brackets.php');
add_submenu_page('bblm_plugin/pages/bb.admin.core.welcome.php', 'Edit Brackets (comp)', 'Edit Brackets', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.edit.comp_brackets.php');
add_submenu_page('bblm_plugin/pages/bb.admin.core.welcome.php', 'Add Stadium', 'Add Stadium', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.add.stadium.php');
add_submenu_page('bblm_plugin/pages/bb.admin.core.welcome.php', 'Create an Award', 'Create Award', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.add.award.php');
add_submenu_page('bblm_plugin/pages/bb.admin.core.welcome.php', 'Close a Competition', 'Close Comp', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.end.comp.php');
add_submenu_page('bblm_plugin/pages/bb.admin.core.welcome.php', 'Close a Season', 'Close Sea', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.end.season.php');
add_submenu_page('bblm_plugin/pages/bb.admin.core.welcome.php', 'Generate Weekly Summary', 'Gen Summary', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.generate.summary.php');

//Adds the subpages to the master heading - Match Management Pages
add_submenu_page('bblm_plugin/pages/bb.admin.core.matchmanagement.php', 'Record Match', 'Record Match', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.add.match.php');
add_submenu_page('bblm_plugin/pages/bb.admin.core.matchmanagement.php', 'Record Player Actions', 'Player Actions', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.add.match_player.php');
add_submenu_page('bblm_plugin/pages/bb.admin.core.matchmanagement.php', 'Edit Match details', 'Edit Match', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.edit.match.php');
add_submenu_page('bblm_plugin/pages/bb.admin.edit.match.php', 'Edit Match Trivia', 'Edit Match Trivia', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.edit.match_trivia.php');
add_submenu_page('bblm_plugin/pages/bb.admin.core.matchmanagement.php', 'Add Fixture', 'Add Fixture', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.add.fixture.php');
add_submenu_page('bblm_plugin/pages/bb.admin.core.matchmanagement.php', 'Edit Fixture', 'Edit Fixture', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.edit.fixture.php');

//Adds the subpages to the master heading - Team Management Pages
add_submenu_page('bblm_plugin/pages/bb.admin.core.teamm.php', 'Add Team', 'Add Team', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.add.team.php');
add_submenu_page('bblm_plugin/pages/bb.admin.core.teamm.php', 'Manage Teams', 'Manage Teams', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.edit.team.php');
add_submenu_page('bblm_plugin/pages/bb.admin.core.teamm.php', 'Add Race', 'Add Race', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.add.race.php');
add_submenu_page('bblm_plugin/pages/bb.admin.core.teamm.php', 'Add Position', 'Add Position', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.add.position.php');
add_submenu_page('bblm_plugin/pages/bb.admin.core.teamm.php', 'Add Player', 'Add Player', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.add.player.php');
add_submenu_page('bblm_plugin/pages/bb.admin.core.teamm.php', 'Edit Player', 'Edit Player', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.edit.player.php');
add_submenu_page('bblm_plugin/pages/bb.admin.core.teamm.php', 'Add Star', 'Add Star', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.add.star.php');
add_submenu_page('bblm_plugin/pages/bb.admin.core.teamm.php', 'JM Report', 'JM Report', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.report.jm.php');

}
add_action('admin_menu', 'bblm_insert_admin_pages');



/************ Update TV function. Version 0.2 (20100123) **********/
function bblm_update_tv($tid) {
	global $wpdb;

	//Calculate worth of players
	$playervaluesql = 'SELECT SUM(P.p_cost_ng) FROM '.$wpdb->prefix.'player P WHERE P.p_status = 1 AND P.t_id = '.$tid;
	$tpvalue = $wpdb->get_var($playervaluesql);

	//Calcuate worth of rest of team (re-rolls, Assistant Coaches etc).
	$teamextravaluesql = 'SELECT SUM((R.r_rrcost*T.t_rr)+(T.t_ff*10000)+(T.t_cl*10000)+(T.t_ac*10000)+(T.t_apoc*50000)) AS TTOTAL FROM '.$wpdb->prefix.'team T, '.$wpdb->prefix.'race R WHERE R.r_id = T.r_id AND T.t_id = '.$tid;
	$tevalue = $wpdb->get_var($teamextravaluesql);

	//Add the two together
	$newtv = $tpvalue+$tevalue;

	//Generate SQL
	$sql = 'UPDATE `'.$wpdb->prefix.'team` SET `t_tv` = \''.$newtv.'\' WHERE `t_id` = '.$tid.' LIMIT 1';
	//Execute SQL
	//print("<h3>".$sql."</h3>");
	if (FALSE !== $wpdb->query($sql)) {
		$sucess = TRUE;
	}
	return true;
}
/************ Update Player function. Version 1.0b (20100123) **********/
function bblm_update_player($pid, $counts = 1) {
	//takes in two values, the player ID and a bool to see if only matches that count should be included
	global $wpdb;

	$playersppsql = 'SELECT SUM(M.mp_spp) FROM '.$wpdb->prefix.'match_player M WHERE M.p_id = '.$pid.' AND M.mp_spp > 0';
	if ($counts) {
		$playersppsql .= " AND M.mp_counts = 1";
	}
	$pspp = $wpdb->get_var($playersppsql);

	//Generate SQL
	$sql = 'UPDATE `'.$wpdb->prefix.'player` SET `p_spp` = \''.$pspp.'\' WHERE `p_id` = \''.$pid.'\' LIMIT 1';
	//Execute SQL
	if (FALSE !== $wpdb->query($sql)) {
		$sucess = TRUE;
	}
	return true;
}

/**
 * Defnes a new capability, bblm_manage_league which is used to authorise access to the acmin section
 * http://www.garyc40.com/2010/04/ultimate-guide-to-roles-and-capabilities/
 *
 */
add_action( 'init', 'bblm_roles_init' );

function bblm_roles_init() {
	$roles_object = get_role( 'administrator' );
	$roles_object->add_cap('bblm_manage_league');
}

/**
 * Defnes the new Taxonomies used within BBLM
 *
 */
function bblm_tax_init() {
	bblm_tax_team_init();
	bblm_tax_comp_init();
}

function bblm_tax_team_init() {
  // create a new taxonomy
  register_taxonomy(
    'post_teams',
    'post',
    array(
      'label' => __('Teams'),
      'sort' => true,
      'args' => array('orderby' => 'term_order'),
      'rewrite' => array('slug' => 'team-post'),
    )
  );
}

function bblm_tax_comp_init() {
  // create a new taxonomy
  register_taxonomy(
    'post_competitions',
    'post',
    array(
      'label' => __('Competitions'),
      'sort' => true,
      'args' => array('orderby' => 'term_order'),
      'rewrite' => array('slug' => 'competition-post'),
    )
  );
}
add_action( 'init', 'bblm_tax_init' );
?>
