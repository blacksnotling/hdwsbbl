<?php
/*
Plugin Name: HDWSBBL Plugin
Plugin URI: http://www.hdwsbbl.co.uk/
Description: HDWSBBL specific features that should not go in the BBLM plugin, or in a theme. Requires the BBLM plugin
Version: 1.1
Author: Blacksnotling
Author URI: https://github.com/blacksnotling
Requires at least: 4.7
Tested up to: 5.6

Text Domain: hdwsbbl

*/
/**
 *
 * @package HDWSBBL
 * @category Core
 * @author Blacksnoptling
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

include_once( 'includes/class-hdwsbbl-widgets.php' );			// Loads the Widgets
include_once( 'includes/hdwsbbl-template-functions.php' );			// Loads the templating functions
