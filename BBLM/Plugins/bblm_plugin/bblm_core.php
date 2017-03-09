<?php
/*
Plugin Name: Blood Bowl League Manager System (BBLM)
Plugin URI: http://www.hdwsbbl.co.uk/
Description: BloodBowl League Manager for the HDWSBBL
Version: 20170218
Author: Blacksnotling
Author URI: https://github.com/blacksnotling
Requires at least: 4.7
Tested up to: 4.7.1

Text Domain: bblm

*/
//stop people from accessing the file directly and causing errors.
if (!function_exists('add_action')) die('You cannot run this file directly. Naughty Person');

/************ Declaration / insertion of Admin Pages **********/
function bblm_insert_admin_pages() {
	//Addition of Top level admin pages

	add_menu_page('League Admin', 'BB: League Admin', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.core.welcome.php');
	add_menu_page('Match Management', 'BB: Match Admin', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.core.matchmanagement.php');
	add_menu_page('Team Management', 'BB: Team Admin', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.core.teamm.php');

	//Adds the subpages to the master heading - League Admin Pages
add_submenu_page('bblm_plugin/pages/bb.admin.core.welcome.php', 'Manage Comps', 'Manage Comps', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.manage.comps.php');
add_submenu_page('bblm_plugin/pages/bb.admin.core.welcome.php', 'Assign teams (comp)', 'Assign teams (comp)', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.edit.comp_team.php');
add_submenu_page('bblm_plugin/pages/bb.admin.core.welcome.php', 'Set-up Brackets (comp)', 'Set-up Brackets', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.add.comp_brackets.php');
add_submenu_page('bblm_plugin/pages/bb.admin.core.welcome.php', 'Edit Brackets (comp)', 'Edit Brackets', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.edit.comp_brackets.php');
add_submenu_page('bblm_plugin/pages/bb.admin.core.welcome.php', 'Create an Award', 'Create Award', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.add.award.php');
add_submenu_page('bblm_plugin/pages/bb.admin.core.welcome.php', 'Close a Competition', 'Close Comp', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.end.comp.php');
add_submenu_page('bblm_plugin/pages/bb.admin.core.welcome.php', 'Close a Season', 'Close Sea', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.end.season.php');
add_submenu_page('bblm_plugin/pages/bb.admin.core.welcome.php', 'Generate Weekly Summary', 'Gen Summary', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.generate.summary.php');

//Adds the subpages to the master heading - Match Management Pages
add_submenu_page('bblm_plugin/pages/bb.admin.core.matchmanagement.php', 'Record Match', 'Record Match', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.add.match.php');
add_submenu_page('bblm_plugin/pages/bb.admin.core.matchmanagement.php', 'Record Player Actions', 'Player Actions', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.add.match_player.php');
add_submenu_page('bblm_plugin/pages/bb.admin.core.matchmanagement.php', 'Edit Match details', 'Edit Match', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.edit.match.php');
add_submenu_page('bblm_plugin/pages/bb.admin.edit.match.php', 'Edit Match Trivia', 'Edit Match Trivia', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.edit.match_trivia.php');
add_submenu_page('bblm_plugin/pages/bb.admin.core.matchmanagement.php', 'Add Fixture', 'Add Fixture', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.add.fixture.php');
add_submenu_page('bblm_plugin/pages/bb.admin.core.matchmanagement.php', 'Edit Fixture', 'Edit Fixture', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.edit.fixture.php');

//Adds the subpages to the master heading - Team Management Pages
add_submenu_page('bblm_plugin/pages/bb.admin.core.teamm.php', 'Add Team', 'Add Team', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.add.team.php');
add_submenu_page('bblm_plugin/pages/bb.admin.core.teamm.php', 'Manage Teams', 'Manage Teams', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.edit.team.php');
add_submenu_page('bblm_plugin/pages/bb.admin.core.teamm.php', 'Add Player', 'Add Player', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.add.player.php');
add_submenu_page('bblm_plugin/pages/bb.admin.core.teamm.php', 'Edit Player', 'Edit Player', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.edit.player.php');
add_submenu_page('bblm_plugin/pages/bb.admin.core.teamm.php', 'Add Star', 'Add Star', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.add.star.php');
add_submenu_page('bblm_plugin/pages/bb.admin.core.teamm.php', 'JM Report', 'JM Report', 'bblm_manage_league', 'bblm_plugin/pages/bb.admin.report.jm.php');

}
add_action('admin_menu', 'bblm_insert_admin_pages', 11);



/************ Update TV function. Version 0.2 (20100123) **********/
function bblm_update_tv($tid) {
	global $wpdb;

	//Calculate worth of players
	$playervaluesql = 'SELECT SUM(P.p_cost_ng) FROM '.$wpdb->prefix.'player P WHERE P.p_status = 1 AND P.t_id = '.$tid;
	$tpvalue = $wpdb->get_var($playervaluesql);

	//Calcuate worth of rest of team (re-rolls, Assistant Coaches etc).
	$teamextravaluesql = 'SELECT SUM((R.r_rrcost*T.t_rr)+(T.t_ff*10000)+(T.t_cl*10000)+(T.t_ac*10000)+(T.t_apoc*50000)) AS TTOTAL FROM '.$wpdb->prefix.'team T, '.$wpdb->prefix.'race R WHERE R.r_id = T.r_id AND T.t_id = '.$tid;
	$tevalue = $wpdb->get_var($teamextravaluesql);

	//Add the two together
	$newtv = $tpvalue+$tevalue;

	//Generate SQL
	$sql = 'UPDATE `'.$wpdb->prefix.'team` SET `t_tv` = \''.$newtv.'\' WHERE `t_id` = '.$tid.' LIMIT 1';
	//Execute SQL
	//print("<h3>".$sql."</h3>");
	if (FALSE !== $wpdb->query($sql)) {
		$sucess = TRUE;
	}
	return true;
}
/************ Update Player function. Version 1.0b (20100123) **********/
function bblm_update_player($pid, $counts = 1) {
	//takes in two values, the player ID and a bool to see if only matches that count should be included
	global $wpdb;

	$playersppsql = 'SELECT SUM(M.mp_spp) FROM '.$wpdb->prefix.'match_player M WHERE M.p_id = '.$pid.' AND M.mp_spp > 0';
	if ($counts) {
		$playersppsql .= " AND M.mp_counts = 1";
	}
	$pspp = $wpdb->get_var($playersppsql);

	//Generate SQL
	$sql = 'UPDATE `'.$wpdb->prefix.'player` SET `p_spp` = \''.$pspp.'\' WHERE `p_id` = \''.$pid.'\' LIMIT 1';
	//Execute SQL
	if (FALSE !== $wpdb->query($sql)) {
		$sucess = TRUE;
	}
	return true;
}

/**
 * Defnes a new capability, bblm_manage_league which is used to authorise access to the acmin section
 * http://www.garyc40.com/2010/04/ultimate-guide-to-roles-and-capabilities/
 *
 */
add_action( 'init', 'bblm_roles_init' );

function bblm_roles_init() {
	$roles_object = get_role( 'administrator' );
	$roles_object->add_cap('bblm_manage_league');
}


/**
 *	New Class (v2) below!
 */

/**
 *
 * @package BBowlLeagueMan
 * @category Core
 * @author Blacksnoptling
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'BBowlLeagueMan' ) ) :

/**
 * Main BBowlLeagueMan Class
 *
 * @class BBowlLeagueMan
 * @version	1.0
 */
final class BBowlLeagueMan {

	/**
	 * @var string
	 */
	public $version = '2.0';

	/**
	 * @var BBowlLeagueMan The single instance of the class
	 */
	protected static $_instance = null;


	/**
	 * @var BBLM_Templates $templates
	 */
	public $templates = null;

	/**
	 * @var array
	 */
	public $text = array();

	/**
	 * Main BBowlLeagueMan Instance
	 *
	 * Ensures only one instance of BBowlLeagueMan is loaded or can be loaded.
	 *
	 * @static
	 * @see BBLM()
	 * @return BBowlLeagueMan - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Illegal Procedure', 'bblm' ), '2.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Illegal Procedure', 'bblm' ), '2.0' );
	}

	/**
	 * BBowlLeagueMan Constructor.
	 * @access public
	 * @return BBowlLeagueMan
	 */
	public function __construct() {

		// Auto-load classes on demand
		if ( function_exists( "__autoload" ) ) {
			spl_autoload_register( "__autoload" );
		}

		spl_autoload_register( array( $this, 'autoload' ) );

		// Define constants
		$this->define_constants();

		// Include required files
		$this->includes();

		// Hooks
		add_action( 'init', array( $this, 'init' ), 0 );
		add_action( 'after_setup_theme', array( $this, 'setup_environment' ) );

	}

	/**
	 * Auto-load BBLM classes on demand to reduce memory consumption (and in the event I forgot to include them in the code!).
	 *
	 * @param mixed $class
	 * @return void
	 */
	public function autoload( $class ) {
		$path  = null;
		$class = strtolower( $class );
		$file = 'class-' . str_replace( '_', '-', $class ) . '.php';

		if ( $path && is_readable( $path . $file ) ) {
			include_once( $path . $file );
			return;
		}

		// Fallback
		if ( strpos( $class, 'bblm_' ) === 0 ) {
			$path = $this->plugin_path() . '/includes/';
		}

		if ( $path && is_readable( $path . $file ) ) {
			include_once( $path . $file );
			return;
		}
	}

	/**
	 * Define BBLM Constants.
	 */
	private function define_constants() {
		define( 'BBLM_PLUGIN_FILE', __FILE__ );
		define( 'BBLM_VERSION', $this->version );

		if ( ! defined( 'BBLM_TEMPLATE_PATH' ) ) {
			define( 'BBLM_TEMPLATE_PATH', $this->template_path() );
		}

	}

	/**
	 * Include required core files
	 */
	private function includes() {

		include_once( 'includes/bblm-common-functions.php' );

		if ( is_admin() ) {
			include_once( 'includes/admin/class-bblm-admin.php' );
		}

		if ( ! is_admin() ) {
			$this->frontend_includes();
		}

		//Post types
		include_once( 'includes/class-bblm-post-types.php' );		// Registers post types
		include_once( 'includes/class-bblm-widgets.php' );			// Loads the Widgets
	}

	/**
	 * Include required frontend files.
	 */
	public function frontend_includes() {

		include_once( 'includes/class-bblm-template-loader.php' );		// Template Loader

	}

	/**
	 * Init BBowlLeagueMan when WordPress Initialises.
	 */
	public function init() {

		//flush rules on plugin init so that the custom permalinks all work (hopefully).
		flush_rewrite_rules();
	}


	/**
	 * Ensure theme and server variable compatibility and setup image sizes if the theme we are using does not include them.
	 */
	public function setup_environment() {
		if ( ! current_theme_supports( 'post-thumbnails' ) ) {
			add_theme_support( 'post-thumbnails' );
		}

		// Add image sizes
		add_image_size( 'bblm-crop-medium',  300, 300, true );
		add_image_size( 'bblm-fit-medium',  300, 300, false );
		add_image_size( 'bblm-fit-icon',  128, 128, false );
		add_image_size( 'bblm-fit-mini',  32, 32, false );
	}

	/** Helper functions ******************************************************/

	/**
	 * Get the plugin url.
	 *
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', __FILE__ ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

	/**
	 * Get the template path.
	 *
	 * @return string
	 */
	public function template_path() {
		//return apply_filters( 'BBLM_TEMPLATE_PATH', 'Bs-cpt-plugin-test/' );
		$tppath = plugin_dir_path( __FILE__ );
		$tppath .= 'templates/';
		return $tppath;
	}
}

endif;

if ( ! function_exists( 'BBLM' ) ):

/**
 * Returns the main instance of BBLM to prevent the need to use globals.
 *
 * @return BBowlLeagueMan
 */
function BBLM() {
	return BBowlLeagueMan::instance();
}

endif;

BBLM();
