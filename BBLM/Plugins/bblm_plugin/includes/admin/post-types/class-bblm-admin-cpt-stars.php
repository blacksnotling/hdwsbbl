<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Star Players CPT admin functions
 *
 * Defines the admin functions related to the Stars CPT (edit screens, custom messages, post saving etc)
 * For the Meta-Boxes, see the meta-boxes directory
 * For front end functions reloated to the CPT see the includes/post-types directory
 *
 * @class 		BBLM_Admin_CPT_Stars
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/Admin/CPT
 * @version   1.0
 */

class BBLM_Admin_CPT_Stars {

 /**
  * Constructor
  */
  public function __construct() {

		add_filter( 'admin_url', array( $this, 'redirect_add_star_link' ), 10, 2 );

    return TRUE;

  }

  /**
   * Redirects the "Add New Stars" link to the custom add stars admin page
   *
   * @param string $url the url I wish to send them to
	 * @param string $path where I am sending them
   * @return string url the url of the custom admin page I wish ro redirect the user to
   */
   public function redirect_add_star_link( $url, $path ) {

		 if( $path === 'post-new.php?post_type=bblm_star' ) {
			 $url = get_bloginfo( 'url' ) . '/wp-admin/admin.php?page=bblm_addstars';
		 }
		 return $url;

   }//end of redirect_add_star_link()

}//end of class

new BBLM_Admin_CPT_Stars();
