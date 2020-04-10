<?php
/**
 * BBowlLeagueMan Teamplate View Star Player
 *
 * Page Template to view Star Players details
 *
 * @author 		Blacksnotling
 * @category 	Template
 * @package 	BBowlLeagueMan/Templates
 */
/*
 * Template Name: View Star Player
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
<?php
			/*
			Gather Information for page
			*/
			$playersql = 'SELECT P.p_id, P.t_id, P.p_ma, P.p_st, P.p_ag, P.p_av, P.p_spp, P.p_skills, P.p_cost FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'bb2wp J WHERE J.tid = P.p_id AND J.prefix = \'p_\' AND J.pid = '.$post->ID;
			$pd = $wpdb->get_row($playersql);
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

		<header class="entry-header">
			<h2 class="entry-title"><?php the_title(); ?></h2>
		</header><!-- .entry-header -->

		<div class="entry-content">

	<div class="bblm_details">
		<?php the_content(); ?>
		</div>
			<table class="bblm_table">
				<tr>
					<th class="bblm_tbl_name">Position</th>
					<th class="bblm_tbl_stat">MA</th>
					<th class="bblm_tbl_stat">ST</th>
					<th class="bblm_tbl_stat">AG</th>
					<th class="bblm_tbl_stat">AV</th>
					<th>Skills</th>
					<th>Cost per match</th>
				</tr>
				<tr>
					<td>Star Player</td>
					<td><?php echo $pd->p_ma; ?></td>
					<td><?php echo $pd->p_st; ?></td>
					<td><?php echo $pd->p_ag; ?></td>
					<td><?php echo $pd->p_av; ?></td>
					<td class="bblm_tbl_skills"><?php  echo $pd->p_skills; ?></td>
					<td><?php  echo number_format($pd->p_cost); ?>gp</td>
				</tr>
			</table>
