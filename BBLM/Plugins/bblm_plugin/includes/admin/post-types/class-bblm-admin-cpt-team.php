<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Team CPT admin functions
 *
 * Defines the admin functions related to the Team CPT (edit screens, custom messages, post saving etc)
 * For the Meta-Boxes, see the meta-boxes directory
 * For front end functions reloated to the CPT see the includes/post-types directory
 *
 * @class 		BBLM_Admin_CPT_Team
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/Admin/CPT
 * @version   1.1
 */

class BBLM_Admin_CPT_Team {

 /**
  * Constructor
  */
  public function __construct() {

		add_filter( 'manage_edit-bblm_team_columns', array( $this, 'my_edit_columns' ) );
		add_action( 'manage_bblm_team_posts_custom_column', array( $this, 'my_manage_columns' ), 10, 2 );
		add_action( 'pre_get_posts', array( $this, 'manage_archives' ) );
		add_filter( 'admin_url', array( $this, 'redirect_add_team_link' ), 10, 2 );

		return TRUE;

  }

	/**
	 * Redirects the "Add New Team" link to the custom add teams admin page
	 *
	 * @param string $url the url I wish to send them to
	 * @param string $path where I am sending them
	 * @return string url the url of the custom admin page I wish ro redirect the user to
	 */
	 public function redirect_add_team_link( $url, $path ) {

		 if( $path === 'post-new.php?post_type=bblm_team' ) {
			 $url = get_bloginfo( 'url' ) . '/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.add.team.php';
		 }
		 return $url;

	 }//end of redirect_add_team_link()

	 /**
		* Sets the Column headers for the CPT edit list screen
		*/
	 function my_edit_columns( $columns ) {

		$columns = array(
			'cb' => '<input type="checkbox" />',
			'title' => __( 'Team Name', 'bblm' ),
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

				$pstatus = get_post_meta( $post_id, 'team_status', true );
				if ( (int) $pstatus ) {

					echo __( 'Active', 'bblm' );
				}

				else {

					echo __( 'Inactive', 'bblm' );

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

			if( is_post_type_archive( 'bblm_team' ) && is_admin() && $query->is_main_query() ) {
					$query->set( 'posts_per_page', 200 );
					$query->set( 'orderby', 'title' );
					$query->set( 'order', 'asc' );
			}

		}

 /**
  * Resets all the injured players on a team (MNG)
  * Can be used after a match to clear those who missed a game, or at the start of a season
  *
  * @param int $ID the ID of the team
  * @return bool true (successfull), or False (Failure)
  */
  public static function reset_team_mng( $ID ) {
    global $wpdb;

    //selects the injured players on the team
    $selectinjplayer = 'SELECT WPID AS PWPID FROM '.$wpdb->prefix.'player WHERE p_mng = 1 AND t_id = ' . (int) $ID;

    if ( $injplayer = $wpdb->get_results( $selectinjplayer ) ) {
      foreach ( $injplayer as $ip ) {
        BBLM_Admin_CPT_Player::reset_player_mng( $ip->PWPID );
      }
    }
    return TRUE;

  }//end of reset_team_injuries()

}//end of class

new BBLM_Admin_CPT_Team();
