<?php
/**
 * BBowlLeagueMan Teamplate View Statistics - CAS
 *
 * Page Template to view CAS Statistics
 *
 * @author 		Blacksnotling
 * @category 	Template
 * @package 	BBowlLeagueMan/Templates
 */
 /*
  * Template Name: View Stats - CAS
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
     $mostxteamseasonsql = 'SELECT A.ats_value AS VALUE, T.WPID AS TWPID, A.sea_id FROM '.$wpdb->prefix.'awards_team_sea A, '.$wpdb->prefix.'team T WHERE A.t_id = T.t_id AND A.a_id = 12 ORDER BY VALUE DESC, A.sea_id ASC LIMIT 1';
		 $mxts = $wpdb->get_row($mostxteamseasonsql);
		 $mostxplayerseasonsql = 'SELECT A.aps_value AS VALUE, P.WPID AS PLAYER, T.WPID, A.sea_id, X.pos_name FROM '.$wpdb->prefix.'awards_player_sea A, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'position X WHERE P.pos_id = X.pos_id AND A.p_id = P.p_id AND P.t_id = T.t_id AND A.a_id = 12 ORDER BY VALUE DESC, A.sea_id ASC LIMIT 1';
		 $mxps = $wpdb->get_row($mostxplayerseasonsql);
		 $mostxteamcompsql = 'SELECT A.atc_value AS VALUE, T.WPID AS TWPID, A.c_id AS CWPID FROM '.$wpdb->prefix.'awards_team_comp A, '.$wpdb->prefix.'team T WHERE A.t_id = T.t_id AND A.a_id = 12 ORDER BY VALUE DESC, A.c_id ASC LIMIT 1';
		 $mxtc = $wpdb->get_row($mostxteamcompsql);
		 $mostxplayercompsql = 'SELECT A.apc_value AS VALUE, P.WPID AS PWPID, X.pos_name, T.WPID AS TWPID, A.c_id AS CWPID FROM '.$wpdb->prefix.'awards_player_comp A, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'position X WHERE P.pos_id = X.pos_id AND A.p_id = P.p_id AND P.t_id = T.t_id AND A.a_id = 12 ORDER BY VALUE DESC, A.c_id ASC LIMIT 1';
		 $mxpc = $wpdb->get_row($mostxplayercompsql);
		 $mostxteammatchsql = 'SELECT T.WPID AS TWPID, M.mt_cas AS VALUE, UNIX_TIMESTAMP(X.m_date) AS MDATE FROM '.$wpdb->prefix.'team T, '.$wpdb->prefix.'match_team M, '.$wpdb->prefix.'match X, '.$wpdb->prefix.'comp C WHERE T.t_id = M.t_id AND M.m_id = X.WPID AND C.WPID = X.c_id AND C.c_counts = 1 AND M.mt_cas > 0 ORDER BY VALUE DESC, M.m_id ASC LIMIT 1';
		 $mxtm = $wpdb->get_row($mostxteammatchsql);
     $mostxplayermatchsql = 'SELECT P.WPID AS PWPID, T.WPID AS TWPID, M.mp_cas AS VALUE, R.pos_name, UNIX_TIMESTAMP(X.m_date) AS MDATE FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'position R, '.$wpdb->prefix.'match X, '.$wpdb->prefix.'comp C  WHERE M.m_id = X.WPID AND C.WPID = X.c_id AND C.c_counts = 1 AND P.pos_id = R.pos_id AND M.p_id = P.p_id AND P.t_id = T.t_id AND M.mp_counts = 1 AND M.mp_cas > 0 ORDER BY VALUE DESC, M.m_id ASC LIMIT 1';
		 $mxpm = $wpdb->get_row($mostxplayermatchsql);
?>
		<ul>
			<li><strong>Most Casualties caused in a Season (Team)</strong>: <?php echo $mxts->VALUE; ?> (<?php echo bblm_get_team_link( $mxts->TWPID ); ?> - <?php echo bblm_get_season_link( $mxts->sea_id ); ?>)</li>
			<li><strong>Most Casualties caused in a Season (Player)</strong>: <?php echo $mxps->VALUE; ?> (<?php echo bblm_get_player_link( $mxps->PLAYER ); ?> - <?php echo esc_html( $mxps->pos_name ); ?> for <?php echo bblm_get_team_link( $mxps->WPID ); ?> - <?php echo bblm_get_season_link( $mxps->sea_id ); ?>)</li>
			<li><strong>Most Casualties caused in a Competition (Team)</strong>: <?php echo $mxtc->VALUE; ?> (<?php echo bblm_get_team_link( $mxtc->TWPID ); ?> - <?php echo bblm_get_competition_link( $mxtc->CWPID ); ?>)</li>
			<li><strong>Most Casualties caused in a Competition (Player)</strong>: <?php echo $mxpc->VALUE; ?> (<?php echo bblm_get_player_link( $mxpc->PWPID ); ?> - <?php echo esc_html( $mxpc->pos_name ); ?> for <?php echo bblm_get_team_link( $mxpc->TWPID ); ?> - <?php echo bblm_get_competition_link( $mxpc->CWPID ); ?>)</li></li>
			<li><strong>Most Casualties caused in a Match (Team)</strong>: <?php echo $mxtm->VALUE; ?> (<?php echo bblm_get_team_link( $mxtm->TWPID ); ?> - <?php echo date( "d.m.25y", $mxtm->MDATE ); ?>)</li>
			<li><strong>Most Casualties caused in a Match (Player)</strong>: <?php echo $mxpm->VALUE; ?> (<?php echo bblm_get_player_link( $mxpm->PWPID ); ?> - <?php echo esc_html( $mxpm->pos_name ); ?> for <?php echo bblm_get_team_link( $mxpm->TWPID ); ?> - <?php echo date( "d.m.25y", $mxpm->MDATE ); ?>)</li>
		</ul>

<?php
		 /*-- Stats part B -- */
