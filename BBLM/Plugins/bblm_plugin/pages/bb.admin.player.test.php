<?php
/**
 * BBowlLeagueMan Player testing page
 *
 * Converting Player skills for the 1.15 release
 *
 * @author 		Blacksnotling
 * @category 	Core
 * @package 	BBowlLeagueMan/Pages
 */

//Check the file is not being accessed directly
if (!function_exists('add_action')) die('You cannot run this file directly. Naughty Person');
?>
<div class="wrap">
	<h2><?php echo __( 'Player Skill Conversion', 'bblm' ); ?></h2>
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
?>
<h3><?php echo __( 'New Format', 'bblm' ); ?></h3>
<?php
		//Show the details of the selected player
		BBLM_CPT_Player::display_player_characteristics( $pid );
?>
		<p><strong><?php echo __( 'Player Rank', 'bblm' ); ?>:</strong> <?php echo BBLM_CPT_Player::get_player_rank( $pid ); ?></p>
		<p><strong><?php echo __( 'Number of Skills', 'bblm' ); ?>:</strong> <?php echo BBLM_CPT_Player::get_player_increase_count( $pid, 'skill' ); ?></p>
		<p><strong><?php echo __( 'Number of Injuries', 'bblm' ); ?>:</strong> <?php echo BBLM_CPT_Player::get_player_increase_count( $pid, 'injury' ); ?></p>
		<p><strong><?php echo __( 'ID', 'bblm' ); ?>:</strong> <?php echo $pid; ?></p>

		<h3><?php echo __( 'Old Format', 'bblm' ); ?></h3>
<?php
	$playersql = 'SELECT P.p_id, P.t_id, P.p_ma, P.p_st, P.p_ag, P.p_av, P.p_pa, P.p_injuries, O.pos_skills, P.p_cost, P.p_spp, P.p_cspp, P.p_legacy, O.pos_name, O.pos_cost, P.p_skills, P.pos_id FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'position O WHERE O.pos_id = P.pos_id AND P.WPID = '.$pid;
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
					 <td><?php echo $pd->p_ag; ?></td>
					 <td><?php echo $pd->p_av; ?></td>
					 <td class="bblm_tbl_skills bblm_tbl_collapse"><?php echo $pd->p_skills; ?></td>
					 <td class="bblm_tbl_skills bblm_tbl_collapse"><?php echo $pd->p_injuries; ?></td>
					 <td class="bblm_tbl_collapse"><?php echo $pd->p_spp; ?></td>
					 <td><?php echo number_format( $pd->p_cost ); ?> gp</td>

				 </tr>
			 </tbody>
		 </table>
	 </div>

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


		<h3 class="bblm-table-caption"><?php echo __( 'Recent Matches', 'bblm' ); ?></h3>
		<div role="region" aria-labelledby="Caption01" tabindex="0">
			<table class="bblm_table bblm_sortable bblm_expandable">
				<thead>
					<tr>
						<th><?php echo __( 'Vs', 'bblm' ); ?></th>
						<th><?php echo __( 'TD', 'bblm' ); ?></th>
						<th><?php echo __( 'CAS', 'bblm' ); ?></th>
						<th><?php echo __( 'INT', 'bblm' ); ?></th>
						<th><?php echo __( 'COMP', 'bblm' ); ?></th>
						<th><?php echo __( 'MVP', 'bblm' ); ?></th>
						<th><?php echo __( 'SPP', 'bblm' ); ?></th>
						<th><?php echo __( 'MNG?', 'bblm' ); ?></th>
						<th><?php echo __( 'Increase', 'bblm' ); ?></th>
						<th><?php echo __( 'Injury', 'bblm' ); ?></th>
					</tr>
				</thead>
				<tbody>

