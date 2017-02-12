<?php
/**
 * The template for displaying 'Stadiums' - Single View
 *
 * @package		BBowlLeagueMan/Templates
 * @category	Template
 * @author 		Blacksnotliung
 */

get_header(); ?>
	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
			<div class="entry">
				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<h2 class="entry-title"><?php the_title(); ?></h2>
<?php

				//code for "home" teams
				$hometeamsql = 'SELECT T.t_name, P.guid FROM '.$wpdb->prefix.'team T, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE T.t_id = J.tid AND J.prefix = \'t_\' AND J.pid = P.ID AND T.t_show = 1 AND T.stad_id = '.get_the_ID();
				echo "<h3>".__( 'Home Teams', 'bblm' )."</h3>\n";
				if ($hometeam = $wpdb->get_results($hometeamsql)) {
					//Check to see how many teams are returned
					if (1 < count($hometeam)) {
						//we have more than one team
						echo "<p>".__( 'At Present, the following teams call this stadium their home.' , 'bblm' )."</p>\n<ul>\n";
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
					echo "	<div class=\"info\">\n		<p>".__('At present, no teams use this stadium for their home games.', 'bblm' )."</p>\n	</div>\n";
				}
?>
				<div class="details staddet">
					<?php the_content(); ?>
				</div>
<?php


				//Recent Matches
				$recentmatchsql = 'SELECT P.guid, P.post_title, M.m_gate, UNIX_TIMESTAMP(M.m_date) AS mdate, C.c_name, D.div_name FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'division D WHERE M.m_id = J.tid AND J.prefix = \'m_\' AND J.pid = P.ID AND M.c_id = C.c_id AND M.div_id = D.div_id AND M.stad_id = '.get_the_ID().' ORDER BY M.m_date DESC';
				if ($recmatch = $wpdb->get_results($recentmatchsql)) {
					$zebracount = 1;
					echo "<h3>".__( 'Matches that have taken place in this stadium', 'bblm' )."</h3>\n";
					echo "<table class=\"sortable\">\n	<tr>\n		<th>".__( 'Date', 'bblm' )."</th>\n		<th>".__( 'Match', 'bblm' )."</th>\n		<th>".__( 'Competition', 'bblm' )."</th>\n		<th>".__( 'Attendance', 'bblm' )."</th>\n	</tr>\n";
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

?>
				<p class="postmeta"><?php edit_post_link( __( 'Edit', 'bblm' ), ' <strong>[</strong> ', ' <strong>]</strong> '); ?></p>

			</div>
			</div>


		<?php endwhile; ?>
	<?php endif; ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
