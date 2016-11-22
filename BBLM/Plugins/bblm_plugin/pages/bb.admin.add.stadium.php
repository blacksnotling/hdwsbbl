<?php
/*
*	Filename: bb.admin.add.stadium.php
*	Version: 1.1
*	Description: Page used to add a new Stadium to the league
*/
/* -- Change History --
20080402 - 1.0b - Initial creation of file
20080403 - 1.1b - Corrected the sqL to display the existing stadiums
20080405 - 1.2b - checked for any refrences to dev_ rather then the correct $wpdb->
20080730 - 1.0 - bump to Version 1 for public release.
20100124 - 1.1 - Updated the prefix for the custom bb tables in the Database (tracker [224])

*/

//Check the file is not being accessed directly
if (!function_exists('add_action')) die('You cannot run this file directly. Naughty Person');

?>
<div class="wrap">
	<h2>Add a Stadium</h2>
	<p>Use the following form to add a new Race to the HDWSBBL.</p>

<?php
if(isset($_POST['bblm_stadium_submit'])) {
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

	//generate time NOW.
	$bblm_date_now = date('Y-m-j H:i:59');

	//pull parent page from somewhere
	$options = get_option('bblm_config');
	$bblm_page_parent = htmlspecialchars($options['page_stadium'], ENT_QUOTES);

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
	$bblm_guid .= "/stadiums/";
	$bblm_guid .= $bblm_page_slug;



$postsql = 'INSERT INTO '.$wpdb->posts.' (`ID`, `post_author`, `post_date`, `post_date_gmt`, `post_content`, `post_title`, `post_category`, `post_excerpt`, `post_status`, `comment_status`, `ping_status`, `post_password`, `post_name`, `to_ping`, `pinged`, `post_modified`, `post_modified_gmt`, `post_content_filtered`, `post_parent`, `guid`, `menu_order`, `post_type`, `post_mime_type`, `comment_count`) VALUES (\'\', \'1\', \''.$bblm_date_now.'\', \''.$bblm_date_now.'\', \''.$bblm_page_content.'\', \''.$bblm_page_title.'\', \'0\', \'\', \'publish\', \'closed\', \'closed\', \'\', \''.$bblm_page_slug.'\', \'\', \'\', \''.$bblm_date_now.'\', \''.$bblm_date_now.'\', \'\', \''.$bblm_page_parent.'\', \''.$bblm_guid.'\', \'0\', \'page\', \'\', \'0\')';



if (FALSE !== $wpdb->query($postsql)) {
	$bblm_post_number = $wpdb->insert_id;//captured from SQL string
}
else {
	$wpdb->print_error();
}

$postmetasql = 'INSERT INTO '.$wpdb->postmeta.' (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES (\'\', \''.$bblm_post_number.'\', \'_wp_page_template\', \'bb.view.stadium.php\')';

if (FALSE !== $wpdb->query($postmetasql)) {
	$sucess = TRUE;
}
else {
	$wpdb->print_error();
}

$stadiumsql = 'INSERT INTO `'.$wpdb->prefix.'stadium` (`stad_id`, `stad_name`, `stad_img`) VALUES (\'\', \''.$bblm_page_title.'\', \'\')';

if (FALSE !== $wpdb->query($stadiumsql)) {
	$bblm_stadium_number = $wpdb->insert_id;//captured from SQL string
}
else {
	$wpdb->print_error();
}

$joinsql = 'INSERT INTO `'.$wpdb->prefix.'bb2wp` (`bb2wp_id`, `tid`, `pid`, `prefix`) VALUES (\'\', \''.$bblm_stadium_number.'\', \''.$bblm_post_number.'\', \'stad_\')';

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
		print("Stadium has been added. <a href=\"".$bblm_guid."\" title=\"View the new Stadium\">View page</a>");
	}
	else {
		print("Something went wrong");
	}
?>
</p>
	</div>
<?php
}
?>

	<form name="bblm_test" method="post" id="post">

	<fieldset id='addstadiumdiv'><legend>Stadium</legend>
		<div>
		  <label for="bblm_sname" class="selectit">Stadium Name</label>
		  <input type="text" name="bblm_sname" size="20" tabindex="1" value="" maxlength="30" id="bblm_sname">
		</div>
	</fieldset>

	<fieldset id="postdivrich"><legend>Stadium Description</legend>
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
	<input type="submit" name="bblm_stadium_submit" tabindex="4" value="Add Stadium" title="Add the Stadium"/>
	</p

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