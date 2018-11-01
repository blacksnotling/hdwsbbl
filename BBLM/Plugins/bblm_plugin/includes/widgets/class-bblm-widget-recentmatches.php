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
 * @version   1.0
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

    $matchsql = "SELECT M.m_date, T.t_name AS tA, Q.t_name AS tB, M.m_teamAtd, M.m_teamBtd, M.m_gate, P.guid FROM ".$wpdb->prefix."match M, ".$wpdb->prefix."bb2wp J, ".$wpdb->posts." P, ".$wpdb->prefix."team T, ".$wpdb->prefix."team Q, ".$wpdb->prefix."comp C WHERE C.c_id = M.c_id AND M.m_id = J.tid AND J.pid = P.ID AND J.prefix = 'm_' AND M.m_teamA = T.t_id AND C.c_show = 1 AND C.type_id = 1 AND M.m_teamB = Q.t_id AND C.c_counts = '1' ORDER BY m_date DESC, m_id DESC LIMIT ".$instance['nummatch'];
    if ($matches = $wpdb->get_results($matchsql)) {
    	print("<ul>\n");
    	foreach ($matches as $match) {
    		print("  <li><a href=\"".$match->guid."\" title=\"View the match in detail\">".$match->tA." <strong>".$match->m_teamAtd."</strong> vs <strong>".$match->m_teamBtd."</strong> ".$match->tB."</a></li>");
    	}
    	print("</ul>\n");
    }

    echo $args['after_widget'];

  }

  // The Widget output on the admin screen
  public function form( $instance ) {

    echo '<p>'.__( 'Displays a summary fo recent matches (suitible for the sidebar)', 'bblm' ).'</p>';
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
