<?php
/*
Template Name: View Stadium
*/
/*
*	Filename: bb.view.Stadium.php
*	Version: 1.1
*	Description: Page template to view a races details.
*/
?>
<?php get_header(); ?>
	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
		<div id="breadcrumb">
			<p><a href="<?php echo home_url(); ?>" title="Back to the front of the HDWSBBL">HDWSBBL</a> &raquo; <a href="<?php echo home_url(); ?>/stadiums/" title="Back to the Stadium listing">Stadiums</a> &raquo; <?php the_title(); ?></p>
		</div>
			<div class="entry">
				<h2><?php the_title(); ?></h2>
<?php
				$stadidsql = 'SELECT stad_id FROM '.$wpdb->prefix.'stadium S, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE S.stad_id = J.tid AND J.prefix = \'stad_\' AND J.pid = P.ID AND P.ID = '.$post->ID;
				$stad_id = $wpdb->get_var($stadidsql);

				//code for "home" teams
				$hometeamsql = 'SELECT T.t_name, P.guid FROM '.$wpdb->prefix.'team T, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE T.t_id = J.tid AND J.prefix = \'t_\' AND J.pid = P.ID AND T.t_show = 1 AND T.stad_id = '.$stad_id;
				print("<h3>Home Teams</h3>\n");
				if ($hometeam = $wpdb->get_results($hometeamsql)) {
					//Check to see how many teams are returned
					if (1 < count($hometeam)) {
						//we have more than one team
						print("<p>At Present, the following teams call this stadium their home.</p>\n<ul>\n");
						foreach ($hometeam as $ht) {
							print("	<li><a href=\"".$ht->guid."\" title=\"Read more about ".$ht->t_name."\">".$ht->t_name."</a></li>\n");
						}
						print("</ul>\n");
					}
					else {
						//only one team is retuned
						foreach ($hometeam as $ht) {
							print("<p>At Present, only <a href=\"".$ht->guid."\" title=\"Read more about ".$ht->t_name."\">".$ht->t_name."</a> call this stadium their home.</p>\n");
						}
					}
				}
				else {
					print("	<div class=\"info\">\n		<p>At present, no teams use this stadium for their home games.</p>\n	</div>\n");
				}
?>
				<div class="details staddet">
					<?php the_content('Read the rest of this entry &raquo;'); ?>
				</div>
<?php


				//Recent Matches
				$recentmatchsql = 'SELECT P.guid, P.post_title, M.m_gate, UNIX_TIMESTAMP(M.m_date) AS mdate, C.c_name, D.div_name FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'division D WHERE M.m_id = J.tid AND J.prefix = \'m_\' AND J.pid = P.ID AND M.c_id = C.c_id AND M.div_id = D.div_id AND M.stad_id = '.$stad_id.' ORDER BY M.m_date DESC';
				if ($recmatch = $wpdb->get_results($recentmatchsql)) {
					$zebracount = 1;
					print("<h3>Recent Matches in this stadium</h3>\n");
					print("<table>\n	<tr>\n		<th>Date</th>\n		<th>Match</th>\n		<th>Competition</th>\n		<th>Attendance</th>\n	</tr>\n");
					foreach ($recmatch as $rm) {
						if (($zebracount % 2) && (10 < $zebracount)) {
							print("		<tr class=\"tb_hide\">\n");
						}
						else if (($zebracount % 2) && (10 >= $zebracount)) {
							print("		<tr>\n");
						}
						else if (10 < $zebracount) {
							print("		<tr class=\"tbl_alt tb_hide\">\n");
						}
						else {
							print("		<tr class=\"tbl_alt\">\n");
						}
						print("		<td>".date("d.m.y", $rm->mdate)."</td>\n		<td><a href=\"".$rm->guid."\" title=\"Read the full match report\">".$rm->post_title."</a></td>\n		<td>".$rm->c_name." (".$rm->div_name.")</td>\n		<td>".number_format($rm->m_gate)."</td>\n	</tr>\n");
						$zebracount++;
					}
					print("</table>\n");
				}

		//Did You Know Display Code
		if (function_exists(bblm_display_dyk)) {
			bblm_display_dyk();
		}
?>
				<p class="postmeta"><?php edit_post_link('Edit', ' <strong>[</strong> ', ' <strong>]</strong> '); ?></p>

			</div>


		<?php endwhile; ?>
	<?php endif; ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
