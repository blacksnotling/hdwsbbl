<?php
/**
 * Recent Matches Widget
 *
 * A Widget that displays a Summary of Recent matches
 *
 * @class 		BBLM_Widget_RecentMatchSum
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/Widget
 * @version   1.4s
 */

class BBLM_Widget_RecentMatchSum extends WP_Widget {

  function __construct() {

    $widget_ops = array('classname' => 'widget_bblm widget_bblm_recentmatches', 'description' => __( 'Display a summary of recent matches', 'bblm' ) );
    parent::__construct('bblm_recentmatches', __( 'BB:All: Recent Match', 'bblm' ), $widget_ops);

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
    if ( empty( $instance['nummatch'] ) ) {
      $instance['nummatch'] = 6;
    }

    $oposts = get_posts(
      array(
        'post_type' => 'bblm_match',
        'numberposts' => $instance['nummatch'],
        'orderby' => 'post_date_gmt',
        'order' => 'DESC'
      )
    );

    if( $oposts ) {
      $zebracount = 1;
?>
      <table class="bblm_table bblm_sortable">
        <thead>
          <tr>
            <th class="bblm_tbl_matchdate"><?php echo __('Date', 'bblm'); ?></th>
            <th class="bblm_tbl_matchname"><?php echo __('Result', 'bblm'); ?></th>
          </tr>
        </thead>
        <tbody>
<?php
    	foreach( $oposts as $o ) {
        if ( $zebracount % 2 ) {
          echo '<tr class="bblm_tbl_alt">';
        }
        else {
          echo '<tr>';
        }
        echo '<td>' . bblm_get_match_link_date( $o->ID ) . '</td>';
        echo '<td>' . bblm_get_match_link_score( $o->ID ) . '</td>';
        echo '</tr>';

        $zebracount++;
    	}
    	echo '</tbody>';
      echo '</table>';

    }

    echo $args['after_widget'];

  }

  // The Widget output on the admin screen
  public function form( $instance ) {

    echo '<p>'.__( 'Displays a summary of recent matches (suitible for the sidebar)', 'bblm' ).'</p>';
    //if no data has been provided then this populates some default values
    $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Recent Matches', 'bblm' );
    $nummatch = ! empty( $instance['nummatch'] ) ? $instance['nummatch'] : "6";
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
      <label for="<?php echo esc_attr( $this->get_field_id( 'nummatch' ) ); ?>"><?php esc_attr_e( 'Number of matches to show:', 'bblm' ); ?></label>
      <input
        class="widefat"
        id="<?php echo esc_attr( $this->get_field_id( 'nummatch' ) ); ?>"
        name="<?php echo esc_attr( $this->get_field_name( 'nummatch' ) ); ?>"
        type="text"
        value="<?php echo esc_attr( $nummatch ); ?>">
    </p>
	<?php

 	}

  // Function to save any settings from he widget
  public function update( $new_instance, $old_instance ) {

    $instance = $old_instance;

    $instance = array();
  	$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
    $instance['nummatch'] = ( ! empty( $new_instance['nummatch'] ) ) ? (int) strip_tags( $new_instance['nummatch'] ) : '';

    return $instance;

  }

}

// Register the widget.
function bblm_register_widget_rms() {
  register_widget( 'BBLM_Widget_RecentMatchSum' );
}
add_action( 'widgets_init', 'bblm_register_widget_rms' );
