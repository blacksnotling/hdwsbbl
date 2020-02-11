<?php
/**
 * Template Companion: Competition Details Widget
 *
 * A Widget that displays the high level details of a competition
 * It should ONLY appear alongside the single-bblm_comp template
 *
 * @class 		BBLM_Widget_TCcompdetails
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/Widget
 * @version   1.0
 */

class BBLM_Widget_TCcompdetails extends WP_Widget {

  function __construct() {

    $widget_ops = array('classname' => 'widget_bblm widget_bblm_tccompdetails widget_bblm_compdetails', 'description' => __( 'Displays the details of a competition (Competition page only)', 'bblm' ) );
    parent::__construct('bblm_tccompdetails', __( 'BB:TC: Competition Details', 'bblm' ), $widget_ops);

  }

  //The Widget Output in the front-end
  public function widget( $args, $instance ) {
    global $wpdb;

    $parentoption = get_option( 'bblm_config' );
    $parentoption = htmlspecialchars( $parentoption[ 'page_comp' ], ENT_QUOTES );

    $parentpage = get_queried_object()->post_parent;

    //Check we are on the correct poat_type before we display the widget
    //Checks to see if the parent of the page matches that in the bblm config
    if ( $parentoption == $parentpage ) {

      //pulling in the vars from the single-bblm_comp template
      global $cd;
      global $cstatus;
      global $cduration;

      //number of teams
      $teamnosql = 'SELECT COUNT( DISTINCT P.t_id ) AS value FROM '.$wpdb->prefix.'team_comp P WHERE P.c_id = '.$cd->c_id.' GROUP BY P.c_id';
      $tno = $wpdb->get_var($teamnosql);

      //comps this season
      $comptseasql = 'SELECT C.c_id, P.post_title, P.guid FROM '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE C.c_id = J.tid AND J.prefix = \'c_\' AND J.pid = P.ID AND C.type_id = 1 AND C.sea_id = '.$cd->sea_id.' AND C.c_id != '.$cd->c_id;

      //comps for this cup
      $comptcupsql = 'SELECT C.c_id, P.post_title, P.guid FROM '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE C.c_id = J.tid AND J.prefix = \'c_\' AND J.pid = P.ID AND C.series_id = '.$cd->SERIES.' AND C.c_id != '.$cd->c_id;

      echo $args['before_widget'];
      echo $args['before_title'] . apply_filters( 'widget_title', 'Comp Information' ) . $args['after_title'];

      echo '<ul>';
      echo '<li><strong>' . __( 'Status', 'bblm' ) . ':</strong> ' . $cstatus . '</li>';
      echo '<li><strong>' . __( 'Duration', 'bblm' ) . ':</strong> ' . $cduration . '</li>';
      echo '<li><strong>' . __( 'Format', 'bblm' ) . ':</strong> ' . $cd->ct_name . '</li>';
      echo '<li><strong>' . __( 'Cup', 'bblm' ) . ':</strong> ' . bblm_get_cup_link( $cd->SERIES ) . '</li>';
      echo '<li><strong>' . __( 'Season', 'bblm' ) . ':</strong> ' . bblm_get_season_link( $cd->sea_id ) . '</li>';
      echo '<li><strong>' . __( 'Number of teams', 'bblm' ) . ':</strong> ' . $tno . '</li>';
      echo '</ul>';

      echo $args['after_widget'];

      echo $args['before_widget'];
      echo $args['before_title'] . apply_filters( 'widget_title', 'Other Competitions this Season' ) . $args['after_title'];

      if ( $comptsea = $wpdb->get_results( $comptseasql ) ) {
        echo '<ul>';
        foreach ( $comptsea as $csea ) {
          echo '<li><a href="' . $csea->guid . '" title="View more on this Competition">' . $csea->post_title . '</a></li>';
        }
        echo '</ul>';
      }
      else {
        echo '<p>None at present.</p>';
      }

      echo $args['after_widget'];

      echo $args['before_widget'];
      echo $args['before_title'] . apply_filters( 'widget_title', 'Other Competitions for this Cup' ) . $args['after_title'];

      if ( $comptcup = $wpdb->get_results( $comptcupsql ) ) {
        echo '<ul>';
        foreach ( $comptcup as $ccup ) {
          echo '<li><a href="' . $ccup->guid . '" title="View more on this Competition">' . $ccup->post_title . '</a></li>';
        }
        echo '</ul>';
      }
      else {
        echo '<p>None at present.</p>';
      }

      echo $args['after_widget'];

    } //end of if the widget should show

  }

  // The Widget output on the admin screen
  public function form( $instance ) {

    echo '<p>'.__( 'Displays the details of a competition (Competition page only)', 'bblm' ).'</p>';
    echo '<p>'.__( 'It will automatically know the competitions that is being displayed', 'bblm' ).'</p>';
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
function bblm_register_widget_tccd() {
  register_widget( 'BBLM_Widget_TCcompdetails' );
}
add_action( 'widgets_init', 'bblm_register_widget_tccd' );
