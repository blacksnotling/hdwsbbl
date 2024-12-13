<?php
/**
 * Template Companion: Player Details Widget
 *
 * A Widget that displays the high level details of a Player
 * It should ONLY appear alongside the single-bblm_player template
 *
 * @class 		BBLM_Widget_TCplayerdetails
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/Widget
 * @version   1.3
 */

class BBLM_Widget_TCplayerdetails extends WP_Widget {

  function __construct() {

    $widget_ops = array('classname' => 'widget_bblm widget_bblm_tcplayerdetails', 'description' => __( 'Displays the details of a Player (Player page only)', 'bblm' ) );
    parent::__construct('bblm_tcplayerdetails', __( 'BB:TC: Player Details', 'bblm' ), $widget_ops);

  }

  //The Widget Output in the front-end
  public function widget( $args, $instance ) {
    global $wpdb;

    $post_type = get_post_type();

    //Check we are on the correct poat_type page, and not an archive before we display the widget
    if ( $post_type == "bblm_player" && is_single() ) {

      //pulling in the vars from the single-bblm_comp template
      global $pd;
      global $status;
      global $has_played;
      global $seasonsql;

    	//determine player race
      $racesql = 'SELECT O.r_id FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'position O WHERE O.pos_id = P.pos_id AND P.p_id = '.$pd->p_id;
    	$rd = $wpdb->get_row($racesql);

    	//determine debut season
    	$seasondebutsql = 'SELECT C.sea_id FROM '.$wpdb->prefix.'match_player P, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C WHERE P.m_id = M.WPID AND M.c_id = C.WPID AND C.c_counts = 1 AND P.p_id = '.$pd->p_id.' ORDER BY C.sea_id ASC LIMIT 1';
    	$sd = $wpdb->get_row($seasondebutsql);

    	//grab list of other players on the team
      $otherplayerssql = 'SELECT P.*, O.* FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'position O WHERE P.pos_id = O.pos_id AND P.p_id != '.$pd->p_id.' AND P.t_id = '.$pd->t_id.' ORDER BY RAND() LIMIT 5';
    	$otherplayers = $wpdb->get_results( $otherplayerssql );

    	//SQL for chapsionships won. like above but restricted to Winner Only!
    	$playerchampionshipssql = 'SELECT A.a_name, C.WPID AS CWPID FROM '.$wpdb->prefix.'player X, '.$wpdb->prefix.'awards A, '.$wpdb->prefix.'awards_team_comp B, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'match_player Z, '.$wpdb->prefix.'match V WHERE X.p_id = Z.p_id AND V.WPID = Z.m_id AND V.c_id = C.WPID AND X.t_id = B.t_id AND A.a_id = B.a_id AND a_cup = 1 AND B.c_id = C.WPID ';
      $playerchampionshipssql .= 'AND A.a_id = 1 AND X.p_id = '.$pd->p_id.' GROUP BY C.c_id ORDER BY A.a_id ASC LIMIT 0, 30 ';

      echo $args['before_widget'];

      echo '<div class="widget_bblm_playerdetails">';

        echo $args['before_title'] . apply_filters( 'widget_title', 'Player Information' ) . $args['after_title'];

        echo '<ul>';
        //Check to see if the Player is the Captain
        $captainsql = 'SELECT tcap_status FROM '.$wpdb->prefix.'team_captain WHERE p_id = '.$pd->p_id.' ORDER BY P_id ASC LIMIT 1';
        if ( $pcap = $wpdb->get_var( $captainsql ) ) {
          if ( $pcap ) {
            echo '<li><strong>' . __( 'Current Captain', 'bblm' ) . '</strong></li>';
          }
          else if (0 == $pcap){
            echo '<li><strong>' . __( 'Former Captain', 'bblm' ) . '</strong></li>';
          }
        }
        echo '<li><strong>' . __( 'Status', 'bblm' ) . ':</strong> ' . $status . '</li>';
        echo '<li><strong>' . __( 'Rank', 'bblm' ) . ':</strong> ' .  BBLM_CPT_Player::get_player_rank( $pd->PWPID ) . '</li>';
        echo '<li><strong>' . __( 'Team', 'bblm' ) . ':</strong> <a href="' . get_post_permalink( $pd->WPID ) . '" title="Read more on this team">' . esc_html( get_the_title( $pd->WPID ) ) . '</a></li>';
        echo '<li><strong>' . __( 'Position Number', 'bblm' ) . ':</strong> ' . $pd->p_num . '</li>';

        if ( ( '0' !== $rd->r_id ) && ( '91' !== $rd->r_id ) ) {
          //Mercs and Journeymen will not return a race ID as the race positions are not assigned to a race
          //Only display the players race if they are a pernament race position
          echo '<li><strong>' . __( 'Race', 'bblm' ) . ':</strong> ' . bblm_get_race_link( $rd->r_id ) . '</li>';
        }
        if ( $has_played ) {
          echo '<li><strong>' . __( 'Debut', 'bblm' ) . ':</strong> ' . bblm_get_season_link( $sd->sea_id ) . '</li>';
        }
        echo '</ul>';

      echo '</div>';

      echo $args['after_widget'];

      if ( $has_played ) {

        echo $args['before_widget'];

        echo '<div class="widget_bblm_awards">';

          echo $args['before_title'] . apply_filters( 'widget_title', 'Major Awards' ) . $args['after_title'];
?>
          <table>
            <thead>
              <tr>
                <th><?php echo __( 'Award','bblm' ); ?></th>
                <th><?php echo __( 'Competition','bblm' ); ?></th>
              </tr>
            </thead>
            <tbody>
<?php
          //note that both SQL strings are above
          if ( ( ( $cawards = $wpdb->get_results( $playerchampionshipssql ) ) || $sawards = $wpdb->get_results( $seasonsql ) ) ) {
            $zebracount = 1;
            if ( $cawards = $wpdb->get_results( $playerchampionshipssql ) ) {
              foreach ( $cawards as $ca ) {
                if ( $zebracount % 2 ) {
                  echo '<tr class="bblm_tbl_alt">';
                }
                else {
                  echo '<tr>';
                }
                echo '<td><strong>' . $ca->a_name . '</strong></td>';
                echo '<td>' . bblm_get_competition_link( $ca->CWPID ) . '</td>';
                echo '</tr>';
                $zebracount++;
              }
            }
            if ( $sawards = $wpdb->get_results( $seasonsql ) ) {
              foreach ( $sawards as $sa ) {
                if ( $zebracount % 2 ) {
                  echo '<tr class="bblm_tbl_alt">';
                }
                else {
                  echo '<tr>';
                }
                echo '<td><strong>' . $sa->a_name . '</strong></td>';
                echo '<td>' . bblm_get_season_link( $sa->sea_id ) . '</td>';
                echo '</tr>';
                $zebracount++;
              }
            }
          }
          else {
            echo '<tr class="bblm_tbl_alt"><td colSpan="2">' . __( 'This player has not won any major awards yet', 'bblm' ) . '</td></tr>';
          }
          echo '</tbody></table>';
          echo '<p><a href="#awardsfull" title="View all awards this player has won">View all awards this player has won &gt;&gt;</a></p>';

        echo '</div>';

        echo $args['after_widget'];

        echo $args['before_widget'];
        echo $args['before_title'] . apply_filters( 'widget_title', 'Currently Participating in' ) . $args['after_title'];
?>
        <table>
          <thead>
            <tr>
              <th><?php echo __( 'Competition','bblm' ); ?></th>
              <th><?php echo __( 'Championship','bblm' ); ?></th>
            </tr>
          </thead>
          <tbody>
<?php
        $currentcompssql = 'SELECT C.WPID AS CWPID, C.series_id AS SWPID FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'team_comp M, '.$wpdb->prefix.'comp C WHERE P.t_id = M.t_id AND M.c_id = C.WPID AND C.c_active = 1 AND T.t_id = M.t_id AND P.p_id = '.$pd->p_id.' GROUP BY C.c_id LIMIT 0, 30 ';
        if ( $currentcomps = $wpdb->get_results( $currentcompssql ) ) {
          $zebracount = 1;
          foreach ( $currentcomps as $curc ) {
            if ( $zebracount % 2 ) {
              echo '<tr class="bblm_tbl_alt">';
            }
            else {
              echo '<tr>';
            }
            echo '<td>' . bblm_get_competition_link( $curc->CWPID ) . '</td>';
            echo '<td>' . bblm_get_cup_link( $curc->SWPID ) . '</td>';
            echo '</tr>';
            $zebracount++;
          }
        }
        else {
          echo '<tr class="bblm_tbl_alt"><td colSpan="2">' . __( 'This player is currently not taking part in any Competitions', 'bblm' ) . '</td></tr>';
        }

        echo '</tbody></table>';

        echo $args['after_widget'];

      }//end of if played

      echo $args['before_widget'];
      echo $args['before_title'] . apply_filters( 'widget_title', 'Other Players on this team (random)' ) . $args['after_title'];
?>
      <table>
        <thead>
          <tr>
            <th><?php echo __( 'Player','bblm' ); ?></th>
            <th><?php echo __( 'Position','bblm' ); ?></th>
          </tr>
        </thead>
        <tbody>
<?php
      $zebracount = 1;
      foreach ( $otherplayers as $op ) {
        if ( $zebracount % 2 ) {
          echo '<tr class="bblm_tbl_alt">';
        }
        else {
          echo '<tr>';
        }
        echo '<td>' . bblm_get_player_link( $op->WPID ) . '</td>';
        echo '<td>' . esc_html( $op->pos_name ) . '</td>';
        echo '</tr>';
        $zebracount++;
      }
      echo '</tbody></table>';

      echo $args['after_widget'];

    } //end of if the widget should show

  }

  // The Widget output on the admin screen
  public function form( $instance ) {

    echo '<p>'.__( 'Displays the details of a player (Player page only)', 'bblm' ).'</p>';
    echo '<p>'.__( 'It will automatically know the player that is being displayed', 'bblm' ).'</p>';

 	}

  // Function to save any settings from he widget
  public function update( $new_instance, $old_instance ) {

    $instance = $old_instance;

    $instance = array();
    //Intentionally blank for now!
    return $instance;

  }

}

// Register the widget.
function bblm_register_widget_tcpd() {
  register_widget( 'BBLM_Widget_TCplayerdetails' );
}
add_action( 'widgets_init', 'bblm_register_widget_tcpd' );
