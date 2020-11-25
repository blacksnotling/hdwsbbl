<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Competition CPT admin functions
 *
 * Defines the admin functions related to the Competition CPT (edit screens, custom messages, post saving etc)
 * For the Meta-Boxes, see the meta-boxes directory
 * For front end functions reloated to the CPT see the includes/post-types directory
 *
 * @class 		BBLM_Admin_CPT_Competition
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/Admin/CPT
 * @version   1.1
 */

class BBLM_Admin_CPT_Competition {

 /**
  * Constructor
 	*/
  public function __construct() {

    add_filter( 'manage_edit-bblm_comp_columns', array( $this, 'my_edit_competition_columns' ) );
    add_action( 'manage_bblm_comp_posts_custom_column', array( $this, 'my_manage_competition_columns' ), 10, 2 );
		add_filter( 'manage_edit-bblm_comp_sortable_columns', array( $this, 'my_manage_sortable_columns' ) );
		add_action( 'restrict_manage_posts', array( $this, 'comp_filter_season' ) );
		add_filter( 'parse_query', array( $this, 'comp_filter_comp_by_season' ) );

 	}

  /**
   * Sets the Column headers for the CPT edit list screen
   */
  function my_edit_competition_columns( $columns ) {

  	$columns = array(
			'cb' => '<input type="checkbox" />',
  		'title' => __( 'Competition', 'bblm' ),
			'id' => __( 'ID', 'bblm' ),
  		'season' => __( 'Season', 'bblm' ),
			'cup' => __( 'Championship', 'bblm' ),
			'teams' => __( 'Teams', 'bblm' ),
  		'sdate' => __( 'Started', 'bblm' ),
			'fdate' => __( 'Ended', 'bblm' ),
			'awards' => __( 'Awards', 'bblm' ),
  	);

  	return $columns;

  }

  /**
   * Sets the Column content for the CPT edit list screen
   */
  function my_manage_competition_columns( $column, $post_id ) {
  	global $post;

    switch( $column ) {

			/* If displaying the 'id' column. */
      case 'id' :

        echo $post_id;

      break;

      /* If displaying the 'season' column. */
      case 'season' :

        echo bblm_get_season_name( get_post_meta( $post_id, 'comp_season', true ) );

      break;

			/* If displaying the 'cup' column. */
			case 'cup' :

				echo bblm_get_season_name( get_post_meta( $post_id, 'comp_cup', true ) );

			break;

			/* If displaying the 'team' column. */
			case 'teams' :

				echo '<a href="';
				bloginfo('url');
				echo '/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.edit.comp_team.php&comp=' . $post_id . '" title="Assign teams to a Competition">Manage Teams</a>';

			break;

			/* If displaying the 'awards' column. */
			case 'awards' :

				echo 'Manage Awards';

			break;

      // If displaying the 'sdate' column.
      case 'sdate' :

        $type = get_post_meta( $post_id, 'comp_sdate', true );
        if ( '0000-00-00' == $type ) {

  			     echo __( 'TBC', 'bblm' );

        }
        else {

          echo date("d-m-Y (25y)", strtotime( $type ) );

        }

      break;

			// If displaying the 'fdate' column.
      case 'fdate' :

        //$bblm_season = new BBLM_CPT_Season;

        if ( BBLM_CPT_Comp::is_competition_active( $post_id ) ) {

  			     echo __( 'In Progress', 'bblm' );

        }
        else {

          echo date("d-m-Y (25y)", strtotime( get_post_meta( $post_id, 'comp_fdate', true ) ) );

        }

      break;

      // Break out of the switch statement for anything else.
      default :
      break;

    }

  } //end of my_manage_competition_columns

	 /*
		* Sets the columns that are filterable
		*/
		function my_manage_sortable_columns( $columns ) {
			$columns['season'] = 'comp_season_filter';
			return $columns;
		}

