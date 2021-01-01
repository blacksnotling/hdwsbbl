<?php
/**
 * BBowlLeagueMan Teamplate View Statistics - TD
 *
 * Page Template to view TD Statistics
 *
 * @author 		Blacksnotling
 * @category 	Template
 * @package 	BBowlLeagueMan/Templates
 */
 /*
  * Template Name: View Stats - TD
  */
?>
<?php get_header(); ?>
<div id="primary" class="content-area content-area-right-sidebar">
  <main id="main" class="site-main" role="main">
  <?php do_action( 'bblm_template_before_posts' ); ?>
	<?php if (have_posts()) : ?>
		<?php do_action( 'bblm_template_before_loop' ); ?>
		<?php while (have_posts()) : the_post(); ?>
			<?php do_action( 'bblm_template_before_content' ); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

		<header class="page-header entry-header">

			<h2 class="entry-title"><?php the_title(); ?></h2>

		</header><!-- .page-header -->

			<div class="entry-content">

				<?php the_content(); ?>

<?php
		 /*-- Stats part A -- */
		 $mostxteamseasonsql = 'SELECT A.ats_value AS VALUE, A.sea_id, T.WPID FROM '.$wpdb->prefix.'awards_team_sea A, '.$wpdb->prefix.'team T WHERE A.t_id = T.t_id AND A.a_id = 11 ORDER BY VALUE DESC, A.sea_id ASC LIMIT 1';
		 $mxts = $wpdb->get_row($mostxteamseasonsql);
		 $mostxplayerseasonsql = 'SELECT A.aps_value AS VALUE, P.WPID AS PLAYER, T.WPID, A.sea_id, X.pos_name FROM '.$wpdb->prefix.'awards_player_sea A, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' S, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'position X WHERE P.pos_id = X.pos_id AND A.p_id = P.p_id AND P.t_id = T.t_id AND A.a_id = 11 ORDER BY VALUE DESC, A.sea_id ASC LIMIT 1';
		 $mxps = $wpdb->get_row($mostxplayerseasonsql);
		 $mostxteamcompsql = 'SELECT A.atc_value AS VALUE, T.WPID, A.c_id AS CWPID  FROM '.$wpdb->prefix.'awards_team_comp A, '.$wpdb->prefix.'team T WHERE A.t_id = T.t_id AND A.a_id = 11 ORDER BY VALUE DESC, A.c_id ASC LIMIT 1';
		 $mxtc = $wpdb->get_row($mostxteamcompsql);
		 $mostxplayercompsql = 'SELECT A.apc_value AS VALUE, L.post_title AS PLAYER, L.guid AS PLAYERLink, T.WPID, A.c_id AS CWPID, X.pos_name FROM '.$wpdb->prefix.'awards_player_comp A, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'bb2wp K, '.$wpdb->posts.' L, '.$wpdb->prefix.'position X WHERE P.pos_id = X.pos_id AND P.p_id = K.tid AND K.prefix = \'p_\' AND K.pid = L.ID AND A.p_id = P.p_id AND P.t_id = T.t_id AND A.a_id = 11 ORDER BY VALUE DESC, A.c_id ASC LIMIT 1';
		 $mxpc = $wpdb->get_row($mostxplayercompsql);
		 $mostxteammatchsql = 'SELECT T.WPID, M.mt_td AS VALUE, UNIX_TIMESTAMP(X.m_date) AS MDATE FROM '.$wpdb->prefix.'team T, '.$wpdb->prefix.'match_team M, '.$wpdb->prefix.'match X, '.$wpdb->prefix.'comp C WHERE T.t_id = M.t_id AND M.m_id = X.WPID AND C.WPID = X.c_id AND C.c_counts = 1 AND M.mt_td > 0 ORDER BY VALUE DESC, M.m_id ASC LIMIT 1';
		 $mxtm = $wpdb->get_row($mostxteammatchsql);
		 $mostxplayermatchsql = 'SELECT Y.post_title AS PLAYER, T.WPID, Y.guid AS PLAYERLink, M.mp_td AS VALUE, R.pos_name, UNIX_TIMESTAMP(X.m_date) AS MDATE FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' Y, '.$wpdb->prefix.'position R, '.$wpdb->prefix.'match X, '.$wpdb->prefix.'comp C WHERE M.m_id = X.WPID AND C.WPID = X.c_id AND C.c_counts = 1 AND P.pos_id = R.pos_id AND P.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = Y.ID AND M.p_id = P.p_id AND P.t_id = T.t_id AND M.mp_counts = 1 AND M.mp_td > 0 ORDER BY VALUE DESC, M.m_id ASC LIMIT 1';
		 $mxpm = $wpdb->get_row($mostxplayermatchsql);
