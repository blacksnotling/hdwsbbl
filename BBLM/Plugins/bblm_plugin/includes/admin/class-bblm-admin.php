<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * BBowlLeagueMan Admin.
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

	}

}

return new BBLM_Admin();
