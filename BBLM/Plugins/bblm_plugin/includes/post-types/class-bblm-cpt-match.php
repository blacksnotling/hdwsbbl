<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Matches functions
 *
 * Until the Match CPT type is created, this class will hold all the relevent functions
 *
 * @class 		BBLM_CPT_match
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/CPT
 * @version   1.3
 */

class BBLM_CPT_Match {

	/**
	 * Constructor
	 */
	public function __construct() {

	}

/**
* Dsiplays the Matches that have taken place at a specific stadium
*
* @param wordpress $query
* @return string
*/

 public function display_match_by_stadium () {
   global $post;
   global $wpdb;

	 $recentmatchsql = 'SELECT M.WPID AS MWPID, M.m_gate, UNIX_TIMESTAMP(M.m_date) AS mdate, D.div_name, M.c_id FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'division D WHERE M.div_id = D.div_id AND M.stad_id = '. get_the_ID() .' ORDER BY M.m_date DESC';
	 if ( $recmatch = $wpdb->get_results( $recentmatchsql ) ) {
		 $zebracount = 1;

		 echo '<div role="region" aria-labelledby="Caption01" tabindex="0">';
		 echo '<table class="bblm_table">';
		 echo '<thead>';
		 echo '<tr>';
		 echo '<th>' . __( 'Date', 'bblm' ) . '</th>';
		 echo '<th>' . __( 'Match', 'bblm' ) . '</th>';
		 echo '<th>' . __( 'Competition', 'bblm' ) . '</th>';
		 echo '<th>' . __( 'Attendance', 'bblm' ) . '</th>';
		 echo '</tr>';
		 echo '</thead>';
		 echo '<tbody>';

		 foreach ( $recmatch as $rm ) {
			 if ( ( $zebracount % 2 ) && ( 10 < $zebracount ) ) {
				 echo '<tr class="bblm_tbl_alt bblm_tbl_hide">';
			 }
			 else if ( ( $zebracount % 2 ) && ( 10 >= $zebracount ) ) {
				 echo '<tr class="bblm_tbl_alt">';
			 }
			 else if ( 10 < $zebracount ) {
				 echo '<tr class="blm_tbl_hide">';
			 }
			 else {
				 echo '<tr>';
			 }
			 echo '<td>' . date( "d.m.y", $rm->mdate ) . '</td>';
			 echo '<td>' . bblm_get_match_link( $rm->MWPID ) . '</td>';
			 echo '<td>' . bblm_get_competition_name( $rm->c_id ) . ' (' . $rm->div_name . ')</td>';
			 echo '<td>' . number_format( $rm->m_gate ) . '</td>';
			 echo '</tr>';

			 $zebracount++;
		 }

		 echo '</tbody></table></div>';

	 }
	 else {
		 //No matches have been played at this stadium
		 echo '<p>' . __( 'No matches have been played at this stadium.', 'bblm' ) . '<p>';


	 }

 } //end of display_match_by_stadium

/**
 * Returns a date of a match alreqady formatted
 *
 * @param $ID the ID of the match (WPID)
 * @return string the data of the match
 */
 public static function get_match_date( $ID, $format ) {
	 global $wpdb;

	 switch ( $format ) {
		case ( 'short' == $format ):
				break;
		case ( 'long' == $format ):
				break;
		default :
				$format = 'short';
				break;
	}

	 $sql = 'SELECT UNIX_TIMESTAMP(M.m_date) AS mdate FROM '.$wpdb->prefix.'match M WHERE M.WPID = '. $ID;

	 $result = $wpdb->get_var( $sql );

	 if ( 'short' == $format ) {
		 return date("d.m.y", $result );
	 }
	 else if ( 'long' == $format ) {
		 return date("jS F 'y", $result );
	 }


 } //end of get_match_date

 /**
	* returns if a match is legacy (played under a former ruleset)
	*
	* @param wordpress $query
	* @return bool
	*/
	public static function is_match_legacy( $ID ) {
		global $wpdb;

		$matchsql = 'SELECT M.m_legacy FROM '.$wpdb->prefix.'match M WHERE M.WPID = '. $ID;
		$md = $wpdb->get_row( $matchsql );

		if ( $md->m_legacy ) {
			return true;
		}
		else {
			return false;
		}

	} //end of is_match_legacy()

