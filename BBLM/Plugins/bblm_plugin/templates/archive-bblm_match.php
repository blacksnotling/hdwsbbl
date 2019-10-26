<?php
/*
Template Name: List Resuts
*/
/*
*	Filename: bb.core.matches.php
*	Description: Page template to list the matches.
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
				<p>Order Resuts by
					<select name="bblm_flayout" id="bblm_flayout">
						<option value="bycomp"<?php if ("bycomp" == $bblm_flayout) { print(" selected=\"selected\""); } ?>>Competition</option>
						<option value="bydate"<?php if ("bydate" == $bblm_flayout) { print(" selected=\"selected\""); } ?>>Date</option>
					</select>
				<input name="bblm_filter_submit" type="submit" id="bblm_filter_submit" value="Filter" /></p>
				</form>

<?php
				$matchsql = 'SELECT M.m_id, UNIX_TIMESTAMP(M.m_date) AS mdate, M.m_gate, M.m_teamAtd, M.m_teamBtd, M.m_teamAcas, M.m_teamBcas, P.guid, P.post_title, C.sea_id, Z.post_title AS c_name, Z.guid AS cguid, D.div_name FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P, '.$wpdb->prefix.'division D, '.$wpdb->prefix.'bb2wp Y, '.$wpdb->posts.' Z WHERE C.c_id = Y.tid AND Y.prefix = \'c_\' AND Y.pid = Z.ID AND M.div_id = D.div_id AND M.c_id = C.c_id AND M.m_id = J.tid AND J.prefix = \'m_\'  AND C.type_id = 1 AND C.c_show = 1 AND J.pid = P.ID ORDER BY ';
				$layout = "";
				//determine the required Layout
				if (isset($_POST['bblm_flayout'])) {
					$flay = $_POST['bblm_flayout'];
					switch ($flay) {
						case ("bycomp" == $flay):
					    	$layout .= 1;
					    	$matchsql .= 'C.sea_id DESC, M.c_id DESC, D.div_id ASC, M.m_date DESC';
						    break;
						case ("bydate" == $flay):
					    	$layout .= 0;
					    	$matchsql .= 'M.m_date DESC, M.c_id DESC, D.div_id ASC';
						    break;
						default:
					    	$layout .= 1;
					    	$matchsql .= 'C.sea_id DESC, M.c_id DESC, D.div_id ASC, M.m_date DESC';
						    break;
					}
				}
				else {
					//form not submitted so load in default values
					$layout .= 1;
					$matchsql .= 'C.sea_id DESC, M.c_id DESC, D.div_id ASC, M.m_date DESC';
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

						foreach ( $match as $m ) {
							if ( $m->sea_id !== $current_sea ) {
								$current_sea = $m->sea_id;
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
								echo '<h3>' . bblm_get_season_name( $m->sea_id ) . '</h3><h4><a href="' . $m->cguid . '" title="View more details about the ' . $m->c_name . '">' . $m->c_name .'</a></h4><h5>' . $m->div_name . '</h5><table class = "bblm_table"><tr><th class="tbl_matchdate bblm_tbl_matchdate">Date</th><th class="tbl_matchname bblm_tbl_matchname">Match</th><th class="tbl_matchresult bblm_tbl_matchresult">Result</th><th class="tbl_matchdgate bblm_tbl_matchdgate">Gate</th></tr>';
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
						<td><?php echo date( "d.m.y", $m->mdate ); ?></td>
						<td><a href="<?php echo $m->guid; ?>" title="View more details of the match"><?php echo $m->post_title; ?></a></td>
						<td><?php echo $m->m_teamAtd . ' - ' . $m->m_teamBtd . ' (' . $m->m_teamAcas . ' - ' . $m->m_teamBcas . ')'; ?></td>
						<td><?php echo number_format( $m->m_gate ); ?></td>
						<td><a href="<?php echo $m->cguid; ?>" title="View more about this competition"><?php echo $m->c_name; ?></a></td>
						<td><?php echo $m->div_name; ?></td>
						<td><?php echo bblm_get_season_name( $m->sea_id ); ?></td>
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

?>
				<p class="postmeta"><?php edit_post_link( __( 'Edit', 'oberwald' ), ' <strong>[</strong> ', ' <strong>]</strong> '); ?></p>


			</div>
		</div>


		<?php endwhile;?>
		<?php endif; ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
