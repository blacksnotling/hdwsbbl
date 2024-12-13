<?php
/**
 * BBowlLeagueMan Edit Team Admin page
 *
 * Main page used to edit teams.
 *
 * @author 		Blacksnotling
 * @category 	Core
 * @package 	BBowlLeagueMan/Pages
 */

//Check the file is not being accessed directly
if (!function_exists('add_action')) die('You cannot run this file directly. Naughty Person');
?>
<div class="wrap">
	<h2>Manage Teams</h2>
<?php
  /////////////////////////////////
 // Submit changes to Team info //
/////////////////////////////////
if (isset($_POST['bblm_tcap_update'])) {
/*	print("<pre>");
	print_r($_POST);
	print("</pre>");
	print("<hr />");*/

	//remove active flag from current captain
	$deactivecapsql = 'UPDATE `'.$wpdb->prefix.'team_captain` SET `tcap_status` = \'0\' WHERE `t_id` = '.$_POST['bblm_tid'];
	$wpdb->query($deactivecapsql);

	$insertcapsql = 'INSERT INTO `'.$wpdb->prefix.'team_captain` (`tcap_id`, `t_id`, `p_id`, `tcap_status`) VALUES (\'\', \''.$_POST['bblm_tid'].'\', \''.$_POST['bblm_tcap'].'\', \'1\')';
	if (FALSE !== $wpdb->query($insertcapsql)) {
		$sucess = TRUE;
		do_action( 'bblm_post_submission' );
	}
?>
		<div id="updated" class="updated fade">
		<p>
<?php
	if ($sucess) {
?>
			New Team Captain has been set. <a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.edit.team.php" title="Edit Team">Back to the Team edit screen</a>
<?php
	}
	else {
		print("Something went wrong");
	}
?>
	</p>
		</div>
<?php

}//end of updating team Captain

  /////////////////////////////////
 // Submit changes to Team info //
/////////////////////////////////
else if (isset($_POST['bblm_stat_update'])) {

/*	print("<pre>");
	print_r($_POST);
	print("</pre>");
	print("<hr />");*/

	//Generate SQL
	$tinfoupdatesql = 'UPDATE `'.$wpdb->prefix.'team` SET `t_hcoach` = \''.$_POST['bblm_thcoach'].'\', `t_ff` = \''.$_POST['bblm_tff'].'\', `t_rr` = \''.$_POST['bblm_trr'].'\', `t_apoc` = \''.$_POST['bblm_tapoc'].'\', `t_cl` = \''.$_POST['bblm_tcl'].'\', `t_ac` = \''.$_POST['bblm_tac'].'\', `t_bank` = \''.$_POST['bblm_tbank'].'\', `t_tv` = \''.$_POST['bblm_ttv'].'\', `t_active` = \''.$_POST['bblm_tactive'].'\', `t_show` = \''.$_POST['bblm_tshow'].'\', `stad_id` = \''.$_POST['bblm_tstad'].'\' WHERE `t_id` = '.$_POST['bblm_tid'].' LIMIT 1';
	//print("<p>".$tinfoupdatesql."</p>");

	if (FALSE !== $wpdb->query($tinfoupdatesql)) {
		$sucess = TRUE;
		//Update Post Meta (Motto etc)
		update_post_meta( $_POST['bblm_twid'], 'team_motto', sanitize_text_field( $_POST['bblm_tmotto'] ) );
		bblm_update_tv( (int) $_POST['bblm_tid'] );
		do_action( 'bblm_post_submission' );
	}
?>
		<div id="updated" class="updated fade">
		<p>
<?php
	if ($sucess) {
?>
			Team has been updated. <a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.edit.team.php" title="Edit Team">Back to the Team edit screen</a>
<?php
	}
	else {
		print("Something went wrong");
	}
?>
	</p>
		</div>
<?php

}//end of info updating
  ////////////////////
 // $_GET checking //
