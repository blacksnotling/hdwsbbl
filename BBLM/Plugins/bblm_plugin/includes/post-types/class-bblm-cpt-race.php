<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Races CPT functions
 *
 * Defines the functions related to the Races CPT (archive page logic, display functions etc)
 * For admin functions reloated to the CPT see the includes/admin/post-types directory
 *
 * @class 		BBLM_CPT_Race
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/CPT
 * @version   1.3
 */

class BBLM_CPT_Race {

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

	    if( is_post_type_archive( 'bblm_race' ) && !is_admin() && $query->is_main_query() ) {
	        $query->set( 'posts_per_page', -1 );
          $query->set( 'orderby', 'title' );
          $query->set( 'order', 'asc' );
          $query->set( 'post_status', 'publish' );
	    }

	  }

 /**
  * Returns a list of all the Races, and the number of Teams
  * that have represented them
  *
  * @param wordpress $query
  * @return none
  */
  public static function get_race_listing() {

    //Grabs a list of 'posts' from the bblm_cup CPT
    $cpostsarg = array(
      'post_type' => 'bblm_race',
      'numberposts' => -1,
			'meta_key' => 'race_rstatus',
      'orderby' => array(
				'meta_value' => 'DESC',
				'post_title' => 'ASC',
			),
			'meta_query' => array(
				array(
					'key'     => 'race_hide',
					'compare' => 'NOT EXISTS',
				),
			),
    );
    if ( $cposts = get_posts( $cpostsarg ) ) {
      $zebracount = 1;
      $race = new BBLM_CPT_Race;
			$rstatus = 0;
			$is_first = 1;
			$current_status = 0;

      foreach( $cposts as $c ) {

				if ($c->race_rstatus !== $rstatus) {
					$rstatus = $c->race_rstatus;
					if (1 !== $is_first) {
						echo '</tbody>';
						echo '</table>';
						$zebracount = 1;
					}
					$is_first = 1;
				}

				if (1 == $rstatus) {
					$status_title = "Available Races";
				}
				else {
					$status_title = "Inactive Races";
				}


				if ( $is_first  ) {
?>
					<h3 class="bblm-table-caption"><?php echo $status_title; ?></h3>
					<table class="bblm_table">
						<thead>
							<tr>
								<th><?php echo __( 'Icon', 'bblm' ); ?></th>
								<th><?php echo __( 'Race Name', 'bblm' ); ?></th>
								<th><?php echo __( '# Teams', 'bblm' ); ?></th>
							</tr>
						</thead>
						<tbody>
<?php
					$is_first = 0;
				}

        if ($zebracount % 2) {
          echo '<tr>';
        }
        else {
          echo '<tr class="bblm_tbl_alt">';
        }
				echo '<td>';
				BBLM_CPT_Race::display_race_icon( $c->ID, 'mini' );
				echo '</td>';
				echo '<td>' . bblm_get_race_link( $c->ID ) . '</td>';
        echo '<td>' . $race->get_number_teams( $c->ID ) . '</td>';
        echo '</tr>';

        $zebracount++;

      } //end of foreach

    } //end of if
    else {

      echo '<p>' . __( 'No Races have been created for this league', 'bblm' ) . '</p>';

    } //end of else

?>
      </tbody>
    </table>
<?php

} //end of get_race_listing()

 /**
  * Returns the number of teams who represented this race
  *
  * @param wordpress $query
  * @return int $teamnum Numnber of teams
  */
  public function get_number_teams( $ID ) {
    global $post;
    global $wpdb;

    $teamnumsql = 'SELECT COUNT(*) AS TEAMNUM FROM '.$wpdb->prefix.'team T WHERE T.r_id = ' . $ID . ' AND T.t_show = 1';
    $teamnum = $wpdb->get_var( $teamnumsql );

    return $teamnum;

  }// end of get_number_teams()

	/**
   * Returns true if a race has "low cost linesmen"
   *
   * @param wordpress $query
   * @return bool true / false
   */
   public static function is_race_cheap_linos( $ID ) {

		 return has_term( 'low-cost-linemen', 'race_rules', $ID );

   }// end of is_race_cheap_linos()

	/**
   * Returns the cost of Rerolls for a race
   *
   * @param wordpress $query
   * @return int $rr_cost cost of reroll
   */
   public static function get_reroll_cost( $ID ) {

		 $rr_cost = get_post_meta( $ID, 'race_rrcost', true );

     return $rr_cost;

   }// end of get_reroll_cost()

		/**
		 * Returns the status (active / retired) of the Race
		 *
		 * @param wordpress $query
		 * @return int $rr_cost cost of reroll
		 */
		 public static function get_race_status( $ID ) {

			$rstatus = get_post_meta( $ID, 'race_rstatus', true );

			 return $rstatus;

		 }// end of et_race_status

	/**
   * OUTPUTS the race icon to a set size
   *
   * @param wordpress $query
   * @return none
   */
   public static function display_race_icon( $ID, $size ) {

		 switch ( $size ) {
	 		case ( 'medium' == $size ):
	 		    break;
	 		case ( 'icon' == $size ):
	 		    break;
	 		case ( 'mini' == $size ):
	 		    break;
	 		default :
	 	    	$size = 'icon';
	 		    break;
	 	}

     echo get_the_post_thumbnail( $ID, 'bblm-fit-' . $size );

   }// end of display_race_icon()

	 /**
		 * RETURNS the race icon to a set size
		 *
		 * @param wordpress $query
		 * @return none
		 */
		 public static function get_race_icon( $ID, $size ) {

			switch ( $size ) {
			 case ( 'medium' == $size ):
					 break;
			 case ( 'icon' == $size ):
					 break;
			 case ( 'mini' == $size ):
					 break;
			 default :
					 $size = 'icon';
					 break;
		 }

			 return get_the_post_thumbnail( $ID, 'bblm-fit-' . $size );

		 }// end of get_race_icon()

	 /**
	  * Outputs the positions that can play for a race
	  *
	  * @param int $id the race in question
	  * @return none
	  */
	  public function display_race_positions( $ID ) {
			global $wpdb;

			$positionsql = 'SELECT * FROM '.$wpdb->prefix.'position WHERE pos_status = 1 AND r_id = ' . $ID . ' ORDER by pos_cost ASC';
			if ( $positions = $wpdb->get_results( $positionsql ) ) {
				$zebracount = 1;
				$legacy = $this->get_race_status( $ID );
?>
			<div role="region" aria-labelledby="Caption01" tabindex="0">
			<table class="bblm_table bblm-tbl-scrollable">
				<thead>
					<tr>
						<th><?php echo __( 'Position', 'bblm' ); ?></th>
						<th><?php echo __( 'Limit', 'bblm' ); ?></th>
						<th class="bblm_tbl_stat"><?php echo __( 'MA', 'bblm' ); ?></th>
						<th class="bblm_tbl_stat"><?php echo __( 'ST', 'bblm' ); ?></th>
						<th class="bblm_tbl_stat"><?php echo __( 'AG', 'bblm' ); ?></th>
<?php
				if ( $legacy ) {
?>
						<th class="bblm_tbl_stat"><?php echo __( 'PA', 'bblm' ); ?></th>
<?php
				}
?>
						<th class="bblm_tbl_stat"><?php echo __( 'AV', 'bblm' ); ?></th>
						<th><?php echo __( 'Skills', 'bblm' ); ?></th>
						<th><?php echo __( 'Cost', 'bblm' ); ?></th>
					</tr>
				</thead>
				<tbody>
<?php
				foreach ( $positions as $pos ) {
					if ( $zebracount % 2 ) {
						echo '<tr class="bblm_tbl_alt" id="pos-' . $pos->pos_id . '">';
					}
					else {
						echo '<tr id="pos-' . $pos->pos_id . '">';
					}
?>
						<td><?php echo esc_html( $pos->pos_name ); ?></td>
						<td>0 - <?php echo $pos->pos_limit; ?></td>
						<td><?php echo $pos->pos_ma; ?></td>
						<td><?php echo $pos->pos_st; ?></td>
<?php
					if ( !$legacy ) {
?>
						<td><?php echo $pos->pos_ag; ?></td>
						<td><?php echo $pos->pos_av; ?></td>
<?php
					}
					else {
?>
						<td><?php echo $pos->pos_ag; ?>+</td>
						<td><?php if ( $pos->pos_pa ==0 ) { echo '-'; } else { echo $pos->pos_pa .'+'; } ?></td>
						<td><?php echo $pos->pos_av; ?>+</td>
<?php
					}
?>
						<td class="bblm_tbl_skills"><?php echo $pos->pos_skills; ?></td>
						<td><?php echo number_format( $pos->pos_cost ); ?> GP</td>
					</tr>
<?php
					$zebracount++;
				}
?>
						</tbody>
					</table>
				</div>
<?php
			}
			else {
				print("	<div class=\"bblm_info\">\n		<p>Sorry, but no positions have been filled out for this race</p>\n	</div>\n");
			}

	  } //end of display_race_positions()

		/**
 	  * Outputs the Star Players availbile to a race
 	  *
 	  * @param int $id the race in question
 	  * @return none
 	  */
 	  public function display_stars_available( $ID ) {
 			global $wpdb;
			global $post;

			$starsql = 0;

			//Grab the list of Race Special Rules / Traits assigned to this race
			$term_obj_list = get_the_terms( $post->ID, 'race_rules' );

			if ( $term_obj_list && ! is_wp_error( $term_obj_list ) ) {
				//Loop through them and add them to an array
				$race_terms = array();
				foreach ( $term_obj_list as $term ) {
					$race_terms[] = $term->slug;
				}

				//Form the custom query, looking for stars who have the same traits as the race
				$args = array(
					'post_type' => 'bblm_star',
					'orderby'   => 'title',
					'order' => 'ASC',
					'posts_per_page' => -1,
					'tax_query' => array(
						array(
							'taxonomy' => 'race_rules',
							'field' => 'slug',
							'terms' => $race_terms
						)
					)
				);

				$starsqlarray = array();
				$starsql = "(";

				// The Query
				$the_query = new WP_Query( $args );

				// The Loop
				if ( $the_query->have_posts() ) {
					while ( $the_query->have_posts() ) {
						$the_query->the_post();

						//Loop though each one and form the sql
						$starsqlarray[] =" X.WPID = " . get_the_ID();
					}
					$starsql .= join( " OR ", $starsqlarray );
					$starsql .= ")";
				}
				else {
					// no posts found
					echo '<p>' . __( 'There are currently no Star Players assigned to this race','bblm' ) . '<p>';
				}
				/* Restore original Post Data */
				wp_reset_postdata();

			}//end of if the race has any terms assigned
			else {
				//nothing is returned
				echo '<p>' . __( 'There are currently no Star Players assigned to this race','bblm' ) . '<p>';
			}


			//Check that Stars exist and were generated
			if ( !$starsql ) {
				//Stars don't exist, error message printed above

			}
			else {
				//Stars do exist, and the SQL was generated in the loop above
					$starplayersql = 'SELECT X.WPID AS PWPID, X.p_ma, X.p_st, X.p_ag, X.p_av, X.p_pa, X.p_skills, X.p_cost FROM '.$wpdb->prefix.'player X WHERE X.p_legacy = 0 AND X.p_status = 1 AND '. $starsql .' ORDER BY X.p_name ASC';

					if ( $starplayer = $wpdb->get_results( $starplayersql ) ) {
						$zebracount = 1;
?>
					<div role="region" aria-labelledby="Caption01" tabindex="0">
					<table class="bblm_table bblm-tbl-scrollable">
						<thead>
							<tr>
								<th><?php echo __( 'Name', 'bblm' ); ?></th>
								<th class="bblm_tbl_stat"><?php echo __( 'MA', 'bblm' ); ?></th>
								<th class="bblm_tbl_stat"><?php echo __( 'ST', 'bblm' ); ?></th>
								<th class="bblm_tbl_stat"><?php echo __( 'AG', 'bblm' ); ?></th>
								<th class="bblm_tbl_stat"><?php echo __( 'PA', 'bblm' ); ?></th>
								<th class="bblm_tbl_stat"><?php echo __( 'AV', 'bblm' ); ?></th>
								<th><?php echo __( 'Skills', 'bblm' ); ?></th>
								<th><?php echo __( 'Cost', 'bblm' ); ?></th>
							</tr>
						</thead>
						<tbody>
<?php
					foreach ( $starplayer as $star ) {
						if ( $zebracount % 2 ) {
							echo '<tr class="bblm_tbl_alt">';
						}
						else {
							echo '<tr>';
						}
?>
								<td><?php echo bblm_get_player_link( $star->PWPID ); ?></td>
								<td><?php echo $star->p_ma; ?></td>
								<td><?php echo $star->p_st; ?></td>
								<td><?php echo $star->p_ag; ?>+</td>
								<td><?php echo $star->p_pa; ?>+</td>
								<td><?php echo $star->p_av; ?>+</td>
								<td class="bblm_tbl_skills"><?php echo $star->p_skills; ?></td>
								<td><?php echo number_format( $star->p_cost ); ?> GP</td>
							</tr>
<?php
						$zebracount++;
					 }
?>
						</tbody>
					</table>
				</div>
<?php
				}
			}

 	  } //end of display_stars_available()

		/**
 	  * Outputs the Teams representing a race
 	  *
 	  * @param int $id the race in question
 	  * @return none
 	  */
 	  public function display_teams_representing( $ID ) {
 			global $wpdb;

			$teamsql = 'SELECT T.WPID AS TWPID FROM '.$wpdb->prefix.'team T WHERE T.t_show = 1 AND T.r_id = ' . $ID . ' ORDER by T.t_name ASC';
			if ($teams = $wpdb->get_results( $teamsql ) ) {
				$zebracount = 1;
?>
					<table class="bblm_table bblm-tbl-scrollable">
						<thead>
							<tr>
								<th><?php echo __( 'Team', 'bblm' ); ?></th>
							</tr>
						</thead>
						<tbody>
<?php
				foreach ( $teams as $td ) {
					if ( $zebracount % 2 ) {
						if ( $zebracount % 2 ) {
							echo '<tr>';
						}
						else {
							echo '<tr class="bblm_tbl_alt">';
						}
					}

					echo '<td>' . bblm_get_team_link( $td->TWPID ) . '</td>';
					echo '</tr>';
				}
				echo '</tbody>';
				echo '</table>';
			}
			else {
				echo '<div class="bblm_info">' . __( 'There are currently no teams representing this Race.', 'bblm' ) . '</div>';
			}

 	  } //end of display_teams_representing()

} //end of class

new BBLM_CPT_Race();
