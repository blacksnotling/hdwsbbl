<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * HDWSBBL Template classes
 *
 * Functions for the templating system
 *
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	HDWSBBL/Templates
 * @version   1.0
 */

/**
 * Add specific CSS class by filter to body_class
 *
 */
add_filter('body_class','hdwsbbl_add_body_class');

function hdwsbbl_add_body_class( $classes ) {

	if ( is_category( 'warzone' ) || is_page('warzone') || ( in_category( 'warzone' ) && is_single() ) ) {
		// add 'section-warzone' to the $classes array if it is part of the Warzone section
		$classes[] = 'hdwsbbl-warzone';
	}

	return $classes;

}
