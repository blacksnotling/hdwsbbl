<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Setup the plugin menus in the WP admin.
 *
 * @class 		BBLM_Admin_Menus
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/Admin
 * @version   1.4
 */

if ( ! class_exists( 'BBLM_Admin_Menus' ) ) :

/**
 * BBLM_Admin_Menus Class
 */
class BBLM_Admin_Menus {

	/**
	 * Set up the hooks to call the top level menus
	 */
	public function __construct() {

		add_action( 'admin_menu', array( $this, 'admin_main_setup_menu' ), 6 );
		add_action( 'admin_menu', array( $this, 'settings_menu' ), 7 );
		add_action( 'admin_menu', array( $this, 'cutover_menu' ), 8 );

	}

	/**
	 * Add main menu item
	 */
	public function admin_main_setup_menu() {

		add_menu_page(
			__( 'Blood Bowl', 'bblm' ),
			__( 'Blood Bowl', 'bblm' ),
			'manage_options',
			'bblm_main_menu',
			array( $this, 'bblm_main_page_content' ),
			'dashicons-forms',
			100
		);

		//Create a submenu page, calling the same function as the parent page above
		add_submenu_page(
			'bblm_main_menu',
			__( 'Overview', 'bblm' ),
			__( 'Overview', 'bblm' ),
			'manage_options',
			'bblm_main_menu',
			array( $this, 'bblm_main_page_content' )
		);

		//Create the 'Players Transfers page'
		include_once( plugin_dir_path( BBLM_PLUGIN_FILE ) . 'pages/class-bblm-player-transfers.php' );
		new BBLM_Player_Transfers();

		//Create the 'Players Transfers page'
		include_once( plugin_dir_path( BBLM_PLUGIN_FILE ) . 'pages/class-bblm-player-addbulk.php' );
		new BBLM_Player_AddBulk();

		//Create the 'Manage Positions Page'
		include_once( plugin_dir_path( BBLM_PLUGIN_FILE ) . 'pages/class-bblm-manage-positions.php' );
		new BBLM_Manage_Positions();

		//Add the 'assign teams to comp page' under the competitions CPT

		add_submenu_page(
			'bblm_main_menu',
			__( 'Assign teams (comp)', 'bblm'),
			__( 'Assign teams (comp)', 'bblm'),
			'bblm_manage_league',
			'bblm_plugin/pages/bb.admin.edit.comp_team.php'
		);

		//Create the 'Record match' page
		include_once( plugin_dir_path( BBLM_PLUGIN_FILE ) . 'pages/bb.admin.add.match.php' );
		new BBLM_Add_Match();

		//Create the 'Edit match' page
		include_once( plugin_dir_path( BBLM_PLUGIN_FILE ) . 'pages/bb.admin.edit.match.php' );
		new BBLM_Edit_Match();

		//Create the 'Manage Fixtures'
		include_once( plugin_dir_path( BBLM_PLUGIN_FILE ) . 'pages/class-bblm-manage-fixtures.php' );
		new BBLM_Manage_Fixtures();

	}

	/**
	 * Loads the content of the main screen of BBowlLeagueMan
	 */
	public function bblm_main_page_content() {

		include_once( plugin_dir_path( BBLM_PLUGIN_FILE ) . 'pages/bb.admin.core.welcome.php' );

	}

	/**
	 * Loads the content of the Cutover page for the BBowlLeagueMan
	 */
	public function cutover_menu() {

		add_menu_page(
			__( 'Cutover', 'bblm' ),
			__( 'Cutover', 'bblm' ),
			'bblm_manage_league',
			'bblm_plugin/pages/cutover.php',
			'',
			'dashicons-hammer'
		);

	}

	/**
	 * Loads the content of the Settings screen of BBowlLeagueMan
	 */
	public function settings_menu() {

		include_once( 'class-bblm-admin-settings.php' );
		new BBLM_Settings_Admin();

	}

}

endif;

return new BBLM_Admin_Menus();
