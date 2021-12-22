<?php
/**
 * BBowlLeagueMan Player testing page
 *
 * Testing new functionlity for the 1.15 Release - Player Skills
 *
 * @author 		Blacksnotling
 * @category 	Core
 * @package 	BBowlLeagueMan/Pages
 */

//Check the file is not being accessed directly
if (!function_exists('add_action')) die('You cannot run this file directly. Naughty Person');
?>
<div class="wrap">
	<h2><?php echo __( 'Player Skill Testing', 'bblm' ); ?></h2>
<?php
	if ( ( isset( $_POST[ 'bblm_player_test_addincrease' ] ) ) && ( wp_verify_nonce( $_POST[ 'bblm_playertest_submit' ], basename(__FILE__) ) ) ) {
		//Increases have been submitted
		$increase = false;
		$injury = false;
		$incdetails = array();
		$injdetails = array();

		//Check that something has been submitted
		if ( "X" != $_POST[ 'bblm_sselect_s1' ] ) {
			$increase = true;
			$incdetails = array(
				'player' => (int) $_POST[ 'bblm_test_player_id' ],
				'match' => (int) $_POST[ 'bblm_mselect_s1' ],
				'skill' => (int) $_POST[ 'bblm_sselect_s1' ],
				'incdetails' => (int) $_POST[ 'bblm_tselect_s1' ]
			);
		}
		if ( "X" != $_POST[ 'bblm_sselect_i1' ] ) {
			$injury = true;
			$injdetails = array(
				'player' => (int) $_POST[ 'bblm_test_player_id' ],
				'match' => (int) $_POST[ 'bblm_mselect_i1' ],
				'injury' => (int) $_POST[ 'bblm_sselect_i1' ],
			);
		}

		//Generate SQL
		//Injuries are done first to make sure current TV is captured correctly
		if ( $injury ) {

			if ( BBLM_Admin_CPT_Player::player_add_injury( $injdetails['player'], $injdetails ) ) {
				echo '<div id="updated" class="notice notice-success inline">';
				echo '<p>' . __( 'Changes have been captured.','bblm' ) . '</p>';
				echo '</div>';
			}
			else {
				echo '<div id="updated" class="notice notice-error inline">';
				echo '<p>' . __( 'Something went wrong! Please try again.','bblm' ) . '</p>';
				echo '</div>';
			}
		} //end of injury
		if ( $increase ) {
			if ( BBLM_Admin_CPT_Player::player_add_skill( $incdetails['player'], $incdetails) ) {
				echo '<div id="updated" class="notice notice-success inline">';
				echo '<p>' . __( 'Changes have been captured.','bblm' ) . '</p>';
				echo '</div>';
			}
			else {
				echo '<div id="updated" class="notice notice-error inline">';
				echo '<p>' . __( 'Something went wrong! Please try again.','bblm' ) . '</p>';
				echo '</div>';
			}
		}



	}

	if ( isset( $_POST['bblm_playertest_select'] ) ) {
		//A player has been selected, show stage 2

		//Sanitise varables
		$pid = (int) $_POST['bblm_pid'];

		//Show the details of the selected player
		display_player_characteristics( $pid );
?>
		<p><strong><?php echo __( 'Player Rank', 'bblm' ); ?>:</strong> <?php echo get_player_rank( $pid ); ?></p>
		<p><strong><?php echo __( 'Number of Skills', 'bblm' ); ?>:</strong> <?php echo BBLM_CPT_Player::get_player_increase_count( $pid, 'skill' ); ?></p>
		<p><strong><?php echo __( 'Number of Injuries', 'bblm' ); ?>:</strong> <?php echo BBLM_CPT_Player::get_player_increase_count( $pid, 'injury' ); ?></p>
		<p><strong><?php echo __( 'Team TV', 'bblm' ); ?>:</strong> X GP (for testing purposes)</p>


		<form method="post" id="player_test_add_skill" name="player_test_add_skill">
			<h3><?php echo __( 'Skills', 'bblm' ); ?></h3>
<?php
			BBLM_Admin_CPT_Player::display_skill_selection_form( $pid );
?>



<h3><?php echo __( 'Injuries', 'bblm' ); ?></h3>

<?php BBLM_Admin_CPT_Player::display_injury_selection_form( $pid ); ?>

<?php wp_nonce_field( basename( __FILE__ ), 'bblm_playertest_submit' ); ?>

<input type="hidden" name="bblm_test_player_id" id="bblm_test_player_id" value="<?php echo $pid; ?>">
<p class="submit">
	<input type="submit" name="bblm_player_test_addincrease" id="bblm_player_test_addincrease" value="Add Skils and Injuries" class="button button-primary" />
</p>
		</form>
<?php

	}
	else {
		//No player has been selected, show staqge 1
		player_test_select_player();
	}


	/**
	*
	* All test functions go Here
	*
	*/

