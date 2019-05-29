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
 * @version   1.1
 */

 /**
  * Returns the leage name from options. If not set it returns a placeholder
  */
function bblm_get_league_name() {

  if ( $options = get_option( 'bblm_config' ) ) {

    $bblm_league_name = htmlspecialchars( $options[ 'league_name' ], ENT_QUOTES );

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

} // end of bblm_get_league_name

/**
 * If set, echo an archive description for a CPT set in the options page
 */
 function bblm_echo_archive_desc( $cpt ) {

   if ( !isset( $cpt ) ) {

     return $cpt;

   }
   else {

     //We have a value, function starts here
     $options = get_option( 'bblm_config' );
     $archive_text = htmlspecialchars( $options['archive_'.$cpt.'_text'], ENT_QUOTES );
       //validates if something was not set
       if ( strlen( $archive_text ) !== 0 ) {

         echo "<p>".nl2br( $archive_text )."</p>\n";

       }

     }

 } // end of bblm_echo_archive_desc()

 /**
	* Returns the Star Player Team ID
	*/
function bblm_get_star_player_team() {

	if ( $options = get_option( 'bblm_config' ) ) {

		$bblm_star_team = htmlspecialchars( $options[ 'team_star' ], ENT_QUOTES );

	}
	else {

		//failsafe
		$bblm_star_team = 1;

	}

	return $bblm_star_team;

} // end of bblm_get_star_player_team()

/**
 * Returns the "To Be Determined" (TBD) Team ID
 */
function bblm_get_tbd_team() {

	if ( $options = get_option( 'bblm_config' ) ) {

		$bblm_tbd_team = htmlspecialchars( $options[ 'team_tbd' ], ENT_QUOTES );

	}
	else {

		//failsafe
		$bblm_tbd_team = 1;

	}

	return $bblm_tbd_team;

} // end of bblm_get_tbd_team()

/**
 * Returns the "setting for max number of player stats to displays
 * default 25
 */
function bblm_get_stat_limit() {

	if ( $options = get_option( 'bblm_config' ) ) {

		$bblm_stat_limit = htmlspecialchars($options[ 'display_stats' ], ENT_QUOTES );

	}
	else {

		//failsafe
		$bblm_stat_limit = 25;

	}

	return $bblm_stat_limit;

} // end of bblm_get_stat_limit()
