<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Championship Cups CPT functions
 *
 * Defines the functions related to the Championships CPT (archive page logic, display functions etc)
 * For the Meta-Boxes, see the meta-boxes directory
 * For admin functions reloated to the CPT see the includes/admin/post-types directory
 *
 * @class 		BBLM_CPT_Cup
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/CPT
 * @version   1.0
 */

class BBLM_CPT_Cup {

	/**
	 * Constructor
	 */
	public function __construct() {

    add_action( 'pre_get_posts', array( $this, 'manage_archives' ) );

	}

	/**
	 * stops the CPT archive pages pagenating
	 *
	 * @param wordpress $query
	 * @return none
	 */
	 public function manage_archives( $query ) {

	    if( is_post_type_archive( 'bblm_cup' ) && !is_admin() && $query->is_main_query() ) {
	        $query->set( 'posts_per_page', -1 );
	        $query->set( 'orderby', 'title' );
	        $query->set( 'order', 'asc' );

					$meta_query = array(
						array(
							'key'     => 'cup_type',
		          'value'   => array('Major', 'Minor'),
		          'compare' => 'IN',
						),
					);
		      $query->set( 'meta_query', $meta_query      );
		      $query->set( 'meta_key',   'cup_type'           );

	    }

	  }


}

new BBLM_CPT_Cup();
