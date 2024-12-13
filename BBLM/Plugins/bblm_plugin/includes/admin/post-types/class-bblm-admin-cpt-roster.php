<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Roster CPT admin functions
 *
 * Defines the admin functions related to the Roster CPT (edit screens, custom messages, post saving etc)
 * For the Meta-Boxes, see the meta-boxes directory
 * For front end functions reloated to the CPT see the includes/post-types directory
 *
 * @class 		BBLM_Admin_CPT_Roster
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/Admin/CPT
 * @version   1.0
 */

class BBLM_Admin_CPT_Roster {

 /**
  * Constructor
  */
  public function __construct() {

		add_action( 'pre_get_posts', array( $this, 'manage_archives' ) );
		add_filter( 'admin_url', array( $this, 'redirect_add_roster_link' ), 10, 2 );

		return TRUE;

  }

	/**
	 * Redirects the "Add New Roster" link to the custom add teams admin page
	 *
	 * @param string $url the url I wish to send them to
	 * @param string $path where I am sending them
	 * @return string url the url of the custom admin page I wish ro redirect the user to
	 */
	 public function redirect_add_roster_link( $url, $path ) {

		 if( $path === 'post-new.php?post_type=bblm_roster' ) {
			 $url = get_bloginfo( 'url' ) . '/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.add.team.php';
		 }
		 return $url;

	 }//end of redirect_add_team_link()


	 /**
	 * stops the CPT archive pages pagenating on the admin side and changes the display order
	 *
	 * @param wordpress $query
	 * @return none
	 */
	 public function manage_archives( $query ) {

			if( is_post_type_archive( 'bblm_roster' ) && is_admin() && $query->is_main_query() ) {
					$query->set( 'posts_per_page', 200 );
					$query->set( 'orderby', 'title' );
					$query->set( 'order', 'asc' );
			}

		}

}//end of class

new BBLM_Admin_CPT_Roster();