<?php
//Assumes player has played in one match for this to display thus far
$playermatchsql = 'SELECT P.*, M.WPID AS MWPID, UNIX_TIMESTAMP(M.m_date) AS mdate, A.WPID as TA, A.t_id AS TAid, B.WPID AS TB, B.t_id AS TBid FROM '.$wpdb->prefix.'match_player P, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'team A, '.$wpdb->prefix.'team B WHERE M.m_teamA = A.t_id AND M.m_teamB = B.t_id AND M.WPID = P.m_id AND P.p_id = ' . $pd->p_id . ' ORDER BY M.m_date DESC';
if ( $playermatch = $wpdb->get_results( $playermatchsql ) ) {
$zebracount = 1;
	foreach ( $playermatch as $pm ) {
		if ( ( $zebracount % 2 ) && ( 10 < $zebracount ) ) {
			echo '<tr class="bblm_tbl_alt bblm_tbl_hide">';
		}
		else if ( ( $zebracount % 2 ) && ( 10 >= $zebracount ) ) {
			echo '<tr class="bblm_tbl_alt">';
		}
		else if ( 10 < $zebracount ) {
			echo '<tr class="bblm_tbl_hide">';
		}
		else {
			echo '<tr>';
		}
		if ( $pm->TAid == $pd->t_id ) {
			echo '<td>' . bblm_get_team_link( $pm->TB ) . '<br />' . bblm_get_match_link_date( $pm->MWPID ) . '</td>';
		}
		else {
			echo '<td>' . bblm_get_team_link( $pm->TA ) . '<br />' . bblm_get_match_link_date( $pm->MWPID ) . '</td>';
		}
		echo '<td>';
		if ( 0 == $pm->mp_td ) {
			echo '0';
		}
		else {
			echo '<strong>' . $pm->mp_td . '</strong>';
		}
		echo '</td>';
		echo '<td>';
		if ( 0 == $pm->mp_cas ) {
			echo '0';
		}
		else {
			echo '<strong>' . $pm->mp_cas . '</strong>';
		}
		echo '</td>';
		echo '<td>';
		if ( 0 == $pm->mp_int ) {
			echo '0';
		}
		else {
			echo '<strong>' . $pm->mp_int . '</strong>';
		}
		echo '</td>';
		echo '<td>';
		if ( 0 == $pm->mp_comp ) {
			echo '0';
		}
		else {
			echo '<strong>' . $pm->mp_comp. '</strong>';
		}
		echo '</td>';
		echo '<td>';
		if ( 0 == $pm->mp_mvp ) {
			echo '0';
		}
		else {
			echo '<strong>' . $pm->mp_mvp . '</strong>';
		}
		echo '</td>';
		echo '<td>';
		if ( 0 == $pm->mp_spp ) {
			echo '0';
		}
		else {
			echo '<strong>' . $pm->mp_spp . '</strong>';
		}
		echo '</td>';
		echo '<td>';
		if ( 0 == $pm->mp_mng ) {
			echo '0';
		}
		else {
			echo '<strong>Y</strong>';
		}
		echo '</td>';
			echo '<td>';
			if ( "none" == $pm->mp_inc ) {
				echo '-';
			}
			else {
				echo '<strong>' . $pm->mp_inc . '</strong>';
			}
			echo '</td>';
			echo '<td>';
			if ( "none" == $pm->mp_inj ) {
				echo '-';
			}
			else {
				echo '<strong>' . $pm->mp_inj . '</strong>';
			}
			echo '</td>';
		$zebracount++;
	}
}
?>
	</tbody>
</table>
</div>

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
	<form name="bblm_playertestselect" method="post" id="post">
		<label for="bblm_rid"><?php echo __( 'Player', 'bblm' ); ?></label>
		<select name="bblm_pid" id="bblm_pid">
			<?php
					$playerselectsql = 'SELECT P.WPID as PWPID, T.WPID as TWPID FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T WHERE T.t_id = P.t_id AND P.p_spp > 0 AND P.p_legacy = 0 and P.t_id != 52 ORDER BY P.t_id ASC, P.WPID ASC';
					if ( $playerselect = $wpdb->get_results( $playerselectsql ) ) {
						foreach ( $playerselect as $s ) {
							echo '<option value="' . $s->PWPID . '">' . bblm_get_team_name( $s->TWPID ) . ' - ' . bblm_get_player_name( $s->PWPID ) . '</option>';
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


?>

</div>