	 /**
		* Allow the page to be filtered on meta_values
 		*/
		function comp_filter_season() {
			global $typenow;
			global $wpdb;

			if ( $typenow == 'bblm_comp' ) {

				$query = $wpdb->prepare('
				SELECT DISTINCT pm.meta_value FROM %1$s pm
				LEFT JOIN %2$s p ON p.ID = pm.post_id
				WHERE pm.meta_key = "%3$s"
				AND p.post_status = "%4$s"
				AND p.post_type = "%5$s"
				ORDER BY "%3$s"',
				$wpdb->postmeta,
				$wpdb->posts,
				'comp_season',
				'publish',
				'bblm_comp',
				'comp_season'
			);
			$results = $wpdb->get_col($query);
			$current_season = '';
			if( isset( $_GET['bblm_comp-filter-season'] ) ) {
				$current_season = $_GET['bblm_comp-filter-season']; // Check if option has been selected
			}
	?>
	 <select name="bblm_comp-filter-season" id="bblm_comp-filter-season">
			<option value="all" <?php selected( 'all', $current_season ); ?>><?php _e( 'All Seasons', 'bblm' ); ?></option>
			<?php foreach( $results as $key ) { ?>
				<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $current_season ); ?>><?php echo bblm_get_season_name( $key ); ?></option>
			<?php } ?>
		</select>
	<?php }
		} // end of comp_filter_season()

	 /*
		* *Modifies thre WP_Query to account for any selected filter
		*/
		function comp_filter_comp_by_season( $query ) {
			global $pagenow;
			// Get the post type
			$post_type = isset( $_GET['post_type'] ) ? $_GET['post_type'] : '';
			if ( is_admin() && $pagenow=='edit.php' && $post_type == 'bblm_comp' && isset( $_GET['bblm_comp-filter-season'] ) && $_GET['bblm_comp-filter-season'] !='all' ) {
				$query->query_vars['meta_key'] = 'comp_season';
		    $query->query_vars['meta_value'] = $_GET['bblm_comp-filter-season'];
		    $query->query_vars['meta_compare'] = '=';
		  }
		} //end of comp_filter_comp_by_season()

	 /*
		* Updates the *comp table with the competition details upon saving,
		* or creating a new competition
		* Assumes input has already been sanitised
		*/
		public static function update_comp_table( $ID, $args ) {
			global $wpdb;

			//check to see if the comp is already in the database
			$compexistsql = 'SELECT WPID FROM `'.$wpdb->prefix.'comp` WHERE WPID = ' . $ID;
			if ( $wpdb->get_var( $compexistsql ) ) {
				return $ID;
			}
			else {

				//new competition to be inserted
				$bblmdatasql = 'INSERT INTO `'.$wpdb->prefix.'comp` (`c_id`, `c_name`, `series_id`, `sea_id`, `ct_id`, `c_active`, `c_counts`, `c_pW`, `c_pL`, `c_pD`, `c_ptd`, `c_pcas`, `c_pround`, `c_sdate`, `c_edate`, `c_showstandings`, `c_show`, `type_id`, `WPID`) ';
				$bblmdatasql .= 'VALUES (\'\', \'Competition\', \''.$args['comp_cup'].'\', \''.$args['comp_season'].'\', \''.$args['comp_format'].'\', \'1\', \''.$args['comp_counts'].'\', \''.$args['comp_pw'].'\', \''.$args['comp_pl'].'\', \''.$args['comp_pd'].'\', \''.$args['comp_ptd'].'\', \''.$args['comp_pcas'].'\', \''.$args['comp_pround'].'\', \'0000-00-00 00:00:00\', \'0000-00-00 00:00:00\', \'0\', \'1\', \'1\', \''.$ID.'\')';
				$wpdb->query( $bblmdatasql );

			}

		} //end of update_comp_table

		/*
		 * Marks a fixture as being complete by updating the *fixture table
		 * Assumes input has already been sanitised
		 */
		 public static function update_fixture_complete( $ID ) {
			 global $wpdb;

			 $completefixturesql = 'UPDATE `'.$wpdb->prefix.'fixture` SET `f_complete` = \'1\' WHERE `f_id` = ' . $ID . ' LIMIT 1';
			 if ( $wpdb->query( $completefixturesql ) ) {
				 return true;
			 }
			 else {
				 return false;
			 }

		 } //end of update_fixture_complete

		 /*
			* Updates the standings for a team in a competition
			* takes in the t_ID of the team and competition
			* Assumes input has already been sanitised
			*/
			public static function update_team_standings( $teamID, $compID, $divID ) {
				global $wpdb;

				//initialise variables
				$comp = array();
				$tdeatails = array();

				//gather information about the competition
				$compdatasql = "SELECT c_counts, c_pW, c_pL, c_pD, c_ptd, c_pcas, c_pround FROM ".$wpdb->prefix."comp WHERE WPID = " . $compID;
				if ( $compd = $wpdb->get_row( $compdatasql ) ) {
					$comp['counts'] = $compd->c_counts;
					$comp['pW'] = $compd->c_pW;
					$comp['pL'] = $compd->c_pL;
					$comp['pD'] = $compd->c_pD;
					$comp['ptd'] = $compd->c_ptd;
					$comp['pcas'] = $compd->c_pcas;
					$comp['round'] = $compd->c_pround;
				}

				//Determine the current wins, Losses, draws and points
				$teamAPerformancesql = "SELECT T.mt_result, COUNT(*) AS PLAYED, SUM(T.mt_td) AS TD, SUM(T.mt_cas) AS CAS, SUM(T.mt_int) AS totINT, SUM(T.mt_comp) AS totCOMP FROM ".$wpdb->prefix."match_team T, ".$wpdb->prefix."match M WHERE T.m_id = M.m_id AND t_id = " . $teamID . " AND M.c_id = " . $compID . " AND M.div_id = " . $divID . " GROUP BY T.mt_result ORDER BY T.mt_result";

				$tdeatails['W'] = 0;
				$tdeatails['L'] = 0;
				$tdeatails['D'] = 0;

				if ( $teamAPerformance = $wpdb->get_results( $teamAPerformancesql ) ) {
					foreach ( $teamAPerformance as $tAp ) {
						if ("W" == $tAp->mt_result ) {
							$tdeatails['W'] = (int) $tAp->PLAYED;
						}
						elseif ("L" == $tAp->mt_result ) {
							$tdeatails['L'] = (int) $tAp->PLAYED;
						}
						elseif ("D" == $tAp->mt_result ) {
							$tdeatails['D'] = (int) $tAp->PLAYED;
						}
						$tdeatails['TD'] = $tdeatails['TD'] + (int) $tAp->TD;
						$tdeatails['CAS'] = $tdeatails['CAS'] + (int) $tAp->CAS;
						$tdeatails['INT'] = $tdeatails['INT'] + (int) $tAp->totINT;
						$tdeatails['COMP'] = $tdeatails['COMP'] + (int) $tAp->totCOMP;
						$tdeatails['PLD'] = $tdeatails['PLD'] + (int) $tAp->PLAYED;
					}
				}

				//Calcualte TD and CAS Against
				$teamAagainst = "SELECT SUM(T.mt_td) AS totTD, SUM(T.mt_cas) AS totCAS FROM ".$wpdb->prefix."match_team T, ".$wpdb->prefix."match M WHERE T.m_id = M.m_id AND (M.m_teamA = " . $teamID . " OR M.m_teamB = " . $teamID . ") AND M.c_id = " . $compID . " AND M.div_id = " . $divID . " ORDER BY T.mt_result";
				if ( $tAaga = $wpdb->get_row( $teamAagainst ) ) {
					$tdeatails['TDa'] = $tAaga->totTD - $tdeatails['TD'];
					$tdeatails['CASa'] = $tAaga->totCAS - $tdeatails['CAS'];
				}

				//Determine the points values
				$tdeatails['points'] = ( $tdeatails['W'] * $comp['pW'] ) + ( $tdeatails['L'] * $comp['pL'] ) + ( $tdeatails['D'] * $comp['pD'] ) + ( $tdeatails['TD'] * $comp['ptd'] ) + ( $tdeatails['CAS'] * $comp['pcas'] );

				//if the competition rounds the results then divide the points by number of games played
				if ( $comp['round'] ) {
					$tdeatails['points'] = ceil( $tdeatails['points'] / $tdeatails['PLD'] );
				}

				//update the team_comp record
				$teamAupdatecomprecsql = "UPDATE ".$wpdb->prefix."team_comp SET `tc_played` = '" . $tdeatails['PLD'] . "', `tc_W` = '" . $tdeatails['W'] . "', `tc_L` = '" . $tdeatails['L'] . "', `tc_D` = '" . $tdeatails['D'] . "', `tc_tdfor` = '" . $tdeatails['TD'] . "', `tc_tdagst` = '" . $tdeatails['TDa'] . "', `tc_casfor` = '" . $tdeatails['CAS'] . "', `tc_casagst` = '" . $tdeatails['CASa'] . "', `tc_int` = '" . $tdeatails['INT'] . "', `tc_comp` = '" . $tdeatails['COMP'] . "', `tc_points` = '" . $tdeatails['points'] . "' WHERE t_id = " . $teamID . " AND c_id = " . $compID . " AND div_id = " . $divID;
				if ( $wpdb->query( $teamAupdatecomprecsql ) ) {
					return true;
				}
				else {
					return false;
				}

			} //end of update_team_standings

} //end of class

new BBLM_Admin_CPT_Competition();
