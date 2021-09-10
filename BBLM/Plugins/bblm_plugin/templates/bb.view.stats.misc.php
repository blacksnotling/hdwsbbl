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

		$stat_limit = bblm_get_stat_limit();
		$bblm_star_team = bblm_get_star_player_team();

		/*-- Misc -- */
		$mostexpplayersql = 'SELECT Z.post_title AS PLAYER, Z.guid AS PLAYERLink, P.p_cost AS VALUE, T.WPID, X.pos_name FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'bb2wp J, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'position X, '.$wpdb->posts.' Z WHERE P.pos_id = X.pos_id AND P.t_id = T.t_id AND P.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = Z.ID AND T.t_id != '.$bblm_star_team.' ORDER BY VALUE DESC, P.p_id ASC LIMIT 1';
		$mep = $wpdb->get_row($mostexpplayersql);
		$biggestattendcesql = 'SELECT UNIX_TIMESTAMP(M.m_date) AS MDATE, M.m_gate AS VALUE, M.WPID AS MWPID FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C WHERE M.c_id = C.WPID AND C.c_counts = 1 ORDER BY M.m_gate DESC, MDATE ASC LIMIT 1';
		$bc = $wpdb->get_row($biggestattendcesql);
		$biggestattendcenonfinalsql = 'SELECT UNIX_TIMESTAMP(M.m_date) AS MDATE, M.m_gate AS VALUE, M.WPID AS MWPID FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C WHERE M.c_id = C.WPID AND C.c_counts = 1 AND M.div_id != 1 AND M.div_id != 2 AND M.div_id != 3 ORDER BY M.m_gate DESC, MDATE ASC LIMIT 1';
		$bcn = $wpdb->get_row($biggestattendcenonfinalsql);
		$lowestattendcesql = 'SELECT UNIX_TIMESTAMP(M.m_date) AS MDATE, M.m_gate AS VALUE, M.WPID AS MWPID FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C WHERE M.c_id = C.WPID AND C.c_counts = 1 AND M.m_gate > 0 ORDER BY M.m_gate ASC, MDATE ASC LIMIT 1';
		$lc = $wpdb->get_row($lowestattendcesql);
		$highesttvsql = 'SELECT T.WPID, P.mt_tv AS VALUE, UNIX_TIMESTAMP(M.m_date) AS MDATE FROM '.$wpdb->prefix.'match_team P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C WHERE P.t_id = T.t_id AND P.m_id = M.WPID AND M.c_id = C.WPID AND C.c_counts = 1 ORDER BY VALUE DESC, MDATE ASC LIMIT 0, 30 ';
		$htv = $wpdb->get_row($highesttvsql);
		$lowesttvsql = 'SELECT T.WPID, P.mt_tv AS VALUE, UNIX_TIMESTAMP(M.m_date) AS MDATE FROM '.$wpdb->prefix.'match_team P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C WHERE P.t_id = T.t_id AND P.m_id = M.WPID AND M.c_id = C.WPID AND C.c_counts = 1 ORDER BY VALUE ASC, MDATE ASC LIMIT 0, 30 ';
		$ltv = $wpdb->get_row($lowesttvsql);
		$teammostplayerssql = 'SELECT COUNT(*) AS VALUE, T.WPID FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T WHERE P.t_id = T.t_id AND T.t_id != '.$bblm_star_team.' GROUP BY P.t_id ORDER BY VALUE DESC, P.t_id ASC LIMIT 1';
		$tmp = $wpdb->get_row($teammostplayerssql);

		//Bits for the Player Career
		$matchnumsql = 'SELECT COUNT(*) AS MATCHNUM FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C WHERE M.c_id = C.WPID AND C.c_counts = 1';
		$matchnum = $wpdb->get_var($matchnumsql);
		$matchrecsql = 'SELECT COUNT(*) FROM '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'team T WHERE T.t_id = M.t_id AND M.mp_counts = 1';
		$matchrec = $wpdb->get_var($matchrecsql);
		$playernumsql = 'SELECT COUNT(*) AS playernum FROM '.$wpdb->prefix.'player M, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE M.t_id = T.t_id AND T.t_show = 1 AND M.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = P.ID';
		$playernum = $wpdb->get_var($playernumsql);
		$trans = new BBLM_CPT_Transfer;
