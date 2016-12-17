<?php
/*
Template Name: List Fixtures
*/
/*
*	Filename: bb.core.fixtures.php
*	Version: 1.3
*	Description: Page template to list the Fixtures
*/
/* -- Change History --
20080421 - 1.0b - Initial creation of file.
20080429 - 1.1b - I added linke to the teams and the competitions.
20080615 - 1.2b - Added table classes and zebra stripes and breadcrumb
20080623 - 1.2.1b - Review of table classes
20080718 - 1.3b - Fixed dev_posts bug!
20080730 - 1.0 - bump to Version 1 for public release.
20090112 - 1.1 - Added Switch box to display Fixtures in a different layout. Also fixed xhtml validation error
20090330 - 1,2 - Editied to filter out non BBBBL details
20090712 - 1.2.1 - Added DYK code to page
20100123 - 1.3 - Updated the prefix for the custom bb tables in the Database (tracker [225])

*/
?>
<?php get_header(); ?>
	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
		<div id="breadcrumb">
			<p><a href="<?php echo get_option('home'); ?>" title="Back to the front of the BBBBL">BBBBL</a> &raquo; Fixtures</p>
		</div>
			<div class="entry">
				<h2><?php the_title(); ?></h2>

				<?php the_content('Read the rest of this entry &raquo;'); ?>

				<form name="bblm_filterlayout" method="post" id="post" action="" class="selectbox">
				<p>Order Fixtures by
					<select name="bblm_flayout" id="bblm_flayout">
						<option value="bycomp"<?php if (bycomp == $_POST['bblm_flayout']) { print(" selected=\"selected\""); } ?>>Competition</option>
						<option value="bydate"<?php if (bydate == $_POST['bblm_flayout']) { print(" selected=\"selected\""); } ?>>Date</option>
					</select>
				<input name="bblm_filter_submit" type="submit" id="bblm_filter_submit" value="Filter" /></p>
				</form>
