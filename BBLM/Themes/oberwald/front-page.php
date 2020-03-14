<?php get_header(); ?>
	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>

<?php  			if (function_exists('aktt_sidebar_tweets')) {
					print("		<div id=\"twitterfeed\">\n");
					aktt_sidebar_tweets();
					print("		</div>\n");
				}

?>

		<h2>Welcome to the <?php echo bblm_get_league_name(); ?></h2>

		<div id="main-tabs">
			<div id="fragments">
				<div id="fragment-1">

				<!-- start of #fragment-1 content -->
<?php
			$newslatestpost = array(
				'post_type' => 'post',
				'posts_per_page' => 1,
				'category__not_in' =>  get_cat_ID( 'warzone' ),
				'post__not_in' => get_option( 'sticky_posts' )
			);
			// The Query
			$the_query = new WP_Query( $newslatestpost );

			// The Loop
			if ( $the_query->have_posts() ) {
				while ( $the_query->have_posts() ) {
					$the_query->the_post();
?>
<div class="entry">
	<h2 class="entry-title">Latest News: <br /><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
	<?php $last_news = TimeAgoInWords(strtotime($post->post_date)); ?>
	<p class="postdate"><?php the_time('F jS, Y') ?> (<?php print($last_news); ?>) (<?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?>) <!-- by <?php the_author(); ?> --></p>

	<?php the_excerpt(); ?>
	<?php

				} //end of while
				/* Restore original Post Data */
				wp_reset_postdata();
			} //end of have posts
?>



		<?php endwhile; ?>
		<?php endif; ?>
			</div>
				<!-- end of #fragment-1 content -->

				</div><!-- end of #fragment-1 -->
				<div id="fragment-2">

				<!-- start of #fragment-2 content -->

<?php $recent = new WP_Query("category_name=warzone&showposts=1"); while($recent->have_posts()) : $recent->the_post();?>
			<div class="entry">
				<h2 class="entry-title">Warzone Latest: <a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
				<?php $last_warzone = TimeAgoInWords(strtotime($post->post_date)); ?>
				<p class="postdate"><?php the_time('F jS, Y') ?> (<?php print($last_warzone); ?>) (<?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?>) <!-- by <?php the_author(); ?> --></p>

				<?php the_excerpt(); ?>

			</div>
