<?php
/*
Template Name: List Resuts
*/
/*
*	Filename: bb.core.matches.php
*	Version: 1.3
*	Description: Page template to list the matches.
*/
/* -- Change History --
20080410 - 1.0b - Initial creation of file.
20080411 - 1.1b - Added cas results to table
20080420 - 1,2b - Changed the Competition titles into links to that competition!
		 - 1.3b - Split the competitions up by division
20080501 - 1.3.1b - Changed the order of the SQL strings as the dvisions where "upside down"
20080615 - 1.4b - Added table classes and zebra stripes and breadcrumb
20080623 - 1.4.1b - Review of table classes
20080730 - 1.0 - bump to Version 1 for public release.
20090124 - 1.1 - Addition of Drop down to change layout. Also changed so the comp name is called from the WP table!
20090330 - 1,2 - Editied to filter out non BBBBL details
20090712 - 1.2.1 - Added DYK code to page
20100123 - 1.3 - Updated the prefix for the custom bb tables in the Database (tracker [225])

*/
?>
<?php get_header(); ?>
	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
		<div id="breadcrumb">
			<p><a href="<?php echo get_option('home'); ?>" title="Back to the front of the BBBBL">BBBBL</a> &raquo; Results</p>
		</div>
			<div class="entry">
				<h2><?php the_title(); ?></h2>

				<?php the_content('Read the rest of this entry &raquo;'); ?>

				<form name="bblm_filterlayout" method="post" id="post" action="" class="selectbox">
				<p>Order Resuts by
					<select name="bblm_flayout" id="bblm_flayout">
						<option value="bycomp"<?php if (bycomp == $_POST['bblm_flayout']) { print(" selected=\"selected\""); } ?>>Competition</option>
						<option value="bydate"<?php if (bydate == $_POST['bblm_flayout']) { print(" selected=\"selected\""); } ?>>Date</option>
					</select>
				<input name="bblm_filter_submit" type="submit" id="bblm_filter_submit" value="Filter" /></p>
				</form>

