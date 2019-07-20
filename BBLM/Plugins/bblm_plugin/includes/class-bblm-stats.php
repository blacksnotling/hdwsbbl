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
 * @package		BBowlLeagueMan/CPTCore
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
    * Displays the list of top Killers for a competition
    *
    * Takes in the BBLM ID of the competition
    *
    * @param wordpress $query
    * @return html
    */
    public function display_top_killers_by_comp( $comp ) {
      global $post;
      global $wpdb;

      $statsql = 'SELECT O.post_title, O.guid, COUNT(*) AS VALUE , E.pos_name, T.WPID';
      $statsql .= ' FROM '.$wpdb->prefix.'player_fate F, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' O, '.$wpdb->prefix.'position E, '.$wpdb->prefix.'team T';
      $statsql .= ' WHERE P.t_id = T.t_id AND P.pos_id = E.pos_id AND P.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = O.ID AND (F.f_id = 1 OR F.f_id = 6 OR F.f_id = 7) AND P.p_id = F.pf_killer AND F.m_id = M.m_id AND M.c_id = '.$comp .' AND T.t_id != '.bblm_get_star_player_team().' GROUP BY F.pf_killer ORDER BY VALUE DESC LIMIT '.bblm_get_stat_limit();

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

    } //display_top_killers_by_comp


 }//end of class