<?php endwhile; ?>
				<!-- end of #fragment-2 content -->

				</div><!-- end of #fragment-2 -->
				<div id="fragment-3">

				<!-- start of #fragment-3 content -->
					<h2>Recent Results</h2>
	<?php
					$matchsql = 'SELECT M.m_gate, M.m_teamAtd, M.m_teamBtd, P.guid, P.post_title, C.WPID AS CWPID FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P, '.$wpdb->prefix.'comp C WHERE M.c_id = C.WPID AND C.c_counts = 1 AND M.m_id = J.tid AND J.prefix = \'m_\' AND J.pid = P.ID ORDER BY M.m_date DESC LIMIT 6';
					if ($matches = $wpdb->get_results($matchsql)) {
						print("<table class=\"bblm_table\">\n	<tr>\n		<th>Match</th>\n		<th>Score</th>\n		<th>Competition</th>\n		<th>Gate</th>\n	</tr>\n");
						$zebracount = 1;
						foreach ($matches as $match) {
							if ($zebracount % 2) {
								print("	<tr>\n");
							}
							else {
								print("	<tr class=\"bblm_tbl_alt\">\n");
							}
							print("		<td><a href=\"".$match->guid."\" title=\"View the match in detail\">".$match->post_title."</a></td>\n		<td>".$match->m_teamAtd." - ".$match->m_teamBtd."</td>\n		<td>" . bblm_get_competition_link( $match->CWPID ) . "</td>\n		<td>".number_format($match->m_gate)."</td>\n	</tr>\n");
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
					$fixturesql = 'SELECT UNIX_TIMESTAMP(F.f_date) AS fdate, T.t_id AS TA, M.t_id AS TB, V.post_title AS TAname, O.post_title AS TBname, V.guid AS TAlink, O.guid AS TBlink, C.WPID AS CWPID FROM '.$wpdb->prefix.'fixture F, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'bb2wp U, '.$wpdb->posts.' V, '.$wpdb->prefix.'team M, '.$wpdb->prefix.'bb2wp N, '.$wpdb->posts.' O, '.$wpdb->prefix.'comp C WHERE F.c_id = C.WPID AND C.c_counts = 1 AND T.t_id = F.f_teamA AND M.t_id = F.f_teamB AND T.t_id = U.tid AND U.prefix = \'t_\' AND U.pid = V.ID AND M.t_id = N.tid AND N.prefix = \'t_\' AND N.pid = O.ID AND F.f_complete = 0 ORDER BY F.f_date ASC LIMIT 6';
					if ($fixtures = $wpdb->get_results($fixturesql)) {
						print("<table class=\"bblm_table\">\n	<tr>\n		<th>Match</th>\n		<th>Competition</th>\n		<th>Date</th>\n	</tr>\n");
						$zebracount = 1;
						foreach ($fixtures as $fix) {
							if ($zebracount % 2) {
								print("	<tr>\n");
							}
							else {
								print("	<tr class=\"bblm_tbl_alt\">\n");
							}
							print("		<td>".$fix->TAname." vs ".$fix->TBname."</td>\n		<td>" . bblm_get_competition_link( $fix->CWPID ) . "</td>\n		<td>".date("d.m.y", $fix->fdate)."</td>\n	</tr>\n");
							$zebracount++;
						}
						print("</table>\n");
					}
					else {
						print("	<div class=\"bblm_info\">\n		<p>There are currenty no fixtures lined up in the near future,</p>\n	</div>\n");
					}
	?>

					<p><a href="<?php echo home_url(); ?>/fixtures/" title="View Full Fixtures List">View Full Fixtures List &raquo;</a></p>
				<!-- end of #fragment-4 content -->
				</div><!-- end of #fragment-4 -->
				<div id="fragment-5">

				<!-- start of #fragment-5 content -->
					<h2>Biggest Teams of the Moment</h2>
<?php
					$topteamsql = 'SELECT E.guid, E.post_title, Q.t_tv, SUM(T.tc_played) AS OP, SUM(T.tc_W) AS OW, SUM(T.tc_L) AS OL, SUM(T.tc_D) AS OD, SUM(T.tc_tdfor) AS OTF, SUM(T.tc_tdagst) AS OTA, SUM(T.tc_comp) AS OC, SUM(T.tc_casfor) AS OCASF, SUM(T.tc_casagst) AS OCASA, SUM(T.tc_int) AS OINT FROM '.$wpdb->prefix.'team_comp T, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'team Q, '.$wpdb->prefix.'bb2wp W, '.$wpdb->posts.' E WHERE Q.t_id = T.t_id AND Q.t_id = W.tid AND W.prefix = \'t_\' AND W.pid = E.ID AND C.c_counts = 1 AND C.c_id = T.c_id AND T.tc_played > 0 AND Q.t_active = 1 GROUP BY T.t_id ORDER BY Q.t_tv DESC LIMIT 6';
					if ($topteam = $wpdb->get_results($topteamsql)) {
						print("<table class=\"bblm_table\">\n	<tr>\n		<th>Team</th>\n		<th class=\"bblm_tbl_stat\">P</th>\n		<th class=\"bblm_tbl_stat\">W</th>\n		<th class=\"bblm_tbl_stat\">L</th>\n		<th class=\"bblm_tbl_stat\">D</th>\n		<th class=\"bblm_tbl_stat\">TF</th>\n		<th class=\"bblm_tbl_stat\">TA</th>\n		<th class=\"bblm_tbl_stat\">CF</th>\n		<th class=\"bblm_tbl_stat\">CA</th>\n		<th class=\"bblm_tbl_stat\">COMP</th>\n		<th class=\"bblm_tbl_stat\">INT</th>\n		<th>Value</th>\n		</tr>\n");
						$zebracount = 1;
						foreach ($topteam as $tt) {
							if ($zebracount % 2) {
								print("	<tr>\n");
							}
							else {
								print("	<tr class=\"bblm_tbl_alt\">\n");
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
					$bblm_star_team = bblm_get_star_player_team();

					$playersql = 'SELECT D.post_title AS Pname, D.guid AS Plink, P.pos_name, T.WPID, A.p_spp AS VALUE FROM '.$wpdb->prefix.'player A, '.$wpdb->prefix.'bb2wp S, '.$wpdb->posts.' D, '.$wpdb->prefix.'position P, '.$wpdb->prefix.'team T WHERE A.p_id = S.tid AND S.prefix = \'p_\' AND S.pid = D.ID AND A.pos_id = P.pos_id AND A.t_id = T.t_id AND A.p_status = 1 AND T.t_active = 1 AND A.p_spp > 1 AND T.t_id != '.$bblm_star_team.' ORDER BY A.p_spp DESC LIMIT 6';
					if ($player = $wpdb->get_results($playersql)) {
						print("<table class=\"bblm_table\">\n	<tr>\n		<th>Player</th>\n		<th>Position</th>\n		<th>Team</th>\n		<th class=\"bblm_tbl_stat\">SPP</th>\n		</tr>\n");
						$zebracount = 1;
						foreach ($player as $tp) {
							if ($zebracount % 2) {
								print("	<tr>\n");
							}
							else {
								print("	<tr class=\"bblm_tbl_alt\">\n");
							}
							print("		<td><a href=\"".$tp->Plink."\" title=\"Read more about ".$tp->Pname."\">".$tp->Pname."</a></td>\n		<td>" . esc_html( $tp->pos_name ) . "</td>\n		<td><a href=\"" . get_post_permalink( $tp->WPID ) . "\" title=\"Read more about this team\">" . esc_html( get_the_title( $tp->WPID ) ) . "</a></td>\n		<td>".$tp->VALUE."</td>\n	</tr>\n	");
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
					$newsrecentposts = array(
						'post_type' => 'post',
						'posts_per_page' => 6,
						'category__not_in' =>  get_cat_ID( 'warzone' ),
						'post__not_in' => get_option( 'sticky_posts' ),
						'offset' => 1
					);
					// The Query
					$the_query = new WP_Query( $newsrecentposts );

					// The Loop
					if ( $the_query->have_posts() ) {
						print("<ul>\n");
						while ( $the_query->have_posts() ) {
							$the_query->the_post();
		?>
		<li><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></li>

			<?php

						} //end of while
						print("</ul>\n");
					} //end of have posts
		?>
		<p><a href="<?php echo get_permalink( get_option( 'page_for_posts' ) ); ?>" title="View full News Archive">View full News Archive &raquo;</a></p>
		</div>

	</div><!-- end of main-left-->

	<div id="main-middle" class="column">

		<div class="main-content">
		<h2>Latest from the Warzone</h2>

		<?php
					$warzonerecentposts = array(
						'post_type' => 'post',
						'posts_per_page' => 6,
						'category__in' =>  get_cat_ID( 'warzone' ),
						'post__not_in' => get_option( 'sticky_posts' ),
						'offset' => 1
					);
					// The Query
					$the_query = new WP_Query( $warzonerecentposts );

					// The Loop
					if ( $the_query->have_posts() ) {
						print("<ul>\n");
						while ( $the_query->have_posts() ) {
							$the_query->the_post();
		?>
		<li><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></li>

			<?php

						} //end of while
						print("</ul>\n");
					} //end of have posts
		?>
		<p><a href="<?php echo esc_url( get_permalink( get_page_by_title( 'Warzone' ) ) ); ?>" title="View full Warzone archive">View full Warzone Archive &raquo;</a></p>
		</div>


	</div><!-- end of main-middle-->




	<div id="main-right" class="column">
		<!-- note, no container div due to widget printing them -->

<?php
	if (function_exists('widget_bblm_listcomps')) {
		widget_bblm_listcomps(array("before_widget" => "", "after_widget" => ""));
	}
?>
	</div><!-- end of main-right-->

</div><!-- end of #main-sub -->


</div><!-- end of #maincontent -->



<?php get_footer(); ?>
