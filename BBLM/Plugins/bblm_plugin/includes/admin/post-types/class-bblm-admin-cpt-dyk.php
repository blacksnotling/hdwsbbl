<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Did You Know (DYK) CPT admin functions
 *
 * Defines the admin functions related to the DYK CPT (edit screens, custom messages, post saving etc)
 * For the Meta-Boxes, see the meta-boxes directory
 * For front end functions reloated to the CPT see the includes/post-types directory
 *
 * @class 		BBLM_Admin_CPT_DYK
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/Admin/CPT
 * @version   1.0
 */

class BBLM_Admin_CPT_DYK {

 /**
  * Constructor
 	*/
  public function __construct() {

    add_filter( 'manage_edit-bblm_dyk_columns', array( $this, 'my_edit_dyk_columns' ) );
    add_action( 'manage_bblm_dyk_posts_custom_column', array( $this, 'my_manage_dyk_columns' ), 10, 2 );

 	}


  /**
   * Sets the Column headers for the CPT edit list screen
   */
  function my_edit_dyk_columns( $columns ) {

  	$columns = array(
  		'cb' => '<input type="checkbox" />',
  		'title' => __( 'Title', 'bblm' ),
  		'preview' => __( 'Preview', 'bblm' ),
  		'type' => __( 'Type', 'bblm' ),
  		'date' => __( 'Date', 'bblm' ),
  	);

  	return $columns;

  }

  /**
   * Sets the Column content for the CPT edit list screen
   */
  function my_manage_dyk_columns( $column, $post_id ) {
  	global $post;

    switch( $column ) {

      /* If displaying the 'preview' column. */
      case 'preview' :

        echo wp_trim_words( $post->post_content , 30 );

      break;

      // If displaying the 'type' column.
      case 'type' :

        $type = get_post_meta( $post_id, 'dyk_type', true );
        if ( empty( $type ) ) {

  			     echo __( 'Unknown', 'bblm' );

        }
        else {

          echo $type;

        }

      break;

      // Break out of the switch statement for anything else.
      default :
      break;

    }

  }

}

new BBLM_Admin_CPT_DYK();
