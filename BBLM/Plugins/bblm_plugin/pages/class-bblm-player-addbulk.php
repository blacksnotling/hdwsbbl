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

    <h1 class="wp-heading-inline"><?php echo __( 'Add Players in Bulk', 'bblm'); ?></h1>
<?php
        $submissionresult = 0;

        if ( ( isset( $_POST[ 'bblm_addbulk_addplayer' ] ) ) && ( wp_verify_nonce( $_POST[ 'bblm_addbulk_player_nonce' ], basename(__FILE__) ) ) ) {
          //Final submit to Database

          echo '<p><pre>';
          print_r($_POST);
          echo '</pre></p>';

          //Determine how many players were potentially added
          $maxadded = (int) $_POST[ 'bblm_addbulk_numplayers' ];
          $addbulk_team = (int) $_POST[ 'bblm_addbulk_team' ];
          $i = 1;

          while($i <= $maxadded) {

            $name = wp_strip_all_tags( $_POST[ 'bblm_addbulk_name'.$i ] );
            $position = (int) $_POST[ 'bblm_addbulk_pos'.$i ];
            $num = (int) $_POST[ 'bblm_addbulk_num'.$i ];

            if ( strlen( $name ) > 0) {

              //The Player name submitted was not blank

              //Now we largly follow the script from add player
              //Get team information (name, ID, Slug)
              //Get Position Details
              //Map position details to temp vars

              echo '<p>Form '.$i.' is a player called '.$name.' - Position '.$position.' playing as number '.$num.'</p>';

            }

            $i++;
          }


        } //End of Final Submit

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
          $racepositionsql = 'SELECT * FROM '.$wpdb->prefix.'position WHERE r_id = ' . $teamdetail->r_id . ' AND pos_status = 1 ORDER BY pos_id ASC';
          $raceposition = $wpdb->get_results( $racepositionsql );

          //Determine current number of players on the team
          $teamplayercountsql = 'SELECT COUNT(*) AS NUM FROM '.$wpdb->prefix.'player WHERE t_id = ' . $bblm_addbulk_team . ' AND p_status = 1';
          $teamplayercount = $wpdb->get_row( $teamplayercountsql );

          $posfree = array();
          $teamnumbers = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16);

          if ( 0 == $teamplayercount->NUM ) {

            //If there are no players on the team
            //Map the positions available to the array
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

              //gather the list of positions that ARE available
              $steamnumsql = 'SELECT p_num FROM '.$wpdb->prefix.'player WHERE p_status = 1 and t_id = ' . $teamdetail->t_id . ' ORDER BY p_num ASC';
              $steamnum = $wpdb->get_results( $steamnumsql);
              //format them into a smaller array
              $teamnuminuse = array();
              foreach ( $steamnum as $tn ) {
                array_push( $teamnuminuse, $tn->p_num );
              }
              //default list of positions
              //compare the arrays and spit out the available numbers
              $teamnumbers = array_diff( $teamnumbers, $teamnuminuse );

            } //end of foreach

          } // end of if team has players

          //Get a list of the positions available for this team (taking into account what is used already)
          $positionfreesql = 'SELECT * FROM '.$wpdb->prefix.'position P WHERE r_id = ' . $teamdetail->r_id . ' AND pos_status = 1 AND (';
          //generate the sql for the positions that are eligable
          $is_first = 1;
          foreach ( $posfree as $pf ) {
            if ( 1 !== $is_first ) {
              $positionfreesql .= ' OR';
            }
            $positionfreesql .= ' P.pos_id = ' . $pf;
            $is_first = 0;
          }

          $positionfreesql .= ') AND P.pos_cost <= ' . $teamdetail->t_bank . ' ORDER BY pos_name ASC';

          $positionfree = $wpdb->get_results( $positionfreesql );
?>
          <h2 class="title"><?php echo __( 'Step 2: Provide details of the players you wish to add', 'bblm'); ?></h2>
          <p><?php echo __( 'Please add a player name and select a position. If a name is not added then the player will NOT be added!', 'bblm'); ?></p>
          <ul>
            <li> - <strong><?php echo __( 'Team', 'bblm'); ?></strong>: <?php echo esc_html( get_the_title( $teamdetail->WPID ) ); ?></li>
            <li> - <strong><?php echo __( 'Available Cash', 'bblm'); ?></strong>: <?php echo number_format( $teamdetail->t_bank ); ?>GP</li>
            <li> - <?php echo __( 'Only positions that are vacent will be displayed below', 'bblm'); ?></li>
            <li> - <?php echo __( 'Leave the player name <strong>blank</strong> if you <strong>DON\'T</strong> want to add them', 'bblm'); ?></li>
          </ul>