?>


		<ul>
			<li><strong>Most Expensive Player</strong>: <?php print(number_format($mep->VALUE)); ?>gp (<a href="<?php print($mep->PLAYERLink); ?>" title="Learn more about this player"><?php print($mep->PLAYER); ?></a> - <?php print(  esc_html( $mep->pos_name ) ); ?> for <a href="<?php print( get_post_permalink( $mep->WPID ) ); ?>" title="Read more about this Team"><?php print( esc_html( get_the_title( $mep->WPID ) )  ); ?></a>)</li>
			<li><strong>Highest Recorded Attendance (Final or Semi-Final)</strong>: <?php print(number_format($bc->VALUE)); ?> fans (<?php echo bblm_get_match_link_score( $bc->MWPID ) ?>)</li>
 			<li><strong>Highest Recorded Attendance</strong>: <?php print(number_format($bcn->VALUE)); ?> fans (<?php echo bblm_get_match_link_score( $bcn->MWPID ) ?>)</li>
 			<li><strong>Lowest Recorded Attendance</strong>: <?php print(number_format($lc->VALUE)); ?> fans (<?php echo  bblm_get_match_link_score( $lc->MWPID ) ?>)</li>
			<li><strong>Highest Recorded TV</strong>: <?php print(number_format($htv->VALUE)); ?>gp (<a href="<?php print( get_post_permalink( $htv->WPID ) ); ?>" title="Read more about this Team"><?php print( esc_html( get_the_title( $htv->WPID ) )  ); ?></a> - <?php print(date("d.m.25y", $htv->MDATE)); ?>)</li>
			<li><strong>Lowest Recorded TV</strong>: <?php print(number_format($ltv->VALUE)); ?>gp (<a href="<?php print( get_post_permalink( $ltv->WPID ) ); ?>" title="Read more about this Team"><?php print( esc_html( get_the_title( $ltv->WPID ) )  ); ?></a> - <?php print(date("d.m.25y", $ltv->MDATE)); ?>)</li>
			<li><strong>Largest Recorded Transfer</strong>: <?php $trans->display_player_transfer_record(); ?> </li>
			<li><strong>Team with most players</strong>: <a href="<?php print( get_post_permalink( $tmp->WPID ) ); ?>" title="Read more about this Team"><?php print( esc_html( get_the_title( $tmp->WPID ) )  ); ?></a> (<?php print($tmp->VALUE); ?>)</li>
			<li><strong>Average Career length of a Player</strong>: <?php print(round($matchrec/$playernum,1)); ?> games</li>
		</ul>




		<h3><?php echo __( 'Performance related Stats','bblm'); ?></h3>
    <h3><?php echo __( 'Star Player Point Related','bblm'); ?></h3>
<?php
		 /*-- SPP -- */
		 $mostxplayerseasonsql = 'SELECT A.aps_value AS VALUE, P.WPID AS PLAYER, T.WPID, A.sea_id, X.pos_name FROM '.$wpdb->prefix.'awards_player_sea A, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'position X WHERE P.pos_id = X.pos_id AND A.p_id = P.p_id AND P.t_id = T.t_id AND A.a_id = 10 ORDER BY VALUE DESC, A.sea_id ASC LIMIT 1';
		 $mxps = $wpdb->get_row($mostxplayerseasonsql);
		 $mostxplayercompsql = 'SELECT A.apc_value AS VALUE, L.post_title AS PLAYER, L.guid AS PLAYERLink, T.WPID, A.c_id AS CWPID, X.pos_name FROM '.$wpdb->prefix.'awards_player_comp A, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'bb2wp K, '.$wpdb->posts.' L, '.$wpdb->prefix.'position X WHERE P.pos_id = X.pos_id AND P.p_id = K.tid AND K.prefix = \'p_\' AND K.pid = L.ID AND A.p_id = P.p_id AND P.t_id = T.t_id AND A.a_id = 10 ORDER BY VALUE DESC, A.c_id ASC LIMIT 1';
		 $mxpc = $wpdb->get_row($mostxplayercompsql);
		 $mostxplayermatchsql = 'SELECT Y.post_title AS PLAYER, T.WPID, Y.guid AS PLAYERLink, M.mp_spp AS VALUE, R.pos_name, UNIX_TIMESTAMP(X.m_date) AS MDATE FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' Y, '.$wpdb->prefix.'position R, '.$wpdb->prefix.'match X, '.$wpdb->prefix.'comp C WHERE M.m_id = X.WPID AND C.WPID = X.c_id AND C.c_counts = 1 AND P.pos_id = R.pos_id AND P.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = Y.ID AND M.p_id = P.p_id AND P.t_id = T.t_id AND M.mp_counts = 1 AND M.mp_td > 0 ORDER BY VALUE DESC, M.m_id ASC LIMIT 1';
		 $mxpm = $wpdb->get_row($mostxplayermatchsql);
