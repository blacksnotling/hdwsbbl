<?php
/**
 * BBowlLeagueMan Manage Competitions Admin page
 *
 * Management page for all competitions
 *
 * @author 		Blacksnotling
 * @category 	Core
 * @package 	BBowlLeagueMan/Pages
 */

//Check the file is not being accessed directly
if (!function_exists('add_action')) die('You cannot run this file directly. Naughty Person');

?>
<div class="wrap">
	<h2>Competition Management</h2>
<?php
  /*
    Note: The $_POST checking is not tied into the main if else structure because depending on the he outcome of the
 	$_post procesing the final screen is modified!
*/
$success = 0;
$editattempt = 0;
$addattempt = 0;

  ////////////////////
 // $_POST checking //
////////////////////
if (isset($_POST['bblm_editcomp_submit'])) {
	  //////////////////////////////
	 // Save Changes to Comp Settings //
	//////////////////////////////
/*	print("<pre>");
	print_r($_POST);
	print("</pre>");
	print("<hr />");*/

	$bblm_safe_input = array();

	$bblm_safe_input['csdate'] = esc_sql( $_POST['bblm_editcomp_sdate'] );
	$bblm_safe_input['cedate'] = esc_sql( $_POST['bblm_editcomp_edate'] );
	$bblm_safe_input['cstand'] = esc_sql( $_POST['bblm_editcomp_standings'] );;
	$bblm_safe_input['cid'] = esc_sql( $_POST['bblm_editcomp_id'] );;

	//$editcompsql = 'UPDATE `'.$wpdb->prefix.'comp` SET `c_sdate` = \'2009-09-22 00:00:01\', `c_edate` = \'0000-00-01 00:00:00\', `c_showstandings` = \'0\' WHERE `c_id` = 17 LIMIT 1';
	$editcompsql = 'UPDATE `'.$wpdb->prefix.'comp` SET `c_sdate` = \''.$bblm_safe_input['csdate'].'\', `c_edate` = \''.$bblm_safe_input['cedate'].'\', `c_showstandings` = \''.$bblm_safe_input['cstand'].'\' WHERE `c_id` = \''.$bblm_safe_input['cid'].'\' LIMIT 1';


	if (FALSE !== $wpdb->query($editcompsql)) {
		$success = 1;
		$editattempt = 1;
	}
	else {
		$success = 0;
		$editattempt = 1;
	}


}
else if (isset($_POST['bblm_add_comp'])) {
	  //////////////////////////
	 // NOT YET IMPLEMENTED //
	/////////////////////////

}
else if (isset($_POST['bblm_edit_dyk'])) {
	  //////////////////////////
	 // NOT YET IMPLEMENTED //
	/////////////////////////
/*	print("<pre>");
	print_r($_POST);
	print("</pre>");
	print("<hr />");*/

}

/*
	End of $_POST checking. Page Flow begins now
*/

  ////////////////////
 // $_GET checking //
