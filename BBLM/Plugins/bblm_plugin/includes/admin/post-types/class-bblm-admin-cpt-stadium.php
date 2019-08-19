<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Stadiums CPT admin functions
 *
 * Defines the admin functions related to the Stadiums CPT (edit screens, custom messages, post saving etc)
 * For the Meta-Boxes, see the meta-boxes directory
 * For front end functions reloated to the CPT see the includes/post-types directory
 *
 * @class 		BBLM_Admin_CPT_STADIUM
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/Admin/CPT
 * @version   1.0.1
 */

class BBLM_Admin_CPT_STADIUM {

 /**
  * Constructor
 	*/
  public function __construct() {

    add_action( 'pre_get_posts', array( $this, 'manage_archives' ) );

 	}

  /**
   * stops the CPT archive pages pagenating on the admin side and changes the display order
   *
   * @param wordpress $query
   * @return none
   */
  public function manage_archives( $query ) {
    if( is_post_type_archive( 'bblm_stadium' ) && is_admin() && $query->is_main_query() ) {
        $query->set( 'posts_per_page', -1 );
        $query->set( 'orderby', 'title' );
        $query->set( 'order', 'asc' );
    }
  }

}

new BBLM_Admin_CPT_STADIUM();
