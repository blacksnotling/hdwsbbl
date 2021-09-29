<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * BBowlLeagueMan Common Display Functions
 *
 * Common Display functions used on both the front-end and the admin.
 *
 * @author 		Blacksnotling
 * @category 	Core
 * @package 	BBowlLeagueMan/Functions
 * @version   1.4
 */

 /**
  * Outputs a message informing that the object in question is from a previous ruleset
  * Takes in the object in question
  */
 function bblm_display_legacy_notice( $legacy_item ) {

	 $legacy_item = sanitize_text_field( $legacy_item );

   $output = "";

	 $output .= '<div class="bblm_details bblm_legacy-notice"><p>' . __('This ', 'bblm') . $legacy_item .  __(' was part of the league under a previous ruleset.','bblm' ) . '</p></div>';

   echo $output;

 }// end of bblm_get_team_name

/**
 * Returns the name of a team, properly escaped and formatted
 * Takes in the ID of the Wordpress Page
 */
function bblm_get_team_name( $ID ) {

  $output = "";

  $output .= esc_html( get_the_title( $ID ) );

  return $output;

}// end of bblm_get_team_name

/**
 * Returns the link of a team, properly escaped and formatted
 * Takes in the ID of the Wordpress Page
 */
function bblm_get_team_link( $ID ) {

  $team_name = bblm_get_team_name( $ID );
  $output = "";

  $output .= '<a title="Read more about ' . $team_name . '" href="' . get_post_permalink( $ID ) . '">' . $team_name . '</a>';

  return __( $output, 'bblm');

}// end of bblm_get_team_link

/**
 * Returns the link of a team, properly escaped and formatted
 * with the teams logo in between (or the race image if not available)
 * Takes in the ID of the Wordpress Page
 */
function bblm_get_team_link_logo( $ID, $size='medium' ) {
	global $wpdb;

	switch ( $size ) {
	 case ( 'medium' == $size ):
			 break;
	 case ( 'icon' == $size ):
			 break;
	 case ( 'mini' == $size ):
			 break;
	 default :
			 $size = 'medium';
			 break;
 }

	//grab the ID of the "tbd" team
	$bblm_tbd_team = bblm_get_tbd_team();

  $team_name = bblm_get_team_name( $ID );
  $output = "";

	$output .= '<a title="Read more about ' . $team_name . '" href="' . get_post_permalink( $ID ) . '">';

	//Determine if a custom logo is present
	$teamsql = 'SELECT * FROM '.$wpdb->prefix.'team T WHERE T.WPID = '. $ID;
	$team = $wpdb->get_row ( $teamsql );
	$filename = $_SERVER['DOCUMENT_ROOT']."/images/teams/" . $team->t_sname . "_big.gif";
	if (file_exists($filename)) {
		$output .= '<img src="' . home_url() . '/images/teams/' . $team->t_sname . '_big.gif" alt="' . $team_name . ' Logo" />';
	}
	else {
		if ( $bblm_tbd_team == $team->t_id ) {
			//If the team is TBD then display the logo of the star races (the league shiled)
			$options = get_option( 'bblm_config' );
			$bblm_star_race = htmlspecialchars( $options[ 'race_star' ], ENT_QUOTES );

			$output .= BBLM_CPT_Race::get_race_icon( $bblm_star_race, $size );
		}
		else {
			//otherwise display the race logo
			$output .= BBLM_CPT_Race::get_race_icon( $team->r_id, $size );
		}

	}

	$output .= '</a>';

  return __( $output, 'bblm');

}// end of bblm_get_team_link_logo

/**
 * Returns the name of a Player, properly escaped and formatted
 * Takes in the ID of the Wordpress Page
 */
function bblm_get_player_name( $ID ) {

  $output = "";

  $output .= esc_html( get_the_title( $ID ) );

  return $output;

}// end of bblm_get_player_name

/**
 * Returns the link for a player, properly escaped and formatted
 * Takes in the ID of the Wordpress Page
 */
function bblm_get_player_link( $ID ) {

  $player_name = bblm_get_player_name( $ID );
  $output = "";

  $output .= '<a title="Read more about ' . $player_name . '" href="' . get_post_permalink( $ID ) . '">' . $player_name . '</a>';

  return __( $output, 'bblm');

}// end of bblm_get_player_link

/**
 * Returns the name of a Stadium, properly escaped and formatted
 * Takes in the ID of the Wordpress Page
 */
