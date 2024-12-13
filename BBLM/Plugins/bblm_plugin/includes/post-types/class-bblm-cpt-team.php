<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Teams CPT functions
 *
 * Defines the functions related to the Teams CPT (archive page logic, display functions etc)
 * For admin functions reloated to the CPT see the includes/admin/post-types directory
 *
 * @class 		BBLM_CPT_Team
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/CPT
 * @version   1.1.2
 */

class BBLM_CPT_Team {

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

	    if( is_post_type_archive( 'bblm_team' ) && !is_admin() && $query->is_main_query() ) {
	        $query->set( 'posts_per_page', -1 );
          $query->set( 'orderby', 'title' );
          $query->set( 'order', 'asc' );
          $query->set( 'post_status', 'publish' );
	    }

	  }

 /**
  * Outputs a team listing ewith detailed statistics
  *
  * @param wordpress $query
  * @return string $output
  */
  public function display_team_list_with_stats() {
		global $wpdb;
		global $post;

		$post_type = get_post_type(); //Determine the CPT that is calling this function

		$itemid = get_the_ID(); //The ID of the Page being displayed

		if ( $post_type == "bblm_cup" ) {

			$teamistingsql = 'SELECT SUM(T.tc_played) AS TP, SUM(T.tc_W) AS TW, SUM(T.tc_L) AS TL, SUM(T.tc_D) AS TD, SUM(T.tc_tdfor) AS TDF, SUM(T.tc_tdagst) AS TDA, SUM(T.tc_casfor) AS TCF, SUM(T.tc_casagst) AS TCA, SUM(T.tc_INT) AS TI, SUM(T.tc_comp) AS TC, Z.WPID FROM '.$wpdb->prefix.'team_comp T, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'team Z WHERE Z.t_id = T.t_id AND C.WPID = T.c_id AND Z.t_show = 1 AND T.tc_played > 0 AND C.series_id = '.$itemid.' GROUP BY T.t_id ORDER BY Z.t_name ASC';

		} //end of if ( $post_type == "bblm_cup" )
		else if ( $post_type == "bblm_season" ) {

				$teamistingsql = 'SELECT Z.WPID, SUM(T.tc_played) AS TP, SUM(T.tc_W) AS TW, SUM(T.tc_L) AS TL, SUM(T.tc_D) AS TD, SUM(T.tc_tdfor) AS TDF, SUM(T.tc_tdagst) AS TDA, SUM(T.tc_casfor) AS TCF, SUM(T.tc_casagst) AS TCA, SUM(T.tc_INT) AS TI, SUM(T.tc_comp) AS TC FROM '.$wpdb->prefix.'team_comp T, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'team Z WHERE Z.t_id = T.t_id anD Z.t_show = 1 AND C.WPID = T.c_id AND C.c_counts = 1 AND C.sea_id = ' . $itemid . ' GROUP BY T.t_id ORDER BY Z.t_name ASC';

		} //end of else if ( $post_type == "bblm_season" ) {


		if ($teamstats = $wpdb->get_results($teamistingsql)) {
			$zebracount = 1;
		?>
			<div role="region" aria-labelledby="Caption01" tabindex="0">
			<table class="bblm_table bblm_sortable">
			<thead>
				<tr>
					<th class="bblm_tbl_name"><?php echo __( 'Team', 'bblm' ); ?></th>
					<th class="bblm_tbl_stat"><?php echo __( 'P', 'bblm' ); ?></th>
					<th class="bblm_tbl_stat"><?php echo __( 'W', 'bblm' ); ?></th>
					<th class="bblm_tbl_stat"><?php echo __( 'L', 'bblm' ); ?></th>
					<th class="bblm_tbl_stat"><?php echo __( 'D', 'bblm' ); ?></th>
					<th class="bblm_tbl_stat"><?php echo __( 'TF', 'bblm' ); ?></th>
					<th class="bblm_tbl_stat"><?php echo __( 'TA', 'bblm' ); ?></th>
					<th class="bblm_tbl_stat"><?php echo __( 'CF', 'bblm' ); ?></th>
					<th class="bblm_tbl_stat"><?php echo __( 'CA', 'bblm' ); ?></th>
					<th class="bblm_tbl_stat"><?php echo __( 'COMP', 'bblm' ); ?></th>
					<th class="bblm_tbl_stat"><?php echo __( 'INT', 'bblm' ); ?></th>
					<th class="bblm_tbl_stat"><?php echo __( 'WIN%', 'bblm' ); ?></th>
				</tr>
			</thead>
			<tbody>
<?php
			foreach ( $teamstats as $tst ) {
				if ($zebracount % 2) {
					echo '<tr class="bblm_tbl_alt">';
				}
				else {
					echo '<tr>';
				}

				echo '<td>' . bblm_get_team_link( $tst->WPID ) . '</td>';
				echo '<td>' . $tst->TP . '</td>';
				echo '<td>' . $tst->TW . '</td>';
				echo '<td>' . $tst->TL . '</td>';
				echo '<td>' . $tst->TD . '</td>';
				echo '<td>' . $tst->TDF . '</td>';
				echo '<td>' . $tst->TDA . '</td>';
				echo '<td>' . $tst->TCF . '</td>';
				echo '<td>' . $tst->TCA . '</td>';
				echo '<td>' . $tst->TC . '</td>';
				echo '<td>' . $tst->TI . '</td>';

				if ( $tst->TP > 0 ) {
					echo '<td>' . number_format( ( ($tst->TW / $tst->TP ) * 100 ) ) . '%</td>';
				}
				else {
					echo '<td>N/A</td>';
				}
        echo '</tr>';
				$zebracount++;
			} //end of foreach
			echo '</tbody>';
			echo '</table>';
			echo '</div>';
		}
		else {

			if ( $post_type == "bblm_cup" ) {

				echo '<p>' . __( 'No Teams have competed for this Championship Cup', 'bblm' ) . '</p>';

			}

		}

  } //end of display_team_list_with_stats()

	/**
		* returns the number of sponsors the team has
		* takes in the WPID of the team in question
		*
		* @param wordpress $query
		* @return intiger
		*/
		public static function get_team_sponsors_count( $ID ) {

			$terms = wp_get_post_terms( $ID, 'team_sponsors');
			$terms_count = count ( $terms );

			return $terms_count;

		} //end of get_team_sponsors_count()

	/**
		* displays the teams custom logo, or the race icon if it does not exixst
		* takes in the WPID of the team in question
		* This does not sanitise for the TBD team. For that use the bblm_get_team_link_logo() function
		*
		* @param wordpress $query
		* @param size - the size required for the image
		* @return none
		*/
		public static function display_team_logo( $ID, $size ) {
			global $wpdb;

			switch ( $size ) {
			 case ( 'medium' == $size ):
					 break;
			 case ( 'icon' == $size ):
					 break;
			 case ( 'mini' == $size ):
					 break;
			 default :
					 $size = 'icon';
					 break;
		 	 }

			 //Determine if a custom logo is present
			 if ( has_post_thumbnail( $ID ) ) {

				 //The team has a thumbnail
				 echo get_the_post_thumbnail( $ID, 'bblm-fit-' . $size );

			 }
			 else {
				 //Display the race Icon
				 $racesql = 'SELECT r_id FROM '.$wpdb->prefix.'team WHERE WPID = ' . $ID;
				 $rid = $wpdb->get_var($racesql);

				 BBLM_CPT_Race::display_race_icon( $rid, $size );
			 }

		} //end of display_team_logo()

	/**
		* returns the teams custom logo, or the race icon if it does not exixst
		* takes in the WPID of the team in question
		* This does not sanitise for the TBD team. For that use the bblm_get_team_link_logo() function
		*
		* @param wordpress $query
		* @param size - the size required for the image
		* @return the icon
		*/
		public static function get_team_logo( $ID, $size ) {
				global $wpdb;

				$output = '';

				switch ( $size ) {
				 case ( 'medium' == $size ):
						 break;
				 case ( 'icon' == $size ):
						 break;
				 case ( 'mini' == $size ):
						 break;
				 default :
						 $size = 'icon';
						 break;
			 	 }

				 //Determine if a custom logo is present
				 if ( has_post_thumbnail( $ID ) ) {

					 //The team has a thumbnail
					 $output = get_the_post_thumbnail( $ID, 'bblm-fit-' . $size );

				 }
				 else {
					 //Display the race Icon
					 $racesql = 'SELECT r_id FROM '.$wpdb->prefix.'team WHERE WPID = ' . $ID;
					 $rid = $wpdb->get_var( $racesql );

					 $output = BBLM_CPT_Race::get_race_icon( $rid, $size );
				 }

				 return $output;

			} //end of get_team_logo()


} //end of class

new BBLM_CPT_Team();
