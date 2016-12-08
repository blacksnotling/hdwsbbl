<?php
/*
*	Filename: bb.admin.add.season.php
*	Description: Page used to start a new season.
*/

//Check the file is not being accessed directly
if (!function_exists('add_action')) die('You cannot run this file directly. Naughty Person');

?>
<div class="wrap">
	<h2>Start a new season</h2>
	<p>The following form will start a new season for the league.</p>

<?php

if(isset($_POST['bblm_season_submit'])) {

	//Determine the parent page
	$options = get_option('bblm_config');
	$bblm_page_parent = htmlspecialchars($options['page_season'], ENT_QUOTES);

	$my_post = array(
		'post_title' => wp_filter_nohtml_kses($_POST['bblm_sname']),
		'post_content' => wp_filter_kses($_POST['bblm_sdesc']),
		'post_type' => 'page',
		'post_status' => 'publish',
		'comment_status' => 'closed',
		'ping_status' => 'closed',
		'post_parent' => $bblm_page_parent
	);

	$bblm_ssdate = $_POST['bblm_ssdate'] ." 10:00:01";

	if ($bblm_submission = wp_insert_post( $my_post )) {
		add_post_meta($bblm_submission, '_wp_page_template', 'bb.view.season.php');

		$bblmdatasql = 'INSERT INTO `'.$wpdb->prefix.'season` (`sea_id`, `sea_name`, `sea_sdate`, `sea_fdate`, `sea_active`) VALUES (\'\', \''.wp_filter_nohtml_kses($_POST['bblm_sname']).'\', \''.$bblm_ssdate.'\', \'0000-00-00 00:00:00\', \'1\')';
		$wpdb->query($bblmdatasql);

		$bblmmappingsql = 'INSERT INTO `'.$wpdb->prefix.'bb2wp` (`bb2wp_id`, `tid`, `pid`, `prefix`) VALUES (\'\',\''.$wpdb->insert_id.'\', \''.$bblm_submission.'\', \'sea_\')';
		$wpdb->query($bblmmappingsql);

		$success = 1;
		$addattempt = 1;
	}
?>
	<div id="updated" class="updated fade">
	<p>
	<?php
	if ($success) {
		print("Season has been Created. <a href=\"".get_permalink($bblm_submission)."\" title=\"View the new Season\">View page</a>");
	}
	else {
		print("Something went wrong! Please try again.");
	}


?>
</p>
	</div>
<?php
}//end of submit if
?>

	<form name="bblm_test" method="post" id="post">

	<table class="form-table">
		<tr valign="top">
			<th scope="row"><label for="bblm_sname">Season Name</label></th>
			<td><input type="text" name="bblm_sname" size="40" value="" id="bblm_sname" class="regular-text"/></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="bblm_ssdate">Start Date</label></th>
			<td><input type="text" name="bblm_ssdate" size="11" value="<?php print(date('Y-m-d')); ?>" id="bblm_ssdate" class="regular-text"  maxlength="10"/></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="bblm_sdesc">Description</label></th>
			<td><p><textarea rows="10" cols="50" name="bblm_sdesc" id="bblm_sdesc" class="large-text"></textarea></p></td>
		</tr>
	</table>

	<p class="submit">
	<input type="submit" name="bblm_season_submit" tabindex="4" value="Create Season" title="Create the Season"/>
	</p>

	</form>
</div>