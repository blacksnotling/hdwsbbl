<?php
/**
 * Recent Players Widget
 *
 * A Widget that displays the most recent players to be hired
 *
 * @class 		BBLM_Widget_RecentPlayers
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/Widget
 * @version   1.0.1
 */

class BBLM_Widget_RecentPlayers extends WP_Widget {

  function __construct() {

    $widget_ops = array('classname' => 'widget_bblm widget_bblm_recentplayers', 'description' => __( 'Display a summary of recently hired players', 'bblm' ) );
    parent::__construct('bblm_recentplayers', __( 'BB:All: Recently Hired Players', 'bblm' ), $widget_ops);

  }

  //The Widget Output in the front-end
  public function widget( $args, $instance ) {
    global $wpdb;

    echo $args['before_widget'];

    //If a title was provided then display the title
    if ( ! empty( $instance['title'] ) ) {
      echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
    }
    //If a number of matches was not submitted, then default to 6
    if ( empty( $instance['numshow'] ) ) {
      $instance['numshow'] = 6;
    }

    $playersql = 'SELECT T.WPID, P.WPID AS PID, H.pos_name FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'position H WHERE H.pos_id = P.pos_ID AND P.t_id = T.t_ID ORDER BY P.WPID DESC LIMIT ' . $instance['numshow'];
    if ( $player = $wpdb->get_results( $playersql ) ) {

      echo '<ul>';

      foreach ( $player as $p ) {

        echo '<li>' . bblm_get_player_link( $p->PID ) . ' (' . esc_html( $p->pos_name ) . ') by ' . bblm_get_team_link( $p->WPID ) . '</li>';

      }

      echo '</ul>';
    }

    echo $args['after_widget'];

  }

  // The Widget output on the admin screen
  public function form( $instance ) {

    echo '<p>'.__( 'Displays a summary of recently hired Players (suitible for the sidebar)', 'bblm' ).'</p>';
    //if no data has been provided then this populates some default values
    $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Recent Hires', 'bblm' );
    $numshow = ! empty( $instance['numshow'] ) ? $instance['numshow'] : "6";
	?>
    <p>
      <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'bblm' ); ?></label>
      <input
        class="widefat"
        id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
        name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
        type="text"
        value="<?php echo esc_attr( $title ); ?>">
    </p>

    <p>
      <label for="<?php echo esc_attr( $this->get_field_id( 'numshow' ) ); ?>"><?php esc_attr_e( 'Number of players to show:', 'bblm' ); ?></label>
      <input
        class="widefat"
        id="<?php echo esc_attr( $this->get_field_id( 'numshow' ) ); ?>"
        name="<?php echo esc_attr( $this->get_field_name( 'numshow' ) ); ?>"
        type="text"
        value="<?php echo esc_attr( $numshow ); ?>">
    </p>
	<?php

 	}

  // Function to save any settings from he widget
  public function update( $new_instance, $old_instance ) {

    $instance = $old_instance;

    $instance = array();
  	$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
    $instance['numshow'] = ( ! empty( $new_instance['numshow'] ) ) ? (int) strip_tags( $new_instance['numshow'] ) : '';

    return $instance;

  }

}

// Register the widget.
function bblm_register_widget_rps() {
  register_widget( 'BBLM_Widget_RecentPlayers' );
}
add_action( 'widgets_init', 'bblm_register_widget_rps' );
