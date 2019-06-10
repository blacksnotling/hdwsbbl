<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Owners (Coaches) CPT admin functions
 *
 * Defines the admin functions related to the Owners CPT (edit screens, custom messages, post saving etc)
 * For the Meta-Boxes, see the meta-boxes directory
 * For front end functions reloated to the CPT see the includes/post-types directory
 *
 * @class 		BBLM_Admin_CPT_OWNER
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/Admin/CPT
 * @version   1.0
 */

class BBLM_Admin_CPT_OWNER {

 /**
  * Constructor
 	*/
  public function __construct() {

    add_filter( 'manage_edit-bblm_owner_columns', array( $this, 'my_edit_owner_columns' ) );
    add_action( 'manage_bblm_owner_posts_custom_column', array( $this, 'my_manage_owner_columns' ), 10, 2 );
    add_action( 'pre_get_posts', array( $this, 'manage_archives' ) );

 	}


  /**
   * Sets the Column headers for the CPT edit list screen
   */
  function my_edit_owner_columns( $columns ) {

  	$columns = array(
  		'cb' => '<input type="checkbox" />',
  		'title' => __( 'Owner / Coaches Name', 'bblm' ),
  		'teams' => __( '# Teams', 'bblm' ),
  	);

  	return $columns;

  }

  /**
   * Sets the Column content for the CPT edit list screen
   */
  function my_manage_owner_columns( $column, $post_id ) {
  	global $post;

    switch( $column ) {

      /* If displaying the 'teams' column. */
      case 'teams' :

        global $wpdb;
        $teamcontsql = 'SELECT COUNT(*) AS CONT FROM '.$wpdb->prefix.'team WHERE ID = '.$post_id;
        $teamcont = $wpdb->get_var( $teamcontsql );
        if ( $teamcont > 0) {

          echo $teamcont;

        }
        else {

          echo __( 'N/A', 'bblm' );

        }

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
    if( is_post_type_archive( 'bblm_owner' ) && is_admin() && $query->is_main_query() ) {
        $query->set( 'posts_per_page', -1 );
        $query->set( 'orderby', 'title' );
        $query->set( 'order', 'asc' );
    }
  }

}

new BBLM_Admin_CPT_OWNER();
