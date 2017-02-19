<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Race CPT functions
 *
 * Defines the functions related to the Race CPT (archive page logic, display functions etc)
 * For the Meta-Boxes, see the meta-boxes directory
 * For admin functions reloated to the CPT see the includes/admin/post-types directory
 *
 * @class 		BBLM_CPT_Race
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/CPT
 * @version   1.0
 */

class BBLM_CPT_Race {

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

	    if( is_post_type_archive( 'bblm_race' ) && !is_admin() && $query->is_main_query() ) {
	        $query->set( 'posts_per_page', -1 );
	        $query->set( 'orderby', 'title' );
	        $query->set( 'order', 'asc' );

					$meta_query = array(
						array(
							'key'     => 'race_hide',
		          'compare' => 'NOT EXISTS',
						),
					);
		      $query->set( 'meta_query', $meta_query );

	    }

	  }


}

new BBLM_CPT_Race();