?>
		<ul>
			<li><strong>Most Touchdowns scored in a Season (Team)</strong>: <?php print($mxts->VALUE); ?> (<?php echo bblm_get_season_link( $mxts->WPID ); ?> - <?php echo bblm_get_season_link( $mxts->sea_id ); ?>)</li>
			<li><strong>Most Touchdowns scored in a Season (Player)</strong>: <?php print($mxps->VALUE); ?> (<?php echo bblm_get_player_link( $mxps->PLAYER ); ?> - <?php echo( esc_html( $mxps->pos_name ) ); ?> for <?php echo bblm_get_team_link( $mxps->WPID ); ?> - <?php echo bblm_get_season_link( $mxps->sea_id ); ?>)</li>
			<li><strong>Most Touchdowns scored in a Competition (Team)</strong>: <?php print($mxtc->VALUE); ?> (<?php echo bblm_get_team_link( $mxtc->WPID ); ?> - <?php echo bblm_get_competition_link( $mxtc->CWPID ); ?>)</li>
			<li><strong>Most Touchdowns scored in a Competition (Player)</strong>: <?php print($mxpc->VALUE); ?> (<a href="<?php print($mxpc->PLAYERLink); ?>" title="See more on this Player"><?php print($mxpc->PLAYER); ?></a> - <?php print( esc_html( $mxpc->pos_name ) ); ?> for <?php echo bblm_get_team_link( $mxpc->WPID ); ?> - <?php echo bblm_get_competition_link( $mxpc->CWPID ); ?>))</li></li>
			<li><strong>Most Touchdowns scored in a Match (Team)</strong>: <?php print($mxtm->VALUE); ?> (<a href="<?php print( get_post_permalink( $mxtm->WPID ) ); ?>" title="Learn more about this Team"><?php print( esc_html( get_the_title( $mxtm->WPID ) ) ); ?></a> - <?php print(date("d.m.25y", $mxtm->MDATE)); ?>)</li>
			<li><strong>Most Touchdowns scored in a Match (Player)</strong>: <?php print($mxpm->VALUE); ?> (<a href="<?php print($mxpm->PLAYERLink); ?>" title="See more on this Player"><?php print($mxpm->PLAYER); ?></a> - <?php print( esc_html( $mxpm->pos_name ) ); ?> for <a href="<?php print( get_post_permalink( $mxpm->WPID ) ); ?>" title="Learn more about this Team"><?php print( esc_html( get_the_title( $mxpm->WPID ) ) ); ?></a> - <?php print(date("d.m.25y", $mxpm->MDATE)); ?>)</li>
		</ul>



			<h3>Statistics tables</h3>
<?php
				  ///////////////////////////////
				 // Filtering of Stats tables //
				///////////////////////////////

				$stat_limit = bblm_get_stat_limit();
				$bblm_star_team = bblm_get_star_player_team();

				//the default is to show the stats for all time (this comes into pay when showing active players
				$period_alltime = 1;
				$statsqlmodp = "";
				$statsqlmodt = "";

				//determine the status we are looking up
				if (!empty($_POST['bblm_status'])) {
					$status = $_POST['bblm_status'];
					//note that the sql is only modified if the "active" option is selected
					switch ($status) {
						case ("active" == $status):
					    	$statsqlmodp .= 'AND T.t_active = 1 AND P.p_status = 1 ';
					    	$statsqlmodt .= 'AND Z.t_active = 1 ';
					    	$period_alltime = 0;
						    break;
					}
				}
				else {
					$status = "";
				}
?>
				<form name="bblm_filterstats" method="post" id="statstable" action="#statstable">
				<p>For the below Statistics tables, show the records for
					<select name="bblm_status" id="bblm_status">
						<option value="alltime"<?php if ("alltime" == $status) { print(" selected=\"selected\""); } ?>>All Time</option>
						<option value="active"<?php if ("active" == $status) { print(" selected=\"selected\""); } ?>>Active Players / Teams</option>
					</select>
				<input name="bblm_filter_submit" type="submit" id="bblm_filter_submit" value="Filter" /></p>
				</form>

