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

 /**
  * Displays the Journeyman / Mercenary output
  * Lists of teams and lists of output
  */
function bblm_jm_report() {
  global $wpdb;

  //call the options from the table
	$options = get_option('bblm_config');
	$merc_pos = htmlspecialchars($options['player_merc'], ENT_QUOTES);

	$jmsql = 'SELECT P.post_title AS Player, O.post_title AS Team, X.p_num, Z.pos_name, Z.pos_id, X.p_id FROM '.$wpdb->prefix.'player X, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P, '.$wpdb->prefix.'bb2wp I, '.$wpdb->posts.' O, '.$wpdb->prefix.'position Z WHERE X.pos_id = Z.pos_id AND X.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = P.ID AND X.t_id = I.tid AND I.prefix = \'t_\' AND I.pid = O.ID AND X.p_status = 1 AND (X.pos_id = 1 OR X.pos_id = '.$merc_pos.') ORDER BY X.t_id, X.p_num';

	if ( $journeymen = $wpdb->get_results($jmsql) ) {
		$is_first = 1;
		$current_team = "";

		foreach ($journeymen as $jm) {
			if ($jm->Team !== $current_team) {
				$current_team = $jm->Team;
				if (1 !== $is_first) {
					print(" </ul>\n");
				}
				$is_first = 1;
			}
			if ($is_first) {
				print("<h3>".$jm->Team."</h3>\n <ul>\n");
				$is_first = 0;
			}
      //Output player details
			print ("   <li># ".$jm->p_num." - ".$jm->Player." (<em>".$jm->pos_name."</em>)");

      //Work out the number of games played
      $PlrPldsql = "SELECT COUNT(M.m_id) as PLYD FROM hdbb_match_player M WHERE M.p_id = ".$jm->p_id." GROUP BY M.p_id";
      if ( $pplyd = $wpdb->get_row($PlrPldsql) ) {
        //They have played a game so list the matches played and the hire / fire options.
        echo ' - '.$pplyd->PLYD.' match(s) played';
        echo ' - <a href="';
        bloginfo('url');
        echo '/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.edit.player.php&action=edit&item=remove&id='.$jm->p_id.'" title="'.__( 'Remove this freebooter from the team', 'bblm').'">['.__( 'Fire / Remove', 'bblm').']</a>';

        if ( $merc_pos !== $jm->pos_id ) {
          //Mercenarys should not have a hire button!
           echo ' OR <a href="';
           bloginfo('url');
           echo '/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.edit.player.php&action=edit&item=jmstatus&id='.$jm->p_id.'" title="'.__( 'Hire this freebooter to the team', 'bblm').'">['.__( 'Hire', 'bblm').']</a>';
        }
        echo '</li>';
      }
      else {
        //They have not played a game so just list their name
        echo __( ' - Not played a match</li>', 'bblm');
      }

		}
		print("</ul>\n");
	}
	else {
    echo __( '<p><strong>There are no Journeymen or Mercenarys currently active in the league!</strong></p>', 'bblm');
	}

}
