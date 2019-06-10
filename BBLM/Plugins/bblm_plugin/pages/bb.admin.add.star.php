<?php
/**
 * BBowlLeagueMan Add Star Player
 *
 * Page used to add a new Star Player to the league
 *
 * @author 		Blacksnotling
 * @category 	Core
 * @package 	BBowlLeagueMan/Pages
 */

//Check the file is not being accessed directly
if (!function_exists('add_action')) die('You cannot run this file directly. Naughty Person');
?>
<div class="wrap">
	<h2>Add a Star Player</h2>
	<p>Use the following form to add a Star Playeer to the League.</p>
<?php
if(isset($_POST['bblm_star_submit'])) {

	//Determine the parent page
	$options = get_option('bblm_config');
	$bblm_page_parent = htmlspecialchars($options['page_stars'], ENT_QUOTES);

	//Determine other need options
	$bblm_race_star = htmlspecialchars($options['race_star'], ENT_QUOTES);
	$bblm_team_star = bblm_get_star_player_team();

	//Determine Star Position
	$posnumsql = "SELECT pos_id FROM ".$wpdb->prefix."position WHERE r_id = ".$bblm_race_star;
	$posnum = $wpdb->get_var($posnumsql);

	$pdesc = wp_filter_nohtml_kses($_POST['bblm_pname'])." is a Star Player!";

	$my_post = array(
		'post_title' => wp_filter_nohtml_kses($_POST['bblm_pname']),
		'post_content' => $pdesc,
		'post_type' => 'page',
		'post_status' => 'publish',
		'comment_status' => 'closed',
		'ping_status' => 'closed',
		'post_parent' => $bblm_page_parent
	);
	if ($bblm_submission = wp_insert_post( $my_post )) {
		add_post_meta($bblm_submission, '_wp_page_template', 'bb.view.starplayer.php');

		$bblmdatasql = 'INSERT INTO `'.$wpdb->prefix.'player` (`p_id`, `t_id`, `pos_id`, `p_name`, `p_num`, `p_ma`, `p_st`, `p_ag`, `p_av`, `p_spp`, `p_skills`, `p_mng`, `p_injuries`, `p_cost`, `p_cost_ng`, `p_status`, `p_img`, `p_former`) VALUES (\'\', \''.$bblm_team_star.'\', \''.$posnum.'\', \''.wp_filter_nohtml_kses($_POST['bblm_pname']).'\', \'00\', \''.$_POST['bblm_pma'].'\', \''.$_POST['bblm_pst'].'\', \''.$_POST['bblm_pag'].'\', \''.$_POST['bblm_pav'].'\', \'0\', \''.$_POST['bblm_pskills'].'\', \'0\', \'none\', \''.$_POST['bblm_pcost'].'\', \''.$_POST['bblm_pcost'].'\', \'1\', \'\', \'0\')';
		$wpdb->query($bblmdatasql);

		//Store the player ID (p_id)
		$bblm_player_id = $wpdb->insert_id;

		$bblmmappingsql = 'INSERT INTO `'.$wpdb->prefix.'bb2wp` (`bb2wp_id`, `tid`, `pid`, `prefix`) VALUES (\'\',\''.$bblm_player_id.'\', \''.$bblm_submission.'\', \'p_\')';
		$wpdb->query($bblmmappingsql);

		$success = 1;
		$addattempt = 1;
		do_action( 'bblm_post_submission' );

		//Now we populate the race2star table in the database
		$p = 1;
		$race2starsqla = array();
		while ($p <= $_POST[bblm_numofraces]){
			//if  "on" result for a field then generate SQL
			if (on == $_POST[bblm_plyd.$p]) {

				$insertstarracesql = 'INSERT INTO `'.$wpdb->prefix.'race2star` (`r_id`, `p_id`) VALUES (\''.$_POST[bblm_raceid.$p].'\', \''.$bblm_player_id.'\')';
				$race2starsqla[$p] = $insertstarracesql;
			}
			$p++;
		}

		foreach ($race2starsqla as $ps) {
			$addstar2race = $wpdb->query($ps);
		}
	} //end of if post insertion was successful

?>
	<div id="updated" class="updated fade">
		<p>
<?php
	if ($success) {
		print("Star Player has been created. <a href=\"".get_permalink($bblm_submission)."\" title=\"View the new Star Player\">View page</a>");
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
	<form name="bblm_addstar" method="post" id="post">
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="bblm_pname">Star Name</label></th>
				<td><input type="text" name="bblm_pname" size="30" maxlength="30" value="" id="bblm_pname" class="regular-text"/></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="bblm_pma">MA</label></th>
				<td><input type="text" name="bblm_pma" size="2" value="6" maxlength="1" id="bblm_pma" class="small-text"/></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="bblm_pst">ST</label></th>
				<td><input type="text" name="bblm_pst" size="2" value="4" maxlength="1" id="bblm_pst" class="small-text"/></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="bblm_pag">AG</label></th>
				<td><input type="text" name="bblm_pag" size="2" value="4" maxlength="1" id="bblm_pag" class="small-text"/></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="bblm_pav">AV</label></th>
				<td><input type="text" name="bblm_pav" size="2" value="8" maxlength="2" id="bblm_pav" class="small-text"/></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="bblm_pcost">Cost</label></th>
				<td><input type="text" name="bblm_pcost" size="10" value="100000" maxlength="6" id="bblm_pcost" class="regular-text"/>gp</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="bblm_pskills">Skills</label></th>
				<td><p><textarea rows="10" cols="50" name="bblm_pskills" id="bblm_pskills" class="large-text"></textarea></p></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="bblm_plyd">Can play for</label></th>
<?php
		$namesql = 'SELECT * FROM `'.$wpdb->prefix.'race` WHERE r_show = 1 order by r_name';
		if ($names = $wpdb->get_results($namesql)) {
			//initiate var for count
			$p = 1;
			print("				<td>\n");
			foreach ($names as $name) {
				print("				<input type=\"checkbox\" name=\"bblm_plyd".$name->r_id."\"/> ".$name->r_name." <input type=\"hidden\" name=\"bblm_raceid".$p."\" id=\"bblm_raceid".$p."\" value=\"".$name->r_id."\"><br/>\n");
				$p++;
			}
			print("</td>\n");
		}
?>
			</tr>
		</table>
		<input type="hidden" name="bblm_numofraces" id="bblm_numofraces" value="<?php print($p-1); ?>">
		<p class="submit"><input type="submit" name="bblm_star_submit" value="Add Star" title="Add the Star" class="button-primary"/></p>
	</form>
</div>
