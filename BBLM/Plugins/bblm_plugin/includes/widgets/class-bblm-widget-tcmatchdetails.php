<?php
/**
 * Template Companion: Match Details Widget
 *
 * A Widget that displays the high level details of a Match
 * It should ONLY appear alongside the single-bblm_match template
 *
 * @class 		BBLM_Widget_TCmatchdetails
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/Widget
 * @version   1.0
 */

class BBLM_Widget_TCmatchdetails extends WP_Widget {

  function __construct() {

    $widget_ops = array('classname' => 'widget_bblm widget_bblm_tcmatchdetails', 'description' => __( 'Displays the details of a match (Match page only)', 'bblm' ) );
    parent::__construct('bblm_tcmatchdetails', __( 'BB:TC: Match Details', 'bblm' ), $widget_ops);

  }

  //The Widget Output in the front-end
  public function widget( $args, $instance ) {
    global $wpdb;

    $parentoption = get_option( 'bblm_config' );
    $parentoption = htmlspecialchars( $parentoption[ 'page_match' ], ENT_QUOTES );

    $parentpage = 0;
    if ( is_single() ) {
      $parentpage = get_queried_object()->post_parent;
    }

    //Check we are on the correct poat_type before we display the widget
    //Checks to see if the parent of the page matches that in the bblm config
    if ( $parentoption == $parentpage ) {

      //pulling in the vars from the single-bblm_comp template
      global $m;
      global $playeractions;


      //Gathering data for the sidebar
      //Top players in match
      $topplayerssql = 'SELECT P.post_title, P.guid, T.mp_spp AS value FROM '.$wpdb->prefix.'match_player T, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE T.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = P.ID AND T.mp_spp > 0 AND T.m_id = '.$m->m_id.' ORDER BY value DESC LIMIT 5';

      //scorers
      $topscorerssql = 'SELECT P.post_title, P.guid, T.mp_td AS value FROM '.$wpdb->prefix.'match_player T, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE T.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = P.ID AND T.mp_td > 0 AND T.m_id = '.$m->m_id.' ORDER BY value DESC LIMIT 10';

      $compsql = 'SELECT C.WPID AS CWPID, D.div_name, C.sea_id FROM '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'division D WHERE D.div_id = '.$m->div_id.' AND C.WPID = '.$m->c_id.' LIMIT 1';
      $comp = $wpdb->get_row($compsql);

      echo $args['before_widget'];

      echo '<div class="widget_bblm_matchdetails">';

        echo $args['before_title'] . apply_filters( 'widget_title', 'Match Information' ) . $args['after_title'];

        echo '<ul>';
        echo '<li><strong>' . __( 'Date', 'bblm' ) . ':</strong> ' . date( "d.m.25y", $m->mdate ) . '</li>';
        echo '<li><strong>' . __( 'Competition', 'bblm' ) . ':</strong> ' . bblm_get_competition_link( $comp->CWPID ) . '</li>';
        echo '<li><strong>';
        if ( $m->div_id > 7 ) {
          echo 'Division';
        }
        else {
          echo 'Stage';
        }
        echo '</strong> ' . $comp->div_name . '</li>';
        echo '<li><strong>' . __( 'Stadium', 'bblm' ) . ':</strong> ' . bblm_get_season_link( $comp->sea_id ) . '</li>';
        echo '<li><strong>' . __( 'Attendance', 'bblm' ) . ':</strong> ' . number_format( $m->m_gate ) . '</li>';
        echo '<li><strong>' . __( 'Stadium', 'bblm' ) . ':</strong> ' . bblm_get_stadium_link( $m->stad_id ) . '</li>';
        echo '</ul>';

      echo '</div>';

      echo $args['after_widget'];

      if ( $playeractions ) {

        echo $args['before_widget'];
        echo $args['before_title'] . apply_filters( 'widget_title', 'Top Players of the Match' ) . $args['after_title'];

        if ( $topplayers = $wpdb->get_results( $topplayerssql ) ) {
          echo '<ul>';
            foreach ($topplayers as $ts) {
              print("						<li><a href=\"".$ts->guid."\" title=\"Read more on this player\">".$ts->post_title."</a> - ".$ts->value." spp</li>");
            }
          echo '</ul>';
        }
        else {
          echo '<p>' . __( 'None!', 'bblm' ) . '</p>';
        }

        echo $args['after_widget'];

        echo $args['before_widget'];
        echo $args['before_title'] . apply_filters( 'widget_title', 'Top Scorers of the Match' ) . $args['after_title'];

        if ( $topscorers = $wpdb->get_results( $topscorerssql ) ) {
          echo '<ul>';
            foreach ($topscorers as $ts) {
              print("						<li><a href=\"".$ts->guid."\" title=\"Read more on this player\">".$ts->post_title."</a> - ".$ts->value."</li>");
            }
          echo '</ul>';
        }
        else {
          echo '<p>' . __( 'None!', 'bblm' ) . '</p>';
        }

        echo $args['after_widget'];

      } //end of if $playeractions

    } //end of if the widget should show

  }

  // The Widget output on the admin screen
  public function form( $instance ) {

    echo '<p>'.__( 'Displays the details of a match (Match page only)', 'bblm' ).'</p>';
    echo '<p>'.__( 'It will automatically know the match that is being displayed', 'bblm' ).'</p>';
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
function bblm_register_widget_tcmd() {
  register_widget( 'BBLM_Widget_TCmatchdetails' );
}
add_action( 'widgets_init', 'bblm_register_widget_tcmd' );
