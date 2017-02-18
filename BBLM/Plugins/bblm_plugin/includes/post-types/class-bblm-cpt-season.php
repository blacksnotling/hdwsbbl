<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Season CPT functions
 *
 * Defines the functions related to the Season CPT (archive page logic, display functions etc)
 * For the Meta-Boxes, see the meta-boxes directory
 * For admin functions reloated to the CPT see the includes/admin/post-types directory
 *
 * @class 		BBLM_CPT_Season
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/CPT
 * @version   1.0
 */

class BBLM_CPT_Season {

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

	    if( is_post_type_archive( 'bblm_season' ) && !is_admin() && $query->is_main_query() ) {
	        $query->set( 'posts_per_page', -1 );
	    }

	  }

	/**
	 * returns the starus of a season
	 *
	 * @param wordpress $query
	 * @return strong
	 */
	 public function season_status() {
		 global $post;

		 $sdate = date("Y-m-d H:i:s", strtotime( get_post_meta( get_the_ID(), 'season_sdate', true ) ) );
		 $fdate = get_post_meta( get_the_ID(), 'season_fdate', true );
		 if ( '0000-00-00' == $fdate ) {

			 //If the end date is not set then set the time to now plus two days
			 $fdate = time()+(60*60*48);

		 }
		 else {

			//otherwise use the end date defined
			$fdate = date("Y-m-d H:i:s", strtotime( $fdate ) );

		 }

		 $today = date("Y-m-d H:i:s");
		 if ( ( $sdate <= $today ) && ( $fdate > $today ) ) {
			 //Season is active
			 $status = 'active';

		 }
		 elseif ( ( $sdate <= $today ) && ( $fdate <= $today ) ) {
		 	//Seson is complete
			$status = 'complete';

		 }
		 else {
		 	//Season is set for the future
			$status = 'future';

		 }

		 return $status;
	 }

}

new BBLM_CPT_Season();