?>
<!--		<ul>
			<li><strong>Teams who suffered most Deaths</strong>: <a href="#" title="Learn more about this Team">TEAM</a> (X)</li>
 			<li><strong>Teams who suffered most injuries</strong>: <a href="#" title="Learn more about this Team">TEAM</a> (X)</li>
 			<li><strong>Player who suffered most injuries</strong>: <a href="#" title="See more on this Player">PLAYER</a> (X - position for <a href="#" title="Learn more about this Team">TEAM</a>)</li>
 		</ul>

 			<h3>Players who Died on debut</h3>
 			<p>Not all players have an illustrious career. Here is a list of players who died on the debut:</p> -->


			<h3><?php echo __( 'Statistics tables','bblm' ); ?></h3>
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
				$statsqlmodt2 = "";

				//determine the status we are looking up
				if (!empty($_POST['bblm_status'])) {
					$status = $_POST['bblm_status'];
					//note that the sql is only modified if the "active" option is selected
					switch ($status) {
						case ("active" == $status):
					    	$statsqlmodp .= 'AND T.t_active = 1 AND P.p_status = 1 ';
					    	$statsqlmodt .= 'AND Z.t_active = 1 ';
								$statsqlmodt2 .= 'AND T.t_active = 1 '; //added as a work around to get most visious teams working
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
				  //////////////////////////
				 // Most VIcious Players //
				//////////////////////////
				$statsql = 'SELECT P.WPID AS PID, T.WPID, SUM(M.mp_cas) AS VALUE, R.pos_name, P.p_status, T.t_active FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'position R WHERE P.pos_id = R.pos_id AND M.p_id = P.p_id AND P.t_id = T.t_id AND M.mp_counts = 1 AND M.mp_cas > 0 AND T.t_id != '.$bblm_star_team.' '.$statsqlmodp.'GROUP BY P.p_id ORDER BY VALUE DESC LIMIT '.$stat_limit;
        echo '<h4>' . __('Most Vicious Players','bblm' );
				if (0 == $period_alltime) {
					print(" (Active)");
				}
				print("</h4>\n");
				if ($topstats = $wpdb->get_results($statsql)) {
					if ($period_alltime) {
						print("	<p>Players who are <strong>highlighted</strong> are still active in the <?php echo bblm_get_league_name(); ?>.</p>\n");
					}
          echo '<div role="region" aria-labelledby="Caption01" tabindex="0">';
					print("<table class=\"bblm_table bblm_expandable\">\n	<thead><tr>\n		<th class=\"bblm_tbl_stat\">#</th>\n		<th class=\"bblm_tbl_name\">Player</th>\n		<th>Position</th>\n		<th class=\"bblm_tbl_name\">Team</th>\n		<th class=\"bblm_tbl_stat\">CAS</th>\n		</tr></thead><tbody>\n");
					$zebracount = 1;
					$prevvalue = 0;

					foreach ($topstats as $ts) {
						if (($zebracount % 2) && (10 < $zebracount)) {
							print("	<tr class=\"bblm_tbl_alt bblm_tbl_hide\">\n");
						}
						else if (($zebracount % 2) && (10 >= $zebracount)) {
							print("	<tr class=\"bblm_tbl_alt\">\n");
						}
						else if (10 < $zebracount) {
							print("	<tr class=\"bblm_tbl_hide\">\n");
						}
						else {
							print("	<tr>\n");
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
							print("	<td>" . esc_html( $ts->pos_name ) . "</td>\n	<td>" . bblm_get_team_link( $ts->WPID ) . "</td>\n	<td>".$ts->VALUE."</td>\n	</tr>\n");
							$prevvalue = $ts->VALUE;
						}
						$zebracount++;
					}
					print("</tbody></table>\n</div>");
				}
				else {
					print("	<div class=\"bblm_info\">\n		<p>No players have caused any Casualties!</p>\n	</div>\n");
				}

				  ////////////////////////
				 // Most Vicious Teams //
				////////////////////////
				$statsql = 'SELECT Z.WPID AS TWPID, SUM(T.tc_casfor) AS VALUE, Z.r_id, Z.t_active FROM '.$wpdb->prefix.'team_comp T, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'team Z WHERE Z.t_id = T.t_id AND Z.t_show = 1 AND C.WPID = T.c_id AND C.c_counts = 1 '.$statsqlmodt.'GROUP BY T.t_id ORDER BY VALUE DESC LIMIT '.$stat_limit;
        echo '<h4>' . __('Most Vicious Teams','bblm' );
				if (0 == $period_alltime) {
					print(" (Active)");
				}
				print("</h4>\n");
				if ($topstats = $wpdb->get_results($statsql)) {
					if ($period_alltime) {
						print("	<p>Teams who are <strong>highlighted</strong> are still active in the League.</p>\n");
					}
          echo '<div role="region" aria-labelledby="Caption01" tabindex="0">';
					print("<table class=\"bblm_table bblm_expandable\">\n	<thead><tr>\n		<th class=\"bblm_tbl_stat\">#</th>\n		<th>Team</th>\n		<th class=\"bblm_tbl_name\">Race</th>\n		<th class=\"bblm_tbl_stat\">CAS</th>\n		</tr></thead><tbody>\n");
					$zebracount = 1;
					$prevvalue = 0;

					foreach ($topstats as $ts) {
						if (($zebracount % 2) && (10 < $zebracount)) {
              print("	<tr class=\"bblm_tbl_alt bblm_tbl_hide\">\n");
						}
						else if (($zebracount % 2) && (10 >= $zebracount)) {
              print("	<tr class=\"bblm_tbl_alt\">\n");
						}
						else if (10 < $zebracount) {
							print("	<tr class=\"bblm_tbl_hide\">\n");
						}
						else {
              print("	<tr>\n");
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
							print("	<td>" . bblm_get_race_name( $ts->r_id ) . "</td>\n	<td>".$ts->VALUE."</td>\n	</tr>\n");
							$prevvalue = $ts->VALUE;
						}
						$zebracount++;
					}
					print("</tbody></table>\n</div>");
				}
				else {
					print("	<div class=\"bblm_info\">\n		<p>No Teams have caused any Casualties!</p>\n	</div>\n");
				}

				  /////////////////////////
				 // Top Killing Players //
				/////////////////////////
				$statsql = 'SELECT P.WPID AS PID, COUNT(*) AS VALUE , E.pos_name, T.WPID, P.p_status, T.t_active FROM `'.$wpdb->prefix.'player_fate` F, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'position E, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'comp C WHERE P.t_id = T.t_id AND P.pos_id = E.pos_id AND (F.f_id = 1 OR F.f_id = 6 OR F.f_id = 7) AND P.p_id = F.pf_killer AND F.m_id = M.WPID AND M.c_id = C.WPID AND C.c_counts = 1 AND T.t_id != '.$bblm_star_team.' '.$statsqlmodp.'GROUP BY F.pf_killer ORDER BY VALUE DESC LIMIT '.$stat_limit;
        echo '<h4>' . __('Most Deadly Players','bblm' );
				if (0 == $period_alltime) {
					print(" (Active)");
				}
				print("</h4>\n");
				if ($topstats = $wpdb->get_results($statsql)) {
					if ($period_alltime) {
						print("	<p>Players who are <strong>highlighted</strong> are still active in the League.</p>\n");
					}
          echo '<div role="region" aria-labelledby="Caption01" tabindex="0">';
					print("<table class=\"bblm_table bblm_expandable\">\n	<thead><tr>\n		<th class=\"bblm_tbl_stat\">#</th>\n		<th class=\"bblm_tbl_name\">Player</th>\n		<th>Position</th>\n		<th class=\"bblm_tbl_name\">Team</th>\n		<th class=\"bblm_tbl_stat\">Kills</th>\n		</tr></thead><tbody>\n");
					$zebracount = 1;
					$prevvalue = 0;

					foreach ($topstats as $ts) {
						if (($zebracount % 2) && (10 < $zebracount)) {
              print("	<tr class=\"bblm_tbl_alt bblm_tbl_hide\">\n");
						}
						else if (($zebracount % 2) && (10 >= $zebracount)) {
              print("	<tr class=\"bblm_tbl_alt\">\n");
						}
						else if (10 < $zebracount) {
							print("	<tr class=\"bblm_tbl_hide\">\n");
						}
						else {
							print("	<tr>\n");
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
							print("	<td>" . esc_html( $ts->pos_name ) . "</td>\n	<td>" . bblm_get_team_link( $ts->WPID ) . "</td>\n	<td>".$ts->VALUE."</td>\n	</tr>\n");
							$prevvalue = $ts->VALUE;
						}
						$zebracount++;
					}
					print("</tbody></table>\n</div>");
				}
				else {
					print("	<div class=\"bblm_info\">\n		<p>No players have made any Kills!!</p>\n	</div>\n");
				}

				  ///////////////////////
				 // Top Killing Teams //
				///////////////////////
				$statsql = 'SELECT COUNT(*) AS VALUE , T.WPID AS TWPID, T.t_active, T.r_id FROM `'.$wpdb->prefix.'player_fate` F, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'comp C WHERE P.t_id = T.t_id AND (F.f_id = 1 OR F.f_id = 6 OR F.f_id = 7) AND P.p_id = F.pf_killer AND F.m_id = M.WPID AND M.c_id = C.WPID AND C.c_counts = 1 '.$statsqlmodt2.'GROUP BY T.t_id ORDER BY VALUE DESC, T.t_id ASC LIMIT '.$stat_limit;
        echo '<h4>' . __('Most Deadly Teams','bblm' );
				if (0 == $period_alltime) {
					print(" (Active)");
				}
				print("</h4>\n");
				if ($topstats = $wpdb->get_results($statsql)) {
					if ($period_alltime) {
						print("	<p>Teams who are <strong>highlighted</strong> are still active in the League.</p>\n");
					}
          echo '<div role="region" aria-labelledby="Caption01" tabindex="0">';
					print("<table class=\"bblm_table bblm_expandable\">\n	<thead><tr>\n		<th class=\"bblm_tbl_stat\">#</th>\n		<th>Team</th>\n		<th class=\"bblm_tbl_name\">Race</th>\n		<th class=\"bblm_tbl_stat\">Kills</th>\n		</tr></thead><tbody>\n");
					$zebracount = 1;
					$prevvalue = 0;

					foreach ($topstats as $ts) {
						if (($zebracount % 2) && (10 < $zebracount)) {
              print("	<tr class=\"bblm_tbl_alt bblm_tbl_hide\">\n");
						}
						else if (($zebracount % 2) && (10 >= $zebracount)) {
							print("	<tr class=\"bblm_tbl_alt\">\n");
						}
						else if (10 < $zebracount) {
							print("	<tr class=\"bblm_tbl_hide\">\n");
						}
						else {
							print("	<tr>\n");
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
							print("	<td>" . bblm_get_race_name( $ts->r_id ) . "</td>\n	<td>".$ts->VALUE."</td>\n	</tr>\n");
							$prevvalue = $ts->VALUE;
						}
						$zebracount++;
					}
					print("</tbody></table>\n</div>");
				}
				else {
					print("	<div class=\"bblm_info\">\n		<p>No teams have made any Kills!</p>\n	</div>\n");
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
