<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Championship Seasons CPT functions
 *
 * Defines the functions related to the Seasons CPT (archive page logic, display functions etc)
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
          $query->set( 'orderby', 'title' );
          $query->set( 'order', 'desc' );
          $query->set( 'post_status', 'publish' );
	    }

	  } //end of manage_archives()

    /**
  	 * Determines if a Season is active
  	 *
  	 * @param int $ID the ID of the season (WP post ID)
  	 * @return bool true (Active), or False (Completed)
  	 */
  	 public static function is_season_active( $ID ) {

       //create a Unix timestamp from the end date of a season
       $enddate = get_post_meta( $ID, 'season_fdate', true );
       $seasonenddate = DateTime::createFromFormat('Y-m-d', $enddate );
       $seasonened = $seasonenddate->format('U');

       if ( ( $seasonened > time() ) || ( $enddate == '0000-00-00' ) ) {

         return true;

       }
       else {

         return false;

       }

     } //end of is_season_active()

	/**
	 * Returns the WOrdPress ID of the active Season
	 *
	 * @return int the WordPress ID of the current Season
	 */
	 public static function get_current_season() {

		 $current_date = DateTime::createFromFormat( 'Y-m-d', time() );

     $seasons = array(  'post_type' => 'bblm_season',
         'posts_per_page' => -1,
         'meta_key' => 'season_fdate',
         'orderby' => 'meta_value_num',
         'order' => 'ASC',
         'meta_query' => array(
             array(
                 'key' => 'season_fdate',
                 'value' => $current_date,
                 'compare' => '>=',
             )
         ),
     );
     if ( $latest_season = get_posts( $seasons ) ) {
       foreach ( $latest_season as $ls ) {

         $sea_id = $ls->ID;

       }

     }

		 return $sea_id;

	 } //end of get_current_season()


} //end of class

new BBLM_CPT_Season();
