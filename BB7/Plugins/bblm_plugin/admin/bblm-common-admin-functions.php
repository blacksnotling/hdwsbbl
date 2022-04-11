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
 * @version   1.6
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
	$teamextravaluesql = 'SELECT T.t_rr, T.t_cl, T.t_ac, T.t_apoc, T.r_id FROM '.$wpdb->prefix.'team T WHERE T.t_id = '.$tid;
	$tev = $wpdb->get_row($teamextravaluesql);
	$rrcost = BBLM_CPT_Race::get_reroll_cost( $tev->r_id );
	$tevalue = ( $rrcost * $tev->t_rr ) + ( $tev->t_cl * 10000 ) + ( $tev->t_ac * 10000 ) + ( $tev->t_apoc * 50000 );

	//Add the two together
	$newtv = $tpvalue+$tevalue;

	//Generate SQL
	$sql = 'UPDATE `'.$wpdb->prefix.'team` SET `t_tv` = \''.$newtv.'\', `t_ctv` = \''.$newtv.'\' WHERE `t_id` = '.$tid.' LIMIT 1';

  //Execute SQL
	if ( FALSE !== $wpdb->query($sql) ) {
		$sucess = TRUE;
	}
	return true;
}

 /**
  * Updates a Players Star Player Points (SPP). Is used during editing a players match history.
  */
function bblm_update_player( $pid, $counts = 1 ) {
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
	$rrookie_pos = htmlspecialchars($options['player_rrookie'], ENT_QUOTES);

	$bblm_tbd_team = bblm_get_tbd_team();

	$jmsql = 'SELECT T.WPID AS TWPID, X.WPID AS PWPID, X.p_num, Z.pos_name, Z.pos_id, X.p_id FROM '.$wpdb->prefix.'player X, '.$wpdb->prefix.'position Z, '.$wpdb->prefix.'team T WHERE T.t_id = X.t_id AND X.pos_id = Z.pos_id AND X.p_status = 1 AND (X.pos_id = 1 OR X.pos_id = '.$merc_pos.' OR X.pos_id = '.$rrookie_pos.') AND T.t_id != ' . $bblm_tbd_team . ' ORDER BY X.t_id, X.p_num';

	if ( $journeymen = $wpdb->get_results($jmsql) ) {
		$is_first = 1;
		$current_team = "";

		foreach ( $journeymen as $jm ) {
			if ( $jm->TWPID !== $current_team ) {
				$current_team = $jm->TWPID;
				if ( 1 !== $is_first ) {
					echo '</ul>';
				}
				$is_first = 1;
			}
			if ( $is_first ) {
				echo '<h3>' . bblm_get_team_name( $jm->TWPID ) . '</h3>';
				echo '<ul>';
				$is_first = 0;
			}
      //Output player details
			echo '<li>' . $jm->p_num . ' - ' . bblm_get_player_name( $jm->PWPID ) . ' (<em>' . $jm->pos_name . '</em>)';

      //Work out the number of games played
      $PlrPldsql = "SELECT COUNT(M.m_id) as PLYD FROM hdbb_match_player M WHERE M.p_id = ".$jm->p_id." GROUP BY M.p_id";
      if ( $pplyd = $wpdb->get_row( $PlrPldsql ) ) {
        //They have played a game so list the matches played and the hire / fire options.
        echo ' - ' . $pplyd->PLYD . ' match(s) played';
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
        echo __( ' - Not played a match', 'bblm') . '</li>';
      }

		}
		echo '</ul>';
	}
	else {
    echo __( '<p><strong>There are no Journeymen, Mercenarys, or Riotous Rookies currently active in the league!</strong></p>', 'bblm');
	}

} // end of bblm_jm_report

/**
 * When a bblm_comp post thpe is added, create a new taxonomy
 */
 function bblm_add_comp_tax( $post_id, $post, $update ) {

    // If this is a revision, don't send the email.
    if ( wp_is_post_revision( $post_id ) ) {
        return;
		}
		if ( (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || ( defined('DOING_AJAX') && DOING_AJAX) || isset($_REQUEST['bulk_edit']) ) {
			return;
		}
		//Check the taxonomy file_exists
		if ( taxonomy_exists('post_competitions') ) {
			$postslug = get_post_field( 'post_name', $post);
			wp_insert_term(
				wp_filter_nohtml_kses( $postslug ), // the term
				'post_competitions' // the taxonomy
			);
		}
}
add_action( 'wp_insert_post', 'bblm_add_comp_tax', 10, 3 );

/**
 * Allows searches through objects
 */
function in_array_field( $needle, $needle_field, $haystack, $strict = false ) {
    if ( $strict ) {
        foreach ( $haystack as $item )
            if ( isset( $item->$needle_field ) && $item->$needle_field === $needle )
                return true;
    }
    else {
        foreach ( $haystack as $item )
            if ( isset( $item->$needle_field ) && $item->$needle_field == $needle )
                return true;
    }
    return false;
}
