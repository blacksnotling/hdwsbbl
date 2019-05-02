<?php
/**
 * BBowlLeagueMan Teamplate View Cup
 *
 * Page Template to view Misc stats
 *
 * @author 		Blacksnotling
 * @category 	Template
 * @package 	BBowlLeagueMan/Templates
 */
 /*
  * Template Name: View Stats - Misc
  */
?>
<?php get_header(); ?>
	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
			<div class="entry">
				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<h2 class="entry-title"><?php the_title(); ?></h2>

				<?php the_content(); ?>
<?php

		$options = get_option('bblm_config');
		$stat_limit = htmlspecialchars($options['display_stats'], ENT_QUOTES);
		$bblm_star_team = htmlspecialchars($options['team_star'], ENT_QUOTES);

		/*-- Misc -- */
		$mostexpplayersql = 'SELECT Z.post_title AS PLAYER, Z.guid AS PLAYERLink, P.p_cost AS VALUE, T.WPID, X.pos_name FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'bb2wp J, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'position X, '.$wpdb->posts.' Z WHERE P.pos_id = X.pos_id AND P.t_id = T.t_id AND P.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = Z.ID AND T.type_id = 1 AND T.t_id != '.$bblm_star_team.' ORDER BY VALUE DESC, P.p_id ASC LIMIT 1';
		$mep = $wpdb->get_row($mostexpplayersql);
		$biggestattendcesql = 'SELECT UNIX_TIMESTAMP(M.m_date) AS MDATE, M.m_gate AS VALUE, P.post_title AS MATCHT, P.guid AS MATCHLink FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE M.m_id = J.tid AND J.prefix = \'m_\' AND J.pid = P.ID AND M.c_id = C.c_id AND C.c_show = 1 AND C.type_id = 1 AND C.c_counts = 1 ORDER BY M.m_gate DESC, MDATE ASC LIMIT 1';
		$bc = $wpdb->get_row($biggestattendcesql);
		$biggestattendcenonfinalsql = 'SELECT UNIX_TIMESTAMP(M.m_date) AS MDATE, M.m_gate AS VALUE, P.post_title AS MATCHT, P.guid AS MATCHLink FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE M.m_id = J.tid AND J.prefix = \'m_\' AND J.pid = P.ID AND M.c_id = C.c_id AND C.c_show = 1 AND C.type_id = 1 AND C.c_counts = 1 AND M.div_id != 1 AND M.div_id != 2 AND M.div_id != 3 ORDER BY M.m_gate DESC, MDATE ASC LIMIT 1';
		$bcn = $wpdb->get_row($biggestattendcenonfinalsql);
		$lowestattendcesql = 'SELECT UNIX_TIMESTAMP(M.m_date) AS MDATE, M.m_gate AS VALUE, P.post_title AS MATCHT, P.guid AS MATCHLink FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE M.m_id = J.tid AND J.prefix = \'m_\' AND J.pid = P.ID AND M.c_id = C.c_id AND C.c_show = 1 AND C.type_id = 1 AND C.c_counts = 1 AND M.m_gate > 0 ORDER BY M.m_gate ASC, MDATE ASC LIMIT 1';
		$lc = $wpdb->get_row($lowestattendcesql);
		$highesttvsql = 'SELECT T.WPID, P.mt_tv AS VALUE, UNIX_TIMESTAMP(M.m_date) AS MDATE FROM '.$wpdb->prefix.'match_team P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C WHERE P.t_id = T.t_id AND P.m_id = M.m_id AND M.c_id = C.c_id AND C.c_counts = 1 AND C.c_show = 1 AND C.type_id = 1 ORDER BY VALUE DESC, MDATE ASC LIMIT 0, 30 ';
		$htv = $wpdb->get_row($highesttvsql);
		$lowesttvsql = 'SELECT T.WPID, P.mt_tv AS VALUE, UNIX_TIMESTAMP(M.m_date) AS MDATE FROM '.$wpdb->prefix.'match_team P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C WHERE P.t_id = T.t_id AND P.m_id = M.m_id AND M.c_id = C.c_id AND C.c_counts = 1 AND C.c_show = 1 AND C.type_id = 1 ORDER BY VALUE ASC, MDATE ASC LIMIT 0, 30 ';
		$ltv = $wpdb->get_row($lowesttvsql);
		$teammostplayerssql = 'SELECT COUNT(*) AS VALUE, T.WPID FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T WHERE P.t_id = T.t_id AND T.t_id != '.$bblm_star_team.' GROUP BY P.t_id ORDER BY VALUE DESC, P.t_id ASC LIMIT 1';
		$tmp = $wpdb->get_row($teammostplayerssql);

		//Bits for the Player Career
		$matchnumsql = 'SELECT COUNT(*) AS MATCHNUM FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE M.c_id = C.c_id AND C.c_counts = 1 AND C.c_show = 1 AND C.type_id = 1 AND M.m_id = J.tid AND J.prefix = \'m_\' AND J.pid = P.ID';
		$matchnum = $wpdb->get_var($matchnumsql);
		$matchrecsql = 'SELECT COUNT(*) FROM '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'team T WHERE T.t_id = M.t_id AND M.mp_counts = 1 AND T.type_id = 1';
		$matchrec = $wpdb->get_var($matchrecsql);
		$playernumsql = 'SELECT COUNT(*) AS playernum FROM '.$wpdb->prefix.'player M, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE M.t_id = T.t_id AND T.t_show = 1 AND T.type_id = 1 AND M.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = P.ID';
		$playernum = $wpdb->get_var($playernumsql);
