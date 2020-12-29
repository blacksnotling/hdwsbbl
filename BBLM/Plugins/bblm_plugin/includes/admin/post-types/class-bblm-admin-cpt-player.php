<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Player CPT admin functions
 *
 * Defines the admin functions related to the Player CPT (edit screens, custom messages, post saving etc)
 * For the Meta-Boxes, see the meta-boxes directory
 * For front end functions reloated to the CPT see the includes/post-types directory
 *
 * @class 		BBLM_Admin_CPT_Player
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/Admin/CPT
 * @version   1.0
 */

class BBLM_Admin_CPT_Player {

 /**
  * Constructor
  */
  public function __construct() {

    return TRUE;

  }

  /**
   * Resets the injured status of a player
   *
   * @param int $ID the ID of the Player
   * @return bool true (successfull), or False (Failure)
   */
   public static function reset_player_mng( $ID ) {
     global $wpdb;

     $reactivatesql = 'UPDATE `'.$wpdb->prefix.'player` SET `p_mng` = \'0\', `p_cost_ng` = p_cost  WHERE `WPID` = '.$ID.' LIMIT 1';
     if ( FALSE !== $wpdb->query( $reactivatesql ) ) {
       return TRUE;
     }
     else {
       return FALSE;
     }

   }//end of reset_team_injuries()

}//end of class

new BBLM_Admin_CPT_Player();
