<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Inducements CPT admin functions
 *
 * Defines the admin functions related to the Inducements CPT (edit screens, custom messages, post saving etc)
 * For the Meta-Boxes, see the meta-boxes directory
 * For front end functions reloated to the CPT see the includes/post-types directory
 *
 * @class 		BBLM_Admin_CPT_INDUCEMENT
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/Admin/CPT
 * @version   1.0
 */

class BBLM_Admin_CPT_INDUCEMENT {

 /**
  * Constructor
 	*/
  public function __construct() {

    add_action( 'pre_get_posts', array( $this, 'manage_archives' ) );
		add_filter( 'manage_edit-bblm_inducement_columns', array( $this, 'my_edit_inducement_columns' ) );
		add_action( 'manage_bblm_inducement_posts_custom_column', array( $this, 'my_manage_inducement_columns' ), 10, 2 );
		add_filter( 'views_edit-bblm_inducement', array( $this, 'post_type_desc' ) );

 	}

  /**
   * stops the CPT archive pages pagenating on the admin side and changes the display order
   *
   * @param wordpress $query
   * @return none
   */
  public function manage_archives( $query ) {
    if( is_post_type_archive( 'bblm_inducement' ) && is_admin() && $query->is_main_query() ) {
        $query->set( 'posts_per_page', -1 );
        $query->set( 'orderby', 'title' );
        $query->set( 'order', 'asc' );
    }
  }

	/**
   * Sets the Column headers for the CPT edit list screen
   */
  function my_edit_inducement_columns( $columns ) {

  	$columns = array(
  		'cb' => '<input type="checkbox" />',
  		'title' => __( 'Title', 'bblm' ),
  		'precord' => __( 'Attached to Player Record?', 'bblm' ),
  	);

  	return $columns;

  }

  /**
   * Sets the Column content for the CPT edit list screen
   */
  function my_manage_inducement_columns( $column, $post_id ) {
  	global $post;

    switch( $column ) {

      // If displaying the 'type' column.
      case 'precord' :

        $type = get_post_meta( $post_id, 'iduc_pos_set', true );
        if ( $type ) {

  			     echo __( 'Yes', 'bblm' );

        }
        else {

          echo __( 'No', 'bblm' );

        }

      break;

      // Break out of the switch statement for anything else.
      default :
      break;

    }

  }

	public function post_type_desc( $views ){

		$screen = get_current_screen();
		$post_type = get_post_type_object($screen->post_type);

		//if ('Inducements' == $post_type->name) {
			printf('<h4>%s</h4>', 'Currently this is an experimental feature of the site. Use with great caution!'); // echo
		//}

		return $views; // return original input unchanged
	}

}

new BBLM_Admin_CPT_INDUCEMENT();