?>


		<ul>
			<li><strong>Most Expensive Player</strong>: <?php print(number_format($mep->VALUE)); ?>gp (<a href="<?php print($mep->PLAYERLink); ?>" title="Learn more about this player"><?php print($mep->PLAYER); ?></a> - <?php print($mep->pos_name); ?> for <a href="<?php print( get_post_permalink( $mep->WPID ) ); ?>" title="Read more about this Team"><?php print( esc_html( get_the_title( $mep->WPID ) )  ); ?></a>)</li>
			<li><strong>Highest Recorded Attendance (Final or Semi-Final)</strong>: <?php print(number_format($bc->VALUE)); ?> fans (<a href="<?php print($bc->MATCHLink); ?>" title="Read the full report of this match"><?php print($bc->MATCHT); ?></a>)</li>
 			<li><strong>Highest Recorded Attendance</strong>: <?php print(number_format($bcn->VALUE)); ?> fans (<a href="<?php print($bcn->MATCHLink); ?>" title="Read the full report of this match"><?php print($bcn->MATCHT); ?></a>)</li>
 			<li><strong>Lowest Recorded Attendance</strong>: <?php print(number_format($lc->VALUE)); ?> fans (<a href="<?php print($lc->MATCHLink); ?>" title="Read the full report of this match"><?php print($lc->MATCHT); ?></a>)</li>
			<li><strong>Highest Recorded TV</strong>: <?php print(number_format($htv->VALUE)); ?>gp (<a href="<?php print( get_post_permalink( $htv->WPID ) ); ?>" title="Read more about this Team"><?php print( esc_html( get_the_title( $htv->WPID ) )  ); ?></a> - <?php print(date("d.m.25y", $htv->MDATE)); ?>)</li>
			<li><strong>Lowest Recorded TV</strong>: <?php print(number_format($ltv->VALUE)); ?>gp (<a href="<?php print( get_post_permalink( $ltv->WPID ) ); ?>" title="Read more about this Team"><?php print( esc_html( get_the_title( $ltv->WPID ) )  ); ?></a> - <?php print(date("d.m.25y", $ltv->MDATE)); ?>)</li>
			<li><strong>Team with most players</strong>: <a href="<?php print( get_post_permalink( $tmp->WPID ) ); ?>" title="Read more about this Team"><?php print( esc_html( get_the_title( $tmp->WPID ) )  ); ?></a> (<?php print($tmp->VALUE); ?>)</li>
			<li><strong>Average Career length of a Player</strong>: <?php print(round($matchrec/$playernum,1)); ?> games</li>
		</ul>




		<h3>Performance related Stats</h3>
		<h4>Star Player Point Related</h4>
<?php
		 /*-- SPP -- */
		 $mostxplayerseasonsql = 'SELECT A.aps_value AS VALUE, L.post_title AS PLAYER, L.guid AS PLAYERLink, T.WPID, S.post_title, S.guid, X.pos_name FROM '.$wpdb->prefix.'awards_player_sea A, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' S, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'bb2wp K, '.$wpdb->posts.' L, '.$wpdb->prefix.'position X WHERE P.pos_id = X.pos_id AND P.p_id = K.tid AND K.prefix = \'p_\' AND K.pid = L.ID AND A.sea_id = J.tid AND J.prefix = \'sea_\' AND J.pid = S.ID AND A.p_id = P.p_id AND P.t_id = T.t_id AND T.type_id = 1 AND A.a_id = 10 ORDER BY VALUE DESC, A.sea_id ASC LIMIT 1';
		 $mxps = $wpdb->get_row($mostxplayerseasonsql);
		 $mostxplayercompsql = 'SELECT A.apc_value AS VALUE, L.post_title AS PLAYER, L.guid AS PLAYERLink, T.WPID, S.post_title, S.guid, X.pos_name FROM '.$wpdb->prefix.'awards_player_comp A, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' S, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'bb2wp K, '.$wpdb->posts.' L, '.$wpdb->prefix.'position X WHERE P.pos_id = X.pos_id AND P.p_id = K.tid AND K.prefix = \'p_\' AND K.pid = L.ID AND A.c_id = J.tid AND J.prefix = \'c_\' AND J.pid = S.ID AND A.p_id = P.p_id AND P.t_id = T.t_id AND T.type_id = 1 AND A.a_id = 10 ORDER BY VALUE DESC, A.c_id ASC LIMIT 1';
		 $mxpc = $wpdb->get_row($mostxplayercompsql);
		 $mostxplayermatchsql = 'SELECT Y.post_title AS PLAYER, T.WPID, Y.guid AS PLAYERLink, M.mp_spp AS VALUE, R.pos_name, UNIX_TIMESTAMP(X.m_date) AS MDATE FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' Y, '.$wpdb->prefix.'position R, '.$wpdb->prefix.'match X, '.$wpdb->prefix.'comp C WHERE M.m_id = X.m_id AND C.c_id = X.c_id AND C.c_counts = 1 AND C.c_show = 1 AND C.type_id = 1 AND P.pos_id = R.pos_id AND P.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = Y.ID AND M.p_id = P.p_id AND P.t_id = T.t_id AND M.mp_counts = 1 AND M.mp_td > 0 ORDER BY VALUE DESC, M.m_id ASC LIMIT 1';
		 $mxpm = $wpdb->get_row($mostxplayermatchsql);
