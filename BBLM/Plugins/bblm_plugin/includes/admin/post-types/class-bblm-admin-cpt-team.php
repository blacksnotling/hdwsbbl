<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Team CPT admin functions
 *
 * Defines the admin functions related to the Team CPT (edit screens, custom messages, post saving etc)
 * For the Meta-Boxes, see the meta-boxes directory
 * For front end functions reloated to the CPT see the includes/post-types directory
 *
 * @class 		BBLM_Admin_CPT_Team
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/Admin/CPT
 * @version   1.0
 */

class BBLM_Admin_CPT_Team {

 /**
  * Constructor
  */
  public function __construct() {

    return TRUE;

  }

 /**
  * Resets all the injured players on a team (MNG)
  * Can be used after a match to clear those who missed a game, or at the start of a season
  *
  * @param int $ID the ID of the team
  * @return bool true (successfull), or False (Failure)
  */
  public static function reset_team_mng( $ID ) {
    global $wpdb;

    //selects the injured players on the team
    $selectinjplayer = 'SELECT WPID AS PWPID FROM '.$wpdb->prefix.'player WHERE p_mng = 1 AND t_id = ' . (int) $ID;

    if ( $injplayer = $wpdb->get_results( $selectinjplayer ) ) {
      foreach ( $injplayer as $ip ) {
        BBLM_Admin_CPT_Player::reset_player_mng( $ip->PWPID );
      }
    }
    return TRUE;

  }//end of reset_team_injuries()

}//end of class

new BBLM_Admin_CPT_Team();
