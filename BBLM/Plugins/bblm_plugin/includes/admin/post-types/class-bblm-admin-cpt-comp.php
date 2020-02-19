<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Competition CPT admin functions
 *
 * Defines the admin functions related to the Competition CPT (edit screens, custom messages, post saving etc)
 * For the Meta-Boxes, see the meta-boxes directory
 * For front end functions reloated to the CPT see the includes/post-types directory
 *
 * @class 		BBLM_Admin_CPT_Competition
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/Admin/CPT
 * @version   1.0
 */

class BBLM_Admin_CPT_Competition {

 /**
  * Constructor
 	*/
  public function __construct() {

    add_filter( 'manage_edit-bblm_comp_columns', array( $this, 'my_edit_competition_columns' ) );
    add_action( 'manage_bblm_comp_posts_custom_column', array( $this, 'my_manage_competition_columns' ), 10, 2 );

 	}

  /**
   * Sets the Column headers for the CPT edit list screen
   */
  function my_edit_competition_columns( $columns ) {

  	$columns = array(
  		'cb' => '<input type="checkbox" />',
  		'title' => __( 'Season', 'bblm' ),
  		'season' => __( 'Season', 'bblm' ),
			'cup' => __( 'Championship', 'bblm' ),
  		'sdate' => __( 'Started', 'bblm' ),
			'fdate' => __( 'Ended', 'bblm' ),
  	);

  	return $columns;

  }

  /**
   * Sets the Column content for the CPT edit list screen
   */
  function my_manage_competition_columns( $column, $post_id ) {
  	global $post;

    switch( $column ) {

      /* If displaying the 'season' column. */
      case 'season' :

        echo 'Season';

      break;

			/* If displaying the 'cup' column. */
			case 'cup' :

				echo 'Champiosnip';

			break;

      // If displaying the 'sdate' column.
      case 'sdate' :

        $type = get_post_meta( $post_id, 'comp_sdate', true );
        if ( '0000-00-00' == $type ) {

  			     echo __( 'TBC', 'bblm' );

        }
        else {

          echo date("d-m-Y (25y)", strtotime( $type ) );

        }

      break;

			// If displaying the 'fdate' column.
      case 'fdate' :

        //$bblm_season = new BBLM_CPT_Season;

        if ( BBLM_CPT_Comp::is_competition_active( $post_id ) ) {

  			     echo __( 'In Progress', 'bblm' );

        }
        else {

          echo date("d-m-Y (25y)", strtotime( get_post_meta( $post_id, 'comp_fdate', true ) ) );

        }

      break;

      // Break out of the switch statement for anything else.
      default :
      break;

    }

  }

}

new BBLM_Admin_CPT_Competition();
