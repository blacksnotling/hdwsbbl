<?php
global $wp;
/*	print("<pre>");
	print_r($wp);
	print("</pre>");*/
$wp_received_argument = false; //initialize this variable and make it false by default
foreach ($wp->query_vars as $k=>$v) {
	if ($v) {
		$wp_received_argument = true; //I guess so, load index.php
	}
}
if ($wp_received_argument) {
	require(TEMPLATEPATH . "/index.php"); //loading index.php, execution of home.php is done for
}
else {
	//We got no parameters, so let's load our custom home page.
?>

<?php query_posts('showposts=1'); ?>

<?php
	//Define the var so the front page specific stuff is activated in the header
	$ismainpage = 1;
	require(TEMPLATEPATH . "/header.php"); ?>
	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>

<?php  			if (function_exists('aktt_sidebar_tweets')) {
					print("		<div id=\"twitterfeed\">\n");
					aktt_sidebar_tweets();
					print("		</div>\n");
				}

				//Load in the options to determine the WarZone Category
				$options = get_option('bblm_config');
				$warzone_category = htmlspecialchars($options['cat_warzone'], ENT_QUOTES);


?>

		<h2>Welcome to the HDWSBBL</h2>

		<div id="main-tabs">
			<div id="fragments">
				<div id="fragment-1">

				<!-- start of #fragment-1 content -->
			<div class="entry">
				<h2>Latest News: <br /><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
				<?php $last_news = TimeAgoInWords(strtotime($post->post_date)); ?>
				<p class="postdate"><?php the_time('F jS, Y') ?> (<?php print($last_news); ?>) (<?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?>) <!-- by <?php the_author(); ?> --></p>

				<?php the_excerpt(); ?>
				<p class="readmorelink">Continue Reading: <a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?> &raquo;</a></p>

		<?php endwhile; ?>
		<?php endif; ?>
			</div>
				<!-- end of #fragment-1 content -->

				</div><!-- end of #fragment-1 -->
				<div id="fragment-2">

				<!-- start of #fragment-2 content -->
<?php $recent = new WP_Query("cat=".$warzone_category."&showposts=1"); while($recent->have_posts()) : $recent->the_post();?>
			<div class="entry">
				<h2>Warzone Latest: <a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
				<?php $last_warzone = TimeAgoInWords(strtotime($post->post_date)); ?>
				<p class="postdate"><?php the_time('F jS, Y') ?> (<?php print($last_warzone); ?>) (<?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?>) <!-- by <?php the_author(); ?> --></p>

				<?php the_excerpt(); ?>
				<p class="readmorelink">Continue Reading: <a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?> &raquo;</a></p>

			</div>
<?php endwhile; ?>
				<!-- end of #fragment-2 content -->

				</div><!-- end of #fragment-2 -->
				<div id="fragment-3">

				<!-- start of #fragment-3 content -->
					<h2>Recent Results</h2>
	<?php
					$matchsql = 'SELECT M.m_gate, M.m_teamAtd, M.m_teamBtd, P.guid, P.post_title, L.guid AS Clink, L.post_title AS Cname FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P, '.$wpdb->prefix.'bb2wp K, '.$wpdb->posts.' L, '.$wpdb->prefix.'comp C WHERE C.c_id = K.tid AND K.prefix = \'c_\' AND K.pid = L.ID AND M.c_id = C.c_id AND C.c_counts = 1 AND C.c_show = 1 AND C.type_id = 1 AND M.m_id = J.tid AND J.prefix = \'m_\' AND J.pid = P.ID ORDER BY M.m_date DESC LIMIT 6';
					if ($matches = $wpdb->get_results($matchsql)) {
						print("<table>\n	<tr>\n		<th>Match</th>\n		<th>Score</th>\n		<th>Competition</th>\n		<th>Gate</th>\n	</tr>\n");
						$zebracount = 1;
						foreach ($matches as $match) {
							if ($zebracount % 2) {
								print("	<tr>\n");
							}
							else {
								print("	<tr class=\"tbl_alt\">\n");
							}
							print("		<td><a href=\"".$match->guid."\" title=\"View the match in detail\">".$match->post_title."</a></td>\n		<td>".$match->m_teamAtd." - ".$match->m_teamBtd."</td>\n		<td><a href=\"".$match->Clink."\" title=\"Read more about the ".$match->Cname."\">".$match->Cname."</a></td>\n		<td>".number_format($match->m_gate)."</td>\n	</tr>\n");
							$zebracount++;
						}
						print("</table>\n");
					}
	?>

					<p><a href="<?php echo home_url(); ?>/matches/" title="View Full Match Listing">View Full List of Recent Matches &raquo;</a></p>
				<!-- end of #fragment-3 content -->

				</div><!-- end of #fragment-3 -->
				<div id="fragment-4">

				<!-- start of #fragment-4 content -->
					<h2>Upcoming Fixtures</h2>
	<?php
					$fixturesql = 'SELECT UNIX_TIMESTAMP(F.f_date) AS fdate, T.t_id AS TA, M.t_id AS TB, V.post_title AS TAname, O.post_title AS TBname, V.guid AS TAlink, O.guid AS TBlink, L.guid AS Clink, L.post_title AS Cname  FROM '.$wpdb->prefix.'fixture F, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'bb2wp U, '.$wpdb->posts.' V, '.$wpdb->prefix.'team M, '.$wpdb->prefix.'bb2wp N, '.$wpdb->posts.' O, '.$wpdb->prefix.'bb2wp K, '.$wpdb->posts.' L, '.$wpdb->prefix.'comp C WHERE C.c_id = K.tid AND K.prefix = \'c_\' AND K.pid = L.ID AND F.c_id = C.c_id AND C.c_counts = 1 AND C.type_id = 1 AND C.c_show = 1 AND T.t_id = F.f_teamA AND M.t_id = F.f_teamB AND T.t_id = U.tid AND U.prefix = \'t_\' AND U.pid = V.ID AND M.t_id = N.tid AND N.prefix = \'t_\' AND N.pid = O.ID AND F.f_complete = 0 ORDER BY F.f_date ASC LIMIT 6';
					if ($fixtures = $wpdb->get_results($fixturesql)) {
						print("<table>\n	<tr>\n		<th>Match</th>\n		<th>Competition</th>\n		<th>Date</th>\n	</tr>\n");
						$zebracount = 1;
						foreach ($fixtures as $fix) {
							if ($zebracount % 2) {
								print("	<tr>\n");
							}
							else {
								print("	<tr class=\"tbl_alt\">\n");
							}
							print("		<td>".$fix->TAname." vs ".$fix->TBname."</td>\n		<td><a href=\"".$fix->Clink."\" title=\"Read more about the ".$fix->Cname."\">".$fix->Cname."</a></td>\n		<td>".date("d.m.y", $fix->fdate)."</td>\n	</tr>\n");
							$zebracount++;
						}
						print("</table>\n");
					}
					else {
						print("	<div class=\"info\">\n		<p>There are currenty no fixtures lined up in the near future,</p>\n	</div>\n");
					}
	?>

					<p><a href="<?php echo home_url(); ?>/fixtures/" title="View Full Fixtures List">View Full Fixtures List &raquo;</a></p>
				<!-- end of #fragment-4 content -->
				</div><!-- end of #fragment-4 -->
				<div id="fragment-5">

				<!-- start of #fragment-5 content -->
					<h2>Biggest Teams of the Moment</h2>
<?php
					$topteamsql = 'SELECT E.guid, E.post_title, Q.t_tv, SUM(T.tc_played) AS OP, SUM(T.tc_W) AS OW, SUM(T.tc_L) AS OL, SUM(T.tc_D) AS OD, SUM(T.tc_tdfor) AS OTF, SUM(T.tc_tdagst) AS OTA, SUM(T.tc_comp) AS OC, SUM(T.tc_casfor) AS OCASF, SUM(T.tc_casagst) AS OCASA, SUM(T.tc_int) AS OINT FROM '.$wpdb->prefix.'team_comp T, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'team Q, '.$wpdb->prefix.'bb2wp W, '.$wpdb->posts.' E WHERE Q.t_id = T.t_id AND Q.t_id = W.tid AND W.prefix = \'t_\' AND W.pid = E.ID AND C.c_counts = 1 AND C.c_show = 1 AND C.type_id = 1 AND C.c_id = T.c_id AND T.tc_played > 0 AND Q.t_active = 1 GROUP BY T.t_id ORDER BY Q.t_tv DESC LIMIT 6';
//Teams by win%										$topteamsql = 'SELECT E.guid, E.post_title, Q.t_tv, SUM(T.tc_played) AS OP, SUM(T.tc_W) AS OW, SUM(T.tc_L) AS OL, SUM(T.tc_D) AS OD, SUM(T.tc_tdfor) AS OTF, SUM(T.tc_tdagst) AS OTA, SUM(T.tc_comp) AS OC, SUM(T.tc_casfor) AS OCASF, SUM(T.tc_casagst) AS OCASA, SUM(T.tc_int) AS OINT, SUM(T.tc_W/T.tc_played) / SUM(T.tc_played)*100 AS WINP FROM '.$wpdb->prefix.'team_comp T, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'team Q, '.$wpdb->prefix.'bb2wp W, '.$wpdb->posts.' E WHERE Q.t_id = T.t_id AND Q.t_id = W.tid AND W.prefix = \'t_\' AND W.pid = E.ID AND C.c_counts = 1 AND C.c_show = 1 AND C.c_id = T.c_id AND T.tc_played > 0 AND Q.t_active = 1 GROUP BY T.t_id ORDER BY WINP DESC, Q.t_tv DESC LIMIT 6';
					if ($topteam = $wpdb->get_results($topteamsql)) {
						print("<table>\n	<tr>\n		<th>Team</th>\n		<th class=\"tbl_stat\">P</th>\n		<th class=\"tbl_stat\">W</th>\n		<th class=\"tbl_stat\">L</th>\n		<th class=\"tbl_stat\">D</th>\n		<th class=\"tbl_stat\">TF</th>\n		<th class=\"tbl_stat\">TA</th>\n		<th class=\"tbl_stat\">CF</th>\n		<th class=\"tbl_stat\">CA</th>\n		<th class=\"tbl_stat\">COMP</th>\n		<th class=\"tbl_stat\">INT</th>\n		<th>Value</th>\n		</tr>\n");
						$zebracount = 1;
						foreach ($topteam as $tt) {
							if ($zebracount % 2) {
								print("	<tr>\n");
							}
							else {
								print("	<tr class=\"tbl_alt\">\n");
							}
							print("		<td><a href=\"".$tt->guid."\" title=\"Read more about ".$tt->post_title."\">".$tt->post_title."</a></td>\n		<td>".$tt->OP."</td>\n		<td>".$tt->OW."</td>\n		<td>".$tt->OL."</td>\n		<td>".$tt->OD."</td>\n		<td>".$tt->OTF."</td>\n		<td>".$tt->OTA."</td>\n		<td>".$tt->OCASF."</td>\n		<td>".$tt->OCASA."</td>\n		<td>".$tt->OC."</td>\n		<td>".$tt->OINT."</td>\n		<td>".number_format($tt->t_tv)."</td>\n	</tr>\n	");
							$zebracount++;
						}
						print("</table>\n");
					}
?>
					<p><a href="<?php echo home_url(); ?>/teams/" title="View the list of all teams">View the list of all teams &raquo;</a></p>


				<!-- end of #fragment-5 content -->

				</div><!-- end of #fragment-5 -->
				<div id="fragment-6">

				<!-- start of #fragment-6 content -->
					<h2>Top Players of the Moment</h2>
<?php
					//$playersql = 'SELECT W.post_title AS Pname, W.guid AS Plink, L.post_title AS Tname, L.guid AS Tlink, SUM(M.mp_spp) AS VALUE, Z.pos_name FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'match X, '.$wpdb->prefix.'bb2wp Q, '.$wpdb->posts.' W, '.$wpdb->prefix.'bb2wp K, '.$wpdb->posts.' L, '.$wpdb->prefix.'position Z WHERE Z.pos_id = P.pos_id AND M.p_id = P.p_id AND P.t_id = T.t_id AND M.m_id = X.m_id AND X.c_id = C.c_id AND C.c_counts = 1 AND C.c_show = 1 AND C.type_id = 1 AND P.p_id = Q.tid AND Q.prefix = \'p_\' AND Q.pid = W.ID AND T.t_id = K.tid AND K.prefix = \'t_\' AND K.pid = L.ID AND M.mp_spp > 0 AND P.p_status = 1 AND T.t_active = 1 GROUP BY P.p_id ORDER BY VALUE DESC LIMIT 6';
					$playersql = 'SELECT D.post_title AS Pname, D.guid AS Plink, P.pos_name, T.t_name AS Tname, T.t_guid AS Tlink, A.p_spp AS VALUE FROM '.$wpdb->prefix.'player A, '.$wpdb->prefix.'bb2wp S, '.$wpdb->posts.' D, '.$wpdb->prefix.'position P, '.$wpdb->prefix.'team T WHERE A.p_id = S.tid AND S.prefix = \'p_\' AND S.pid = D.ID AND A.pos_id = P.pos_id AND A.t_id = T.t_id AND T.type_id = 1 AND A.p_status = 1 AND T.t_active = 1 AND A.p_spp > 1 ORDER BY A.p_spp DESC LIMIT 6';
					if ($player = $wpdb->get_results($playersql)) {
						print("<table>\n	<tr>\n		<th>Player</th>\n		<th>Position</th>\n		<th>Team</th>\n		<th class=\"tbl_stat\">SPP</th>\n		</tr>\n");
						$zebracount = 1;
						foreach ($player as $tp) {
							if ($zebracount % 2) {
								print("	<tr>\n");
							}
							else {
								print("	<tr class=\"tbl_alt\">\n");
							}
							print("		<td><a href=\"".$tp->Plink."\" title=\"Read more about ".$tp->Pname."\">".$tp->Pname."</a></td>\n		<td>".$tp->pos_name."</td>\n		<td><a href=\"".$tp->Tlink."\" title=\"Read more about ".$tp->Tname."\">".$tp->Tname."</a></td>\n		<td>".$tp->VALUE."</td>\n	</tr>\n	");
							$zebracount++;
						}
						print("</table>\n");
					}
?>
					<p><a href="<?php echo home_url(); ?>/stats/#statstable" title="View more Player Statistics">View more Player Statistics &raquo;</a></p>
				<!-- end of #fragment-6 content -->

				</div><!-- end of #fragment-6 -->
			</div><!-- end of #fragments -->

			<ul id="main-tabs-links">
				<li><a href="#fragment-1"><span>News (<small><?php print($last_news); ?></small>)</span></a></li>
				<li><a href="#fragment-2"><span>WarZone (<small><?php print($last_warzone); ?></small>)</span></a></li>
				<li><a href="#fragment-3"><span>Recent Results</span></a></li>
				<li><a href="#fragment-4"><span>Upcoming Fixtures</span></a></li>
				<li><a href="#fragment-5"><span>Biggest Teams</span></a></li>
				<li><a href="#fragment-6"><span>Top Players</span></a></li>
				<!-- <li><a href="#fragment-6"><span>Featured Player</span></a></li> -->
			</ul>
		</div><!-- end of #main-tabs -->



<div id="main-sub">
	<hr />

	<div id="main-left" class="column">
		<div class="main-content">
		<h2>Recent News</h2>
<?php
		query_posts('showposts=6&offset=1');
		if (have_posts()) :
			print("<ul>\n");
			while (have_posts()) : the_post();
?>
		<li><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></li>
<?php
			endwhile;
			print("</ul>\n");
		endif;
?>
		<p><a href="<?php echo home_url(); ?>/news/" title="View full News Archive">View full News Archive &raquo;</a></p>
		</div>

	</div><!-- end of main-left-->

	<div id="main-middle" class="column">

		<div class="main-content">
		<h2>Latest from the Warzone</h2>

		<?php
		query_posts('cat='.$warzone_category.'&showposts=6&offset=1');
		if (have_posts()) :
			print("<ul>\n");
			while (have_posts()) : the_post();
		?>
		<li><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></li>
		<?php
			endwhile;
			print("</ul>\n");
		endif;
		?>
		<p><a href="<?php echo home_url(); ?>/warzone/" title="View full Warzone archive">View full Warzone Archive &raquo;</a></p>
		</div>


	</div><!-- end of main-middle-->




	<div id="main-right" class="column">
		<!-- note, no container div due to widget printing them -->
			<?php widget_bblm_listcomps(array()) ?>

	</div><!-- end of main-right-->

</div><!-- end of #main-sub -->


</div><!-- end of #maincontent -->



<?php get_footer(); ?>

<?php
}//end of template detection
?>
