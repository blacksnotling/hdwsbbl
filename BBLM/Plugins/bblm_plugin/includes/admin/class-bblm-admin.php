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
 * @version   1.0
 */

class BBLM_Admin {

	/**
	 * Constructor
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'includes' ) );

	}

	/**
	 * Include any classes we need within the admin pages.
	 */
	public function includes() {

		include( 'class-bblm-admin-menus.php' );

		// Classes for functionality
    include_once( 'class-bblm-admin-post-types.php' );

	}

}

return new BBLM_Admin();
