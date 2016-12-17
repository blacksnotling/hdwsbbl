<?php
/*
*	Filename: bb.admin.manage.dyk.php
*	Version: 1.1
*	Description: Management page for "Did You Know"
*/
/* -- Change History --
20090130 - 0.1b - Initial creation of file. Frame work Laid out.
20090131 - 0.2b - Added the De/activation bit, added the addition bit and began work on editing a DYK!
20090831 - 1.0 - Extended the title fiele to match that of the one in the DB!, added cancel links
20100123 - 1.1 - Updated the prefix for the custom bb tables in the Database (tracker [224])
*/

//Check the file is not being accessed directly
if (!function_exists('add_action')) die('You cannot run this file directly. Naughty Person');

?>
<div class="wrap">
	<h2>"Did You Know" Management</h2>
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
if (isset($_POST['bblm_activate_dyk'])) {
	  ////////////////////
	 // De/Activate DYK //
	//////////////////////
/*	print("<pre>");
	print_r($_POST);
	print("</pre>");
	print("<hr />");*/

	$did = $_POST['bblm_did'];
	$dshow = $_POST['bblm_dshow'];

	//Flip the show value
	if ($dshow) {
		$dshow = 0;
	}
	else {
		$dshow = 1;
	}

	$updatedyksql = 'UPDATE `'.$wpdb->prefix.'dyk` SET `dyk_show` = \''.$dshow.'\' WHERE `dyk_id` = \''.$did.'\' LIMIT 1';

	if (FALSE !== $wpdb->query($updatedyksql)) {
		$success = 1;
		$editattempt = 1;
	}
	else {
		$success = 0;
		$editattempt = 1;
	}
}
else if (isset($_POST['bblm_add_dyk'])) {
	  ////////////////////
	 // Add new DYK //
	//////////////////////
/*	print("<pre>");
	print_r($_POST);
	print("</pre>");
	print("<hr />");*/

	$bblm_safe_input = array();

	if (get_magic_quotes_gpc()) {
		$_POST['bblm_dtitle'] = stripslashes($_POST['bblm_dtitle']);
		$_POST['bblm_dcontent'] = stripslashes($_POST['bblm_dcontent']);
	}
	$bblm_safe_input['dtitle'] = $wpdb->escape($_POST['bblm_dtitle']);
	$bblm_safe_input['dcontent'] = $wpdb->escape($_POST['bblm_dcontent']);

	if ($_POST['bblm_dtype']) {
		$bblm_safe_input['dtype'] = 1;
	}
	else {
		$bblm_safe_input['dtype'] = 0;
	}

	//generate time NOW.
	$bblm_date_now = date('Y-m-j H:i:59');

	$adddyksql = 'INSERT INTO `'.$wpdb->prefix.'dyk` (`dyk_id`, `dyk_type`, `dyk_title`, `dyk_desc`, `dyk_show`, `dyk_cdate`, `dyk_edate`) VALUES (\'\', \''.$bblm_safe_input['dtype'].'\', \''.$bblm_safe_input['dtitle'].'\', \''.$bblm_safe_input['dcontent'].'\', \'0\', \''.$bblm_date_now.'\', \'0000-00-00 00:00:00\')';

	if (FALSE !== $wpdb->query($adddyksql)) {
		$success = 1;
		$addattempt = 1;
	}
	else {
		$success = 0;
		$addattempt = 1;
	}

}
else if (isset($_POST['bblm_edit_dyk'])) {
	  ////////////////////
	 // Edit existing DYK //
	//////////////////////
/*	print("<pre>");
	print_r($_POST);
	print("</pre>");
	print("<hr />");*/

	$did= $_POST['bblm_did'];
	$bblm_safe_input = array();

	if (get_magic_quotes_gpc()) {
		$_POST['bblm_dtitle'] = stripslashes($_POST['bblm_dtitle']);
		$_POST['bblm_dcontent'] = stripslashes($_POST['bblm_dcontent']);
	}
	$bblm_safe_input['dtitle'] = $wpdb->escape($_POST['bblm_dtitle']);
	$bblm_safe_input['dcontent'] = $wpdb->escape($_POST['bblm_dcontent']);

	if ($_POST['bblm_dtype']) {
		$bblm_safe_input['dtype'] = 1;
	}
	else {
		$bblm_safe_input['dtype'] = 0;
	}

	//generate time NOW.
	$bblm_date_now = date('Y-m-j H:i:59');

	//$updatesqlsql = 'UPDATE `'.$wpdb->prefix.'dyk` SET `dyk_type` = \'0\', `dyk_title` = \'SQL.\', `dyk_desc` = \'This is the test of the SQl.\', `dyk_edate` = \'2009-02-01 00:15:00\' WHERE `dyk_id` = 1 LIMIT 1';
	$editdyksql = 'UPDATE `'.$wpdb->prefix.'dyk` SET `dyk_type` = \''.$bblm_safe_input['dtype'].'\', `dyk_title` = \''.$bblm_safe_input['dtitle'].'\', `dyk_desc` = \''.$bblm_safe_input['dcontent'].'\', `dyk_edate` = \''.$bblm_date_now.'\' WHERE `dyk_id` = \''.$did.'\' LIMIT 1';

		if (FALSE !== $wpdb->query($editdyksql)) {
			$success = 1;
			$editattempt = 1;
		}
		else {
			$success = 0;
			$editattempt = 1;
	}
}



  ////////////////////
 // $_GET checking //
