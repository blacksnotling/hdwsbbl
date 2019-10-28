<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Stadiums CPT functions
 *
 * Defines the functions related to the Stadium CPT (archive page logic, display functions etc)
 * For admin functions reloated to the CPT see the includes/admin/post-types directory
 *
 * @class 		BBLM_CPT_Stadium
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/CPT
 * @version   1.0.1
 */

class BBLM_CPT_Stadium {

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

	    if( is_post_type_archive( 'bblm_stadium' ) && !is_admin() && $query->is_main_query() ) {
	        $query->set( 'posts_per_page', -1 );
          $query->set( 'orderby', 'title' );
          $query->set( 'order', 'asc' );
          $query->set( 'post_status', 'publish' );
	    }

	  }

/**
* Dsiplays the team(s) that call this stadium home, or a message that nobody uses it
*
* @param wordpress $query
* @return string
*/

 public function display_home_teams () {
   global $post;
   global $wpdb;

   $hometeamsql = 'SELECT T.WPID FROM '.$wpdb->prefix.'team T WHERE T.t_show = 1 AND T.stad_id = '.get_the_ID();

   if ( $hometeam = $wpdb->get_results( $hometeamsql ) ) {

     //Check to see how many teams are returned
     if ( 1 < count( $hometeam ) ) {

       //we have more than one team
       echo '<p>' . __( 'At present the following teams call this stadium their home.', 'bblm' ) . '</p>';
       echo '<ul>';

       foreach ( $hometeam as $ht ) {

         echo '<li>' . bblm_get_team_link( $ht->WPID ) . '</li>';

       }

       echo '</ul>';

     }

     else {

       //only one team is retuned
       foreach ( $hometeam as $ht ) {

         echo '<p>' . __( 'At present only ', 'bblm' ) . bblm_get_team_link( $ht->WPID ) . __( ' call this stadium their home.', 'bblm' ) . '</p>';

       }

     }

   }
   else {

     echo '<div class="bblm_info"><p>' . __( 'At present no teams use this stadium for their home games.', 'bblm' ) . '</p></div>';

   }

  } //end of display_home_teams

} //end of class

new BBLM_CPT_Stadium();
