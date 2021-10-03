<?php
/**
 * Fixtures Widget
 *
 * A Widget that displays the sponsor of the league for the Front Page
 *
 * @class 		BBLM_Widget_FrontSponsor
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/Widget
 * @version   1.0
 */

class BBLM_Widget_FrontSponsor extends WP_Widget {

  function __construct() {

    // Add Widget scripts
    add_action('admin_enqueue_scripts', array( $this, 'scripts') );

    $widget_ops = array('classname' => 'widget_bblm widget_bblm_frontsponsors bblm-footer-sponsors-section', 'description' => __( 'Front Page: Displays the Sponsor of the League', 'bblm' ) );
    parent::__construct('bblm_frontsponsors', __( 'BB:Front: Sponsors', 'bblm' ), $widget_ops);

  }

  //The Widget Output in the front-end
  public function widget( $args, $instance ) {
    global $wpdb;

    $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Proudly Sponsored By:', 'bblm' ) : $instance['title'] );
    $image = ! empty( $instance['image'] ) ? $instance['image'] : '';

    echo $args['before_widget'];

?>
    <div class="bblm-footer-sponsors">
      <div class="bblm-sponsors">
<?php
          if ( ! empty( $instance['title'] ) ) {
            echo '<h3 class="bblm-sponsors-title">' . $title . '</h3>';
          }
          if ( $image ) {
            echo '<img width="300" height="100"  src="' . esc_url( $image ) . '" alt="">';
          }
?>
      </div>
    </div>
<?php


    echo $args['after_widget'];

  }

  // The Widget output on the admin screen
  public function form( $instance ) {

    echo '<p>'.__( 'Front Page: Displays a summary of upcoming Fixtures in full graphical format', 'bblm' ).'</p>';
    //if no data has been provided then this populates some default values
    $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Proudly Sponsored By:', 'bblm' );
    $image = ! empty( $instance['image'] ) ? $instance['image'] : '';
  ?>
    <p>
      <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title of Sponsor area:', 'bblm' ); ?></label>
      <input
        class="widefat"
        id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
        name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
        type="text"
        value="<?php echo esc_attr( $title ); ?>">
    </p>
    <p>
       <label for="<?php echo $this->get_field_id( 'image' ); ?>"><?php _e( 'Image:' ); ?></label>
       <img id="image_upload_preview" src="<?php echo $image; ?>" alt=""  width="300" height="100" />
       <input class="widefat" id="<?php echo $this->get_field_id( 'image' ); ?>" name="<?php echo $this->get_field_name( 'image' ); ?>" type="text" value="<?php echo esc_url( $image ); ?>" />
       <button class="upload_image_button button button-primary">Upload Image</button>
    </p>
	<?php

 	}

  // Function to save any settings from he widget
  public function update( $new_instance, $old_instance ) {

    $instance = $old_instance;

    $instance = array();
  	$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
    $instance['image'] = ( ! empty( $new_instance['image'] ) ) ? $new_instance['image'] : '';

    return $instance;

  }

  public function scripts()
  {
     wp_enqueue_script( 'media-upload' );
     wp_enqueue_media();
     wp_register_script( 'widget_sponsor_media', plugin_dir_url( __FILE__ ) . '../js/widget_sponsor_media.js' );
     wp_enqueue_script( 'widget_sponsor_media' );
  }

}


// Register the widget.
function bblm_register_widget_fspon() {
  register_widget( 'BBLM_Widget_FrontSponsor' );
}
add_action( 'widgets_init', 'bblm_register_widget_fspon' );