?>
		<ul>
			<li><strong>Most Star Player Points earnt in a Season (Player)</strong>: <?php print($mxps->VALUE); ?> (<?php echo bblm_get_player_link( $mxps->PLAYER ); ?> - <?php echo esc_html( $mxps->pos_name ); ?> for <?php echo bblm_get_team_link( $mxps->WPID ); ?> - <?php echo bblm_get_season_link( $mxps->sea_id ); ?>)</li>
			<li><strong>Most Star Player Points earnt in a Competition (Player)</strong>: <?php print($mxpc->VALUE); ?> (<a href="<?php print($mxpc->PLAYERLink); ?>" title="See more on this Player"><?php print($mxpc->PLAYER); ?></a> - <?php print( esc_html( $mxpc->pos_name ) ); ?> for <?php echo bblm_get_team_link( $mxpc->WPID ); ?> - <?php echo bblm_get_competition_link( $mxpc->CWPID ); ?>)</li></li>
			<li><strong>Most Star Player Points earnt in a Match (Player)</strong>: <?php print($mxpm->VALUE); ?> (<a href="<?php print($mxpm->PLAYERLink); ?>" title="See more on this Player"><?php print($mxpm->PLAYER); ?></a> - <?php print( esc_html( $mxpm->pos_name ) ); ?> for <a href="<?php print( get_post_permalink( $mxpm->WPID ) ); ?>" title="Learn more about this Team"><?php print( esc_html( get_the_title( $mxpm->WPID ) )  ); ?></a> - <?php print(date("d.m.25y", $mxpm->MDATE)); ?>)</li>
		</ul>

		<h4><?php echo __( 'Completion Related','bblm'); ?></h4>
