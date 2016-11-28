<?php
/*
Template Name: List Teams
*/
/*
*	Filename: bb.core.teams.php
*	Version: 1.4.1
*	Description: Page template to list the teams.
*/
/* -- Change History --
20080405 - 0.1a - Intital creation of file
20080419 - 0.2a - changed the sql so only hdwsbbl teams are shown.
20080429 - 0.1b - moved into beta. improved SQL (race details and sorted by type, active, name from wp tbl notbb tbl)
20080505 - 1.0b - Moved the teams into a table and split them by Type and Status
20080604 - 1.0b - Added Team Value to the table
20080610 - 1.1b - removed rogue | at bottom of page and added some casses to the tables. also added some classes to the tables
20080611 - 1.1.1b - Some smal formatting changes
20080730 - 1.0 - bump to Version 1 for public release.
20080915 - 1.0 (0.1b) - Added tbody and thead so that the table is stable when being sorted.
			   - Each team has its race logo by its side.
20081226 - 1.1 - finished cup code, reformatted css for team table, added games played and if the team has a custom small image it is displayed insted of the generic race logo.
20090330 - 1,2 - Editied to filter out non hdwsbbl details
20090331 - 1.3 - Fixed the bug that was stopping the custom image from displaying
				 implemented DYK on the page
20100123 - 1.4 - Updated the prefix for the custom bb tables in the Database (tracker [225])
20100831 - 1.4.1 - Added the t_id to the TR value of the table and impreoved image Alt text

*/
?>
<?php get_header(); ?>
	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
		<div id="breadcrumb">
			<p><a href="<?php echo home_url(); ?>" title="Back to the front of the HDWSBBL">HDWSBBL</a> &raquo; Teams</p>
		</div>
			<div class="entry">
				<h2><?php the_title(); ?></h2>

				<?php the_content('Read the rest of this entry &raquo;'); ?>

<?php
	//Start of Custom content
	//$teamsql = "SELECT P.post_title, P.guid FROM '.$wpdb->prefix.'team AS R, $wpdb->posts AS P, '.$wpdb->prefix.'bb2wp AS J WHERE R.t_id = J.tid AND P.ID = J.pid AND J.prefix = 't_' AND R.t_show = 1 ORDER BY t_name ASC";
	$teamsql = 'SELECT P.post_title, R.r_name, R.r_id, P.guid, T.t_active, T.t_tv, T.t_sname, X.type_name, T.t_id FROM '.$wpdb->prefix.'team T, '.$wpdb->posts.' P, '.$wpdb->prefix.'bb2wp J, '.$wpdb->prefix.'race R, '.$wpdb->prefix.'team_type X WHERE T.type_id = X.type_id AND R.r_id = T.r_id AND T.t_id = J.tid AND P.ID = J.pid AND J.prefix = \'t_\' AND T.t_show = 1 AND T.type_id = 1 ORDER BY T.type_id ASC, T.t_active DESC, P.post_title ASC';