function bblm_get_stadium_name( $ID ) {

  $output = "";

  $output .= esc_html( get_the_title( $ID ) );

  return $output;

}// end of bblm_get_stadium_name

/**
 * Returns the link of a Stadsium, properly escaped and formatted
 * Takes in the ID of the Wordpress Page
 */
function bblm_get_stadium_link( $ID ) {

  $stadium_name = bblm_get_team_name( $ID );
  $output = "";

  $output .= '<a title="Read more about ' . $stadium_name . '" href="' . get_post_permalink( $ID ) . '">' . $stadium_name . '</a>';

  return __( $output, 'bblm');

}// end of bblm_get_stadium_link

/**
 * Returns the name of an Owner, properly escaped and formatted
 * Takes in the ID of the Wordpress Page
 */
function bblm_get_owner_name( $ID ) {

  $output = "";

  $output .= esc_html( get_the_title( $ID ) );

  return $output;

}// end of bblm_get_owner_name

/**
 * Returns the link of an Owner, properly escaped and formatted
 * Takes in the ID of the Wordpress Page
 */
function bblm_get_owner_link( $ID ) {

  $owner_name = bblm_get_team_name( $ID );
  $output = "";

  $output .= '<a title="Read more about ' . $owner_name . '" href="' . get_post_permalink( $ID ) . '">' . $owner_name . '</a>';

  return __( $output, 'bblm');

}// end of bblm_get_owner_link

/**
 * Returns the name of a Championship Cup, properly escaped and formatted
 * Takes in the ID of the Wordpress Page
 */
function bblm_get_cup_name( $ID ) {

  $output = "";

  $output .= esc_html( get_the_title( $ID ) );

  return $output;

}// end of bblm_get_cup_name

/**
 * Returns the link of a Championship Cup, properly escaped and formatted
 * Takes in the ID of the Wordpress Page
 */
function bblm_get_cup_link( $ID ) {

  $cup_name = bblm_get_cup_name( $ID );
  $output = "";

  $output .= '<a title="Read more about ' . $cup_name . '" href="' . get_post_permalink( $ID ) . '">' . $cup_name . '</a>';

  return __( $output, 'bblm');

}// end of bblm_get_cup_link

/**
 * Returns the name of a Season, properly escaped and formatted
 * Takes in the ID of the Wordpress Page
 */
function bblm_get_season_name( $ID ) {

  $output = "";

  $output .= esc_html( get_the_title( $ID ) );

  return $output;

}// end of bblm_get_season_name

/**
 * Returns the link of a Season, properly escaped and formatted
 * Takes in the ID of the Wordpress Page
 */
function bblm_get_season_link( $ID ) {

  $season_name = bblm_get_season_name( $ID );
  $output = "";

  $output .= '<a title="Read more about ' . $season_name . '" href="' . get_post_permalink( $ID ) . '">' . $season_name . '</a>';

  return __( $output, 'bblm');

}// end of bblm_get_season_link

/**
 * Returns the name of a Competition, properly escaped and formatted
 * Takes in the ID of the Wordpress Page
 */
function bblm_get_competition_name( $ID ) {

  $output = "";

  $output .= esc_html( get_the_title( $ID ) );

  return $output;

}// end of bblm_get_season_name

/**
 * Returns the link of a Competition, properly escaped and formatted
 * Takes in the ID of the Wordpress Page
 */
function bblm_get_competition_link( $ID ) {

  $competition_name = bblm_get_competition_name( $ID );
  $output = "";

  $output .= '<a title="Read more about ' . $competition_name . '" href="' . get_post_permalink( $ID ) . '">' . $competition_name . '</a>';

  return __( $output, 'bblm');

}// end of bblm_get_season_link

/**
 * Returns the title of a Competition Format, properly escaped and formatted
 * Takes in the ID of the Competition format
 */
function bblm_get_competition_format_name( $ID ) {
	global $wpdb;

	$output = "Undefined";

	$sql = 'SELECT ct_name FROM ' . $wpdb->prefix . 'comp_type WHERE ct_id = ' . $ID;
	if ( $cname = $wpdb->get_var( $sql ) ) {

		$output = esc_html( $cname );

	}

  return $output;

}// end of bblm_get_competition_format_name

/**
 * Returns the name of a Race, properly escaped and formatted
 * Takes in the ID of the Wordpress Page
 */
