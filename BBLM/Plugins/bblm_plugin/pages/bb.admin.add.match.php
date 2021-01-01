<?php
/**
 * BBowlLeagueMan Add Match Admin page
 *
 * Main page used to record details of a match
 *
 * @class 		BBLM_Add_Match
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/Admin
 * @version 	2.0
 */
//Check the file is not being accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class BBLM_Add_Match {

	// class instance
	static $instance;

	// class constructor
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'plugin_menu' ) );
	}

	public function plugin_menu() {

		$hook = add_submenu_page(
			'bblm_main_menu',
			__( 'Record Match', 'bblm' ),
			__( 'Record Match', 'bblm' ),
			'manage_options',
			'bblm_add_match',
			array( $this, 'add_match_page' )
		);

	}//end of plugin_menu

	/**
	 * The Output of the Page
	 */
	public function add_match_page() {
		global $wpdb;

?>
		<div class="wrap">
			<h1 class="wp-heading-inline"><?php echo __( 'Record a Match', 'bblm' ); ?></h1>
			<p><?php echo __( 'This page is used to record the details of a match that took place in the League.', 'bblm' ); ?></p>
<?php

			$submissionresult = 0;

			//Check to see if the final submission has been made
			if ( ( isset( $_POST[ 'bblm_match_add' ] ) ) && ( wp_verify_nonce( $_POST[ 'bblm_match_submission' ], basename(__FILE__) ) ) ) {

				//initialise variables
				$bblm_submit_tA = array();
				$bblm_submit_tB = array();
				$bblm_submit_match = array();

				$bblm_submit_tA['id'] = (int) $_POST['bblm_teama'];
				$bblm_submit_tA['tv'] = (int) $_POST['tAtr'];
				$bblm_submit_tA['td'] = (int) $_POST['tAtd'];
				$bblm_submit_tA['cas'] = (int) $_POST['tAcas'];
				$bblm_submit_tA['int'] = (int) $_POST['tAint'];
				$bblm_submit_tA['comp'] = (int) $_POST['tAcomp'];
				$bblm_submit_tA['att'] = (int) $_POST['tAatt'];
				$bblm_submit_tA['winning'] = (int) $_POST['tAwin'];
				$bblm_submit_tA['ff'] = (int) $_POST['tAff'];
				$bblm_submit_tA['comment'] = sanitize_textarea_field( esc_textarea( $_POST['tAnotes'] ) );
				if ( isset( $_POST['tAcddiv'] ) ) {
					$bblm_submit_tA['div'] = (int) $_POST['tAcddiv'];
				}
				else {
					$bblm_submit_tA['div'] = 0;
				}

				$bblm_submit_tB['id'] = (int) $_POST['bblm_teamb'];
				$bblm_submit_tB['tv'] = (int) $_POST['tBtr'];
				$bblm_submit_tB['td'] = (int) $_POST['tBtd'];
				$bblm_submit_tB['cas'] = (int) $_POST['tBcas'];
				$bblm_submit_tB['int'] = (int) $_POST['tBint'];
				$bblm_submit_tB['comp'] = (int) $_POST['tBcomp'];
				$bblm_submit_tB['att'] = (int) $_POST['tBatt'];
				$bblm_submit_tB['winning'] = (int) $_POST['tBwin'];
				$bblm_submit_tB['ff'] = (int) $_POST['tBff'];
				$bblm_submit_tB['comment'] = sanitize_textarea_field( esc_textarea( $_POST['tBnotes'] ) );
				if ( isset( $_POST['tBcddiv'] ) ) {
					$bblm_submit_tB['div'] = (int) $_POST['tBcddiv'];
				}
				else {
					$bblm_submit_tB['div'] = 0;
				}

				$bblm_submit_match['date'] = sanitize_text_field( esc_textarea( $_POST['mdate'] ) ) . " 12:00:00";
				$bblm_submit_match['comp'] = (int) $_POST['bblm_comp'];
				$bblm_submit_match['div'] = (int) $_POST['bblm_div'];
				$bblm_submit_match['stad'] = (int) $_POST['mstad'];
				$bblm_submit_match['gate'] = (int) $_POST['gate'];
				$bblm_submit_match['trivia'] = sanitize_textarea_field( esc_textarea( $_POST['matchtrivia'] ) );
				$bblm_submit_match['weather1'] = (int) $_POST['mweather'];
				$bblm_submit_match['weather2'] = (int) $_POST['mweather2'];
				$bblm_submit_match['content'] = "No Report Filed Yet";
				$bblm_submit_match['title'] = "";
				if ( isset( $_POST['bblm_fid'] ) ) {
					$bblm_submit_match['fixture'] = (int) $_POST['bblm_fid'];
				}
				else {
					$bblm_submit_match['fixture'] = 0;
				}

				/** Grab Information From the Database **/
				$bblm_submit_tA['TWPID'] = $wpdb->get_var( "SELECT WPID AS TWPID FROM ".$wpdb->prefix."team WHERE t_id=" . $bblm_submit_tA['id'] );
				$bblm_submit_tB['TWPID'] = $wpdb->get_var( "SELECT WPID AS TWPID FROM ".$wpdb->prefix."team WHERE t_id=" . $bblm_submit_tB['id'] );

				/** Content Preperation **/
				$bblm_submit_match['title'] = esc_sql( sanitize_textarea_field( esc_textarea( get_the_title( $bblm_submit_tA['TWPID'] ) . ' vs ' . get_the_title( $bblm_submit_tB['TWPID'] ) ) ) );

				//Determine if competition counts
				if ( BBLM_CPT_Comp::does_comp_count( $bblm_submit_match['comp'] ) ) {
					$bblm_submit_match['compcounts'] = 1;
				}

				//get page # for parent from DB (pre custom post type)
				$options = get_option( 'bblm_config' );
				$bblm_submit_match['parent'] = htmlspecialchars( $options[ 'page_match' ], ENT_QUOTES );

				///Match Calculations
				$bblm_submit_match['td'] = $bblm_submit_tA['td'] + $bblm_submit_tB['td'];
				$bblm_submit_match['cas'] = $bblm_submit_tA['cas'] + $bblm_submit_tB['cas'];
				$bblm_submit_match['int'] = $bblm_submit_tA['int'] + $bblm_submit_tB['int'];
				$bblm_submit_match['completions'] = $bblm_submit_tA['comp'] + $bblm_submit_tB['comp'];

				if ($bblm_submit_tA['td'] > $bblm_submit_tB['td']) {
					$bblm_submit_tA['result'] = "W";
					$bblm_submit_tB['result'] = "L";
				}
				else if ($bblm_submit_tA['td'] < $bblm_submit_tB['td']) {
					$bblm_submit_tA['result'] = "L";
					$bblm_submit_tB['result'] = "W";
				}
				else {
					$bblm_submit_tA['result'] = "D";
					$bblm_submit_tB['result'] = "D";
				}

				//WP Page Submission
				$my_post = array(
					'post_title' => $bblm_submit_match['title'],
					'post_content' => $bblm_submit_match['content'],
					'post_type' => 'bblm_match',
					'post_status' => 'publish',
					'comment_status' => 'closed',
					'ping_status' => 'closed',
					'post_parent' => $bblm_submit_match['parent']
				);

				//Calculate change in FF
				$bblm_ff_options = array('0','-1','+1');
				$bblm_submit_tA['ff'] = $bblm_ff_options[$bblm_submit_tA['ff']];
				$bblm_submit_tB['ff'] = $bblm_ff_options[$bblm_submit_tB['ff']];

				//Databse submission
				if ( $bblm_submission = wp_insert_post( $my_post ) ) {
					add_post_meta($bblm_submission, '_wp_page_template', BBLM_TEMPLATE_PATH . 'single-bblm_match.php');

					//Insert the match into the database table
					$matchaddsql = 'INSERT INTO `'.$wpdb->prefix.'match` (`m_id`, `c_id`, `div_id`, `m_date`, `m_gate`, `m_teamA`, `m_teamB`, `m_teamAtd`, `m_teamBtd`, `m_teamAcas`, `m_teamBcas`, `m_tottd`, `m_totcas`, `m_totint`, `m_totcomp`, `weather_id`, `weather_id2`, `m_trivia`, `m_complete`, `stad_id`, `m_counts`, `WPID`) VALUES (\'\', \''.$bblm_submit_match['comp'].'\', \''.$bblm_submit_match['div'].'\', \''.$bblm_submit_match['date'].'\', \''.$bblm_submit_match['gate'].'\', \''.$bblm_submit_tA['id'].'\', \''.$bblm_submit_tB['id'].'\', \''.$bblm_submit_tA['td'].'\', \''.$bblm_submit_tB['td'].'\', \''.$bblm_submit_tA['cas'].'\', \''.$bblm_submit_tB['cas'].'\', \''.$bblm_submit_match['td'].'\', \''.$bblm_submit_match['cas'].'\', \''.$bblm_submit_match['int'].'\', \''.$bblm_submit_match['completions'].'\', \''.$bblm_submit_match['weather1'].'\', \''.$bblm_submit_match['weather2'].'\', \''.$bblm_submit_match['trivia'].'\', \'0\', \''.$bblm_submit_match['stad'].'\', \''.$bblm_submit_match['compcounts'].'\', \''.$bblm_submission.'\')';

					$wpdb->query( $matchaddsql );
					$bblm_match_number = $wpdb->insert_id;

					//Insert a record of the match for each team
					$matchteamsql = 'INSERT INTO `'.$wpdb->prefix.'match_team` (`m_id`, `t_id`, `mt_td`, `mt_cas`, `mt_int`, `mt_comp`, `mt_winnings`, `mt_att`, `mt_ff`, `mt_result`, `mt_tv`, `mt_comment`) VALUES (\''.$bblm_submission.'\', \''.$bblm_submit_tA['id'].'\', \''.$bblm_submit_tA['td'].'\', \''.$bblm_submit_tA['cas'].'\', \''.$bblm_submit_tA['int'].'\', \''.$bblm_submit_tA['comp'].'\', \''.$bblm_submit_tA['att'].'\', \''.$bblm_submit_tA['winning'].'\', \''.$bblm_submit_tA['ff'].'\', \''.$bblm_submit_tA['result'].'\', \''.$bblm_submit_tA['tv'].'\', \''.$bblm_submit_tA['comment'].'\'), (\''.$bblm_submission.'\', \''.$bblm_submit_tB['id'].'\', \''.$bblm_submit_tB['td'].'\', \''.$bblm_submit_tB['cas'].'\', \''.$bblm_submit_tB['int'].'\', \''.$bblm_submit_tB['comp'].'\', \''.$bblm_submit_tB['att'].'\', \''.$bblm_submit_tB['winning'].'\', \''.$bblm_submit_tB['ff'].'\', \''.$bblm_submit_tB['result'].'\', \''.$bblm_submit_tB['tv'].'\', \''.$bblm_submit_tB['comment'].'\')';

					$wpdb->query( $matchteamsql );

					// Update teams (FF and bank)

					//Calculate values of the Fan Factor changes
					$teamA_ffinc = $bblm_submit_tA['ff']*10000;
					$teamB_ffinc = $bblm_submit_tB['ff']*10000;

					///Update the database
					$teamAupdatesql = 'UPDATE `'.$wpdb->prefix.'team` SET `t_ff` = `t_ff`+\''.$bblm_submit_tA['ff'].'\', `t_bank` = `t_bank`+\''.$bblm_submit_tA['winning'].'\', `t_tv` = `t_tv`+\''.$teamA_ffinc.'\' WHERE `t_id` = \''.$bblm_submit_tA['id'].'\' LIMIT 1';
					$wpdb->query( $teamAupdatesql );
					$teamBupdatesql = 'UPDATE `'.$wpdb->prefix.'team` SET `t_ff` = `t_ff`+\''.$bblm_submit_tB['ff'].'\', `t_bank` = `t_bank`+\''.$bblm_submit_tB['winning'].'\', `t_tv` = `t_tv`+\''.$teamB_ffinc.'\' WHERE `t_id` = \''.$bblm_submit_tB['id'].'\' LIMIT 1';
					$wpdb->query( $teamBupdatesql );

					//Mark Fixture and brackets as complete
					if ( $bblm_submit_match['fixture'] > 0 ) {

						//Mark the fixture as complete
						BBLM_Admin_CPT_Competition::update_fixture_complete( $bblm_submit_match['fixture'] );

						//now we check to see if it was part of a tournament
						$checkbracketssql = 'SELECT cb_id FROM '.$wpdb->prefix.'comp_brackets WHERE f_id = '.$bblm_submit_match['fixture'];
						$cb_id = $wpdb->get_var($checkbracketssql);

						if ( !empty( $cb_id ) ) {
							$updatebracketsql = 'UPDATE `'.$wpdb->prefix.'comp_brackets` SET `m_id` = \''.$bblm_submission.'\', `f_id` = \'0\', `cb_text` = \'' . bblm_get_team_link( $bblm_submit_tA['TWPID'] )  . ' <strong>' . $bblm_submit_tA['td'] . '</strong><br />' . bblm_get_team_link( $bblm_submit_tB['TWPID'] ) . ' <strong>' . $bblm_submit_tB['td'] . '</strong>\' WHERE `cb_id` = \''.$cb_id.'\' LIMIT 1';
							$wpdb->query( $updatebracketsql );
						}
					} //end of if this match was based off a fixture

					//Update the teams_comp records (including points)

					//If cross devisional download the match results from match_team for the teams original devision
					if ( 13 == $bblm_submit_match['div'] ) {
						$tAdiv = $bblm_submit_tA['div'];
						$tBdiv = $bblm_submit_tB['div'];
					}
					else {
						//If not cross devisional than use the one submitted
						$tAdiv = $bblm_submit_match['div'];
						$tBdiv = $bblm_submit_match['div'];
					}

					BBLM_Admin_CPT_Competition::update_team_standings( $bblm_submit_tA['id'], $bblm_submit_match['comp'], $tAdiv );
					BBLM_Admin_CPT_Competition::update_team_standings( $bblm_submit_tB['id'], $bblm_submit_match['comp'], $tBdiv );

					//If we get to this point we have added a match to the database!
					$sucess = TRUE;
					do_action( 'bblm_post_submission' );

				} //end of if WP post insertion is successful
?>
				<div id="updated" class="notice notice-success">
					<p>
<?php
				if ( $sucess ) {
					print("Match has been recorded. <a href=\"".get_permalink($bblm_submission)."\" title=\"View the match page\">View page</a> or enter the <a href=\"".home_url()."/wp-admin/admin.php?page=bblm_add_match_player\" title=\"Enter the player actions for the match\">player actions for the match</a>");
				}
				else {
					print("Something went wrong! Please try again.");
				}
?>
					</p>
				</div>


<?php

			} //end of isset( $_POST[ 'bblm_match_add' ] )

			if ( ( isset( $_POST[ 'bblm_matchcomp_select' ] ) ) && ( wp_verify_nonce( $_POST[ 'bblm_match_method_nonce' ], basename(__FILE__) ) ) ) {

				//a method of entry has been selected, so the match entry can start
				$this->enter_match_details();

			}
			else {
				//Nothing has been submitted, so display the initial page
?>
				<form name="bblm_addmatch" method="post" id="post">

					<h2 class="title"><?php echo __( 'Step 1: Select your method', 'bblm'); ?></h2>

					<p><?php echo __( 'There are two ways of recording details of a match; You can select the details from a fixture that has been entered, or select the Competition and Division that the match took place in:', 'bblm' ); ?></p>

					<h3><?php echo __( 'Select a Fixture', 'bblm' ); ?></h3>
					<p><input type="radio" value="F" name="bblm_mtype" checked> <?php echo __( 'Use a Fixture - Please select a fixture from the below list:', 'bblm'); ?></p>
					<label for="bblm_fid"><?php echo __( 'Fixture(s):', 'bblm' ); ?></label>
					<select name="bblm_fid" id="bblm_fid">
<?php
					$fixturesql = 'SELECT F.f_id, UNIX_TIMESTAMP(F.f_date) AS mdate, F.c_id, D.div_name, T.WPID AS TA, R.WPID AS TB FROM '.$wpdb->prefix.'fixture F, '.$wpdb->prefix.'division D, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'team R WHERE F.f_teamA = T.t_id AND F.f_teamB = R.t_id AND F.div_id = D.div_id AND F.f_complete = 0 ORDER BY mdate ASC, F.c_id DESC, F.div_id DESC';
					if ( $fixtures = $wpdb->get_results( $fixturesql ) ) {
						foreach ( $fixtures as $fd ) {
							echo '<option value="' . $fd->f_id . '">' . bblm_get_team_name( $fd->TA ) . ' vs ' .bblm_get_team_name( $fd->TB ) . ' (' . bblm_get_competition_name( $fd->c_id ) . ' / ' . $fd->div_name . ' - ' . date("d.m.y", $fd->mdate) . ')</option>';
						}
					}
					else {
						echo '<option value="x">' . __( 'There are currently no scheduled fixtures. Please create one, or use the option for creating from scratch.', 'bblm' ) . '</option>';
					}
?>
					</select>


					<h3><?php echo __( 'Create from scratch', 'bblm' ); ?></h3>
					<p><input type="radio" value="M" name="bblm_mtype"> <?php echo __( 'Create from scratch - Select a Competition and Division below:', 'bblm' ); ?></p>

					<label for="bblm_mcomp" class="selectit"><?php echo __( 'Competition:', 'bblm'); ?></label>
					<select name="bblm_mcomp" id="bblm_mcomp">
<?php
					$oposts = get_posts(
						array(
							'post_type' => 'bblm_comp',
							'numberposts' => -1,
							'orderby' => 'ID',
							'order' => 'DESC'
						)
					);
					if( ! $oposts ) return;
					foreach( $oposts as $o ) {
						echo '<option value="' . $o->ID . '">' . bblm_get_competition_name( $o->ID ) . '</option>';
					}
?>
					</select>

					<label for="bblm_mdiv"><?php echo __( 'Division:', 'bblm'); ?></label>
					<select name="bblm_mdiv" id="bblm_mdiv">
<?php
					$divsql = 'SELECT div_id, div_name FROM '.$wpdb->prefix.'division ORDER BY div_id';
					if ( $divs = $wpdb->get_results( $divsql ) ) {
						foreach ( $divs as $div ) {
							echo '<option value="' . $div->div_id . '">' . $div->div_name . '</option>';
						}
					}
?>
					</select>
<?php
					wp_nonce_field( basename( __FILE__ ), 'bblm_match_method_nonce' );
?>
					<p class="submit">
						<input type="submit" name="bblm_matchcomp_select" value="Continue" title="Continue with selection" class="button-primary"/>
					</p>
				</form>
<?php
			}//end of else
?>
			</div>
<?php
	} //end of add_match_page

 /*
	* Displays the main match entry section
	*/
	public function enter_match_details() {
		global $wpdb;
?>
		<h2 class="title"><?php echo __( 'Step 2: Enter Match details', 'bblm'); ?></h2>

		<form name="bblm_editcompteam" method="post" id="post">

			<div class="notice"><p><strong><?php echo __( 'Remember to add Journeymen and Mercenaries to the teams BEFORE you complete the below so the Team Values are accurate!', 'bblm' ); ?></strong></p></div>
<?php
			$f_id = $_POST['bblm_fid'];

			if ( "F" == $_POST['bblm_mtype'] ) {

				//We are using a fixture to base the match record off
				$fixturedetailsql = 'SELECT F.f_id, UNIX_TIMESTAMP(F.f_date) AS mdate, F.c_id, D.div_name, D.div_id, T.WPID AS TA, R.WPID AS TB, T.t_tv AS TAtv, R.t_tv AS TBtv, T.stad_id, F.f_teamA, F.f_teamB FROM '.$wpdb->prefix.'fixture F, '.$wpdb->prefix.'division D, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'team R WHERE F.f_teamA = T.t_id AND F.f_teamB = R.t_id AND F.div_id = D.div_id AND F.f_complete = 0 AND F.f_id = '.$f_id;

 				//check the fixture exists
				if ( $fixturedetail = $wpdb->get_results( $fixturedetailsql ) ) {
					foreach ( $fixturedetail as $fd ) {

						$comp_name = bblm_get_competition_name( $fd->c_id );
						$div_name = $fd->div_name;
						$comp_id = $fd->c_id;
						$div_id = $fd->div_id;

?>
					<ul>
						<li><strong><?php echo __( 'Competition', 'bblm'); ?></strong>: <?php echo $comp_name; ?></li>
						<li><strong><?php echo __( 'Division', 'bblm'); ?></strong>: <?php echo $div_name; ?></li>
					</ul>

					<table>
						<thead>
							<tr>
								<th>&nbsp;</th>
								<th><?php echo __( 'Home', 'bblm'); ?></th>
								<th>&nbsp;</th>
								<th><?php echo __( 'Away', 'bblm'); ?></th>
								<th>&nbsp;</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><?php echo __( 'Teams', 'bblm'); ?></td>
								<td><strong><?php echo bblm_get_team_name( $fd->TA ); ?></strong><input name="bblm_teama" type="hidden" value="<?php echo $fd->f_teamA; ?>"></td>
								<td>vs</td>
								<td><strong><?php echo bblm_get_team_name( $fd->TB ); ?></strong><input name="bblm_teamb" type="hidden" value="<?php echo $fd->f_teamB; ?>"></td>
								<td>&nbsp;</td>
							</tr>
							<tr>
								<td><?php echo __( 'Team Value:', 'bblm' ); ?></td>
								<td><input name="tAtr" type="text" size="7" maxlength="7" value="<?php echo $fd->TAtv; ?>"></td>
								<td>vs</td>
								<td><input name="tBtr" type="text" size="7" maxlength="7" value="<?php echo $fd->TBtv; ?>"></td>
								<td class="comment"><?php echo __( 'The team value <em>before</em> the game.', 'bblm'); ?></td>
							</tr>
							<tr>
								<td><?php echo __( 'Date', 'bblm' ); ?></td>
								<td colspan="3"><input name="mdate" type="text" size="12" maxlength="10" value="<?php echo date( 'Y-m-d', $fd->mdate); ?>" class="custom_date"></td>
								<td class="comment"><?php echo __( 'This is the scheduled date, feel free to change it.', 'bblm'); ?></td>
							</tr>
							<tr>
								<td><?php echo __( 'Location', 'bblm' ); ?></td>
								<td colspan="3">
									<select name="mstad" id="mstad">
										<?php
										$oposts = get_posts(
											array(
												'post_type' => 'bblm_stadium',
												'numberposts' => -1,
												'orderby' => 'post_title',
												'order' => 'ASC'
											)
										);
										if( ! $oposts ) return;
										foreach( $oposts as $o ) {
											echo '<option value="' . $o->ID . '">' . bblm_get_stadium_name( $o->ID ) . '</option>';
										}
										?>
									</select>
								</td>
								<td class="comment"><?php echo __( 'The Stadium where this game took place.', 'bblm'); ?></td>
							</tr>
<?php
					}// end of foreach

				} //end of if ficture exists
				else {
					//something has gone wrong
					echo '<p>' . __( 'Something has gone wrong, please check the fixture still exists', 'bblm') . '</p>';
				}

				$is_fixture = 1;

			} //end of if ( "F" == $_POST['bblm_mtype'] ) {
			else {

				//Createing a match record from scratch
				$comp_id = (int) $_POST['bblm_mcomp'];

				$divnamesql = "SELECT div_name, div_id FROM ".$wpdb->prefix."division WHERE div_id = " . (int) $_POST['bblm_mdiv'];

				if ( $divname = $wpdb->get_results( $divnamesql ) ) {
					foreach ( $divname as $dn ) {
						$div_name = $dn->div_name;
						$div_id = $dn->div_id;
					}
				}

?>
					<ul>
						<li><strong><?php echo __( 'Competition', 'bblm'); ?></strong>: <?php echo bblm_get_competition_name( $comp_id ); ?></li>
						<li><strong><?php echo __( 'Division', 'bblm'); ?></strong>: <?php echo $div_name; ?></li>
					</ul>

					<table>
						<thead>
							<tr>
								<th>&nbsp;</th>
								<th><?php echo __( 'Home', 'bblm'); ?></th>
								<th>&nbsp;</th>
								<th><?php echo __( 'Away', 'bblm'); ?></th>
								<th>&nbsp;</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><?php echo __( 'Teams', 'bblm'); ?></td>
<?php
								if (13 == $_POST['bblm_mdiv']) {
									//Cross Division has been selected, All the teams in the compeition are slected
									$existingteamssql = "SELECT T.t_name, T.t_id FROM ".$wpdb->prefix."team T, ".$wpdb->prefix."team_comp C WHERE T.t_id = C.t_id AND C.c_id = " . (int) $_POST['bblm_mcomp'];
								}
								else {
									//Just select the temas in this division
									$existingteamssql = "SELECT T.WPID AS TWPID, T.t_id FROM ".$wpdb->prefix."team T, ".$wpdb->prefix."team_comp C WHERE T.t_id = C.t_id AND C.c_id = " . (int) $_POST['bblm_mcomp'] . " AND C.div_id = " . (int) $_POST['bblm_mdiv'];
								}

								if ( $existingteam = $wpdb->get_results( $existingteamssql ) ) {

									$teamlist = "";
									foreach ( $existingteam as $et ) {
										$teamlist .= '<option value="' . $et->t_id . '">' . bblm_get_team_name( $et->TWPID ) . '</option>';
									}
?>
									<td><select name="bblm_teama" id="bblm_teama"><?php echo $teamlist; ?></select></td>
									<td>vs</td>
									<td><select name="bblm_teamb" id="bblm_teamb"><?php echo $teamlist; ?></select></td>
									<td>&nbsp;</td>
<?php
								}
								else {
?>
								<td colspan="3"><?php echo __( 'No Teams counld be found in this Competition and Division combination - please double check', 'bblm' ); ?></td>
<?php
								}
?>
							</tr>
							<tr>
								<td><?php echo __( 'Team Value:', 'bblm' ); ?></td>
								<td><input name="tAtr" type="text" size="7" maxlength="7" value="1000000"></td>
								<td>vs</td>
								<td><input name="tBtr" type="text" size="7" maxlength="7" value="1000000"></td>
								<td class="comment"><?php echo __( 'The team value <em>before</em> the game.', 'bblm'); ?></td>
							</tr>
							<tr>
								<td><?php echo __( 'Date', 'bblm' ); ?></td>
								<td colspan="3"><input name="mdate" type="text" size="12" maxlength="10" value="<?php echo date( 'Y-m-d', strtotime( 'last thursday' ) ); ?>" class="custom_date"></td>
								<td class="comment"><?php echo __( 'The date the game took place.', 'bblm'); ?></td>
							</tr>
							<tr>
								<td><?php echo __( 'Location', 'bblm' ); ?></td>
								<td colspan="3">
									<select name="mstad" id="mstad">
										<?php
										$oposts = get_posts(
											array(
												'post_type' => 'bblm_stadium',
												'numberposts' => -1,
												'orderby' => 'post_title',
												'order' => 'ASC'
											)
										);
										if( ! $oposts ) return;
										foreach( $oposts as $o ) {
											echo '<option value="' . $o->ID . '">' . bblm_get_stadium_name( $o->ID ) . '</option>';
										}
										?>
									</select>
								</td>
								<td class="comment"><?php echo __( 'The Stadium where this game took place.', 'bblm'); ?></td>
							</tr>
<?php
			} //end of else - creating a match from scratch

			//All of the below is displayed no matter the option selected on the previous screen
			if ( 13 == $_POST[ 'bblm_mdiv' ] || 13 == $div_id ) {
				//if this is a cross-divisional game then the input is ammneded to the actual divisions the teams are in!
				?>
						 <tr>
							 <td><strong><?php echo __( 'Original Division', 'bblm' ); ?></strong></td>
							 <td>
								 <select name="tAcddiv" id="tAcddiv">
									 <?php
									 $divsql = 'SELECT div_id, div_name FROM '.$wpdb->prefix.'division ORDER BY div_id';
									 if ($divs = $wpdb->get_results($divsql)) {
										 foreach ($divs as $div) {
											 print("<option value=\"".$div->div_id."\">".$div->div_name."</option>\n");
										 }
									 }
									 ?>
								 </select>
							 </td>
							 <td>vs</td>
							 <td>
								 <select name="tBcddiv" id="tBcddiv">
									 <?php
									 $divsql = 'SELECT div_id, div_name FROM '.$wpdb->prefix.'division ORDER BY div_id';
									 if ($divs = $wpdb->get_results($divsql)) {
										 foreach ($divs as $div) {
											 print("<option value=\"".$div->div_id."\">".$div->div_name."</option>\n");
										 }
									 }
									 ?>
								 </select>
							 </td>
							 <td class="comment"><?php echo __( 'The Divisions the teams actually belong in!', 'bblm' ); ?></td>
						 </tr>
<?php
} //end of if division 13 / cross divisional
?>
							<tr>
								<td><?php echo __( 'Score', 'bblm' ); ?></td>
								<td><input name="tAtd" type="text" size="3" maxlength="2" value="0"></td>
								<td>vs</td>
								<td><input name="tBtd" type="text" size="3" maxlength="2" value="0"></td>
								<td class="comment"><?php echo __( 'The final score', 'bblm' ); ?></td>
							</tr>
							<tr>
								<td><?php echo __( 'Casualities', 'bblm' ); ?></td>
								<td><input name="tAcas" type="text" size="3" maxlength="2" value="0"></td>
								<td>vs</td>
								<td><input name="tBcas" type="text" size="3" maxlength="2" value="0"></td>
								<td class="comment"><?php echo __( 'Casualities caused by each team (that count for SPP)', 'bblm' ); ?></td>
							</tr>
							<tr>
								<td><?php echo __( 'Interceptions', 'bblm' ); ?></td>
								<td><input name="tAint" type="text" size="3" maxlength="2" value="0"></td>
								<td>vs</td>
								<td><input name="tBint" type="text" size="3" maxlength="2" value="0"></td>
								<td class="comment"><?php echo __( 'Number of Incerceptions made', 'bblm' ); ?></td>
							</tr>
							<tr>
								<td><?php echo __( 'Completions', 'bblm' ); ?></td>
								<td><input name="tAcomp" type="text" size="3" maxlength="2" value="0"></td>
								<td>vs</td>
								<td><input name="tBcomp" type="text" size="3" maxlength="2" value="0"></td>
								<td class="comment"><?php echo __( 'Number of Completions made', 'bblm' ); ?></td>
							</tr>
							<tr>
								<td><?php echo __( 'Attendance', 'bblm' ); ?></td>
								<td><input name="tAatt" id="tAatt" type="text" size="6" maxlength="6" value="20000" onChange="BBLM_UpdateGate()"></td>
								<td>vs</td>
								<td><input name="tBatt" id="tBatt" type="text" size="6" maxlength="6" value="20000" onChange="BBLM_UpdateGate()"></td>
								<td class="comment"><?php echo __( 'Number of fans attending for each team', 'bblm' ); ?></td>
							</tr>
							<tr>
								<td><?php echo __( 'Gate', 'bblm' ); ?></td>
								<td colspan="3"><input name="gate" id="gate" type="text" size="6" maxlength="6" value="4000"></td>
								<td class="comment"><?php echo __( 'The total number of fans', 'bblm' ); ?></td>
							<tr>
								<td><?php echo __( 'Winnings', 'bblm' ); ?></td>
								<td><input name="tAwin" type="text" size="6" maxlength="6" value="10000"> GP</td>
								<td>vs</td>
								<td><input name="tBwin" type="text" size="6" maxlength="6" value="10000"> GP</td>
								<td class="comment"><?php echo __( 'Include extras for winning, tornament finals etc.', 'bblm' ); ?></td>
							</tr>
							<tr>
								<td><?php echo __( 'Match Trivia', 'bblm' ); ?></td>
								<td colspan="3"><textarea name="matchtrivia" cols="80" rows="6" placeholder="Points of note, notable injuries, deaths, or debuts, etc."></textarea></td>
								<td class="comment">&nbsp;</td>
							</tr>
							<tr>
								<td><?php echo __( 'Weather', 'bblm' ); ?></td>
								<td>
									<select id="mweather" name="mweather">
										<option value="1"><?php echo __( 'Nice', 'bblm' ); ?></option>
										<option value="2"><?php echo __( 'Very Sunny', 'bblm' ); ?></option>
										<option value="3"><?php echo __( 'Blizzard', 'bblm' ); ?></option>
										<option value="4"><?php echo __( 'Pouring Rain', 'bblm' ); ?></option>
										<option value="5"><?php echo __( 'Sweltering Heat', 'bblm' ); ?></option>
									</select>
								</td>
								<td>&nbsp;</td>
								<td>
									<select id="mweather2" name="mweather2">
										<option value="1"><?php echo __( 'Nice', 'bblm' ); ?></option>
										<option value="2"><?php echo __( 'Very Sunny', 'bblm' ); ?></option>
										<option value="3"><?php echo __( 'Blizzard', 'bblm' ); ?></option>
										<option value="4"><?php echo __( 'Pouring Rain', 'bblm' ); ?></option>
										<option value="5"><?php echo __( 'Sweltering Heat', 'bblm' ); ?></option>
									</select>
								</td>
								<td class="comment"><?php echo __( 'The weather during the match', 'bblm' ); ?></td>
							</tr>
							<tr>
								<td><?php echo __( 'Change in Fan Factor', 'bblm' ); ?></td>
								<td>
									<select id="tAff" name="tAff">
										<option value="0" selected=selected><?php echo __( 'No change', 'bblm' ); ?></option>
										<option value="1"><?php echo __( 'Minus One (-1)', 'bblm' ); ?></option>
										<option value="2"><?php echo __( 'Plus One (+1)', 'bblm' ); ?></option>
									</select>
								</td>
								<td>vs</td>
								<td>
									<select id="tBff" name="tBff">
										<option value="0" selected=selected><?php echo __( 'No change', 'bblm' ); ?></option>
										<option value="1"><?php echo __( 'Minus One (-1)', 'bblm' ); ?></option>
										<option value="2"><?php echo __( 'Plus One (+1)', 'bblm' ); ?></option>
									</select>
								</td>
								<td class="comment"><?php echo __( 'The change in FF (if any).', 'bblm' ); ?></td>
							</tr>
							<tr>
								<td><?php echo __( 'Coach Comments', 'bblm' ); ?></td>
								<td><textarea name="tAnotes" cols="40" rows="8">No comment</textarea></td>
								<td>vs</td>
								<td><textarea name="tBnotes" cols="40" rows="8">No Comment</textarea></td>
								<td class="comment"><?php echo __( 'Any team specific comments, coach comments etc', 'bblm' ); ?></td>
							</tr>
						</tbody>
					</table>

					<input type="hidden" name="bblm_comp" size="20" value="<?php echo $comp_id; ?>" />
					<input type="hidden" name="bblm_div" size="10" value="<?php echo $div_id; ?>" />
<?php
					if ( $is_fixture ) {
						echo '<input type="hidden" name="bblm_fid" size="10" value="' . $f_id . '" />';
					}
					wp_nonce_field( basename( __FILE__ ), 'bblm_match_submission' );
?>
					<p class="submit">
						<input type="submit" name="bblm_match_add" value="Submit match details" title="submit match details" class="button-primary"/>
					</p>
				</form>
<?php

	} //end of enter_match_details

} //End of BBLM_Add_Match