<?php
            echo '<h3>' . __(  'Positions Availible', 'bblm' ) . '</h3>';

            $teamplayertakensql = 'SELECT * FROM '.$wpdb->prefix.'position WHERE r_id = '.$teamdetail->r_id.' AND pos_status = 1 ORDER by pos_limit DESC';
            $teamplayertaken = $wpdb->get_results( $teamplayertakensql );
?>
            <table border="1">
              <thead>
                <tr>
                  <th><?php echo __( 'Position', 'bblm'); ?></th>
                  <th><?php echo __( 'Max', 'bblm'); ?></th>
                  <th><?php echo __( 'Hired', 'bblm'); ?></th>
                  <th><?php echo __( 'Available', 'bblm'); ?></th>
                </tr>
              </thead>
              <tbody>
<?php
            foreach ( $teamplayertaken as $tpt ) {

              $free = 0;
              $hired = 0;

              $playercountsql = 'SELECT COUNT(*) AS COUNT FROM '.$wpdb->prefix.'player WHERE pos_id = ' . $tpt->pos_id . ' and t_id = ' . $teamdetail->t_id . ' AND p_status = 1 GROUP BY pos_id';
              if ( $playercount = $wpdb->get_row( $playercountsql ) ) {
                if ( $playercount->COUNT > 0 ) {

                  $hired = (int)$playercount->COUNT;
                  $free = (int) $tpt->pos_limit - $hired;

                }

              }

              echo '<tr>';
              echo '<td>' . $tpt->pos_name . '</td>';
              echo '<td>' . (int) $tpt->pos_limit . '</td>';
              echo '<td>' . $hired . '</td>';
              echo '<td>' . $free . '</td>';
              echo '</tr>';

            }

            echo '</tbody></table>';
?>
          <form id="addbulk_player_detail" method="post" action="">

          <table class="widefat">
            <thead>
              <tr>
                <th><?php echo __( 'Player #', 'bblm'); ?></th>
                <th><?php echo __( 'Player Name', 'bblm'); ?></th>
                <th><?php echo __( 'Position', 'bblm'); ?></th>
              </tr>
            </thead>
            <tbody>
<?php
          //Output of table
          $bblm_limit = 16 - $teamplayercount->NUM;
          $i = 1;

          while( $i <= $bblm_limit ) {
?>
            <tr>
              <td><select name="bblm_addbulk_num<?php echo $i; ?>" id="bblm_addbulk_num<?php echo $i; ?>">
    <?php
              //output the list of available positions
              foreach ( $teamnumbers as $a ) {
                echo '<option value="' . $a . '">' . $a . '</option>';
              }
    ?>
                </select></td>
              <td><input type="text" name="bblm_addbulk_name<?php echo $i; ?>" size="60" value="" id="bblm_addbulk_name<?php echo $i; ?>" placeholder="Player Name - leave blank to skip"/></td>
              <td><td><select name="bblm_addbulk_pos<?php echo $i; ?>" id="bblm_addbulk_pos<?php echo $i; ?>">
    <?php
              //output the list of available positions
              foreach ( $positionfree as $pf ) {
                echo '<option value="' . $pf->pos_id . '">' . esc_html( $pf->pos_name ) . ' - ' . number_format( $pf->pos_cost ) . 'GP</option>';
              }
    ?>
                </select></td></td>
            </tr>
<?php
            $i++;

          }
?>
            </tbody>
          </table>
          <?php wp_nonce_field( basename( __FILE__ ), 'bblm_addbulk_player_nonce' ); ?>
          <input type="hidden" name="bblm_addbulk_team" size="10000" value="<?php echo $bblm_addbulk_team; ?>">
          <input type="hidden" name="bblm_addbulk_numplayers" size="10000" value="<?php echo $bblm_limit; ?>">
          <input type="submit" name="bblm_addbulk_addplayer" id="bblm_addbulk_addplayer" value="Add Players" title="Add Players" />
          </form>
<?php



        } //end of addbulk_player_details

    } // end of CLASS
?>
