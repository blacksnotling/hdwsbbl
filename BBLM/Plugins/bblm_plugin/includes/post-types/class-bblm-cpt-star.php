<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Star Players CPT functions
 *
 * Defines the functions related to the Star Players CPT (archive page logic, display functions etc)
 * For admin functions reloated to the CPT see the includes/admin/post-types directory
 *
 * @class 		BBLM_CPT_Star
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/CPT
 * @version   1.0
 */

class BBLM_CPT_Star extends BBLM_CPT_Player {

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

	    if( is_post_type_archive( 'bblm_player' ) && !is_admin() && $query->is_main_query() ) {
	        $query->set( 'posts_per_page', -1 );
          $query->set( 'orderby', 'title' );
          $query->set( 'order', 'asc' );
          $query->set( 'post_status', 'publish' );
	    }

	  }

		/**
		 * Displays a players performance in a seaspn / cup / competition
		 *
		 * @param wordpress $query
		 * @return none
		 */
		 public static function display_star_characteristics( $ID ) {
			 global $wpdb;

			 $playersql = 'SELECT P.p_id, P.t_id, P.p_ma, P.p_st, P.p_ag, P.p_av, P.p_pa, P.p_spp, P.p_skills, P.p_cost, P.p_legacy FROM '.$wpdb->prefix.'player P WHERE P.WPID = '.$ID;
			 $pd = $wpdb->get_row( $playersql );
?>
			 <div role="region" aria-labelledby="Caption01" tabindex="0">
       <table class="bblm_table bblm_table_collapsable">
				 <thead>
					 <tr>
						 <th class="bblm_tbl_name"><?php echo __( 'Position','bblm' ); ?></th>
						 <th class="bblm_tbl_stat"><?php echo __( 'MA','bblm' ); ?></th>
						 <th class="bblm_tbl_stat"><?php echo __( 'ST','bblm' ); ?></th>
						 <th class="bblm_tbl_stat"><?php echo __( 'AG','bblm' ); ?></th>
 <?php
  		if ( ! self::is_player_legacy( $ID ) ) {
 ?>
							<th class="bblm_tbl_stat"><?php echo __( 'PA', 'bblm' ); ?></th>
 <?php
      }
 ?>
							<th class="bblm_tbl_stat"><?php echo __( 'AV','bblm' ); ?></th>
							<th class="bblm_tbl_collapse"><?php echo __( 'Skills','bblm' ); ?></th>
							<th><?php echo __( 'Cost Per Match','bblm' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<tr class="bblm_tbl_alt">
							<td><?php echo __( 'Star Player','bblm' ); ?></td>
							<td><?php echo $pd->p_ma; ?></td>
							<td><?php echo $pd->p_st; ?></td>
 <?php
    		if ( self::is_player_legacy( $ID ) ) {
 ?>
							<td><?php echo $pd->p_ag; ?></td>
							<td><?php echo $pd->p_av; ?></td>
 <?php
      }
			else {
 ?>
							<td><?php echo $pd->p_ag; ?>+</td>
							<td><?php if ( $pd->p_pa ==0 ) { echo '-'; } else { echo $pd->p_pa .'+'; } ?></td>
							<td><?php echo $pd->p_av; ?>+</td>
 <?php
      }
 ?>
							<td class="bblm_tbl_skills bblm_tbl_collapse"><?php  echo $pd->p_skills; ?></td>
		 					<td><?php  echo number_format( $pd->p_cost ); ?> gp</td>
						</tr>

 <?php
     	if ( !self::is_player_legacy( $ID ) ) {
				//Only show this is the star is active

				//Display the player special rules if set
				$races_for = esc_textarea( get_post_meta( $ID, 'star_srules', true ) );

				echo '<tr>';
				echo '<td colspan="2"><strong>' . __( 'Special Rules','bblm' ) . '</strong></td>';
				echo '<td colspan="6"><em>' . $races_for . '</em></td>';
				echo '</tr>';

				//Grab the list of Race Special Rules / Traits assigned to this race

				echo '<tr class="bblm_tbl_alt">';

				$term_obj_list = get_the_terms( $ID, 'race_rules' );
				if ( $term_obj_list && ! is_wp_error( $term_obj_list ) ) {
					//Loop through them and add them to an array
					$race_terms = array();
					foreach ( $term_obj_list as $term ) {
						$race_terms[] = $term->slug;
					}
					//Form the custom query, looking for races who have the same traits as the Star
					$args = array(
						'post_type' => 'bblm_race',
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
					$racelist = "";

					// The Query
					$the_query = new WP_Query( $args );

					// The Loop
					if ( $the_query->have_posts() ) {
						while ( $the_query->have_posts() ) {
							$the_query->the_post();

							//Loop though each one and form the sql
							$starsqlarray[] = bblm_get_race_link( get_the_ID() );
						}
						$racelist .= join( ", ", $starsqlarray );
						echo '<td colspan="2"><strong>' . __( 'Plays for ', 'bblm') . '</strong></td>';
						echo '<td colspan="6">' . $racelist . '</td>';
					}
					else {
						// no posts found
						echo '<td colSpan="8">' . __( 'There are currently no Races assigned to this Star Players','bblm' ) . '</td>';
					}
					/* Restore original Post Data */
					wp_reset_postdata();

				}//end of if the race has any terms assigned
				else {
					//nothing is returned
					echo '<td colSpan="8">' . __( 'There are currently no Races assigned to this Star Players','bblm' ) . '</td>';
				}
				echo '</tr>';

			}//end of if not legacy
 ?>
					</tbody>
				</table>
			</div>
<?php

			} //end of display_star_characteristics()

		 /**
			* Displays a Star Players Match history
			* This one differs from the parent class in the stats it shows
			*
			* @param wordpress $query
			* @return none
			*/
			public static function display_plyaer_matchhistory( $ID ) {
				 global $wpdb;
?>
				 <div role="region" aria-labelledby="Caption01" tabindex="0">
				<table class="bblm_table bblm_sortable bblm_expandable bblm_table_collapsable">
					<thead>
					<tr>
						<th><?php echo __( 'Date', 'bblm' ); ?></th>
						<th></th>
						<th><?php echo __( 'For', 'bblm' ); ?></th>
						<th class="bblm_tbl_stat" ><?php echo __( 'TD', 'bblm' ); ?></th>
						<th class="bblm_tbl_stat" ><?php echo __( 'CAS', 'bblm' ); ?></th>
						<th class="bblm_tbl_stat bblm_tbl_collapse"><?php echo __( 'COMP','bblm' ); ?></th>
						<th class="bblm_tbl_stat bblm_tbl_collapse"><?php echo __( 'INT','bblm' ); ?></th>
						<th class="bblm_tbl_stat bblm_tbl_collapse"><?php echo __( 'MVP','bblm' ); ?></th>
						<th class="bblm_tbl_stat" ><?php echo __( 'SPP', 'bblm' ); ?></th>
					</tr>
					</thead>
					<tbody>
<?php
					$playermatchsql = 'SELECT P.*, M.WPID AS MWPID, UNIX_TIMESTAMP(M.m_date) AS mdate, A.WPID as TAWPID, A.t_id AS TAid, B.WPID AS TBWPID, B.t_id AS TBid FROM '.$wpdb->prefix.'match_player P, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'team A, '.$wpdb->prefix.'team B, '.$wpdb->prefix.'player X WHERE M.m_teamA = A.t_id AND M.m_teamB = B.t_id AND M.WPID = P.m_id AND P.p_id = X.p_id AND X.WPID = ' . $ID . ' ORDER BY M.m_date DESC';
					if ( $playermatch = $wpdb->get_results( $playermatchsql ) ) {
						$zebracount = 1;
						foreach ( $playermatch as $pm ) {
							if ( ( $zebracount % 2 ) && ( 10 < $zebracount ) ) {
								echo '<tr class="bblm_tbl_alt bblm_tbl_hide">';
							}
							else if (($zebracount % 2) && (10 >= $zebracount)) {
								echo '<tr class="bblm_tbl_alt">';
							}
							else if ( 10 < $zebracount ) {
								echo '<tr class="bblm_tbl_hide">';
							}
							else {
								echo '<tr>';
							}
							echo '<td>';
							echo bblm_get_match_link_date( $pm->MWPID );
							echo '</td>';
							if ( $pm->TAid == $pm->t_id ) {
								echo '<td>' . bblm_get_team_link_logo( $pm->TAWPID, 'mini' ) . '</td>';
								echo '<td>' . bblm_get_team_link( $pm->TAWPID ) . '</td>';

							}
							else {
								echo '<td>' . bblm_get_team_link_logo( $pm->TBWPID, 'mini' ) . '</td>';
								echo '<td>' . bblm_get_team_link( $pm->TBWPID ) . '</td>';
							}
							echo '<td>';
							if ( 0 == (int) $pm->mp_td ) {
								echo "0";
							}
							else {
								echo '<strong>' . (int) $pm->mp_td . '</strong>';
							}
							echo '</td>';
							echo '<td>';
							if ( 0 == (int) $pm->mp_cas ) {
								echo "0";
							}
							else {
								echo '<strong>' . (int) $pm->mp_cas . '</strong>';
							}
							echo '</td>';
							echo '<td class="bblm_tbl_collapse">';
							if ( 0 == (int) $pm->mp_comp ) {
								echo "0";
							}
							else {
								echo '<strong>' . (int) $pm->mp_comp . '</strong>';
							}
							echo '</td>';
							echo '<td class="bblm_tbl_collapse">';
							if ( 0 == (int) $pm->mp_int ) {
								echo "0";
							}
							else {
								echo '<strong>' . (int) $pm->mp_int . '</strong>';
							}
							echo '</td>';
							echo '<td class="bblm_tbl_collapse">';
							if ( 0 == (int) $pm->mp_mvp ) {
								echo "0";
							}
							else {
								echo '<strong>' . (int) $pm->mp_mvp . '</strong>';
							}
							echo '</td>';
							echo '<td>';
							if ( 0 == (int) $pm->mp_spp ) {
								echo "0";
							}
							else {
								echo '<strong>' . (int) $pm->mp_spp . '</strong>';
							}
							echo '</td>';
							echo '</tr>';
							$zebracount++;
						}
						echo '</tbody>';
						echo '</table>';
						echo '</div>';
					}

			 } //end of display_plyaer_matchhistory()

			 /**
				* Displays teh list of star players, grouped by if they are active or not
				*
				* @param wordpress $query
				* @return html
				*/
				public static function get_star_listing() {
					global $wpdb;

					$bblm_star_team = bblm_get_star_player_team();

					//First we collate all the star player stats to see if any have played
					$starstatssql = 'SELECT P.WPID AS PWPID, COUNT(*) AS GAMES, SUM(M.mp_td) AS TD, SUM(M.mp_cas) AS CAS, SUM(M.mp_comp) AS COMP, SUM(M.mp_int) AS MINT, SUM(M.mp_mvp) AS MVP, SUM(M.mp_spp) AS SPP';
					$starstatssql .= ' FROM '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'player P WHERE P.p_id = M.p_id AND M.mp_counts = 1 AND P.t_id = '.$bblm_star_team.' GROUP BY M.p_id ORDER BY P.WPID ASC';
					if ( $starstats = $wpdb->get_results( $starstatssql, 'OBJECT_K' ) ) {

						//Now we load in the star player post types
						$spostsarg = array(
							'post_type' => 'bblm_star',
							'numberposts' => -1,
							'meta_key' => 'star_status',
							'orderby' => array(
								'meta_value' => 'DESC',
								'post_title' => 'ASC',
							),
						);
						if ( $sposts = get_posts( $spostsarg ) ) {
							//We have some output
							$is_first = 1;
							$current_status = 0;
							$zebracount = 1;

							foreach( $sposts as $s ) {
								//Main Display loop

								if ( $s->star_status !== $current_status ) {
									$current_status = $s->star_status;

									if ( 1 !== $is_first ) {
										echo '</tbody>';
										echo '</table>';
										echo '</div>';
									}
									$is_first = 1;
								}
								if ( $is_first ) {
									$zebracount = 1;
									echo '<h3 class="bblm-table-caption">';
									if ( $current_status ) {
										echo __( 'Active Star Players','bblm' );
									}
									else {
										echo __( 'Retired / Legacy Star Players','bblm' );
									}
									echo '</h3>';
	?>
				<div role="region" aria-labelledby="Caption01" tabindex="0">
					<table class="bblm_table bblm_sortable bblm_table_collapsable">
						<thead>
							<tr>
								<th><?php echo __( 'Star', 'bblm' ); ?></th>
								<th><?php echo __( 'Pld', 'bblm' ); ?></th>
								<th><?php echo __( 'TD', 'bblm' ); ?></th>
								<th><?php echo __( 'CAS', 'bblm' ); ?></th>
								<th class="bblm_tbl_collapse"><?php echo __( 'COMP', 'bblm' ); ?></th>
								<th class="bblm_tbl_collapse"><?php echo __( 'INT', 'bblm' ); ?></th>
								<th class="bblm_tbl_collapse"><?php echo __( 'MVP', 'bblm' ); ?></th>
								<th><?php echo __( 'SPP', 'bblm' ); ?></th>
							</tr>
						</thead>
						<tbody>
	<?php
									$is_first = 0;
								}

								if ($zebracount % 2) {
									echo '<tr class="bblm_tbl_alt">';
								}
								else {
									echo '<tr>';
								}
								//If the star has olayed a game (they exist in the DB result)
								if (in_array_recursive( $s->ID, $starstats ) ) {
	?>
								<td><?php echo bblm_get_player_link( $s->ID ); ?></td>
								<td><strong><?php echo $starstats[$s->ID]->GAMES; ?></strong></td>
								<td><?php echo $starstats[$s->ID]->TD; ?></td>
								<td><?php echo $starstats[$s->ID]->CAS; ?></td>
								<td class="bblm_tbl_collapse"><?php echo $starstats[$s->ID]->COMP; ?></td>
								<td class="bblm_tbl_collapse"><?php echo $starstats[$s->ID]->MINT; ?></td>
								<td class="bblm_tbl_collapse"><?php echo $starstats[$s->ID]->MVP; ?></td>
								<td><strong><?php echo $starstats[$s->ID]->SPP; ?></strong></td>
							</tr>
	<?php
								}
								else {
									//The player has not played a game
	?>
								<td><?php echo bblm_get_player_link( $s->ID ); ?></td>
								<td><strong>0</strong></td>
								<td>-</td>
								<td>-</td>
								<td class="bblm_tbl_collapse">-</td>
								<td class="bblm_tbl_collapse">-</td>
								<td class="bblm_tbl_collapse">-</td>
								<td><strong>0</strong></td>
							</tr>
	<?php
								}
								$zebracount++;

							} //end of foreach
	?>
					</tbody>
					</table>
				</div>
	<?php
						} //enf of if star players exist

					}//end of if any players have taken part in a match
					else {
						//No games have been played with a Star Player
						echo '<div class="bblm_info">';
						echo '<p>' .  __( 'No Star Players have been hired by any teams, yet...', 'bblm' ) . '</p>';
						echo '</div>';
					}

				} //end of get_star_listing()


} //end of class

new BBLM_CPT_Star();
