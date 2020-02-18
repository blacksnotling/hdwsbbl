<?php
/*
Template Name: List Fixtures
*/
/*
*	Filename: bb.core.fixtures.php
*	Description: Page template to list the Fixtures
*/
?>
<?php get_header(); ?>
	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
			<div class="entry">
				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<h2 class="entry-title"><?php the_title(); ?></h2>

					<?php the_content(); ?>

<?php if (!empty($_POST['bblm_flayout'])) {
		$bblm_flayout = $_POST['bblm_flayout'];
} else {
		$bblm_flayout = "";
} ?>
				<form name="bblm_filterlayout" method="post" id="post" action="" class="selectbox">
				<p>Order Fixtures by
					<select name="bblm_flayout" id="bblm_flayout">
						<option value="bycomp"<?php if ("bycomp" == $bblm_flayout) { print(" selected=\"selected\""); } ?>>Competition</option>
						<option value="bydate"<?php if ("bydate" == $bblm_flayout) { print(" selected=\"selected\""); } ?>>Date</option>
					</select>
				<input name="bblm_filter_submit" type="submit" id="bblm_filter_submit" value="Filter" /></p>
				</form>
<?php
				//Initial SQL
				$fixturesql = 'SELECT UNIX_TIMESTAMP(F.f_date) AS mdate, P.post_title AS cname, P.guid as clink, D.div_name, X.post_title AS TA, X.guid AS TAlink, Z.post_title AS TB, Z.guid AS TBlink, T.t_id AS TAid, R.t_id AS TBid, F.f_id FROM '.$wpdb->prefix.'fixture F, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P, '.$wpdb->prefix.'bb2wp W, '.$wpdb->posts.' X, '.$wpdb->prefix.'bb2wp Y, '.$wpdb->posts.' Z, '.$wpdb->prefix.'division D, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'team R WHERE C.c_id = J.tid AND J.prefix = \'c_\' AND J.pid = P.ID AND T.t_id = W.tid AND W.prefix = \'t_\' AND W.pid = X.ID AND R.t_id = Y.tid AND Y.prefix = \'t_\' AND Y.pid = Z.ID AND F.f_teamA = T.t_id AND F.f_teamB = R.t_id AND F.c_id = C.c_id AND C.type_id = 1 AND F.div_id = D.div_id AND F.f_complete = 0 ORDER BY ';
				$layout = "";
				//determine the required Layout
					switch ($bblm_flayout) {
						case ("bycomp" == $bblm_flayout):
					    	$layout .= 1;
					    	$fixturesql .= 'F.c_id DESC, F.div_id DESC, F.f_date ASC';
						    break;
						case ("bydate" == $bblm_flayout):
					    	$layout .= 0;
					    	$fixturesql .= 'F.f_date ASC, F.c_id DESC, F.div_id DESC';
						    break;
						default:
					    	$layout .= 1;
					    	$fixturesql .= 'F.c_id DESC, F.div_id DESC, F.f_date ASC';
						    break;
					}


				//Run the Query. If successful....
				if ($fixture = $wpdb->get_results($fixturesql)) {

					//grab the ID of the "tbd" team
					$bblm_tbd_team = bblm_get_tbd_team();

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
								print("<h3><a href=\"".$m->clink."\" title=\"View more about the ".$m->cname."\">".$m->cname."</a></h3>\n <h4>".$m->div_name."</h4>\n  <table class=\"bblm_table\">\n		 <tr>\n		   <th class=\"bblm_tbl_matchdate\">Date</th>\n		   <th class=\"bblm_tbl_matchname\">Match</th>\n		 </tr>\n");
								$is_first_comp = 0;
								$is_first_div = 0;
							}
							if ($is_first_div) {
								print("<h4>".$m->div_name."</h4>\n  <table class=\"bblm_table\">\n		 <tr>\n		   <th class=\"bblm_tbl_matchdate\">Date</th>\n		   <th class=\"bblm_tbl_matchname\">Match</th>\n		   </tr>\n");
								$is_first_div = 0;
							}
							if ($zebracount % 2) {
								print("		<tr id=\"F".$m->f_id."\">\n");
							}
							else {
								print("		<tr class=\"bblm_tbl_alt\"  id=\"F".$m->f_id."\">\n");
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
				<table class="bblm_table bblm_sortable">
					<thead>
					<tr>
						<th class="bblm_tbl_matchdate">Date</th>
						<th class="bblm_tbl_matchname">Match</th>
						<th class="bblm_tbl_name">Competition</th>
						<th class="bblm_tbl_matchname">Round</th>
					</tr>
					</thead>
					<tbody>
<?php
						foreach ($fixture as $m) {
							if ($zebracount % 2) {
								print("					<tr  id=\"F".$m->f_id."\">\n");
							}
							else {
								print("					<tr class=\"bblm_tbl_alt\"  id=\"F".$m->f_id."\">\n");
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
					print("<div class=\"bblm_info\">\n	<p>There are currently no fixtures scheduled.</p>\n	</div>");
				}

?>
				<p class="postmeta"><?php edit_post_link( __( 'Edit', 'oberwald' ), ' <strong>[</strong> ', ' <strong>]</strong> '); ?></p>


			</div>
			</div>


		<?php endwhile;?>
		<?php endif; ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