////////////////////
else if ("edit" == $_GET['action']) {
	if ("stats" == $_GET['item']) {
		  //////////////////////////
		 // Editing team Info //
		//////////////////////////
		$tid = $_GET['id'];
		$teaminfosql = 'SELECT P.post_title, T.* FROM '.$wpdb->prefix.'team T, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE T.t_id = J.tid AND J.prefix = \'t_\' AND J.pid = P.ID AND T.t_id = '.$tid.' LIMIT 1';
		if ($t = $wpdb->get_row($teaminfosql)) {
			$playervaluesql = 'SELECT SUM(P.p_cost_ng) FROM '.$wpdb->prefix.'player P WHERE P.p_status = 1 AND P.t_id = '.$tid;
			$tpvalue = $wpdb->get_var($playervaluesql);
			$rrcost = BBLM_CPT_Race::get_reroll_cost( $t->r_id );
			$meta = get_post_custom( $t->WPID );
			$tmotto = ! isset( $meta['team_motto'][0] ) ? '' : $meta['team_motto'][0];
?>
		<h3>Edit Team Infomation - <?php print($t->post_title); ?></h3>
		<form name="bblm_updatestats" method="post" id="post">
		<p>Below information recorded for this team. make any changes and press the update button to save. For the "team active" and "show in listing" options, 0 = No and 1 = Yes</p>

		<table class="form-table">
			<tr class="form-field form-required">
				<th scope="row" valign="top">Team Name</th>
				<td><?php print("$t->post_title"); ?></td>
			</tr>
			<tr class="form-field form-required">
				<th scope="row" valign="top"><label for="bblm_tmotto"><?php echo __( 'Team Motto','bblm' ); ?></label></th>
				<td><input type="text" name="bblm_tmotto" size="50" value="<?php echo sanitize_text_field( $tmotto ); ?>" maxlength="1000"></td>
			</tr>
			<tr class="form-field form-required">
				<th scope="row" valign="top">Short Name</th>
				<td><?php print("$t->t_sname"); ?><br />
				This becomes important for generating the team logos.</td>
			</tr>
			<tr class="form-field form-required">
				<th scope="row" valign="top">Race</th>
				<td><?php echo bblm_get_race_name( $t->r_id ); ?></td>
			</tr>
			<tr class="form-field form-required">
				<th scope="row" valign="top"><label for="bblm_thcoach">Head Coach</label></th>
				<td><input type="text" name="bblm_thcoach" size="25" tabindex="3" value="<?php print("$t->t_hcoach"); ?>" maxlength="25"></td>
			</tr>
			<tr class="form-field form-required">
				<th scope="row" valign="top"><label for="bblm_tstad">Home Stadium</label></th>
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
			echo '<option value="' . $o->ID . '"';
			if ( $o->ID == $t->stad_id ) {
				echo ' selected="selected"';
			}
			echo '>' . bblm_get_stadium_name( $o->ID ) . '</option>';
		}
?>
				</select></td>
			</tr>
			<tr class="form-field form-required">
				<th scope="row" valign="top"><label for="bblm_trr">Re-Rolls</label></th>
				<td><input type="text" name="bblm_trr" size="2" tabindex="4" value="<?php print("$t->t_rr"); ?>" maxlength="1" id="bblm_trr"><br />
				@ <?php echo number_format( $rrcost ); ?> GP each - remember that they cost double when bought during a season</td>
			</tr>
			<tr class="form-field form-required">
				<th scope="row" valign="top"><label for="bblm_tff">Dedicated Fans</label></th>
				  <td><input type="text" name="bblm_tff" size="2" tabindex="5" value="<?php print("$t->t_ff"); ?>" maxlength="2" id="bblm_tff"><br />
				  @ 20,000 each</td>
			</tr>
			<tr class="form-field form-required">
				<th scope="row" valign="top"><label for="bblm_tcl">Cheerleaders</label></th>
				  <td><input type="text" name="bblm_tcl" size="2" tabindex="7" value="<?php print("$t->t_cl"); ?>" maxlength="2" id="bblm_tcl"><br />
				  @ 20,000 each</td>
			</tr>
			<tr class="form-field form-required">
				<th scope="row" valign="top"><label for="bblm_tac">Assistant Coaches</label></th>
				  <td><input type="text" name="bblm_tac" size="2" tabindex="8" value="<?php print("$t->t_ac"); ?>" maxlength="3" id="bblm_tac"><br />
				  @ 20,000 each</td>
			</tr>
			<tr class="form-field form-required">
				<th scope="row" valign="top"><label for="bblm_tapoc">Apothecary</label></th>
				  <td><input type="text" name="bblm_tapoc" size="1" tabindex="6" value="<?php print("$t->t_apoc"); ?>" maxlength="1" id="bblm_tapoc"><br />
				  @ 80,000 each</td>
			</tr>
			<tr class="form-field form-required">
				<th scope="row" valign="top"><label for="bblm_tbank">Remaining Bank</label></th>
			  <td><input type="text" name="bblm_tbank" size="7" tabindex="9" value="<?php print("$t->t_bank"); ?>" maxlength="7" id="bblm_tbank">gp</td>
			</tr>
			<tr class="form-field form-required">
				<th scope="row" valign="top"><label for="bblm_ttv">Team Value</label></th>
				  <td><input type="hidden" name="bblm_ttv" size="7" tabindex="10" value="<?php print("$t->t_tv"); ?>" maxlength="7" id="bblm_ttv"><?php echo number_format( $t->t_tv ); ?> gp<br />
					<?php echo __('This will automatically update when you save','bblm' ); ?></td>
			</tr>
			<tr class="form-field form-required">
				<th scope="row" valign="top"><label for="bblm_tctv">Current Team Value</label></th>
					<td><input type="hidden" name="bblm_tctv" size="7" tabindex="10" value="<?php print("$t->t_ctv"); ?>" maxlength="7" id="bblm_tctv"><?php echo number_format( $t->t_ctv ); ?> gp<br />
					<?php echo __('This will automatically update when you save','bblm' ); ?></td>
			</tr>
			</table>
			<hr/>
			<table class="form-table">
			<tr class="form-field form-required">
				<th scope="row" valign="top"><label for="bblm_tshow">Show team in listings?</label></th>
				<td><input type="text" name="bblm_tshow" size="2" value="<?php print("$t->t_show"); ?>" maxlength="1"></td>
			</tr>
			<tr class="form-field form-required">
				<th scope="row" valign="top"><label for="bblm_tactive">Team Active?</label></th>
				<td><input type="text" name="bblm_tactive" size="2" value="<?php print("$t->t_active"); ?>" maxlength="1"></td>
			</tr>
		</table>

		<input type="hidden" name="bblm_tid" size="5" value="<?php print($tid); ?>" id="bblm_tid" maxlength="5">
		<input type="hidden" name="bblm_twid" size="5" value="<?php print($t->WPID); ?>" id="bblm_twid" maxlength="5">


		<p class="submit">
		<input type="submit" name="bblm_stat_update" value="Update team" title="Update Team"/>
		</p>
	</form>
<?php
		}//end of if sql
		else {
			print("<p><strong>That team could not be found! are you sure you have the correct one?</strong></p>\n");
		}
	}//end of item = stats
	if ("captain" == $_GET['item']) {
		  ////////////////////////////
		 // Setting a Team Captain //
		////////////////////////////
		$tid = $_GET['id'];
		print("<h3>Set a Team Captain</h3>");
		//determine the current team captain (if there is one)
		$currentcapsql = 'SELECT P.post_title, R.p_status, R.p_id FROM '.$wpdb->prefix.'team_captain C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P, '.$wpdb->prefix.'player R WHERE C.p_id = R.p_id AND C.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = P.ID AND C.t_id = '.$tid.' AND C.tcap_status = 1';
		if ($cc = $wpdb->get_row($currentcapsql)) {
			print("<p>The Current Team Captain is <strong>".$cc->post_title." ");
			if ($cc->p_status) {
				print("(Active)");
			}
			else {
				print("(Inactive)");
			}
			print("</strong>. You can use the below form to change this if you wish. If you are happy with the current captain then navigate away from the page.</p>\n");
			$tcap = $cc->p_id;
		}
		else {
			print("<p><strong>There is currently no team Captain assigned. you can use the form below to set a Team Captain<strong></p>\n");
		}
		print("<h4>Define a new Captain</h2>");
?>
	<form name="bblm_updatetcap" method="post" id="post">
		<table class="form-table">
			<tr class="form-field form-required">
				<th scope="row" valign="top"><label for="bblm_tcap">New Captain</label></th>
				<td><select name="bblm_tcap" id="bblm_tcap">
<?php
		$playersql = 'SELECT P.p_id, P.p_num, X.post_title, Y.pos_name, X.ID, X.guid, P.t_id, P.p_status, P.pos_id FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' X, '.$wpdb->prefix.'position Y WHERE P.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = X.ID AND P.pos_id = Y.pos_id AND P.p_status = 1 AND P.t_id = '.$tid.' ORDER BY P.p_num';
		if ($players = $wpdb->get_results($playersql)) {
			foreach ($players as $tp) {
				print("<option value=\"".$tp->p_id."\"");
				if ($tp->p_id == $tcap) {
					print(" selected=\"selected\"");
				}
				print(">#".$tp->p_num." - ".$tp->post_title." (".$tp->pos_name.")</option>\n");
			}
		}
?>
				</select></td>
			</tr>
		</table>
		<input type="hidden" name="bblm_tid" size="5" value="<?php print($tid); ?>" id="bblm_tid" maxlength="5">
		<p class="submit">
			<input type="submit" name="bblm_tcap_update" value="Set Captain" title="Set Captain"/>
		</p>
	</form>
<?php
	}//end of if item=captain
}//end of $_GET checking

