<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Match Record CPT admin functions
 *
 * Defines the admin functions related to the Match Record CPT (edit screens, custom messages, post saving etc)
 * For the Meta-Boxes, see the meta-boxes directory
 * For front end functions reloated to the CPT see the includes/post-types directory
 *
 * @class 		BBLM_Admin_CPT_Match
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/Admin/CPT
 * @version   1.0
 */

class BBLM_Admin_CPT_Match {

 /**
	* Constructor
 	*/
  public function __construct() {

		add_action( 'admin_enqueue_scripts', array( $this, 'match_auto_update' ) );

 	}

	/**
	 * Adds the following Javascript content to the add match, edit match, and fixtures pages
	 */
	public function match_auto_update( $hook_suffix ) {

		if( in_array( $hook_suffix, array( 'blood-bowl_page_bblm_add_match', 'blood-bowl_page_bblm_edit_match', 'blood-bowl_page_bblm_fixtures', 'blood-bowl_page_bblm_add_match_player' ) ) ) {

			//loads in the required javascript file
			wp_enqueue_script( 'bblm_match_management' );
			wp_enqueue_script( 'bblm_player_changes' );

			//jQuery UI date picker file
			wp_enqueue_script('jquery-ui-datepicker');

			//jQuery UI theme css file
			wp_enqueue_style('e2b-admin-ui-css','http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.0/themes/base/jquery-ui.css',false,"1.9.0",false);

		}

	} //end of match_auto_update



} //end of class

new BBLM_Admin_CPT_Match();
