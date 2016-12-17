<?php
/*
*	Filename: bb.admin.add.stadium.php
*	Description: This page is used to add a new Stadium to the BBLM.
*/

//Check the file is not being accessed directly
if (!function_exists('add_action')) die('You cannot run this file directly. Naughty Person');

?>
<div class="wrap">
	<h2>Add a Stadium</h2>
	<p>Use the following form to add a new Stadium to the League.</p>

<?php
if(isset($_POST['bblm_stadium_submit'])) {

	//pull parent page from somewhere
	$options = get_option('bblm_config');
	$bblm_page_parent = htmlspecialchars($options['page_stadium'], ENT_QUOTES);

	$my_post = array(
		'post_title' => wp_filter_nohtml_kses($_POST['bblm_sname']),
		'post_content' => wp_filter_kses($_POST['bblm_sdesc']),
		'post_type' => 'page',
		'post_status' => 'publish',
		'comment_status' => 'closed',
		'ping_status' => 'closed',
		'post_parent' => $bblm_page_parent
	);

	if ($bblm_submission = wp_insert_post( $my_post )) {
		add_post_meta($bblm_submission, '_wp_page_template', 'bb.view.stadium.php');

		$bblmdatasql = 'INSERT INTO `'.$wpdb->prefix.'stadium` (`stad_id`, `stad_name`, `stad_img`) VALUES (\'\', \''.wp_filter_nohtml_kses($_POST['bblm_sname']).'\', \'\')';
		$wpdb->query($bblmdatasql);

		$bblmmappingsql = 'INSERT INTO `'.$wpdb->prefix.'bb2wp` (`bb2wp_id`, `tid`, `pid`, `prefix`) VALUES (\'\',\''.$wpdb->insert_id.'\', \''.$bblm_submission.'\', \'stad_\')';
		$wpdb->query($bblmmappingsql);

		$success = 1;
		$addattempt = 1;
	} //end of if post insertion was successful

?>
	<div id="updated" class="updated fade">
	<p>
	<?php
	if ($success) {
		print("Stadium has been added. <a href=\"".get_permalink($bblm_submission)."\" title=\"View the new Stadium\">View page</a>");
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
				<th scope="row"><label for="bblm_sname">Stadium Name</label></th>
				<td><input type="text" name="bblm_sname" size="40" value="" id="bblm_sname" class="regular-text"/></td>
		</tr>
		<tr valign="top">
				<th scope="row"><label for="bblm_sdesc">Description</label></th>
				<td><p><textarea rows="10" cols="50" name="bblm_sdesc" id="bblm_sdesc" class="large-text"></textarea></p></td>
		</tr>
	</table>

	<p class="submit">
	<input type="submit" name="bblm_stadium_submit" tabindex="4" value="Add Stadium" title="Add the Stadium"/>
	</p>

	</form>


	<h3>Existing Stadiums</h3>
	<p>The following Stadiums already exist in the HDWSBBL:</p>
	<?php
	$namesql = 'SELECT S.* FROM '.$wpdb->prefix.'stadium S, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE S.stad_id = J.tid AND J.prefix = \'stad_\' AND J.pid = P.ID ORDER BY S.stad_name';
	if ($names = $wpdb->get_results($namesql)) {
		print("<ul>");
		foreach ($names as $name) {
			print("<li>".$name->stad_name."</li>");
		}
		print("</ul>");
	}
	else {
		print("<p><strong>No Stadiums could be found!</strong></p>");
	}
?>
</div>