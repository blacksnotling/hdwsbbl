<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Player CPT admin functions
 *
 * Defines the admin functions related to the Player CPT (edit screens, custom messages, post saving etc)
 * For the Meta-Boxes, see the meta-boxes directory
 * For front end functions reloated to the CPT see the includes/post-types directory
 *
 * @class 		BBLM_Admin_CPT_Player
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/Admin/CPT
 * @version   1.1
 */

class BBLM_Admin_CPT_Player {

 /**
  * Constructor
  */
  public function __construct() {

		add_filter( 'manage_edit-bblm_player_columns', array( $this, 'my_edit_columns' ) );
		add_action( 'manage_bblm_player_posts_custom_column', array( $this, 'my_manage_columns' ), 10, 2 );
		add_filter( 'manage_edit-bblm_player_sortable_columns', array( $this, 'my_manage_sortable_columns' ) );
		add_action( 'pre_get_posts', array( $this, 'manage_archives' ) );
		add_filter( 'admin_url', array( $this, 'redirect_add_player_link' ), 10, 2 );
		add_action( 'restrict_manage_posts', array( $this, 'player_filter_team' ) );
		add_filter( 'parse_query', array( $this, 'comp_filter_player_by_team' ) );

    return TRUE;

  }

	/**
   * Redirects the "Add New Players" link to the custom add players admin page
   *
   * @param string $url the url I wish to send them to
	 * @param string $path where I am sending them
   * @return string url the url of the custom admin page I wish ro redirect the user to
   */
   public function redirect_add_player_link( $url, $path ) {

		 if( $path === 'post-new.php?post_type=bblm_player' ) {
			 $url = get_bloginfo( 'url' ) . '/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.add.player.php';
		 }
		 return $url;

   }//end of redirect_add_player_link()

	 /**
		* Sets the Column headers for the CPT edit list screen
		*/
	 function my_edit_columns( $columns ) {

		$columns = array(
			'cb' => '<input type="checkbox" />',
			'title' => __( 'Player Name', 'bblm' ),
			'team' => __( 'Team', 'bblm' ),
			'status' => __( 'Status', 'bblm' ),
		);

		return $columns;

	 }

	 /**
		* Sets the Column content for the CPT edit list screen
		*/
	 function my_manage_columns( $column, $post_id ) {
		global $post;

		switch( $column ) {

			// If displaying the 'status' column.
			case 'status' :

				$pstatus = get_post_meta( $post_id, 'player_status', true );
				if ( (int) $pstatus ) {

					echo __( 'Active', 'bblm' );
				}

				else {

					echo __( 'Inactive', 'bblm' );

				}

			break;

			// if displaying the team column
			case 'team' :

				echo bblm_get_team_name( get_post_meta( $post_id, 'player_team', true ) );

			break;

			 // Break out of the switch statement for anything else.
			 default :
			 break;

		 }

	 }

	 /**
	 * stops the CPT archive pages pagenating on the admin side and changes the display order
	 *
	 * @param wordpress $query
	 * @return none
	 */
	 public function manage_archives( $query ) {

			if( is_post_type_archive( 'bblm_player' ) && is_admin() && $query->is_main_query() ) {
					$query->set( 'posts_per_page', 50 );
					$query->set( 'orderby', 'title' );
					$query->set( 'order', 'asc' );
			}

		}

		/*
 		* Sets the columns that are filterable
 		*/
 		function my_manage_sortable_columns( $columns ) {
 			$columns['team'] = 'player_team_filter';
 			return $columns;
 		}

