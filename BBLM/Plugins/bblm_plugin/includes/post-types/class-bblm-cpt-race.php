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
 * @version   1.0
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
?>
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
    //Grabs a list of 'posts' from the bblm_cup CPT
    $cpostsarg = array(
      'post_type' => 'bblm_race',
      'numberposts' => -1,
      'orderby' => 'post_title',
      'order' => 'ASC',
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

      foreach( $cposts as $c ) {

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

      echo '<p>' . __( 'No Championship cups have been created for this league', 'bblm' ) . '</p>';

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
  * @return int $count Number of games played
  */
  public function get_number_teams( $ID ) {
    global $post;
    global $wpdb;

    $teamnumsql = 'SELECT COUNT(*) AS TEAMNUM FROM '.$wpdb->prefix.'team T WHERE T.r_id = ' . $ID . ' AND T.t_show = 1';
    $teamnum = $wpdb->get_var( $teamnumsql );

    return $teamnum;

  }// end of get_number_teams()

	/**
   * OUTPUTS the race icon to a set size
   *
   * @param wordpress $query
   * @return int $count Number of games played
   */
   public function display_race_icon( $ID, $size ) {

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

   }// end of get_number_teams()

} //end of class

new BBLM_CPT_Race();
