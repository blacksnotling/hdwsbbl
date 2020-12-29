<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Matches functions
 *
 * Until the Match CPT type is created, this class will hold all the relevent functions
 *
 * @class 		BBLM_CPT_match
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/CPT
 * @version   1.1
 */

class BBLM_CPT_Match {

	/**
	 * Constructor
	 */
	public function __construct() {

	}

/**
* Dsiplays the Matches that have taken place at a specific stadium
*
* @param wordpress $query
* @return string
*/

 public function display_match_by_stadium () {
   global $post;
   global $wpdb;

	 $recentmatchsql = 'SELECT P.guid, P.post_title, M.m_gate, UNIX_TIMESTAMP(M.m_date) AS mdate, D.div_name, M.c_id FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P, '.$wpdb->prefix.'division D WHERE M.m_id = J.tid AND J.prefix = \'m_\' AND J.pid = P.ID AND M.div_id = D.div_id AND M.stad_id = '. get_the_ID() .' ORDER BY M.m_date DESC';
	 if ( $recmatch = $wpdb->get_results( $recentmatchsql ) ) {
		 $zebracount = 1;

		 echo '<table class="bblm_table">';
		 echo '<tr>';
		 echo '<th>' . __( 'Date', 'bblm' ) . '</th>';
		 echo '<th>' . __( 'Match', 'bblm' ) . '</th>';
		 echo '<th>' . __( 'Competition', 'bblm' ) . '</th>';
		 echo '<th>' . __( 'Attendance', 'bblm' ) . '</th>';
		 echo '</tr>';

		 foreach ( $recmatch as $rm ) {
			 if ( ( $zebracount % 2 ) && ( 10 < $zebracount ) ) {
				 echo '<tr class="bblm_tbl_hide">';
			 }
			 else if ( ( $zebracount % 2 ) && ( 10 >= $zebracount ) ) {
				 echo '<tr>';
			 }
			 else if ( 10 < $zebracount ) {
				 echo '<tr class="bblm_tbl_alt bblm_tbl_hide">';
			 }
			 else {
				 echo '<tr class="bblm_tbl_alt">';
			 }
			 echo '<td>' . date( "d.m.y", $rm->mdate ) . '</td>';
			 echo '<td><a href="' . $rm->guid . '" title="Read the full match report">' . $rm->post_title . '</a></td>';
			 echo '<td>' . bblm_get_competition_name( $rm->c_id ) . ' (' . $rm->div_name . ')</td>';
			 echo '<td>' . number_format( $rm->m_gate ) . '</td>';
			 echo '</tr>';

			 $zebracount++;
		 }

		 echo '</table>';

	 }
	 else {
		 //No matches have been played at this stadium
		 echo '<p>' . __( 'No matches have been played at this stadium.', 'bblm' ) . '<p>';


	 }

 } //end of display_match_by_stadium

/**
 * Returns a date of a match alreqady formatted
 *
 * @param $ID the ID of the match (WPID)
 * @return string the data of the match
 */
 public static function get_match_date( $ID ) {
	 global $wpdb;

	 $sql = 'SELECT UNIX_TIMESTAMP(M.m_date) AS mdate FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'bb2wp J WHERE M.m_id = J.tid AND J.prefix=\'m_\' AND J.tid = '. $ID;

	 $result = $wpdb->get_var( $sql );

	 return date("d-m-25y", $result );

 } //end of get_match_date

} //end of class

new BBLM_CPT_Match();