////////////////////
if ("edit" == $_GET['action']) {
	if ("activate" == $_GET['item']) {
		  /////////////////////////
		 // De/Activate the DYK //
		/////////////////////////
		$did = $_GET['id'];
?>
	<h3>Activate / Deactivate this Did you Know</h3>
	<p>There comes a time when a Did You Know is no longer relevant. If this is the case you can deactivate it and remove
	it from the sire. On the flip side, when a Did You Know is ready you can activate it from here and make it appear on the site.</p>

	<p>The Did You Know is displayed below for refrence. If you want to de/activate this Did You Know then press the button below. Alternativly if you wish to cancel this then please <a href="<?php bloginfo('url'); ?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.manage.dyk.php" title="Return to the Did You Know Management screen">return to the Did You Know Management screen</a>.</p>
<?php
		$dyksql = 'SELECT dyk_show, dyk_title, dyk_desc FROM '.$wpdb->prefix.'dyk WHERE dyk_id ='.$did;
		$dd = $wpdb->get_row($dyksql);
?>
	<div style="border:1px solid #333;width:350px;padding:0 10px;">
<?php
		if ("none" !== $dd->dyk_title) {
			print("<h4>".$dd->dyk_title."</h4>\n");
		}
?>
		<p><?php print(wpautop($dd->dyk_desc)); ?></p>
	</div>
	<form name="bblm_activatedyk" method="post" id="post" action="<?php bloginfo('url'); ?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.manage.dyk.php">

	<input type="hidden" name="bblm_did" value="<?php print($did); ?>">
	<input type="hidden" name="bblm_dshow" value="<?php print($dd->dyk_show); ?>">
	<p class="submit">
<?php
		if (1 == $dd->dyk_show) {
			print("<input type=\"submit\" name=\"bblm_activate_dyk\" value=\"Deactivate Did You Know\" title=\"Deactivate Did You Know\"/>");
		}
		else {
			print("<input type=\"submit\" name=\"bblm_activate_dyk\" value=\"Activate Did You Know\" title=\"Activate Did You Know\"/>");
		}
?>
	</p>
	</form>
<?php
	}
	else if ("dyk" == $_GET['item']) {
		  /////////////////
		 // Editing DYK //
		/////////////////
		$did = $_GET['id'];
		$dyksql = 'SELECT dyk_type, dyk_title, dyk_desc FROM '.$wpdb->prefix.'dyk WHERE dyk_id ='.$did;
		$dd = $wpdb->get_row($dyksql);
?>
	<h3>Edit this Did you Know</h3>
	<p>From this screen you can edit a Did You Know. Below is the Did You Know that you selected for editing. When happy with the text then press the "Save Changes" button.</p>
	<p>As always, if you wish to cancel this then please <a href="<?php bloginfo('url'); ?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.manage.dyk.php" title="Return to the Did You Know Management screen">return to the Did You Know Management screen</a>.</p>
	<p><strong>Note:</strong> If you wish to remove the title of the Did You Know, set the title below to "none".</p>

	<form name="bblm_editdyk" method="post" id="post" action="<?php bloginfo('url'); ?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.manage.dyk.php">

	<p><label for="bblm_dtitle">Title:</label>
	<input type="text" name="bblm_dtitle" size="25" value="<?php print($dd->dyk_title); ?>" maxlength="55"></p>

	<p><label for="bblm_dcontent">Content:</label><br />
	<textarea name="bblm_dcontent" cols="80" rows="8"><?php print($dd->dyk_desc); ?></textarea><br />
	Note that HTML is allowed in the above but you will have to type it yourself!</p>

	<p>What type of Did You Know is this?</p>
	<ul>
	  <li><input type="radio" value="1" name="bblm_dtype"<?php if (1 == $dd->dyk_type) print(" checked=\"yes\""); ?>> Trivia</li>
	  <li><input type="radio" value="0" name="bblm_dtype"<?php if (0 == $dd->dyk_type) print(" checked=\"yes\""); ?>> Fact</li>
	</ul>

	<input type="hidden" name="bblm_did" value="<?php print($did); ?>">
	<p class="submit">
		<input type="submit" name="bblm_edit_dyk" value="Save Changes" title="Save Changes"/> or <a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.manage.dyk.php" title="Create a new Did You Know">Cancel</a>.
	</p>
	</form>
<?php
	}
}//end of if $_GET action edit
else if ("add" == $_GET['action']) {
	  /////////////////
	 // Add New DYK //
	/////////////////
?>
	<h3>Add a new Did You Know</h3>
	<p>Use the below form to add a new Did You Know to the system. By default the Did You Know will not appear in the listings
	stright away. This is to give you a chance to review and catch any errors. The Did You Know can be activated from the
	main Did You Know Management Screen,</p>
	<p>As always, if you wish to cancel this then please <a href="<?php bloginfo('url'); ?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.manage.dyk.php" title="Return to the Did You Know Management screen">return to the Did You Know Management screen</a>.</p>
	<p><strong>Note:</strong> If you don't want the Did You Know to have a title then leave the "none" in place.</p>

	<form name="bblm_adddyk" method="post" id="post" action="<?php bloginfo('url'); ?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.manage.dyk.php">

	<p><label for="bblm_dtitle">Title:</label>
	<input type="text" name="bblm_dtitle" size="25" value="none" maxlength="55"></p>

	<p><label for="bblm_dcontent">Content:</label><br />
	<textarea name="bblm_dcontent" cols="80" rows="8"></textarea><br />
	Note that HTML is allowed in the above but you will have to type it yourself!</p>

	<p>What type of Did You Know is this?</p>
	<ul>
	  <li><input type="radio" value="1" name="bblm_dtype" checked="yes"> Trivia</li>
	  <li><input type="radio" value="0" name="bblm_dtype"> Fact</li>
	</ul>

	<p class="submit">
		<input type="submit" name="bblm_add_dyk" value="Add Did You Know" title="Add Did You Know"/> or <a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.manage.dyk.php" title="Create a new Did You Know">Cancel</a>.
	</p>
	</form>
<?php
}
else {
	  ///////////////////////////////////////////////
	 // Step 1: No Action selected so Display list of DYK //
	///////////////////////////////////////////////
?>
	<h3>Management Central</h3>
	<p>Did You Know that you can manage the Did You Knows from here? Below is the list of all the existing DYKs (with the
	newest at the top). Click on title to edit one or select one of the options next to it. There is a link to add a new one at the
	top and the bottom of the page.</p>

<?php
	  /////////////////////////////////////////////////////////////////////////////////////////////
	 // Here we check to see if any information has been passed down from the $_POST processing //
	/////////////////////////////////////////////////////////////////////////////////////////////

	if ($success && $editattempt) {
		//An Edit was attempted and it worked
?>
		<div id="updated" class="updated fade">
			<p>The Did You Know has been sucesfully updated!</p>
		</div>
<?php
	}
	else if ($success && $addattempt) {
		//An Add was attempted and it worked
?>
		<div id="updated" class="updated fade">
			<p>The Did You Know has been sucesfully Added to the site. You will need to ativate it from the list below before it is displayed on the site!</p>
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

	<ul>
		<li><a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.manage.dyk.php&action=add" title="Create a new Did You Know">Create a new Did You Know</a></li>
	</ul>

<?php
	$dyksql = 'SELECT dyk_id, dyk_type, dyk_show, dyk_title, dyk_desc FROM '.$wpdb->prefix.'dyk ORDER BY dyk_id DESC';
	if ($dyks = $wpdb->get_results($dyksql)) {
		$zebracount = 1;
?>
	<table class="widefat">
		<thead>
		<tr>
			<th scope="row">ID</th>
			<th scope="col">Title [Edit]</th>
			<th scope="col">Snippit</th>
			<th scope="col">Type</th>
			<th scope="col">[De]Activate</th>
		</tr>
		</thead>
		<tbody>
<?php
		foreach ($dyks as $d) {
			if ($zebracount % 2) {
				print("		<tr class=\"alternate\">\n");
			}
			else {
				print("		<tr>\n");
			}
?>
			<td><?php print("$d->dyk_id"); ?></td>
			<td><a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.manage.dyk.php&action=edit&item=dyk&id=<?php print("$d->dyk_id"); ?>" title="Edit this Did You Know"><?php if ("none" !== $d->dyk_title) {print("$d->dyk_title");} else { print("Edit"); } ?></a></td>
			<td><?php print(wp_trim_excerpt("$d->dyk_desc")); ?></td>
<?php
			if ($d->dyk_type) {
				print("			<td>Trivia</td>\n");
			}
			else {
				print("			<td>Fact</td>\n");
			}
			if ($d->dyk_show) {
				print("			<td><a href=\"");
				bloginfo('url');
				print("/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.manage.dyk.php&action=edit&item=activate&id=".$d->dyk_id."\" title=\"Deactivate this Did You Know\">Deactivate</td>\n");
			}
			else {
				print("			<td><a href=\"");
				bloginfo('url');
				print("/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.manage.dyk.php&action=edit&item=activate&id=".$d->dyk_id."\" title=\"Activate this Did You Know\">Activate</td>\n");
			}
			print("		</tr>\n");

			$zebracount++;
		}//end of for each dyk
?>
		</tbody>
		</thead>
	</table>
<?
	}//end of if SQL
	else {
		print("	<p><strong>There appears to be no Did YOu Knows! Would you lie to create some?</strong></p>");
	}

?>
	<ul>
		<li><a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.manage.dyk.php&action=add" title="Create a new Did You Know">Create a new Did You Know</a></li>
	</ul>
<?php
}//end of final else
?>

</div>