<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Season CPT admin functions
 *
 * Defines the admin functions related to the Season CPT (edit screens, custom messages, post saving etc)
 * For the Meta-Boxes, see the meta-boxes directory
 * For front end functions reloated to the CPT see the includes/post-types directory
 *
 * @class 		BBLM_Admin_CPT_Season
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/Admin/CPT
 * @version   1.0
 */

class BBLM_Admin_CPT_Season {

 /**
  * Constructor
 	*/
  public function __construct() {

    add_filter( 'manage_edit-bblm_season_columns', array( $this, 'my_edit_season_columns' ) );
    add_action( 'manage_bblm_season_posts_custom_column', array( $this, 'my_manage_season_columns' ), 10, 2 );

 	}


  /**
   * Sets the Column headers for the CPT edit list screen
   */
  function my_edit_season_columns( $columns ) {

  	$columns = array(
  		'cb' => '<input type="checkbox" />',
  		'title' => __( 'Season', 'bblm' ),
  		'competition' => __( 'Competitions', 'bblm' ),
  		'sdate' => __( 'Started', 'bblm' ),
			'fdate' => __( 'Ended', 'bblm' ),
  	);

  	return $columns;

  }

  /**
   * Sets the Column content for the CPT edit list screen
   */
  function my_manage_season_columns( $column, $post_id ) {
  	global $post;

    switch( $column ) {

      /* If displaying the 'competition' column. */
      case 'competition' :

        echo 'View Comps';

      break;

      // If displaying the 'sdate' column.
      case 'sdate' :

        $type = get_post_meta( $post_id, 'season_sdate', true );
        if ( '0000-00-00' == $type ) {

  			     echo __( 'TBC', 'bblm' );

        }
        else {

          echo date("d-m-Y (25y)", strtotime( $type ) );

        }

      break;

			// If displaying the 'fdate' column.
      case 'fdate' :

        $type = get_post_meta( $post_id, 'season_fdate', true );
        if ( '0000-00-00' == $type ) {

  			     echo __( 'End Season', 'bblm' );

        }
        else {

          echo date("d-m-Y (25y)", strtotime( $type ) );

        }

      break;

      // Break out of the switch statement for anything else.
      default :
      break;

    }

  }

}

new BBLM_Admin_CPT_Season();
