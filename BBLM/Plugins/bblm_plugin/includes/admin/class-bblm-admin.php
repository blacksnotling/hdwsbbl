<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * BBowlLeagueMan Admin.
 * Includes all the relevent Admin classes and functions required to operate the website.
 * These will not be called on the frontend.
 *
 * @class 		BBLM_Admin
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/Admin
 * @version   1.5
 */

class BBLM_Admin {

	/**
	 * Constructor
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'includes' ) );
    add_action( 'dashboard_glance_items', array( $this, 'add_dashboard_counts' ) );
		add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_freebooter_report' ) );
		add_action( 'admin_init', array( $this, 'add_admin_js' ) );

	}

	/**
	 * Include any classes we need within the admin pages.
	 */
	public function includes() {

		include( 'class-bblm-admin-menus.php' );
		include_once( 'bblm-common-admin-functions.php' );

		// Classes for functionality
    include_once( 'class-bblm-admin-post-types.php' );
    include_once( 'class-gamajo-dashboard-glancer.php' );

	}

  /**
   * Dashboard glancer - courtesy of Gary Jones (Gamajo)
   */
   function add_dashboard_counts() {

     $glancer = new Gamajo_Dashboard_Glancer;
     $my_post_types = array( 'bblm_dyk', 'bblm_owner', 'bblm_transfer', 'bblm_stadium', 'bblm_cup', 'bblm_season', 'bblm_comp', 'bblm_race' );
     $glancer->add( $my_post_types, array( 'publish' ) );

   }

	 /**
		* Adds the freebooter (JM) report to the admin dashboard.
		* It calls the bblm_jm_report() function from the common admin functions file
		*/
   function add_dashboard_freebooter_report() {

     wp_add_dashboard_widget('bblm_jm_widget', 'Blood Bowl: Freebooter Report', 'bblm_jm_report' );

		}

		/**
 		* Registers any custom JavaScript files required in the admin pages
 		*/
    function add_admin_js() {

			wp_register_script( 'bblm_match_management', plugin_dir_url( __FILE__ ) . '../../includes/js/admin.match.management.js' );

 		} //end of add_admin_js

}

return new BBLM_Admin();
