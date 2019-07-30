<?php
/**
 * Recent Matches Widget
 *
 * A Widget that displays a Summary of Recent transfers
 *
 * @class 		BBLM_Widget_RecentTransfers
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/Widget
 * @version   1.0
 */

class BBLM_Widget_RecentTransfers extends WP_Widget {

  function __construct() {

    $widget_ops = array('classname' => 'widget_bblm widget_bblm_recenttransfers', 'description' => __( 'Display a summary of recent transfers', 'bblm' ) );
    parent::__construct('bblm_recenttransfers', __( 'BB:All: Recent Transfers', 'bblm' ), $widget_ops);

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

    $trans = new BBLM_CPT_Transfer;
    $trans->display_recent_transfer_list( $instance[ 'numshow' ] );

    echo $args['after_widget'];

  }

  // The Widget output on the admin screen
  public function form( $instance ) {

    echo '<p>'.__( 'Displays a summary of recent transfers (suitible for the sidebar)', 'bblm' ).'</p>';
    //if no data has been provided then this populates some default values
    $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Recent Transfers', 'bblm' );
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
      <label for="<?php echo esc_attr( $this->get_field_id( 'numshow' ) ); ?>"><?php esc_attr_e( 'Number of transfers to show:', 'bblm' ); ?></label>
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
function bblm_register_widget_rts() {
  register_widget( 'BBLM_Widget_RecentTransfers' );
}
add_action( 'widgets_init', 'bblm_register_widget_rts' );
