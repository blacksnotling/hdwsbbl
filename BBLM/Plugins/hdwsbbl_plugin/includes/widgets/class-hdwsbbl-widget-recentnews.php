<?php
/**
 * Recent News Widget
 *
 * A Widget that displays recent news, minus a category that an admin ca select (Warzone)
 *
 * @class 		HDWSBBL_Widget_RecentNews
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	HDWSBBL/Widgets
 * @version   1.0
 */

class HWSBBL_Widget_RecentNews extends WP_Widget {

  function __construct() {

    $widget_ops = array('classname' => 'widget_hdwsbbl widget_hdwsbbl_recentnews', 'description' => __( 'Displays the recent news posts, minus any categories selected', 'hdwsbbl' ) );
    parent::__construct('hdwsbbl_recentnews', __( 'HDWSBBL: Recent News', 'hdwsbbl' ), $widget_ops);

  }

  //The Widget Output in the front-end
  public function widget( $args, $instance ) {
    global $wpdb;

    echo $args['before_widget'];

    //If a title was provided then display the title
    if ( ! empty( $instance['title'] ) ) {
      echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
    }
    //Grabs the last 6 entries from the Warzone and Displays them.
          $recentposts = array(
            'post_type' => 'post',
            'posts_per_page' => 6,
            'category__not_in' => array( $instance['cathide'] ),
            'post__not_in' => get_option( 'sticky_posts' )
          );
          // The Query
          $the_query = new WP_Query( $recentposts );

          // The Loop
          if ( $the_query->have_posts() ) {
            print("<ul>\n");
            while ( $the_query->have_posts() ) {
              $the_query->the_post();
    ?>
    <li><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></li>

      <?php
            } //end of while
            print("</ul>\n");
          } //end of have posts


    echo $args['after_widget'];

  }

  // The Widget output on the admin screen
  public function form( $instance ) {

    echo '<p>'.__( 'Displays a summary of recent News (minus set categories)', 'hdwsbbl' ).'</p>';
    //if no data has been provided then this populates some default values
    $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Recent News', 'hdwsbbl' );

    $defaults = array( 'title' => __($title, 'title'), 'cathide' => '');
    $instance = wp_parse_args( (array) $instance, $defaults );
	?>
    <p>
      <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'hdwsbbl' ); ?></label>
      <input
        class="widefat"
        id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
        name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
        type="text"
        value="<?php echo esc_attr( $title ); ?>">
    </p>

    <p>
      <select id="<?php echo $this->get_field_id('cathide'); ?>" name="<?php echo $this->get_field_name('cathide'); ?>" class="widefat" style="width:100%;">
        <?php foreach(get_terms('category','parent=0&hide_empty=0') as $term) { ?>
        <option <?php selected( $instance['cathide'], $term->term_id ); ?> value="<?php echo $term->term_id; ?>"><?php echo $term->name; ?></option>
              <?php } ?>
      </select>
    </p>
	<?php

 	}

  // Function to save any settings from he widget
  public function update( $new_instance, $old_instance ) {

    $instance = $old_instance;

    $instance = array();
  	$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
    $instance['cathide'] = ( ! empty( $new_instance['cathide'] ) ) ? (int) strip_tags( $new_instance['cathide'] ) : '';

    return $instance;

  }

}

// Register the widget.
function hdwsbbl_register_widget_rnews() {
  register_widget( 'HWSBBL_Widget_RecentNews' );
}
add_action( 'widgets_init', 'hdwsbbl_register_widget_rnews' );