<?php
				  /////////////////////////
				 // Top Scoring Players //
				/////////////////////////
				$statsql = 'SELECT P.WPID AS PID, T.WPID, SUM(M.mp_td) AS VALUE, R.pos_name, P.p_status, T.t_active FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'position R WHERE P.pos_id = R.pos_id AND M.p_id = P.p_id AND P.t_id = T.t_id AND M.mp_counts = 1 AND M.mp_td > 0 AND T.t_id != '.$bblm_star_team.' '.$statsqlmodp.'GROUP BY P.p_id ORDER BY VALUE DESC LIMIT '.$stat_limit;
				print("<h4>Top Scoring Players");
				if (0 == $period_alltime) {
					print(" (Active)");
				}
				print("</h4>\n");
				if ($topstats = $wpdb->get_results($statsql)) {
					if ($period_alltime) {
						print("	<p>Players who are <strong>highlighted</strong> are still active in the League.</p>\n");
					}
					print("<table class=\"bblm_table bblm_expandable\">\n	<tr>\n		<th class=\"bblm_tbl_stat\">#</th>\n		<th class=\"bblm_tbl_name\">Player</th>\n		<th>Position</th>\n		<th class=\"bblm_tbl_name\">Team</th>\n		<th class=\"bblm_tbl_stat\">TD</th>\n		</tr>\n");
					$zebracount = 1;
					$prevvalue = 0;

					foreach ($topstats as $ts) {
						if (($zebracount % 2) && (10 < $zebracount)) {
							print("	<tr class=\"bblm_tbl_hide\">\n");
						}
						else if (($zebracount % 2) && (10 >= $zebracount)) {
							print("	<tr>\n");
						}
						else if (10 < $zebracount) {
							print("	<tr class=\"bblm_tbl_alt bblm_tbl_hide\">\n");
						}
						else {
							print("	<tr class=\"bblm_tbl_alt\">\n");
						}
						if ($ts->VALUE > 0) {
							if ($prevvalue == $ts->VALUE) {
								print("	<td>-</td>\n");
							}
							else {
								print("	<td><strong>".$zebracount."</strong></td>\n");
							}
							if ($ts->t_active && $ts->p_status && $period_alltime) {
								print("	<td><strong>" . bblm_get_player_link( $ts->PID ) . "</strong></td>\n");
							}
							else {
								print("	<td>" . bblm_get_player_link( $ts->PID ) . "</td>\n");
							}
							print("	<td>" . esc_html( $ts->pos_name ). "</td>\n	<td>" . bblm_get_team_link( $ts->WPID ) . "</td>\n	<td>".$ts->VALUE."</td>\n	</tr>\n");
							$prevvalue = $ts->VALUE;
						}
						$zebracount++;
					}
					print("</table>\n");
				}
				else {
					print("	<div class=\"bblm_info\">\n		<p>No players have scored any Touchdowns!</p>\n	</div>\n");
				}

				  ////////////////////////
				 // Top Scoring Teams //
				////////////////////////
				$statsql = 'SELECT Z.WPID AS TWPID, SUM(T.tc_tdfor) AS VALUE, Z.r_id, Z.t_active FROM '.$wpdb->prefix.'team_comp T, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'team Z WHERE Z.t_id = T.t_id AND Z.t_show = 1 AND C.WPID = T.c_id AND C.c_counts = 1 '.$statsqlmodt.'GROUP BY T.t_id ORDER BY VALUE DESC LIMIT '.$stat_limit;
				print("<h4>Top Scoring Teams");
				if (0 == $period_alltime) {
					print(" (Active)");
				}
				print("</h4>\n");
				if ($topstats = $wpdb->get_results($statsql)) {
					if ($period_alltime) {
						print("	<p>Teams who are <strong>highlighted</strong> are still active in the League.</p>\n");
					}
					print("<table class=\"bblm_table bblm_expandable\">\n	<tr>\n		<th class=\"bblm_tbl_stat\">#</th>\n		<th>Team</th>\n		<th class=\"bblm_tbl_name\">Race</th>\n		<th class=\"bblm_tbl_stat\">TD</th>\n		</tr>\n");
					$zebracount = 1;
					$prevvalue = 0;

					foreach ($topstats as $ts) {
						if (($zebracount % 2) && (10 < $zebracount)) {
							print("	<tr class=\"bblm_tbl_hide\">\n");
						}
						else if (($zebracount % 2) && (10 >= $zebracount)) {
							print("	<tr>\n");
						}
						else if (10 < $zebracount) {
							print("	<tr class=\"bblm_tbl_alt tbblm_tbl_hide\">\n");
						}
						else {
							print("	<tr class=\"bblm_tbl_alt\">\n");
						}
						if ($ts->VALUE > 0) {
							if ($prevvalue == $ts->VALUE) {
								print("	<td>-</td>\n");
							}
							else {
								print("	<td><strong>".$zebracount."</strong></td>\n");
							}
							if ($ts->t_active && $period_alltime) {
							print("	<td><strong>" . bblm_get_team_link( $ts->TWPID ) . "</strong></td>\n");
							}
							else {
							print("	<td>" . bblm_get_team_link( $ts->TWPID ) . "</td>\n");
							}
							print("	<td>" . bblm_get_team_name( $ts->r_id ) . "</td>\n	<td>".$ts->VALUE."</td>\n	</tr>\n");
							$prevvalue = $ts->VALUE;
						}
						$zebracount++;
					}
					print("</table>\n");
				}
				else {
					print("	<div class=\"bblm_info\">\n		<p>No Teams have scored any Touchdowns!</p>\n	</div>\n");
				}


?>
</div><!-- .entry-content -->

<footer class="entry-footer">
	<p class="postmeta"><?php bblm_display_page_edit_link(); ?></p>
</footer><!-- .entry-footer -->

</article><!-- .post-ID -->

<?php do_action( 'bblm_template_after_content' ); ?>
<?php endwhile; ?>
<?php do_action( 'bblm_template_after_loop' ); ?>
<?php endif; ?>
<?php do_action( 'bblm_template_after_posts' ); ?>
</main><!-- #main -->
</div><!-- #primary -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>
