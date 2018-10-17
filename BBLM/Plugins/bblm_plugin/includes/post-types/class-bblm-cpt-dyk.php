<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Did You Know (DYK) CPT functions
 *
 * Defines the functions related to the DYK CPT (archive page logic, display functions etc)
 * For the Meta-Boxes, see the meta-boxes directory
 * For admin functions reloated to the CPT see the includes/admin/post-types directory
 *
 * @class 		BBLM_CPT_DYK
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/CPT
 * @version   1.0
 */

class BBLM_DYK {

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

	    if( is_post_type_archive( 'bblm_dyk' ) && !is_admin() && $query->is_main_query() ) {
	        $query->set( 'posts_per_page', -1 );
	    }

	  }

}

new BBLM_DYK();
