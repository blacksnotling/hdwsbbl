<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Display Statistics
 *
 * THe class that handles the output of Statistics tables
 *
 * @class 		BBLM_Stat
 * @version		1.4
 * @package		BBowlLeagueMan/Statistics
 * @category	Class
 * @author 		blacksnotling
 */
 class BBLM_Stat {

   /**
    * Constructor
    */
    public function __construct() {

    }
   /**
    * Displays the list of top performing players for ALL Stats
    *
    * Takes in the BBLM ID of the competition
    *
    * @param int $ID ID of the comp/season/etc to generate the list for
    * @param int $coverage Are we we looing for a Comp, season, all time etc
    * @param int $limit How many results to return
    * @param int $active Binary option to determine if only active players are to be shown
    * @return html
    */
    public function display_top_players_table( $limit = 0, $active = 0 ) {
      global $post;
      global $wpdb;

			$post_type = get_post_type(); //Determine the CPT that is calling this function
			$ID = get_the_ID(); //The ID of the Page being displayed

      //Generates an array containing all the Stats that are going to be checked
      $playerstatsarray = array();
      $playerstatsarray[0]['item'] = "mp_spp";
      $playerstatsarray[0]['title'] = "Best Players";
      $playerstatsarray[0]['error'] = "The top Player list is not available at the moment";
      $playerstatsarray[1]['item'] = "mp_td";
      $playerstatsarray[1]['title'] = "Top Scorers";
      $playerstatsarray[1]['error'] = "No Touch Downs have been made yet!";
      $playerstatsarray[2]['item'] = "mp_cas";
      $playerstatsarray[2]['title'] = "Most Vicious";
      $playerstatsarray[2]['error'] = "No Casualties have been caused yet";
      $playerstatsarray[3]['item'] = "mp_comp";
      $playerstatsarray[3]['title'] = "Top Passers";
      $playerstatsarray[3]['error'] = "No Completions have been made yet";
      $playerstatsarray[4]['item'] = "mp_int";
      $playerstatsarray[4]['title'] = "Top Interceptors";
      $playerstatsarray[4]['error'] = "No Inteceptions have been made yet";
			$playerstatsarray[5]['item'] = "mp_def";
			$playerstatsarray[5]['title'] = "Top Deflections";
			$playerstatsarray[5]['error'] = "No Deflections have been made yet";
      $playerstatsarray[6]['item'] = "mp_mvp";
      $playerstatsarray[6]['title'] = "Most Valuable Players (MVP)";
      $playerstatsarray[6]['error'] = "The Most Valuable Players list is not available at the moment";

      //For each of the stats, print the top players list. If none are found, display the relevant error
      foreach ( $playerstatsarray as $tpa ) {

        $statsql = '';
        $statsql .= 'SELECT P.WPID AS PWPID, T.WPID AS TWPID, SUM(M.'.$tpa['item'].') AS VALUE, R.pos_name, P.p_status ';

        if ( $post_type == "bblm_comp" ) {

					$statsql .= 'FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'match X, '.$wpdb->prefix.'position R ';
					$statsql .= 'WHERE P.pos_id = R.pos_id AND M.m_id = X.WPID AND M.p_id = P.p_id AND P.t_id = T.t_id AND M.'.$tpa['item'].' > 0 AND X.c_id = '.$ID.' AND T.t_id != '. bblm_get_star_player_team() .' GROUP BY P.p_id ORDER BY VALUE DESC';

				}
				else if ( $post_type == "bblm_season" ) {

					$statsql .= 'FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'match X, '.$wpdb->prefix.'position R ';
					$statsql .= 'WHERE P.pos_id = R.pos_id AND M.m_id = X.WPID AND X.c_id = C.WPID AND C.c_counts = 1 AND M.p_id = P.p_id AND P.t_id = T.t_id AND M.'.$tpa['item'].' > 0 AND C.sea_id = '.$ID.' AND T.t_id != '. bblm_get_star_player_team() . ' GROUP BY P.p_id ORDER BY VALUE DESC';

				}
				if ( $post_type == "bblm_cup" ) {

					$statsql .= 'FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'match X, '.$wpdb->prefix.'position R ';
					$statsql .= 'WHERE P.pos_id = R.pos_id AND M.m_id = X.WPID AND X.c_id = C.WPID AND M.p_id = P.p_id AND P.t_id = T.t_id AND M.'.$tpa['item'].' > 0 AND C.series_id = '.$ID.' AND T.t_id != '.bblm_get_star_player_team() .' GROUP BY P.p_id ORDER BY VALUE DESC';
				}

        if ( $limit > 0 ) {
          $statsql .= ' LIMIT '.bblm_get_stat_limit();
        }

        echo '<h4 class="bblm-table-caption">' . __( $tpa[ 'title' ], 'bblm' ) . '</h4>';

        if ( $topstats = $wpdb->get_results( $statsql ) ) {

          $this->display_player_table( $topstats );

        }
        else {
  ?>
        <div class="bblm_info">
          <p><?php echo __( $tpa[ 'error' ], 'bblm' ); ?></p>
        </div>
  <?php
        }

      }

    } //end of display_top_players_table

   /**
    * Displays the list of top Killers
    *
    * Takes in the BBLM ID of the competition
    *
    * @param int $ID ID of the comp/season/etc to generate the list for
    * @param int $coverage Are we we looing for a Comp, season, all time etc
    * @param int $limit How many results to return
    * @param int $active Binary option to determine if only active players are to be shown
    * @return html
    */
    public function display_top_killers_table( $limit = 0, $active = 0 ) {
      global $post;
      global $wpdb;

			$post_type = get_post_type(); //Determine the CPT that is calling this function
			$ID = get_the_ID(); //The ID of the Page being displayed

      $statsql = '';
      $statsql .= 'SELECT P.WPID AS PWPID, COUNT(*) AS VALUE , E.pos_name, P.p_status, T.WPID AS TWPID';

			if ( $post_type == "bblm_comp" ) {

				$statsql .= ' FROM '.$wpdb->prefix.'player_fate F, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'position E, '.$wpdb->prefix.'team T';
				$statsql .= ' WHERE P.t_id = T.t_id AND P.pos_id = E.pos_id AND (F.f_id = 1 OR F.f_id = 6 OR F.f_id = 7) AND P.p_id = F.pf_killer AND F.m_id = M.WPID AND M.c_id = '.$ID .' AND T.t_id != ' . bblm_get_star_player_team();

			}
			else if ( $post_type == "bblm_season" ) {

				$statsql .= ' FROM `'.$wpdb->prefix.'player_fate` F, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'position E, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'comp C';
				$statsql .= ' WHERE P.t_id = T.t_id AND P.pos_id = E.pos_id AND (F.f_id = 1 OR F.f_id = 6 OR F.f_id = 7) AND P.p_id = F.pf_killer AND F.m_id = M.WPID AND M.c_id = C.WPID AND C.c_counts = 1 AND C.sea_id = '.$ID.' AND T.t_id != ' . bblm_get_star_player_team();

			}
			else if ( $post_type == "bblm_cup" ) {

				$statsql .= ' FROM `'.$wpdb->prefix.'player_fate` F, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'position E, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'comp C';
				$statsql .= ' WHERE P.t_id = T.t_id AND P.pos_id = E.pos_id AND (F.f_id = 1 OR F.f_id = 6 OR F.f_id = 7) AND P.p_id = F.pf_killer AND F.m_id = M.WPID AND M.c_id = C.WPID AND C.series_id = '.$ID.' AND T.t_id != ' . bblm_get_star_player_team();

			}

      $statsql .= ' GROUP BY F.pf_killer ORDER BY VALUE DESC';
      if ( $limit > 0 ) {
        $statsql .= ' LIMIT '.bblm_get_stat_limit();
      }

      echo '<h4 class="bblm-table-caption">' . __( 'Top Killers', 'bblm' ) . '</h4>';

      if ( $topstats = $wpdb->get_results( $statsql ) ) {

        $this->display_player_table( $topstats );

      }
      else {
?>
      <div class="bblm_info">
        <p><?php echo __( 'So far nobody has died!', 'bblm' ); ?></p>
      </div>
<?php
      }

    } //display_top_killers_table

   /**
    * Performs the actual actual output of top player tables for each entry
    *
    * @param array $args The result of the HTML array
    * @return html
    */
    public function display_player_table( $args ) {
?>
			<div role="region" aria-labelledby="Caption01" tabindex="0">
			<table class="bblm_expandable bblm_table bblm_stats">
				<thead>
					<tr>
						<th class="bblm_tbl_stat">#</th>
						<th class="bblm_tbl_name"><?php echo __( 'Player', 'bblm' ); ?></th>
						<th><?php echo __( 'Position', 'bblm' ); ?></th>
						<th class="bblm_tbl_name"><?php echo __( 'Team', 'bblm' ); ?></th>
						<th class="bblm_tbl_stat"><?php echo __( 'Value', 'bblm' ); ?></th>
					</tr>
				</thead>
				<tbody>
<?php
      $zebracount = 1;
      $prevvalue = 0;

      foreach ( $args as $ts ) {
      if ( ( $zebracount % 2 ) && ( 10 < $zebracount ) ) {
				echo '<tr class="bblm_tbl_hide bblm_tbl_alt">';
      }
      else if ( ( $zebracount % 2 ) && ( 10 >= $zebracount ) ) {
        echo '<tr class="bblm_tbl_alt">';
      }
      else if ( 10 < $zebracount ) {
        echo '<tr class="bblm_tbl_hide">';
      }
      else {
        echo '<tr>';
      }
      if ( $ts->VALUE > 0 ) {
        if ( $prevvalue == $ts->VALUE ) {
          echo '<td>-</td>';
        }
        else {
          echo '<td><strong>' . $zebracount . '</strong></td>';
        }

        echo '<td>' . bblm_get_player_link( $ts->PWPID ) . '</td> <td>' . esc_html( $ts->pos_name ) . '</td> <td>' . bblm_get_team_link( $ts->TWPID ) . '</td> <td>' . $ts->VALUE . '</td> </tr>';
        $prevvalue = $ts->VALUE;
      }
      $zebracount++;
    }
    echo '</tbody>';
		echo '</table>';
		echo '</div>';

  } // end of display_player_table()

   /**
    * Outputs the detailed statistics break down of championship cups, competitions, etc
    *
    *
    * @param wordpress $query
    * @return html
    */
    public function display_stats_breakdown() {
      global $post;
      global $wpdb;

      $post_type = get_post_type(); //Determine the CPT that is calling this function

      $itemid = get_the_ID(); //The ID of the Page being displayed

      if ( $post_type == "bblm_cup" ) {

        //Load the Class(es) we need
        $cup = new BBLM_CPT_Cup;

        $matchnum = $cup->get_number_games();

        //The queries to generate the stats
        $matchstatssql = 'SELECT SUM(M.m_tottd) AS TD, SUM(M.m_totcas) AS CAS, SUM(M.m_totcomp) AS COMP, SUM(M.m_totint) AS MINT FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C WHERE M.c_id = C.WPID AND C.series_id = ' . $itemid;
        //Counts the Dead. Note: THis does not check for c_counts = 1 as the cups page will show all matches within the cup
        $deathnumsql = 'SELECT COUNT(F.f_id) AS DEAD FROM '.$wpdb->prefix.'player_fate F, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C WHERE M.c_id = C.WPID AND F.m_id = M.WPID AND (F.f_id = 1 OR F.f_id = 6 OR F.f_id = 7) AND C.series_id = ' . $itemid;
        $compnumsql = 'SELECT COUNT(*) AS ccount FROM '.$wpdb->prefix.'comp WHERE series_id = '.$itemid;
        $playermnumsql = 'SELECT COUNT(DISTINCT P.p_id) AS value FROM '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'match_player P WHERE C.WPID = M.c_id AND M.WPID = P.m_id AND C.series_id = '.$itemid.' GROUP BY C.series_id';
        $teamnumsql = 'SELECT COUNT(DISTINCT P.t_id) AS value FROM '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'team_comp P WHERE P.c_id = C.WPID AND C.series_id = '.$itemid.' GROUP BY C.series_id';

        $biggestattendcesql = 'SELECT UNIX_TIMESTAMP(M.m_date) AS MDATE, M.m_gate AS VALUE, M.WPID AS MWPID FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C WHERE M.c_id = C.WPID AND C.series_id = '.$itemid.' ORDER BY M.m_gate DESC, MDATE ASC LIMIT 1';
        $biggestattendcenonfinalsql = 'SELECT UNIX_TIMESTAMP(M.m_date) AS MDATE, M.m_gate AS VALUE, M.WPID AS MWPID FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C WHERE M.c_id = C.WPID AND M.div_id != 1 AND M.div_id != 2 AND M.div_id != 3 AND C.series_id = '.$itemid.' ORDER BY M.m_gate DESC, MDATE ASC LIMIT 1';

      } //end of ( $post_type == "bblm_cup" )
			else if ( $post_type == "bblm_season" ) {

				$seasonactive = BBLM_CPT_Season::is_season_active( $itemid );

				$matchnumsql = 'SELECT COUNT(*) AS MATCHNUM FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C WHERE M.c_id = C.WPID AND C.c_counts = 1 AND C.sea_id = ' . $itemid;
				$matchnum = $wpdb->get_var( $matchnumsql );

				//The queries to generate the stats
				$matchstatssql = 'SELECT SUM(M.m_tottd) AS TD, SUM(M.m_totcas) AS CAS, SUM(M.m_totcomp) AS COMP, SUM(M.m_totint) AS MINT FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C WHERE M.c_id = C.WPID AND C.c_counts = 1 AND C.sea_id = ' . $itemid;
				//Counts the Dead. Note: THis does not check for c_counts = 1 as the cups page will show all matches within the cup
				$deathnumsql = 'SELECT COUNT(F.f_id) AS DEAD FROM '.$wpdb->prefix.'player_fate F, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C WHERE (F.f_id = 1 OR F.f_id = 6 OR F.f_id = 7) AND F.m_id = M.WPID AND M.c_id = C.WPID AND  C.c_counts = 1 AND C.sea_id = ' . $itemid;
				$compnumsql = 'SELECT COUNT(*) AS compnum FROM '.$wpdb->prefix.'comp WHERE c_counts = 1 AND sea_id = '.$itemid;
				$cupnumsql = 'SELECT COUNT(DISTINCT(C.series_id)) AS cupnum FROM '.$wpdb->prefix.'comp C WHERE C.c_counts = 1 AND C.sea_id = ' . $itemid;
				$playermnumsql = 'SELECT COUNT(DISTINCT P.p_id) AS value FROM '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'match_player P WHERE C.WPID = M.c_id AND M.WPID = P.m_id AND C.c_counts = 1 AND C.sea_id = ' . $itemid . ' GROUP BY C.sea_id';
				$teamnumsql = 'SELECT COUNT(DISTINCT P.t_id) AS value FROM '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'team_comp P, '.$wpdb->prefix.'team T WHERE P.t_id = T.t_id AND P.c_id = C.WPID AND C.c_counts = 1 AND C.sea_id = ' . $itemid . ' GROUP BY C.sea_id';

				$biggestattendcesql = 'SELECT UNIX_TIMESTAMP(M.m_date) AS MDATE, M.m_gate AS VALUE, M.WPID AS MWPID FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C WHERE M.c_id = C.WPID AND C.c_counts = 1 AND ((M.div_id = 1 OR M.div_id = 2 OR M.div_id = 3)) AND C.sea_id = ' . $itemid . ' ORDER BY M.m_gate DESC, MDATE ASC LIMIT 1';
				$biggestattendcenonfinalsql = 'SELECT UNIX_TIMESTAMP(M.m_date) AS MDATE, M.m_gate AS VALUE, M.WPID AS MWPID FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C WHERE M.c_id = C.WPID AND C.c_counts = 1 AND M.div_id != 1 AND M.div_id != 2 AND M.div_id != 3 AND C.sea_id = ' . $itemid . ' ORDER BY M.m_gate DESC, MDATE ASC LIMIT 1';

			} //end of else if ( $post_type == "bblm_season" )

      //Run the queries
      if ( $matchstats = $wpdb->get_results( $matchstatssql ) ) {

        foreach ( $matchstats as $ms ) {
          $tottd = $ms->TD;
          $totcas = $ms->CAS;
          $totcomp = $ms->COMP;
          $totint = $ms->MINT;
        }
        $deathnum = $wpdb->get_var( $deathnumsql );
        $compnum = $wpdb->get_var( $compnumsql );
        $playernum = $wpdb->get_var( $playermnumsql );
        $teamnum = $wpdb->get_var( $teamnumsql );
				if ( $post_type == "bblm_season" ) {
					$cupnum = $wpdb->get_var( $cupnumsql );
				}

        //Output the Statistics breakdown
				$output = '<p>';
?>
        <h3><?php echo __( 'Overall Statistics and information', 'bblm' ); ?></h3>
<?php
				if ( $post_type == "bblm_season" ) {

					if ( $seasonactive ) {
						$output .= __( 'This Season began in ', 'bblm') . '<strong>' . date( "M y", strtotime( $post->season_sdate ) ) . '</strong> ' . __( ' and is currently ', 'bblm') . ' <strong>' . __( ' active', 'bblm') . '</strong>. ';
					}
					else {
						$output .= __( 'This Season ran between ', 'bblm') . '<strong>' . date( "M y", strtotime( $post->season_sdate ) ) . '</strong>' . __( ' and ', 'bblm') . '<strong>' . date( "M y", strtotime( $post->season_fdate ) ) . '</strong>. ';
					}

				}
        $output .= '<strong>' . $playernum . '</strong> Players in <strong>' . $teamnum . '</strong> Teams have played <strong>' . $matchnum . '</strong> Matches in <strong>' . $compnum . '</strong> Competitions for ';
				if ( $post_type == "bblm_cup" ) {
					$output .= 'this Championship Cup. ';
				}
				else if ( $post_type == "bblm_season" ) {
					$output .= '<strong>' . $cupnum . '</strong> Championship Cups. ';
				}
				$output .= 'To date they have managed to:</p>';
        $output .= '<ul>';
        $output .= '<li>Score <strong>' . $tottd . '</strong> Touchdowns (average <strong>' . round( $tottd / $matchnum, 1 ) . '</strong> per match);</li>';
        $output .= '<li>Make <strong>' . $totcomp . '</strong> successful Completions (average <strong>' . round( $totcomp / $matchnum, 1 ) . '</strong> per match);</li>';
        $output .= '<li>Cause <strong>' . $totcas . '</strong> Casualties (average <strong>' . round( $totcas / $matchnum, 1 ) . '</strong> per match);</li>';
        $output .= '<li>Catch <strong>' . $totint . '</strong> Interceptions (average <strong>' . round( $totint / $matchnum, 1 ) . '</strong> per match).</li>';
        $output .= '<li>Kill <strong>' . $deathnum . '</strong> players (average <strong>' . round( $deathnum / $matchnum, 1 ) . '</strong> per match).</li>';
        $output .= '</ul>';

        $output .= '<ul>';
        if ( $bcn = $wpdb->get_row( $biggestattendcenonfinalsql ) ) {
          $output .= '<li>The Highest recorded attendance (not a Final or Semi-Final) is <strong>' . number_format( $bcn->VALUE ) . ' fans</strong> in the match between <strong>' . bblm_get_match_link_score( $bcn->MWPID, 0 ) . '</strong> on ' . date( "d.m.25y", $bcn->MDATE ) . '</li>';
        }
        if ( $bc = $wpdb->get_row($biggestattendcesql ) ) {
          $output .= '<li>The Highest recorded attendance (Final or Semi-Final) is <strong>' . number_format( $bc->VALUE ) . ' fans</strong> in the match between <strong>' . bblm_get_match_link_score( $bc->MWPID, 0 ) . '</strong> on ' . date( "d.m.25y" , $bc->MDATE ) . '</li>';
        }
        $output .= '</ul>';

        echo __( $output, 'bblm' );

      }
      else {

        echo '<p>' . __( 'No Matches have been played', 'bblm' ) . '</p>';

      }

    }//end of display_stats_breakdown()

		/**
		 * Outputs the detailed statistics break down of all Star Players
		 *
		 *
		 * @param wordpress $query
		 * @return html
		 */
		 public static function display_stats_breakdown_star() {
			 global $wpdb;

			 $bblm_star_team = bblm_get_star_player_team();

			 $stargmespldsql = 'SELECT COUNT(DISTINCT M.m_id) AS VALUE FROM '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'player P WHERE P.p_id = M.P_id AND P.t_id = '.$bblm_star_team;
			 if ( $matchnum = $wpdb->get_var( $stargmespldsql ) ) {

				 //Stars have been used, generate the SQL now
				 $matchstatssql = 'SELECT SUM(M.mp_td) AS TD, SUM(M.mp_cas) AS CAS, SUM(M.mp_comp) AS COMP, SUM(M.mp_int) AS MINT, SUM(P.p_cost) AS COST, SUM(M.mp_SPP) AS SPP, SUM(M.mp_mvp) AS MVP FROM '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'player P WHERE P.p_id = M.P_id AND M.mp_counts = 1 AND P.t_id = '.$bblm_star_team;
				 if ( $matchstats = $wpdb->get_results( $matchstatssql ) ) {
					 foreach ( $matchstats as $ms ) {
						 $tottd = $ms->TD;
						 $totcas = $ms->CAS;
						 $totcomp = $ms->COMP;
						 $totint = $ms->MINT;
						 $totcost = $ms->COST;
						 $totspp = $ms->SPP;
						 $totmvp = $ms->MVP;
					 }
				 }
				 $killsnumsql = 'SELECT COUNT(*) AS KILLS FROM '.$wpdb->prefix.'player_fate F, '.$wpdb->prefix.'player P WHERE (F.f_id = 1 OR F.f_id = 6 OR F.f_id = 7) AND F.pf_killer = P.p_id AND P.t_id = '.$bblm_star_team;
				 $killnum = $wpdb->get_var( $killsnumsql );

?>

					 <p><?php echo __( 'Combined, the Star Players have' , 'bblm' ); ?></p>
					 <ul>
						 <li><?php echo __( 'Taken Part in', 'bblm' ); ?> <strong><?php echo $matchnum; ?></strong> <?php echo __( 'unique matches', 'bblm' ); ?></li>
<?php

						if ( 0 < $tottd ) {
							echo '<li>' . __( 'Score', 'bblm' ) . ' <strong>' . $tottd . '</strong> ' . __( 'Touchdowns (average', 'bblm' ) . '  <strong>' . round( $tottd/$matchnum,1 ) . '</strong> ' . __( 'per appearance', 'bblm' ) . ')</li>';
						}
						if ( 0 < $totcas ) {
							echo '<li>' . __( 'Cause', 'bblm' ) . ' <strong>' . $totcas . '</strong> ' . __( 'Casualties (average', 'bblm' ) . '  <strong>' . round( $totcas/$matchnum,1 ) . '</strong> ' . __( 'per appearance', 'bblm' ) . ')</li>';
						}
						if ( 0 < $totcomp ) {
							echo '<li>' . __( 'Make', 'bblm' ) . ' <strong>' . $totcomp . '</strong> ' . __( 'successful Completions (average', 'bblm' ) . '  <strong>' . round( $totcomp/$matchnum,1 ) . '</strong> ' . __( 'per appearance', 'bblm' ) . ')</li>';
						}
						if ( 0 < $totint ) {
							echo '<li>' . __( 'Catch', 'bblm' ) . ' <strong>' . $totint . '</strong> ' . __( 'Interceptions (average', 'bblm' ) . '  <strong>' . round( $totint/$matchnum,1 ) . '</strong> ' . __( 'per appearance', 'bblm' ) . ')</li>';
						}
						if ( 0 < $totmvp ) {
							echo '<li>' . __( 'Deprive Players of', 'bblm' ) . ' <strong>' . $totmvp . '</strong> ' . __( 'MVP Awards', 'bblm' ) . '</li>';
						}
						if ( 0 < $totspp ) {
							echo '<li>' . __( 'Earn a total of', 'bblm' ) . ' <strong>' . $totspp . '</strong> ' . __( 'Star Player Points', 'bblm' ) . '</li>';
						}
						if ( 0 < $killnum ) {
							echo '<li>' . __( 'Kill', 'bblm' ) . ' <strong>' . $killnum . '</strong> ' . __( 'players (average', 'bblm' ) . '  <strong>' . round( $killnum/$matchnum,1 ) . '</strong> ' . __( 'per appearance', 'bblm' ) . ')</li>';
						}
?>

					 </ul>
					 <p><?php echo __( 'To date the teams of the league have spent ', 'bblm' ); ?><strong><?php echo number_format( $totcost ); ?></strong>gp <?php echo __( 'on hiring Star Players!' , 'bblm'); ?></p>

<?php
			} //end of if star Players have played a match
		}//end of display_stats_breakdown_star()

 }//end of class
