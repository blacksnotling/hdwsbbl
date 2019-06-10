<?php
  if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
  }

  /**
  	 *
  	 * @class 		BBLM_Player_AddBulk
  	 * @author 		Blacksnotling
  	 * @category 	Admin
  	 * @package 	BBowlLeagueMan/Admin
  	 * @version   1.0
  	 */

  	class BBLM_Player_AddBulk {

      // class instance
      static $instance;

      // class constructor
      public function __construct() {
        add_action( 'admin_menu', array( $this, 'plugin_menu' ) );
      }

      public function plugin_menu() {

        $hook = add_submenu_page(
          'bblm_main_menu',
          __( 'Add Bulk Players', 'bblm' ),
          __( 'Add Bulk Players', 'bblm' ),
          'manage_options',
          'bblm_player_addbulk',
          array( $this, 'add_bulk_players_page' )
        );

      }//end of plugin_menu

      /**
       * The Output of the Page
       */
      public function add_bulk_players_page() {
        global $wpdb;
?>
  <div class="wrap">

    <h1 class="wp-heading-inline"><?php echo __( 'Add Player in Bulk', 'bblm'); ?></h1>
<?php
        if ( ( isset( $_POST[ 'bblm_addbulk_teamselect' ] ) ) && ( wp_verify_nonce( $_POST[ 'bblm_addbulk_team_nonce' ], basename(__FILE__) ) ) ) {

          //a team has been selected, display the add players form
          $this->addbulk_player_details();
        }
        else {
          //No team has been selected so disply the initial page
?>
    <h2 class="title"><?php echo __( 'Step 1: Select the Team you wish to add players to', 'bblm'); ?></h2>

    <form id="addbulk_team_select" method="post" action="">

      <p><label for="bblm_addbulk_team"><?php echo __( 'Team: ', 'bblm'); ?></label>
        <select name="bblm_addbulk_team" id="bblm_addbulk_team">
<?php
        //Determine the Star Player Team (to get the star players)
        $bblm_team_star = bblm_get_star_player_team();
        //Grab the ID of the 'To Be Determined Team'
        $bblm_tbd_team = bblm_get_tbd_team();

        $teamlistsql = 'SELECT WPID, t_id FROM '.$wpdb->prefix.'team WHERE t_id != ' . $bblm_team_star . ' AND t_id != ' . $bblm_tbd_team . ' AND t_active = 1 ORDER BY t_name ASC';
        if ( $teams = $wpdb->get_results( $teamlistsql ) ) {
          foreach ( $teams as $team ) {
            echo '<option value="' . $team->t_id . '">' . esc_html( get_the_title( $team->WPID ) ) . '</option>';
          }
        }
      ?>
        </select>
      </p>
<?php
        wp_nonce_field( basename( __FILE__ ), 'bblm_addbulk_team_nonce' );
?>
      <input type="submit" name="bblm_addbulk_teamselect" id="bblm_addbulk_teamselect" value="Select Team" title="Select Team" />
    </form>

<?php
        }//end of else - display initial page

?>
  </div> <!-- End of .wrap -->
<?php
      } //end of add_bulk_players_page()

     /**
      * The Player detail page
      **/
      public function addbulk_player_details() {
          global $wpdb;
          $bblm_addbulk_team = (int) $_POST[ 'bblm_addbulk_team' ];

          //gather team details (bank, race etc)
          $teamdetailssql = 'SELECT * from '.$wpdb->prefix.'team WHERE t_id = ' . $bblm_addbulk_team;
          $teamdetail = $wpdb->get_row( $teamdetailssql );

          //Obtain a list of positions available for the tams race
          $racepositionsql = 'SELECT * FROM '.$wpdb->prefix.'position WHERE r_id = ' . $teamdetail->r_id .' AND pos_status = 1 ORDER BY pos_id ASC';
          $raceposition = $wpdb->get_results( $racepositionsql );

          //Determine current number of players on the team
          $teamplayercountsql = 'SELECT COUNT(*) AS NUM FROM '.$wpdb->prefix.'player WHERE t_id = ' . $bblm_addbulk_team . ' AND p_status = 1';
          $teamplayercount = $wpdb->get_row( $teamplayercountsql );

          $posfree = array();

          if ( 0 == $teamplayercount->NUM ) {

            //If there are no players on the team
            //Map the positions availible to the array
            foreach ( $raceposition as $rp ) {

              $posfree[] = $rp->pos_id;

            }

          }
          else {

            //The team has players

            //determine the current number of players in each position on the team
            $teamplayerpossql = 'SELECT pos_id, COUNT(*) AS COUNT FROM '.$wpdb->prefix.'player WHERE t_id = ' . $bblm_addbulk_team . ' AND p_status = 1 AND pos_id != 1 GROUP BY pos_id ORDER BY pos_id';
            $teamplayerpos = $wpdb->get_results( $teamplayerpossql , 'OBJECT_K' );

            foreach ( $raceposition as $rp ) {

              if ( isset( $teamplayerpos[$rp->pos_id] ) ) { //if that position is occupied in the taeam

                if ( $teamplayerpos[$rp->pos_id]->COUNT < $rp->pos_limit ) {

                  //there is a free slot for this position so add it to the array
                  $posfree[] = $rp->pos_id;

                }

              }
              else {

                //no player with that position is on the team so add it to the array anyway
                $posfree[] = $rp->pos_id;

              }

            } //end of foreach

          } // end of if team has players

          echo '<p><pre>';
          print_r($posfree);
          echo '</pre><p>';


?>
          <h2 class="title"><?php echo __( 'Step 2: Provide details of the players you wish to add', 'bblm'); ?></h2>
          <p><?php echo __( 'Please add a player name and select a position. If a name is not added then the player will NOT be added!', 'bblm'); ?></p>
          <ul>
            <li> - <strong><?php echo __( 'Team', 'bblm'); ?></strong>: <?php echo esc_html( get_the_title( $teamdetail->WPID ) ); ?></li>
            <li> - <strong><?php echo __( 'Availible Cash', 'bblm'); ?></strong>: <?php echo number_format( $teamdetail->t_bank ); ?>GP</li>
            <li> - <?php echo __( 'Only positions that are vacent will be displayed below', 'bblm'); ?></li>
          </ul>

          <table>
            <thead>
              <tr>
                <th><?php echo __( 'Position #', 'bblm'); ?></th>
                <th><?php echo __( 'Player Name', 'bblm'); ?></th>
                <th><?php echo __( 'Position', 'bblm'); ?></th>
              </tr>
            </thead>
            <tbody>
<?php
          //Output of table
          $bblm_limit = 16 - $teamplayercount->NUM;
          $i = 1;

          while($i <= $bblm_limit) {
?>
            <tr>
              <td>X</td>
              <td>Hello!</td>
              <td>Hello!</td>
            </tr>
<?php
            $i++;

          }
?>
            </tbody>
          </table>
<?php



        } //end of addbulk_player_details

    } // end of CLASS
?>