?>
		<ul>
			<li><strong>Most Star Player Points earnt in a Season (Player)</strong>: <?php print($mxps->VALUE); ?> (<a href="<?php print($mxps->PLAYERLink); ?>" title="See more on this Player"><?php print($mxps->PLAYER); ?></a> - <?php print($mxps->pos_name); ?> for <a href="<?php print( get_post_permalink( $mxps->WPID ) ); ?>" title="Learn more about this Team"><?php print( esc_html( get_the_title( $mxps->WPID ) )  ); ?></a> - <a href="<?php print($mxps->guid); ?>" title="Read more on this Season"><?php print($mxps->post_title); ?></a>)</li>
			<li><strong>Most Star Player Points earnt in a Competition (Player)</strong>: <?php print($mxpc->VALUE); ?> (<a href="<?php print($mxpc->PLAYERLink); ?>" title="See more on this Player"><?php print($mxpc->PLAYER); ?></a> - <?php print($mxpc->pos_name); ?> for <a href="<?php print( get_post_permalink( $mxpc->WPID ) ); ?>" title="Learn more about this Team"><?php print( esc_html( get_the_title( $mxpc->WPID ) )  ); ?></a> - <a href="<?php print($mxpc->guid); ?>" title="Read more on this Competition"><?php print($mxpc->post_title); ?></a>)</li></li>
			<li><strong>Most Star Player Points earnt in a Match (Player)</strong>: <?php print($mxpm->VALUE); ?> (<a href="<?php print($mxpm->PLAYERLink); ?>" title="See more on this Player"><?php print($mxpm->PLAYER); ?></a> - <?php print($mxpm->pos_name); ?> for <a href="<?php print( get_post_permalink( $mxpm->WPID ) ); ?>" title="Learn more about this Team"><?php print( esc_html( get_the_title( $mxpm->WPID ) )  ); ?></a> - <?php print(date("d.m.25y", $mxpm->MDATE)); ?>)</li>
		</ul>

		<h4>Completion Related</h4>
