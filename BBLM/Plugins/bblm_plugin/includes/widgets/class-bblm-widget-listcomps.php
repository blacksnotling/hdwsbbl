<?php
/**
 * Competition Widget
 *
 * A Widget that displays a list of recent, active, and upcoming competitions
 *
 * @class 		BBLM_Widget_ListComps
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/Widget
 * @version   1.0
 */

class BBLM_Widget_ListComps extends WP_Widget {

  function __construct() {

    $widget_ops = array('classname' => 'widget_bblm widget_bblm_listcomps', 'description' => __( 'Display a List of recent, active, and upcoming Competitions', 'bblm' ) );
    parent::__construct('bblm_listcomps', __( 'BB: Competitions List', 'bblm' ), $widget_ops);

  }

  //The Widget Output in the front-end
  public function widget( $args, $instance ) {
    global $wpdb;

    echo $args['before_widget'];

    //determine current Season
		$seasonsql = 'SELECT S.sea_id FROM '.$wpdb->prefix.'season S, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE S.sea_id = J.tid AND J.prefix = \'sea_\' AND J.pid = P.ID AND S.sea_active = 1 ORDER BY S.sea_sdate DESC LIMIT 1';
		$sea_id = $wpdb->get_var($seasonsql);

		$compsql = 'SELECT P.post_title, P.guid, C.c_active, UNIX_TIMESTAMP(C.c_sdate) AS sdate  FROM '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE C.c_id = J.tid AND J.prefix = \'c_\' AND J.pid = P.ID AND C.c_counts = 1 AND C.type_id = 1 AND C.sea_id = '.$sea_id.' ORDER BY C.c_active DESC, C.c_sdate ASC';
		if ($complisting = $wpdb->get_results($compsql)) {
      //set up the code below
			$is_first = 1;
			$last_stat = 0;
			$today = date("U");

			foreach ($complisting as $cl) {
				if (($cl->c_active) && ($cl->sdate < $today)) {

						if ((1 !== $last_stat) && (!$is_first)) {
							print("		</ul>\n	</div>\n");
							$is_first = 1;
						}
						if ($is_first) {
              echo '<div>';
              echo $args['before_title'] . apply_filters( 'widget_title', __( 'Active Competitions' ) ) . $args['after_title'];
              echo '<ul>';

              $is_first = 0;
						}
						print("			<li><a href=\"".$cl->guid."\" title=\"View more about ".$cl->post_title."\">".$cl->post_title."</a></li>\n");
						$last_stat = 1;
				}//end of active comp
				else if (($cl->c_active) && ($cl->sdate > $today)) {

						if ((2 !== $last_stat) && (!$is_first)) {
							print("		</ul>\n	</div>\n");
							$is_first = 1;
						}
						if ($is_first) {
              echo '<div>';
              echo $args['before_title'] . apply_filters( 'widget_title', __( 'Upcoming Competitions' ) ) . $args['after_title'];
              echo '<ul>';

							$is_first = 0;
						}
						print("			<li><a href=\"".$cl->guid."\" title=\"View more about ".$cl->post_title."\">".$cl->post_title."</a></li>\n");
						$last_stat = 2;
				}//end of upcoming comp
				else {

						if ((3 !== $last_stat) && (!$is_first)) {
							print("		</ul>\n	</div>\n");
							$is_first = 1;
						}
						if ($is_first) {
              echo '<div>';
              echo $args['before_title'] . apply_filters( 'widget_title', __( 'Recent Competitions' ) ) . $args['after_title'];
              echo '<ul>';

							$is_first = 0;
						}
						print("			<li><a href=\"".$cl->guid."\" title=\"View more about ".$cl->post_title."\">".$cl->post_title."</a></li>\n");
						$last_stat = 3;
				}//end of recent comp
			}//end of for each
			print("		</ul>\n	</div>\n");
		}//end of if sql


    echo $args['after_widget'];

  }

  // The Widget output on the admin screen
  public function form( $instance ) {

    echo '<p>'.__( 'Displays a list of recent, active, and upcoming competitions (suitible for the sidebar)', 'bblm' ).'</p>';

  }

  // Function to save any settings from he widget
  public function update( $new_instance, $old_instance ) {

    $instance = $old_instance;
    $instance[ 'title' ] = strip_tags( $new_instance[ 'title' ] );
    return $instance;

  }

}

// Register the widget.
function bblm_register_widget_lcs() {
  register_widget( 'BBLM_Widget_ListComps' );
}
add_action( 'widgets_init', 'bblm_register_widget_lcs' );
