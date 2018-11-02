<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Import the admin handler files and Meta-Boxes for the defined CPT's
 *
 * @class 		BBLM_Admin_Post_Types
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/Admin
 * @version   1.1
 */

 class BBLM_Admin_Post_Types {

  /**
 	 * Constructor
 	 */
 	public function __construct() {

 		add_action( 'admin_init', array( $this, 'include_post_type_handlers' ) );
		add_action( 'admin_init', array( $this, 'include_post_meta_boxes' ) );

 	}

  /**
	 * Loads all the CPT handler classes for admin screen functions
	 */
	public function include_post_type_handlers() {

    include_once( 'post-types/class-bblm-admin-cpt-dyk.php' );
		include_once( 'post-types/class-bblm-admin-cpt-owner.php' );

 }

 /**
 	* Loads all the CPT Meta-Boxes for admin screen functions
 	*/
	public function include_post_meta_boxes() {

		include_once( 'post-types/meta-boxes/class-bblm-meta-dyk.php' );

	}

}

return new BBLM_Admin_Post_Types();