 	 /**
 		* Allow the page to be filtered on meta_values
  		*/
 		function player_filter_team() {
 			global $typenow;
 			global $wpdb;

 			if ( $typenow == 'bblm_player' ) {

 				$query = $wpdb->prepare('
 				SELECT DISTINCT pm.meta_value FROM %1$s pm
 				LEFT JOIN %2$s p ON p.ID = pm.post_id
 				WHERE pm.meta_key = "%3$s"
 				AND p.post_status = "%4$s"
 				AND p.post_type = "%5$s"
 				ORDER BY "%3$s"',
 				$wpdb->postmeta,
 				$wpdb->posts,
 				'player_team',
 				'publish',
 				'bblm_player',
 				'player_team'
 			);
 			$results = $wpdb->get_col($query);
 			$current_team = '';
 			if( isset( $_GET['bblm_player-filter-team'] ) ) {
 				$current_team = $_GET['bblm_player-filter-team']; // Check if option has been selected
 			}
 	?>
 	 <select name="bblm_player-filter-team" id="bblm_player-filter-team">
 			<option value="all" <?php selected( 'all', $current_team ); ?>><?php _e( 'All Teams', 'bblm' ); ?></option>
 			<?php foreach( $results as $key ) { ?>
 				<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $current_team ); ?>><?php echo bblm_get_season_name( $key ); ?></option>
 			<?php } ?>
 		</select>
 	<?php }
 		} // end of comp_filter_season()

		/*
 		* *Modifies thre WP_Query to account for any selected filter
 		*/
 		function comp_filter_player_by_team( $query ) {
 			global $pagenow;
 			// Get the post type
 			$post_type = isset( $_GET['post_type'] ) ? $_GET['post_type'] : '';
 			if ( is_admin() && $pagenow=='edit.php' && $post_type == 'bblm_player' && isset( $_GET['bblm_player-filter-team'] ) && $_GET['bblm_player-filter-team'] !='all' ) {
 				$query->query_vars['meta_key'] = 'player_team';
 		    $query->query_vars['meta_value'] = $_GET['bblm_player-filter-team'];
 		    $query->query_vars['meta_compare'] = '=';
 		  }
 		} //end of comp_filter_comp_by_season()

  /**
   * Resets the injured status of a player
   *
   * @param int $ID the ID of the Player
   * @return bool true (successfull), or False (Failure)
   */
   public static function reset_player_mng( $ID ) {
     global $wpdb;

     $reactivatesql = 'UPDATE `'.$wpdb->prefix.'player` SET `p_mng` = \'0\', `p_cost_ng` = p_cost  WHERE `WPID` = '.$ID.' LIMIT 1';
     if ( FALSE !== $wpdb->query( $reactivatesql ) ) {
       return TRUE;
     }
     else {
       return FALSE;
     }

   }//end of reset_player_mng()

	 	 /**
			* Outputs the form to allow a skill to be added to a player
			* includes all relevent validation
			* This is inteded to only be used with 2020 players
			*
			* @param wordpress $query
			* @param int a number to append to the form if this is called more than once on a screen
			* @return html
			*/
			public static function display_skill_selection_form( $ID, $count=1, $mselect=1 ) {
				global $wpdb;

				//Optional Param to add a number to the fields, in the event more then one is displayed
				//such as on the record player actions page
				$count = (int) $count;

				//Optional Param to toggle the match selection option on or not
				$mselect = (int) $mselect;

				//Chech the Player has actually played in a match to earn SPP
				if ( BBLM_CPT_Player::has_player_played( $ID ) ) {

					//Capture how many Skills they already have - to display the right increases
					$skillnum = (int) BBLM_CPT_Player::get_player_increase_count( $ID, 'skill' );

					//If it is 6 then immeditally abort as they cannot earn any more skills
					if ( 5 > $skillnum ) {

						//Then we see if they have enough SPP to level up
						$increasesppsql = 'SELECT inc_spp FROM '.$wpdb->prefix.'increase WHERE inc_tier > ' . $skillnum . ' ORDER BY inc_spp ASC';
						$increasespp = $wpdb->get_var( $increasesppsql );

						//and the players availble SPP
						$playersppsql = 'SELECT p_cspp FROM '.$wpdb->prefix.'player WHERE WPID = ' . $ID;
						$playerspp = $wpdb->get_var( $playersppsql );
						//Determine the least SPP required for the level they are on

						//If they have enough then continue
						if ( $playerspp >= $increasespp ) {

							if ( $mselect ) {
								BBLM_CPT_Player::display_player_match_history_select( $ID, $count, 1 );
							}
?>
							<label for="bblm_sselect_s<?php echo $count; ?>"><?php echo __( 'Selected Skill', 'bblm' ); ?>:</label>
							<select name="bblm_sselect_s<?php echo $count; ?>" id="bblm_sselect_s<?php echo $count; ?>">
								<?php
										$skillselectsql = 'SELECT * FROM '.$wpdb->prefix.'skills S, '.$wpdb->prefix.'skill_cat C WHERE S.sc_id = C.sc_id ORDER BY S.sc_id ASC';
										if ( $skillselect = $wpdb->get_results( $skillselectsql ) ) {
											echo '<option value="X">' . __( 'Not Applicable - No increase chosen','bblm' ) . ' </option>';
											foreach ( $skillselect as $s ) {
												echo '<option value="' . $s->skill_id . '">' . esc_textarea( $s->skill_name ) . ' (' . esc_textarea( $s->sc_name ) . ')</option>';
											}
										}
										else {
											echo 'error';
										}
?>
							</select>


							<label for="bblm_tselect_s<?php echo $count; ?>"><?php echo __( 'Increase Type', 'bblm' ); ?>:</label>
							<select name="bblm_tselect_s<?php echo $count; ?>" id="bblm_tselect_s<?php echo $count; ?>">
<?php
										//Determine the next tier of skill the plahyer can take
										$skillpossible = $skillnum+1;
										$incselectsql = 'SELECT * FROM '.$wpdb->prefix.'increase I WHERE I.inc_tier = ' . $skillpossible . ' ORDER BY I.inc_id ASC';
										if ( $incselectsql = $wpdb->get_results( $incselectsql ) ) {
											foreach ( $incselectsql as $i ) {
												echo '<option value="' . $i->inc_id . '">' . esc_textarea( $i->inc_name ) . ' (' . bblm_ordinal( (int) $i->inc_tier ) . ', ' . esc_textarea( number_format( $i->inc_cost ) ) . 'gp, -' . (int) $i->inc_spp . ' SPP)</option>';
											}
										}
										else {
											echo 'error';
										}
?>
							</select>
<?php
		} //end of if they have enough SPP
						else {
							//The player does not have enough SPP to skill up
							echo '<p>' . __( 'The Player does not have enough SPP to increase', 'bblm' ) . '</p>';
						}
					} //end of if they have 6 skills
					else {
						//The player does not have enough SPP to skill up
						echo '<p>' . __( 'The Player has already recieved the maximum number of increases', 'bblm' ) . '</p>';
					}
				}//end of if a player has played a game
				else {
					echo '<p>' . __( 'The Player has not yet made their debut', 'bblm' ) . '</p>';
				}

			} //end of display_skill_selection_form()


		 /**
			* Outputs the form to allow an injury to be added to a player
			* includes all relevent validation
			* This is inteded to only be used with 2020 players
			*
			* @param int a number to append to the form if this is called more than once on a screen
			* @return html
			*/
			public static function display_injury_selection_form( $ID, $count=1, $mselect=1 ) {
				global $wpdb;

				//Optional Param to add a number to the fields, in the event more then one is displayed
				//such as on the record player actions page
				$count = (int) $count;

				//Optional Param to toggle the match selection option on or not
				$mselect = (int) $mselect;

				if ( $mselect ) {
					BBLM_CPT_Player::display_player_match_history_select( $ID, $count, 2 );
				}
?>
				<label for="bblm_sselect_i<?php echo $count; ?>"><?php echo __( 'Selected Injury', 'bblm' ); ?>:</label>
				<select name="bblm_sselect_i<?php echo $count; ?>" id="bblm_sselect_i<?php echo $count; ?>">
<?php
				$injuryselectsql = 'SELECT * FROM '.$wpdb->prefix.'injury I ORDER BY I.inj_id ASC';
				if ( $injuryselect = $wpdb->get_results( $injuryselectsql ) ) {
					echo '<option value="X">' . __( 'Not Applicable - No injury recieved','bblm' ) . ' </option>';
					foreach ( $injuryselect as $i ) {
						echo '<option value="' . $i->inj_id . '">' . esc_textarea( $i->inj_name ) . ' (';

						if (! is_null( $i->inj_stat ) ) {
							esc_textarea( $i->inj_stat );
						}
						if ( $i->inj_mng ) {
							echo 'MNG';
						}
						else if ( "Dead" == $i->inj_name ) {
							echo __( 'the Finger of Death Strikes!','bblm' );
						}
						else {
							echo __( 'No long term effect','bblm' );
						}
						echo ')</option>';
					}
				}
				else {
					echo 'error';
				}
?>
				</select>
<?php

			}// end of display_injury_selection_form()

		/**
		 * Returns the cost of a player, taking into account any increases but ignoreing TR and MNG
		 * Calculates it based on skills, rather then pulling it from the DB
		 * Intended to be used by update functions to capture the correct cost
		 * rather than just display it on the frint end
		 * works for both legacy and 2020+ players
		 *
		 * @param wordpress $query
		 * @return html
		 */
		 public static function generate_player_cost( $ID ) {
			 global $wpdb;

			 $cost = 0;

			 //Grab cost if players position
			 $poscostsql = 'SELECT P.pos_cost, P.r_id FROM '.$wpdb->prefix.'position P, '.$wpdb->prefix.'player T WHERE P.pos_id = T.pos_id AND T.WPID = ' . (int) $ID;
			 $poscost = $wpdb->get_row( $poscostsql );
			 if ( BBLM_CPT_Race::is_race_cheap_linos( $poscost->r_id ) ) {
				 $pcost = 0;
			 }
			 else {
				 $pcost = $poscost->pos_cost;
			 }

			 //Grab cost of skills
			 $inccost = BBLM_CPT_Player::get_player_skills_cost( $ID );

			 $cost = $pcost + $inccost;

			 return $cost;

		 } //end of generate_player_cost

		 /**
			* Adds a Skill to a Player record
			* This is inteded to only be used with 2020 players
			*
			* @param int $ID WPID of the Player
			* @param array $args The details of the skill, cost, etc
			* @return bool true (successfull), or False (Failure)
			*/
			public static function player_add_skill( $ID, $incdetails ) {
				global $wpdb;

				//Validate $args - it should contain a Match, Skill, Cost, and SPP reduction
				if ( array_key_exists( 'player', $incdetails ) &&
						 array_key_exists( 'match', $incdetails ) &&
						 array_key_exists( 'skill', $incdetails ) &&
						 array_key_exists( 'incdetails', $incdetails ) ) {

					$success = FALSE;
					//Prepare the Increase Record
					$incsql = 'INSERT INTO '.$wpdb->prefix.'player_increase (`p_id`, `m_id`, `pi_type`, `inj_id`, `skill_id`, `inc_id`) VALUES ("' . $incdetails['player'] . '", "' . $incdetails['match'] . '", "1", "0", "' . $incdetails['skill'] . '", "' . $incdetails['incdetails'] . '")';


					//We grab the increase details from the increase table
					$increasededetailssql = 'SELECT I.inc_spp, S.skill_stat FROM '.$wpdb->prefix.'increase I, '.$wpdb->prefix.'skills S WHERE S.skill_id = ' . $incdetails['skill'] . ' AND inc_id = ' . $incdetails['incdetails'];
					//Validate what was provided is valid
					if ($increasededetails = $wpdb->get_row( $increasededetailssql )) {
						//Insert the increase record now we have validated the input
						$wpdb->query( $incsql );

						$increasespp = (int) $increasededetails->inc_spp;
						$increasestat = $increasededetails->skill_stat;
						$playercost = self::generate_player_cost( $incdetails['player'] );

						//Calculate Cost Next game - see if they are currently injured
						//Grab the team ID at the same time
						$playerinjuredsql = 'SELECT P.p_mng, P.t_id FROM '.$wpdb->prefix.'player P WHERE P.WPID = ' . $incdetails['player'];
						$playerinjured = $wpdb->get_row( $playerinjuredsql );
						$playerinj = (int) $playerinjured->p_mng;
						$playerteam = (int) $playerinjured->t_id;

						//See if the cost next game is zero or the same
						if ( $playerinj ) {
							$playercostng = 0;
						}
						else {
							$playercostng = $playercost;
						}

						//Update the Player Record to reduce SPP and increase Cost, and record the new cost difference
						$playerupdatesql = 'UPDATE `'.$wpdb->prefix.'player` SET `p_cost` = \''.$playercost.'\', `p_cost_ng` = \''.$playercostng.'\', `p_cspp` = `p_cspp`-\''.$increasespp.'\'';

						//Update the player characteristics if a stat increase has been captured
						if (! is_null( $increasestat ) ) {
							//convert to lower case to match database table
							$increasestat = strtolower( $increasestat );

							//Some Stats go up, others go down with increases!
							if ( "ag" == $increasestat || "pa" == $increasestat ) {
								$playerupdatesql .= ', `p_'. $increasestat .'` = `p_'. $increasestat .'`-\'1\'';
							}
							else {
								$playerupdatesql .= ', `p_'. $increasestat .'` = `p_'. $increasestat .'`+\'1\'';
							}
						}
						$playerupdatesql .= ' WHERE `WPID` = \''. $incdetails['player'] .'\' LIMIT 1';

						if ( FALSE !== $wpdb->query( $playerupdatesql ) ) {

							//Update the team TV
							bblm_update_tv( $playerteam );
							$success = TRUE;
						}

					} //end of if skill and increase is valid

					return $success;

				}
				else {
					//The correct arguments were not supplied to let us add a skill
					return FALSE;
				}

			} //end of player_add_skill()

		 /**
			* Adds an Injury to a Player record
			* This is inteded to only be used with 2020 players
			*
			* @param int $ID WPID of the Player
			* @param array $args The details of the injury, change, etc
			* @return bool true (successfull), or False (Failure)
			*/
			public static function player_add_injury( $ID, $injdetails ) {
				global $wpdb;

				if ( array_key_exists( 'player', $injdetails ) &&
						 array_key_exists( 'match', $injdetails ) &&
						 array_key_exists( 'injury', $injdetails ) ) {

					$success = FALSE;
					//Prepare the Injury Record
					$injsql = 'INSERT INTO '.$wpdb->prefix.'player_increase (`p_id`, `m_id`, `pi_type`, `inj_id`, `skill_id`, `inc_id`) VALUES ("' . $injdetails['player'] . '", "' . $injdetails['match'] . '", "2", "' . $injdetails['injury'] . '", "0", "0")';

					//Check the injury is valid
					$injdetailssql = 'SELECT * FROM '.$wpdb->prefix.'injury WHERE inj_id = ' . $injdetails['injury'];
					if ( $inj = $wpdb->get_row( $injdetailssql ) ) {
						//submit the injury to the database
						$wpdb->query( $injsql );
						$success = TRUE;

						//If the result is MNG or a stat decrease then the MNG flag is set, and the cost next game is 0
						if ( $inj->inj_mng ) {
							$playerupdatesql = 'UPDATE '.$wpdb->prefix.'player SET `p_cost_ng` = \'0\', `p_mng` = \'1\' WHERE `WPID` = \'' . $injdetails['player'] . '\' LIMIT 1';
							if ( FALSE !== $wpdb->query( $playerupdatesql ) ) {
								$success = TRUE;
								//Grab the players team to update the TV
								$playerteamsql = 'SELECT t_id FROM '.$wpdb->prefix.'player WHERE WPID = ' . $injdetails['player'];
								$playerteam = $wpdb->get_var( $playerteamsql );
								//Team TV only gets updated when a MNG is applied
								bblm_update_tv( $playerteam );
							}
							else {
								$success = FALSE;
							}
						}
						//If the result changes a stat then update the player record
						if (! is_null( $inj->inj_stat ) ) {

							//convert to lower case to match database table
							$decreasestat = strtolower( $inj->inj_stat );
							//Ignore if the stat changed is 'NI' to avoid errors
							if ( "ni" != $decreasestat ) {
								$playerupdatesql = 'UPDATE `'.$wpdb->prefix.'player` SET ';

								//Some Stats go up, others go down with injuries!
								if ( "ag" == $decreasestat || "pa" == $decreasestat ) {
									$playerupdatesql .= '`p_'. $decreasestat .'` = `p_'. $decreasestat .'`+\'1\'';
								}
								else if ( "av" == $decreasestat || "ma" == $decreasestat || "st" == $decreasestat ) {
									$playerupdatesql .= '`p_'. $decreasestat .'` = `p_'. $decreasestat .'`-\'1\'';
								}

								$playerupdatesql .= ' WHERE `WPID` = \''. $injdetails['player'] .'\' LIMIT 1';
							}
							if ( FALSE !== $wpdb->query( $playerupdatesql ) ) {
								$success = TRUE;
							}
						} //end of if a stat decrease is recieved

						return $success;

					}//end of if injury is valid

				}
				else {
					//The correct arguments were not supplied to let us add a skill
					return FALSE;
				}

			} //end of player_add_injury()


}//end of class

new BBLM_Admin_CPT_Player();
