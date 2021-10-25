<?php
/**
 * Team Performance
 *
 * A Widget that displays the performance of active teams for the Front Page
 *
 * @class 		BBLM_Widget_FrontTeamPerformance
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/Widget
 * @version   1.0.1
 */

class BBLM_Widget_FrontTeamPerformance extends WP_Widget {

  function __construct() {

    $widget_ops = array('classname' => 'widget_bblm widget_bblm_frontteamperf', 'description' => __( 'Front Page:Displays the active teams performance', 'bblm' ) );
    parent::__construct('bblm_frontteamperf', __( 'BB:Front: Team Performance', 'bblm' ), $widget_ops);

  }

  //The Widget Output in the front-end
  public function widget( $args, $instance ) {
    global $wpdb;

    $seasonlist = 0;
    $numteam = ! empty( $instance['numteam'] ) ? $instance['numteam'] : '12';
    $teamistingsql = 'SELECT Z.WPID AS TWPID, SUM(T.tc_played) AS TP, SUM(T.tc_W) AS TW, SUM(T.tc_L) AS TL, SUM(T.tc_D) AS TD, SUM(T.tc_tdfor) AS TDF, SUM(T.tc_tdagst) AS TDA, SUM(T.tc_casfor) AS TCF, SUM(T.tc_casagst) AS TCA, SUM(T.tc_INT) AS TI, SUM(T.tc_comp) AS TC, (SUM(T.tc_W) / SUM(T.tc_played)) AS WINPC FROM '.$wpdb->prefix.'team_comp T, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'team Z WHERE Z.t_id = T.t_id AND Z.t_show = 1 AND C.WPID = T.c_id AND C.c_counts = 1 ';

    //Grabs the last season, and then determines if it is active
    $currentsea = BBLM_CPT_Season::get_current_season();
    if ( BBLM_CPT_Season::is_season_active( $currentsea ) ) {
      //We have an active season, grab the players who performed the best
      $teamistingsql .= 'AND C.sea_id = ' . $currentsea . ' GROUP BY T.t_id ORDER BY WINPC DESC';
      $seasonlist = 1;
    }
    else {
      $teamistingsql .= 'GROUP BY T.t_id ORDER BY WINPC DESC LIMIT ' . $numteam;
    }

    echo '<div class="bblm-widget-align-' . esc_attr( $instance['align'] ) . '">';
    echo $args['before_widget'];

    if ( $seasonlist ) {
      echo '<h2 class="bblm-table-caption">' . apply_filters( 'widget_title', __( 'Top Performing Teams this Season','bblm' ) ) . '</h2>';
    }
    else {
      echo '<h2 class="bblm-table-caption">' . apply_filters( 'widget_title', __( 'Top Performing Teams in League History','bblm' ) ) . '</h2>';
    }

    if ($teamstats = $wpdb->get_results($teamistingsql)) {
      $zebracount = 1;
?>
      <div role="region" aria-labelledby="Caption01" tabindex="0">
        <table class="bblm_table bblm_sortable bblm_table_collapsable">
          <thead>
            <tr>
              <th></th>
              <th class="bblm_tbl_name"><?php echo __( 'Team', 'bblm' ); ?></th>
              <th class="bblm_tbl_stat"><?php echo __( 'P', 'bblm' ); ?></th>
              <th class="bblm_tbl_stat"><?php echo __( 'W', 'bblm' ); ?></th>
              <th class="bblm_tbl_stat"><?php echo __( 'L', 'bblm' ); ?></th>
              <th class="bblm_tbl_stat"><?php echo __( 'D', 'bblm' ); ?></th>
              <th class="bblm_tbl_stat bblm_tbl_collapse"><?php echo __( 'TF', 'bblm' ); ?></th>
              <th class="bblm_tbl_stat bblm_tbl_collapse"><?php echo __( 'TA', 'bblm' ); ?></th>
              <th class="bblm_tbl_stat bblm_tbl_collapse"><?php echo __( 'CF', 'bblm' ); ?></th>
              <th class="bblm_tbl_stat bblm_tbl_collapse"><?php echo __( 'CA', 'bblm' ); ?></th>
              <th class="bblm_tbl_stat bblm_tbl_collapse"><?php echo __( 'COMP', 'bblm' ); ?></th>
              <th class="bblm_tbl_stat bblm_tbl_collapse"><?php echo __( 'INT', 'bblm' ); ?></th>
              <th class="bblm_tbl_stat"><?php echo __( 'WIN%', 'bblm' ); ?></th>
            </tr>
          </thead>
          <tbody>
<?php
      foreach ( $teamstats as $tst ) {
        if ($zebracount % 2) {
          echo '<tr class="bblm_tbl_alt">';
        }
        else {
          echo '<tr>';
        }

        echo '<td>' . bblm_get_team_link_logo( $tst->TWPID, 'mini' ) . '</td>';
        echo '<td>' . bblm_get_team_link( $tst->TWPID ) . '</td>';
        echo '<td>' . $tst->TP . '</td>';
        echo '<td>' . $tst->TW . '</td>';
        echo '<td>' . $tst->TL . '</td>';
        echo '<td>' . $tst->TD . '</td>';
        echo '<td class="bblm_tbl_collapse">' . $tst->TDF . '</td>';
        echo '<td class="bblm_tbl_collapse">' . $tst->TDA . '</td>';
        echo '<td class="bblm_tbl_collapse">' . $tst->TCF . '</td>';
        echo '<td class="bblm_tbl_collapse">' . $tst->TCA . '</td>';
        echo '<td class="bblm_tbl_collapse">' . $tst->TC . '</td>';
        echo '<td class="bblm_tbl_collapse">' . $tst->TI . '</td>';

        if ( $tst->TP > 0 ) {
          echo '<td>' . number_format( $tst->WINPC  * 100 ) . '%</td>';
        }
        else {
          echo '<td>N/A</td>';
        }
        echo '</tr>';
        $zebracount++;
      } //end of foreach
      echo '</tbody>';
      echo '</table>';
      echo '</div>';
    }

  }

