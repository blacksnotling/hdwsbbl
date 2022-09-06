<?php
/**
 * BBLM Conditional Functions
 *
 * Functions for determining the current query/page.
 *
 * @author 		Blacksnotling
 * @category 	Core
 * @package 	BBowlLeagueMan/Functions
 * @version		1.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * is_bblm - Returns true if on a page which uses BBLM templates
 *
 * @access public
 * @return bool
 */
function is_bblm() {
	return apply_filters( 'is_bblm', ( is_singular( bblm_post_types() ) || is_post_type_archive( bblm_post_types() ) ) ? true : false );
}

/**
 * bblm_post_types - Returns array of BBLM post types
 *
 * @access public
 * @return array
 */
if ( ! function_exists( 'bblm_post_types' ) ) {
	function bblm_post_types() {
		return apply_filters( 'bblm_filter_post_types', array( 'bblm_dyk', 'bblm_owner', 'bblm_stadium', 'bblm_cup', 'bblm_season', 'bblm_race', 'bblm_comp', 'bblm-match', 'bblm_star', 'bblm_inducement', 'bblm_team' ) );
	}
}
