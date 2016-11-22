<?php
/*
Plugin Name: Blood Bowl League Manager System (BBLM)
Plugin URI: http://www.hdwsbbl.co.uk/
Description: Everthing you need to run a Blood Bowl League via Wordpress!
Version: 1.5
Author: Blacksnotling
Author URI: http://www.hdwsbbl.co.uk/
*/
//stop people from accessing the file directly and causing errors.
if (!function_exists('add_action')) die('You cannot run this file directly. Naughty Person');

/************ Custom Login Box **********/

function bblm_custom_login() {
	echo '<link rel="stylesheet" type="text/css" href="' . get_settings('siteurl') . '/wp-content/plugins/bblm_plugin/bblm_plugin.css" />';
}

add_action('login_head', 'bblm_custom_login');

function change_wp_login_url() {
    echo  get_option('siteurl');
}

add_filter('login_headerurl', 'change_wp_login_url');


function change_wp_login_title() {
    echo  'Powered by ' . get_option('blogname');
}

add_filter('login_headertitle', 'change_wp_login_title');

/************ Removal of Default Widgets **********/
function bblm_remove_default_widgets() {
	if (function_exists('unregister_sidebar_widget')) {
		unregister_sidebar_widget('Meta');
	}
}

add_action('widgets_init','bblm_remove_default_widgets',0);


