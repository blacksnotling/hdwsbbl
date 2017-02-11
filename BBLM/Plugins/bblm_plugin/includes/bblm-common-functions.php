<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * BBowlLeagueMan Common Functions
 *
 * Common functions used on both the front-end and the admin.
 *
 * @author 		Blacksnotling
 * @category 	Core
 * @package 	BBowlLeagueMan/Functions
 * @version   1.0
 */

 /**
  * Returns the leage name from options. If not set it returns a placeholder
  */
function bblm_get_league_name () {

  if ( $options = get_option('bblm_config') ) {

    $bblm_league_name = htmlspecialchars( $options['league_name'], ENT_QUOTES );

    //validates if something was not set
  	if ( strlen( $bblm_league_name ) == 0 ) {

  	   $bblm_league_name = "League";

  	}

  }
  // options were not found
  else {

    $bblm_league_name = "League";

  }

  return $bblm_league_name;

}
