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
 * @version   1.0
 */

class BBLM_Widget_TCteamdetails extends WP_Widget {

  function __construct() {

    $widget_ops = array('classname' => 'widget_bblm widget_bblm_tcteamdetails', 'description' => __( 'Displays the details of a team (Team page only)', 'bblm' ) );
    parent::__construct('bblm_tcteamdetails', __( 'BB:TC: Team Details', 'bblm' ), $widget_ops);

  }

  //The Widget Output in the front-end
  public function widget( $args, $instance ) {
    global $wpdb;

    $parentoption = get_option( 'bblm_config' );
    $parentoption = htmlspecialchars( $parentoption[ 'page_team' ], ENT_QUOTES );

    $parentpage = get_queried_object()->post_parent;

    //Check we are on the correct poat_type before we display the widget
    //Checks to see if the parent of the page matches that in the bblm config
    if ( $parentoption == $parentpage ) {

      //pulling in the vars from the single-bblm_team template
      global $tid;
      global $has_played;
      global $ti;
      global $rosterlink;
      global $has_cups;
      global $champs;

      //Current match form
      $formsql = 'SELECT R.mt_result FROM '.$wpdb->prefix.'match_team R, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE M.m_id = J.tid AND J.prefix = \'m_\' AND J.pid = P.ID AND M.c_id = C.WPID AND C.c_counts = 1 AND M.m_id = R.m_id AND R.t_id = '.$tid.' ORDER BY m_date DESC LIMIT 5';
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
        $seasondebutsql = 'SELECT C.sea_id AS season FROM '.$wpdb->prefix.'match_team T, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C WHERE C.WPID = M.c_id AND C.c_counts = 1 AND M.m_id = T.m_id AND T.t_id = '.$tid.' ORDER BY M.m_date ASC LIMIT 1';
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
        echo '<li><strong>' . __( 'Race', 'bblm' ) . ':</strong> <a href="' . $ti->racelink . ' " title="Learn more about ' . esc_html( $ti->r_name ) .'">'.esc_html( $ti->r_name ) . '</a></li>';
        echo '</ul>';
        if ( $ti->t_roster ) {
          echo '<ul>';
          echo '<li><a href="' . $rosterlink . '" title="View the teams full roster">View Full Roster &gt;&gt;</a></li>';
          echo '</ul>';
        }

      echo '</div>';

      echo $args['after_widget'];
      if ($has_played) {
        echo $args['before_widget'];

        echo '<div class="widget_bblm_awards">';

          echo $args['before_title'] . apply_filters( 'widget_title', 'Championships' ) . $args['after_title'];

          if ( $has_cups ) {
            echo '<ul>';
            foreach ( $champs as $cc ) {
              print("	<li><strong>".$cc->a_name."</strong> - <a href=\"".$cc->guid."\" title=\"View full details about ".$cc->post_title."\">".$cc->post_title."</a></li>\n");
            }
            echo '</ul>';
          }
          else {
            echo '<p>' . __( 'This team has not won any Championships at present.', 'bblm' ) . '</p>';
          }
          print("<p><a href=\"#awardsfull\" title=\"View all awards this team has won\">View all awards this team has won &gt;&gt;</a></p>");

        echo '</div>';

        echo $args['after_widget'];

        echo $args['before_widget'];
        echo $args['before_title'] . apply_filters( 'widget_title', 'Currently Participating in' ) . $args['after_title'];

        $currentcompssql = 'SELECT C.WPID AS CWPID FROM '.$wpdb->prefix.'team_comp M, '.$wpdb->prefix.'comp C WHERE M.c_id = C.WPID AND C.c_active = 1 AND M.t_id = '.$tid.' LIMIT 0, 30 ';
        if ( $currentcomp = $wpdb->get_results( $currentcompssql ) ) {
          echo '<ul>';
          foreach ($currentcomp as $curc) {
            echo '<li>' . bblm_get_competition_link( $curc->CWPID ) . '</li>';
          }
          echo '</ul>';
        }
        else {
          print("<p>This team is currently not taking part in any Competitions.</p>\n");
        }
        echo $args['after_widget'];

        $topplayerssql = 'SELECT P.post_title, P.guid, T.p_spp FROM '.$wpdb->prefix.'player T, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE T.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = P.ID AND T.t_id = '.$tid.' ORDER BY T.p_spp DESC LIMIT 5';
        if ( $topp = $wpdb->get_results( $topplayerssql ) ) {
          echo $args['before_widget'];
          echo $args['before_title'] . apply_filters( 'widget_title', 'Top Players on this team' ) . $args['after_title'];
          echo '<ul>';
          foreach ($topp as $tp) {
            print("	<li><a href=\"".$tp->guid."\" title=\"Read more about ".$tp->post_title."\">".$tp->post_title."</a> - ".$tp->p_spp."</li>\n");
          }
          echo '</ul>';
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