//Temp functions only used for this page

	function player_test_select_player() {
		global $wpdb;
?>
	<h2><?php echo __( 'Stage 1: Select a Player', 'bblm' ); ?></h2>
	<p><?php echo __( 'only one team is selected for testing purposes. In the end it will show all teams with non-legacy players, who have at least one skill or injury recorded.', 'bblm' ); ?></p>
	<form name="bblm_playertestselect" method="post" id="post">
		<label for="bblm_rid"><?php echo __( 'Player', 'bblm' ); ?></label>
		<select name="bblm_pid" id="bblm_pid">
			<?php
					$playerselectsql = 'SELECT WPID as PWPID FROM '.$wpdb->prefix.'player WHERE t_id = "142"';
					if ( $playerselect = $wpdb->get_results( $playerselectsql ) ) {
						foreach ( $playerselect as $s ) {
							echo '<option value="' . $s->PWPID . '">' . bblm_get_player_name( $s->PWPID ) . '</option>';
						}
					}
					else {
						echo 'error';
					}

			?></select>

	<p class="submit"><input type="submit" name="bblm_playertest_select" value="Select above Player" title="Select the above Player" class="button-primary"/></p>
	<?php wp_nonce_field( basename( __FILE__ ), 'bblm_player_test_select' ); ?>
	</form>
<?php
}//end of function

//end of temp Functions
//below are functions to be incorporated into other pages when the time comes

