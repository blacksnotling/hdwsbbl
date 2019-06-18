<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Transfers CPT functions
 *
 * Defines the functions related to the Transfers CPT (archive page logic, display functions etc)
 *
 * @class 		BBLM_CPT_TRANSFER
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/CPT
 * @version   1.1
 */

class BBLM_CPT_Transfer {

  /**
   * Constructor
   */
   public function __construct() {

     add_action( 'pre_get_posts', array( $this, 'manage_archives' ) );

  }

  /**
   * stops the CPT archive pages pagenating and changes the display order
   *
   * @param wordpress $query
   * @return none
   */
  public function manage_archives( $query ) {

    if( is_post_type_archive( 'bblm_transfer' ) && !is_admin() && $query->is_main_query() ) {
      $query->set( 'posts_per_page', -1 );
      $query->set( 'orderby', 'post_date_gmt' );
      $query->set( 'order', 'desc' );
      $query->set( 'post_status', 'publish' );
      }

    }//end of manage_archives

   /**
    * Outputs the players transfer history (if it exists)
    *
    * @return output
    */
    public function display_player_transfer_history() {
      global $post;
      global $wpdb;

      $output = "";

      //Check to see if the player has been transfered in the past
      $args = array(
        'post_type'  => 'bblm_transfer',
        'meta_query' => array(
          array(
            'key'   => 'bblm_transfer_player',
            'value' => get_the_ID(),
          )
        )
      );
      //get the list and check something was returned
      if ( $transferlist = get_posts( $args ) ) {

        //Display title and description
        $output .= '<h3>Transfer History</h3>';
        $output .= '<p>This player has been part of multiple teams, below is their transfer history:</p>';
        $output .= '<ul>';

        //loop through the results
        foreach ( $transferlist as $tl ) {

          $meta = get_post_meta( $tl->ID );

          //display the team they went from-> to, the value, and the season
          $output .= '<li>From ';
          $output .= bblm_get_team_link( $meta[ 'bblm_transfer_steam' ][0] );
          $output .= ' to ';
          $output .= bblm_get_team_link( $meta[ 'bblm_transfer_hteam' ][0] );
          $output .= ' for <strong>';
          $output .= number_format( $meta[ 'bblm_transfer_cost' ][0] );
          $output .= '</strong>GP - ';
          $output .= '<a title="Read more about this Season" href="' . get_post_permalink( $meta[ 'bblm_transfer_season' ][0] ) . '">' . esc_html( get_the_title( $meta[ 'bblm_transfer_season' ][0] ) ) . '</a>';
          if ( "" !== $tl->post_content ) {
            $output .= '<ul><li>' . nl2br( $tl->post_content ) . '</li></ul>';
          }
          $output .= '</li>';


        }//end of foreach

        $output .= '</ul>';

      }//end of if pages found

      echo __( $output, 'bblm');

    }// end of display_player_transfer_history

