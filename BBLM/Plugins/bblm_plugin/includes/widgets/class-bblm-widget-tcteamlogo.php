<?php
/**
 * Template Companion: Team Logo Widget
 *
 * A Widget that displays the team logo
 * It should ONLY appear alongside the single-bblm_team template
 *
 * @class 		BBLM_Widget_TCteamlogo
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/Widget
 * @version   1.0
 */

class BBLM_Widget_TCteamlogo extends WP_Widget {

  function __construct() {

    $widget_ops = array('classname' => 'widget_bblm widget_bblm_tcteamlogo', 'description' => __( 'Displays the logo of a team (Team page only)', 'bblm' ) );
    parent::__construct('bblm_tcteamlogo', __( 'BB:TC: Team Logo', 'bblm' ), $widget_ops);

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
      global $timg;

      echo $args['before_widget'];
      echo $timg;
      echo $args['after_widget'];

    } //end of if the widget should show

  }

  // The Widget output on the admin screen
  public function form( $instance ) {

    echo '<p>'.__( 'Displays the logo of a team (Team page only)', 'bblm' ).'</p>';
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
function bblm_register_widget_tctl() {
  register_widget( 'BBLM_Widget_TCteamlogo' );
}
add_action( 'widgets_init', 'bblm_register_widget_tctl' );
