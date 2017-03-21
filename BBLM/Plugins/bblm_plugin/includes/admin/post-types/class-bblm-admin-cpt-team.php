<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Teams CPT admin functions
 *
 * Defines the admin functions related to the Teams (edit screens, custom messages, post saving etc)
 * For the Meta-Boxes, see the meta-boxes directory
 * For front end functions reloated to the CPT see the includes/post-types directory
 *
 * @class 		BBLM_Admin_CPT_Team
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/Admin/CPT
 * @version   1.0
 */

class BBLM_Admin_CPT_Team {

 /**
  * Constructor
 	*/
  public function __construct() {

    add_filter( 'manage_edit-bblm_team_columns', array( $this, 'my_edit_team_columns' ) );
    add_action( 'manage_bblm_team_posts_custom_column', array( $this, 'my_manage_team_columns' ), 10, 2 );
    add_action( 'pre_get_posts', array( $this, 'manage_archives' ) );
		add_action( 'restrict_manage_posts', array( $this, 'table_filtering' ) );
		add_filter( 'parse_query', array( $this, 'admin_posts_filter' ) );

 	}


  /**
   * Sets the Column headers for the CPT edit list screen
   */
  function my_edit_team_columns( $columns ) {

  	$columns = array(
			'picture' => __( 'Logo', 'bblm' ),
			'cb' => '<input type="checkbox" />',
  		'title' => __( 'Team', 'bblm' ),
  		'race' => __( 'Race', 'bblm' ),
			'players' => __( 'Players', 'bblm' ),
  		'status' => __( 'Status', 'bblm' ),
  	);

  	return $columns;

  }

  /**
   * Sets the Column content for the CPT edit list screen
   */
  function my_manage_team_columns( $column, $post_id ) {
  	global $post;

    switch( $column ) {

			// If displaying the 'picture' column.
      case 'picture' :

        the_post_thumbnail( 'bblm-fit-icon');

      break;

      // If displaying the 'race' column.
      case 'race' :

        $type = get_post_meta( $post_id, 'team_race', true );
        if ( empty( $type ) ) {

  			     echo __( 'Unknown', 'bblm' );

        }
        else {

					echo get_the_title( $type );

        }

      break;

			// If displaying the 'players' column.
      case 'players' :

        echo __( 'Players', 'bblm' );

      break;

			// If displaying the 'status' column.
      case 'status' :

        $type = get_post_meta( $post_id, 'team_retired', true );
        if ( empty( $type ) ) {

  			     echo __( 'Active', 'bblm' );

        }
        else {

          echo __( 'Disbanded', 'bblm' );

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

	    if( is_post_type_archive( 'bblm_cup' ) && is_admin() && $query->is_main_query() ) {
	        $query->set( 'posts_per_page', 30 );
	        $query->set( 'orderby', 'title' );
	        $query->set( 'order', 'asc' );
	    }

	  }

		/**
		 * Adds a drop-down box to filter on items
		 */
			function table_filtering() {

				$cpt = 'bblm_team';

				$screen = get_current_screen();

				if( is_object( $screen ) && $cpt == $screen->post_type ){

					//Drop down field for Team Status
					echo '<select name="bblm_team_filter_status" id="bblm_team_filter_status">';
					echo '<option value ="">' . __( 'Filter by Status', 'textdomain' ) . '</option>';
					$value = 1;
					$name = "Disbanded Teams";
					$selected = ( !empty( $_GET['bblm_team_filter_status'] ) AND $_GET['bblm_team_filter_status'] == $value ) ? 'selected="selected"' : '';
					echo '<option value ="'.$value.'" '.$selected.'>' . $name . '</option>';
					$value = 2;
					$name = "Active Teams";
					$selected = ( !empty( $_GET['bblm_team_filter_status'] ) AND $_GET['bblm_team_filter_status'] == $value ) ? 'selected="selected"' : '';
					echo '<option value ="'.$value.'" '.$selected.'>' . $name . '</option>';
					echo '</select>';

					//generate the list of Races into a dropdown
					$seasonargs = array(
							'post_type' => 'bblm_race',
							'orderby' => 'title',
							'order'   => 'ASC',
							'posts_per_page'=> -1,
						);

						$query = new WP_Query( $seasonargs );

						if ( $query->have_posts() ) : ?>
						<select name="bblm_team_filter_race" id="bblm_team_filter_race">
							<option value ="">Filter by Race</option>
							<?php while ( $query->have_posts() ) : $query->the_post(); ?>
								<?php $value = get_the_ID(); ?>
								<?php $selected = ( !empty( $_GET['bblm_team_filter_race'] ) AND $_GET['bblm_team_filter_race'] == $value ) ? 'selected="selected"' : ''; ?>
								<option value="<?php the_ID(); ?>" <?php echo $selected; ?>><?php the_title(); ?></option>
							<?php endwhile; wp_reset_postdata();?>
						</select>
						<?php endif; ?>
		<?php

				}

			}

		/**
			* Filters the query based on the custom filter
			*/
			 function admin_posts_filter( $query ) {
				 if( is_admin() && $query->query['post_type'] == 'bblm_team' ) {

					 if ( isset( $_GET['bblm_team_filter_status'] ) ) {
						 //We are filtering by Status

					 	if ( 1 == $_GET['bblm_team_filter_status'] ) {

						 	//Show active comps
						 	$meta_query = array(
							 	array(
								 	'key'     => 'team_retired',
								 	'compare' => 'EXISTS',
							 	),
						 	);
						 	$query->set( 'meta_query', $meta_query );

					 	}
					 	elseif ( 2 == $_GET['bblm_team_filter_status'] ) {

						 	//Show active teams which don't have the meta key set)
						 	$meta_query = array(
							 	array(
								 	'key'     => 'team_retired',
								 	'compare' => 'NOT EXISTS',
							 	),
						 	);
						 	$query->set( 'meta_query', $meta_query );

						}

					}

					if ( isset( $_GET['bblm_team_filter_race'] ) && '' !== $_GET['bblm_team_filter_race'] ) {

						//we are filtering by Race
						$meta_query = array(
							array(
								'key'     => 'team_race',
								'value' => $_GET['bblm_team_filter_race'],
								'compare' => '=',
							),
						);
						$query->set( 'meta_query', $meta_query );

					}

				}

			}

}

new BBLM_Admin_CPT_Team();