<?php
		 /*-- COMPLETIONS -- */
		 $mostxteamseasonsql = 'SELECT A.ats_value AS VALUE, T.WPID, S.post_title, S.guid FROM '.$wpdb->prefix.'awards_team_sea A, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' S WHERE A.sea_id = J.tid AND J.prefix = \'sea_\' AND J.pid = S.ID AND A.t_id = T.t_id AND T.type_id = 1 AND A.a_id = 14 ORDER BY VALUE DESC, A.sea_id ASC LIMIT 1';
		 $mxts = $wpdb->get_row($mostxteamseasonsql);
		 $mostxplayerseasonsql = 'SELECT A.aps_value AS VALUE, L.post_title AS PLAYER, L.guid AS PLAYERLink, T.WPID, S.post_title, S.guid, X.pos_name FROM '.$wpdb->prefix.'awards_player_sea A, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' S, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'bb2wp K, '.$wpdb->posts.' L, '.$wpdb->prefix.'position X WHERE P.pos_id = X.pos_id AND P.p_id = K.tid AND K.prefix = \'p_\' AND K.pid = L.ID AND A.sea_id = J.tid AND J.prefix = \'sea_\' AND J.pid = S.ID AND A.p_id = P.p_id AND P.t_id = T.t_id AND T.type_id = 1 AND A.a_id = 14 ORDER BY VALUE DESC, A.sea_id ASC LIMIT 1';
		 $mxps = $wpdb->get_row($mostxplayerseasonsql);
		 $mostxteamcompsql = 'SELECT A.atc_value AS VALUE, T.WPID, S.post_title, S.guid FROM '.$wpdb->prefix.'awards_team_comp A, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' S WHERE A.c_id = J.tid AND J.prefix = \'c_\' AND J.pid = S.ID AND A.t_id = T.t_id AND T.type_id = 1 AND A.a_id = 14 ORDER BY VALUE DESC, A.c_id ASC LIMIT 1';
		 $mxtc = $wpdb->get_row($mostxteamcompsql);
		 $mostxplayercompsql = 'SELECT A.apc_value AS VALUE, L.post_title AS PLAYER, L.guid AS PLAYERLink, T.WPID, S.post_title, S.guid, X.pos_name FROM '.$wpdb->prefix.'awards_player_comp A, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' S, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'bb2wp K, '.$wpdb->posts.' L, '.$wpdb->prefix.'position X WHERE P.pos_id = X.pos_id AND P.p_id = K.tid AND K.prefix = \'p_\' AND K.pid = L.ID AND A.c_id = J.tid AND J.prefix = \'c_\' AND J.pid = S.ID AND A.p_id = P.p_id AND P.t_id = T.t_id AND T.type_id = 1 AND A.a_id = 14 ORDER BY VALUE DESC, A.c_id ASC LIMIT 1';
		 $mxpc = $wpdb->get_row($mostxplayercompsql);
		 $mostxteammatchsql = 'SELECT T.WPID, M.mt_comp AS VALUE, UNIX_TIMESTAMP(X.m_date) AS MDATE FROM '.$wpdb->prefix.'team T, '.$wpdb->prefix.'match_team M, '.$wpdb->prefix.'match X, '.$wpdb->prefix.'comp C WHERE T.t_id = M.t_id AND M.m_id = X.m_id AND C.c_id = X.c_id AND C.c_counts = 1 AND C.c_show = 1 AND C.type_id = 1 AND M.mt_comp > 0 ORDER BY VALUE DESC, M.m_id ASC LIMIT 1';
		 $mxtm = $wpdb->get_row($mostxteammatchsql);
		 $mostxplayermatchsql = 'SELECT Y.post_title AS PLAYER, T.WPID, Y.guid AS PLAYERLink, M.mp_comp AS VALUE, R.pos_name, UNIX_TIMESTAMP(X.m_date) AS MDATE FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' Y, '.$wpdb->prefix.'position R, '.$wpdb->prefix.'match X, '.$wpdb->prefix.'comp C WHERE M.m_id = X.m_id AND C.c_id = X.c_id AND C.c_counts = 1 AND C.c_show = 1 AND C.type_id = 1 AND P.pos_id = R.pos_id AND P.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = Y.ID AND M.p_id = P.p_id AND P.t_id = T.t_id AND M.mp_counts = 1 AND M.mp_comp > 0 ORDER BY VALUE DESC, M.m_id ASC LIMIT 1';
		 $mxpm = $wpdb->get_row($mostxplayermatchsql);
?>
		<ul>
			<li><strong>Most Passes completed in a Season (Team)</strong>: <?php print($mxts->VALUE); ?> (<a href="<?php print( get_post_permalink( $mxts->WPID ) ); ?>" title="Learn more about this Team"><?php print( esc_html( get_the_title( $mxts->WPID ) )  ); ?></a> - <a href="<?php print($mxts->guid); ?>" title="Read more on this Season"><?php print($mxts->post_title); ?></a>)</li>
			<li><strong>Most Passes completed in a Season (Player)</strong>: <?php print($mxps->VALUE); ?> (<a href="<?php print($mxps->PLAYERLink); ?>" title="See more on this Player"><?php print($mxps->PLAYER); ?></a> - <?php print($mxps->pos_name); ?> for <a href="<?php print( get_post_permalink( $mxps->WPID ) ); ?>" title="Learn more about this Team"><?php print( esc_html( get_the_title( $mxps->WPID ) )  ); ?></a> - <a href="<?php print($mxps->guid); ?>" title="Read more on this Season"><?php print($mxps->post_title); ?></a>)</li>
			<li><strong>Most Passes completed in a Competition (Team)</strong>: <?php print($mxtc->VALUE); ?> (<a href="<?php print( get_post_permalink( $mxtc->WPID ) ); ?>" title="Learn more about this Team"><?php print( esc_html( get_the_title( $mxtc->WPID ) )  ); ?></a> - <a href="<?php print($mxtc->guid); ?>" title="Read more on this Competition"><?php print($mxtc->post_title); ?></a>)</li>
			<li><strong>Most Passes completed in a Competition (Player)</strong>: <?php print($mxpc->VALUE); ?> (<a href="<?php print($mxpc->PLAYERLink); ?>" title="See more on this Player"><?php print($mxpc->PLAYER); ?></a> - <?php print($mxpc->pos_name); ?> for <a href="<?php print( get_post_permalink( $mxpc->WPID ) ); ?>" title="Learn more about this Team"><?php print( esc_html( get_the_title( $mxpc->WPID ) )  ); ?></a> - <a href="<?php print($mxpc->guid); ?>" title="Read more on this Competition"><?php print($mxpc->post_title); ?></a>)</li></li>
			<li><strong>Most Passes completed in a Match (Team)</strong>: <?php print($mxtm->VALUE); ?> (<a href="<?php print( get_post_permalink( $mxtm->WPID ) ); ?>" title="Learn more about this Team"><?php print( esc_html( get_the_title( $mxtm->WPID ) )  ); ?></a> - <?php print(date("d.m.25y", $mxtm->MDATE)); ?>)</li>
			<li><strong>Most Passes completed in a Match (Player)</strong>: <?php print($mxpm->VALUE); ?> (<a href="<?php print($mxpm->PLAYERLink); ?>" title="See more on this Player"><?php print($mxpm->PLAYER); ?></a> - <?php print($mxpm->pos_name); ?> for <a href="<?php print( get_post_permalink( $mxpm->WPID ) ); ?>" title="Learn more about this Team"><?php print( esc_html( get_the_title( $mxpm->WPID ) )  ); ?></a> - <?php print(date("d.m.25y", $mxpm->MDATE)); ?>)</li>
		</ul>

		<h4>Interception Related</h4>
