<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Championship Cups CPT functions
 *
 * Defines the functions related to the Championship Cups CPT (archive page logic, display functions etc)
 * For admin functions reloated to the CPT see the includes/admin/post-types directory
 *
 * @class 		BBLM_CPT_Cup
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/CPT
 * @version   1.2.1
 */

class BBLM_CPT_Cup {

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

	    if( is_post_type_archive( 'bblm_cup' ) && !is_admin() && $query->is_main_query() ) {
	        $query->set( 'posts_per_page', -1 );
          $query->set( 'orderby', 'title' );
          $query->set( 'order', 'asc' );
          $query->set( 'post_status', 'publish' );
	    }

	  }

 /**
  * Returns a list of all the Championsip Cups, and the number of competitions
  * that have been played for them
  *
  * @param wordpress $query
  * @return none
  */
  public static function get_cup_listing() {
?>
    <table class="bblm_table">
      <thead>
        <tr>
          <th><?php echo __( 'Championship Cup Name', 'bblm' ); ?></th>
          <th><?php echo __( '# Competitions', 'bblm' ); ?></th>
        </tr>
      </thead>
      <tbody>

<?php
    //Grabs a list of 'posts' from the bblm_cup CPT
    $cpostsarg = array(
      'post_type' => 'bblm_cup',
      'numberposts' => -1,
      'orderby' => 'post_title',
      'order' => 'ASC'
    );
    if ( $cposts = get_posts( $cpostsarg ) ) {
      $zebracount = 1;
      $comp = new BBLM_CPT_Comp;

      foreach( $cposts as $c ) {

        if ($zebracount % 2) {
          echo '<tr class="bblm_tbl_alt">';
        }
        else {
          echo '<tr>';
        }
        echo '<td>' . bblm_get_cup_link( $c->ID ) . '</td>';
        echo '<td>' . $comp->get_cup_count_by_cup( $c->ID ) . '</td>';
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

} //end of get_cup_listing()

 /**
  * Returns the number of games played for this championship cup
  *
  * @param wordpress $query
  * @return int $count Number of games played
  */
  public function get_number_games() {
    global $post;
    global $wpdb;

    $matchnumsql = 'SELECT COUNT(*) AS MATCHNUM FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C WHERE M.c_id = C.WPID AND C.series_id = ' . get_the_ID();
    $matchnum = $wpdb->get_var( $matchnumsql );

    return $matchnum;

  }// end of get_number_games()

} //end of class

new BBLM_CPT_Cup();
