<?php
/**
 * Template Companion: Team Details Widget
 *
 * A Widget that displays the details of a team
 * It should ONLY appear alongside the single-bblm_team template
 *
 * @class 		BBLM_Widget_TCteamdetails
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/Widget
 * @version   1.4
 */

class BBLM_Widget_TCteamdetails extends WP_Widget {

  function __construct() {

    $widget_ops = array('classname' => 'widget_bblm widget_bblm_tcteamdetails', 'description' => __( 'Displays the details of a team (Team page only)', 'bblm' ) );
    parent::__construct('bblm_tcteamdetails', __( 'BB:TC: Team Details', 'bblm' ), $widget_ops);

  }

  //The Widget Output in the front-end
  public function widget( $args, $instance ) {
    global $wpdb;
    global $post;

    $post_type = get_post_type();


    //Check we are on the correct poat_type before we display the widget
    //Checks to see if the parent of the page matches that in the bblm config
    //and that this isn't the star player page
    if ( $post_type == "bblm_team" ) {

      //pulling in the vars from the single-bblm_team template
      global $tid;
      global $has_played;
      global $ti;
      global $has_cups;
      global $champs;

      //Current match form
      $formsql = 'SELECT R.mt_result FROM '.$wpdb->prefix.'match_team R, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C WHERE M.c_id = C.WPID AND C.c_counts = 1 AND M.WPID = R.m_id AND R.t_id = '.$tid.' ORDER BY m_date DESC LIMIT 5';
      $currentform = "";
      if ( $form = $wpdb->get_results( $formsql ) ) {
        foreach ( $form as $tf ) {
          $currentform .= $tf->mt_result;
        }
      }
      else {
          $currentform = "N/A";
      }

      //determine debut season
      if ( $has_played ) {
        $seasondebutsql = 'SELECT C.sea_id AS season FROM '.$wpdb->prefix.'match_team T, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C WHERE C.WPID = M.c_id AND C.c_counts = 1 AND M.WPID = T.m_id AND T.t_id = '.$tid.' ORDER BY M.m_date ASC LIMIT 1';
        $sd = $wpdb->get_row( $seasondebutsql );
      }

      $tstatus = "Disbanded";
      if ( $ti->t_active ) {
        $tstatus = "Active";
      }

      echo $args['before_widget'];

      echo '<div class="widget_bblm_teamdetails">';

        echo $args['before_title'] . apply_filters( 'widget_title', 'Team Information' ) . $args['after_title'];

        echo '<ul>';
        echo '<li><strong>' . __( 'Status', 'bblm' ) . ':</strong> ' . $tstatus . '</li>';
        echo '<li><strong>' . __( 'Team Value', 'bblm' ) . ':</strong> ' . number_format( $ti->t_tv ) . '</li>';
        echo '<li><strong>' . __( 'Current Team Value', 'bblm' ) . ':</strong> ' . number_format( $ti->t_ctv ) . '</li>';
        echo '<li><strong>' . __( 'Current Form', 'bblm' ) . ':</strong> ' . $currentform . '</li>';
        echo '<li><strong>' . __( 'Head Coach', 'bblm' ) . ':</strong> ' . $ti->t_hcoach . '</li>';
        if ( isset( $teamcaplink ) ) {
          echo '<li><strong>' . __( 'Current Captain', 'bblm' ) . ':</strong> ' . $teamcaplink . '</li>';
        }
        echo '<li><strong>' . __( 'Team Owner', 'bblm' ) . ':</strong> <a href="' . get_post_permalink( $ti->ID ) . ' " title="Learn more about ' . esc_html( get_the_title( $ti->ID ) ).'">'.esc_html( get_the_title( $ti->ID ) ) . '</a></li>';
        echo '<li><strong>' . __( 'Stadium', 'bblm' ) . ':</strong> ' . bblm_get_stadium_link( $ti->stad_id ) . '</li>';
        if ( $has_played ) {
          echo '<li><strong>' . __( 'Debut', 'bblm' ) . ':</strong> ' . bblm_get_season_link( $sd->season ) . '</li>';
        }
        echo '<li><strong>' . __( 'Race', 'bblm' ) . ':</strong> ' . bblm_get_race_link( $ti->r_id ) . '</li>';
        $meta = get_post_custom( $post->ID );
        $tmotto = ! isset( $meta['team_motto'][0] ) ? '' : $meta['team_motto'][0];
        echo '<li><strong>' . __( 'Motto', 'bblm' ) . ':</strong> <em>' . sanitize_text_field( $tmotto ) . '</em></li>';
        echo '</ul>';
        if ( $ti->t_roster ) {
          echo '<ul>';
          echo '<li>' . bblm_get_roster_link( $ti->WPID ) . '</li>';
          echo '</ul>';
        }

      echo '</div>';

      echo $args['after_widget'];
      if ($has_played) {
        echo $args['before_widget'];

        echo '<div class="widget_bblm_awards">';

          echo $args['before_title'] . apply_filters( 'widget_title', 'Championships' ) . $args['after_title'];
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
          if ( $has_cups ) {
            $zebracount = 1;
            foreach ( $champs as $cc ) {
              if ( $zebracount % 2 ) {
                echo '<tr class="bblm_tbl_alt">';
              }
              else {
                echo '<tr>';
              }
              echo '<td><strong>' . $cc->a_name . '</strong></td>';
              echo '<td>' . bblm_get_competition_link( $cc->CWPID ) . '</td>';
              echo '</tr>';
              $zebracount++;
            }
          }
          else {
            echo '<tr class="bblm_tbl_alt"><td colSpan="2">' . __( 'This team has not won any Championships at present.', 'bblm' ) . '</td></tr>';
          }
          echo '</tbody></table>';
          print("<p><a href=\"#awardsfull\" title=\"View all awards this team has won\">View all awards this team has won &gt;&gt;</a></p>");

        echo '</div>';

        echo $args['after_widget'];

        echo $args['before_widget'];
        echo $args['before_title'] . apply_filters( 'widget_title', 'Currently Participating in' ) . $args['after_title'];
?>
        <table>
          <thead>
            <tr>
              <th><?php echo __( 'Competition','bblm' ); ?></th>
              <th><?php echo __( 'Cup','bblm' ); ?></th>
            </tr>
          </thead>
          <tbody>
<?php
        $currentcompssql = 'SELECT C.WPID AS CWPID, C.series_id AS SWPID FROM '.$wpdb->prefix.'team_comp M, '.$wpdb->prefix.'comp C WHERE M.c_id = C.WPID AND C.c_active = 1 AND M.t_id = '.$tid.' LIMIT 0, 30 ';
        if ( $currentcomp = $wpdb->get_results( $currentcompssql ) ) {
          $zebracount = 1;
          foreach ($currentcomp as $curc) {
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
          echo '<tr class="bblm_tbl_alt"><td colSpan="2">' . __( 'This team is currently not participating in any Competitions.', 'bblm' ) . '</td></tr>';
        }
        echo '</tbody></table>';
        echo $args['after_widget'];

        $topplayerssql = 'SELECT T.WPID AS PWPID, T.p_spp FROM '.$wpdb->prefix.'player T WHERE T.t_id = '.$tid.' ORDER BY T.p_spp DESC LIMIT 5';
        if ( $topp = $wpdb->get_results( $topplayerssql ) ) {
          $zebracount = 1;
          echo $args['before_widget'];
          echo $args['before_title'] . apply_filters( 'widget_title', 'Top Players on this team' ) . $args['after_title'];
?>
          <table>
            <thead>
              <tr>
                <th><?php echo __( 'Player','bblm' ); ?></th>
                <th><?php echo __( 'SPP','bblm' ); ?></th>
              </tr>
            </thead>
            <tbody>
<?php
          foreach ($topp as $tp) {
            if ( $zebracount % 2 ) {
              echo '<tr class="bblm_tbl_alt">';
            }
            else {
              echo '<tr>';
            }
            echo '<td>' . bblm_get_player_link( $tp->PWPID ) . '</td>';
            echo '<td>' . (int) $tp->p_spp . '</td>';
            echo '</tr>';
            $zebracount++;
          }
          echo '</tbody></table>';
          echo $args['after_widget'];
        }

      }//end of if $has_played

    } //end of if the widget should show

  }

  // The Widget output on the admin screen
  public function form( $instance ) {

    echo '<p>'.__( 'Displays the details of a team (Team page only)', 'bblm' ).'</p>';
    echo '<p>'.__( 'It will automatically know the team that is being displayed', 'bblm' ).'</p>';
	?>
	<?php

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
function bblm_register_widget_tctd() {
  register_widget( 'BBLM_Widget_TCteamdetails' );
}
add_action( 'widgets_init', 'bblm_register_widget_tctd' );