if ($teams = $wpdb->get_results($teamsql)) {
	$is_first_status = 0;
	$current_status = "";
	$is_first_type = 1;
	$current_type = "";

	//print("<table>\n	<tr>\n		<th>Team</th>\n		<th>Race</th>\n	</tr>\n");
	$zebracount = 1;
	foreach ($teams as $team) {
		if ($team->type_name !== $current_type) {
			$current_type = $team->type_name;
			$current_status = $team->t_active;
			if (1 !== $is_first_type) {
				print(" 	</tbody>\n	</table>\n");
				$zebracount = 1;
			}
			$is_first_type = 1;
		}
		if ($team->t_active !== $current_status) {
			$current_status = $team->t_active;
			if (1 !== $is_first_status) {
				print(" 	</tbody>\n	</table>\n");
				$zebracount = 1;
			}
			$is_first_status = 1;
		}

		if (1 == $current_status) {
			$status_title = "Active Teams";
		}
		else {
			$status_title = "Inactive Teams";
		}


		if ($is_first_type) {
			print("<h3>".$team->type_name." Teams</h3>\n <h4>".$status_title."</h4>\n  <table class=\"sortable\">\n	<thead>\n	<tr>\n		<th>&nbsp</th>\n		<th class=\"tbl_name\">Team</th>\n		<th class=\"tbl_teamrace\">Race</th>\n		<th class=\"tbl_teamvalue\">Team Value</th>\n		<th class=\"tbl_stat\">Games</th>\n		<th class=\"tbl_teamcup\">Championships</th>\n	</tr>\n	</thead>\n	<tbody>\n");
			$is_first_type = 0;
			$is_first_status = 0;
		}
		if ($is_first_status) {
			print("<h4>".$status_title."</h4>\n  <table class=\"sortable\">\n	<thead>\n	<tr>\n		<th>&nbsp</th>\n		<th class=\"tbl_name\">Team</th>\n		<th class=\"tbl_teamrace\">Race</th>\n		<th class=\"tbl_teamvalue\">Team Value</th>\n		<th class=\"tbl_stat\">Games</th>\n		<th class=\"tbl_teamcup\">Championships</th>\n	</tr>\n	</thead>\n	<tbody>\n");
			$is_first_status = 0;
		}
		if ($zebracount % 2) {
			print("		<tr id=\"".$team->t_id."\">\n");
		}
		else {
			print("		<tr class=\"tbl_alt\" id=\"".$team->t_id."\">\n");
		}
		print("		<td>");

		$options = get_option('bblm_config');
		$site_dir = htmlspecialchars($options['site_dir'], ENT_QUOTES);


		//$filename = $_SERVER['DOCUMENT_ROOT']."/".$site_dir."/images/teams/".$team->t_sname."_small.gif";
		$filename = $_SERVER['DOCUMENT_ROOT']."/images/teams/".$team->t_sname."_small.gif";
		if (file_exists($filename)) {
			print("<img src=\"".home_url()."/images/teams/".$team->t_sname."_small.gif\" alt=\"".$team->t_sname." Logo\" />");
		}
		else {
			print("<img src=\"".home_url()."/images/races/race".$team->r_id."_small_fade.gif\" alt=\"".$team->r_name." Race Logo\" />");
		}
		print("</td>\n		<td><a href=\"".$team->guid."\" title=\"View more informaton about ".$team->post_title."\">".$team->post_title."</a></td>\n		<td>".$team->r_name."</td>\n		<td>".number_format($team->t_tv)."gp</td>\n");


		$nummatchsql = 'SELECT COUNT(*) AS NMATCH FROM '.$wpdb->prefix.'match_team T, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C WHERE T.m_id = M.m_id AND M.c_id = C.c_id AND C.c_counts = 1 AND T.t_id = '.$team->t_id;
		$nummatch = $wpdb->get_var($nummatchsql);
		//If not more than 1 then team is new, set to 0 as the default result will be null).
		if (NULL == $nummatch) {
			$nummatch = 0;
		}

		print("		<td>".$nummatch."</td>\n");

		$cupscountsql = 'SElECT B.a_id, A.a_name, COUNT(*) AS ANUM FROM '.$wpdb->prefix.'awards_team_comp AS B, '.$wpdb->prefix.'awards AS A WHERE A.a_id = B.a_id AND (B.a_id = 1 or B.a_id = 2 or B.a_id = 3) AND B.t_id = '.$team->t_id.' GROUP BY B.a_id ORDER BY B.a_id ASC';
		if ($cups = $wpdb->get_results($cupscountsql)) {
			print("		<td class=\"tbl_teamcup\">");
			foreach ($cups as $cup) {
			print("<img src=\"".home_url()."/images/misc/cup".$cup->a_id."-".$cup->ANUM.".gif\" alt=\"".$cup->ANUM." ".$cup->a_name." Trophy\" />");
			}
			print("</td>\n	</tr>\n");
		}
		else {
			//No Cups won, or error
			print("		<td>&nbsp;</td>\n	</tr>\n");
		}

		$zebracount++;
	}
	print("	</tbody>\n	</table>\n");
}
else {
	print("	<div class=\"info\">\n		<p>There are no Teams currently set-up!</p>	</div>\n");
}

//End of Custom content

		//Did You Know Display Code
		if (function_exists(bblm_display_dyk)) {
			bblm_display_dyk();
		}
?>


				<p class="postmeta"><?php edit_post_link('Edit', ' <strong>[</strong> ', ' <strong>]</strong> '); ?></p>

			</div>


		<?php endwhile; else: ?>
			<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
		<?php endif; ?>


<?php get_sidebar(); ?>
<?php get_footer(); ?>
