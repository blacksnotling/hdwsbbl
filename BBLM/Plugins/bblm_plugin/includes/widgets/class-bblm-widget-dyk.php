<?php
/**
 * Did You Know Widget
 *
 * A Widget that displays a random 'did You Know' CPT
 *
 * @class 		BBLM_Widget_DYK
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/Widget
 * @version   1.0
 */

class BBLM_Widget_DYK extends WP_Widget {

  function __construct() {

    $widget_ops = array('classname' => 'widget_bblm widget_dyk', 'description' => __( 'Display a single Did You Know entry', 'bblm' ) );
    parent::__construct('bblm_dyk', __( 'Did You Know', 'bblm' ), $widget_ops);

  }

  //The Widget Output in the front-end
  public function widget( $args, $instance ) {

    extract( $args );
		echo $before_widget;
		$dykcontent = new WP_Query( array( 'post_type' => 'bblm_dyk', 'post_status' => 'publish', 'orderby' => 'rand', 'showposts' => 1 ) );
		if ( $dykcontent->have_posts() ) : while ( $dykcontent->have_posts() ) : $dykcontent->the_post();
		  $type = ucfirst( get_post_meta( get_the_ID(), 'dyk_type', true ) );
?>
      <div class="dykcontainer dyk<?php echo strtolower( $type ); ?>" id="dyk<?php echo the_ID(); ?>">
        <h3 class="dykheader"><?php echo bblm_get_league_name(); ?> - <?php if( "Trivia" == $type ) { print("Did You Know"); } else { print("Fact"); } ?></h3>
<?php

      if ( strlen( get_the_title() ) !== 0 ) {

        echo '<h4>';
        the_title();
        echo '</h4>';

      }
  		the_content();
?>
        <p><?php edit_post_link( __( 'Edit', 'bblm' ), ' <strong>[</strong> ', ' <strong>]</strong> '); ?></p>
        <p class="dykfooter"><a href="<?php echo get_post_type_archive_link( 'bblm_dyk' ); ?>">View more Did You Knows</a></p>
      </div>
<?php
  	endwhile; endif;
  	echo $after_widget;

  }

  // The Widget output on the admin screen
  public function form( $instance ) {

    echo '<p>Displays a single, random Did You Know</p>';

 	}

  // Function to save any settings from he widget
  public function update( $new_instance, $old_instance ) {

    $instance = $old_instance;
    $instance[ 'title' ] = strip_tags( $new_instance[ 'title' ] );
    return $instance;

  }

}

// Register the widget.
function bblm_register_widget_dyk() {
  register_widget( 'BBLM_Widget_DYK' );
}
add_action( 'widgets_init', 'bblm_register_widget_dyk' );