	/**
	* Returns a list of increases gained during the match
	* Works both Legacy and 2020+ Ruleset players and matches
	*
	* @param $ID the ID of the match (WPID)
	* @param $Team the ID of the team (WPID)
	* @return string a list of increases
	*/
	public static function get_match_increases( $ID, $team ) {
		global $wpdb;

		$output = "Not Recorded";

		if ( self::is_match_legacy( $ID ) ) {
			//Old legacy output

			$playersql = 'SELECT M.*, P.p_num, P.WPID AS PWPID FROM '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T WHERE T.t_id = P.t_id AND P.p_id = M.p_id AND M.m_id = '.$ID.' AND T.WPID = '.$team.' ORDER BY P.p_num ASC';
			if ( $player = $wpdb->get_results( $playersql ) ) {
				//as we have players, initialize arrays to hold injuries and increases
				$valid = array();

				//Loop through the players to see if any recorded increases
				foreach ($player as $tp) {
					if ("none" !== $tp->mp_inc) {
						//if this player has an injury record it for later
						$valid[] = "#".$tp->p_num." - ".bblm_get_player_name( $tp->PWPID ) . " - " . esc_html( $tp->mp_inc );
					}
				}

				if ( isset( $valid ) ) {
					if ( 0 !== count( $valid ) ) {
						//If players where inj, we have details
						$output = '<ul>';
						foreach ( $valid as $v ) {
							$output .= '<li>' . $v . '</li>';
						}
						$output .= '</ul>';
					}
					else {
						$output = 'None';
					}
				}

			} //end of if playersql

		} //end of if legacy
		else {
			//New output for the 2020+ Ruleset
			$playersql = 'SELECT P.p_num, P.WPID AS PWPID, S.skill_name FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'player_increase I, '.$wpdb->prefix.'skills S WHERE S.skill_id = I.skill_id AND I.p_id = P.WPID AND I.skill_id > 0 AND I.m_id = ' . $ID . ' AND T.t_id = P.t_id AND T.WPID = '.$team.' ORDER BY P.p_num ASC';
			if ( $player = $wpdb->get_results( $playersql ) ) {
				//as we have players, initialize arrays to hold injuries and increases
				$valid = array();

				//Loop through the players to capture any entries
				foreach ($player as $tp) {
					$valid[] = "#".$tp->p_num." - ".bblm_get_player_name( $tp->PWPID )." - ". esc_html( $tp->skill_name );
				}

				if ( isset( $valid ) ) {
					if ( 0 !== count( $valid ) ) {
						//If players where inj, we have details
						$output = '<ul>';
						foreach ( $valid as $v ) {
							$output .= '<li>' . $v . '</li>';
						}
						$output .= '</ul>';
					}
					else {
						$output = 'None';
					}
				}

			} //end of if playersql
		}

		return $output;
	}//end of get_match_increases()

	/**
	* Returns a list of injuries sustained during the match
	* Works both Legacy and 2020+ Ruleset players and matches
	*
	* @param $ID the ID of the match (WPID)
	* @param $Team the ID of the team (WPID)
	* @return string a list of injuries
	*/
	public static function get_match_injuries( $ID, $team ) {
		global $wpdb;
		$output = "Not Recorded";

		if ( self::is_match_legacy( $ID ) ) {
			//Old legacy output

			$playersql = 'SELECT M.*, P.p_num, P.WPID AS PWPID FROM '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T WHERE T.t_id = P.t_id AND P.p_id = M.p_id AND M.m_id = '.$ID.' AND T.WPID = '.$team.' ORDER BY P.p_num ASC';
			if ( $player = $wpdb->get_results( $playersql ) ) {
				//as we have players, initialize arrays to hold injuries and increases
				$valid = array();

				//Loop through the players to see if any recorded increases
				foreach ($player as $tp) {
					if ("none" !== $tp->mp_inj) {
						//if this player has an injury record it for later
						$valid[] = "#".$tp->p_num." - " . bblm_get_player_name( $tp->PWPID ) . " - " . esc_html( $tp->mp_inj );
					}
				}

				if ( isset( $valid ) ) {
					if ( 0 !== count( $valid ) ) {
						//If players where inj, we have details
						$output = '<ul>';
						foreach ( $valid as $v ) {
							$output .= '<li>' . $v . '</li>';
						}
						$output .= '</ul>';
					}
					else {
						$output = 'None';
					}
				}

			} //end of if playersql

		} //end of if legacy
		else {
			//New output for the 2020+ Ruleset
			$playersql = 'SELECT P.p_num, P.WPID AS PWPID, S.inj_name FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'player_increase I, '.$wpdb->prefix.'injury S WHERE S.inj_id = I.inj_id AND I.p_id = P.WPID AND I.inj_id > 0 AND I.m_id = ' . $ID . ' AND T.t_id = P.t_id AND T.WPID = '.$team.' ORDER BY P.p_num ASC';
			if ( $player = $wpdb->get_results( $playersql ) ) {
				//as we have players, initialize arrays to hold injuries and increases
				$valid = array();

				//Loop through the players to capture any entries
				foreach ($player as $tp) {
					$valid[] = "#".$tp->p_num." - " . bblm_get_player_name( $tp->PWPID ) . " - " . esc_html( $tp->inj_name );
				}

				if ( isset( $valid ) ) {
					if ( 0 !== count( $valid ) ) {
						//If players where inj, we have details
						$output = '<ul>';
						foreach ( $valid as $v ) {
							$output .= '<li>' . $v . '</li>';
						}
						$output .= '</ul>';
					}
					else {
						$output = 'None';
					}
				}

			} //end of if playersql
		}

		return $output;
	}//end of get_match_injuries()


} //end of class


new BBLM_CPT_Match();