<?php
		 /*-- COMPLETIONS -- */
		 $mostxteamseasonsql = 'SELECT A.ats_value AS VALUE, T.WPID, A.sea_id FROM '.$wpdb->prefix.'awards_team_sea A, '.$wpdb->prefix.'team T WHERE A.t_id = T.t_id AND A.a_id = 14 ORDER BY VALUE DESC, A.sea_id ASC LIMIT 1';
		 $mxts = $wpdb->get_row($mostxteamseasonsql);
		 $mostxplayerseasonsql = 'SELECT A.aps_value AS VALUE, P.WPID AS PLAYER, T.WPID, A.sea_id, X.pos_name FROM '.$wpdb->prefix.'awards_player_sea A, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'position X WHERE P.pos_id = X.pos_id AND A.p_id = P.p_id AND P.t_id = T.t_id AND A.a_id = 14 ORDER BY VALUE DESC, A.sea_id ASC LIMIT 1';
		 $mxps = $wpdb->get_row($mostxplayerseasonsql);
		 $mostxteamcompsql = 'SELECT A.atc_value AS VALUE, T.WPID, A.c_id AS CWPID FROM '.$wpdb->prefix.'awards_team_comp A, '.$wpdb->prefix.'team T WHERE A.t_id = T.t_id AND A.a_id = 14 ORDER BY VALUE DESC, A.c_id ASC LIMIT 1';
		 $mxtc = $wpdb->get_row($mostxteamcompsql);
		 $mostxplayercompsql = 'SELECT A.apc_value AS VALUE, L.post_title AS PLAYER, L.guid AS PLAYERLink, T.WPID, A.c_id AS CWPID, X.pos_name FROM '.$wpdb->prefix.'awards_player_comp A, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'bb2wp K, '.$wpdb->posts.' L, '.$wpdb->prefix.'position X WHERE P.pos_id = X.pos_id AND P.p_id = K.tid AND K.prefix = \'p_\' AND K.pid = L.ID AND A.p_id = P.p_id AND P.t_id = T.t_id AND A.a_id = 14 ORDER BY VALUE DESC, A.c_id ASC LIMIT 1';
		 $mxpc = $wpdb->get_row($mostxplayercompsql);
		 $mostxteammatchsql = 'SELECT T.WPID, M.mt_comp AS VALUE, UNIX_TIMESTAMP(X.m_date) AS MDATE FROM '.$wpdb->prefix.'team T, '.$wpdb->prefix.'match_team M, '.$wpdb->prefix.'match X, '.$wpdb->prefix.'comp C WHERE T.t_id = M.t_id AND M.m_id = X.WPID AND C.WPID = X.c_id AND C.c_counts = 1 AND M.mt_comp > 0 ORDER BY VALUE DESC, M.m_id ASC LIMIT 1';
		 $mxtm = $wpdb->get_row($mostxteammatchsql);
		 $mostxplayermatchsql = 'SELECT Y.post_title AS PLAYER, T.WPID, Y.guid AS PLAYERLink, M.mp_comp AS VALUE, R.pos_name, UNIX_TIMESTAMP(X.m_date) AS MDATE FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' Y, '.$wpdb->prefix.'position R, '.$wpdb->prefix.'match X, '.$wpdb->prefix.'comp C WHERE M.m_id = X.WPID AND C.WPID = X.c_id AND C.c_counts = 1 AND P.pos_id = R.pos_id AND P.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = Y.ID AND M.p_id = P.p_id AND P.t_id = T.t_id AND M.mp_counts = 1 AND M.mp_comp > 0 ORDER BY VALUE DESC, M.m_id ASC LIMIT 1';
		 $mxpm = $wpdb->get_row($mostxplayermatchsql);
?>
		<ul>
			<li><strong>Most Passes completed in a Season (Team)</strong>: <?php echo $mxts->VALUE; ?> (<?php echo bblm_get_team_link( $mxts->WPID ); ?> - <?php echo bblm_get_season_link( $mxts->sea_id ); ?>)</li>
			<li><strong>Most Passes completed in a Season (Player)</strong>: <?php echo $mxps->VALUE; ?> (<?php echo bblm_get_player_link( $mxps->PLAYER ); ?> - <?php echo esc_html( $mxps->pos_name ); ?> for <?php echo bblm_get_team_link( $mxps->WPID ); ?> - <?php echo bblm_get_season_link( $mxps->sea_id ); ?>)</li>
			<li><strong>Most Passes completed in a Competition (Team)</strong>: <?php print($mxtc->VALUE); ?> (<?php echo bblm_get_team_link( $mxtc->WPID ); ?> - <?php echo bblm_get_competition_link( $mxtc->CWPID ); ?>)</li>
			<li><strong>Most Passes completed in a Competition (Player)</strong>: <?php print($mxpc->VALUE); ?> (<a href="<?php print($mxpc->PLAYERLink); ?>" title="See more on this Player"><?php print($mxpc->PLAYER); ?></a> - <?php print( esc_html( $mxpc->pos_name ) ); ?> for <?php echo bblm_get_team_link( $mxpc->WPID ); ?>) - <?php echo bblm_get_competition_link( $mxpc->CWPID ); ?>))</li></li>
			<li><strong>Most Passes completed in a Match (Team)</strong>: <?php print($mxtm->VALUE); ?> (<a href="<?php print( get_post_permalink( $mxtm->WPID ) ); ?>" title="Learn more about this Team"><?php print( esc_html( get_the_title( $mxtm->WPID ) )  ); ?></a> - <?php print(date("d.m.25y", $mxtm->MDATE)); ?>)</li>
			<li><strong>Most Passes completed in a Match (Player)</strong>: <?php print($mxpm->VALUE); ?> (<a href="<?php print($mxpm->PLAYERLink); ?>" title="See more on this Player"><?php print($mxpm->PLAYER); ?></a> - <?php print( esc_html( $mxpm->pos_name ) ); ?> for <a href="<?php print( get_post_permalink( $mxpm->WPID ) ); ?>" title="Learn more about this Team"><?php print( esc_html( get_the_title( $mxpm->WPID ) )  ); ?></a> - <?php print(date("d.m.25y", $mxpm->MDATE)); ?>)</li>
		</ul>

    <h4><?php echo __( 'Interception Related','bblm'); ?></h4>
