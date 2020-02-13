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
 * @version   1.2
 */


/**
 * Loads all the Widgets used in the league
 */

include_once( 'widgets/class-bblm-widget-dyk.php' );
include_once( 'widgets/class-bblm-widget-recentmatches.php' );
include_once( 'widgets/class-bblm-widget-listcomps.php' );
include_once( 'widgets/class-bblm-widget-recenttransfers.php' );
include_once( 'widgets/class-bblm-widget-recentplayers.php' );
include_once( 'widgets/class-bblm-widget-recentdepartures.php' );

 //Template Companions - only displayed with certain templates
include_once( 'widgets/class-bblm-widget-tccompdetails.php' );
include_once( 'widgets/class-bblm-widget-tcteamlogo.php' );
include_once( 'widgets/class-bblm-widget-tcteamdetails.php' );
include_once( 'widgets/class-bblm-widget-tcmatchdetails.php' );
include_once( 'widgets/class-bblm-widget-tcplayerdetails.php' );