<?php
		$racelistsql = 'SELECT R.r_id FROM '.$wpdb->prefix.'race2star R WHERE R.p_id = '.$pd->p_id.' ORDER BY R.r_id ASC';
		$racelist = $wpdb->get_results($racelistsql);

		$is_first = 1;
		echo '<p>' . __( 'Available to hire for the following Races:', 'bblm');
		foreach ($racelist as $rl) {
			if (! $is_first) {
				echo ',';
			}

			echo ' ' . bblm_get_race_link( $rl->r_id );
			$is_first = 0;
		}
		echo ".</p>\n";


		//Career Stats
		$careerstatssql = 'SELECT COUNT(*) AS GAMES, SUM(M.mp_td) AS TD, SUM(M.mp_cas) AS CAS, SUM(M.mp_comp) AS COMP, SUM(M.mp_int) AS MINT, SUM(M.mp_mvp) AS MVP, SUM(M.mp_spp) AS SPP, T.t_name AS post_title FROM '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'team T WHERE M.t_id = T.t_id AND M.mp_counts = 1 AND M.p_id = '.$pd->p_id.' GROUP BY M.p_id ORDER BY T.t_name ASC';
		if ($s = $wpdb->get_row($careerstatssql)) {
			//The Star has played a match so continue
?>
			<h3>League Statistics</h3>
			<table class="bblm_table">
				<tr>
					<th class="bblm_tbl_title">Career Total</th>
					<th class="bblm_tbl_stat">Pld</th>
					<th class="bblm_tbl_stat">TD</th>
					<th class="bblm_tbl_stat">CAS</th>
					<th class="bblm_tbl_stat">COMP</th>
					<th class="bblm_tbl_stat">INT</th>
					<th class="bblm_tbl_stat">MVP</th>
					<th class="bblm_tbl_stat">SPP</th>
				</tr>
				<tr>
					<td><?php the_title(); ?></th>
					<td><?php echo $s->GAMES; ?></th>
					<td><?php echo $s->TD; ?></th>
					<td><?php echo $s->CAS; ?></th>
					<td><?php echo $s->COMP; ?></th>
					<td><?php echo $s->MINT; ?></th>
					<td><?php echo $s->MVP; ?></th>
					<td><?php echo $s->SPP; ?></th>
				</tr>
			</table>
<?php

			//Breakdown by team
			$statssql = 'SELECT COUNT(*) AS GAMES, SUM(M.mp_td) AS TD, SUM(M.mp_cas) AS CAS, SUM(M.mp_comp) AS COMP, SUM(M.mp_int) AS MINT, SUM(M.mp_mvp) AS MVP, SUM(M.mp_spp) AS SPP, T.WPID FROM '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'team T WHERE M.t_id = T.t_id AND M.mp_counts = 1 AND M.p_id = '.$pd->p_id.' GROUP BY T.t_id ORDER BY GAMES DESC, T.t_name ASC';
			if ($stats = $wpdb->get_results($statssql)) {
				$zebracount = 1;
?>

			<table class="bblm_table">
				<tr>
					<th class="bblm_tbl_title">Playing for</th>
					<th class="bblm_tbl_stat">Pld</th>
					<th class="bblm_tbl_stat">TD</th>
					<th class="bblm_tbl_stat">CAS</th>
					<th class="bblm_tbl_stat">COMP</th>
					<th class="bblm_tbl_stat">INT</th>
					<th class="bblm_tbl_stat">MVP</th>
					<th class="bblm_tbl_stat">SPP</th>
				</tr>

<?php
				foreach ($stats as $s) {
					if ($zebracount % 2) {
						print("				<tr>\n");
					}
					else {
						print("				<tr class=\"bblm_tbl_alt\">\n");
					}
					$team_name = esc_html( get_the_title( $s->WPID ) );
					print ("					<td><a href=\"" . get_post_permalink( $s->WPID ) . "\" title=\"Read more about " . $team_name . "\">" . $team_name . "</a></td>\n					<td>".$s->GAMES."</td>\n					<td>".$s->TD."</td>\n					<td>".$s->CAS."</td>\n					<td>".$s->COMP."</td>\n					<td>".$s->MINT."</td>\n					<td>".$s->MVP."</td>\n					<td>".$s->SPP."</td>\n				</tr>\n");
					$zebracount++;
				}
				print("			</table>\n");
			}

			// -- KILLER --
			$killersql = 'SELECT O.post_title AS PLAYER, O.guid AS PLAYERLink, T.WPID, X.pos_name FROM '.$wpdb->prefix.'player_fate F, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' O, '.$wpdb->prefix.'position X WHERE F.p_id = P.p_id AND P.t_id = T.t_id AND P.pos_id = X.pos_id AND F.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = O.ID AND F.pf_killer = '.$pd->p_id.' AND F.p_id != '.$pd->p_id.' ORDER BY F.m_id ASC';
			if ($killer = $wpdb->get_results($killersql)) {
				//If the player has killed people
?>
			<h3>Killer!</h3>
			<p>This player has killed another player in the course of their career. They have killed the following players:</p>
			<ul>
<?php
				foreach ($killer as $k) {
					print ("				<li><a href=\"".$k->PLAYERLink."\" title=\"Read more about ".$k->PLAYER."\">".$k->PLAYER."</a> (" . esc_html( $k->pos_name ) . " for <a href=\"" . get_post_permalink( $k->WPID ) . "\" title=\"Read more about this team\">" . esc_html( get_the_title( $k->WPID ) ) . "</a>)</li>\n");
				}
?>
			</ul>
<?php
			}

?>
			<h3>Breakdown by Competition</h3>
			<table class="bblm_table">
				<tr>
					<th class="bblm_tbl_title">Competition</th>
					<th class="bblm_tbl_stat">Pld</th>
					<th class="bblm_tbl_stat">TD</th>
					<th class="bblm_tbl_stat">CAS</th>
					<th class="bblm_tbl_stat">INT</th>
					<th class="bblm_tbl_stat">COMP</th>
					<th class="bblm_tbl_stat">MVP</th>
					<th class="bblm_tbl_stat">SPP</th>
				</tr>
<?php
			$playercompsql = 'SELECT COUNT(*) AS GAMES, SUM(M.mp_td) AS TD, SUM(M.mp_cas) AS CAS, SUM(M.mp_comp) AS COMP, SUM(M.mp_int) AS MINT, SUM(M.mp_mvp) AS MVP, SUM(M.mp_spp) AS SPP, C.WPID AS CWPID FROM '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'match Q WHERE M.m_id = Q.m_id AND Q.c_id = C.WPID AND C.c_counts = 1 AND M.p_id = P.p_id AND M.p_id = '.$pd->p_id.' GROUP BY C.c_id ORDER BY C.c_id DESC';
			if ($playercomp = $wpdb->get_results($playercompsql)) {
				$zebracount = 1;
				foreach ($playercomp as $pc) {
					if ($zebracount % 2) {
						print("				<tr>\n");
					}
					else {
						print("				<tr class=\"bblm_tbl_alt\">\n");
					}
					print("				<td>" . bblm_get_competition_link( $pc->CWPID ) . "</td>\n					<td>".$pc->GAMES."</td>\n					<td>".$pc->TD."</td>\n					<td>".$pc->CAS."</td>\n					<td>".$pc->MINT."</td>\n					<td>".$pc->COMP."</td>\n					<td>".$pc->MVP."</td>\n					<td>".$pc->SPP."</td>\n				</tr>\n");
					$zebracount++;
				}
			}
			print("			</table>\n");
?>

			<h3>Breakdown by Season</h3>
			<table class="bblm_table">
				<tr>
					<th class="bblm_tbl_title">Season</th>
					<th class="bblm_tbl_stat">P</th>
					<th class="bblm_tbl_stat">TD</th>
					<th class="bblm_tbl_stat">CAS</th>
					<th class="bblm_tbl_stat">INT</th>
					<th class="bblm_tbl_stat">COMP</th>
					<th class="bblm_tbl_stat">MVP</th>
					<th class="bblm_tbl_stat">SPP</th>
				</tr>
<?php
					$playerseasql = 'SELECT C.sea_id, COUNT(*) AS GAMES, SUM(M.mp_td) AS TD, SUM(M.mp_cas) AS CAS, SUM(M.mp_comp) AS COMP, SUM(M.mp_int) AS MINT, SUM(M.mp_mvp) AS MVP, SUM(M.mp_spp) AS SPP FROM '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'match Q WHERE C.c_counts = 1 AND M.m_id = Q.m_id AND Q.c_id = C.WPID AND M.p_id = P.p_id AND M.p_id = '.$pd->p_id.' GROUP BY C.sea_id ORDER BY C.sea_id DESC';
					if ($playersea = $wpdb->get_results($playerseasql)) {
					$zebracount = 1;
						foreach ($playersea as $pc) {
							if ($zebracount % 2) {
								print("				<tr>\n");
							}
							else {
								print("				<tr class=\"bblm_tbl_alt\">\n");
							}
							print("					<td>" . bblm_get_season_link( $pc->sea_id ) . "</td>\n					<td>".$pc->GAMES."</td>\n					<td>".$pc->TD."</td>\n					<td>".$pc->CAS."</td>\n					<td>".$pc->MINT."</td>\n					<td>".$pc->COMP."</td>\n					<td>".$pc->MVP."</td>\n					<td>".$pc->SPP."</td>\n				</tr>\n");
							$zebracount++;
						}
					}
					print("			</table>\n");
?>

			<h3>Recent Matches</h3>
			<table class="bblm_table bblm_sortable bblm_expandable">
				<thead>
				<tr>
					<th>Date</th>
					<th>For</th>
					<th>Against</th>
					<th>TD</th>
					<th>CAS</th>
					<th>INT</th>
					<th>COMP</th>
					<th>MVP</th>
					<th>SPP</th>
				</tr>
				</thead>
				<tbody>
<?php
  $playermatchsql = 'SELECT P.*, J.pid AS MID, UNIX_TIMESTAMP(M.m_date) AS mdate, A.WPID as TA, A.t_id AS TAid, B.WPID AS TB, B.t_id AS TBid FROM '.$wpdb->prefix.'match_player P, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'bb2wp J, '.$wpdb->prefix.'team A, '.$wpdb->prefix.'team B WHERE M.m_teamA = A.t_id AND M.m_teamB = B.t_id AND J.prefix = \'m_\' AND J.tid = M.m_id AND M.m_id = P.m_id AND P.p_id = ' . $pd->p_id . ' ORDER BY M.m_date DESC';
			if ( $playermatch = $wpdb->get_results( $playermatchsql ) ) {
			$zebracount = 1;
      foreach ( $playermatch as $pm ) {
				if ( ( $zebracount % 2 ) && ( 10 < $zebracount ) ) {
          echo '<tr class="bblm_tbl_hide">';
        }
        else if (($zebracount % 2) && (10 >= $zebracount)) {
          echo '<tr>';
        }
        else if ( 10 < $zebracount ) {
          echo '<tr class="bblm_tbl_alt bblm_tbl_hide">';
        }
        else {
          echo '<tr class="bblm_tbl_alt">';
        }
        echo '<td>';
        echo '<a href="' . get_post_permalink( $pm->MID ) . '" title="View the match in more detail">' . date( 'd.m.y' , $pm->mdate ) . '</a>';
        echo '</td>';
        if ( $pm->TAid == $pm->t_id ) {
          echo '<td>' . bblm_get_team_link( $pm->TA ) . '</td>';
          echo '<td>' . bblm_get_team_link( $pm->TB ) . '</td>';
        }
        else {
          echo '<td>' . bblm_get_team_link( $pm->TB ) . '</td>';
          echo '<td>' . bblm_get_team_link( $pm->TA ) . '</td>';
        }
        echo '<td>';
        if (0 == $pm->mp_td) {
          echo "0";
        }
        else {
          echo '<strong>' . $pm->mp_td . '</strong>';
        }
        echo '</td>';
        echo '<td>';
        if ( 0 == $pm->mp_cas ) {
          echo "0";
        }
        else {
          echo '<strong>' . $pm->mp_cas . '</strong>';
        }
        echo '</td>';
        echo '<td>';
        if ( 0 == $pm->mp_int ) {
          echo "0";
        }
        else {
          echo '<strong>' . $pm->mp_int . '</strong>';
        }
        echo '</td>';
        echo '<td>';
        if ( 0 == $pm->mp_comp ) {
          echo "0";
        }
        else {
          echo '<strong>' . $pm->mp_comp . '</strong>';
        }
        echo '</td>';
        echo '<td>';
        if ( 0 == $pm->mp_mvp ) {
          echo "0";
        }
        else {
          echo '<strong>' . $pm->mp_mvp . '</strong>';
        }
        echo '</td>';
        echo '<td>';
        if ( 0 == $pm->mp_spp ) {
          echo "0";
        }
        else {
          echo '<strong>' . $pm->mp_spp . '</strong>';
        }
        echo '</td>';
        echo '</tr>';
        $zebracount++;
      }
      echo '</tbody>';
      echo '</table>';
    }




		}//End of if a player has played a match
		else {
			//Star has not made debut yet
			print("					<div class=\"bblm_info\">\n						<p>This Star Player has not made their Debut yet. Stay tuned for further developments.</p>\n					</div>\n");
		}



?>
<footer class="entry-footer">
	<p class="postmeta"><?php bblm_display_page_edit_link(); ?></p>
</footer><!-- .entry-footer -->

</div><!-- .entry-content -->
</article>

<?php do_action( 'bblm_template_after_content' ); ?>
<?php endwhile; ?>
<?php do_action( 'bblm_template_after_loop' ); ?>
<?php endif; ?>
<?php do_action( 'bblm_template_after_posts' ); ?>
</main><!-- #main -->
</div><!-- #primary -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>