/************ Declaration / insertion of Admin Pages **********/
function bblm_insert_admin_pages() {
	//Addition of Top level admin pages

	add_menu_page('League Admin', 'BB: League Admin', 8, 'bblm_plugin/pages/bb.admin.core.welcome.php');
	add_menu_page('Match Management', 'BB: Match Admin', 8, 'bblm_plugin/pages/bb.admin.core.matchmanagement.php');
	add_menu_page('Team Management', 'BB: Team Admin', 8, 'bblm_plugin/pages/bb.admin.core.teamm.php');

	//Adds the subpages to the master heading - League Admin Pages
	add_submenu_page('bblm_plugin/pages/bb.admin.core.welcome.php', 'BB Settings', 'BB Settings', 8, 'bblm_plugin/pages/bb.admin.edit.options.php');
	add_submenu_page('bblm_plugin/pages/bb.admin.core.welcome.php', 'Did You Know', 'Did You Know', 8, 'bblm_plugin/pages/bb.admin.manage.dyk.php');
	add_submenu_page('bblm_plugin/pages/bb.admin.core.welcome.php', 'New Season', 'New Season', 8, 'bblm_plugin/pages/bb.admin.add.season.php');
	add_submenu_page('bblm_plugin/pages/bb.admin.core.welcome.php', 'Add Cup', 'Add Cup', 8, 'bblm_plugin/pages/bb.admin.add.series.php');
	add_submenu_page('bblm_plugin/pages/bb.admin.core.welcome.php', 'Add Competition', 'Add Competition', 8, 'bblm_plugin/pages/bb.admin.add.comp.php');
	add_submenu_page('bblm_plugin/pages/bb.admin.core.welcome.php', 'Manage Comps', 'Manage Comps', 8, 'bblm_plugin/pages/bb.admin.manage.comps.php');
	add_submenu_page('bblm_plugin/pages/bb.admin.core.welcome.php', 'Assign teams (comp)', 'Assign teams (comp)', 8, 'bblm_plugin/pages/bb.admin.edit.comp_team.php');
	add_submenu_page('bblm_plugin/pages/bb.admin.core.welcome.php', 'Set-up Brackets (comp)', 'Set-up Brackets', 8, 'bblm_plugin/pages/bb.admin.add.comp_brackets.php');
	add_submenu_page('bblm_plugin/pages/bb.admin.core.welcome.php', 'Edit Brackets (comp)', 'Edit Brackets', 8, 'bblm_plugin/pages/bb.admin.edit.comp_brackets.php');
	add_submenu_page('bblm_plugin/pages/bb.admin.core.welcome.php', 'Add Stadium', 'Add Stadium', 8, 'bblm_plugin/pages/bb.admin.add.stadium.php');
	add_submenu_page('bblm_plugin/pages/bb.admin.core.welcome.php', 'Create an Award', 'Create Award', 8, 'bblm_plugin/pages/bb.admin.add.award.php');
	add_submenu_page('bblm_plugin/pages/bb.admin.core.welcome.php', 'Close a Competition', 'Close Comp', 8, 'bblm_plugin/pages/bb.admin.end.comp.php');
	add_submenu_page('bblm_plugin/pages/bb.admin.core.welcome.php', 'Close a Season', 'Close Sea', 8, 'bblm_plugin/pages/bb.admin.end.season.php');
	add_submenu_page('bblm_plugin/pages/bb.admin.core.welcome.php', 'Generate Weekly Summary', 'Gen Summary', 8, 'bblm_plugin/pages/bb.admin.generate.summary.php');

	//Adds the subpages to the master heading - Match Management Pages
	add_submenu_page('bblm_plugin/pages/bb.admin.core.matchmanagement.php', 'Record Match', 'Record Match', 8, 'bblm_plugin/pages/bb.admin.add.match.php');
	add_submenu_page('bblm_plugin/pages/bb.admin.core.matchmanagement.php', 'Record Player Actions', 'Player Actions', 8, 'bblm_plugin/pages/bb.admin.add.match_player.php');
	add_submenu_page('bblm_plugin/pages/bb.admin.core.matchmanagement.php', 'Edit Match details', 'Edit Match', 8, 'bblm_plugin/pages/bb.admin.edit.match.php');
	add_submenu_page('bblm_plugin/pages/bb.admin.edit.match.php', 'Edit Match Trivia', 'Edit Match Trivia', 8, 'bblm_plugin/pages/bb.admin.edit.match_trivia.php');
	add_submenu_page('bblm_plugin/pages/bb.admin.core.matchmanagement.php', 'Add Fixture', 'Add Fixture', 8, 'bblm_plugin/pages/bb.admin.add.fixture.php');
	add_submenu_page('bblm_plugin/pages/bb.admin.core.matchmanagement.php', 'Edit Fixture', 'Edit Fixture', 8, 'bblm_plugin/pages/bb.admin.edit.fixture.php');

	//Adds the subpages to the master heading - Team Management Pages
	add_submenu_page('bblm_plugin/pages/bb.admin.core.teamm.php', 'Add Team', 'Add Team', 8, 'bblm_plugin/pages/bb.admin.add.team.php');
	add_submenu_page('bblm_plugin/pages/bb.admin.core.teamm.php', 'Manage Teams', 'Manage Teams', 8, 'bblm_plugin/pages/bb.admin.edit.team.php');
	add_submenu_page('bblm_plugin/pages/bb.admin.core.teamm.php', 'Add Race', 'Add Race', 8, 'bblm_plugin/pages/bb.admin.add.race.php');
	add_submenu_page('bblm_plugin/pages/bb.admin.core.teamm.php', 'Add Position', 'Add Position', 8, 'bblm_plugin/pages/bb.admin.add.position.php');
	add_submenu_page('bblm_plugin/pages/bb.admin.core.teamm.php', 'Manage Players', 'Manage Players', 8, 'bblm_plugin/pages/bb.admin.add.player.php');
	add_submenu_page('bblm_plugin/pages/bb.admin.core.teamm.php', 'Edit Player', 'Edit Player', 8, 'bblm_plugin/pages/bb.admin.edit.player.php');
	add_submenu_page('bblm_plugin/pages/bb.admin.core.teamm.php', 'Add Star', 'Add Star', 8, 'bblm_plugin/pages/bb.admin.add.star.php');
	add_submenu_page('bblm_plugin/pages/bb.admin.core.teamm.php', 'JM Report', 'JM Report', 8, 'bblm_plugin/pages/bb.admin.report.jm.php');

}
add_action('admin_menu', 'bblm_insert_admin_pages');


/************ Warzone Fitering **********/
$options = get_option('bblm_config');
$warzone_category = htmlspecialchars($options['cat_warzone'], ENT_QUOTES);


add_action('pre_get_posts', 'bs_remove_warzone_cat' );

function bs_remove_warzone_cat( $notused )
{
  global $wp_query;
  global $warzone_category;

  // Figure out if we need to exclude glossary - exclude from
  // archives (except category archives), feeds, and home page
  if( is_home() || is_feed() ||
      ( is_archive() && !is_category() )) {
     $wp_query->query_vars['cat'] = '-' . $warzone_category;
  }
}

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