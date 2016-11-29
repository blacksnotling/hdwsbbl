<?php
/*
Template Name: Star Players Team View
*/
/*
*	Filename: page-star-players.php
*	Description: .Page to display the Star Players of the league. This replace the view team for this page.
*/
?>
<?php get_header(); ?>
	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
		<div id="breadcrumb">
			<p><a href="<?php echo home_url(); ?>" title="Back to the front of the HDWSBBL">HDWSBBL</a> &raquo; <?php the_title(); ?></p>
		</div>
			<div>
				<h2><?php the_title(); ?></h2>
				<div class="details">
					<?php the_content('Read the rest of this entry &raquo;'); ?>
				</div>
<?php
		$options = get_option('bblm_config');
		$bblm_star_team = htmlspecialchars($options['team_star'], ENT_QUOTES);

		$stargmespldsql = 'SELECT COUNT(DISTINCT M.m_id) AS VALUE FROM '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'player P WHERE P.p_id = M.P_id AND P.t_id = '.$bblm_star_team;
		if ($matchnum = $wpdb->get_var($stargmespldsql)) {

			//Stars have been used, generate the SQL now
			$matchstatssql = 'SELECT SUM(M.mp_td) AS TD, SUM(M.mp_cas) AS CAS, SUM(M.mp_comp) AS COMP, SUM(M.mp_int) AS MINT, SUM(P.p_cost) AS COST, SUM(M.mp_SPP) AS SPP, SUM(M.mp_mvp) AS MVP FROM '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'player P WHERE P.p_id = M.P_id AND M.mp_counts = 1 AND P.t_id = '.$bblm_star_team;
			if ($matchstats = $wpdb->get_results($matchstatssql)) {
				foreach ($matchstats as $ms) {
					$tottd = $ms->TD;
					$totcas = $ms->CAS;
					$totcomp = $ms->COMP;
					$totint = $ms->MINT;
					$totcost = $ms->COST;
					$totspp = $ms->SPP;
					$totmvp = $ms->MVP;
				}
			}
			$killsnumsql = 'SELECT COUNT(*) AS KILLS FROM '.$wpdb->prefix.'player_fate F, '.$wpdb->prefix.'player P WHERE (F.f_id = 1 or F.f_id = 6) AND F.pf_killer = P.p_id AND P.t_id = '.$bblm_star_team;
			$killnum = $wpdb->get_var($killsnumsql);

?>

				<p>Combined, the Star Players have:</p>
				<ul>
					<li>Taken part in <strong><?php print($matchnum); ?></strong> unique matches</li>
<?php if (0 < $tottd) { ?>	<li>Score <strong><?php print($tottd); ?></strong> Touchdowns (average <strong><?php print(round($tottd/$matchnum,1)); ?></strong> per appearance);</li> <?php } ?>
<?php if (0 < $totcas) { ?>	<li>Cause <strong><?php print($totcas); ?></strong> Casualties (average <strong><?php print(round($totcas/$matchnum,1)); ?></strong> per appearance);</li> <?php } ?>
<?php if (0 < $totcomp) { ?>	<li>Make <strong><?php print($totcomp); ?></strong> successful Completions (average <strong><?php print(round($totcomp/$matchnum,1)); ?></strong> per appearance);</li> <?php } ?>
<?php if (0 < $totint) { ?>	<li>Catch <strong><?php print($totint); ?></strong> Interceptions (average <strong><?php print(round($totint/$matchnum,1)); ?></strong> per appearance).</li> <?php } ?>
<?php if (0 < $totmvp) { ?>	<li>Deprive Players a total of <strong><?php print($totmvp); ?></strong> MVP awards.</li> <?php } ?>
<?php if (0 < $totspp) { ?>	<li>Earn a total of <strong><?php print($totspp); ?></strong> Star Player Points.</li> <?php } ?>
<?php if (0 < $killnum) { ?>	<li>Kill <strong><?php print($killnum); ?></strong> players (average <strong><?php print(round($killsnum/$matchnum,1)); ?></strong> per appearance).</li> <?php } ?>
				</ul>
				<p>So far, the teams of the league have spent <strong><?php print(number_format($totcost)); ?></strong>gp on hiring Star Players!</p>
<?php

			/*** Star Player Listing ***/
			$starplayerlistsql = 'SELECT P.post_title, P.guid, COUNT(M.p_id) AS PLD, SUM(M.mp_td) AS TD, SUM(M.mp_cas) AS CAS, SUM(M.mp_comp) AS COMP, SUM(M.mp_int) AS MINT, SUM(M.mp_mvp) AS MVP, SUM(M.mp_spp) AS SPP, P.guid '
			        . ' FROM '.$wpdb->posts.' P'
			        . ' JOIN '.$wpdb->prefix.'bb2wp J ON P.ID = J.pid'
			        . ' JOIN '.$wpdb->prefix.'player X ON J.tid = X.p_id'
			        . ' LEFT JOIN '.$wpdb->prefix.'match_player M ON X.p_id = M.p_id'
			        . ' WHERE J.prefix = "p_"'
			        . ' AND X.t_id = '.$bblm_star_team
			        . ' GROUP BY X.p_id';

			if ($starplayerlist = $wpdb->get_results($starplayerlistsql)) {
				//If any Star Players are found
?>
				<h3>The Stars</h3>
				<table class="sortable">
					<thead>
						<tr>
							<th>Star</th>
							<th>Pld</th>
							<th>TD</th>
							<th>CAS</th>
							<th>COMP</th>
							<th>INT</th>
							<th>MVP</th>
							<th>SPP</th>
						</tr>
					</thead>
					<tbody>
<?php
				$zebracount = 1;
				foreach ($starplayerlist as $spl) {
					if ($zebracount % 2) {
						print("						<tr>\n");
					}
					else {
						print("						<tr class=\"tbl_alt\">\n");
					}
?>
							<td><a href="<?php echo $spl->guid; ?>" title="Learn more about <?php echo $spl->post_title; ?>"><?php echo $spl->post_title; ?></a></td>
							<td><strong><?php echo $spl->PLD; ?></strong></td>
							<td><?php echo $spl->TD; ?></td>
							<td><?php echo $spl->CAS; ?></td>
							<td><?php echo $spl->COMP; ?></td>
							<td><?php echo $spl->MINT; ?></td>
							<td><?php echo $spl->MVP; ?></td>
							<td><strong><?php echo $spl->SPP; ?></strong></td>
						</tr>
<?php
					$zebracount++;
				} //end of for each star
?>
					</tbody>
				</table>

<?php
			} //End of if any Star Players are found


		}//End if a Star has played
		else {
			//No games have been played with a Star Player
			print("				<div class=\"info\">\n					<p>No Star Players have been hired by any teams, yet...</p>\n				</div>\n");
		}



		//Did You Know Display Code
		if (function_exists(bblm_display_dyk)) {
			bblm_display_dyk();
		}
?>

				<p class="postmeta"><?php edit_post_link('Edit', ' <strong>[</strong> ', ' <strong>]</strong> '); ?></p>

			</div>


		<?php endwhile;?>
	<?php endif; ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