    /**
     * Outputs the players transfer history (if it exists)
     *
     * @return output
     */
    public function display_team_transfer_history() {
      global $post;
      global $wpdb;

      $output = "";

      //Gather the requiref data to see if the team has performed any transfers
      //There are two - one for hiring players, one for Selling
      //Selling
      $argss = array(
        'post_type'  => 'bblm_transfer',
        'meta_query' => array(
          array(
            'key'   => 'bblm_transfer_steam',
            'value' => get_the_ID(),
          )
        )
      );
      //buying
      $argsh = array(
        'post_type'  => 'bblm_transfer',
        'meta_query' => array(
          array(
            'key'   => 'bblm_transfer_hteam',
            'value' => get_the_ID(),
          )
        )
      );

      //determines if a transfer has occured
      if ( ( get_posts( $argss ) ) || ( get_posts( $argsh ) ) ) {
        $transferteamsell = get_posts( $argss );
        $transferteamshire = get_posts( $argsh );

        $output .= '<h3>Transfer History</h3>';
        $output .= '<p>This team has hired or sold at least one player. Below is the teams transfer history:</p>';

        if ( !empty( $transferteamshire ) ) {

          //they have hired players
          $output .= '<h4>Players Hired</h4>';
          $output .= '<ul>';

          //loop through the results
          foreach ( $transferteamshire as $tth ) {

            $meta = get_post_meta( $tth->ID );

            //display the Player, the team they dealed with, the cosr, and the season
            $output .= '<li>' . bblm_get_player_link( $meta[ 'bblm_transfer_player' ][0] );
            $output .= ' hired from ';
            $output .= bblm_get_team_link( $meta[ 'bblm_transfer_steam' ][0] );
            $output .= ' for <strong>';
            $output .= number_format( $meta[ 'bblm_transfer_cost' ][0] );
            $output .= '</strong>GP - <a title="Read more about this Season" href="' . get_post_permalink( $meta[ 'bblm_transfer_season' ][0] ) . '">' . esc_html( get_the_title( $meta[ 'bblm_transfer_season' ][0] ) ) . '</a>';
            $output .= '</li>';

          }//end of for each
          $output .= '</ul>';

        }
        if ( !empty( $transferteamsell ) ) {

          //they have sold players
          $output .= '<h4>Players Sold</h4>';
          $output .= '<ul>';

          //loop through the results
          foreach ( $transferteamsell as $tts ) {

            $meta = get_post_meta( $tts->ID );

            //display the Player, the team they dealed with, the cosr, and the season
            $output .= '<li>' . bblm_get_player_link( $meta[ 'bblm_transfer_player' ][0] );
            $output .= ' sold to ';
            $output .= bblm_get_team_link( $meta[ 'bblm_transfer_hteam' ][0] );
            $output .= ' for <strong>';
            $output .= number_format( $meta[ 'bblm_transfer_cost' ][0] );
            $output .= '</strong>GP - <a title="Read more about this Season" href="' . get_post_permalink( $meta[ 'bblm_transfer_season' ][0] ) . '">' . esc_html( get_the_title( $meta[ 'bblm_transfer_season' ][0] ) ) . '</a>';
            $output .= '</li>';

          }//end of for each
          $output .= '</ul>';

        }


      }//end of if transfers

      echo __( $output, 'bblm');

    }// end of display_team_transfer_history

		/**
		 * Outputs the record for largest transfer
		 *
		 * @return output
		 */
		public function display_player_transfer_record() {

			$output = "";

			$args = array(
				'post_type'  => 'bblm_transfer',
				'numberposts' => 1,
				'order'			=> 'DESC',
				'meta_key'	=> 'bblm_transfer_cost',
				'orderby'   => 'meta_value_num',
			);

			$record = get_posts( $args );
			foreach ( $record as $r ) {
				$meta = get_post_meta( $r->ID );

        $output .= bblm_get_player_link( $meta[ 'bblm_transfer_player' ][0] );
				$output .=	' for ';
				$output .=  number_format( $meta[ 'bblm_transfer_cost' ][0] );
				$output .=  'GP (';
				$output .=	'hired by ';
				$output .= bblm_get_team_link( $meta[ 'bblm_transfer_hteam' ][0] );
				$output .=	' from ';
        $output .= bblm_get_team_link( $meta[ 'bblm_transfer_steam' ][0] );


			}

			echo __( $output, 'bblm');

		} //end of display_player_transfer_record()

		/**
		 * Outputs a list of recent transfers
		 *
		 * @return output
		 */
		public function display_recent_transfer_list( $Limit ) {

			$output = "";

			$args = array(
				'post_type'  => 'bblm_transfer',
				'numberposts' => $Limit,
				'order'			=> 'DESC',
			);
			if ( $recenttransfers = get_posts( $args ) ) {

				$output .= '<ul>';

				foreach ( $recenttransfers as $rt ) {
					$meta = get_post_meta( $rt->ID );

					$output .= '<li>';
					$output .= bblm_get_team_link( $meta[ 'bblm_transfer_hteam' ][0] );
        	$output .= ' hires ';
        	$output .= bblm_get_player_link( $meta[ 'bblm_transfer_player' ][0] );
        	$output .= ' from ';
        	$output .= bblm_get_team_link( $meta[ 'bblm_transfer_steam' ][0] );
        	$output .= '</li>';

				}//end of foreach

				$output .= '</ul>';

			}

			echo __( $output, 'bblm');

		} //end of display_recent_transfer_list

} //end of Class

new BBLM_CPT_Transfer();
