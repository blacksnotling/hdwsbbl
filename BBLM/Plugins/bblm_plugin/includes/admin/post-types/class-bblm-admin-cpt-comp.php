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
 * @class 		BBLM_Admin_CPT_Comp
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/Admin/CPT
 * @version   1.0
 */

class BBLM_Admin_CPT_Comp {

 /**
  * Constructor
 	*/
  public function __construct() {

    add_filter( 'manage_edit-bblm_comp_columns', array( $this, 'my_edit_comp_columns' ) );
    add_action( 'manage_bblm_comp_posts_custom_column', array( $this, 'my_manage_comp_columns' ), 10, 2 );
		add_action( 'restrict_manage_posts', array( $this, 'table_filtering' ) );
		add_filter( 'parse_query', array( $this, 'admin_posts_filter' ) );

 	}


  /**
   * Sets the Column headers for the CPT edit list screen
   */
  function my_edit_comp_columns( $columns ) {

  	$columns = array(
  		'cb' => '<input type="checkbox" />',
  		'title' => __( 'Competition', 'bblm' ),
  		'season' => __( 'Season', 'bblm' ),
			'series' => __( 'Cup', 'bblm' ),
			'matches' => __( 'Matches', 'bblm' ),
  		'cdate' => __( 'Status', 'bblm' ),
			'teams' => __( 'Teams', 'bblm' ),
  	);

  	return $columns;

  }

  /**
   * Sets the Column content for the CPT edit list screen
   */
  function my_manage_comp_columns( $column, $post_id ) {
  	global $post;
		global $wpdb;

		$sql = "SELECT UNIX_TIMESTAMP(c_sdate) AS sdate, UNIX_TIMESTAMP(c_edate) AS edate, c_active, sea_id, series_id FROM ".$wpdb->prefix."comp WHERE ID = ".$post->ID;
		$comp = $wpdb->get_row( $sql );

    switch( $column ) {

			/* If displaying the 'Season' column. */
			case 'season' :

				echo get_the_title( $comp->sea_id );

			break;

			/* If displaying the 'series' column. */
			case 'series' :

				echo get_the_title( $comp->series_id );

			break;

      /* If displaying the 'competition' column. */
      case 'matches' :

        echo 'View Matches';

      break;

      // If displaying the 'date' column.
      case 'cdate' :

				$sdate = date("Y-m-d H:i:s", $comp->sdate );
				$today = date("Y-m-d H:i:s");

				if ( ! $comp->c_active ) {

					//Competition is over
					echo __( 'Complete', 'bblm' );
				}
				elseif ( $sdate > $today ) {

					//Competition is scheduled for the future
					echo __( 'Upcoming', 'bblm' );

				}
				else {

					//Competition is in progress
					echo __( 'Close Season', 'bblm' );

				}

			break;

			/* If displaying the 'teams' column. */
      case 'teams' :

        echo 'Manage teams';

      break;

      // Break out of the switch statement for anything else.
      default :
      break;

    }

  }

/**
 * Adds a drop-down box to filter on items
 */
	function table_filtering() {

		$cpt = 'bblm_comp';

		$screen = get_current_screen();

		if( is_object( $screen ) && $cpt == $screen->post_type ){

			//Drop down field for COmpetition Status
			echo '<select name="bblm_comp_filter_status" id="bblm_comp_filter_status">';
			echo '<option value ="">' . __( 'Filter by Status', 'textdomain' ) . '</option>';
			$value = 1;
			$name = "Completed Competitions";
			$selected = ( !empty( $_GET['bblm_comp_filter_status'] ) AND $_GET['bblm_comp_filter_status'] == $value ) ? 'selected="selected"' : '';
			echo '<option value ="'.$value.'" '.$selected.'>' . $name . '</option>';
			$value = 2;
			$name = "Active and Upcoming Competitions";
			$selected = ( !empty( $_GET['bblm_comp_filter_status'] ) AND $_GET['bblm_comp_filter_status'] == $value ) ? 'selected="selected"' : '';
			echo '<option value ="'.$value.'" '.$selected.'>' . $name . '</option>';
			echo '</select>';

			//generate the list of Seasons into a dropdown
			$seasonargs = array(
					'post_type' => 'bblm_season',
					'orderby' => 'title',
					'order'   => 'DESC',
					'posts_per_page'=> -1,
				);

				$query = new WP_Query( $seasonargs );

				if ( $query->have_posts() ) : ?>
				<select name="bblm_comp_filter_season" id="bblm_comp_filter_season">
					<option value ="">Filter by Seasons</option>
					<?php while ( $query->have_posts() ) : $query->the_post(); ?>
						<?php $value = get_the_ID(); ?>
						<?php $selected = ( !empty( $_GET['bblm_comp_filter_season'] ) AND $_GET['bblm_comp_filter_season'] == $value ) ? 'selected="selected"' : ''; ?>
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
		 if( is_admin() && $query->query['post_type'] == 'bblm_comp' ) {

			 if ( isset( $_GET['bblm_comp_filter_status'] ) ) {
				 //We are filtering by Status

			 	if ( 1 == $_GET['bblm_comp_filter_status'] ) {

				 	//Show active comps
				 	$meta_query = array(
					 	array(
						 	'key'     => 'comp_complete',
						 	'compare' => 'EXISTS',
					 	),
				 	);
				 	$query->set( 'meta_query', $meta_query );

			 	}
			 	elseif ( 2 == $_GET['bblm_comp_filter_status'] ) {

				 	//SHow active and upcoming *which don't have the meta key set)
				 	$meta_query = array(
					 	array(
						 	'key'     => 'comp_complete',
						 	'compare' => 'NOT EXISTS',
					 	),
				 	);
				 	$query->set( 'meta_query', $meta_query );

				}

			}

			if ( isset( $_GET['bblm_comp_filter_season'] ) && '' !== $_GET['bblm_comp_filter_season'] ) {

				//we are filtering by Season
				$meta_query = array(
					array(
						'key'     => 'comp_season',
						'value' => $_GET['bblm_comp_filter_season'],
						'compare' => '=',
					),
				);
				$query->set( 'meta_query', $meta_query );

			}

		}

	}

}

new BBLM_Admin_CPT_Comp();