<?php
		 /*-- Interceptions -- */
		 $mostxteamseasonsql = 'SELECT A.ats_value AS VALUE, T.WPID, A.sea_id FROM '.$wpdb->prefix.'awards_team_sea A, '.$wpdb->prefix.'team T WHERE A.t_id = T.t_id AND A.a_id = 13 ORDER BY VALUE DESC, A.sea_id ASC LIMIT 1';
		 $mxts = $wpdb->get_row($mostxteamseasonsql);
		 $mostxplayerseasonsql = 'SELECT A.aps_value AS VALUE, P.WPID AS PLAYER, T.WPID, A.sea_id, X.pos_name FROM '.$wpdb->prefix.'awards_player_sea A, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'position X WHERE P.pos_id = X.pos_id AND A.p_id = P.p_id AND P.t_id = T.t_id AND A.a_id = 13 ORDER BY VALUE DESC, A.sea_id ASC LIMIT 1';
		 $mxps = $wpdb->get_row($mostxplayerseasonsql);
		 $mostxteamcompsql = 'SELECT A.atc_value AS VALUE, T.WPID, A.c_id AS CWPID FROM '.$wpdb->prefix.'awards_team_comp A, '.$wpdb->prefix.'team T WHERE A.t_id = T.t_id AND A.a_id = 13 ORDER BY VALUE DESC, A.c_id ASC LIMIT 1';
		 $mxtc = $wpdb->get_row($mostxteamcompsql);
		 $mostxplayercompsql = 'SELECT A.apc_value AS VALUE, L.post_title AS PLAYER, L.guid AS PLAYERLink, T.WPID, A.c_id AS CWPID, X.pos_name FROM '.$wpdb->prefix.'awards_player_comp A, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'bb2wp K, '.$wpdb->posts.' L, '.$wpdb->prefix.'position X WHERE P.pos_id = X.pos_id AND P.p_id = K.tid AND K.prefix = \'p_\' AND K.pid = L.ID AND A.p_id = P.p_id AND P.t_id = T.t_id AND A.a_id = 13 ORDER BY VALUE DESC, A.c_id ASC LIMIT 1';
		 $mxpc = $wpdb->get_row($mostxplayercompsql);
		 $mostxteammatchsql = 'SELECT T.WPID, M.mt_int AS VALUE, UNIX_TIMESTAMP(X.m_date) AS MDATE FROM '.$wpdb->prefix.'team T, '.$wpdb->prefix.'match_team M, '.$wpdb->prefix.'match X, '.$wpdb->prefix.'comp C WHERE T.t_id = M.t_id AND M.m_id = X.WPID AND C.WPID = X.c_id AND C.c_counts = 1 AND M.mt_int > 0 ORDER BY VALUE DESC, M.m_id ASC LIMIT 1';
		 $mxtm = $wpdb->get_row($mostxteammatchsql);
		 $mostxplayermatchsql = 'SELECT Y.post_title AS PLAYER, T.WPID, Y.guid AS PLAYERLink, M.mp_int AS VALUE, R.pos_name, UNIX_TIMESTAMP(X.m_date) AS MDATE FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' Y, '.$wpdb->prefix.'position R, '.$wpdb->prefix.'match X, '.$wpdb->prefix.'comp C WHERE M.m_id = X.WPID AND C.WPID = X.c_id AND C.c_counts = 1 AND P.pos_id = R.pos_id AND P.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = Y.ID AND M.p_id = P.p_id AND P.t_id = T.t_id AND M.mp_counts = 1 AND M.mp_int > 0 ORDER BY VALUE DESC, M.m_id ASC LIMIT 1';
		 $mxpm = $wpdb->get_row($mostxplayermatchsql);
