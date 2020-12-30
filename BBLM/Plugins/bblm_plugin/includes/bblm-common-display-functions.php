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
 * @version   1.2
 */

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
function bblm_get_match_link_date( $ID ) {

  $match_date = BBLM_CPT_Match::get_match_date( $ID );
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

		if ( $formatted ) {

			$output .= bblm_get_team_name( $match->TAWPID ) . ' <strong>' . $match->m_teamAtd . '</strong> vs ' . bblm_get_team_name( $match->TBWPID ) . ' <strong>' . $match->m_teamBtd . '</strong>';

		}
		else {

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

	if ( $formatted ) {
		$match = bblm_get_match_name_score( $ID, 1 );
	}
	else {

		$match = bblm_get_match_name_score( $ID, 0 );

	}
	$output = "";

	$output .= '<a title="View details of the match" href="' . get_post_permalink( $ID ) . '">' . $match . '</a>';


  return __( $output, 'bblm');

}// end of bblm_get_match_link_score