<?php
		 /*-- Interceptions -- */
		 $mostxteamseasonsql = 'SELECT A.ats_value AS VALUE, T.WPID, S.post_title, S.guid FROM '.$wpdb->prefix.'awards_team_sea A, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' S WHERE A.sea_id = J.tid AND J.prefix = \'sea_\' AND J.pid = S.ID AND A.t_id = T.t_id AND T.type_id = 1 AND A.a_id = 13 ORDER BY VALUE DESC, A.sea_id ASC LIMIT 1';
		 $mxts = $wpdb->get_row($mostxteamseasonsql);
		 $mostxplayerseasonsql = 'SELECT A.aps_value AS VALUE, L.post_title AS PLAYER, L.guid AS PLAYERLink, T.WPID, S.post_title, S.guid, X.pos_name FROM '.$wpdb->prefix.'awards_player_sea A, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' S, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'bb2wp K, '.$wpdb->posts.' L, '.$wpdb->prefix.'position X WHERE P.pos_id = X.pos_id AND P.p_id = K.tid AND K.prefix = \'p_\' AND K.pid = L.ID AND A.sea_id = J.tid AND J.prefix = \'sea_\' AND J.pid = S.ID AND A.p_id = P.p_id AND P.t_id = T.t_id AND T.type_id = 1 AND A.a_id = 13 ORDER BY VALUE DESC, A.sea_id ASC LIMIT 1';
		 $mxps = $wpdb->get_row($mostxplayerseasonsql);
		 $mostxteamcompsql = 'SELECT A.atc_value AS VALUE, T.WPID, S.post_title, S.guid FROM '.$wpdb->prefix.'awards_team_comp A, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' S WHERE A.c_id = J.tid AND J.prefix = \'c_\' AND J.pid = S.ID AND A.t_id = T.t_id AND T.type_id = 1 AND A.a_id = 13 ORDER BY VALUE DESC, A.c_id ASC LIMIT 1';
		 $mxtc = $wpdb->get_row($mostxteamcompsql);
		 $mostxplayercompsql = 'SELECT A.apc_value AS VALUE, L.post_title AS PLAYER, L.guid AS PLAYERLink, T.WPID, S.post_title, S.guid, X.pos_name FROM '.$wpdb->prefix.'awards_player_comp A, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' S, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'bb2wp K, '.$wpdb->posts.' L, '.$wpdb->prefix.'position X WHERE P.pos_id = X.pos_id AND P.p_id = K.tid AND K.prefix = \'p_\' AND K.pid = L.ID AND A.c_id = J.tid AND J.prefix = \'c_\' AND J.pid = S.ID AND A.p_id = P.p_id AND P.t_id = T.t_id AND T.type_id = 1 AND A.a_id = 13 ORDER BY VALUE DESC, A.c_id ASC LIMIT 1';
		 $mxpc = $wpdb->get_row($mostxplayercompsql);
		 $mostxteammatchsql = 'SELECT T.WPID, M.mt_int AS VALUE, UNIX_TIMESTAMP(X.m_date) AS MDATE FROM '.$wpdb->prefix.'team T, '.$wpdb->prefix.'match_team M, '.$wpdb->prefix.'match X, '.$wpdb->prefix.'comp C WHERE T.t_id = M.t_id AND M.m_id = X.m_id AND C.c_id = X.c_id AND C.c_counts = 1 AND C.c_show = 1 AND C.type_id = 1 AND M.mt_int > 0 ORDER BY VALUE DESC, M.m_id ASC LIMIT 1';
		 $mxtm = $wpdb->get_row($mostxteammatchsql);
		 $mostxplayermatchsql = 'SELECT Y.post_title AS PLAYER, T.WPID, Y.guid AS PLAYERLink, M.mp_int AS VALUE, R.pos_name, UNIX_TIMESTAMP(X.m_date) AS MDATE FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' Y, '.$wpdb->prefix.'position R, '.$wpdb->prefix.'match X, '.$wpdb->prefix.'comp C WHERE M.m_id = X.m_id AND C.c_id = X.c_id AND C.c_counts = 1 AND C.c_show = 1 AND C.type_id = 1 AND P.pos_id = R.pos_id AND P.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = Y.ID AND M.p_id = P.p_id AND P.t_id = T.t_id AND M.mp_counts = 1 AND M.mp_int > 0 ORDER BY VALUE DESC, M.m_id ASC LIMIT 1';
		 $mxpm = $wpdb->get_row($mostxplayermatchsql);