?>
		<ul>
			<li><strong>Most Interceptions made in a Season (Team)</strong>: <?php echo $mxts->VALUE; ?> (<?php echo bblm_get_team_link( $mxts->WPID ); ?> - <?php echo bblm_get_season_link( $mxts->sea_id ); ?>)</li>
			<li><strong>Most Interceptions made in a Season (Player)</strong>: <?php echo $mxps->VALUE; ?> (<?php echo bblm_get_player_link( $mxps->PLAYER ); ?> - <?php echo esc_html( $mxps->pos_name ); ?> for <?php echo bblm_get_team_link( $mxps->WPID ); ?> - <?php echo bblm_get_season_link( $mxps->sea_id ); ?>)</li>
			<li><strong>Most Interceptions made in a Competition (Team)</strong>: <?php print($mxtc->VALUE); ?> (<?php echo bblm_get_team_link( $mxtc->WPID ); ?>) - <?php echo bblm_get_competition_link( $mxtc->CWPID ); ?>))</li>
			<li><strong>Most Interceptions made in a Competition (Player)</strong>: <?php print($mxpc->VALUE); ?> (<a href="<?php print($mxpc->PLAYERLink); ?>" title="See more on this Player"><?php print($mxpc->PLAYER); ?></a> - <?php print( esc_html( $mxpc->pos_name ) ); ?> for <?php echo bblm_get_team_link( $mxpc->WPID ); ?>) - <?php echo bblm_get_competition_link( $mxpc->CWPID ); ?>))</li></li>
			<li><strong>Most Interceptions made in a Match (Team)</strong>: <?php print($mxtm->VALUE); ?> (<a href="<?php print( get_post_permalink( $mxtm->WPID ) ); ?>" title="Learn more about this Team"><?php print( esc_html( get_the_title( $mxtm->WPID ) )  ); ?></a> - <?php print(date("d.m.25y", $mxtm->MDATE)); ?>)</li>
			<li><strong>Most Interceptions made in a Match (Player)</strong>: <?php print($mxpm->VALUE); ?> (<a href="<?php print($mxpm->PLAYERLink); ?>" title="See more on this Player"><?php print($mxpm->PLAYER); ?></a> - <?php print( esc_html( $mxpm->pos_name ) ); ?> for <a href="<?php print( get_post_permalink( $mxpm->WPID ) ); ?>" title="Learn more about this Team"><?php print( esc_html( get_the_title( $mxpm->WPID ) )  ); ?></a> - <?php print(date("d.m.25y", $mxpm->MDATE)); ?>)</li>
		</ul>



			<h3><?php echo __( 'Statistics tables','bblm' ); ?></h3>
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
				$statsql = 'SELECT P.WPID AS PID, T.WPID, SUM(M.mp_comp) AS VALUE, R.pos_name, P.p_status, T.t_active FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'position R WHERE P.pos_id = R.pos_id AND M.p_id = P.p_id AND P.t_id = T.t_id AND M.mp_counts = 1 AND M.mp_comp > 0 AND T.t_id != '.$bblm_star_team.' '.$statsqlmodp.'GROUP BY P.p_id ORDER BY VALUE DESC LIMIT '.$stat_limit;
        echo '<h4>' . __('Best Passing Players','bblm' );
				if (0 == $period_alltime) {
					print(" (Active)");
				}
				print("</h4>\n");
				if ($topstats = $wpdb->get_results($statsql)) {
					if ($period_alltime) {
						print("	<p>Players who are <strong>highlighted</strong> are still active in the League.</p>\n");
					}
          echo '<div role="region" aria-labelledby="Caption01" tabindex="0">';
					print("<table class=\"bblm_table bblm_expandable\">\n	<thead><tr>\n		<th class=\"bblm_tbl_stat\">#</th>\n		<th class=\"bblm_tbl_name\">Player</th>\n		<th>Position</th>\n		<th class=\"bblm_tbl_name\">Team</th>\n		<th class=\"bblm_tbl_stat\">COMP</th>\n		</tr></thead><tbody>\n");
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
							print("	<td>" . esc_html( $ts->pos_name ). "</td>\n	<td>" . bblm_get_team_link( $ts->WPID ) . "</td>\n	<td>".$ts->VALUE."</td>\n	</tr>\n");
							$prevvalue = $ts->VALUE;
						}
						$zebracount++;
					}
					print("</tbody></table>\n</div>");
				}
				else {
					print("	<div class=\"bblm_info\">\n		<p>No players have made any successfull passes!!</p>\n	</div>\n");
				}

				  //////////////////////////////
				 // Top Interceptors Players //
				//////////////////////////////
				$statsql = 'SELECT P.WPID AS PID, T.WPID, SUM(M.mp_int) AS VALUE, R.pos_name, P.p_status, T.t_active FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'position R WHERE P.pos_id = R.pos_id AND M.p_id = P.p_id AND P.t_id = T.t_id AND M.mp_counts = 1 AND M.mp_int > 0  AND T.t_id != '.$bblm_star_team.' '.$statsqlmodp.'GROUP BY P.p_id ORDER BY VALUE DESC LIMIT '.$stat_limit;
        echo '<h4>' . __('Top Intercepting Players','bblm' );
				if (0 == $period_alltime) {
					print(" (Active)");
				}
				print("</h4>\n");
				if ($topstats = $wpdb->get_results($statsql)) {
					if ($period_alltime) {
						print("	<p>Players who are <strong>highlighted</strong> are still active in the League.</p>\n");
					}
          echo '<div role="region" aria-labelledby="Caption01" tabindex="0">';
					print("<table class=\"bblm_table bblm_expandable\">\n	<thead><tr>\n		<th class=\"bblm_tbl_stat\">#</th>\n		<th class=\"bblm_tbl_name\">Player</th>\n		<th>Position</th>\n		<th class=\"bblm_tbl_name\">Team</th>\n		<th class=\"bblm_tbl_stat\">INT</th>\n		</tr></thead><tbody>\n");
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
					print("	<div class=\"bblm_info\">\n		<p>No players have made any successfull Interceptions!!</p>\n	</div>\n");
				}

				  //////////////////
				 // MVPs Players //
				//////////////////
				$statsql = 'SELECT P.WPID AS PID, T.WPID, SUM(M.mp_mvp) AS VALUE, R.pos_name, P.p_status, T.t_active FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'position R WHERE P.pos_id = R.pos_id AND M.p_id = P.p_id AND P.t_id = T.t_id AND M.mp_counts = 1 AND M.mp_mvp > 0  AND T.t_id != '.$bblm_star_team.' '.$statsqlmodp.'GROUP BY P.p_id ORDER BY VALUE DESC LIMIT '.$stat_limit;
        echo '<h4>' . __('Most Valued Players','bblm' );
				if (0 == $period_alltime) {
					print(" (Active)");
				}
				print("</h4>\n");
				if ($topstats = $wpdb->get_results($statsql)) {
					if ($period_alltime) {
						print("	<p>Players who are <strong>highlighted</strong> are still active in the League.</p>\n");
					}
          echo '<div role="region" aria-labelledby="Caption01" tabindex="0">';
					print("<table class=\"bblm_table bblm_expandable\">\n	<thead><tr>\n		<th class=\"bblm_tbl_stat\">#</th>\n		<th class=\"bblm_tbl_name\">Player</th>\n		<th>Position</th>\n		<th class=\"bblm_tbl_name\">Team</th>\n		<th class=\"bblm_tbl_stat\">MVP</th>\n		</tr></thead><tbody>\n");
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
					print("	<div class=\"bblm_info\">\n		<p>No players have been assigned an MVP!!</p>\n	</div>\n");
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
