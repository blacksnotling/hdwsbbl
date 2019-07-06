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
 * @version   1.0
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

	 $recentmatchsql = 'SELECT P.guid, P.post_title, M.m_gate, UNIX_TIMESTAMP(M.m_date) AS mdate, C.c_name, D.div_name FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'division D WHERE M.m_id = J.tid AND J.prefix = \'m_\' AND J.pid = P.ID AND M.c_id = C.c_id AND M.div_id = D.div_id AND M.stad_id = '. get_the_ID() .' ORDER BY M.m_date DESC';
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
				 echo '<tr class="tb_hide">';
			 }
			 else if ( ( $zebracount % 2 ) && ( 10 >= $zebracount ) ) {
				 echo '<tr>';
			 }
			 else if ( 10 < $zebracount ) {
				 echo '<tr class="tbl_alt tb_hide">';
			 }
			 else {
				 echo '<tr class="tbl_alt">';
			 }
			 echo '<td>' . date( "d.m.y", $rm->mdate ) . '</td>';
			 echo '<td><a href="' . $rm->guid . '" title="Read the full match report">' . $rm->post_title . '</a></td>';
			 echo '<td>' . $rm->c_name . ' (' . $rm->div_name . ')</td>';
			 echo '<td>' . number_format( $rm->m_gate ) . '</td>';
			 echo '</tr>';

			 $zebracount++;
		 }

		 echo '</table>';

	 }

 } //end of display_match_by_stadium

} //end of class

new BBLM_CPT_Match();
