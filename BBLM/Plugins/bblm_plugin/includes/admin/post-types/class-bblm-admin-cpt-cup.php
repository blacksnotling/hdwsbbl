<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Championship Cups CPT admin functions
 *
 * Defines the admin functions related to the Championship Cups (edit screens, custom messages, post saving etc)
 * For the Meta-Boxes, see the meta-boxes directory
 * For front end functions reloated to the CPT see the includes/post-types directory
 *
 * @class 		BBLM_Admin_CPT_Cup
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/Admin/CPT
 * @version   1.0
 */

class BBLM_Admin_CPT_Cup {

 /**
  * Constructor
 	*/
  public function __construct() {

    add_filter( 'manage_edit-bblm_cup_columns', array( $this, 'my_edit_cup_columns' ) );
    add_action( 'manage_bblm_cup_posts_custom_column', array( $this, 'my_manage_cup_columns' ), 10, 2 );
    add_action( 'pre_get_posts', array( $this, 'manage_archives' ) );

 	}


  /**
   * Sets the Column headers for the CPT edit list screen
   */
  function my_edit_cup_columns( $columns ) {

  	$columns = array(
  		'cb' => '<input type="checkbox" />',
  		'title' => __( 'Title', 'bblm' ),
  		'type' => __( 'Type', 'bblm' ),
      'picture' => __( 'Icon', 'bblm' ),
  		'date' => __( 'Date', 'bblm' ),
  	);

  	return $columns;

  }

  /**
   * Sets the Column content for the CPT edit list screen
   */
  function my_manage_cup_columns( $column, $post_id ) {
  	global $post;

    switch( $column ) {

      // If displaying the 'type' column.
      case 'type' :

        $type = get_post_meta( $post_id, 'cup_type', true );
        if ( empty( $type ) ) {

  			     echo __( 'Unknown', 'bblm' );

        }
        else {

          echo $type;

        }

      break;
      // If displaying the 'picture' column.
      case 'picture' :

        the_post_thumbnail( 'bblm-fit-icon');

      break;

      // Break out of the switch statement for anything else.
      default :
      break;

    }

  }

  /**
	 * stops the CPT archive pages pagenating on the admin side and changes the display order
	 *
	 * @param wordpress $query
	 * @return none
	 */
	 public function manage_archives( $query ) {

	    if( is_post_type_archive( 'bblm_cup' ) && is_admin() && $query->is_main_query() ) {
	        $query->set( 'posts_per_page', -1 );
	        $query->set( 'orderby', 'title' );
	        $query->set( 'order', 'asc' );
	    }

	  }

}

new BBLM_Admin_CPT_Cup();
