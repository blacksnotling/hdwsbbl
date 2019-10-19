<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Competitions CPT functions
 *
 * Defines the functions related to the Competitions CPT (archive page logic, display functions etc)
 * For admin functions reloated to the CPT see the includes/admin/post-types directory
 *
 * @class 		BBLM_CPT_Comp
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/CPT
 * @version   1.0
 */

class BBLM_CPT_Comp {

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

	    if( is_post_type_archive( 'bblm_comp' ) && !is_admin() && $query->is_main_query() ) {
	        $query->set( 'posts_per_page', -1 );
          $query->set( 'orderby', 'title' );
          $query->set( 'order', 'asc' );
          $query->set( 'post_status', 'publish' );
	    }

	  }

 /**
  * Returns the number of competitions that have taken place within a Championship Cup
  *
  * @param int $id the championship cup in question
  * @return int $count
  */
  public function get_cup_count_by_cup( $ID ) {
		global $wpdb;

		$ccount = 0;

		$countsql = 'SELECT COUNT(*) AS CCount FROM '.$wpdb->prefix.'comp WHERE series_id = ' . $ID;
		$ccount = $wpdb->get_var( $countsql );

		return $ccount;

  } //end of get_cup_count_by_cup()

 /**
  * Outputs a list of competitions with stats
  *
  * @param wordpress $query
  * @return string $output
  */
	public function display_comp_list_with_stats() {
		global $wpdb;
		global $post;

		$post_type = get_post_type(); //Determine the CPT that is calling this function

		$itemid = get_the_ID(); //The ID of the Page being displayed

		if ( $post_type == "bblm_cup" ) {

			$complistingsql = 'SELECT P.post_title, P.guid, SUM(T.tc_played) AS PLD, SUM(T.tc_tdfor) AS TD, SUM(T.tc_casfor) AS CAS, SUM(T.tc_comp) AS COMP, SUM(T.tc_int) AS cINT FROM '.$wpdb->prefix.'team_comp T, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE T.c_id = C.c_id AND J.tid = C.c_id AND J.prefix = \'c_\' AND J.pid = P.ID AND tc_played > 0 AND C.c_show = 1 AND C.series_id = '.$itemid.' GROUP BY C.c_id ORDER BY C.c_id DESC';

		} //end of if ( $post_type == "bblm_cup" )
		else if ( $post_type == "bblm_season" ) {

			$complistingsql = 'SELECT P.post_title, P.guid, SUM(T.tc_played) AS PLD, SUM(T.tc_tdfor) AS TD, SUM(T.tc_casfor) AS CAS, SUM(T.tc_comp) AS COMP, SUM(T.tc_int) AS cINT FROM '.$wpdb->prefix.'team_comp T, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE T.c_id = C.c_id AND J.tid = C.c_id AND J.prefix = \'c_\' AND J.pid = P.ID AND tc_played > 0 AND C.c_show = 1 AND C.sea_id = '.$itemid.' GROUP BY C.c_id ORDER BY C.c_id DESC';

		} //end of else if ( $post_type == "bblm_season" ) {

		if ( $compl = $wpdb->get_results( $complistingsql ) ) {
			$zebracount = 1;
?>
			<table class="bblm_tbl bblm_sortable sortable">
				<thead>
					<tr>
						<th class="tbl_title bblm_tbl_title"><?php echo __( 'Competition(s)', 'bblm' ); ?></th>
						<th class="tbl_stat bblm_tbl_stat"><?php echo __( '# Games', 'bblm' ); ?></th>
						<th class="tbl_stat bblm_tbl_stat"><?php echo __( 'TD', 'bblm' ); ?></th>
						<th class="tbl_stat bblm_tbl_stat"><?php echo __( 'CAS', 'bblm' ); ?></th>
						<th class="tbl_stat bblm_tbl_stat"><?php echo __( 'COMP', 'bblm' ); ?></th>
						<th class="tbl_stat bblm_tbl_stat"><?php echo __( 'INT', 'bblm' ); ?></th>
					</tr>
				</thead>
				<tbody>
<?php
			foreach ( $compl as $cl ) {
				if ( $zebracount % 2 ) {
					echo '<tr>';
				}
				else {
					echo '<tr class="tbl_alt bblm_tbl_alt">';
				}
				echo '<td><a href="' . $cl->guid . '" title="View more info about "' . $cl->post_title . '">' . $cl->post_title . '</a></td>';
				echo '<td>' . ( $cl->PLD / 2 ) . '</td>';
				echo '<td>' . $cl->TD . '</td>';
				echo '<td>' . $cl->CAS . '</td>';
				echo '<td>' . $cl->COMP . '</td>';
				echo '<td>' . $cl->cINT . '</td>';
				echo '</tr>';

				$zebracount++;
			} //end of foreach
			echo '</tbody>';
			echo '</table>';
		}
		else {

			if ( $post_type == "bblm_cup" ) {

				echo '<p>' . __( 'No Teams have competed for this Championship Cup', 'bblm' ) . '</p>';

			}

		}






	} // end of display_comp_list_with_stats()


} //end of class

new BBLM_CPT_Comp();
