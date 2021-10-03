<?php
/**
 * Recent Players Widget
 *
 * A Widget that displays the most recent players to Leave a team
 *
 * @class 		BBLM_Widget_RecentDepartures
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/Widget
 * @version   1.1
 */

class BBLM_Widget_RecentDepartures extends WP_Widget {

  function __construct() {

    $widget_ops = array('classname' => 'widget_bblm widget_bblm_recentdepartures', 'description' => __( 'Display a summary of recently departed players', 'bblm' ) );
    parent::__construct('bblm_recentdepartures', __( 'BB:All: Recently Departed Players', 'bblm' ), $widget_ops);

  }

  //The Widget Output in the front-end
  public function widget( $args, $instance ) {
    global $wpdb;

    echo '<div class="bblm-widget-align-' . esc_attr( $instance['align'] ) . '">';
    echo $args['before_widget'];


    //If a title was provided then display the title, If on the front page then force a heading wrapper
    if ( ( ! empty( $instance['title'] ) ) && is_front_page() ) {
      echo '<h2 class="bblm-table-caption">' . apply_filters( 'widget_title', $instance['title'] ) . '</h2>';
    }
    else if ( ! empty( $instance['title'] ) ) {
      echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
    }
    //If a number of items to show was not submitted, then default to 6
    if ( empty( $instance['numshow'] ) ) {
      $instance['numshow'] = 6;
    }

    $playersql = 'SELECT T.WPID, P.WPID AS PID, H.pos_name FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'position H WHERE H.pos_id = P.pos_ID AND P.t_id = T.t_ID ORDER BY P.WPID DESC LIMIT ' . $instance['numshow'];
    $playersql = 'SELECT T.WPID, P.WPID AS PID, F.*, H.pos_name FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'player_fate X, '.$wpdb->prefix.'fate F, '.$wpdb->prefix.'position H WHERE H.pos_id = P.pos_ID AND F.f_id = X.f_id AND X.p_id = P.p_id AND P.t_id = T.t_ID AND F.f_id != 5 ORDER BY X.m_id DESC LIMIT ' . $instance['numshow'];
    if ( $player = $wpdb->get_results( $playersql ) ) {
      $zebracount = 1;

?>
      <table>
        <thead>
          <tr>
            <th><?php echo __( 'Player','bblm' ); ?></th>
            <th><?php echo __( 'Team','bblm' ); ?></th>
            <th><?php echo __( 'Reason','bblm' ); ?></th>
          </tr>
        </thead>
        <tbody>
<?php

      foreach ( $player as $p ) {

        if ( $zebracount % 2 ) {
          echo '<tr class="bblm_tbl_alt">';
        }
        else {
          echo '<tr>';
        }

        echo '<td>' . bblm_get_player_link( $p->PID ) . ' (' . esc_html( $p->pos_name ) . ')</td>';
        echo '<td>' . bblm_get_team_link( $p->WPID ) . '</td>';
        echo '<td>' . esc_html( $p->d_desc ) . '</td>';
        echo '</tr>';

        $zebracount++;

      }

      echo '</tbody></table>';
    }

    echo $args['after_widget'];
    echo '</div>';

  }

  // The Widget output on the admin screen
  public function form( $instance ) {

    echo '<p>'.__( 'Displays a summary of recently departed Players (suitible for the sidebar)', 'bblm' ).'</p>';
    //if no data has been provided then this populates some default values
    $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Recent Departures', 'bblm' );
    $numshow = ! empty( $instance['numshow'] ) ? $instance['numshow'] : "6";
    $align = ! empty( $instance['align'] ) ? $instance['align'] : "none";
	?>
    <p><label for="<?php echo esc_attr( $this->get_field_id( 'align' ) ); ?>"><?php esc_attr_e( 'Alignment when on Front page:', 'bblm' ); ?></label>
      <select id="<?php echo esc_attr( $this->get_field_id( 'align' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'align' ) ); ?>" class="widefat" style="width:100%;">
      <option<?php selected( esc_attr( $align ), "none" ); ?> value="none"><?php echo __( 'None','bblm' ); ?></option>
      <option<?php selected( esc_attr( $align ), "left" ); ?> value="left"><?php echo __( 'Left','bblm' ); ?></option>
      <option<?php selected( esc_attr( $align ), "right" ); ?> value="right"><?php echo __( 'Right','bblm' ); ?></option>
    </select></p>
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
    $instance['align'] = ( ! empty( $new_instance['align'] ) ) ? esc_attr( $new_instance['align'] ) : '';

    return $instance;

  }

}

// Register the widget.
function bblm_register_widget_rds() {
  register_widget( 'BBLM_Widget_RecentDepartures' );
}
add_action( 'widgets_init', 'bblm_register_widget_rds' );
