<?php
/**
 * BBowlLeagueMan Add Player match record
 *
 * Page used to record a players performance during a match
 *
 * @class 		BBLM_Add_Match_Player
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/Admin
 * @version 	2.0
 */
//Check the file is not being accessed directly
 if ( ! defined( 'ABSPATH' ) ) {
 	exit; // Exit if accessed directly
 }

class BBLM_Add_Match_Player {


	// class instance
	static $instance;

	// class constructor
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'plugin_menu' ) );
	}

	public function plugin_menu() {

		$hook = add_submenu_page(
			'bblm_main_menu',
			__( 'Player Actions', 'bblm' ),
			__( 'Player Actions', 'bblm' ),
			'manage_options',
			'bblm_add_match_player',
			array( $this, 'add_match_player_page' )
		);
	}//end of plugin_menu

	/**
	 * The Output of the Page
	 */
	public function add_match_player_page() {
		global $wpdb;

?>
		<div class="wrap">
			<h1 class="wp-heading-inline"><?php echo __( 'Record Player Actions for a Match', 'bblm' ); ?></h1>

<?php

		if ( isset( $_POST[ 'bblm_match_select' ] ) ) {

			//a match has been selected so enter the player actions
			$this->enter_player_actions();

		}
		else if ( ( isset( $_POST[ 'bblm_player_actions' ] ) ) && ( wp_verify_nonce( $_POST[ 'bblm_player_submission' ], basename(__FILE__) ) ) ) {

			//Player actions have been submitted - add them to the database and confirm changes
			$this->save_player_details();

		}
		else {
			//Nothing has been submitted, or final submission is occuring so we display the initial screen

			if ( ( isset( $_POST[ 'bblm_player_increase' ] ) ) && ( wp_verify_nonce( $_POST[ 'bblm_player_changes' ], basename(__FILE__) ) ) ) {

				//Set initil values for loop
				$p = 1;
				$pmax = $_POST['bblm_numofplayers'];
				//define array to hold playerupdate sql
				$playersqla = array();

				while ( $p <= $pmax ) {

					//if  "on" result in "changed" then generate SQL
					if ( isset( $_POST['bblm_pcng'.$p] ) ) {
						if ( "on" == $_POST['bblm_pcng'.$p] ) {

							$updatesql = 'UPDATE `'.$wpdb->prefix.'player` SET `p_ma` = \''.(int) $_POST['bblm_pma'.$p].'\', `p_st` = \''.(int) $_POST['bblm_pst'.$p].'\', `p_ag` = \''.(int) $_POST['bblm_pag'.$p].'\', `p_av` = \''.(int) $_POST['bblm_pav'.$p].'\', `p_skills` = \''.sanitize_textarea_field( esc_textarea( $_POST['bblm_pskills'.$p] ) ).'\', `p_injuries` = \''.sanitize_textarea_field( esc_textarea( $_POST['bblm_pinjuries'.$p] ) ).'\', `p_cost` = \''.(int) $_POST['bblm_pcost'.$p].'\'';

							if ( '1' !== $_POST['bblm_mng'.$p] ) {
								$updatesql .= ', `p_cost_ng` = \''.(int) $_POST['bblm_pcost'.$p].'\'';
							}

							$updatesql .= ' WHERE `p_id` = '. (int) $_POST['bblm_pid'.$p].' LIMIT 1';

							$playersqla[$p] = $updatesql;

						} //end of if changed

					}

					$p++;

				} //end of player loop

				//Now we must set the match to complete
				/*CREATE FUNCTION*/
				$updatematchsql = 'UPDATE `'.$wpdb->prefix.'match` SET `m_complete` = \'1\' WHERE `m_id` = '. (int) $_POST['bblm_mid'].' LIMIT 1';

				//insert the string into the DB
				foreach ($playersqla as $ps) {
					if (FALSE !== $wpdb->query($ps)) {
						$sucess = TRUE;
					}
				}

				bblm_update_tv(  (int) $_POST['bblm_teamA'] );
				bblm_update_tv(  (int) $_POST['bblm_teamB'] );

				if ( FALSE !== $wpdb->query( $updatematchsql ) ) {
					$sucess = TRUE;
				}

?>
				<div id="updated" class="notice notice-success">
					<p>
<?php
				if ( $sucess ) {
					echo __( 'All Player details have been successfully updated','bblm' );
				}
				else {
					echo __( 'Something went wrong, please try again','bblm' );
				}
?>
	</p>
</div>
<?php

		} //end of if final submission is happening
?>
			<h2 class="title"><?php echo __( 'Step 1: Select a Match', 'bblm'); ?></h2>
			<p><?php echo __( 'This page records players actions during a match, and allows player profiles to be updated.', 'bblm' ); ?></p>
			<form name="bblm_selectteam" method="post" id="post">
				<p><?php echo __( 'Below is a list of all the matches that have not yet had their player actions completed. Please select a match and press the continue button.', 'bblm' ); ?></p>

				<table class="form-table">
					<tr valign="top">
						<th scope="row"><label for="bblm_mid" >Match: </label></th>
						<td><select name="bblm_mid" id="bblm_mid">
<?php
						$matchsql = "SELECT M.m_id, M.m_date, T.WPID AS tA, Q.WPID AS tB, M.m_teamAtd, M.m_teamBtd, M.m_gate, P.guid, M.c_id FROM ".$wpdb->prefix."match M, ".$wpdb->prefix."bb2wp J, ".$wpdb->posts." P, ".$wpdb->prefix."team T, ".$wpdb->prefix."team Q WHERE M.m_id = J.tid AND J.pid = P.ID AND J.prefix = 'm_' AND M.m_teamA = T.t_id AND M.m_teamB = Q.t_id AND M.m_complete = 0 ORDER BY m_date DESC, m_id DESC";
						if ($matches = $wpdb->get_results($matchsql)) {
							foreach ($matches as $match) {
								echo '<option value="' . $match->m_id . '">' . bblm_get_competition_name( $match->c_id ) . ' - ' . bblm_get_team_name( $match->tA ) . ' ' . $match->m_teamAtd . ' vs ' . $match->m_teamBtd . ' ' . bblm_get_team_name( $match->tB ) . '</option>';
							}
						}
?>
						</select></td>
					</tr>
				</table>
				<p class="submit"><input type="submit" name="bblm_match_select" value="Continue" title="Select the above Match" class="button-primary"/></p>
			</form>
		</div>
<?PHP
		}//end of nothing submitted so display initial page
	} //end of page output

	/*
	 * Enter details of the player actions
	 */
	 public function enter_player_actions() {
		 global $wpdb;
?>
			<h2 class="title"><?php echo __( 'Step 2: Record Player Actions', 'bblm'); ?></h2>
			<p><strong><?php echo __( 'Warning', 'bblm' ); ?></strong>: <?php echo __( 'This may take some time to process all the information! Please ', 'bblm' ); ?><strong><?php echo __( 'don\'t', 'bblm' ); ?></strong><?php echo __( ' hit submit multiple times.', 'bblm' ); ?></p>
<?php
			$ccounts = 0;
			$matchsql2 = "SELECT M.m_id, UNIX_TIMESTAMP(M.m_date) AS MDATE, M.m_teamA AS tAid, M.m_teamB AS tBid, T.t_name AS tA, Q.t_name AS tB, M.m_teamAtd, M.m_teamBtd, A.mt_cas AS tAcas, B.mt_cas AS tBcas, A.mt_int AS tAint, B.mt_int AS tBint, A.mt_comp AS tAcomp, B.mt_comp AS tBcomp, M.c_id FROM ".$wpdb->prefix."match M, ".$wpdb->prefix."team T, ".$wpdb->prefix."team Q, ".$wpdb->prefix."match_team A, ".$wpdb->prefix."match_team B WHERE M.m_teamA = T.t_id AND M.m_teamB = Q.t_id AND M.m_complete = 0 AND A.m_id = M.m_id AND A.t_id = M.m_teamA AND B.m_id = M.m_id AND B.t_id = M.m_teamB AND M.m_id = ". (int) $_POST['bblm_mid'];
			if ($md = $wpdb->get_row($matchsql2)) {
?>
			<h3><?php echo __( 'Match Reference', 'bblm' ); ?></h3>
			<table cellspacing="0" class="widefat" style="width:360px;">
			<thead>
				<tr>
					<th scope="col"><?php echo $md->tA; ?></th>
					<th scope="col" class="column-comments">VS</th>
					<th scope="col"><?php echo $md->tB; ?></th>
				</tr>
				<tr>
					<th colspan="3">Date: <?php echo date( "d.m.Y", $md->MDATE ) ; ?></th>
				</tr>
			</thead>
			<tbody>
				<tr class="alternate">
					<td><?php echo $md->m_teamAtd; ?></td>
					<th class="column-comments">TD</th>
					<td><?php echo $md->m_teamBtd; ?></td>
				</tr>
				<tr>
					<td><?php echo $md->tAcas; ?></td>
					<th class="column-comments">CAS</th>
					<td><?php echo $md->tBcas; ?></td>
				</tr>
				<tr class="alternate">
					<td><?php echo $md->tAcomp; ?></td>
					<th class="column-comments">COMP</th>
					<td><?php echo $md->tBcomp; ?></td>
				</tr>
				<tr>
					<td><?php echo $md->tAint; ?></td>
					<th class="column-comments">INT</th>
					<td><?php echo $md->tBint; ?></td>
				</tr>
			</tbody>
		</table>
<?php
				$tAid = $md->tAid;
				$tBid = $md->tBid;
				$teamA = $md->tA;
				$teamB = $md->tB;
				if ( BBLM_CPT_Comp::does_comp_count( $md->c_id ) ) {
					$ccounts = 1;
				}
			}
?>

			<form name="bblm_recordparticipation" method="post" id="post">
				<input type="hidden" name="bblm_mid" size="3" value="<?php echo  (int) $_POST['bblm_mid']; ?>" />
				<input type="hidden" name="bblm_ccounts" size="3" value="<?php echo $ccounts; ?>" />
				<input type="hidden" name="bblm_teamA" size="3" value="<?php echo $tAid; ?>" />
				<input type="hidden" name="bblm_teamB" size="3" value="<?php echo $tBid; ?>" />

				<h2><?php echo __( 'Please Detail Participation', 'bblm' ); ?></h2>
				<p><?php echo __( 'Below are all the players who where available to take part in this match. If they took part in this match please ensure that the &quot;Played?&quot; tickbox is selected.', 'bblm' ); ?></p>
				<p><?php echo __( 'There will be a chance to record the actions of any Star Players at the bottom of the page.', 'bblm' ); ?></p>

<?php

			$noplayers = 0;
			$playersql = 'SELECT P.p_id, P.t_id, P.p_spp, X.post_title AS p_name, P.p_num, T.t_name from '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' X where P.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = X.ID AND P.t_id = T.t_id AND (P.t_id = '.$tAid.' OR P.t_id = '.$tBid.') AND P.p_status = 1 AND P.p_mng = 0 ORDER BY P.t_id, P.p_num';
			if ( $playerlist = $wpdb->get_results( $playersql ) ) {
				//initiate var for count

				$p = 1;
				$is_first = 1;
				$current_team = "";

				foreach ( $playerlist as $pl ) {
					if ( $pl->t_name !== $current_team ) {
						$current_team = $pl->t_name;
						if ( 1 !== $is_first ) {
							echo '</tbody></table>';
						}
						$is_first = 1;
					}

					if ( $is_first ) {
?>
				<h3><?php echo $pl->t_name; ?></h3>
				<table cellspacing="0" class="widefat">
					<thead>
						<tr>
							<th>#</th>
							<th><?php echo __( 'Name', 'bblm' ); ?></th>
							<th><?php echo __( 'TD', 'bblm' ); ?></th>
							<th><?php echo __( 'COMP', 'bblm' ); ?></th>
							<th><?php echo __( 'CAS', 'bblm' ); ?></th>
							<th><?php echo __( 'INT', 'bblm' ); ?></th>
							<th><?php echo __( 'MVP', 'bblm' ); ?></th>
							<th><?php echo __( 'Gained SPP', 'bblm' ); ?></th>
							<th><?php echo __( 'Prev SPP', 'bblm' ); ?></th>
							<th><?php echo __( 'Played?', 'bblm' ); ?></th>
							<th><?php echo __( 'MNG?', 'bblm' ); ?></th>
							<th><?php echo __( 'Increase', 'bblm' ); ?></th>
							<th><?php echo __( 'Injury', 'bblm' ); ?></th>
						</tr>
					</thead>
					<tbody>
<?php
						$is_first = 0;
					}
					if ( $p % 2 ) {
						echo '<tr>';
					}
					else {
						echo '<tr class="alternate">';
					}
?>
							<td>
								<input type="hidden" name="bblm_tid<?php echo $p; ?>" id="bblm_tid<?php echo $p; ?>" size="3" value="<?php echo $pl->t_id; ?>" />
								<input type="hidden" name="bblm_pid<?php echo $p; ?>" size="3" value="<?php echo $pl->p_id; ?>" /><?php echo $pl->p_num; ?>
							</td>
							<td><?php echo $pl->p_name; ?></td>
							<td><input type="text" name="bblm_td<?php echo $p; ?>" id="bblm_td<?php echo $p; ?>" size="3" value="0" maxlength="2" onChange="UpdateSPP(<?php echo $p; ?>)" /></td>
							<td><input type="text" name="bblm_comp<?php echo $p; ?>" id="bblm_comp<?php echo $p; ?>" size="3" value="0" maxlength="2" onChange="UpdateSPP(<?php echo $p; ?>)" /></td>
							<td><input type="text" name="bblm_cas<?php echo $p; ?>" id="bblm_cas<?php echo $p; ?>" size="3" value="0" maxlength="2" onChange="UpdateSPP(<?php echo $p; ?>)" /></td>
							<td><input type="text" name="bblm_int<?php echo $p; ?>" id="bblm_int<?php echo $p; ?>" size="3" value="0" maxlength="2" onChange="UpdateSPP(<?php echo $p; ?>)" /></td>
							<td><input type="text" name="bblm_mvp<?php echo $p; ?>" id="bblm_mvp<?php echo $p; ?>" size="3" value="0" maxlength="1" onChange="UpdateSPP(<?php echo $p; ?>)" /></td>
<?php
					if ( $ccounts ) {
?>
							<td style="background-color:#ddd;"><input type="text" name="bblm_spp<?php echo $p; ?>" id="bblm_spp<?php echo $p; ?>" size="3" value="0" maxlength="2" /></td>
<?php
					}
					else {
?>
							<td style="background-color:#ddd;"><input type="hidden" name="bblm_spp<?php echo $p; ?>" id="bblm_spp<?php echo $p; ?>" size="3" value="0" maxlength="2" />N/A</td>
<?php
					}
?>
							<td><input type="hidden" name="bblm_oldspp<?php echo $p; ?>" id="bblm_oldspp<?php echo $p; ?>" size="3" value="<?php echo $pl->p_spp; ?>" /><?php echo $pl->p_spp; ?></td>
							<td><input type="checkbox" name="bblm_plyd<?php echo $p; ?>" checked="checked" /></td>
							<td><input type="checkbox" name="mng<?php echo $p; ?>" /></td>
<?php
					if ( $ccounts ) {
?>
							<td><input type="text" name="bblm_increase<?php echo $p; ?>" id="bblm_increase<?php echo $p; ?>" size="10" value="" maxlength="30" /></td>
<?php
					}
					else {
?>
							<td><input type="hidden" name="bblm_increase<?php echo $p; ?>" id="bblm_increase<?php echo $p; ?>" size="10" value="" maxlength="30" /><?php echo __( 'Exhibition Game', 'bblm'); ?></td>
<?php
					}
?>
							<td><input type="text" name="bblm_injury<?php echo $p; ?>" size="10" value="" maxlength="30" /></td>
						</tr>
<?php
					$p++;
				} //end of for each
				echo '</tbody></table>';

				//end of player output - start of Star Player output

				//Determine the Star Player Team (to get the star players)
				$bblm_team_star = bblm_get_star_player_team();

				$starssql = 'SELECT X.p_id, X.WPID AS PWPID FROM '.$wpdb->prefix.'player X WHERE X.t_id = '.$bblm_team_star.' order by X.p_id ASC';
				if ( $stars = $wpdb->get_results( $starssql ) ) {
					$starlist = "";
					foreach ( $stars as $star ) {
						$starlist .= "<option value=\"".$star->p_id."\">" . bblm_get_player_name( $star->PWPID ) . "</option>\n";
					}
					//create a drop down containing the two teams.
					$teamlist = "	<option value=\"".$tAid."\">".$teamA."</option>\n	<option value=\"".$tBid."\">".$teamB."</option>\n";

					$pmax = $p+4;
?>
				<h3><?php echo __( 'Star Players', 'bblm' ); ?></h3>
				<p><?php echo __( 'If Any Star Players took part in the match enter their details below and tick the played box.', 'bblm' ); ?></p>
				<table cellspacing="0" class="widefat">
					<thead>
						<tr>
							<th><?php echo __( 'Team', 'bblm' ); ?></th>
							<th><?php echo __( 'Star', 'bblm' ); ?></th>
							<th><?php echo __( 'TD', 'bblm' ); ?></th>
							<th><?php echo __( 'COMP', 'bblm' ); ?></th>
							<th><?php echo __( 'CAS', 'bblm' ); ?></th>
							<th><?php echo __( 'INT', 'bblm' ); ?></th>
							<th><?php echo __( 'MVP', 'bblm' ); ?></th>
							<th><?php echo __( 'SPP', 'bblm' ); ?></th>
							<th><?php echo __( 'Played?', 'bblm' ); ?></th>
						</tr>
					</thead>
					<tbody>
<?php
					while ( $p < $pmax ){

						if ( $p % 2 ) {
							echo '<tr>';
						}
						else {
							echo '<tr class="alternate">';
						}
?>
							<td><select id="bblm_tid<?php echo $p; ?>" name="bblm_tid<?php echo $p; ?>"><?php echo $teamlist; ?></select></td>
							<td><select id="bblm_pid<?php echo $p; ?>" name="bblm_pid<?php echo $p; ?>"><?php echo $starlist; ?></select></td>
							<td><input type="text" name="bblm_td<?php echo $p; ?>" id="bblm_td<?php echo $p; ?>" size="3" value="0" maxlength="2" onChange="UpdateSPP(<?php echo $p; ?>)" /></td>
							<td><input type="text" name="bblm_comp<?php echo $p; ?>" id="bblm_comp<?php echo $p; ?>" size="3" value="0" maxlength="2" onChange="UpdateSPP(<?php echo $p; ?>)" /></td>
							<td><input type="text" name="bblm_cas<?php echo $p; ?>" id="bblm_cas<?php echo $p; ?>" size="3" value="0" maxlength="2" onChange="UpdateSPP(<?php echo $p; ?>)" /></td>
							<td><input type="text" name="bblm_int<?php echo $p; ?>" id="bblm_int<?php echo $p; ?>" size="3" value="0" maxlength="2" onChange="UpdateSPP(<?php echo $p; ?>)" /></td>
							<td><input type="text" name="bblm_mvp<?php echo $p; ?>" id="bblm_mvp<?php echo $p; ?>" size="3" value="0" maxlength="1" onChange="UpdateSPP(<?php echo $p; ?>)" /></td>
							<td style="background-color:#ddd;"><input type="text" name="bblm_spp<?php echo $p; ?>" id="bblm_spp<?php echo $p; ?>" size="3" value="0" maxlength="2" /></td>
							<td>
								<input type="checkbox" name="bblm_plyd<?php echo $p; ?>" />
								<input type="hidden" name="bblm_oldspp<?php echo $p; ?>" id="bblm_oldspp<?php echo $p; ?>" size="3" value="0" />
								<input type="hidden" name="mng<?php echo $p; ?>" id="mng<?php echo $p; ?>" size="3" value="0" />
								<input type="hidden" name="bblm_increase<?php echo $p; ?>" id="bblm_increase<?php echo $p; ?>" size="10" value="" maxlength="30" />
								<input type="hidden" name="bblm_injury<?php echo $p; ?>" size="10" value="" maxlength="30" />
							</td>
						</tr>
<?php
						$p++;
					}//end of while
					echo '</tbody></table>';
				}//end of if stars
?>
				<input type="hidden" name="bblm_numofplayers" size="2" value="<?php echo $p-1; ?>" />
				<p class="submit"><input type="submit" name="bblm_player_actions" value="Submit Player Actions" title="Submit Player Actions" class="button-primary"/></p>
				<?php wp_nonce_field( basename( __FILE__ ), 'bblm_player_submission' ); ?>
			</form>
<?php

			} //end of if there are players assigned to the team
			else {
				echo '<p><strong>' . __( 'These teams do not have any players registered with them! Please add some to the teams before you can continue.', 'bblm' ) . '</strong></p>';
				$noplayers = 1;
			}
?>


				<h3><?php echo __( 'Increase Reference', 'bblm' ); ?></h3>

				<table cellspacing="0" class="widefat" style="width:360px;">
					<thead>
						<tr>
							<th><?php echo __( 'SPPS', 'bblm' ); ?></th>
							<th><?php echo __( 'Title', 'bblm' ); ?></th>
							<th><?php echo __( 'Increases', 'bblm' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><?php echo __( '0 - 5', 'bblm' ); ?></td>
							<td><?php echo __( 'Rookie', 'bblm' ); ?></td>
							<td><?php echo __( 'None', 'bblm' ); ?></td>
						</tr>
						<tr class="alternate">
							<td><?php echo __( '6 - 15', 'bblm' ); ?></td>
							<td><?php echo __( 'Experienced', 'bblm' ); ?></td>
							<td><?php echo __( 'One', 'bblm' ); ?></td>
						</tr>
						<tr>
							<td><?php echo __( '16 - 30', 'bblm' ); ?></td>
							<td><?php echo __( 'Veteran', 'bblm' ); ?></td>
							<td><?php echo __( 'Two', 'bblm' ); ?></td>
						</tr>
						<tr class="alternate">
							<td><?php echo __( '31 - 50', 'bblm' ); ?></td>
							<td><?php echo __( 'Emerging Star', 'bblm' ); ?></td>
							<td><?php echo __( 'Three', 'bblm' ); ?></td>
						</tr>
						<tr>
							<td><?php echo __( '51 - 75', 'bblm' ); ?></td>
							<td><?php echo __( 'Star', 'bblm' ); ?></td>
							<td><?php echo __( 'Four', 'bblm' ); ?></td>
						</tr>
						<tr class="alternate">
							<td><?php echo __( '76 - 175', 'bblm' ); ?></td>
							<td><?php echo __( 'Super Star', 'bblm' ); ?></td>
							<td><?php echo __( 'Five', 'bblm' ); ?></td>
						</tr>
						<tr>
							<td><?php echo __( '175+', 'bblm' ); ?></td>
							<td><?php echo __( 'Legend', 'bblm' ); ?></td>
							<td><?php echo __( 'Six', 'bblm' ); ?></td>
						</tr>
					</tbody>
				</table>

				<h3><?php echo __( 'SPP Reference', 'bblm' ); ?></h3>

				<table cellspacing="0" class="widefat" style="width:360px;">
					<thead>
						<tr>
							<th><?php echo __( 'Action', 'bblm' ); ?></th>
							<th><?php echo __( 'SPP', 'bblm' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><?php echo __( 'Passing Completion', 'bblm' ); ?></td>
							<td>1</td>
						</tr>
						<tr class="alternate">
							<td><?php echo __( 'Casuality', 'bblm' ); ?></td>
							<td>2</td>
						</tr>
						<tr>
							<td><?php echo __( 'Interception', 'bblm' ); ?></td>
							<td>2</td>
						</tr>
						<tr class="alternate">
							<td><?php echo __( 'Touchdown', 'bblm' ); ?></td>
							<td>3</td>
						</tr>
						<tr>
							<td><?php echo __( 'Most Valued Player (MVP)', 'bblm' ); ?></td>
							<td>5</td>
						</tr>
					</tbody>
				</table>

<?php

		} //end of enter_player_actions()

	 /*
		* confirm updated player profiles before submission to the database
		*/
		public function save_player_details() {
			global $wpdb;

			$playeractionsubmission = $this->save_player_actions();

			//Only continue if submitting the player actions to the database was successful
			if ( 1 == $playeractionsubmission ) {
				//the submission was successful and the competition counts

?>
			<h2 class="title"><?php echo __( 'Step 3: Record Changes to Players', 'bblm'); ?></h2>
			<p><?php echo __( 'Below are all the players who took part in the match. If they had an increase, please ensure that the "Changed?" box is ticked. The Skills and Injuries boxes should be automatically filled but you will have to update any changes to Stats manually.', 'bblm' ); ?></p>
			<p><strong><?php echo __( 'Warning', 'bblm' ); ?></strong>: <?php echo __( 'This may take some time to process all the information! Please ', 'bblm' ); ?><strong><?php echo __( 'don\'t', 'bblm' ); ?></strong><?php echo __( ' hit submit multiple times.', 'bblm' ); ?></p>

			<form name="bblm_recordincreases" method="post" id="post">
				<input type="hidden" name="bblm_mid" size="3" value="<?php echo  (int) $_POST['bblm_mid']; ?>" />
				<input type="hidden" name="bblm_ccounts" size="3" value="<?php echo $_POST['bblm_ccounts']; ?>" />
				<input type="hidden" name="bblm_teamA" size="3" value="<?php echo  (int) $_POST['bblm_teamA']; ?>" />
				<input type="hidden" name="bblm_teamB" size="3" value="<?php echo  (int) $_POST['bblm_teamB']; ?>" />

<?php
				$playersql = 'SELECT P.*, M.mp_inj, M.mp_inc, T.t_name FROM '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T WHERE P.p_id = M.p_id AND m_id = '. (int) $_POST['bblm_mid'].' AND M.t_id = T.t_id ORDER BY P.t_id, P.p_num';
				if ( $playerlist = $wpdb->get_results( $playersql ) ) {

					//initiate var for count
					$p = 1;
					$is_first = 1;
					$current_team = "";

					foreach ($playerlist as $pl) {
						if ($pl->t_name !== $current_team) {
							$current_team = $pl->t_name;
							if (1 !== $is_first) {
								echo '</tbody></table>';
							}
							$is_first = 1;
						}

						if ($is_first) {
?>
				<h3><?php echo $pl->t_name; ?></h3>
				<table cellspacing="0" class="widefat">
					<thead>
						<tr>
							<th>#</th>
							<th><?php echo __( 'Name', 'bblm' ); ?></th>
							<th><?php echo __( 'MA', 'bblm' ); ?></th>
							<th><?php echo __( 'ST', 'bblm' ); ?></th>
							<th><?php echo __( 'AG', 'bblm' ); ?></th>
							<th><?php echo __( 'AV', 'bblm' ); ?></th>
							<th><?php echo __( 'SPP', 'bblm' ); ?></th>
							<th><?php echo __( 'COST', 'bblm' ); ?></th>
							<th><?php echo __( 'Skills', 'bblm' ); ?></th>
							<th><?php echo __( 'Injuries', 'bblm' ); ?></th>
							<th><?php echo __( 'Changed?', 'bblm' ); ?></th>
						</tr>
					</thead>
					<tbody>
<?php
							$is_first = 0;
						}
						echo '<tr>';
						if ($p % 2) {
							echo '<tr>';
						}
						else {
							echo '<tr class="alternate">';
						}

						$incmade = 0;
						$injmade = 0;
						//Determine if player has had an increase or injury
						if ( "none" !== $pl->mp_inc ) {
							$incmade = 1;
						}
						else if ( "none" !== $pl->mp_inj ) {
							$injmade = 1;
						}

						echo '<td><input type="hidden" name="bblm_pid'.$p.'" size="3" value="'.$pl->p_id.'" />';
						echo '<input type="hidden" name="bblm_mng'.$p.'" size="3" value="'.$pl->p_mng.'" />'.$pl->p_num.'</td>';
						echo '<td>' . $pl->p_name . '</td>';
						echo '<td><input type="text" name="bblm_pma'.$p.'" size="3" value="'.$pl->p_ma.'" maxlength="2" /></td>';
						echo '<td><input type="text" name="bblm_pst'.$p.'" size="3" value="'.$pl->p_st.'" maxlength="2" /></td>';
						echo '<td><input type="text" name="bblm_pag'.$p.'" size="3" value="'.$pl->p_ag.'" maxlength="2" /></td>';
						echo '<td><input type="text" name="bblm_pav'.$p.'" size="3" value="'.$pl->p_av.'" maxlength="2" /></td>';
						echo '<td><input type="text" name="bblm_pspp'.$p.'" size="3" value="'.$pl->p_spp.'" maxlength="2" /></td>';
						echo '<td><input type="text" name="bblm_pcost'.$p.'" size="7" value="'.$pl->p_cost.'" maxlength="7"';
						if ( $incmade ) {
							echo ' style="background-color:#5EFB6E"';
						}
						echo '></td>';
						echo '<td><input type="text" name="bblm_pskills'.$p.'" size="20" value="'.$pl->p_skills;
						if ( $incmade ) {
							echo ', '.$pl->mp_inc.'" style="background-color:#5EFB6E';
						}
						echo '"></td>';
						echo '<td><input type="text" name="bblm_pinjuries'.$p.'" size="20" value="'.$pl->p_injuries;
						if ( $injmade ) {
							echo ', '.$pl->mp_inj.'" style="background-color:#5EFB6E';
						}
						echo '"></td>';
						echo '<td><input type="checkbox" name="bblm_pcng'.$p.'"';
						if ( $incmade || $injmade ) {
							echo ' checked="checked"';
						}
						echo '></td>';
						echo '</tr>';

						$p++;
						$incmade = 0;
						$injmade = 0;
					}
					echo '</table>';
				}
				else {
					echo '<p><strong>' . __( 'It appears that no players actually took part in this match! Please try again', 'bblm' ) . '</strong></p>';
				}
?>
							<input type="hidden" name="bblm_numofplayers" size="2" value="<?php echo $p-1; ?>" />
							<p class="submit"><input type="submit" name="bblm_player_increase" tabindex="4" value="Submit Player Changes" title="Submit Player Changes"/ class="button-primary" /></p>
							<?php wp_nonce_field( basename( __FILE__ ), 'bblm_player_changes' ); ?>
						</form>

						<h3><?php echo __( 'Cost Reference', 'bblm' ); ?></h3>

						<table cellspacing="0" class="widefat" style="width:360px;">
							<thead>
								<tr>
									<th><?php echo __( 'Cost', 'bblm' ); ?></th>
									<th><?php echo __( 'Description', 'bblm' ); ?></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>20,000</td>
									<td><?php echo __( 'New Skill', 'bblm' ); ?></td>
								</tr>
								<tr class="alternate">
									<td>30,000</td>
									<td><?php echo __( 'Double Skill', 'bblm' ); ?></td>
								</tr>
								<tr>
									<td>30,000</td>
									<td>+MA <?php echo __( 'or', 'bblm' ); ?> +AV</td>
								</tr>
								<tr class="alternate">
									<td>40,000</td>
									<td>+AG</td>
								</tr>
								<tr>
									<td>50,000</td>
									<td>+ST</td>
								</tr>
							</tbody>
							</table>
<?php

			}//end of if submission of playere actions was successfull
			else if ( 2 == $playeractionsubmission ) {
				//The submission was successful but the competiton does not count - terminate
?>
				<div id="updated" class="notice notice-success">
					<p><?php echo __( 'The player actions were successfully captured.','bblm' ); ?></p>
				</div>
<?php
			}
			else {
				//something went wrong with the database submission
?>
				<div id="updated" class="notice notice-success">
					<p><?php echo __( 'Something went wrong, please try again','bblm' ); ?></p>
				</div>
<?php
			}


		} //end of save_player_details()

		/*
 		* Saves the player actions to the database
 		*/
		public function save_player_actions() {
			global $wpdb;

			$compcounts = (int) $_POST['bblm_ccounts'];
			$finished = 0;

			$playermatchsql = "INSERT INTO `".$wpdb->prefix."match_player` (`m_id`, `p_id`, `t_id`, `mp_td`, `mp_cas`, `mp_comp`, `mp_int`, `mp_mvp`, `mp_spp`, `mp_mng`, `mp_inj`, `mp_inc`, `mp_counts`) VALUES ";
			//Set initial values for loop
			$p = 1;
			$pmax = (int) $_POST['bblm_numofplayers'];
			//define array to hold injured players
			$playerinj = array();
			//define array to hold injured sql
			$reactivatesql = array();

			//before we begin the main loop, we re-activate all the players who missed the game
			/*CREATE FUNCTION*/
			$selectinjplayer = 'SELECT p_id FROM '.$wpdb->prefix.'player WHERE p_mng = 1 AND (t_id = '.(int) $_POST['bblm_teamA'].' or t_id = '. (int) $_POST['bblm_teamB'].')';

			if ( $injplayer = $wpdb->get_results( $selectinjplayer ) ) {
				foreach ( $injplayer as $ip ) {
					$reactivatesql[] .= 'UPDATE `'.$wpdb->prefix.'player` SET `p_mng` = \'0\', `p_cost_ng` = p_cost  WHERE `p_id` = '.$ip->p_id.' LIMIT 1';
				}
			}

			//Initialize var to capture first input
			$is_first_player = 1;
			$playerplayed = array(); //Records the list of players who took part

			//Beginning of main loop.
			while ( $p <= $pmax ){

				//before we go any further, we should see if the player in question ctually took part in the match!
				if ( isset( $_POST['bblm_plyd'.$p] ) ) {

					if ("on" == $_POST['bblm_plyd'.$p]) {

						$playerplayed[$p] =  (int) $_POST['bblm_pid'.$p];

						//we only want a comma added for all but the first
						if ( 1 !== $is_first_player ) {
							$playermatchsql .= ", ";
						}

						$mng = array();

						//Set the default "on" result from a checkbox to a 1
						if ( isset( $_POST['mng'.$p] ) ) {
							$mng[$p] = $_POST['mng'.$p];

							if ( "on" == $mng[$p] ) {
								$mng[$p] = 1;
							}
							else {
								$mng[$p] = 0;
							}
						}//end of if mng is set
						else {
							$mng[$p] = 0;
						}

						//Fill in blanks for Injuries and Increases
						if ( empty( $_POST['bblm_injury'.$p] ) ) {
							$_POST['bblm_injury'.$p] = "none";
						}
						if ( empty( $_POST['bblm_increase'.$p] ) ) {
							$_POST['bblm_increase'.$p] = "none";
						}

						//generate the SQL
						$playermatchsql .= '(\''. (int) $_POST['bblm_mid'].'\', \''. (int) $_POST['bblm_pid'.$p].'\', \''. (int) $_POST['bblm_tid'.$p].'\', \''. (int) $_POST['bblm_td'.$p].'\', \''. (int) $_POST['bblm_cas'.$p].'\', \''. (int) $_POST['bblm_comp'.$p].'\', \''. (int) $_POST['bblm_int'.$p].'\', \''. (int) $_POST['bblm_mvp'.$p].'\', \''. (int) $_POST['bblm_spp'.$p].'\', \''. $mng[$p] .'\', \''.sanitize_textarea_field( esc_textarea( $_POST['bblm_injury'.$p] ) ).'\', \''.sanitize_textarea_field( esc_textarea( $_POST['bblm_increase'.$p] ) ).'\', \''.$compcounts.'\')';

						//If the player is injured (exhibition or otherwise) then update the player table
						$playerupdatesql = "";
						if ( $mng[$p] )  {
							$playerupdatesql = 'UPDATE `'.$wpdb->prefix.'player` SET `p_cost_ng` = \'0\', `p_mng` = \'1\' WHERE `p_id` = \''. (int) $_POST['bblm_pid'.$p].'\' LIMIT 1';
						}

						//once we have the sql generated, we can insert into the array to insert later on
						if ( strlen( $playerupdatesql ) > 0 ) {
							$playerinj[$p] = $playerupdatesql;
						}

						//change flag so that comma's are added to the insert sql string.
						//We do this here because we don't want the flag to change until someone has been entered into the db
						$is_first_player = 0;

					}//end of bblm_plyd checking

				}//end of ifset player played

				// increment player and go onto the next one.
				$p++;

			} //end of while

			//Generate SQL to update the match
			$updatematchsql = 'UPDATE `'.$wpdb->prefix.'match` SET `m_complete` = \'1\' WHERE `m_id` = '. (int) $_POST['bblm_mid'].' LIMIT 1';

			$result = 0;
			//Regardless of if the comp counts, we add the player records to the match_player table
			if ( FALSE !== $wpdb->query( $playermatchsql ) ) {

				/*CREATE FUNCTION*/
				foreach ( $reactivatesql as $rs ) {
					if ( FALSE !== $wpdb->query( $rs ) ) {
						$result = 1;
					}
				}
				foreach ( $playerinj as $ps ) {
					if ( FALSE !== $wpdb->query( $ps ) ) {
						$result = 1;
					}
				}

				bblm_update_tv(  (int) $_POST['bblm_teamA'] );
				bblm_update_tv(  (int) $_POST['bblm_teamB'] );

				$result = 1;
				do_action( 'bblm_post_submission' );
			}
			//then if the comp counts, update the participating players SPP
			if ( $compcounts ) {

				foreach ( $playerplayed as $pssp ) {
					//Update the players SPP
					bblm_update_player( $pssp, $compcounts );
				}

			} //end of if comp counts
			else {
				//update the match as complete
				/*CREATE FUNCTION*/
				$updatematchsql = 'UPDATE `'.$wpdb->prefix.'match` SET `m_complete` = \'1\' WHERE `m_id` = '. (int) $_POST['bblm_mid'].' LIMIT 1';
				if ( FALSE !== $wpdb->query( $updatematchsql ) ) {
					$result = 1;
				}
			}

			//Determine what to return to the main script

			if ( $result && $compcounts ) {
				//DB insert was successful, and the competition counts
				return 1;

			}
			else if ( $result && !$compcounts ) {
				//DB insert was successful, but the competition does not count
				return 2;

			}
			else {
				//Something went wrong
				return 0;
			}

		}//end of save_player_actions()


} //end of CLASS BBLM_Add_Match_Player
