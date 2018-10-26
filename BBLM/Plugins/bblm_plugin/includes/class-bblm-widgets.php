<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Import the Widgets used in the plugin
 *
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/Widgets
 * @version   1.0
 */


/**
 * Loads all the Widgets used in the league
 */

include_once( 'widgets/class-bblm-widget-dyk.php' );
include_once( 'widgets/class-bblm-widget-recentmatches.php' );
include_once( 'widgets/class-bblm-widget-listcomps.php' );
