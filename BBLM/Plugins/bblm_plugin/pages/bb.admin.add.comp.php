<?php
/**
 * BBowlLeagueMan Add Competition
 *
 * Page used to add a new Competition to the league
 *
 * @author 		Blacksnotling
 * @category 	Core
 * @package 	BBowlLeagueMan/Pages
 */

//Check the file is not being accessed directly
if (!function_exists('add_action')) die('You cannot run this file directly. Naughty Person');

?>
<div class="wrap">
	<h2>New Competition</h2>
	<p>Use the following page to start a new competition in the League.</p>

<?php
if(isset($_POST['bblm_comp_submit'])) {

	//pull parent page from somewhere
	$options = get_option('bblm_config');
	$bblm_page_parent = htmlspecialchars($options['page_comp'], ENT_QUOTES);

	$my_post = array(
		'post_title' => wp_filter_nohtml_kses($_POST['bblm_cname']),
		'post_content' => wp_filter_kses($_POST['bblm_cdesc']),
		'post_type' => 'page',
		'post_status' => 'publish',
		'comment_status' => 'closed',
		'ping_status' => 'closed',
		'post_parent' => $bblm_page_parent
	);
	if ($bblm_submission = wp_insert_post( $my_post )) {
		add_post_meta($bblm_submission, '_wp_page_template', 'bb.view.comp.php');

		$bblm_csdate = $_POST['bblm_csdate'] ." 17:00:01";
		$bblmdatasql = 'INSERT INTO `'.$wpdb->prefix.'comp` (`c_id`, `c_name`, `series_id`, `sea_id`, `ct_id`, `c_active`, `c_counts`, `c_pW`, `c_pL`, `c_pD`, `c_ptd`, `c_pcas`, `c_pround`, `c_sdate`, `c_edate`, `c_showstandings`, `c_show`, `type_id`) VALUES (\'\', \''.wp_filter_nohtml_kses($_POST['bblm_cname']).'\', \''.$_POST['bblm_cseries'].'\', \''.$_POST['bblm_cseason'].'\', \''.$_POST['bblm_ctype'].'\', \'1\', \''.$_POST['bblm_counts'].'\', \''.$_POST['bblm_pwin'].'\', \''.$_POST['bblm_ploss'].'\', \''.$_POST['bblm_pdraw'].'\', \''.$_POST['bblm_ptd'].'\', \''.$_POST['bblm_pcas'].'\', \''.$_POST['bblm_pround'].'\', \''.$bblm_csdate.'\', \'0000-00-00 00:00:00\', \'0\', \'1\', \'1\')';
		$wpdb->query($bblmdatasql);

		$bblmmappingsql = 'INSERT INTO `'.$wpdb->prefix.'bb2wp` (`bb2wp_id`, `tid`, `pid`, `prefix`) VALUES (\'\',\''.$wpdb->insert_id.'\', \''.$bblm_submission.'\', \'c_\')';
		$wpdb->query($bblmmappingsql);

		//Add a new Term to the database
		wp_insert_term(
			wp_filter_nohtml_kses($_POST['bblm_cname']), // the term
			'post_competitions' // the taxonomy
		);

		$success = 1;
		$addattempt = 1;
		do_action( 'bblm_post_submission' );

	} //end of if post insertion was successful

?>
	<div id="updated" class="updated fade">
		<p>
	<?php
	if ($success) {
		print("Competition has been started. <a href=\"".get_permalink($bblm_submission)."\" title=\"View the new Championship\">View page</a>");
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
?>

	<form name="bblm_addcomp" method="post" id="post">

		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="bblm_cname">Competition Name</label></th>
				<td><input type="text" name="bblm_cname" size="50" value="" id="bblm_cname" maxlength="50" class="regular-text"/></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="bblm_cseries">Championship</label></th>
				<td><select name="bblm_cseries" id="bblm_cseries">
		<?php
		$seriessql = 'SELECT series_id, series_name FROM '.$wpdb->prefix.'series order by series_name';

		if ($seriess = $wpdb->get_results($seriessql)) {
			foreach ($seriess as $cup) {
				print("					<option value=\"$cup->series_id\">".$cup->series_name."</option>\n");
			}
		}
		?>
		</select></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="bblm_cseason">Season</label></th>
				<td><select name="bblm_cseason" id="bblm_cseason">
		<?php
		$seasonsql = 'SELECT sea_id, sea_name FROM '.$wpdb->prefix.'season WHERE sea_active = 1 order by sea_name';

		if ($seasons = $wpdb->get_results($seasonsql)) {
			foreach ($seasons as $sea) {
				print("					<option value=\"$sea->sea_id\">".$sea->sea_name."</option>\n");
			}
		}
		?>
		</select></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="bblm_ctype">Format</label></th>
				<td><select name="bblm_ctype" id="bblm_ctype">
		<?php
		$typesql = 'SELECT ct_id, ct_name FROM '.$wpdb->prefix.'comp_type ORDER BY ct_name';

		if ($types = $wpdb->get_results($typesql)) {
			foreach ($types as $type) {
				print("					<option value=\"$type->ct_id\">".$type->ct_name."</option>\n");
			}
		}
		?>
		</select></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="bblm_csdate">Start Date (YYYY-MM-DD)</label></th>
				<td><input type="text" name="bblm_csdate" size="11" value="<?php print(date('Y-m-d')); ?>" maxlength="10" class="regular-text"/></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="bblm_cdesc">Description</label></th>
				<td><p><textarea rows="10" cols="50" name="bblm_cdesc" id="bblm_cdesc" class="large-text"></textarea></p></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="bblm_pwin">Points for a Win</label></th>
				<td><input type="text" name="bblm_pwin" size="2" value="0" maxlength="2" class="regular-text"/></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="bblm_pdraw">Points for a Draw</label></th>
				<td><input type="text" name="bblm_pdraw" size="2" value="0" maxlength="2" class="regular-text"/></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="bblm_ploss">Points for a Loss</label></th>
				<td><input type="text" name="bblm_ploss" size="2" value="0" maxlength="2" class="regular-text"/></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="bblm_ptd">League points per TD</label></th>
				<td><input type="text" name="bblm_ptd" size="2" value="0" maxlength="2" class="regular-text"/></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="bblm_pcas">League points per CAS</label></th>
				<td><input type="text" name="bblm_pcas" size="2" value="0" maxlength="2" class="regular-text"/></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="bblm_counts">Does the competition count?</label></th>
				<td><select name="bblm_counts" id="bblm_counts">
					<option value="1">Yes - Games played in this competition count towards statistics</option>
					<option value="0">No - This competition is for exhibition / friendly games</option>
				</select></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="bblm_pround">Round points by number of games played?</label></th>
				<td><select name="bblm_pround" id="bblm_pround">
					<option value="0">No</option>
					<option value="1">Yes</option>
				</select></td>
			</tr>
		</table>

	<p class="submit"><input type="submit" name="bblm_comp_submit" id="bblm_comp_submit" value="Start Competition" title="Add the Competition" class="button-primary"/></p>

	</form>
</div>
