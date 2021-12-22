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

		return true;

    //return TRUE;

  }

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

   }//end of reset_team_injuries()

	 	 /**
			* Outputs the form to allow a skill to be added to a player
			* includes all relevent validation
			* This is inteded to only be used with 2020 players
			*
			* @param wordpress $query
			* @param int a number to append to the form if this is called more than once on a screen
			* @return html
			*/
			public static function display_skill_selection_form( $ID, $count=1 ) {
				global $wpdb;

				//Optional Param to add a number to the fields, in the event more then one is displayed
				//such as on the record player actions page
				$count = (int) $count;

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

						BBLM_CPT_Player::display_player_match_history_select( $ID, $count, 1 );
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
			public static function display_injury_selection_form( $ID, $count=1 ) {
				global $wpdb;

				//Optional Param to add a number to the fields, in the event more then one is displayed
				//such as on the record player actions page
				$count = (int) $count;

				BBLM_CPT_Player::display_player_match_history_select( $ID, $count, 2 );
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
							if ( "NI" != $decreasestat ) {
								$playerupdatesql = 'UPDATE `'.$wpdb->prefix.'player` SET ';

								//Some Stats go up, others go down with injuries!
								if ( "ag" == $decreasestat || "pa" == $decreasestat ) {
									$playerupdatesql .= '`p_'. $decreasestat .'` = `p_'. $decreasestat .'`+\'1\'';
								}
								else {
									$playerupdatesql .= '`p_'. $decreasestat .'` = `p_'. $decreasestat .'`-\'1\'';
								}

							}
							$playerupdatesql .= ' WHERE `WPID` = \''. $injdetails['player'] .'\' LIMIT 1';
						}
						if ( FALSE !== $wpdb->query( $playerupdatesql ) ) {
							$success = TRUE;
						}

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