else {
  ///////////////////////////
 // Step 1: Main team selection //
///////////////////////////
?>
<form name="bblm_playeroptions" method="post" id="post">
	<p>Below are all the teams that have played in the League. Please select one of the options below to continue with your request.</p>

<?php

  ///////////////////////
 // List Active Teams //
///////////////////////
	$teamsql = 'SELECT P.t_id, X.post_title, X.ID, X.guid, P.r_id, P.t_active, P.t_img FROM '.$wpdb->prefix.'team P, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' X WHERE P.t_id = J.tid AND J.prefix = \'t_\' AND J.pid = X.ID ORDER BY X.post_title';
	if ($teams = $wpdb->get_results($teamsql)) {
		$zebracount = 1;
		print("<table class=\"widefat\">\n	<thead>\n		 <tr>\n		   		<th scope=\"row\">ID</th>\n		   <th scope=\"col\">Team Name</th>\n		   <th scope=\"col\">Edit Stats</th>\n		   <th scope=\"col\">Edit Players</th>\n		   <th scope=\"col\">Add Player</th>\n		   <th scope=\"col\">Captain</th>\n		   <th scope=\"col\">View</th>\n		 </tr>\n	</thead>\n	<tbody>\n");

		foreach ($teams as $t) {

				if ($zebracount % 2) {
					print("					<tr class=\"alternate\">\n");
				}
				else {
					print("					<tr>\n");
				}

				print("		   <td>".$t->t_id."</a></td>\n		   <td><a href=\"");

				bloginfo('url');
				print("/wp-admin/post.php?post=".$t->ID."&action=edit\">".$t->post_title."</a> - " . bblm_get_race_name( $t->r_id ) . "</td>\n");

				print("							<td><a href=\"");
				bloginfo('url');
				print("/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.edit.team.php&action=edit&item=stats&id=".$t->t_id."\" title=\"Edit the Team Purchases\">Edit Purchases</a></td>\n");

				//we now check to see how players are on this team
				$tplayerssql = 'SELECT COUNT(*) FROM '.$wpdb->prefix.'player WHERE t_id = '.$t->t_id;
				$tplayers = $wpdb->get_var($tplayerssql);
				if (0 < $tplayers) {
					//we have players so display the link to edit them.
					print("							<td><a href=\"");
					bloginfo('url');
					print("/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.edit.player.php&action=select&item=none&id=".$t->t_id."\" title=\"View the list of players on the team\">Edit Players</a></td>\n");
				}
				else {
					print("							<td>-</td>\n");
				}

				if ( ( $t->t_active ) && ( 0 == $tplayers ) ) {
					print("							<td><a href=\"");
					bloginfo('url');
					print("/wp-admin/admin.php?page=bblm_player_addbulk\" title=\"Add a new payer to the team\">Add Bulk Players</a></td>\n");
				}
				else if ( $t->t_active ) {
					print("							<td><a href=\"");
					bloginfo('url');
					print("/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.add.player.php&action=add&item=none&id=".$t->t_id."\" title=\"Add a new payer to the team\">Add Player</a></td>\n");
				}
				else {
					print("							<td>-</td>\n");
				}

				if (0 < $tplayers) {
					print("							<td><a href=\"");
					bloginfo('url');
					print("/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.edit.team.php&action=edit&item=captain&id=".$t->t_id."\" title=\"Set Team Captain\">Set Captain</a></td>\n");
				}
				else {
					//There are no players so no point in defining a Captain
					print("							<td>-</td>\n");
				}
				print("							<td><a href=\"".$t->guid."\" title=\"View the Team page\">View</a></td>		 </tr>\n");


			$zebracount++;
		}
		print("	</tbody>\n</table>\n");
?>
	<h3>Related Links</h3>
	<ul>
		<li><a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.add.team.php" title="Add a new player to the team">Add a new team</a></li>
	</ul>
<?php
	}
	else {
		print("<p>There are no known teams in the league!</p>");
	}

}
?>
</div>