?>
		<ul>
			<li><strong>Most Interceptions made in a Season (Team)</strong>: <?php print($mxts->VALUE); ?> (<a href="<?php print( get_post_permalink( $mxts->WPID ) ); ?>" title="Learn more about this Team"><?php print( esc_html( get_the_title( $mxts->WPID ) )  ); ?></a> - <a href="<?php print($mxts->guid); ?>" title="Read more on this Season"><?php print($mxts->post_title); ?></a>)</li>
			<li><strong>Most Interceptions made in a Season (Player)</strong>: <?php print($mxps->VALUE); ?> (<a href="<?php print($mxps->PLAYERLink); ?>" title="See more on this Player"><?php print($mxps->PLAYER); ?></a> - <?php print($mxps->pos_name); ?> for <a href="<?php print(get_post_permalink( $mxps->WPID )); ?>" title="Learn more about this Team"><?php print( esc_html( get_the_title( $mxps->WPID ) )  ); ?></a> - <a href="<?php print($mxps->guid); ?>" title="Read more on this Season"><?php print($mxps->post_title); ?></a>)</li>
			<li><strong>Most Interceptions made in a Competition (Team)</strong>: <?php print($mxtc->VALUE); ?> (<a href="<?php print( get_post_permalink( $mxtc->WPID ) ); ?>" title="Learn more about this Team"><?php print( esc_html( get_the_title( $mxtc->WPID ) )  ); ?></a> - <a href="<?php print($mxtc->guid); ?>" title="Read more on this Competition"><?php print($mxtc->post_title); ?></a>)</li>
			<li><strong>Most Interceptions made in a Competition (Player)</strong>: <?php print($mxpc->VALUE); ?> (<a href="<?php print($mxpc->PLAYERLink); ?>" title="See more on this Player"><?php print($mxpc->PLAYER); ?></a> - <?php print($mxpc->pos_name); ?> for <a href="<?php print( get_post_permalink( $mxpc->WPID ) ); ?>" title="Learn more about this Team"><?php print( esc_html( get_the_title( $mxpc->WPID ) )  ); ?></a> - <a href="<?php print($mxpc->guid); ?>" title="Read more on this Competition"><?php print($mxpc->post_title); ?></a>)</li></li>
			<li><strong>Most Interceptions made in a Match (Team)</strong>: <?php print($mxtm->VALUE); ?> (<a href="<?php print( get_post_permalink( $mxtm->WPID ) ); ?>" title="Learn more about this Team"><?php print( esc_html( get_the_title( $mxtm->WPID ) )  ); ?></a> - <?php print(date("d.m.25y", $mxtm->MDATE)); ?>)</li>
			<li><strong>Most Interceptions made in a Match (Player)</strong>: <?php print($mxpm->VALUE); ?> (<a href="<?php print($mxpm->PLAYERLink); ?>" title="See more on this Player"><?php print($mxpm->PLAYER); ?></a> - <?php print($mxpm->pos_name); ?> for <a href="<?php print( get_post_permalink( $mxpm->WPID ) ); ?>" title="Learn more about this Team"><?php print( esc_html( get_the_title( $mxpm->WPID ) )  ); ?></a> - <?php print(date("d.m.25y", $mxpm->MDATE)); ?>)</li>
		</ul>



			<h3>Statistics tables</h3>
<?php
				  ///////////////////////////////
				 // Filtering of Stats tables //
				///////////////////////////////

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
				} else {
					$status = "";
				}

?>
				<form name="bblm_filterstats" method="post" id="statstable" action="#statstable">
				<p>For the below Statistics tables, show the records for
					<select name="bblm_status" id="bblm_status">
						<option value="alltime"<?php if ("alltime" == $status) { print(" selected=\"selected\""); } ?>>All Time</option>
						<option value="active"<?php if ("active" == $status) { print(" selected=\"selected\""); } ?>>Active Players</option>
					</select>
				<input name="bblm_filter_submit" type="submit" id="bblm_filter_submit" value="Filter" /></p>
				</form>