<?php
				//Initial SQL
				$fixturesql = 'SELECT UNIX_TIMESTAMP(F.f_date) AS mdate, P.post_title AS cname, P.guid as clink, D.div_name, X.post_title AS TA, X.guid AS TAlink, Z.post_title AS TB, Z.guid AS TBlink, T.t_id AS TAid, R.t_id AS TBid, F.f_id FROM '.$wpdb->prefix.'fixture F, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P, '.$wpdb->prefix.'bb2wp W, '.$wpdb->posts.' X, '.$wpdb->prefix.'bb2wp Y, '.$wpdb->posts.' Z, '.$wpdb->prefix.'division D, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'team R WHERE C.c_id = J.tid AND J.prefix = \'c_\' AND J.pid = P.ID AND T.t_id = W.tid AND W.prefix = \'t_\' AND W.pid = X.ID AND R.t_id = Y.tid AND Y.prefix = \'t_\' AND Y.pid = Z.ID AND F.f_teamA = T.t_id AND F.f_teamB = R.t_id AND F.c_id = C.c_id AND C.type_id = 1 AND F.div_id = D.div_id AND F.f_complete = 0 ORDER BY ';

				//determine the required Layout
				if (isset($_POST['bblm_flayout'])) {
					$flay = $_POST['bblm_flayout'];
					switch ($flay) {
						case ("bycomp" == $flay):
					    	$layout .= 1;
					    	$fixturesql .= 'F.c_id DESC, F.div_id DESC, F.f_date ASC';
						    break;
						case ("bydate" == $flay):
					    	$layout .= 0;
					    	$fixturesql .= 'F.f_date ASC, F.c_id DESC, F.div_id DESC';
						    break;
						default:
					    	$layout .= 1;
					    	$fixturesql .= 'F.c_id DESC, F.div_id DESC, F.f_date ASC';
						    break;
					}
				}
				else {
					//form not submitted so load in default values
					$layout .= 1;
					$fixturesql .= 'F.c_id DESC, F.div_id DESC, F.f_date ASC';
				}


				//Run the Query. If successful....
				if ($fixture = $wpdb->get_results($fixturesql)) {

					//grab the ID of the "tbd" team
					$options = get_option('bblm_config');
					$bblm_tbd_team = htmlspecialchars($options['team_tbd'], ENT_QUOTES);

					if (1 == $layout) {
						//Load the default by Competition
						$is_first_comp = 1;
						$is_first_div = 0;
						$is_first_sea = 1;
						$current_comp = "";
						$current_div ="";
						$current_sea = "";
						$zebracount = 1;

						foreach ($fixture as $m) {
							if ($m->cname !== $current_comp) {
								$current_comp = $m->cname;
								$current_div = $m->div_name;
								if (1 !== $is_first_comp) {
									print(" </table>\n");
									$zebracount = 1;
								}
								$is_first_comp = 1;
							}
							if ($m->div_name !== $current_div) {
								$current_div = $m->div_name;
								if (1 !== $is_first_div) {
									print(" </table>\n");
									$zebracount = 1;
								}
								$is_first_div = 1;
							}
							if ($is_first_comp) {
								print("<h3><a href=\"".$m->clink."\" title=\"View more about the ".$m->cname."\">".$m->cname."</a></h3>\n <h4>".$m->div_name."</h4>\n  <table>\n		 <tr>\n		   <th class=\"tbl_matchdate\">Date</th>\n		   <th class=\"tbl_matchname\">Match</th>\n		 </tr>\n");
								$is_first_comp = 0;
								$is_first_div = 0;
							}
							if ($is_first_div) {
								print("<h4>".$m->div_name."</h4>\n  <table>\n		 <tr>\n		   <th class=\"tbl_matchdate\">Date</th>\n		   <th class=\"tbl_matchname\">Match</th>\n		   </tr>\n");
								$is_first_div = 0;
							}
							if ($zebracount % 2) {
								print("		<tr id=\"F".$m->f_id."\">\n");
							}
							else {
								print("		<tr class=\"tbl_alt\"  id=\"F".$m->f_id."\">\n");
							}
							print("		   <td>".date("d.m.y", $m->mdate)."</td>\n		<td>");
							if ($bblm_tbd_team == $m->TAid) {
								print($m->TA);
							}
							else {
								print("<a href=\"".$m->TAlink."\" title=\"Learn more about ".$m->TA."\">".$m->TA."</a>");
							}
							print(" vs ");
							if ($bblm_tbd_team == $m->TBid) {
								print($m->TB);
							}
							else {
								print("<a href=\"".$m->TBlink."\" title=\"Learn more about ".$m->TB."\">".$m->TB."</a>");
							}

							print("</td>\n	</tr>\n");
							$zebracount++;
						}
						print("</table>\n");
						//END OF LAYOUT 1 (by Comp)
					}
					else {
						//The Second Layout has been selected
						$zebracount = 1;
?>
				<table class="sortable">
					<thead>
					<tr>
						<th class="tbl_matchdate">Date</th>
						<th class="tbl_matchname">Match</th>
						<th class="tbl_name">Competition</th>
						<th class="tbl_matchname">Round</th>
					</tr>
					</thead>
					<tbody>
<?php
						foreach ($fixture as $m) {
							if ($zebracount % 2) {
								print("					<tr  id=\"F".$m->f_id."\">\n");
							}
							else {
								print("					<tr class=\"tbl_alt\"  id=\"F".$m->f_id."\">\n");
							}
?>
						<td><?php print(date("d.m.y", $m->mdate)); ?></td>
						<td>
<?php
							if ($bblm_tbd_team == $m->TAid) {
								print($m->TA);
							}
							else {
								print("<a href=\"".$m->TAlink."\" title=\"Learn more about ".$m->TA."\">".$m->TA."</a>");
							}
							print(" vs ");
							if ($bblm_tbd_team == $m->TBid) {
								print($m->TB);
							}
							else {
								print("<a href=\"".$m->TBlink."\" title=\"Learn more about ".$m->TB."\">".$m->TB."</a>");
							}
?>
						</td>
<?php

?>
						<td><a href="<?php print($m->clink); ?>" title="View more about the <?php print($m->cname); ?>"><?php print($m->cname); ?></a></td>
						<td><?php print($m->div_name); ?></td>
					</tr>
<?php
							$zebracount++;
						}//end of foreach $fixture
?>
					</tbody>
				</table>
<?php
						//END OF LAYOUT 2 (by Date)
					}


				}
				else {
					//The Query did not run
					print("<div class=\"info\">\n	<p>There are currently no fixtures scheduled.</p>\n	</div>");
				}

		//Did You Know Display Code
		if (function_exists(bblm_display_dyk)) {
			bblm_display_dyk();
		}
?>
				<p class="postmeta"><?php edit_post_link('Edit', ' <strong>[</strong> ', ' <strong>]</strong> '); ?></p>


			</div>


		<?php endwhile;?>
		<?php endif; ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>