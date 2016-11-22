<?php
/*
*	Filename: bb.admin.add.comp.php
*	Version: 1.2b
*	Description: Page used to add a new Competition to the BBLM.
*/
/* -- Change History --
20080310 - 1.0b - Initial creation of file.
20080311 - 1.1b - Corrected one of the sql so correct Prefix was inserted!
20080407 - 1.2b - Ammended input to take into account new c_showstandings field in db

*/

//Check the file is not being accessed directly
if (!function_exists('add_action')) die('You cannot run this file directly. Naughty Person');

?>
<div class="wrap">
	<h2>Start a competition</h2>
	<p>Use the following form to Start a new competition in the HDWSBBL.</p>

<?php
if(isset($_POST['bblm_comp_submit'])) {
	print("<pre>");
	print_r($_POST);
	print("</pre>");
	print("<hr />");


	$bblm_safe_input = array();

	//think about a for each
	if (get_magic_quotes_gpc()) {
		$_POST['bblm_cname'] = stripslashes($_POST['bblm_cname']);
		$_POST['bblm_cdesc'] = stripslashes($_POST['bblm_cdesc']);
		$_POST['bblm_sname'] = stripslashes($_POST['bblm_sname']);

	}

	$bblm_safe_input['cname'] = $wpdb->escape($_POST['bblm_cname']);
	$bblm_safe_input['cdesc'] = $wpdb->escape($_POST['bblm_cdesc']);
	$bblm_safe_input['sname'] = $wpdb->escape($_POST['bblm_sname']);


	$bblm_safe_input['series'] = $_POST['bblm_cseries'];
	$bblm_safe_input['season'] = $_POST['bblm_cseason'];
	$bblm_safe_input['type'] = $_POST['bblm_ctype'];
	$bblm_safe_input['pwin'] = $_POST['bblm_pwin'];
	$bblm_safe_input['ploss'] = $_POST['bblm_ploss'];
	$bblm_safe_input['pdraw'] = $_POST['bblm_pdraw'];
	$bblm_safe_input['ptd'] = $_POST['bblm_ptd'];
	$bblm_safe_input['pcas'] = $_POST['bblm_pcas'];
	$bblm_safe_input['pround'] = $_POST['bblm_pround'];
	$bblm_safe_input['csdate'] = $_POST['bblm_csdate'] ." 00:00:00";

	//generate time NOW. (for page)
	$bblm_date_now = date('Y-m-j H:i:59');

	//pull parent page from somewhere
	$options = get_option('bblm_config');
	$bblm_page_parent = htmlspecialchars($options['page_comp'], ENT_QUOTES);

	//filter page body
	$bblm_removable = array("<pre>","</pre>","<p>","&nbsp;");
	$bblm_page_content = $bblm_safe_input['cdesc'];
	$bblm_page_content = str_replace($bblm_removable,"",$bblm_page_content);
	$bblm_page_content = str_replace("</p>","\n\n",$bblm_page_content);

	//snaitise page title
	$bblm_page_title = $bblm_safe_input['cname'];

	//convert page title to slug
	$bblm_page_slug = sanitize_title($bblm_safe_input['sname']);

	//generate GUID
	$bblm_guid = get_bloginfo('wpurl');
	$bblm_guid .= "/competitions/";
	$bblm_guid .= $bblm_page_slug;

$postsql = 'INSERT INTO '.$wpdb->posts.' (`ID`, `post_author`, `post_date`, `post_date_gmt`, `post_content`, `post_title`, `post_category`, `post_excerpt`, `post_status`, `comment_status`, `ping_status`, `post_password`, `post_name`, `to_ping`, `pinged`, `post_modified`, `post_modified_gmt`, `post_content_filtered`, `post_parent`, `guid`, `menu_order`, `post_type`, `post_mime_type`, `comment_count`) VALUES (\'\', \'1\', \''.$bblm_date_now.'\', \''.$bblm_date_now.'\', \''.$bblm_page_content.'\', \''.$bblm_page_title.'\', \'0\', \'\', \'publish\', \'closed\', \'closed\', \'\', \''.$bblm_page_slug.'\', \'\', \'\', \''.$bblm_date_now.'\', \''.$bblm_date_now.'\', \'\', \''.$bblm_page_parent.'\', \''.$bblm_guid.'\', \'0\', \'page\', \'\', \'0\')';
$compsql = 'INSERT INTO `bb_comp` (`c_id`, `c_name`, `series_id`, `sea_id`, `ct_id`, `c_active`, `c_counts`, `c_pW`, `c_pL`, `c_pD`, `c_ptd`, `c_pcas`, `c_pround`, `c_sdate`, `c_edate`, `c_showstandings`) VALUES (\'\', \''.$bblm_page_title.'\', \''.$bblm_safe_input['series'].'\', \''.$bblm_safe_input['season'].'\', \''.$bblm_safe_input['type'].'\', \'1\', \'1\', \''.$bblm_safe_input['pwin'].'\', \''.$bblm_safe_input['ploss'].'\', \''.$bblm_safe_input['pdraw'].'\', \''.$bblm_safe_input['ptd'].'\', \''.$bblm_safe_input['pcas'].'\', \'1\', \''.$bblm_safe_input['csdate'].'\', \'0000-00-00 00:00:00\', \'1\')';
print("<p>".$compsql."</p>");

/* Commented out while testing changes
if (FALSE !== $wpdb->query($postsql)) {
	$bblm_post_number = $wpdb->insert_id;//captured from SQL string
}
else {
	$wpdb->print_error();
}

$postmetasql = 'INSERT INTO '.$wpdb->postmeta.' (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES (\'\', \''.$bblm_post_number.'\', \'_wp_page_template\', \'bb.view.comp.php\')';

if (FALSE !== $wpdb->query($postmetasql)) {
	$sucess = TRUE;
}
else {
	$wpdb->print_error();
}


//compsql moved up top!

if (FALSE !== $wpdb->query($compsql)) {
	$bblm_comp_number = $wpdb->insert_id;//captured from SQL string
}
else {
	$wpdb->print_error();
}

$joinsql = 'INSERT INTO `bb2wp` (`bb2wp_id`, `tid`, `pid`, `prefix`) VALUES (\'\', \''.$bblm_comp_number.'\', \''.$bblm_post_number.'\', \'c_\')';

if (FALSE !== $wpdb->query($joinsql)) {
	$sucess = TRUE;
}
else {
	$wpdb->print_error();
}

// Now we flush the re-write rules to make them regenerate the rules to include our new page.
	$wp_rewrite->flush_rules();

*/
?>
	<div id="updated" class="updated fade">
	<p>
	<?php
	if ($sucess) {
		print("Competition has been added. <a href=\"".$bblm_guid."\" title=\"View the new Competition\">View page</a>");
	}
	else {
		print("Something went wrong");
	}
	?>
</p>
	</div>
<?php
//end of submit if
}
?>

	<form name="bblm_addcomp" method="post" id="post">

	<fieldset id='addcompdiv'><legend>Competition Details</legend>
		<div>
		  <label for="bblm_cname" class="selectit">Competition title</label>
		  <input type="text" name="bblm_cname" size="50" tabindex="1" value="" id="bblm_cname" maxlength="50">

		  <label for="bblm_sname" class="selectit">Slug</label>
		  <input type="text" name="bblm_sname" size="50" tabindex="2" value="" id="bblm_sname" maxlength="50">

		  <label for="bblm_cseries" class="selectit">Cup Series</label>
		  <select name="bblm_cseries" id="bblm_cseries">
		<?php
		$seriessql = 'SELECT series_id, series_name FROM bb_series order by series_name';

		if ($seriess = $wpdb->get_results($seriessql)) {
			foreach ($seriess as $cup) {
				print("<option value=\"$cup->series_id\">".$cup->series_name."</option>\n");
			}
		}
		?>
		</select>

		  <label for="bblm_cseason" class="selectit">Season</label>
		  <select name="bblm_cseason" id="bblm_cseason">
		<?php
		$seasonsql = 'SELECT sea_id, sea_name FROM bb_season WHERE sea_active = 1 order by sea_name';

		if ($seasons = $wpdb->get_results($seasonsql)) {
			foreach ($seasons as $sea) {
				print("<option value=\"$sea->sea_id\">".$sea->sea_name."</option>\n");
			}
		}
		?>
		</select>

		  <label for="bblm_ctype" class="selectit">Type</label>
		  <select name="bblm_ctype" id="bblm_ctype">
		<?php
		$typesql = 'SELECT ct_id, ct_name FROM bb_comp_type ORDER BY ct_name';

		if ($types = $wpdb->get_results($typesql)) {
			foreach ($types as $type) {
				print("<option value=\"$type->ct_id\">".$type->ct_name."</option>\n");
			}
		}
		?>
		</select>

	</fieldset>

	<fieldset id="postdivrich"><legend>Competition Description</legend>
	<script type="text/javascript" src="../wp-includes/js/tinymce/tiny_mce.js"></script>
	<script type="text/javascript">
	<!--
		tinyMCE.init({
		theme : "advanced",
		mode : "exact",
		elements : "bblm_cdesc",
		width : "565",
		height : "200"
		});
	-->
	</script>

	<div>
	  <textarea class='mceEditor' rows='10' cols='40' name='bblm_cdesc' tabindex='3' id='bblm_cdesc'></textarea>
	</div>
	</fieldset>


	<fieldset id='addteamdiv2'><legend>Competition Settings</legend>


		  <label for="bblm_pwin" class="selectit">Points for winning?</label>
		  <input type="text" name="bblm_pwin" size="2" tabindex="3" value="0" maxlength="2">

		  <label for="bblm_ploss" class="selectit">Points for Losing?</label>
		  <input type="text" name="bblm_ploss" size="2" tabindex="4" value="0" maxlength="2">

		  <label for="bblm_pdraw" class="selectit">Points for Drawing</label>
		  <input type="text" name="bblm_pdraw" size="2" tabindex="5" value="0" maxlength="2">

		  <label for="bblm_ptd" class="selectit">Points for a TD</label>
		  <input type="text" name="bblm_ptd" size="1" tabindex="6" value="0" maxlength="2">

		  <label for="bblm_pcas" class="selectit">Points for a CAS</label>
		  <input type="text" name="bblm_pcas" size="2" tabindex="7" value="0" maxlength="2">

		  <label for="bblm_pround" class="selectit">Round by games? (0 = No)</label>
		  <input type="text" name="bblm_pround" size="2" tabindex="8" value="0" maxlength="1">

		  <label for="bblm_csdate" class="selectit">Start Date</label>
		  <input type="text" name="bblm_csdate" size="20" tabindex="9" value="YYYY:MM:DD 00:00:01" maxlength="2">

		  <!-- Place holder for end date -->

		</div>
	</fieldset>



	<p class="submit">
	<input type="submit" name="bblm_comp_submit" tabindex="4" value="Start Competition" title="Add the Competition"/>
	</p>

	</form>
</div>