/**
 * Displays a players full charcteristics
 * Intended to be used on the view player template
 * works for both legacy and 2020+ players
 *
 * @param wordpress $query
 * @return html
 */
 function display_player_characteristics( $ID ) {
	 global $wpdb;

	 $playersql = 'SELECT P.p_id, P.t_id, P.p_ma, P.p_st, P.p_ag, P.p_av, P.p_pa, P.p_injuries, O.pos_skills, P.p_cost, P.p_spp, P.p_cspp, P.p_legacy, O.pos_name, O.pos_cost FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'position O WHERE O.pos_id = P.pos_id AND P.WPID = '.$ID;
	 $pd = $wpdb->get_row( $playersql );
?>
	 <div role="region" aria-labelledby="Caption01" tabindex="0">
	 <table class="bblm_table bblm_table_collapsable">
		 <thead>
			 <tr>
				 <th class="bblm_tbl_name"><?php echo __( 'Position','bblm' ); ?></th>
				 <th class="bblm_tbl_stat"><?php echo __( 'MA','bblm' ); ?></th>
				 <th class="bblm_tbl_stat"><?php echo __( 'ST','bblm' ); ?></th>
				 <th class="bblm_tbl_stat"><?php echo __( 'AG','bblm' ); ?></th>
<?php
	if ( ! BBLM_CPT_Player::is_player_legacy( $ID ) ) {
?>
					<th class="bblm_tbl_stat"><?php echo __( 'PA', 'bblm' ); ?></th>
<?php
	}
?>
					<th class="bblm_tbl_stat"><?php echo __( 'AV','bblm' ); ?></th>
					<th class="bblm_tbl_collapse"><?php echo __( 'Skills','bblm' ); ?></th>
					<th class="bblm_tbl_collapse"><?php echo __( 'Injuries','bblm' ); ?></th>
					<th><?php echo __( 'SPP','bblm' ); ?></th>
					<th><?php echo __( 'Cost','bblm' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr class="bblm_tbl_alt">
					<td><?php echo $pd->pos_name; ?></td>
					<td><?php echo $pd->p_ma; ?></td>
					<td><?php echo $pd->p_st; ?></td>
<?php
		if ( BBLM_CPT_Player::is_player_legacy( $ID ) ) {
?>
					<td><?php echo $pd->p_ag; ?></td>
					<td><?php echo $pd->p_av; ?></td>
					<td class="bblm_tbl_skills bblm_tbl_collapse"><?php echo $pd->p_skills; ?></td>
					<td class="bblm_tbl_skills bblm_tbl_collapse"><?php echo $pd->p_injuries; ?></td>
					<td><?php echo $pd->p_spp; ?></td>
					<td><?php echo number_format( $pd->p_cost ); ?> gp</td>
<?php
	}
	else {
?>
					<td><?php echo $pd->p_ag; ?>+</td>
					<td><?php echo $pd->p_pa; ?>+</td>
					<td><?php echo $pd->p_av; ?>+</td>
					<td class="bblm_tbl_skills bblm_tbl_collapse"><span class="bblm_pos_skill"><?php echo $pd->pos_skills; ?></span>
						<br /><strong><?php echo BBLM_CPT_Player::get_player_skills( $ID ) ?></strong></td>
					<td class="bblm_tbl_skills bblm_tbl_collapse"><?php echo BBLM_CPT_Player::get_player_injuries( $ID ) ?></td>
					<td><?php echo $pd->p_cspp . '/' . $pd->p_spp; ?></td>
					<td><?php echo number_format( $pd->p_cost ); ?> gp
						<br />(<span class="bblm_pos_skill"><?php echo number_format( $pd->pos_cost ) . ' + ' . number_format( BBLM_CPT_Player::get_player_skills_cost( $ID ) ); ?></span>)</td>
<?php
	}
?>
				</tr>
			</tbody>
		</table>
	</div>
<?php

	} //end of display_player_characteristics()




	/**
 	 * returns a players rank
	 * works for both legacy and 2020+ players
 	 *
 	 * @param wordpress $query
 	 * @return string
 	 */
 	 function get_player_rank( $ID ) {
		 global $wpdb;

		 $plevel = "";

		 if ( BBLM_CPT_Player::is_player_legacy( $ID ) ) {

			 $playercsppsql = 'SELECT P.p_cspp FROM '.$wpdb->prefix.'player P WHERE P.WPID = '.$ID;
			 $pspp = $wpdb->get_var( $playercsppsql );

			 switch ( $pspp ) {
				 case 0:
						 $plevel = "Rookie";
						 break;
				 case ( $pspp < 6 ):
						 $plevel = "Rookie";
						 break;
				 case ( $pspp < 16 ):
						 $plevel = "Experienced";
						 break;
				 case ( $pspp < 31 ):
						 $plevel = "Veteran";
						 break;
				 case ( $pspp < 51 ):
						 $plevel = "Emerging Star";
						 break;
				 case ( $pspp < 76 ):
						 $plevel = "Star";
						 break;
				 case ($pspp < 176):
						 $plevel = "Super Star";
						 break;
				 case ( $pspp > 175 ):
						 $plevel = "Legend";
						 break;
				 default:
						 $plevel = "Rookie";
						 break;
			 }
		 } //end of if legacy
		 else {

			 //Using the 2020 ruleset definitions
			 $pspp = (int) BBLM_CPT_Player::get_player_increase_count( $ID, "skill" );

			 switch ( $pspp ) {
				 case 0:
						 $plevel = "Rookie";
						 break;
				 case ( 1 == $pspp ):
						 $plevel = "Experienced";
						 break;
				 case ( 2 == $pspp ):
						 $plevel = "Veteran";
						 break;
				 case ( 3 == $pspp ):
						 $plevel = "Emerging Star";
						 break;
				 case ( 4 == $pspp ):
						 $plevel = "Star";
						 break;
				 case ( 5 == $pspp ):
						 $plevel = "Super Star";
						 break;
				 case ( 6 == $pspp ):
						 $plevel = "Legend";
						 break;
				 default:
						 $plevel = "Rookie";
						 break;
			 }

		 }

		 return $plevel;
 	 } //end of get_player_rank()



	//Notes for adding to classes later on
	// - replace calls to the same class with self::
	// - restore the 'public static' before the function names


?>

</div>
