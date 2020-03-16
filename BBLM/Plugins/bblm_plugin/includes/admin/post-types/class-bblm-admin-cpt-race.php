<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Races CPT admin functions
 *
 * Defines the admin functions related to the Races (edit screens, custom messages, post saving etc)
 * For the Meta-Boxes, see the meta-boxes directory
 * For front end functions reloated to the CPT see the includes/post-types directory
 *
 * @class 		BBLM_Admin_CPT_Race
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/Admin/CPT
 * @version   1.0
 */

class BBLM_Admin_CPT_Race {

 /**
  * Constructor
 	*/
  public function __construct() {

    add_filter( 'manage_edit-bblm_race_columns', array( $this, 'my_edit_columns' ) );
    add_action( 'manage_bblm_race_posts_custom_column', array( $this, 'my_manage_columns' ), 10, 2 );
    add_action( 'pre_get_posts', array( $this, 'manage_archives' ) );

 	}


  /**
   * Sets the Column headers for the CPT edit list screen
   */
  function my_edit_columns( $columns ) {

  	$columns = array(
			'picture' => __( 'Logo', 'bblm' ),
  		'cb' => '<input type="checkbox" />',
  		'title' => __( 'Race Name', 'bblm' ),
  		'cost' => __( 'ReRoll Cost', 'bblm' ),
      'position' => __( 'Positions', 'bblm' ),
  	);

  	return $columns;

  }

  /**
   * Sets the Column content for the CPT edit list screen
   */
  function my_manage_columns( $column, $post_id ) {
  	global $post;

		switch( $column ) {

			// If displaying the 'picture' column.
      case 'picture' :

        the_post_thumbnail( 'bblm-fit-mini');

      break;

      // If displaying the 'cost' column.
      case 'cost' :

        $cost = get_post_meta( $post_id, 'race_rrcost', true );
        if ( empty( $cost ) ) {

  			     echo __( 'Not Set', 'bblm' );

        }
        else {

          echo number_format( $cost ) .' GP';

        }

      break;
      // If displaying the 'position' column.
      case 'position' :

				$cost = get_post_meta( $post_id, 'race_hide', true );
				if ( empty( $cost ) ) {

					echo '<a href="'.admin_url().'admin.php?page=bblm_positions&bblm_filter='.$post_id.'">Manage Positions</a>';

				}
				else {

					//This is the Stars team so positions are not applicable
					echo 'N/A';

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

	    if( is_post_type_archive( 'bblm_race' ) && is_admin() && $query->is_main_query() ) {
	        $query->set( 'posts_per_page', -1 );
	        $query->set( 'orderby', 'title' );
	        $query->set( 'order', 'asc' );
	    }

	  }

}

new BBLM_Admin_CPT_Race();
