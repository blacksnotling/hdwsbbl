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

    $widget_ops = array('classname' => 'widget_bblm widget_bblm_tccompdetails', 'description' => __( 'Displays the details of a competition (Competition page only)', 'bblm' ) );
    parent::__construct('bblm_tccompdetails', __( 'BB:TC: Competition Details', 'bblm' ), $widget_ops);

  }

  //The Widget Output in the front-end
  public function widget( $args, $instance ) {
    global $wpdb;

    if ( (is_single() ) && ( $compid = get_queried_object() ) ) {
      $compid = get_queried_object()->ID;
    }
    else {
      $compid = 0;
    }
    $cm = get_post_meta( $compid );

    //Check we are on the correct poat_type before we display the widget
    if ( ( is_single() ) && ( get_post_type( $compid ) == 'bblm_comp' ) ) {

      //number of teams
      $teamnosql = 'SELECT COUNT( DISTINCT P.t_id ) AS value FROM '.$wpdb->prefix.'team_comp P WHERE P.c_id = ' . $compid . ' GROUP BY P.c_id';
      $tno = $wpdb->get_var( $teamnosql );
      if ( 0 == $tno ) {
        $tno = 0;
      }

      //Competition Status
      if ( BBLM_CPT_Comp::is_competition_active( $compid ) ) {
        $cstatus = "In Progress";
      }
      else {
        $cstatus = "Complete";
      }

      //comps this season
      $sposts = get_posts(
        array(
          'post_type' => 'bblm_comp',
          'numberposts' => -1,
          'orderby' => 'ID',
          'order' => 'DESC',
          'meta_query' => array(
              array(
                  'key' => 'comp_season',
                  'value' => $cm[ 'comp_season' ][0],
                  'compare' => '=',
              )
          ),
        )
      );

      //comps for this cup
      $cposts = get_posts(
        array(
          'post_type' => 'bblm_comp',
          'numberposts' => -1,
          'orderby' => 'ID',
          'order' => 'DESC',
          'meta_query' => array(
              array(
                  'key' => 'comp_cup',
                  'value' => $cm[ 'comp_cup' ][0],
                  'compare' => '=',
              )
          ),
        )
      );

      echo $args['before_widget'];

      echo '<div class="widget_bblm_compdetails">';

        echo $args['before_title'] . apply_filters( 'widget_title', 'Comp Information' ) . $args['after_title'];

        echo '<ul>';
        echo '<li><strong>' . __( 'Status', 'bblm' ) . ':</strong> ' . $cstatus . '</li>';
        echo '<li><strong>' . __( 'Duration', 'bblm' ) . ':</strong> ' . BBLM_CPT_Comp::get_comp_duration( $compid ) . '</li>';
        echo '<li><strong>' . __( 'Format', 'bblm' ) . ':</strong> ' . BBLM_CPT_Comp::get_comp_format_name( $compid ) . '</li>';
        echo '<li><strong>' . __( 'Cup', 'bblm' ) . ':</strong> ' . bblm_get_cup_link( $cm[ 'comp_cup' ][0] ) . '</li>';
        echo '<li><strong>' . __( 'Season', 'bblm' ) . ':</strong> ' . bblm_get_season_link( $cm[ 'comp_season' ][0] ) . '</li>';
        echo '<li><strong>' . __( 'Number of teams', 'bblm' ) . ':</strong> ' . esc_html( $tno ) . '</li>';
        echo '</ul>';

      echo '</div>';

      echo $args['after_widget'];

      echo $args['before_widget'];
      echo $args['before_title'] . apply_filters( 'widget_title', 'Other Competitions this Season' ) . $args['after_title'];

      if( $sposts ) {
        echo '<ul>';
        foreach( $sposts as $o ) {
          echo '<li>' . bblm_get_competition_link( $o->ID ) . '</li>';
        }
        echo '</ul>';
      }
      else {
        echo '<p>' . __( 'None at present.', 'bblm' ) . '</p>';
      }

      echo $args['after_widget'];

      echo $args['before_widget'];
      echo $args['before_title'] . apply_filters( 'widget_title', 'Other Competitions for this Cup' ) . $args['after_title'];

      if( $cposts ) {
        echo '<ul>';
        foreach( $cposts as $o ) {
          echo '<li>' . bblm_get_competition_link( $o->ID ) . '</li>';
        }
        echo '</ul>';
      }
      else {
        echo '<p>' . __( 'None at present.', 'bblm' ) . '</p>';
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
