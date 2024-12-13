<?php
/**
 * BBowlLeagueMan Add Team
 *
 * Page used to add a new team to the league
 *
 * @author 		Blacksnotling
 * @category 	Core
 * @package 	BBowlLeagueMan/Pages
 */

//Check the file is not being accessed directly
if (!function_exists('add_action')) die('You cannot run this file directly. Naughty Person');
?>
<div class="wrap">
	<h2>Create a new Team</h2>

<?php
if(isset($_POST['bblm_team_submit'])) {

	//Determine the parent page
	$options = get_option('bblm_config');
	$bblm_page_parent = htmlspecialchars($options['page_team'], ENT_QUOTES);

	//Determine if a new Owner was created or an existing one used
	$bblm_tuser = "";
	if ( empty( $_POST['bblm_tusernew'] ) ) {

		//If a new coach has not been provided then use the value of the drop down
		$bblm_tuser = $_POST['bblm_tuser'];
	}
	else {
		//If the NEW field is not blank then add a new Owner post (should be moved into the Owner CPT class at some point)
		$post_id = wp_insert_post( array( 'post_title'=>sanitize_text_field( $_POST[ 'bblm_tusernew' ] ), 'post_type'=>'bblm_owner', 'post_content'=>'', 'post_status'=>'publish' ) );
		$bblm_tuser = $post_id;
	}

	//Determine if a new Stadium was created or an existing one used
	$bblm_tstad = "";
	if ( empty( $_POST['bblm_tstadnew'] ) ) {

		//If a new coach has not been provided then use the value of the drop down
		$bblm_tstad = $_POST[ 'bblm_tstad' ];
	}
	else {
		//If the NEW field is not blank then add a new Owner post (should be moved into the Owner CPT class at some point)
		$post_id = wp_insert_post( array( 'post_title'=>sanitize_text_field( $_POST[ 'bblm_tstadnew' ] ), 'post_type'=>'bblm_stadium', 'post_content'=>'', 'post_status'=>'publish' ) );
		$bblm_tstad = $post_id;
		add_post_meta( $bblm_tstad, 'stadium_featured', 'No' );
	}

	//Add the free Dedicated Fan
	$bblm_tff = $_POST['bblm_tff']+1;

	$my_post = array(
		'post_title' => wp_filter_nohtml_kses($_POST['bblm_tname']),
		'post_content' => wp_filter_kses($_POST['bblm_tdesc']),
		'post_type' => 'page',
		'post_status' => 'publish',
		'comment_status' => 'closed',
		'ping_status' => 'closed',
		'post_parent' => $bblm_page_parent
	);
	if ($bblm_submission = wp_insert_post( $my_post )) {
		add_post_meta($bblm_submission, '_wp_page_template', BBLM_TEMPLATE_PATH . 'single-bblm_team.php');
		add_post_meta($bblm_submission, 'team_motto', esc_textarea( $_POST['bblm_tmotto'] ) );

		//Determine permlink for this page
		$bblmpageguid = get_permalink($bblm_submission);

		$bblmdatasql = 'INSERT INTO `'.$wpdb->prefix.'team` (`t_id`, `t_name`, `r_id`, `ID`, `t_hcoach`, `t_ff`, `t_rr`, `t_apoc`, `t_cl`, `t_ac`, `t_bank`, `t_tv`, `t_ctv`, `t_active`, `t_show`, `type_id`, `t_sname`, `stad_id`, `t_img`, `t_roster`, `t_guid`, `WPID`) VALUES (\'\', \''.wp_filter_nohtml_kses($_POST['bblm_tname']).'\', \''.$_POST['bblm_trace'].'\', \''.$bblm_tuser.'\', \''.$_POST['bblm_thcoach'].'\', \''.$bblm_tff.'\', \''.$_POST['bblm_trr'].'\', \''.$_POST['bblm_tapoc'].'\', \''.$_POST['bblm_tcl'].'\', \''.$_POST['bblm_tac'].'\', \''.$_POST['bblm_tbank'].'\', \''.$_POST['bblm_ttv'].'\', \''.$_POST['bblm_ttv'].'\', \'1\', \'1\', \'1\', \''.wp_filter_nohtml_kses($_POST['bblm_sname']).'\', \''.$bblm_tstad.'\', \'\', \''.$_POST['bblm_roster'].'\', \''.$bblmpageguid.'\',  \''.$bblm_submission.'\')';
		$wpdb->query($bblmdatasql);

		$team_id = $wpdb->insert_id;

		$bblmmappingsql = 'INSERT INTO `'.$wpdb->prefix.'bb2wp` (`bb2wp_id`, `tid`, `pid`, `prefix`) VALUES (\'\',\''.$team_id.'\', \''.$bblm_submission.'\', \'t_\')';
		$wpdb->query($bblmmappingsql);


		//Add a new Term to the database
		wp_insert_term(
			wp_filter_nohtml_kses($_POST['bblm_tname']), // the term
			'post_teams' // the taxonomy
		);

		//Check to see if  roster needs generating, if it does then insert an additionl page into the database
		$roster_added = 0;
		if ($_POST['bblm_roster']) {
			$my_post = array(
				'post_title' => 'Roster',
				'post_content' => '',
				'post_type' => 'page',
				'post_status' => 'publish',
				'comment_status' => 'closed',
				'ping_status' => 'closed',
				'post_parent' => $bblm_submission
			);
			if ($bblm_submission = wp_insert_post( $my_post )) {
				add_post_meta($bblm_submission, '_wp_page_template', BBLM_TEMPLATE_PATH . 'single-bblm_roster.php');

				$bblmmappingsql = 'INSERT INTO `'.$wpdb->prefix.'bb2wp` (`bb2wp_id`, `tid`, `pid`, `prefix`) VALUES (\'\',\''.$team_id.'\', \''.$bblm_submission.'\', \'roster\')';
				$wpdb->query($bblmmappingsql);

				//correct the roster post title
				$my_post = array();
				$my_post['ID'] = $bblm_submission;
				$my_post['post_title'] = 'Roster - '.wp_filter_nohtml_kses($_POST['bblm_tname']);

				// Update the post into the database
				wp_update_post( $my_post );

				$roster_added = 1;
			}
		}

		$success = 1;
		$addattempt = 1;
		do_action( 'bblm_post_submission' );

	} //end of if post insertion was successful


?>
	<div id="updated" class="updated fade">
		<p>
	<?php
	if ($success) {
		print("Team has been created. <a href=\"".$bblmpageguid."\" title=\"View the new Team\">View page</a>.");
		if ($roster_added) {
			print(" A roster has also been added. <a href=\"".get_permalink($bblm_submission)."\" title=\"View the new Teams roster\">View Roster</a>");
		}
		print("</p>\n<p>You can now <a href=\"".site_url()."/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.add.player.php&action=add&item=none&id=".$team_id."\" title=\"Add some players to this team\">add players to this team</a> or <a href=\"".site_url()."/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.add.team.php\" title=\"Add another new team\">add another new team</a>.</p>");
		print("<p>You can also <a href=\"");
		echo site_url();
		print("/wp-admin/admin.php?page=bblm_player_addbulk\" title=\"Add a new payer to the team\">Add Players in Bulk to this team</a></p>\n");
		echo '<p>If you added a new Stadium, <a href="' . site_url() . '/wp-admin/post.php?post=' . $bblm_tstad . '&action=edit">then add a description to the stadium.</a>';
	}
	else {
		print("Something went wrong! Please try again.");
	}


?>
		</p>
	</div>
<?php
//end of submit if
}
else if(isset($_POST['bblm_race_select'])) {
	  ///////////////////////////////
	 // Begin Output of main form //
	///////////////////////////////
?>

	<form name="bblm_addteam" method="post" id="post">

	<table class="form-table">
	<tr valign="top">
		<th scope="row" valign="top"><label for="bblm_tname">Team Name</label></th>
		<td><input type="text" name="bblm_tname" size="50" value="" id="bblm_tname" maxlength="50" class="large-text"/></td>
	</tr>
	<tr valign="top">
		<th scope="row"><label for="bblm_tdesc">Bio</label></th>
		<td><p><textarea rows="10" cols="50" name="bblm_tdesc" id="bblm_tdesc" class="large-text"></textarea></p></td>
	</tr>
	<tr valign="top">
		<th scope="row"><label for="bblm_tdesc"><?php echo __( 'Team Motto','bblm');?></label></th>
		<td><p><textarea rows="1" cols="50" name="bblm_tmotto" id="bblm_tmotto" class="large-text"></textarea></p></td>
	</tr>
	<tr valign="top">
		<th scope="row" valign="top"><label for="bblm_sname">Short Name</label></th>
		<td><input type="text" name="bblm_sname" size="3" value="" id="bblm_sname" maxlength="5" class="small-text"/><br />
		This will be displayed on various reports and pages.</td>
	</tr>
	<tr valign="top">
		<th scope="row" valign="top"><label for="bblm_tuser"><?php echo __( 'Owner', 'bblm' ); ?></label></th>
		<td><select name="bblm_tuser" id="bblm_tuser">
<?php
		//Grabs a list of 'posts' from the Owners CPT
		$oposts = get_posts(
			array(
				'post_type' => 'bblm_owner',
				'numberposts' => -1,
				'orderby' => 'post_title',
				'order' => 'ASC'
			)
		);
		if( ! $oposts ) return;
		foreach( $oposts as $o ) {
			echo '<option value="' . $o->ID . '">' . bblm_get_owner_name( $o->ID ) . '</option>';
		}

?>
		</select>
		<label for="bblm_tusernew"><?php echo __( ' OR Add a new one:', 'bblm' ); ?></label>
		<input type="text" name="bblm_tusernew" id="bblm_tusernew" size="25" value="" maxlength="25" class="large-text" placeholder="New Owner - leave this message (I.E blank) to ignore"/></td>
	</tr>
	<tr valign="top">
		<th scope="row" valign="top"><label for="bblm_thcoach">Head Coach</label></th>
		<td><input type="text" name="bblm_thcoach" size="25" value="Unkown" maxlength="50" class="large-text"/></td>
	</tr>
	<tr valign="top">
		<th scope="row" valign="top"><label for="bblm_tstad"><?php echo __( 'Home Stadium', 'bblm' ); ?></label></th>
		<td><select name="bblm_tstad" id="bblm_tstad">
			<?php
					//Grabs a list of 'posts' from the Stadiums CPT
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

			?></select>
			<label for="bblm_tstadnew"><?php echo __( ' OR Add a new one:', 'bblm' ); ?></label>
			<input type="text" name="bblm_tstadnew" id="bblm_tstadnew" size="25" value="" maxlength="25" class="large-text" placeholder="New Stadium - leave this message (I.E blank) to ignore"/></td>
	</tr>
	<tr valign="top">
		<th scope="row" valign="top"><label for="bblm_roster">Generate Roster?</label></th>
		<td><select name="bblm_roster" id="bblm_roster">
			<option value="1">Yes</option>
			<option value="0">No</option>
		</select></td>
	</tr>

	</table


	<h3>Initial Purchases</h3>

<script type="text/javascript">
function UpdateBankTv() {
	var tot_rr = document.getElementById('bblm_trr').value * document.getElementById('bblm_trrcost').value;

	var tot_ff = document.getElementById('bblm_tff').value * 20000;

	var tot_cl = document.getElementById('bblm_tcl').value * 20000;

	var tot_ac = document.getElementById('bblm_tac').value * 20000;

	var tot_apoc = document.getElementById('bblm_tapoc').value * 80000;

	var tot_tv = tot_rr + tot_cl + tot_ac + tot_apoc;
	var tot_tvb = tot_rr + tot_ff + tot_cl + tot_ac + tot_apoc;
	document.getElementById('bblm_ttv').value = tot_tv;

	var tot_bank = 600000 - tot_tvb;
	document.getElementById('bblm_tbank').value = tot_bank;
}
</script>

<?php
		$rid = (int) $_POST['bblm_rid'];
		$rrcost = BBLM_CPT_Race::get_reroll_cost( $rid );
?>

	<table class="form-table">
	<tr valign="top">
		<th scope="row" valign="top"><label for="bblm_trr">Re-Rolls</label></th>
		<td><input type="text" name="bblm_trr" size="2" value="0" maxlength="1" id="bblm_trr" class="small-text"/><br />
		@ <?php print($rrcost); ?> each</td>
	</tr>
	<tr valign="top">
		<th scope="row" valign="top"><label for="bblm_tff">Dedicated Fans</label></th>
		  <td><input type="text" name="bblm_tff" size="2" value="0" maxlength="2" id="bblm_tff" class="small-text"/><br />
		  @ 20,000 each<br />
		<strong><?php echo __( 'An extra Dedicated Fan will be added for free when the team is created','bblm' ); ?></strong></td>
	</tr>
	<tr valign="top">
		<th scope="row" valign="top"><label for="bblm_tcl">Cheerleaders</label></th>
		  <td><input type="text" name="bblm_tcl" size="2" value="0" maxlength="2" id="bblm_tcl" class="small-text"/><br />
		  @ 20,000 each</td>
	</tr>
	<tr valign="top">
		<th scope="row" valign="top"><label for="bblm_tac">Assistant Coaches</label></th>
		  <td><input type="text" name="bblm_tac" size="2" value="0" maxlength="2" id="bblm_tac" class="small-text"/><br />
		  @ 20,000 each</td>
	</tr>
	<tr valign="top">
		<th scope="row" valign="top"><label for="bblm_tapoc">Apothecary</label></th>
		  <td><input type="text" name="bblm_tapoc" size="1" value="0" maxlength="1" id="bblm_tapoc" class="small-text"/><br />
		  @ 80,000 each</td>
	</tr>
	<tr valign="top">
		<th scope="row" valign="top">&nbsp;</th>
		  <td><input type="button" value="Update Bank + TV" onClick="UpdateBankTv();"/></td>
	</tr>

	<tr valign="top">
		<th scope="row" valign="top"><label for="bblm_tbank">Remaining Bank</label></th>
		  <td><input type="text" name="bblm_tbank" size="7" value="600000" maxlength="7" id="bblm_tbank"/>gp</td>
	</tr>
	<tr valign="top">
		<th scope="row" valign="top"><label for="bblm_ttv">Team Value (initial)</label></th>
		  <td><input type="text" name="bblm_ttv" size="7" value="0" maxlength="7" id="bblm_ttv"/>gp</td>
	</tr>
	</table>

	<input type="hidden" name="bblm_trace" size="3" value="<?php print($rid); ?>"/>
	<input type="hidden" name="bblm_trrcost" id="bblm_trrcost" maxlength="6" value="<?php print($rrcost); ?>"/>

	<p class="submit"><input type="submit" name="bblm_team_submit" value="Create Team" title="Add the Team" class="button-primary"/></p>
</form>

<?php
}//end of elseIF
else {
?>
	<form name="bblm_addposition" method="post" id="post">

	<p>The following page is used to add a new team to the league.</p>
	<p>Before you continue, please ensure that you have created a user ID for the coach and set up a stadium.</p>
	<p>Before you can begin creating the new team, you must first select the Race of the new team:</p>


	<table class="form-table">
	<tr valign="top">
		<th scope="row"><label for="bblm_rid">Race</label></th>
		<td><select name="bblm_rid" id="bblm_rid">
			<?php
					//Grabs a list of 'posts' from the Stadiums CPT
					$oposts = get_posts(
						array(
							'post_type' => 'bblm_race',
							'numberposts' => -1,
							'orderby' => 'post_title',
							'order' => 'ASC'
						)
					);
					if( ! $oposts ) return;
					foreach( $oposts as $o ) {
						echo '<option value="' . $o->ID . '">' . bblm_get_race_name( $o->ID ) . '</option>';
					}

			?></select></td>
	</tr>
	</table>

	<p class="submit"><input type="submit" name="bblm_race_select" value="Select above Race" title="Select the above Race" class="button-primary"/></p>
	</form>
<?php
} //end of else section
?>
</div>
