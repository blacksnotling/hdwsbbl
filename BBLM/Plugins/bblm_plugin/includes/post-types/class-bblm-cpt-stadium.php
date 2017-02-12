<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Stadium CPT functions
 *
 * Defines the functions related to the Stadium CPT (archive page logic, display functions etc)
 * For the Meta-Boxes, see the meta-boxes directory
 * For admin functions reloated to the CPT see the includes/admin/post-types directory
 *
 * @class 		BBLM_CPT_Stadium
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/CPT
 * @version   1.0
 */

class BBLM_CPT_Stadium {

	/**
	 * Constructor
	 */
	public function __construct() {

    add_action( 'pre_get_posts', array( $this, 'manage_archives' ) );

	}

	/**
	 * stops the CPT archive pages pagenating
	 *
	 * @param wordpress $query
	 * @return none
	 */
	 public function manage_archives( $query ) {

	    if( is_post_type_archive( 'bblm_stadium' ) && !is_admin() && $query->is_main_query() ) {
	        $query->set( 'posts_per_page', -1 );
	        $query->set( 'orderby', 'title' );
	        $query->set( 'order', 'asc' );
	    }

	  }

		/**
		 * Echos a list of home teams for this stadium
		 *
		 * @return string
		 */
		 public function home_teams( ) {
			 global $post;
			 global $wpdb;

			 $hometeamsql = 'SELECT T.t_name, P.guid FROM '.$wpdb->prefix.'team T, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE T.t_id = J.tid AND J.prefix = \'t_\' AND J.pid = P.ID AND T.t_show = 1 AND T.stad_id = '.get_the_ID();
			 if ( $hometeam = $wpdb->get_results($hometeamsql) ) {

				 //Check to see how many teams are returned
				 if ( 1 < count( $hometeam ) ) {

					 //we have more than one team
					 echo  "<p>".__( 'At Present, the following teams call this stadium their home.' , 'bblm' )."</p>\n<ul>\n";
					 foreach ( $hometeam as $ht ) {

						 echo "	<li><a href=\"".$ht->guid."\" title=\"Read more about ".$ht->t_name."\">".$ht->t_name."</a></li>\n";

					 }
					 echo '</ul>\n';

				 }
				 else {

					 //only one team is retuned
					 foreach ( $hometeam as $ht ) {

						 echo "<p>At Present, only <a href=\"".$ht->guid."\" title=\"Read more about ".$ht->t_name."\">".$ht->t_name."</a> call this stadium their home.</p>\n";

					 }

				 }

			 }
			 else {

				 echo "	<div class=\"info\">\n		<p>".__('At present, no teams use this stadium for their home games.', 'bblm' )."</p>\n	</div>\n";

			 }

		 }

		 /**
 		 * Echos a list of recent matches for this stadium
 		 *
 		 * @return string
 		 */
		 public function echo_recent_matches() {
		 	global $post;
		 	global $wpdb;

			$recentmatchsql = 'SELECT P.guid, P.post_title, M.m_gate, UNIX_TIMESTAMP(M.m_date) AS mdate, C.c_name, D.div_name FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'division D WHERE M.m_id = J.tid AND J.prefix = \'m_\' AND J.pid = P.ID AND M.c_id = C.c_id AND M.div_id = D.div_id AND M.stad_id = '.get_the_ID().' ORDER BY M.m_date DESC';
			if ( $recmatch = $wpdb->get_results($recentmatchsql) ) {

				$zebracount = 1;
				echo "<table class=\"sortable\">\n	<tr>\n		<th>".__( 'Date', 'bblm' )."</th>\n		<th>".__( 'Match', 'bblm' )."</th>\n		<th>".__( 'Competition', 'bblm' )."</th>\n		<th>".__( 'Attendance', 'bblm' )."</th>\n	</tr>\n";
				foreach ( $recmatch as $rm ) {

					if ( ($zebracount % 2) && (10 < $zebracount) ) {

						echo "		<tr class=\"tb_hide\">\n";

					}

					else if ( ($zebracount % 2) && (10 >= $zebracount) ) {

						echo "		<tr>\n";

					}

					else if ( 10 < $zebracount ) {

						echo "		<tr class=\"tbl_alt tb_hide\">\n";

					}

					else {

						echo "		<tr class=\"tbl_alt\">\n";

					}

					echo "		<td>".date("d.m.y", $rm->mdate)."</td>\n		<td><a href=\"".$rm->guid."\" title=\"Read the full match report\">".$rm->post_title."</a></td>\n		<td>".$rm->c_name." (".$rm->div_name.")</td>\n		<td>".number_format($rm->m_gate)."</td>\n	</tr>\n";
					$zebracount++;

				}

				echo "</table>\n";

			}
			else {

				//SQL failed, no matches have taken place
				echo "<p>".__( 'No Matches have taken place at this Stadium' , 'bblm' )."</p>\n";

			}

		}

}

new BBLM_CPT_Stadium();