<?php
				$matchsql = 'SELECT M.m_id, UNIX_TIMESTAMP(M.m_date) AS mdate, M.m_gate, M.m_teamAtd, M.m_teamBtd, M.m_teamAcas, M.m_teamBcas, P.guid, P.post_title, S.sea_name, Z.post_title AS c_name, Z.guid AS cguid, D.div_name FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'season S, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P, '.$wpdb->prefix.'division D, '.$wpdb->prefix.'bb2wp Y, '.$wpdb->posts.' Z WHERE C.c_id = Y.tid AND Y.prefix = \'c_\' AND Y.pid = Z.ID AND M.div_id = D.div_id AND C.sea_id = S.sea_id AND M.c_id = C.c_id AND M.m_id = J.tid AND J.prefix = \'m_\'  AND C.type_id = 1 AND C.c_show = 1 AND J.pid = P.ID ORDER BY ';

				//determine the required Layout
				if (isset($_POST['bblm_flayout'])) {
					$flay = $_POST['bblm_flayout'];
					switch ($flay) {
						case ("bycomp" == $flay):
					    	$layout .= 1;
					    	$matchsql .= 'S.sea_id DESC, M.c_id DESC, D.div_id ASC, M.m_date DESC';
						    break;
						case ("bydate" == $flay):
					    	$layout .= 0;
					    	$matchsql .= 'M.m_date DESC, M.c_id DESC, D.div_id ASC';
						    break;
						default:
					    	$layout .= 1;
					    	$matchsql .= 'S.sea_id DESC, M.c_id DESC, D.div_id ASC, M.m_date DESC';
						    break;
					}
				}
				else {
					//form not submitted so load in default values
					$layout .= 1;
					$matchsql .= 'S.sea_id DESC, M.c_id DESC, D.div_id ASC, M.m_date DESC';
				}


				//Run the Query. If successfull
				if ($match = $wpdb->get_results($matchsql)) {

					if (1 == $layout) {
						//Load the default by Competition

						$is_first_comp = 0;
						$is_first_div = 0;
						$is_first_sea = 1;
						$current_comp = "";
						$current_div ="";
						$current_sea = "";
						$zebracount = 1;

						foreach ($match as $m) {
							if ($m->sea_name !== $current_sea) {
								$current_sea = $m->sea_name;
								$current_comp = $m->c_name;
								$current_div = $m->div_name;
								if (1 !== $is_first_sea) {
									print(" </table>\n");
									$zebracount = 1;
								}
								$is_first_sea = 1;
							}
							if ($m->c_name !== $current_comp) {
								$current_comp = $m->c_name;
								if ((1 !== $is_first_comp) && (1 !== $is_first_sea)) {
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
							if ($is_first_sea) {
								print("<h3>".$m->sea_name."</h3>\n <h4><a href=\"".$m->cguid."\" title=\"View more details about the ".$m->c_name."\">".$m->c_name."</a></h4>\n <h5>".$m->div_name."</h5>\n  <table>\n		 <tr>\n		   <th class=\"tbl_matchdate\">Date</th>\n		   <th class=\"tbl_matchname\">Match</th>\n		   <th class=\"tbl_matchresult\">Result</th>\n		   <th class=\"tbl_matchdgate\">Gate</th>\n		 </tr>\n");
								$is_first_sea = 0;
								$is_first_comp = 0;
								$is_first_div = 0;
							}
							if ($is_first_comp) {
								print("<h4><a href=\"".$m->cguid."\" title=\"View more details about the ".$m->c_name."\">".$m->c_name."</a></h4>\n <h5>".$m->div_name."</h5>\n  <table>\n		 <tr>\n		   <th class=\"tbl_matchdate\">Date</th>\n		   <th class=\"tbl_matchname\">Match</th>\n		   <th class=\"tbl_matchresult\">Result</th>\n		   <th class=\"tbl_matchdgate\">Gate</th>\n		 </tr>\n");
								$is_first_comp = 0;
								$is_first_div = 0;
							}
							if ($is_first_div) {
								print("<h5>".$m->div_name."</h5>\n  <table>\n		 <tr>\n		   <th class=\"tbl_matchdate\">Date</th>\n		   <th class=\"tbl_matchname\">Match</th>\n		   <th class=\"tbl_matchresult\">Result</th>\n		   <th class=\"tbl_matchdgate\">Gate</th>\n		 </tr>\n");
								$is_first_div = 0;
							}
							if ($zebracount % 2) {
								print("		<tr>\n");
							}
							else {
								print("		<tr class=\"tbl_alt\">\n");
							}
							//print("<table>\n		 <tr>\n		   <th>Date</th>\n		   <th>Match</th>\n		   <th>Result</th>\n		   <th>Attendance</th>\n		 </tr>\n");
							print("		   <td>".date("d.m.y", $m->mdate)."</td>\n		   <td><a href=\"".$m->guid."\" title=\"View the details of the match\">".$m->post_title."</a></td>\n		   <td>".$m->m_teamAtd." - ".$m->m_teamBtd." (".$m->m_teamAcas." - ".$m->m_teamBcas.")</td>\n		   <td><em>".number_format($m->m_gate)."</em></td>\n		 </tr>\n");
							$zebracount++;
						}
						print("</table>\n");
					}//end of if layout 1
					else {
						//The Second Layout has been selected
						$zebracount = 1;
?>
				<table class="sortable">
					<thead>
					<tr>
						<th class="tbl_matchdate">Date</th>
						<th class="tbl_matchname">Match</th>
						<th>Result</th>
						<th>Atten</th>
						<th class="tbl_name">Comp</th>
						<th>Round</th>
						<th>Season</th>
					</tr>
					</thead>
					<tbody>
<?php
						foreach ($match as $m) {
							if ($zebracount % 2) {
								print("					<tr  id=\"F".$m->m_id."\">\n");
							}
							else {
								print("					<tr class=\"tbl_alt\"  id=\"F".$m->m_id."\">\n");
							}
?>
						<td><?php print(date("d.m.y", $m->mdate)); ?></td>
						<td><a href="<?php print($m->guid); ?>" title="View more details of the match"><?php print($m->post_title); ?></a></td>
						<td><?php print($m->m_teamAtd." - ".$m->m_teamBtd." (".$m->m_teamAcas." - ".$m->m_teamBcas.")"); ?></td>
						<td><?php print(number_format($m->m_gate)); ?></td>
						<td><a href="<?php print($m->cguid); ?>" title="View more about this competition"><?php print($m->c_name); ?></a></td>
						<td><?php print($m->div_name); ?></td>
						<td><?php print($m->sea_name); ?></td>
					</tr>
<?php
							$zebracount++;
						}//end of for each
						print("				</table>\n");
					}//end of Layout 2
				}//end of if SQL worked
				else {
					print("  <p>Sorry, but no Matches could be retrieved at this time, please try again later.</p>\n");
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