<?php
				  /////////////////////////
				 // Best Passing Players //
				/////////////////////////
				$statsql = 'SELECT Y.post_title, T.WPID, Y.guid, SUM(M.mp_comp) AS VALUE, R.pos_name, P.p_status, T.t_active FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' Y, '.$wpdb->prefix.'position R WHERE P.pos_id = R.pos_id AND P.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = Y.ID AND M.p_id = P.p_id AND P.t_id = T.t_id AND M.mp_counts = 1 AND M.mp_comp > 0 AND T.t_id != '.$bblm_star_team.' '.$statsqlmodp.'GROUP BY P.p_id ORDER BY VALUE DESC LIMIT '.$stat_limit;
				print("<h4>Best Passing Players");
				if (0 == $period_alltime) {
					print(" (Active)");
				}
				print("</h4>\n");
				if ($topstats = $wpdb->get_results($statsql)) {
					if ($period_alltime) {
						print("	<p>Players who are <strong>highlighted</strong> are still active in the League.</p>\n");
					}
					print("<table class=\"expandable\">\n	<tr>\n		<th class=\"tbl_stat\">#</th>\n		<th class=\"tbl_name\">Player</th>\n		<th>Position</th>\n		<th class=\"tbl_name\">Team</th>\n		<th class=\"tbl_stat\">COMP</th>\n		</tr>\n");
					$zebracount = 1;
					$prevvalue = 0;

					foreach ($topstats as $ts) {
						if (($zebracount % 2) && (10 < $zebracount)) {
							print("	<tr class=\"tb_hide\">\n");
						}
						else if (($zebracount % 2) && (10 >= $zebracount)) {
							print("	<tr>\n");
						}
						else if (10 < $zebracount) {
							print("	<tr class=\"tbl_alt tb_hide\">\n");
						}
						else {
							print("	<tr class=\"tbl_alt\">\n");
						}
						if ($ts->VALUE > 0) {
							if ($prevvalue == $ts->VALUE) {
								print("	<td>-</td>\n");
							}
							else {
								print("	<td><strong>".$zebracount."</strong></td>\n");
							}
							if ($ts->t_active && $ts->p_status && $period_alltime) {
								print("	<td><strong><a href=\"".$ts->guid."\" title=\"View more details on ".$ts->post_title."\">".$ts->post_title."</a></strong></td>\n");
							}
							else {
								print("	<td><a href=\"".$ts->guid."\" title=\"View more details on ".$ts->post_title."\">".$ts->post_title."</a></td>\n");
							}
							print("	<td>".$ts->pos_name."</td>\n	<td><a href=\"".  get_post_permalink( $ts->WPID ) ."\" title=\"Read more on this team\">" . esc_html( get_the_title( $ts->WPID ) ) . "</a></td>\n	<td>".$ts->VALUE."</td>\n	</tr>\n");
							$prevvalue = $ts->VALUE;
						}
						$zebracount++;
					}
					print("</table>\n");
				}
				else {
					print("	<div class=\"info\">\n		<p>No players have made any successfull passes!!</p>\n	</div>\n");
				}

				  //////////////////////////////
				 // Top Interceptors Players //
				//////////////////////////////
				$statsql = 'SELECT Y.post_title, T.WPID, Y.guid, SUM(M.mp_int) AS VALUE, R.pos_name, P.p_status, T.t_active FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' Y, '.$wpdb->prefix.'position R WHERE P.pos_id = R.pos_id AND P.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = Y.ID AND M.p_id = P.p_id AND P.t_id = T.t_id AND M.mp_counts = 1 AND M.mp_int > 0  AND T.t_id != '.$bblm_star_team.' '.$statsqlmodp.'GROUP BY P.p_id ORDER BY VALUE DESC LIMIT '.$stat_limit;
				print("<h4>Top Intercepting Players");
				if (0 == $period_alltime) {
					print(" (Active)");
				}
				print("</h4>\n");
				if ($topstats = $wpdb->get_results($statsql)) {
					if ($period_alltime) {
						print("	<p>Players who are <strong>highlighted</strong> are still active in the League.</p>\n");
					}
					print("<table class=\"expandable\">\n	<tr>\n		<th class=\"tbl_stat\">#</th>\n		<th class=\"tbl_name\">Player</th>\n		<th>Position</th>\n		<th class=\"tbl_name\">Team</th>\n		<th class=\"tbl_stat\">INT</th>\n		</tr>\n");
					$zebracount = 1;
					$prevvalue = 0;

					foreach ($topstats as $ts) {
						if (($zebracount % 2) && (10 < $zebracount)) {
							print("	<tr class=\"tb_hide\">\n");
						}
						else if (($zebracount % 2) && (10 >= $zebracount)) {
							print("	<tr>\n");
						}
						else if (10 < $zebracount) {
							print("	<tr class=\"tbl_alt tb_hide\">\n");
						}
						else {
							print("	<tr class=\"tbl_alt\">\n");
						}
						if ($ts->VALUE > 0) {
							if ($prevvalue == $ts->VALUE) {
								print("	<td>-</td>\n");
							}
							else {
								print("	<td><strong>".$zebracount."</strong></td>\n");
							}
							if ($ts->t_active && $ts->p_status && $period_alltime) {
								print("	<td><strong><a href=\"".$ts->guid."\" title=\"View more details on ".$ts->post_title."\">".$ts->post_title."</a></strong></td>\n");
							}
							else {
								print("	<td><a href=\"".$ts->guid."\" title=\"View more details on ".$ts->post_title."\">".$ts->post_title."</a></td>\n");
							}
							print("	<td>".$ts->pos_name."</td>\n	<td><a href=\"".  get_post_permalink( $ts->WPID ) ."\" title=\"Read more on this team\">" . esc_html( get_the_title( $ts->WPID ) ) . "</a></td>\n	<td>".$ts->VALUE."</td>\n	</tr>\n");
							$prevvalue = $ts->VALUE;
						}
						$zebracount++;
					}
					print("</table>\n");
				}
				else {
					print("	<div class=\"info\">\n		<p>No players have made any successfull Interceptions!!</p>\n	</div>\n");
				}

				  //////////////////
				 // MVPs Players //
				//////////////////
				$statsql = 'SELECT Y.post_title, T.WPID, Y.guid, SUM(M.mp_mvp) AS VALUE, R.pos_name, P.p_status, T.t_active FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' Y, '.$wpdb->prefix.'position R WHERE P.pos_id = R.pos_id AND P.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = Y.ID AND M.p_id = P.p_id AND P.t_id = T.t_id AND M.mp_counts = 1 AND M.mp_mvp > 0  AND T.t_id != '.$bblm_star_team.' '.$statsqlmodp.'GROUP BY P.p_id ORDER BY VALUE DESC LIMIT '.$stat_limit;
				print("<h4>Most Valued Players");
				if (0 == $period_alltime) {
					print(" (Active)");
				}
				print("</h4>\n");
				if ($topstats = $wpdb->get_results($statsql)) {
					if ($period_alltime) {
						print("	<p>Players who are <strong>highlighted</strong> are still active in the League.</p>\n");
					}
					print("<table class=\"expandable\">\n	<tr>\n		<th class=\"tbl_stat\">#</th>\n		<th class=\"tbl_name\">Player</th>\n		<th>Position</th>\n		<th class=\"tbl_name\">Team</th>\n		<th class=\"tbl_stat\">MVP</th>\n		</tr>\n");
					$zebracount = 1;
					$prevvalue = 0;

					foreach ($topstats as $ts) {
						if (($zebracount % 2) && (10 < $zebracount)) {
							print("	<tr class=\"tb_hide\">\n");
						}
						else if (($zebracount % 2) && (10 >= $zebracount)) {
							print("	<tr>\n");
						}
						else if (10 < $zebracount) {
							print("	<tr class=\"tbl_alt tb_hide\">\n");
						}
						else {
							print("	<tr class=\"tbl_alt\">\n");
						}
						if ($ts->VALUE > 0) {
							if ($prevvalue == $ts->VALUE) {
								print("	<td>-</td>\n");
							}
							else {
								print("	<td><strong>".$zebracount."</strong></td>\n");
							}
							if ($ts->t_active && $ts->p_status && $period_alltime) {
								print("	<td><strong><a href=\"".$ts->guid."\" title=\"View more details on ".$ts->post_title."\">".$ts->post_title."</a></strong></td>\n");
							}
							else {
								print("	<td><a href=\"".$ts->guid."\" title=\"View more details on ".$ts->post_title."\">".$ts->post_title."</a></td>\n");
							}
							print("	<td>".$ts->pos_name."</td>\n	<td><a href=\"".  get_post_permalink( $ts->WPID ) ."\" title=\"Read more on this team\">" . esc_html( get_the_title( $ts->WPID ) ) . "</a></td>\n	<td>".$ts->VALUE."</td>\n	</tr>\n");
							$prevvalue = $ts->VALUE;
						}
						$zebracount++;
					}
					print("</table>\n");
				}
				else {
					print("	<div class=\"info\">\n		<p>No players have been assigned an MVP!!</p>\n	</div>\n");
				}


?>

<p class="postmeta"><?php edit_post_link( __( 'Edit', 'oberwald' ), ' <strong>[</strong> ', ' <strong>]</strong> '); ?></p>

</div>
</div>


		<?php endwhile;?>
	<?php endif; ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
