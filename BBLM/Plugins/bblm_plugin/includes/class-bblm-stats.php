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
 * @version		1.1
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
    public function display_top_players_table( $ID, $coverage = '', $limit = 0, $active = 0 ) {
      global $post;
      global $wpdb;

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
      $playerstatsarray[5]['item'] = "mp_mvp";
      $playerstatsarray[5]['title'] = "Most Valuable Players (MVP)";
      $playerstatsarray[5]['error'] = "The Most Valuable Players list is not available at the moment";

      //For each of the stats, print the top players list. If none are found, display the relevant error
      foreach ( $playerstatsarray as $tpa ) {

        $statsql = '';
        $statsql .= 'SELECT P.WPID AS PID, T.WPID, SUM(M.'.$tpa['item'].') AS VALUE, R.pos_name, P.p_status ';

        switch ( $coverage ) {

          case "bblm_comp":
            $statsql .= 'FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'match X, '.$wpdb->prefix.'position R ';
            $statsql .= 'WHERE P.pos_id = R.pos_id AND M.m_id = X.m_id AND M.p_id = P.p_id AND P.t_id = T.t_id AND M.'.$tpa['item'].' > 0 AND X.c_id = '.$ID.' AND T.t_id != '. bblm_get_star_player_team() .' GROUP BY P.p_id ORDER BY VALUE DESC';
            break;

          case "bblm_season":
            $statsql .= 'FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'match X, '.$wpdb->prefix.'position R ';
            $statsql .= 'WHERE P.pos_id = R.pos_id AND M.m_id = X.m_id AND X.c_id = C.c_id AND C.c_counts = 1 AND C.type_id = 1 AND M.p_id = P.p_id AND P.t_id = T.t_id AND M.'.$tpa['item'].' > 0 AND C.sea_id = '.$ID.' AND T.t_id != '. bblm_get_star_player_team() . ' GROUP BY P.p_id ORDER BY VALUE DESC';
            break;

          case "bblm_cup":
            $statsql .= 'FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'match X, '.$wpdb->prefix.'position R ';
            $statsql .= 'WHERE P.pos_id = R.pos_id AND M.m_id = X.m_id AND X.c_id = C.c_id AND M.p_id = P.p_id AND P.t_id = T.t_id AND M.'.$tpa['item'].' > 0 AND C.series_id = '.$ID.' AND T.t_id != '.bblm_get_star_player_team() .' GROUP BY P.p_id ORDER BY VALUE DESC';
            break;

        }

        if ( $limit > 0 ) {
          $statsql .= ' LIMIT '.bblm_get_stat_limit();
        }

        echo '<h4>' . __( $tpa[ 'title' ], 'bblm' ) . '</h4>';

        if ( $topstats = $wpdb->get_results( $statsql ) ) {

          $this->display_player_table( $topstats );

        }
        else {
  ?>
        <div class="info bblm_info">
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
    public function display_top_killers_table( $ID, $coverage = '', $limit = 0, $active = 0 ) {
      global $post;
      global $wpdb;

      $statsql = '';
      $statsql .= 'SELECT P.WPID AS PID, COUNT(*) AS VALUE , E.pos_name, P.p_status, T.WPID';

      switch ( $coverage ) {

        case "bblm_comp":
          $statsql .= ' FROM '.$wpdb->prefix.'player_fate F, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'position E, '.$wpdb->prefix.'team T';
          $statsql .= ' WHERE P.t_id = T.t_id AND P.pos_id = E.pos_id AND (F.f_id = 1 OR F.f_id = 6 OR F.f_id = 7) AND P.p_id = F.pf_killer AND F.m_id = M.m_id AND M.c_id = '.$ID .' AND T.t_id != ' . bblm_get_star_player_team();
          break;

        case "bblm_season":
          $statsql .= ' FROM `'.$wpdb->prefix.'player_fate` F, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'position E, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'comp C';
          $statsql .= ' WHERE P.t_id = T.t_id AND P.pos_id = E.pos_id AND (F.f_id = 1 OR F.f_id = 6 OR F.f_id = 7) AND P.p_id = F.pf_killer AND F.m_id = M.m_id AND M.c_id = C.c_id AND C.type_id = 1 AND C.c_counts = 1 AND C.c_show = 1 AND C.sea_id = '.$ID.' AND T.t_id != ' . bblm_get_star_player_team();
          break;

        case "bblm_cup":
          $statsql .= ' FROM `'.$wpdb->prefix.'player_fate` F, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'position E, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'comp C';
          $statsql .= ' WHERE P.t_id = T.t_id AND P.pos_id = E.pos_id AND (F.f_id = 1 OR F.f_id = 6 OR F.f_id = 7) AND P.p_id = F.pf_killer AND F.m_id = M.m_id AND M.c_id = C.c_id AND C.type_id = 1 AND C.c_show = 1 AND C.series_id = '.$ID.' AND T.t_id != ' . bblm_get_star_player_team();
          break;

        default:
        break;
      }

      $statsql .= ' GROUP BY F.pf_killer ORDER BY VALUE DESC';
      if ( $limit > 0 ) {
        $statsql .= ' LIMIT '.bblm_get_stat_limit();
      }

      echo '<h4>' . __( 'Top Killers', 'bblm' ) . '</h4>';

      if ( $topstats = $wpdb->get_results( $statsql ) ) {

        $this->display_player_table( $topstats );

      }
      else {
?>
      <div class="info bblm_info">
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
      <table class="expandable bblm_expandable bblm_table bblm_stats">
        <tr>
          <th class="tbl_stat bblm_tbl_stat">#</th>
          <th class="tbl_name bblm_tbl_name"><?php echo __( 'Player', 'bblm' ); ?></th>
          <th><?php echo __( 'Position', 'bblm' ); ?></th>
          <th class="tbl_name bblm_tbl_name"><?php echo __( 'Team', 'bblm' ); ?></th>
          <th class="tbl_stat bblm_tbl_stat"><?php echo __( 'Value', 'bblm' ); ?></th>
        </tr>
<?php
      $zebracount = 1;
      $prevvalue = 0;

      foreach ( $args as $ts ) {
      if ( ( $zebracount % 2 ) && ( 10 < $zebracount ) ) {
        echo '<tr class="tb_hide bblm_tbl_hide">';
      }
      else if ( ( $zebracount % 2 ) && ( 10 >= $zebracount ) ) {
        echo '<tr>';
      }
      else if ( 10 < $zebracount ) {
        echo '<tr class="tb_hide bblm_tbl_hide tbl_alt bblm_tbl_alt">';
      }
      else {
        echo '<tr class="tbl_alt bblm_tbl_alt">';
      }
      if ( $ts->VALUE > 0 ) {
        if ( $prevvalue == $ts->VALUE ) {
          echo '<td>-</td>';
        }
        else {
          echo '<td><strong>' . $zebracount . '</strong></td>';
        }

        echo '<td>' . bblm_get_player_link( $ts->PID ) . '</td> <td>' . esc_html( $ts->pos_name ) . '</td> <td>' . bblm_get_team_link( $ts->WPID ) . '</td> <td>' . $ts->VALUE . '</td> </tr>';
        $prevvalue = $ts->VALUE;
      }
      $zebracount++;
    }
    echo '</table>';

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
        $matchstatssql = 'SELECT SUM(M.m_tottd) AS TD, SUM(M.m_totcas) AS CAS, SUM(M.m_totcomp) AS COMP, SUM(M.m_totint) AS MINT FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C WHERE M.c_id = C.c_id AND C.series_id = ' . $itemid;
        //Counts the Dead. Note: THis does not check for c_counts = 1 as the cups page will show all matches within the cup
        $deathnumsql = 'SELECT COUNT(F.f_id) AS DEAD FROM '.$wpdb->prefix.'player_fate F, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C WHERE M.c_id = C.c_id AND F.m_id = M.m_id AND (F.f_id = 1 OR F.f_id = 6 OR F.f_id = 7) AND C.series_id = ' . $itemid;
        $compnumsql = 'SELECT COUNT(*) AS ccount FROM '.$wpdb->prefix.'comp WHERE series_id = '.$itemid;
        $playermnumsql = 'SELECT COUNT(DISTINCT P.p_id) AS value FROM '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'match_player P WHERE C.c_id = M.c_id AND M.m_id = P.m_id AND C.c_show = 1 AND C.series_id = '.$itemid.' GROUP BY C.series_id';
        $teamnumsql = 'SELECT COUNT(DISTINCT P.t_id) AS value FROM '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'team_comp P WHERE P.c_id = C.c_id AND C.c_show = 1 AND C.series_id = '.$itemid.' GROUP BY C.series_id';

        $biggestattendcesql = 'SELECT UNIX_TIMESTAMP(M.m_date) AS MDATE, M.m_gate AS VALUE, P.post_title AS MATCHT, P.guid AS MATCHLink FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE M.m_id = J.tid AND J.prefix = \'m_\' AND J.pid = P.ID AND M.c_id = C.c_id AND C.c_show = 1 AND C.type_id = 1 AND C.series_id = '.$itemid.' ORDER BY M.m_gate DESC, MDATE ASC LIMIT 1';
        $biggestattendcenonfinalsql = 'SELECT UNIX_TIMESTAMP(M.m_date) AS MDATE, M.m_gate AS VALUE, P.post_title AS MATCHT, P.guid AS MATCHLink FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE M.m_id = J.tid AND J.prefix = \'m_\' AND J.pid = P.ID AND M.c_id = C.c_id AND C.c_show = 1 AND C.type_id = 1 AND M.div_id != 1 AND M.div_id != 2 AND M.div_id != 3 AND C.series_id = '.$itemid.' ORDER BY M.m_gate DESC, MDATE ASC LIMIT 1';

      } //end of ( $post_type == "bblm_cup" )

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

        //Output the Statistics breakdown
?>
        <h3><?php echo __( 'Overall Statistics and information', 'bblm' ); ?></h3>
<?php
        $output = '<p><strong>' . $playernum . '</strong> Players in <strong>' . $teamnum . '</strong> Teams have played <strong>' . $matchnum . '</strong> Matches in <strong>' . $compnum . '</strong> Competitions for this Championship Cup. To date they have managed to:</p>';
        $output .= '<ul>';
        $output .= '<li>Score <strong>' . $tottd . '</strong> Touchdowns (average <strong>' . round( $tottd / $matchnum, 1 ) . '</strong> per match);</li>';
        $output .= '<li>Make <strong>' . $totcomp . '</strong> successful Completions (average <strong>' . round( $totcomp / $matchnum, 1 ) . '</strong> per match);</li>';
        $output .= '<li>Cause <strong>' . $totcas . '</strong> Casualties (average <strong>' . round( $totcas / $matchnum, 1 ) . '</strong> per match);</li>';
        $output .= '<li>Catch <strong>' . $totint . '</strong> Interceptions (average <strong>' . round( $totint / $matchnum, 1 ) . '</strong> per match).</li>';
        $output .= '<li>Kill <strong>' . $deathnum . '</strong> players (average <strong>' . round( $deathnum / $matchnum, 1 ) . '</strong> per match).</li>';
        $output .= '</ul>';

        $output .= '<ul>';
        if ( $bcn = $wpdb->get_row( $biggestattendcenonfinalsql ) ) {
          $output .= '<li>The Highest recorded attendance (not a Final or Semi-Final) is <strong>' . number_format( $bcn->VALUE ) . ' fans</strong> in the match between <strong>' . $bcn->MATCHT . '</strong> on ' . date( "d.m.25y", $bcn->MDATE ) . '</li>';
        }
        if ( $bc = $wpdb->get_row($biggestattendcesql ) ) {
          $output .= '<li>The Highest recorded attendance (Final or Semi-Final) is <strong>' . number_format( $bc->VALUE ) . ' fans</strong> in the match between <strong>' . $bc->MATCHT . '</strong> on ' . date( "d.m.25y" , $bc->MDATE ) . '</li>';
        }
        $output .= '</ul>';

        echo __( $output, 'bblm' );

      }
      else {

        echo '<p>' . __( 'No Matches have been played', 'bblm' ) . '</p>';

      }

    }//end of display_stats_breakdown()

 }//end of class
