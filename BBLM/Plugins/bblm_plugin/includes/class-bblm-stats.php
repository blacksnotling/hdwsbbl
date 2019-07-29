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
 * @version		1.0
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
    * Displays the list of top performing players
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
        $statsql .= 'SELECT Y.post_title, T.WPID, Y.guid, SUM(M.'.$tpa['item'].') AS VALUE, R.pos_name ';

        switch ( $coverage ) {

          case "bblm_comp":
            $statsql .= 'FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'match X, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' Y, '.$wpdb->prefix.'position R ';
            $statsql .= 'WHERE P.pos_id = R.pos_id AND P.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = Y.ID AND M.m_id = X.m_id AND M.p_id = P.p_id AND P.t_id = T.t_id AND M.'.$tpa['item'].' > 0 AND X.c_id = '.$ID.' AND T.t_id != '. bblm_get_star_player_team() .' GROUP BY P.p_id ORDER BY VALUE DESC';
            break;

          case "bblm_season":
            $statsql .= 'FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'match X, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' Y, '.$wpdb->prefix.'position R ';
            $statsql .= 'WHERE P.pos_id = R.pos_id AND P.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = Y.ID AND M.m_id = X.m_id AND X.c_id = C.c_id AND C.c_counts = 1 AND C.type_id = 1 AND M.p_id = P.p_id AND P.t_id = T.t_id AND M.'.$tpa['item'].' > 0 AND C.sea_id = '.$ID.' AND T.t_id != '. bblm_get_star_player_team() . ' GROUP BY P.p_id ORDER BY VALUE DESC';
            break;

          case "bblm_cup":
            $statsql .= 'FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'match X, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' Y, '.$wpdb->prefix.'position R ';
            $statsql .= 'WHERE P.pos_id = R.pos_id AND P.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = Y.ID AND M.m_id = X.m_id AND X.c_id = C.c_id AND M.p_id = P.p_id AND P.t_id = T.t_id AND M.'.$tpa['item'].' > 0 AND C.series_id = '.$ID.' AND T.t_id != '.bblm_get_star_player_team() .' GROUP BY P.p_id ORDER BY VALUE DESC';
            break;

        }

        if ( $limit > 0 ) {
          $statsql .= ' LIMIT '.bblm_get_stat_limit();
        }

        echo '<h4>' . __( $tpa[ 'title' ], 'bblm' ) . '</h4>';

        if ( $topstats = $wpdb->get_results( $statsql ) ) {
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

          foreach ( $topstats as $ts ) {
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

              echo '<td><a href="' . $ts->guid . '" title=\"View more details on ' . $ts->post_title . '">' . $ts->post_title . '</a></td> <td>' . esc_html( $ts->pos_name ) . '</td> <td>' . bblm_get_team_link( $ts->WPID ) . '</td> <td>' . $ts->VALUE . '</td> </tr>';
              $prevvalue = $ts->VALUE;
            }
            $zebracount++;
          }
          echo '</table>';
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
      $statsql .= 'SELECT O.post_title, O.guid, COUNT(*) AS VALUE , E.pos_name, T.WPID';

      switch ( $coverage ) {

        case "bblm_comp":
          $statsql .= ' FROM '.$wpdb->prefix.'player_fate F, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' O, '.$wpdb->prefix.'position E, '.$wpdb->prefix.'team T';
          $statsql .= ' WHERE P.t_id = T.t_id AND P.pos_id = E.pos_id AND P.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = O.ID AND (F.f_id = 1 OR F.f_id = 6 OR F.f_id = 7) AND P.p_id = F.pf_killer AND F.m_id = M.m_id AND M.c_id = '.$ID .' AND T.t_id != ' . bblm_get_star_player_team();
          break;

        case "bblm_season":
          $statsql .= ' FROM `'.$wpdb->prefix.'player_fate` F, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' O, '.$wpdb->prefix.'position E, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'comp C';
          $statsql .= ' WHERE P.t_id = T.t_id AND P.pos_id = E.pos_id AND P.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = O.ID AND (F.f_id = 1 OR F.f_id = 6 OR F.f_id = 7) AND P.p_id = F.pf_killer AND F.m_id = M.m_id AND M.c_id = C.c_id AND C.type_id = 1 AND C.c_counts = 1 AND C.c_show = 1 AND C.sea_id = '.$ID.' AND T.t_id != ' . bblm_get_star_player_team();
          break;

        case "bblm_cup":
          $statsql .= 'FROM `'.$wpdb->prefix.'player_fate` F, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' O, '.$wpdb->prefix.'position E, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'comp C ';
          $statsql .= 'WHERE P.t_id = T.t_id AND P.pos_id = E.pos_id AND P.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = O.ID AND (F.f_id = 1 OR F.f_id = 6 OR F.f_id = 7) AND P.p_id = F.pf_killer AND F.m_id = M.m_id AND M.c_id = C.c_id AND C.type_id = 1 AND C.c_show = 1 AND C.series_id = '.$ID.' AND T.t_id != ' . bblm_get_star_player_team();
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

        foreach ( $topstats as $ts ) {
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

            echo '<td><a href="' . $ts->guid . '" title=\"View more details on ' . $ts->post_title . '">' . $ts->post_title . '</a></td> <td>' . esc_html( $ts->pos_name ) . '</td> <td>' . bblm_get_team_link( $ts->WPID ) . '</td> <td>' . $ts->VALUE . '</td> </tr>';
            $prevvalue = $ts->VALUE;
          }
          $zebracount++;
        }
        echo '</table>';
      }
      else {
?>
      <div class="info bblm_info">
        <p><?php echo __( 'So far nobody has died!', 'bblm' ); ?></p>
      </div>
<?php
      }

    } //display_top_killers_table


 }//end of class