////////////////////
if ( "edit" == $_GET['action'] ) {
	if ( "comp" == $_GET['item'] ) {
		  //////////////////
		 // Editing Comp //
		//////////////////
		$cid = $_GET['id'];
		$compsql = 'SELECT C.*, C.sea_id, C.series_id, T.ct_name, P.post_title FROM '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'comp_type T, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE C.c_id = J.tid AND J.prefix = "c_" AND J.pid = P.ID AND C.ct_id = T.ct_id AND C.c_id ='.$cid;
		if ($cc = $wpdb->get_row($compsql)) {

?>
	<h3>Edit Settings - <?php print($cc->post_title); ?></h3>
	<p>From this screen you can edit a Competition. Below is the information for the competition that you selected for editing. When happy with your changes press the "Save Changes" button.</p>
	<p>As always, if you wish to cancel this then please <a href="<?php bloginfo('url'); ?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.manage.comps.php" title="Return to the Competition Management screen">return to the Competition Management screen</a>.</p>

	<form name="bblm_editcompsettings_frm" method="post" id="post" action="<?php bloginfo('url'); ?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.manage.comps.php">
		<div id="col-right"><div class="col-wrap"><div class="form-wrap">
			<h4>Competition Information</h4>
			<ul>
				<li><strong>Status</strong>: <strong><?php if ($cc->c_active) {print("Active");} else { print("Complete"); } ?></strong></li>
				<li><strong>Season</strong>: <?php echo bblm_get_season_name( $cc->sea_id ); ?></li>
				<li><strong>Type</strong>: <?php print($cc->ct_name); ?></li>
				<li><strong>Cup</strong>:<?php print($cc->series_name); ?></li>
			</ul>
		</div></div></div>

		<div id="col-left"><div class="col-wrap"><div class="form-wrap">
			<div class="form-field">
				<label for="bblm_editcomp_sdate">Start Date</label>
				<input id="bblm_editcomp_sdate" name="bblm_editcomp_sdate" type="text" value="<?php print($cc->c_sdate); ?>" maxlength="19" size="20" />
			</div>
<?php
			if (!$cc->c_active) {
?>
			<div class="form-field">
				<label for="bblm_editcomp_edate">End Date</label>
				<input id="bblm_editcomp_edate" name="bblm_editcomp_edate" type="text" value="<?php print($cc->c_edate); ?>" maxlength="19" size="20" />
			</div>
<?php
			}
			else {
?>
				<input type="hidden" name="bblm_editcomp_edate" size="19" value="<?php print($cc->c_edate); ?>" id="bblm_editcomp_edate">
<?php
			}
?>
			<div class="form-field">
				<label for="bblm_editcomp_standings">Show standings?*</label>
				<select name="bblm_editcomp_standings" id="bblm_editcomp_standings">
<?php
			print("				<option value=\"1\"");
			if (1 == $cc->c_showstandings) {
				print(" selected=\"selected\"");
			}
			print(">Yes</option>\n");
			print("				<option value=\"0\"");
			if (0 == $cc->c_showstandings) {
				print(" selected=\"selected\"");
			}
			print(">No</option>\n");
?>
				</select>
			</div>
			<p><strong>*</strong> - before you change this to yes, make sure teams have been added to the Competition and any brackers have been generated (if applicable)</p>
		</div></div></div>

		<input id="bblm_editcomp_id" name="bblm_editcomp_id" type="hidden" value="<?php print($cid); ?>" maxlength="4" />

		<p class="submit">
			<input type="submit" name="bblm_editcomp_submit" id="bblm_editcomp_submit" value="Save Changes" title="Save changes to the competition"> or <a href="<?php bloginfo('url'); ?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.manage.comps.php" title="Cancel and return to the Competition Management Screen">Cancel</a>
		</p>
	</form>
<?php
		}//end of if comp data returned
		else {
?>
		<h3>Edit Settings - Error!</h3>
		<p><strong>We could not find the competition you selected! Please please <a href="<?php bloginfo('url'); ?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.manage.comps.php" title="Return to the Competition Management screen">return to the Competition Management screen</a> and select a Competition from there.</strong></p>
<?php
		}
	}
}//end of if $_GET action edit
else if ("add" == $_GET['action']) {
	  //////////////////
	 // Add New Comp //
	//////////////////
?>
	<h3>Add a new Competition</h3>
<?php
}
else {
	  ///////////////////////////////////////////////
	 // Step 1: No Action selected so Display list of Comps //
	///////////////////////////////////////////////
?>
	<h3>Management Central</h3>
	<p>Welcome to the Competition Management page. Below is a list of all the Competitions that are recorded in the database. Click on the name of one to edit the description or select one of the other options on the row There is a link to add a new one at the
	top and the bottom of the page.</p>

<?php
	  /////////////////////////////////////////////////////////////////////////////////////////////
	 // Here we check to see if any information has been passed down from the $_POST processing //
	/////////////////////////////////////////////////////////////////////////////////////////////

	if ($success && $editattempt) {
		//An Edit was attempted and it worked
?>
		<div id="updated" class="updated fade">
			<p>The Competition has been sucesfully updated!</p>
		</div>
<?php
	}
	else if ($success && $addattempt) {
		//An Add was attempted and it worked
?>
		<div id="updated" class="updated fade">
			<p>The Competition has been sucesfully Added to the site and you can start to add teams to it. You will need to enable the league standings display once this has been done</p>
		</div>
<?php
	}
	else if ($success && $editattempt) {
		//An edit was attempted and it worked
?>
		<div id="updated" class="updated fade">
			<p>The Competition has been sucesfully Edited.</p>
		</div>
<?php
	}
	else if ($success && !$editattempt) {
		//An Edit was attempted and it failed
?>
		<div id="updated" class="updated fade">
			<p>Something went wrong with the edit. Please try again!</p>
		</div>
<?php
	}
	else if ($success && !$addattempt) {
			//An Add was attempted and it failed
	?>
			<div id="updated" class="updated fade">
				<p>Something went wrong with the addition. Please try again!</p>
			</div>
	<?php
	}
	  /////////////////////////////////////
	 // Start of main Management screen //
	/////////////////////////////////////
?>
<!--
	<ul>
		<li><a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.manage.comps.php&action=add" title="Create a new Did You Know">Create a new Did You Know</a></li>
	</ul>
-->

<?php
	$compsql = 'SELECT C.c_id, C.c_active, P.post_title, P.ID, C.sea_id, P.guid FROM '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE C.c_id = J.tid AND J.prefix = "c_" AND J.pid = P.ID AND C.c_show = 1 ORDER BY C.c_id DESC';
	if ($comps = $wpdb->get_results($compsql)) {
		$zebracount = 1;
?>
	<table class="widefat">
		<thead>
		<tr>
			<th scope="row">ID</th>
			<th scope="col">Title [Edit]</th>
			<th scope="col">Edit Settings</th>
			<th scope="col">Teams</th>
			<th scope="col">Status</th>
			<th scope="col">View</th>
		</tr>
		</thead>
		<tbody>
<?php
		foreach ($comps as $c) {
			if ($zebracount % 2) {
				print("		<tr class=\"alternate\">\n");
			}
			else {
				print("		<tr>\n");
			}
?>
			<td><?php print($c->c_id); ?></td>
			<td><a href="<?php print(bloginfo('url')); ?>/wp-admin/post.php?post=<?php print($c->ID); ?>&action=edit" title="Edit the description of <?php print($c->post_title); ?>"><?php print($c->post_title); ?></a></td>
			<td><a href="<?php print(bloginfo('url')); ?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.manage.comps.php&action=edit&item=comp&id=<?php print($c->c_id); ?>" title="Edit the Settings of <?php print($c->post_title); ?>">Edit Settings</a></td>
			<td>Edit / Add Teams</td>
<?php
			if ($c->c_active) {
?>
			<td><a href="#" title="Close this competiton [NOT WORKING]">Close</a></td>
<?php
			}
			else {
?>
			<td>Complete</td>
<?php
			}
?>
			<td><a href="<?php print($c->guid); ?>" title="View the Competitions page on the main site">View</a></td>
		</tr>
<?php
		}//end of foreach
?>
		</tbody>
	</table>
<?php
	}//end of if SQL
	else {
		print("	<p><strong>There appears to be no Compeiitions! Would you like to create one?</strong></p>");
	}

?>
<!--	<ul>
		<li><a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.manage.dyk.php&action=add" title="Create a new Did You Know">Create a new Did You Know</a></li>
	</ul>
-->
<?php
}//end of final else
?>

</div>
