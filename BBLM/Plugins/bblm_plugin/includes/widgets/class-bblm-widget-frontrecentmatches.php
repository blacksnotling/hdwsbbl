<?php
/**
 * Recent Matches Widget
 *
 * A Widget that displays a Summary of Recent matches for the Front Page
 *
 * @class 		BBLM_Widget_FrontRecentMatchSum
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/Widget
 * @version   1.0
 */

class BBLM_Widget_FrontRecentMatchSum extends WP_Widget {

  function __construct() {

    $widget_ops = array('classname' => 'widget_bblm widget_bblm_frontrecentmatches', 'description' => __( 'Front Page:Display a summary of recent matches', 'bblm' ) );
    parent::__construct('bblm_frontrecentmatches', __( 'BB:Front: Recent Match', 'bblm' ), $widget_ops);

  }

  //The Widget Output in the front-end
  public function widget( $args, $instance ) {
    global $wpdb;

    echo '<div class="bblm-widget-align-' . esc_attr( $instance['align'] ) . '">';
    echo $args['before_widget'];

    //If a title was provided then display the title
    if ( ! empty( $instance['title'] ) ) {
      echo '<h2 class="bblm-table-caption">' . apply_filters( 'widget_title', $instance['title'] ) . '</h2>';
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
      <table class="bblm_table bblm-event-blocks bblm-data-table">
        <thead>
          <tr>
            <th></th>
          </tr>
        </thead>
        <tbody>
<?php
    	foreach( $oposts as $o ) {
        $matchsql = 'SELECT M.m_teamAtd, M.m_teamBtd, M.c_id AS CWPID, T.WPID AS TATWPID, R.WPID AS TBTWPID FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'team R WHERE M.m_teamA = T.t_id AND M.m_teamB = R.t_id AND M.WPID = '. $o->ID;
        $match = $wpdb->get_row ( $matchsql );

        if ( $zebracount % 2 ) {
          echo '<tr class="bblm_tbl_alt">';
        }
        else {
          echo '<tr>';
        }
?>
            <td>
              <span class="team-logo logo-odd">
                <?php echo bblm_get_team_link_logo( $match->TATWPID ); ?>
              </span>
              <span class="team-logo logo-even">
                <?php echo bblm_get_team_link_logo( $match->TBTWPID ); ?>
              </span>
              <h4 class="bblm-event-title">
                <?php echo bblm_get_match_link( $o->ID ); ?>
              </h4>
              <h5 class="bblm-event-results">
                <span class="bblm-result">1-0</span>
              </h5>
              <div class="sp-event-comp"><?php echo bblm_get_competition_link( $match->CWPID ); ?></div>
              <time class="bblm-event-date">
                <?php echo bblm_get_match_link_date( $o->ID, "long" ); ?>
              </time>
            </td>
          </tr>
<?php
        $zebracount++;
    	}
    	echo '</tbody>';
      echo '</table>';

    }
    echo $args['after_widget'];
    echo '</div>';

  }

  // The Widget output on the admin screen
  public function form( $instance ) {

    echo '<p>'.__( 'Front Page: Displays a summary of recent matches in full graphical format', 'bblm' ).'</p>';
    //if no data has been provided then this populates some default values
    $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Recent Matches', 'bblm' );
    $nummatch = ! empty( $instance['nummatch'] ) ? $instance['nummatch'] : "6";
    $align = ! empty( $instance['align'] ) ? $instance['align'] : "none";
	?>
    <p><select id="<?php echo esc_attr( $this->get_field_id( 'align' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'align' ) ); ?>" class="widefat" style="width:100%;">
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
    $instance['align'] = ( ! empty( $new_instance['align'] ) ) ? esc_attr( $new_instance['align'] ) : '';

    return $instance;

  }

}

// Register the widget.
function bblm_register_widget_frms() {
  register_widget( 'BBLM_Widget_FrontRecentMatchSum' );
}
add_action( 'widgets_init', 'bblm_register_widget_frms' );
