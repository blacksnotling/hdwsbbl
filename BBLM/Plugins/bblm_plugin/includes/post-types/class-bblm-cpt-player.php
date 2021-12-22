<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Players CPT functions
 *
 * Defines the functions related to the Players CPT (archive page logic, display functions etc)
 * For admin functions reloated to the CPT see the includes/admin/post-types directory
 *
 * @class 		BBLM_CPT_Player
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/CPT
 * @version   1.0
 */

class BBLM_CPT_Player {

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

	    if( is_post_type_archive( 'bblm_player' ) && !is_admin() && $query->is_main_query() ) {
	        $query->set( 'posts_per_page', -1 );
          $query->set( 'orderby', 'title' );
          $query->set( 'order', 'asc' );
          $query->set( 'post_status', 'publish' );
	    }

	  }

		/**
		 * returns if a player is legacy (played under a former ruleset)
		 *
		 * @param wordpress $query
		 * @return bool
		 */
		 public static function is_player_legacy( $ID ) {
			 global $wpdb;

			 $playersql = 'SELECT P.p_legacy FROM '.$wpdb->prefix.'player P WHERE P.WPID = '. $ID;
			 $pd = $wpdb->get_row( $playersql );

			 if ( $pd->p_legacy ) {
				 return true;
			 }
			 else {
				 return false;
			 }

		 } //end of is_player_legacy()

		 /**
		 * determines if a player has a match history
		 *
		 * @param wordpress $query
		 * @return bool
		 */
		 public static function has_player_played( $ID ) {
			 global $wpdb;

			 $playedsql = 'SELECT COUNT(*) AS GAMES FROM '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'player P WHERE P.p_id = M.p_id AND M.mp_counts = 1 AND P.WPID = ' . $ID;
			 $stats = $wpdb->get_row( $playedsql );
			 if ( $stats->GAMES > 0 ) {
				 return TRUE;
			 }
			 else {
				 return FALSE;
			 }

		 } //end of has_player_played()


		 /**
 		 * Displays a table of kills the plyaer has made
 		 *
 		 * @param wordpress $query
 		 * @return string
 		 */
		 public static function display_player_kills( $ID ) {
			 global $wpdb;

			 $playersql = 'SELECT P.p_id FROM '.$wpdb->prefix.'player P WHERE P.WPID = '. $ID;
			 $pd = $wpdb->get_row( $playersql );

			 $killersql = 'SELECT P.WPID AS PWPID, T.WPID AS TWPID, X.pos_name FROM '.$wpdb->prefix.'player_fate F, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'position X WHERE F.p_id = P.p_id AND P.t_id = T.t_id AND P.pos_id = X.pos_id AND F.pf_killer = '.$pd->p_id.' AND F.p_id != '.$pd->p_id.' ORDER BY F.m_id ASC';
			 if ( $killer = $wpdb->get_results( $killersql ) ) {
				 $zebracount = 1;
				 //If the player has killed people
 ?>
				 <h3 class="bblm-table-caption"><?php echo __( 'Players Killed','bblm' ); ?></h3>
				 <div role="region" aria-labelledby="Caption01" tabindex="0">
					 <table class="bblm_table">
						 <thead>
							 <tr>
								 <th class="bblm_tbl_title"><?php echo __( 'Player Killed','bblm' ); ?></th>
								 <th class="bblm_tbl_stat"><?php echo __( 'Position','bblm' ); ?></th>
								 <th class="bblm_tbl_stat"><?php echo __( 'Team','bblm' ); ?></th>
							 </tr>
						 </thead>
						 <tbody>
 <?php
				foreach ( $killer as $k ) {
					 if ( $zebracount % 2 ) {
						 echo '<tr class="bblm_tbl_alt">';
					 }
					 else {
						 echo '<tr>';
					 }
					 echo '<td>' . bblm_get_player_link( $k->PWPID ) . '</td>';
					 echo '<td>' . esc_html( $k->pos_name ) . '</td>';
					 echo '<td>' . bblm_get_team_link( $k->TWPID ) . '</td>';
					 echo '</tr>';
					 $zebracount++;
				}
 ?>
						 </tbody>
					 </table>
				 </div>
 <?php
			}
		}//end of display_player_kills()

		/**
		* Displays a table of kills the plyaer has made
		*
		* @param wordpress $query
		* @return string
		*/
		public static function display_player_performance( $ID, $scope ) {
			global $wpdb;

			switch ( $scope ) {
				case ( 'Season' == $scope ):
				$playerperfsql = 'SELECT C.sea_id AS SWPID, COUNT(*) AS GAMES, SUM(M.mp_td) AS TD, SUM(M.mp_cas) AS CAS, SUM(M.mp_comp) AS COMP, SUM(M.mp_int) AS MINT, SUM(M.mp_mvp) AS MVP, SUM(M.mp_spp) AS SPP FROM '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'match Q WHERE C.c_counts = 1 AND M.m_id = Q.WPID AND Q.c_id = C.WPID AND M.p_id = P.p_id AND P.WPID = '.$ID.' GROUP BY C.sea_id ORDER BY C.sea_id DESC';
				break;
				case ( 'Cup' == $scope ):
					$scope = 'Championsip Cup';
					$playerperfsql = 'SELECT C.series_id AS SWPID, COUNT(*) AS GAMES, SUM(M.mp_td) AS TD, SUM(M.mp_cas) AS CAS, SUM(M.mp_comp) AS COMP, SUM(M.mp_int) AS MINT, SUM(M.mp_mvp) AS MVP, SUM(M.mp_spp) AS SPP FROM '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'match Q WHERE C.c_counts = 1 AND M.m_id = Q.WPID AND Q.c_id = C.WPID AND M.p_id = P.p_id AND P.WPID = '.$ID.' GROUP BY C.series_id ORDER BY C.series_id DESC';
				break;
				case ( 'Comp' == $scope ):
					$scope = 'Competition';
					$playerperfsql = 'SELECT COUNT(*) AS GAMES, SUM(M.mp_td) AS TD, SUM(M.mp_cas) AS CAS, SUM(M.mp_comp) AS COMP, SUM(M.mp_int) AS MINT, SUM(M.mp_mvp) AS MVP, SUM(M.mp_spp) AS SPP, C.WPID AS SWPID FROM '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'match Q WHERE M.m_id = Q.WPID AND Q.c_id = C.WPID AND M.p_id = P.p_id AND P.WPID = '.$ID.' GROUP BY C.c_id ORDER BY C.c_id DESC';
				break;
				default :
					$scope = 'Season';
				break;
			}

?>
			<div role="region" aria-labelledby="Caption01" tabindex="0">
			<table class="bblm_table bblm_table_collapsable">
				<thead>
					<tr>
						<th class="bblm_tbl_title"><?php echo $scope; ?></th>
						<th class="bblm_tbl_stat"><?php echo __( 'P', 'bblm' ); ?></th>
						<th class="bblm_tbl_stat"><?php echo __( 'TD', 'bblm' ); ?></th>
						<th class="bblm_tbl_stat"><?php echo __( 'CAS', 'bblm' ); ?></th>
						<th class="bblm_tbl_stat bblm_tbl_collapse"><?php echo __( 'COMP','bblm' ); ?></th>
						<th class="bblm_tbl_stat bblm_tbl_collapse"><?php echo __( 'INT','bblm' ); ?></th>
						<th class="bblm_tbl_stat bblm_tbl_collapse"><?php echo __( 'MVP','bblm' ); ?></th>
						<th class="bblm_tbl_stat"><?php echo __( 'SPP', 'bblm' ); ?></th>
					</tr>
				</thead>
				<tbody>
	<?php
		 if ( $playersea = $wpdb->get_results( $playerperfsql ) ) {
			 $zebracount = 1;
			 foreach ( $playersea as $pc ) {
				 if ( $zebracount % 2 ) {
					 echo '<tr class="bblm_tbl_alt">';
				 }
				 else {
					 echo '<tr>';
				 }
				 echo '<td>' . bblm_get_season_link( $pc->SWPID ) . '</td>';
				 echo '<td>' . $pc->GAMES . '</td>';
				 echo '<td>' . $pc->TD . '</td>';
				 echo '<td>' . $pc->CAS . '</td>';
				 echo '<td class="bblm_tbl_collapse">' . $pc->COMP . '</td>';
				 echo '<td class="bblm_tbl_collapse">' . $pc->MINT . '</td>';
				 echo '<td class="bblm_tbl_collapse">' . $pc->MVP . '</td>';
				 echo '<td>' . $pc->SPP . '</td>';
				 echo '</tr>';

				 $zebracount++;
			 }
		 }
	?>
					</tbody>
				</table>
			</div>
<?php
		} //end of display_player_performance

		/**
		* Displays a players career history
		*
		* @param wordpress $query
		* @return string
		*/
		public static function display_player_career( $ID ) {
			global $wpdb;

			$careerstatssql = 'SELECT COUNT(*) AS GAMES, SUM(M.mp_td) AS TD, SUM(M.mp_cas) AS CAS, SUM(M.mp_comp) AS COMP, SUM(M.mp_int) AS MINT, SUM(M.mp_mvp) AS MVP, SUM(M.mp_spp) AS SPP FROM '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'player P WHERE P.p_id = M.p_id AND M.t_id = T.t_id AND M.mp_counts = 1 AND P.WPID = '.$ID.' GROUP BY M.p_id ORDER BY T.t_name ASC';
			if ( $s = $wpdb->get_row( $careerstatssql ) ) {
				//The Star has played a match so continue
?>
				<div role="region" aria-labelledby="Caption01" tabindex="0">
					<table class="bblm_table bblm_table_collapsable">
						<thead>
						<tr class="bblm_tbl_alt">
							<th class="bblm_tbl_title bblm_tbl_collapse"><?php echo __( 'Career Total','bblm' ); ?></th>
							<th class="bblm_tbl_stat"><?php echo __( 'Pld','bblm' ); ?></th>
							<th class="bblm_tbl_stat"><?php echo __( 'TD','bblm' ); ?></th>
							<th class="bblm_tbl_stat"><?php echo __( 'CAS','bblm' ); ?></th>
							<th class="bblm_tbl_stat"><?php echo __( 'COMP','bblm' ); ?></th>
							<th class="bblm_tbl_stat"><?php echo __( 'INT','bblm' ); ?></th>
							<th class="bblm_tbl_stat"><?php echo __( 'MVP','bblm' ); ?></th>
							<th class="bblm_tbl_stat"><?php echo __( 'SPP','bblm' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="bblm_tbl_collapse"><?php the_title(); ?></th>
							<td><?php echo $s->GAMES; ?></th>
							<td><?php echo $s->TD; ?></th>
							<td><?php echo $s->CAS; ?></th>
							<td><?php echo $s->COMP; ?></th>
							<td><?php echo $s->MINT; ?></th>
							<td><?php echo $s->MVP; ?></th>
							<td><?php echo $s->SPP; ?></th>
						</tr>
					</tbody>
				</table>
			</div>
	<?php
			}
		} //end of display_player_career

		/**
		* Displays a players career with the teams they have played for
		*
		* @param wordpress $query
		* @return string
		*/
		public static function display_player_team_history( $ID ) {
			global $wpdb;

			$statssql = 'SELECT COUNT(*) AS GAMES, SUM(M.mp_td) AS TD, SUM(M.mp_cas) AS CAS, SUM(M.mp_comp) AS COMP, SUM(M.mp_int) AS MINT, SUM(M.mp_mvp) AS MVP, SUM(M.mp_spp) AS SPP, T.WPID AS TWPID FROM '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'player P WHERE P.p_id = M.p_id AND M.t_id = T.t_id AND M.mp_counts = 1 AND P.WPID = '.$ID.' GROUP BY T.t_id ORDER BY GAMES DESC, T.t_name ASC';
			if ( $stats = $wpdb->get_results( $statssql ) ) {
				$zebracount = 1;
?>
			<div role="region" aria-labelledby="Caption01" tabindex="0">
			<table class="bblm_table bblm_table_collapsable">
				<thead>
				<tr>
					<th class="bblm_tbl_title"><?php echo __( 'Playing for','bblm' ); ?></th>
					<th class="bblm_tbl_stat"><?php echo __( 'Pld','bblm' ); ?></th>
					<th class="bblm_tbl_stat"><?php echo __( 'TD','bblm' ); ?></th>
					<th class="bblm_tbl_stat"><?php echo __( 'CAS','bblm' ); ?></th>
					<th class="bblm_tbl_stat bblm_tbl_collapse"><?php echo __( 'COMP','bblm' ); ?></th>
					<th class="bblm_tbl_stat bblm_tbl_collapse"><?php echo __( 'INT','bblm' ); ?></th>
					<th class="bblm_tbl_stat bblm_tbl_collapse"><?php echo __( 'MVP','bblm' ); ?></th>
					<th class="bblm_tbl_stat"><?php echo __( 'SPP','bblm' ); ?></th>
				</tr>
			</thead>
			<tbody>

<?php
				foreach ( $stats as $s ) {
					if ( $zebracount % 2 ) {
						echo '<tr class="bblm_tbl_alt">';
					}
					else {
						echo '<tr>';
					}
					echo '<td>' . bblm_get_team_link( $s->TWPID ) . '</td>';
					echo '<td>' . $s->GAMES . '</td>';
					echo '<td>' . $s->TD . '</td>';
					echo '<td>' . $s->CAS . '</td>';
					echo '<td class="bblm_tbl_collapse">' . $s->COMP . '</td>';
					echo '<td class="bblm_tbl_collapse">' . $s->MINT . '</td>';
					echo '<td class="bblm_tbl_collapse">' . $s->MVP . '</td>';
					echo '<td>' . $s->SPP . '</td>';
					echo '</tr>';
					$zebracount++;
				}
				echo '</tbody>';
				echo '</table>';
				echo '</div>';
			}

		} //display_player_team_history()

		/**
		 * returns a players skills
		 * Only works for 2020+ players
		 *
		 * @param wordpress $query
		 * @return string
		 */
		 public static function get_player_skills( $ID ) {
			 global $wpdb;

			 $increasesql = 'SELECT * FROM '.$wpdb->prefix.'player_increase P, '.$wpdb->prefix.'increase I, '.$wpdb->prefix.'skills S WHERE P.inc_id = I.inc_id AND P.skill_id = S.skill_id AND P.p_id = '. $ID . ' ORDER BY I.inc_id ASC';
			 if ( $increase = $wpdb->get_results( $increasesql ) ) {
				 $first = 1;
				 $inclist = "";
				 foreach ( $increase as $in ) {
					 if ( $first ) {
						 $inclist = '<span class="bblm_increase-' . $in->inc_tier . '">' . $in->skill_name . '</span>';
						 $first = 0;
					 }
					 else {
						 $inclist .= ', <span class="bblm_increase-' . $in->inc_tier . '">' . $in->skill_name . '</span>';
					 }
				 }
				 return $inclist;
			 }
			 else {
				 //This player currently has no increases
				 return '&nbsp;';
			 }

		 } //end of get_player_skills()

	 /**
	  * returns a players injuries
	 	* Only works for 2020+ players
	  *
	  * @param wordpress $query
	  * @return string
	  */
	  public static function get_player_injuries( $ID ) {
			global $wpdb;

			$injurysql = 'SELECT * FROM '.$wpdb->prefix.'player_increase P, '.$wpdb->prefix.'injury I WHERE P.inj_id = I.inj_id AND P.p_id = '. $ID . ' ORDER BY I.inj_id ASC';
			if ( $injury = $wpdb->get_results( $injurysql ) ) {
				$first = 1;
				$injlist = "";
				foreach ( $injury as $in ) {
					if ( $first ) {
						$first = 0;
					}
					else {
						$injlist .= ', ';
					}
					//Some Stats go up, others go down with injuries!
					if ( "NI" == $in->inj_stat ) {
						$injlist .= '';
					}
					else if ( "ag" == $in->inj_stat || "pa" == $in->inj_stat ) {
						$injlist .= '+';
					}
					else {
						$injlist .= '-';
					}
					$injlist .= $in->inj_stat;
				}
				return $injlist;
			}
			else {
				//This player currently has no increases
				return 'None';
			}

		} //end of get_player_injuries()

	 /**
	 	* returns a players cost of skills
		* works for both legacy and 2020+ players
	 	*
	 	* @param wordpress $query
	 	* @return int
	 	*/
	 	public static function get_player_skills_cost( $ID ) {
			global $wpdb;

			$increasesql = 'SELECT SUM(S.inc_cost) AS ICOST FROM '.$wpdb->prefix.'player_increase I, '.$wpdb->prefix.'increase S WHERE S.inc_id = I.inc_id AND I.pi_type = 1 AND I.p_id = '. $ID;
			$inccost = $wpdb->get_var( $increasesql );

			if ( 0 < $inccost ) {
				return (int) $inccost;
			}
			else {
				return '0';
			}

	 	 } //end of get_player_skills_cost()

	 /**
	  * Outputs the list of matches the player has participated in
	  * includes all relevent validation
		* works for both legacy and 2020+ players
	  *
	  * @param wordpress $query
		* @param int a number to append to the form if this is called more than once on a screen
		* @param int 1 for increases, 2 for injuries
	  * @return html
	  */
	  public static function display_player_match_history_select( $ID, $count=1, $type=1 ) {
			global $wpdb;

			//Optional Param to add a number to the fields, in the event more then one is displayed
			//such as on the record player actions page
			$count = (int) $count;
			$fieldname = "";
			if ( 1== (int) $type ) {
				$fieldname = "bblm_mselect_s".$count;
			}
			else if ( 2== (int) $type ) {
				$fieldname = "bblm_mselect_i".$count;
			}
			else {
				$fieldname = "bblm_mselect_s".$count;
			}
?>
			<label for="<?php echo $fieldname; ?>"><?php echo __( 'Match Recieved', 'bblm' ); ?>:</label>
			<select name="<?php echo $fieldname; ?>" id="<?php echo $fieldname; ?>">
<?php
			$playermatchselectsql = 'SELECT M.m_id as MWPID FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'match_player M WHERE M.p_id = P.p_id AND P.WPID = ' . $ID . ' ORDER BY M.m_id DESC';
			if ( $playermatchselect = $wpdb->get_results( $playermatchselectsql ) ) {
				foreach ( $playermatchselect as $m ) {
					echo '<option value="' . $m->MWPID . '">' . bblm_get_match_name_score( $m->MWPID ) . '</option>';
				}
			}
			else {
				echo '<option value="X">' . __( 'No matches played','bblm' ) . '</option>';
			}
?>
			</select>
<?php

		} //end of display_player_match_history_select

		/**
	  	* returns the number of increases a player has recieved (skill or injury)
	  	* Defaults to skills
			* Only works for 2020+ players
	  	*
	  	* @param wordpress $query
			* @param string skill or injury
	  	* @return string
	  	*/
			public static function get_player_increase_count( $ID, $inctype = "skill" ) {
				global $wpdb;

				$type = '';

				switch ( $inctype ) {
					case ( 'skill' == $inctype ):
						$type = '1';
						break;
					case ( 'injury' == $inctype ):
						$type = '2';
						break;
					default :
					$type = '1';
						break;
				}

				$playernumincsql = 'SELECT COUNT(*) AS ICOUNT FROM '.$wpdb->prefix.'player_increase P WHERE P.pi_type = ' . $type . ' AND P.p_id = '.$ID;
				if ( $playernuminc = $wpdb->get_var( $playernumincsql ) ) {
					return $playernuminc;
				}
				else {
					return '0';
				}

			} //end of get_player_increase_countt()

} //end of class

new BBLM_CPT_Player();
