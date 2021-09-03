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
 * @version   1.3.1
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
  //        $query->set( 'orderby', 'title' );
    //      $query->set( 'order', 'asc' );
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

			$complistingsql = 'SELECT C.WPID AS CWPID, SUM(T.tc_played) AS PLD, SUM(T.tc_tdfor) AS TD, SUM(T.tc_casfor) AS CAS, SUM(T.tc_comp) AS COMP, SUM(T.tc_int) AS cINT FROM '.$wpdb->prefix.'team_comp T, '.$wpdb->prefix.'comp C WHERE T.c_id = C.WPID AND tc_played > 0 AND C.series_id = '.$itemid.' GROUP BY C.c_id ORDER BY C.c_id DESC';

		} //end of if ( $post_type == "bblm_cup" )
		else if ( $post_type == "bblm_season" ) {

			$complistingsql = 'SELECT C.WPID AS CWPID, SUM(T.tc_played) AS PLD, SUM(T.tc_tdfor) AS TD, SUM(T.tc_casfor) AS CAS, SUM(T.tc_comp) AS COMP, SUM(T.tc_int) AS cINT FROM '.$wpdb->prefix.'team_comp T, '.$wpdb->prefix.'comp C WHERE T.c_id = C.WPID AND tc_played > 0 AND C.sea_id = '.$itemid.' GROUP BY C.c_id ORDER BY C.c_id DESC';

		} //end of else if ( $post_type == "bblm_season" ) {

		if ( $compl = $wpdb->get_results( $complistingsql ) ) {
			$zebracount = 1;
?>
			<div role="region" aria-labelledby="Caption01" tabindex="0">
			<table class="bblm_table bblm_sortable">
				<thead>
					<tr>
						<th class="bblm_tbl_title"><?php echo __( 'Competition(s)', 'bblm' ); ?></th>
						<th class="bblm_tbl_stat"><?php echo __( '# Games', 'bblm' ); ?></th>
						<th class="bblm_tbl_stat"><?php echo __( 'TD', 'bblm' ); ?></th>
						<th class="bblm_tbl_stat"><?php echo __( 'CAS', 'bblm' ); ?></th>
						<th class="bblm_tbl_stat"><?php echo __( 'COMP', 'bblm' ); ?></th>
						<th class="bblm_tbl_stat"><?php echo __( 'INT', 'bblm' ); ?></th>
					</tr>
				</thead>
				<tbody>
<?php
			foreach ( $compl as $cl ) {
				if ( $zebracount % 2 ) {
					echo '<tr>';
				}
				else {
					echo '<tr class="bblm_tbl_alt">';
				}
				echo '<td>' . bblm_get_competition_link( $cl->CWPID ) . '</td>';
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
			echo '</div>';
		}
		else {

			if ( $post_type == "bblm_cup" ) {

				echo '<p>' . __( 'No Teams have competed for this Championship Cup', 'bblm' ) . '</p>';

			}

		}

	} // end of display_comp_list_with_stats()

	/**
	 * Determines if a Competition is active
	 *
	 * @param int $ID the ID of the Competition (WP post ID)
	 * @return bool true (Active), or False (Completed)
	 */
	 public static function is_competition_active( $ID ) {

		 //create a Unix timestamp from the end date of a season
		 $enddate = get_post_meta( $ID, 'comp_fdate', true );
		 $compenddate = DateTime::createFromFormat('Y-m-d', $enddate );
		 $compened = $compenddate->format('U');

		 if ( ( $compened > time() ) || ( $enddate == '0000-00-00' ) ) {

			 return true;

		 }
		 else {

			 return false;

		 }

	 } //end of is_competition_active()

	/**
	 * Returns a list of all the Competitions, split by season
	 *
	 * @param wordpress $query
	 * @return none
	 */
	 public static function get_comp_listing() {

		 //Grabs a list of 'posts' from the bblm_comp CPT
		 $cpostsarg = array(
			 'post_type' => 'bblm_comp',
			 'numberposts' => -1,
//			 'meta_key' => 'comp_season',
			 'meta_query' => array(
				 'season' => array(
					 'key' => 'comp_season',
					 'type' => 'NUMERIC'
				 ),
				),
				'orderby' => 'season'
		 );
		 if ( $cposts = get_posts( $cpostsarg ) ) {
			 $is_first = 1;
			 $current_sea = 0;

			 foreach( $cposts as $c ) {
				 $cm = get_post_meta( $c->ID );

				 if ( $cm[ 'comp_season' ][0] !== $current_sea ) {
					 $current_sea = $cm[ 'comp_season' ][0];

					 if ( 1 !== $is_first ) {
						 echo '</ul>';
					 }
					 $is_first = 1;
				 }
				 if ( $is_first ) {
					 echo '<h3>' . bblm_get_season_link( $cm[ 'comp_season' ][0] ) . '</h3>';
					 echo '<ul>';
					 $is_first = 0;
				 }

				 echo '<li';
				 if ( BBLM_CPT_Comp::is_competition_active( $c->ID ) ) {
					 echo (' class="bblm_comp_active"');
				 }
				 echo '>' . bblm_get_competition_link( $c->ID ) . ' - (' . bblm_get_cup_name( $cm[ 'comp_cup' ][0] ) . ')</li>';

			 }//end of foreach
			 echo '</ul>';

		 }//end of if ( $cposts = get_posts( $cpostsarg )
		 else {
			 echo  '<p>' . __( 'Sorry, but no Competitions could be retrieved at this time, please try again later.', 'bblm' ) . '</p>';
		 }

	 } //end of get_comp_listing

	 /**
		* Returns the name of the competition format
		*
		* @param wordpress $query
		* @return string
		*/
		public static function get_comp_format_name( $ID ) {

			$ctype = get_post_meta( $ID, 'comp_format', true );
			$ctype = bblm_get_competition_format_name( $ctype );

			return $ctype;

		} //end of get_comp_format_name()

		/**
 		* Returns dates which a competition ran for
 		*
 		* @param wordpress $query
 		* @return string
 		*/
 		public static function get_comp_duration( $ID ) {

			$csdate = date("d-m-Y", strtotime( get_post_meta( $ID, 'comp_sdate', true ) ) );
			$duration = "";

			if ( BBLM_CPT_Comp::is_competition_active( $ID ) ) {

				$duration = $csdate . __( ' to the present', 'bblm' );

			}
			else {

				$cfdate = date("d-m-Y", strtotime( get_post_meta( $ID, 'comp_fdate', true ) ) );
				$duration = $csdate . __( ' to', 'bblm' ) . $cfdate;

			}

 			return $duration;

 		} //end of get_comp_duration()

		/**
 		* Returns if the competition counts towards ststisticd
		* or if it id sn exhibition / friendly game
 		*
 		* @param wordpress $query
 		* @return bool
 		*/
 		public static function does_comp_count( $ID ) {

 			$ccounts = get_post_meta( $ID, 'comp_counts', true );

 			return $ccounts;

 		} //end of does_comp_count()

		/**
		* Returns the number of teams in the competition
		*
		* @param $ID //the number of teams in the comp
		* @return INT
		*/
		public static function get_number_of_teams( $ID ) {
			global $wpdb;

			$ID = (int) $ID;
			$numteams = 0;

			$sql = 'SELECT COUNT(*) AS NUMTEAMS FROM '.$wpdb->prefix.'team_comp WHERE c_id = ' . $ID;
			if ( $numteamscount = $wpdb->get_var( $sql ) ) {
				$numteams = $numteamscount;
			}

			return $numteams;

		} //end of get_number_of_teams()

		/**
		 * Outputs the tournament brackets for this competition
		 *
		 * @param $ID //may be called from a non-competition page
		 * @return HTML
		 */
		 public static function display_comp_brackets( $ID ) {
			 global $wpdb;

			 $ID = (int) $ID;

			 $bracketssql = 'SELECT C.cb_text, D.div_name FROM '.$wpdb->prefix.'comp_brackets C, '.$wpdb->prefix.'division D WHERE C.div_id = D.div_id AND C.c_id = '.$ID.' ORDER BY C.div_ID DESC, cb_order ASC';
			 $brackets = $wpdb->get_results( $bracketssql, ARRAY_N );
			 //determine number of games (which determines the layout to be used
			 $numgames = count( $brackets );
			 if ( 7 == $numgames ) {
?>
		<h3 class="bblm-table-caption"><?php echo __( 'Tournament Standings', 'bblm' ); ?></h3>
		 <table class="bblm_table bblm_brackets">
			 <tr>
				 <th><?php echo $brackets[0][1]; ?></th>
				 <th><?php echo $brackets[4][1]; ?></th>
				 <th><?php echo $brackets[6][1]; ?></th>
			 </tr>
			 <tr>
				 <td><?php echo $brackets[0][0]; ?></td>
				 <td rowspan="2"><?php echo $brackets[4][0]; ?></td>
				 <td rowspan="4"><?php echo $brackets[6][0]; ?></td>
						 </tr>
						 <tr>
				 <td><?php echo $brackets[1][0]; ?></td>
			 </tr>
			 <tr>
				 <td><?php echo $brackets[2][0]; ?></td>
				 <td rowspan="2"><?php echo $brackets[5][0]; ?></td>
			 </tr>
			 <tr>
				 <td><?php echo $brackets[3][0]; ?></td>
			 </tr>
		 </table>
<?php
			 } //end of if 7 games
			 else if ( 3 == $numgames ) {
?>
		 <table class="bblm_table bblm_brackets">
			 <tr>
				 <th><?php echo $brackets[0][1]; ?></th>
				 <th><?php echo $brackets[2][1]; ?></th>
			 </tr>
			 <tr>
				 <td><?php echo $brackets[0][0]; ?></td>
				 <td rowspan="2"><?php echo $brackets[2][0]; ?></td>
			 </tr>
			 <tr>
				 <td><?php echo $brackets[1][0]; ?></td>
			 </tr>
		 </table>
<?php
			 } //end of else if 3 games
								 else if ( 15 == $numgames ) {
			 ?>

		 <table class="bblm_table bblm_brackets">
			 <tr>
				 <th><?php echo $brackets[0][1]; ?></th>
				 <th><?php echo $brackets[8][1]; ?></th>
				 <th><?php echo $brackets[12][1]; ?></th>
				 <th><?php echo $brackets[14][1]; ?></th>
			 </tr>
			 <tr>
				 <td><?php echo $brackets[0][0]; ?></td>
				 <td rowspan="2"><?php echo $brackets[8][0]; ?></td>
				 <td rowspan="4"><?php echo $brackets[12][0]; ?></td>
				 <td rowspan="8"><?php echo $brackets[14][0]; ?></td>
			 </tr>
			 <tr>
				 <td><?php echo $brackets[1][0]; ?></td>
			 </tr>
			 <tr>
				 <td><?php echo $brackets[2][0]; ?></td>
				 <td rowspan="2"><?php echo $brackets[9][0]; ?></td>
			 </tr>
			 <tr>
				 <td><?php echo $brackets[3][0]; ?></td>
			 </tr>
			 <tr>
				 <td><?php echo $brackets[4][0]; ?></td>
				 <td rowspan="2"><?php echo $brackets[10][0]; ?></td>
				 <td rowspan="4"><?php echo $brackets[13][0]; ?></td>
			 </tr>
			 <tr>
				 <td><?php echo $brackets[5][0]; ?></td>
			 </tr>
			 <tr>
				 <td><?php echo $brackets[6][0]; ?></td>
				 <td rowspan="2"><?php echo $brackets[11][0]; ?></td>
			 </tr>
			 <tr>
				 <td><?php echo $brackets[7][0]; ?></td>
			 </tr>
		 </table>
<?php
			 } //end of else if 15 games
			 else {
				 //something has gone wrong
				 echo '<p>' . __( 'Please bear with us - a Goblin has got into the machinery', 'bblm') . '</p>';
			 } //end of else if

		 }

} //end of class

new BBLM_CPT_Comp();