function bblm_get_race_name( $ID ) {

  $output = "";

  $output .= esc_html( get_the_title( $ID ) );

  return $output;

}// end of bblm_get_season_name

/**
 * Returns the link of a Race, properly escaped and formatted
 * Takes in the ID of the Wordpress Page
 */
function bblm_get_race_link( $ID ) {

  $race_name = bblm_get_race_name( $ID );
  $output = "";

  $output .= '<a title="Read more about ' . $race_name . '" href="' . get_post_permalink( $ID ) . '">' . $race_name . '</a>';

  return __( $output, 'bblm');

}// end of bblm_get_season_link

/**
 * Returns the name of a Match, properly escaped and formatted
 * Takes in the ID of the Wordpress Page
 */
function bblm_get_match_name( $ID ) {

  $output = "";

  $output .= esc_html( get_the_title( $ID ) );

  return $output;

}// end of bblm_get_match_name

/**
 * Returns the link of a Match, properly escaped and formatted
 * Takes in the ID of the Wordpress Page
 */
function bblm_get_match_link( $ID ) {

  $match_name = bblm_get_match_name( $ID );
  $output = "";

  $output .= '<a title="Read more about ' . $match_name . '" href="' . get_post_permalink( $ID ) . '">' . $match_name . '</a>';

  return __( $output, 'bblm');

}// end of bblm_get_match_link

/**
 * Returns the link of a Match as a date, properly escaped and formatted
 * Takes in the ID of the Wordpress Page
 */
function bblm_get_match_link_date( $ID, $format="short" ) {

	switch ( $format ) {
	 case ( 'short' == $format ):
			 break;
	 case ( 'long' == $format ):
			 break;
	 default :
			 $format = 'short';
			 break;
 }

  $match_date = BBLM_CPT_Match::get_match_date( $ID, $format );
  $output = "";

  $output .= '<a title="View details of the match" href="' . get_post_permalink( $ID ) . '">' . $match_date . '</a>';

  return __( $output, 'bblm');

}// end of bblm_get_match_link_date

/**
 * Returns the title of a Match with score, properly escaped and formatted
 * Takes in the ID of the Wordpress Page
 */
function bblm_get_match_name_score( $ID, $formatted = 1 ) {
	global $wpdb;

	$output = "";

  $matchsql = 'SELECT M.m_teamAtd, M.m_teamBtd, T.WPID AS TAWPID, R.WPID AS TBWPID FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'team R WHERE M.m_teamA = T.t_id AND M.m_teamB = R.t_id AND M.WPID = '. $ID;
	if ( $match = $wpdb->get_row ( $matchsql ) ) {

		if ( 1 == $formatted ) {
			//normal formatting
			$output .= bblm_get_team_name( $match->TAWPID ) . ' <strong>' . $match->m_teamAtd . '</strong> vs ' . bblm_get_team_name( $match->TBWPID ) . ' <strong>' . $match->m_teamBtd . '</strong>';

		}
		else if ( 2 == $formatted ) {
			//tournament bracket formatting
			$output .= bblm_get_team_name( $match->TAWPID ) . ' <strong>' . $match->m_teamAtd . '</strong><br />' . bblm_get_team_name( $match->TBWPID ) . ' <strong>' . $match->m_teamBtd . '</strong>';
		}
		else {
			//no formatting
			$output .= bblm_get_team_name( $match->TAWPID ) . ' ' . $match->m_teamAtd . ' vs ' . bblm_get_team_name( $match->TBWPID ) . ' ' . $match->m_teamBtd;

		}


	}

  return __( $output, 'bblm');

}// end of bblm_get_match_name_score

/**
 * Returns the link of a Match with score, properly escaped and formatted
 * Takes in the ID of the Wordpress Page
 */
function bblm_get_match_link_score( $ID, $formatted = 1 ) {

	if ( 1 == $formatted ) {
		//standard formatting
		$match = bblm_get_match_name_score( $ID, 1 );

	}
	else if ( 2 == $formatted ) {
		//tournament bracket formatting
		$match = bblm_get_match_name_score( $ID, 2 );

	}
	else {
		//no formatting
		$match = bblm_get_match_name_score( $ID, 0 );

	}
	$output = "";

	$output .= '<a title="View details of the match" href="' . get_post_permalink( $ID ) . '">' . $match . '</a>';


  return __( $output, 'bblm');

}// end of bblm_get_match_link_score
