<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * BBowlLeagueMan Common Admin Functions
 *
 * Common functions used on the admin side of the site.
 * These will NOT be loaded on the front end.
 *
 * @author 		Blacksnotling
 * @category 	Core
 * @package 	BBowlLeagueMan/Admin
 * @version   1.0
 */

 /**
  * Updates a teams Team Value (TV)
  */
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
	if ( FALSE !== $wpdb->query($sql) ) {
		$sucess = TRUE;
	}
	return true;
}

 /**
  * Updates a Players Star Player Points (SPP). Is used during editing a players match history.
  */
function bblm_update_player($pid, $counts = 1) {
	//takes in two values, the player ID and a bool to see if only matches that count should be included
	global $wpdb;

	$playersppsql = 'SELECT SUM(M.mp_spp) FROM '.$wpdb->prefix.'match_player M WHERE M.p_id = '.$pid.' AND M.mp_spp > 0';
	if ( $counts ) {
		$playersppsql .= " AND M.mp_counts = 1";
	}
	$pspp = $wpdb->get_var($playersppsql);

	//Generate SQL
	$sql = 'UPDATE `'.$wpdb->prefix.'player` SET `p_spp` = \''.$pspp.'\' WHERE `p_id` = \''.$pid.'\' LIMIT 1';
	//Execute SQL
	if ( FALSE !== $wpdb->query($sql) ) {
		$sucess = TRUE;
	}
	return true;
}
