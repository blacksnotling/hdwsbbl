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
 * @version   1.6
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

		//Legacy Menu Items
		add_menu_page(
			__( 'League Admin', 'bblm' ),
			__( 'BB: League Admin', 'bblm' ),
			'bblm_manage_league',
			'bblm_plugin/pages/bb.admin.core.welcome.php'
		);

		add_menu_page(
			__( 'Team Management', 'bblm' ),
			__( 'BB: Team Admin', 'bblm' ),
			'bblm_manage_league',
			'bblm_plugin/pages/bb.admin.core.teamm.php'
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

		//Create the 'Manage Tournament Brackets' page
		include_once( plugin_dir_path( BBLM_PLUGIN_FILE ) . 'pages/class-bblm-manage-brackets.php' );
		new BBLM_Manage_Brackets();

		//Create the 'Record match' page
		include_once( plugin_dir_path( BBLM_PLUGIN_FILE ) . 'pages/bb.admin.add.match.php' );
		new BBLM_Add_Match();

		//Create the 'Edit match' page
		include_once( plugin_dir_path( BBLM_PLUGIN_FILE ) . 'pages/bb.admin.edit.match.php' );
		new BBLM_Edit_Match();

		//Create the 'Add Player Actions' page
		include_once( plugin_dir_path( BBLM_PLUGIN_FILE ) . 'pages/bb.admin.add.match_player.php' );
		new BBLM_Add_Match_Player();

		//Create the 'Manage Fixtures'
		include_once( plugin_dir_path( BBLM_PLUGIN_FILE ) . 'pages/class-bblm-manage-fixtures.php' );
		new BBLM_Manage_Fixtures();

		//Create the 'Add Stars Page'
		include_once( plugin_dir_path( BBLM_PLUGIN_FILE ) . 'pages/bb.admin.add.star.php' );
		new BBLM_Add_StarPlayers();

		//Force the display of the race special rules tax page in the menu
		add_submenu_page(
			'bblm_main_menu',
			__( 'Race Traits', 'bblm' ),
			__( 'Race Traits', 'bblm' ),
			'manage_options',
			'edit-tags.php?taxonomy=race_rules',
			false
		);

		//Load Legacy pages
		//Adds the subpages to the master heading - League Admin Pages
	add_submenu_page('bblm_plugin/pages/bb.admin.core.welcome.php', 'Create an Award', 'Create Award', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.add.award.php');
	add_submenu_page('bblm_plugin/pages/bb.admin.core.welcome.php', 'Close a Competition', 'Close Comp', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.end.comp.php');
	add_submenu_page('bblm_plugin/pages/bb.admin.core.welcome.php', 'Close a Season', 'Close Sea', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.end.season.php');
	add_submenu_page('bblm_plugin/pages/bb.admin.core.welcome.php', 'Generate Weekly Summary', 'Gen Summary', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.generate.summary.php');

	//Adds the subpages to the master heading - Team Management Pages
	add_submenu_page('bblm_plugin/pages/bb.admin.core.teamm.php', 'Add Team', 'Add Team', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.add.team.php');
	add_submenu_page('bblm_plugin/pages/bb.admin.core.teamm.php', 'Manage Teams', 'Manage Teams', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.edit.team.php');
	add_submenu_page('bblm_plugin/pages/bb.admin.core.teamm.php', 'Add Player', 'Add Player', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.add.player.php');
	add_submenu_page('bblm_plugin/pages/bb.admin.core.teamm.php', 'Edit Player', 'Edit Player', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.edit.player.php');
	add_submenu_page('bblm_plugin/pages/bb.admin.core.teamm.php', 'JM Report', 'JM Report', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.report.jm.php');

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
