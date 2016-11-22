<?php
/*
*	Filename: bb.admin.edit.match.php
*	Version: 1.2
*	Description: Link page to add.edit match reports, coachs comments and match trivia.
*/
/* -- Change History --
20080526 - 0.1b - Initial creation of file.
20080719 - 0.2b - added the edit button for match comments
20080730 - 1.0 - bump to Version 1 for public release.
20080804 - 1.1 - consolidated edit,match_comments and edit.match_trivia into one page. re-designed the main table to match WP styles.
20080806 - 1.1.1 - added stripslashes to the output of the coaches comments (for editing)
20100308 - 1.2 - Updated the prefix for the custom bb tables in the Database (tracker [224])

*/
?>
<div class="wrap">
	<h2>Edit Matches</h2>
<?php
	//Check the file is not being accessed directly
if (!function_exists('add_action')) die('You cannot run this file directly. Naughty Person');
?>

<?php
  ////////////////////////////////////
 // Submit changes to Match Trivia //
////////////////////////////////////
if (isset($_POST['bblm_trivia_edit'])) {

	$bblm_safe_input['mtrivia'] = $wpdb->escape($_POST['matchtrivia']);

	$bblm_removable = array("<pre>","</pre>","<p>","&nbsp;", "<br>", "<br />");
	$bblm_trivia_content = $bblm_safe_input['mtrivia'];
	$bblm_trivia_content = str_replace($bblm_removable,"",$bblm_trivia_content);
	$bblm_trivia_content = str_replace("</p>","\n\n",$bblm_trivia_content);

	$updatesql = 'UPDATE `'.$wpdb->prefix.'match` SET `m_trivia` = \''.$bblm_trivia_content.'\' WHERE `m_id` = '.$_POST['mid'].' LIMIT 1';

	if (FALSE !== $wpdb->query($updatesql)) {
		$sucess = TRUE;
	}

	?>
		<div id="updated" class="updated fade">
		<p>
		<?php
		if ($sucess) {
?>
			Trivia has been updated. <a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.edit.match.php" title="Edit match details">Back to the match edit screen</a>
<?php
		}
		else {
			print("Something went wrong");
		}
		?>
	</p>
		</div>
	<?php

	//end of submit trivia
}
if (isset($_POST['bblm_comment_edit'])) {
  //////////////////////////////////////
 // Submit changes to Coach Comments //
//////////////////////////////////////
	$bblm_safe_input['mcA'] = $wpdb->escape($_POST['matchcomment1']);
	$bblm_safe_input['mcB'] = $wpdb->escape($_POST['matchcomment2']);

	//$updatesql = 'UPDATE `'.$wpdb->prefix.'match` SET `m_trivia` = \''.$bblm_trivia_content.'\' WHERE `m_id` = '.$_POST['mid'].' LIMIT 1';
	$updatesql = 'UPDATE `'.$wpdb->prefix.'match_team` SET `mt_comment` = \''.$bblm_safe_input['mcA'].'\' WHERE `m_id` = '.$_POST['mid'].' AND `t_id` = '.$_POST['team_a'].' LIMIT 1';
	$updatesql2 = 'UPDATE `'.$wpdb->prefix.'match_team` SET `mt_comment` = \''.$bblm_safe_input['mcB'].'\' WHERE `m_id` = '.$_POST['mid'].' AND `t_id` = '.$_POST['team_b'].' LIMIT 1';

	if (FALSE !== $wpdb->query($updatesql)) {
		if (FALSE !== $wpdb->query($updatesql2)) {
			$sucess = TRUE;
		}
		$sucess = TRUE;
	}

	?>
		<div id="updated" class="updated fade">
		<p>
		<?php
		if ($sucess) {
?>
			Trivia has been updated. <a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.edit.match.php" title="Edit match details">Back to the match edit screen</a>
<?php
		}
		else {
			print("Something went wrong - ".$updatesql." - ".$updatesql2);
		}
		?>
	</p>
		</div>
	<?php

	//end of submit comments
}



	  ////////////////////
	 // $_GET checking //
	////////////////////
	if ("edit" == $_GET['action']) {
		if ("trivia" == $_GET['item']) {
			//Editing match trivia
?>
	<h3>Edit Match Trivia</h3>
	<script type="text/javascript" src="../wp-includes/js/tinymce/tiny_mce.js"></script>
	<script type="text/javascript">
	<!--
		tinyMCE.init({
		theme : "advanced",
		mode : "exact",
		elements : "matchtrivia",
		width : "600",
		height : "200"
		});
	-->
	</script>
<?php
	$match_id = $_GET['id'];
	$matchsql = 'SELECT M.m_id, P.post_title, M.m_trivia FROM '.$wpdb->prefix.'match M, '.$wpdb->posts.' P, '.$wpdb->prefix.'bb2wp J WHERE M.m_id = J.tid AND J.prefix = \'m_\' AND J.pid = P.ID AND M.m_id = '.$match_id.' LIMIT 1';
	if ($m = $wpdb->get_row($matchsql)) {
			print("<p>You are editing the match trivia for <strong>".$m->post_title."</strong>:</p>\n");
			$trivia = $m->m_trivia;
			$trivia = str_replace("\n\n","</p>",$trivia);

?>
			<form name="bblm_editmatchtrivia" method="post" id="post">
				<h3>Match Trivia</h3>
				<p>Please ensure that the items below are formatted in a <strong>list</strong>.</p>
				<input type="hidden" name="mid" value="<?php print($match_id); ?>">
				<textarea name="matchtrivia" cols="80" rows="6"><?php print($trivia); ?></textarea>
				<input type="submit" name="bblm_trivia_edit" value="Save Changes"/>
			</form>
<?php
			}
		} //end of if item ==trivia
		else if ("comment" == $_GET['item']) {
			//Editing match Comments
			$match_id = $_GET['id'];
			$matchsql = 'SELECT M.m_id, UNIX_TIMESTAMP(M.m_date) AS mdate, M.c_id, M.m_teamA, M.m_teamB, D.div_name, C.c_name, T.t_name AS TA, V.t_name AS TB, R.mt_comment AS TAcomm, S.mt_comment AS TBcomm FROM '.$wpdb->prefix.'season X, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'team V, '.$wpdb->prefix.'match_team R, '.$wpdb->prefix.'match_team S, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'division D WHERE X.sea_id = C.sea_id AND M.c_id = C.c_id AND M.div_id = D.div_id AND M.m_id = R.m_id AND M.m_id = S.m_id AND M.m_teamA = R.t_id AND M.m_teamB = S.t_id AND M.m_teamA = T.t_id AND M.m_teamB = V.t_id AND M.m_id = '.$match_id;
			if ($m = $wpdb->get_row($matchsql)) {
				print("<h3>Edit Coaches Comments</h3>");
				print("<p>You are editing the Match Comments for <strong>".$m->TA." vs ".$m->TB."</strong> (".$m->c_name.", ".$m->div_name."):</p>\n");
?>
				<form name="bblm_editmatchcomments" method="post" id="post">
					<h3>Match Comment</h3>
					<p>please note that the comments below do <strong>not</strong> have a spell checker! Do it manually.</p>
					<input type="hidden" name="mid" value="<?php print($match_id); ?>">
					<input type="hidden" name="team_a" value="<?php print($m->m_teamA); ?>">
					<input type="hidden" name="team_b" value="<?php print($m->m_teamB); ?>">
					<table>
					<tr>
						<th><?php print($m->TA); ?></th>
						<th>Vs</th>
						<th><?php print($m->TB); ?></th>
					</tr>
					<tr>
						<td><textarea name="matchcomment1" cols="50" rows="6"><?php print(stripslashes($m->TAcomm)); ?></textarea></td>
						<td>&nbsp;</td>
						<td><textarea name="matchcomment2" cols="50" rows="6"><?php print(stripslashes($m->TBcomm)); ?></textarea></td>
					</tr>
					</table>
					<input type="submit" name="bblm_comment_edit" value="Save Changes"/>
				</form>
<?php
			}
		}//end of if item == comment
		else {
			//Catch all
			print("<p>That request was not recognised. Please try again.</p>");
		}
	}
	else {
		//Display main form
?>
	<p>Below is a list of matches in the HDWSBBL. Select the match title to edit the report or use the other links to edit the Coaches comments or match trivia.</p>

<?php
		$matchsql = 'SELECT UNIX_TIMESTAMP(M.m_date) AS mdate, M.m_id, M.m_gate, M.m_teamAtd, M.m_teamBtd, M.m_teamAcas, M.m_teamBcas, P.guid, P.post_title, S.sea_name, C.c_name, Z.guid AS cguid, D.div_name, P.ID, M.m_id FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'season S, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P, '.$wpdb->prefix.'division D, '.$wpdb->prefix.'bb2wp Y, '.$wpdb->posts.' Z WHERE C.c_id = Y.tid AND Y.prefix = \'c_\' AND Y.pid = Z.ID AND M.div_id = D.div_id AND C.sea_id = S.sea_id AND M.c_id = C.c_id AND M.m_id = J.tid AND J.prefix = \'m_\' AND J.pid = P.ID ORDER BY S.sea_id DESC, M.c_id DESC, D.div_id ASC, M.m_date DESC';
		if ($match = $wpdb->get_results($matchsql)) {
			$zebracount = 1;

			print("<table class=\"widefat\">\n	<thead>\n		 <tr>\n		   		<th scope=\"row\">ID</th>\n		   <th scope=\"col\">Match Details</th>\n		   <th scope=\"col\">Comments</th>\n		   <th scope=\"col\">Facts</th>\n		   <th scope=\"col\">View Match</th>\n		 </tr>\n	</thead>\n	<tbody>\n");
			foreach ($match as $m) {

				if ($zebracount % 2) {
					print("					<tr class=\"alternate\">\n");
				}
				else {
					print("					<tr>\n");
				}

				print("		   <td>".$m->m_id."</a></td>\n		   <td><a href=\"");

				bloginfo('url');
				print("/wp-admin/page.php?action=edit&post=".$m->ID."\">".date("d.m.y", $m->mdate)." ".$m->post_title."</a> (".$m->c_name." - ".$m->div_name.") [ ".$m->m_teamAtd." - ".$m->m_teamBtd." (".$m->m_teamAcas." - ".$m->m_teamBcas.")]</td>\n");

				print("<td><a href=\"");
				bloginfo('url');
				print("/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.edit.match.php&action=edit&item=comment&id=".$m->m_id."\" title=\"Edit the Coaches Comments\">Edit Comments</a></td>\n");

				print("<td><a href=\"");
				bloginfo('url');
				print("/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.edit.match.php&action=edit&item=trivia&id=".$m->m_id."\" title=\"Edit the Coaches Comments\">Edit Trivia</a></td>\n");

/*			print("<td><form method=\"post\" action=\"");
			bloginfo('url');
			print("/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.edit.match_comment.php\"><input type=\"hidden\" name=\"mtrivia\" value=\"".$m->m_id."\"><input type=\"submit\" name=\"bblm_comment_select\" class=\"bblm_table_submit\" value=\"Edit\"/></form></td>\n");*/

/*			print("<td><form method=\"post\" action=\"");
			bloginfo('url');
			print("/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.edit.match_trivia.php\"><input type=\"hidden\" name=\"mtrivia\" value=\"".$m->m_id."\"><input type=\"submit\" name=\"bblm_trivia_select\" class=\"bblm_table_submit\" value=\"Edit\"/></form></td>\n");*/

				print("<td><a href=\"".$m->guid."\" title=\"View the match page\">View</a></td>		 </tr>\n");
				$zebracount++;
			}
			print("	</tbody>\n</table>\n");
		}
	}//end of else action != edit
?>

</div>