  // The Widget output on the admin screen
  public function form( $instance ) {

    echo '<p>'.__( 'Front Page: Displays the performance of active teams. If no teams are active then historical teams are displayed', 'bblm' ).'</p>';
    //if no data has been provided then this populates some default values
    $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Recent Matches', 'bblm' );
    $numteam = ! empty( $instance['numteam'] ) ? $instance['numteam'] : "12";
    $align = ! empty( $instance['align'] ) ? $instance['align'] : "none";
	?>
    <p><label for="<?php echo esc_attr( $this->get_field_id( 'align' ) ); ?>"><?php esc_attr_e( 'Alignment when on Front page:', 'bblm' ); ?></label>
      <select id="<?php echo esc_attr( $this->get_field_id( 'align' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'align' ) ); ?>" class="widefat" style="width:100%;">
      <option<?php selected( esc_attr( $align ), "none" ); ?> value="none"><?php echo __( 'None','bblm' ); ?></option>
      <option<?php selected( esc_attr( $align ), "left" ); ?> value="left"><?php echo __( 'Left','bblm' ); ?></option>
      <option<?php selected( esc_attr( $align ), "right" ); ?> value="right"><?php echo __( 'Right','bblm' ); ?></option>
    </select></p>
    <p>
      <label for="<?php echo esc_attr( $this->get_field_id( 'numteam' ) ); ?>"><?php esc_attr_e( 'Number of Teams to show (When showing all time teams):', 'bblm' ); ?></label>
      <input
        class="widefat"
        id="<?php echo esc_attr( $this->get_field_id( 'numteam' ) ); ?>"
        name="<?php echo esc_attr( $this->get_field_name( 'numteam' ) ); ?>"
        type="text"
        value="<?php echo esc_attr( $numteam ); ?>">
    </p>
	<?php

 	}

  // Function to save any settings from he widget
  public function update( $new_instance, $old_instance ) {

    $instance = $old_instance;

    $instance = array();
    $instance['align'] = ( ! empty( $new_instance['align'] ) ) ? esc_attr( $new_instance['align'] ) : '';
    $instance['numteam'] = ( ! empty( $new_instance['numteam'] ) ) ? (int) strip_tags( $new_instance['numteam'] ) : '';

    return $instance;

  }

}

// Register the widget.
function bblm_register_widget_ftp() {
  register_widget( 'BBLM_Widget_FrontTeamPerformance' );
}
add_action( 'widgets_init', 'bblm_register_widget_ftp' );
