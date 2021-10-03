<?php
/**
 * Fixtures Widget
 *
 * A Widget that displays a Summary of upcoming matches
 *
 * @class 		BBLM_Widget_Fixtures
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/Widget
 * @version   1.0
 */

class BBLM_Widget_Fixtures extends WP_Widget {

  function __construct() {

    $widget_ops = array('classname' => 'widget_bblm widget_bblm_wfixtures', 'description' => __( 'Displays a list of Fixtures', 'bblm' ) );
    parent::__construct('bblm_wfixtures', __( 'BB:All: Fixtures', 'bblm' ), $widget_ops);

  }

  //The Widget Output in the front-end
  public function widget( $args, $instance ) {
    global $wpdb;

    echo $args['before_widget'];

    //If a title was provided then display the title
    if ( ! empty( $instance['title'] ) ) {
      echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
    }
    //If a number of fixtures was not submitted, then default to 6
    if ( empty( $instance['numfix'] ) ) {
      $instance['numfix'] = 6;
    }

    $fixturesql = 'SELECT UNIX_TIMESTAMP(F.f_date) AS mdate, C.WPID AS CWPID, T.t_id AS TAid, T.WPID AS TATWPID, R.t_id AS TBid, R.WPID AS TBTWPID, F.f_id FROM '.$wpdb->prefix.'fixture F, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'team R WHERE F.f_teamA = T.t_id AND F.f_teamB = R.t_id AND F.c_id = C.WPID AND F.f_complete = 0 ORDER BY F.f_date ASC, F.c_id DESC, F.div_id DESC LIMIT ' . $instance['numfix'];
    //Run the Query. If successful....
    if ( $fixture = $wpdb->get_results( $fixturesql ) ) {

      //grab the ID of the "tbd" team
      $bblm_tbd_team = bblm_get_tbd_team();

      $zebracount = 1;
?>
      <table class="bblm_table bblm_sortable">
        <thead>
          <tr>
            <th class="bblm_tbl_matchdate"><?php echo __('Date', 'bblm'); ?></th>
            <th class="bblm_tbl_matchname"><?php echo __('Home', 'bblm'); ?></th>
            <th class="bblm_tbl_name"><?php echo __('Away', 'bblm'); ?></th>
          </tr>
        </thead>
        <tbody>
<?php
      foreach ( $fixture as $m ) {
        if ( $zebracount % 2 ) {
          echo '<tr class="bblm_tbl_alt" id="F'. $m->f_id . '">';
        }
        else {
          echo '<tr id="F'. $m->f_id . '">';
        }
?>
            <td><?php echo date("d.m.y", $m->mdate ); ?></td>
            <td>
<?php
        if ( $bblm_tbd_team == $m->TAid ) {
          echo __( 'To Be Determined', 'bblm' );
        }
        else {
          echo bblm_get_team_link( $m->TATWPID );
        }
        echo ' </td><td> ';
        if ( $bblm_tbd_team == $m->TBid ) {
          echo __( 'To Be Determined', 'bblm' );
        }
        else {
          echo bblm_get_team_link( $m->TBTWPID );
        }
?>
            </td>
          </tr>
<?php
        $zebracount++;

      }//end of foreach $fixture
?>
        </tbody>
      </table>
<?php

    }//end of if fixtures sql

    echo $args['after_widget'];

  }

  // The Widget output on the admin screen
  public function form( $instance ) {

    echo '<p>'.__( 'Displays a summary of upcoming Fixtures (suitible for the sidebar)', 'bblm' ).'</p>';
    //if no data has been provided then this populates some default values
    $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Upcoming Fixtures', 'bblm' );
    $numfix = ! empty( $instance['numfix'] ) ? $instance['numfix'] : "6";
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
      <label for="<?php echo esc_attr( $this->get_field_id( 'numfix' ) ); ?>"><?php esc_attr_e( 'Number of Fixtures to show:', 'bblm' ); ?></label>
      <input
        class="widefat"
        id="<?php echo esc_attr( $this->get_field_id( 'numfix' ) ); ?>"
        name="<?php echo esc_attr( $this->get_field_name( 'numfix' ) ); ?>"
        type="text"
        value="<?php echo esc_attr( $numfix ); ?>">
    </p>
	<?php

 	}

  // Function to save any settings from he widget
  public function update( $new_instance, $old_instance ) {

    $instance = $old_instance;

    $instance = array();
  	$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
    $instance['numfix'] = ( ! empty( $new_instance['numfix'] ) ) ? (int) strip_tags( $new_instance['numfix'] ) : '';

    return $instance;

  }

}

// Register the widget.
function bblm_register_widget_fix() {
  register_widget( 'BBLM_Widget_Fixtures' );
}
add_action( 'widgets_init', 'bblm_register_widget_fix' );
