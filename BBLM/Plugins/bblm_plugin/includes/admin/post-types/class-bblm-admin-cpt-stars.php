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

		add_filter( 'manage_edit-bblm_star_columns', array( $this, 'my_edit_columns' ) );
    add_action( 'manage_bblm_star_posts_custom_column', array( $this, 'my_manage_columns' ), 10, 2 );
    add_action( 'pre_get_posts', array( $this, 'manage_archives' ) );
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


	   /**
	    * Sets the Column headers for the CPT edit list screen
	    */
	   function my_edit_columns( $columns ) {

	   	$columns = array(
	   		'cb' => '<input type="checkbox" />',
	   		'title' => __( 'Star Player Name', 'bblm' ),
				'racefor' => __( '# Races plays for', 'bblm' ),
	 			'status' => __( 'Status', 'bblm' ),
	   	);

	   	return $columns;

	   }

	   /**
	    * Sets the Column content for the CPT edit list screen
	    */
	   function my_manage_columns( $column, $post_id ) {
	   	global $post;

	 		switch( $column ) {

	 			// If displaying the 'status' column.
	 			case 'status' :

	 				$sstatus = get_post_meta( $post_id, 'star_status', true );
	 				if ( (int) $sstatus ) {

	 					echo __( 'Available', 'bblm' );
	 				}

	 				else {

	 					echo __( 'Legacy / Retired', 'bblm' );

	 				}

	 			break;

				// if displaying the racefor column
				case 'racefor' :

				$terms = wp_get_post_terms( $post_id, 'race_rules' );
				echo count( $terms );

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

	 	    if( is_post_type_archive( 'bblm_star' ) && is_admin() && $query->is_main_query() ) {
	 	        $query->set( 'posts_per_page', -1 );
	 	        $query->set( 'orderby', 'title' );
	 	        $query->set( 'order', 'asc' );
	 	    }

	 	  }


}//end of class

new BBLM_Admin_CPT_Stars();
