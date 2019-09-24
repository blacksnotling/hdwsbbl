<?php
/**
 * BBowlLeagueMan Add Championship Cup
 *
 * Page used to add a new Championship Cup to the league
 *
 * @author 		Blacksnotling
 * @category 	Core
 * @package 	BBowlLeagueMan/Pages
 */

//Check the file is not being accessed directly
if (!function_exists('add_action')) die('You cannot run this file directly. Naughty Person');

?>
<div class="wrap">
	<h2>Add a Championship Cup</h2>
	<p>Use the following form to add a new Championship Cup to the League.</p>

<?php

if(isset($_POST['bblm_series_submit'])) {

	//Determine the parent page
	$options = get_option('bblm_config');
	$bblm_page_parent = htmlspecialchars($options['page_series'], ENT_QUOTES);

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
		add_post_meta($bblm_submission, '_wp_page_template', 'bb.view.cup.php');

		$bblmdatasql = 'INSERT INTO `'.$wpdb->prefix.'series` (`series_id`, `series_name`, `series_type`, `series_active`, `series_show`) VALUES (\'\', \''.wp_filter_nohtml_kses($_POST['bblm_sname']).'\', \''.$_POST['bblm_stype'].'\', \'1\', \'1\')';
		$wpdb->query($bblmdatasql);

		$bblmmappingsql = 'INSERT INTO `'.$wpdb->prefix.'bb2wp` (`bb2wp_id`, `tid`, `pid`, `prefix`) VALUES (\'\',\''.$wpdb->insert_id.'\', \''.$bblm_submission.'\', \'series_\')';
		$wpdb->query($bblmmappingsql);

		$success = 1;
		$addattempt = 1;
		do_action( 'bblm_post_submission' );
	} //end of if post insertion was successful
?>
	<div id="updated" class="updated fade">
		<p>
	<?php
	if ($success) {
		print("Championship has been created. <a href=\"".get_permalink($bblm_submission)."\" title=\"View the new Championship\">View page</a>");
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

	<form name="bblm_addcup" method="post" id="post">

	<table class="form-table">
		<tr valign="top">
				<th scope="row"><label for="bblm_sname">Championship Name</label></th>
				<td><input type="text" name="bblm_sname" size="40" value="" id="bblm_sname" class="regular-text"/></td>
		</tr>
		<tr valign="top">
				<th scope="row"><label for="bblm_sdesc">Description</label></th>
				<td><p><textarea rows="10" cols="50" name="bblm_sdesc" id="bblm_sdesc" class="large-text"></textarea></p></td>
		</tr>
		<tr valign="top">
				<th scope="row"><label for="bblm_stype">Type of Championship</label></th>
				<td><select name="bblm_stype" id="bblm_stype">
					<option value="Major">Major</option>
					<option value="Minor">Minor</option>
					<option value="Series">Series</option>
				</select></td>
		</tr>
	</table>
	<p class="submit"><input type="submit" name="bblm_series_submit" value="Add Cup" title="Add the Cup" class="button-primary"/></p>

	</form>
</div>
