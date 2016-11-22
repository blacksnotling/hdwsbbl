<?php
/*
*	Filename: bb.admin.add.season.php
*	Version: 1.1.1
*	Description: Page used to start a new season. This will be added under the stats page
*/
/* -- Change History --
20080420 - 0.1b - Initial creation of file.
20080531 - 0.2b - Changed the page parent from stats to season
20080730 - 1.0 - bump to Version 1 for public release.
20100204 - 1.1 - Updated the prefix for the custom bb tables in the Database (tracker [224])
20100901 - 1.1.1 - Fixed a bug where the TBL_bb2wp table was not getting updated on creation! (tracker [316])
*/

//Check the file is not being accessed directly
if (!function_exists('add_action')) die('You cannot run this file directly. Naughty Person');

?>
<div class="wrap">
	<h2>Start a new season</h2>
	<p>The following form will start a new season for the league. The page will be created as a sub-page under stats.</p>

<?php

if(isset($_POST['bblm_season_submit'])) {
	$bblm_safe_input = array();


	//think about a for each
	if (get_magic_quotes_gpc()) {
	$_POST['bblm_sname'] = stripslashes($_POST['bblm_sname']);
	$_POST['bblm_sdesc'] = stripslashes($_POST['bblm_sdesc']);

	}

	$bblm_safe_input['sname'] = stripslashes($_POST['bblm_sname']);
	$bblm_safe_input['sdesc'] = stripslashes($_POST['bblm_sdesc']);

	$bblm_safe_input['sname'] = $wpdb->escape($bblm_safe_input['sname']);
	$bblm_safe_input['sdesc'] = $wpdb->escape($bblm_safe_input['sdesc']);
	$bblm_safe_input['ssdate'] = $_POST['bblm_ssdate'] ." 00:00:01";

	//generate time NOW.
	$bblm_date_now = date('Y-m-j H:i:59');

	//pull parent page from somewhere
	$options = get_option('bblm_config');
	$bblm_page_parent = htmlspecialchars($options['page_season'], ENT_QUOTES);

	//filter page body
	$bblm_removable = array("<pre>","</pre>","<p>","&nbsp;");
	$bblm_page_content = $bblm_safe_input['sdesc'];
	$bblm_page_content = str_replace($bblm_removable,"",$bblm_page_content);
	$bblm_page_content = str_replace("</p>","\n\n",$bblm_page_content);

	//snaitise page title
	$bblm_page_title = $bblm_safe_input['sname'];

	//convert page title to slug
	$bblm_page_slug = sanitize_title($bblm_page_title);

	//generate GUID
	$bblm_guid = get_bloginfo('wpurl');
	$bblm_guid .= "/season/";
	$bblm_guid .= $bblm_page_slug;

	$bblm_series_type = $bblm_safe_input['stype'];


$postsql = 'INSERT INTO '.$wpdb->posts.' (`ID`, `post_author`, `post_date`, `post_date_gmt`, `post_content`, `post_title`, `post_category`, `post_excerpt`, `post_status`, `comment_status`, `ping_status`, `post_password`, `post_name`, `to_ping`, `pinged`, `post_modified`, `post_modified_gmt`, `post_content_filtered`, `post_parent`, `guid`, `menu_order`, `post_type`, `post_mime_type`, `comment_count`) VALUES (\'\', \'1\', \''.$bblm_date_now.'\', \''.$bblm_date_now.'\', \''.$bblm_page_content.'\', \''.$bblm_page_title.'\', \'0\', \'\', \'publish\', \'closed\', \'closed\', \'\', \''.$bblm_page_slug.'\', \'\', \'\', \''.$bblm_date_now.'\', \''.$bblm_date_now.'\', \'\', \''.$bblm_page_parent.'\', \''.$bblm_guid.'\', \'0\', \'page\', \'\', \'0\')';


	if (FALSE !== $wpdb->query($postsql)) {
		$bblm_post_number = $wpdb->insert_id;//captured from SQL string
	}
	else {
		$wpdb->print_error();
	}

$postmetasql = 'INSERT INTO '.$wpdb->postmeta.' (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES (\'\', \''.$bblm_post_number.'\', \'_wp_page_template\', \'bb.view.season.php\')';

	if (FALSE !== $wpdb->query($postmetasql)) {
		$sucess = TRUE;
	}
	else {
		$wpdb->print_error();
	}

//note, season is active by default
$seasonsql = 'INSERT INTO `'.$wpdb->prefix.'season` (`sea_id`, `sea_name`, `sea_sdate`, `sea_fdate`, `sea_active`) VALUES (\'\', \''.$bblm_page_title.'\', \''.$bblm_safe_input['ssdate'].'\', \'0000-00-00 00:00:00\', \'1\')';

if (FALSE !== $wpdb->query($seasonsql)) {
	$bblm_season_number = $wpdb->insert_id;//captured from SQL string
}
else {
	$wpdb->print_error();
}


$joinsql = 'INSERT INTO `'.$wpdb->prefix.'bb2wp` (`bb2wp_id`, `tid`, `pid`, `prefix`) VALUES (\'\',\''.$bblm_season_number.'\', \''.$bblm_post_number.'\', \'sea_\')';

	if (FALSE !== $wpdb->query($joinsql)) {
		$sucess = TRUE;
	}
	else {
		$wpdb->print_error();
	}

	// Now we flush the re-write rules to make them regenerate the rules to include our new page.
	$wp_rewrite->flush_rules();
?>
	<div id="updated" class="updated fade">
	<p>
	<?php
	if ($sucess) {
		print("Season has been Created. <a href=\"".$bblm_guid."\" title=\"View the new Season\">View page</a>");
	}
	else {
		print("Something went wrong");
	}


?>
</p>
	</div>
<?php
}//end of submit if
?>

	<form name="bblm_test" method="post" id="post">

	<fieldset id='addseasondiv'><legend>Season Details</legend>
		<div>
		  <label for="bblm_sname" class="selectit">Season Name</label>
		  <input type="text" name="bblm_sname" size="40" tabindex="1" value="" id="bblm_sname">

		  <label for="bblm_ssdate" class="selectit">Start Date</label>
	      <input type="text" name="bblm_ssdate" size="11" value="<?php print(date('Y-m-d')); ?>" maxlength="10">
		</div>
	</fieldset>

	<fieldset id="postdivrich"><legend>Season description</legend>
	<script type="text/javascript" src="../wp-includes/js/tinymce/tiny_mce.js"></script>
	<script type="text/javascript">
	<!--
		tinyMCE.init({
		theme : "advanced",
		mode : "exact",
		elements : "bblm_sdesc",
		width : "565",
		height : "200"
		});
	-->
	</script>

	<div>
	  <textarea class='mceEditor' rows='10' cols='40' name='bblm_sdesc' tabindex='2' id='bblm_sdesc'></textarea>
	</div>
	</fieldset>

	<p class="submit">
	<input type="submit" name="bblm_season_submit" tabindex="4" value="Create Season" title="Create the Season"/>
	</p

	</form>
</div>