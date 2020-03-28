<?php
/**
 * BBowlLeagueMan Manage teams in a competition
 *
 * Page used to Manage the teams assigned to a competition
 *
 * @author 		Blacksnotling
 * @category 	Core
 * @package 	BBowlLeagueMan/Pages
 */


//Check the file is not being accessed directly
if (!function_exists('add_action')) die('You cannot run this file directly. Naughty Person');
?>
<div class="wrap">
<?php
	echo '<h2>' . __( 'Manage teams in a Competition', 'bblm' ) . '</h2>';
	echo '<p>' . __( 'Use the following page to assign and remove teams from a competition.', 'bblm' ) . '</p>';

	if ( ( ( isset( $_POST['bblm_teamcomp_add'] ) ) || ( isset( $_POST['bblm_teamcomp_remove'] ) ) ) && wp_verify_nonce( $_POST['competition_teams'], basename(__FILE__) ) ) {
		$bblm_comp = 0;
		$bblm_div = 0;
		$bblm_teams = 0;
		$bblm_tc_id = 0;
		$bblm_comp_counts = 0;

		if ( isset( $_POST['bblm_comp'] ) ) {
			$bblm_comp = intval( $_POST['bblm_comp'] );
		}
		if ( isset( $_POST['bblm_div'] ) ) {
			$bblm_div = intval( $_POST['bblm_div'] ); // used when adding teams
		}
		if ( isset( $_POST['bblm_team'] ) ) {
			$bblm_teams = $_POST['bblm_team'];
		}
		if ( isset( $_POST['bblm_tcid'] ) ) {
			$bblm_tc_id = $_POST['bblm_tcid']; //for teams removed with the delete button
		}

		//Check to see if the competitio counts
		if ( BBLM_CPT_Comp::does_comp_count( $bblm_comp ) ) {
			$bblm_comp_counts = 1;
		}

		if ( isset( $_POST['bblm_teamcomp_add'] ) ) {

			foreach ($bblm_teams as $team) {

				$addsql = 'INSERT INTO `'.$wpdb->prefix.'team_comp` (`tc_id`, `t_id`, `c_id`, `div_id`, `tc_played`, `tc_W`, `tc_L`, `tc_D`, `tc_tdfor`, `tc_tdagst`, `tc_casfor`, `tc_casagst`, `tc_int`, `tc_comp`, `tc_points`, `tc_counts`) VALUES (\'\', \''.intval( $team ).'\', \''.$bblm_comp.'\', \''.$bblm_div.'\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\', \''. $bblm_comp_counts .'\')';

				if ( FALSE !== $wpdb->query( $addsql ) ) {
					$sucess = TRUE;
					do_action( 'bblm_post_submission' );
				}
				else {
					$wpdb->print_error();
				}

			}//end of for each

		} //end of if add submit

		if ( isset( $_POST['bblm_teamcomp_remove'] ) ) {

			foreach ($bblm_tc_id as $tid) {

				$removesql = "DELETE FROM ".$wpdb->prefix."team_comp WHERE tc_id = ". intval( $tid );

				if ( FALSE !== $wpdb->query( $removesql ) ) {
					$sucess = TRUE;
				}
				else {
					$wpdb->print_error();
				}

			}//end of for each

		} //end of remove submit if

?>
		<div id="updated" class="updated fade">
			<p>
<?php
			if ($sucess) {
				echo __( 'Competition Updated', 'bblm' );
			}
			else {
				echo __( 'Something went wrong', 'bblm' );
			}
?>
			</p>
		</div>
<?php

	} // end of add / remove form submitted

	if ( ( isset( $_POST['bblm_teamcomp_select'] ) )	|| ( isset( $_GET['comp'] ) ) || ( (isset( $_POST['bblm_teamcomp_add'] ) ) || ( isset( $_POST['bblm_teamcomp_remove'] ) ) ) ) {

		if ( isset( $_POST['bblm_teamcomp_select'] ) ) {
			$compid = intval( $_POST['bblm_ctcomp'] );
		}
		elseif ( isset( $_GET['comp'] ) ) {
			$compid = intval( $_GET['comp'] );
		}
		elseif ( isset( $_GET['bblm_comp'] ) ) {
			$compid = intval( $_GET['bblm_comp'] );
		}
		else {
			exit( 1 );
		}

		echo '<h3>' . __( 'Review Teams currently participating in ', 'bblm' ) . bblm_get_competition_name( $compid ) . '</h3>';
?>
		<form name="bblm_editcompteam" method="post" id="post">
<?php
		$existingteamssql = "SELECT C.*, D.*, T.WPID FROM ".$wpdb->prefix."team T, ".$wpdb->prefix."team_comp C, ".$wpdb->prefix."division D WHERE D.div_id = C.div_id AND T.t_id = C.t_id AND C.c_id = " . $compid . " ORDER BY C.div_id ASC";
		if ( $extteam = $wpdb->get_results( $existingteamssql ) ) {
		$is_first = 1;
		$current_div = 0;

		foreach( $extteam  as $et ) {

			if ( $et->div_id !== $current_div ) {
				$current_div = $et->div_id;

				if ( 1 !== $is_first ) {
					echo '</tbody>';
					echo '</table>';
				}
				$is_first = 1;
			}
			if ( $is_first ) {
				echo '<h3>' . esc_html( $et->div_name ) . '</h3>';
				echo '<table border="1">';
				echo '<thead>';
				echo '<th>' . __( 'Select', 'bblm' ) . '</th>';
				echo '<th>' . __( 'Team', 'bblm' ) . '</th>';
				echo '<th>' . __( 'Played', 'bblm' ) . '</th>';
				echo '<th>' . __( 'W', 'bblm' ) . '</th>';
				echo '<th>' . __( 'L', 'bblm' ) . '</th>';
				echo '<th>' . __( 'D', 'bblm' ) . '</th>';
				echo '<th>' . __( 'Points', 'bblm' ) . '</th>';
				echo '</thead>';
				echo '<tbody>';
				$is_first = 0;
			}

			echo '<tr>';
			echo '<td><input type="checkbox" value="' . $et->tc_id . '" name="bblm_tcid[]"></td>';
			echo '<td>' . bblm_get_team_name( $et->WPID ) . '</td>';
			echo '<td>' . intval( $et->tc_played ) . '</td>';
			echo '<td>' . intval( $et->tc_W ) . '</td>';
			echo '<td>' . intval( $et->tc_L ) . '</td>';
			echo '<td>' . intval( $et->tc_D ) . '</td>';
			echo '<td>' . intval( $et->tc_points ) . '</td>';
			echo '</tr>';

		}//end of foreach
		echo '</tbody>';
		echo '</table>';
		echo '<p><input type="submit" name="bblm_teamcomp_remove" value="Remove Selected" title="Remove Selected"/></p>';
	}
	else {
		echo '<p>' . __( 'There are currently no teams in this competition', 'bblm' ) . '</p>';
	}
?>
	<input type="hidden" name="bblm_comp" size="3" value="<?php echo $compid; ?>">

<?php
	echo '<h3>' . __( 'Select a Division to add more teams to ', 'bblm' ) . '</h3>';
?>
	<p><label for="bblm_div">Division</label>
		<select name="bblm_div" id="bblm_div">
<?php
		$divsql = 'SELECT div_id, div_name FROM '.$wpdb->prefix.'division ORDER BY div_id';
		if ( $divs = $wpdb->get_results( $divsql ) ) {
			foreach ( $divs as $div ) {
				echo '<option value="' . $div->div_id . '">' . $div->div_name . '</option>';
			}
		}
?>
		</select></p>
<?php
		$teamsql = "SELECT t_id, WPID FROM ".$wpdb->prefix."team WHERE t_active = 1 ORDER BY t_name ASC";
		if ( $teams = $wpdb->get_results( $teamsql ) ) {
			echo '<ul>';
			foreach ( $teams as $team ) {
				echo '<li><input type="checkbox" value="' . $team->t_id . '" name="bblm_team[]"> ' . bblm_get_team_name( $team->WPID ) . '</li>';
			}
			echo '</ul>';
		}
?>

		<p class="submit">
			<input type="submit" name="bblm_teamcomp_add" tabindex="4" value="Add to Competition" title="Add to Competition"/>
		</p>

		<?php wp_nonce_field( basename( __FILE__ ), 'competition_teams' ); ?>

	</form>
<?php

	}
	else {
?>
		<form name="bblm_editcompteam" method="post" id="post">
<?php
		echo '<h3>' . __( 'Select a Competition', 'bblm' ) . '</h3>';
		echo '<p>' . __( 'Select a Competition for which you want to manage the teams', 'bblm' ) . '</p>'
?>
	  <label for="bblm_ctcomp">Competition</label>
	  <select name="bblm_ctcomp" id="bblm_ctcomp">
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

			<p class="submit">
				<input type="submit" name="bblm_teamcomp_select" value="Continue" title="Continue"/>
			</p>
		</form>
<?php
	} //end of else section
?>

</div>
