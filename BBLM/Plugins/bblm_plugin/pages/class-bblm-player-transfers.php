<?php
  if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
  }

  /**
  	 *
  	 * @class 		BBLM_Player_Transfers
  	 * @author 		Blacksnotling
  	 * @category 	Admin
  	 * @package 	BBowlLeagueMan/Admin
  	 * @version   1.1
  	 */

  	class BBLM_Player_Transfers {

      // class instance
      static $instance;

      // class constructor
      public function __construct() {
        add_action( 'admin_menu', array( $this, 'plugin_menu' ) );
      }

      public function plugin_menu() {

        $hook = add_submenu_page(
          'bblm_main_menu',
          __( 'Transfers', 'bblm' ),
          __( 'Transfers', 'bblm' ),
          'manage_options',
          'bblm_transfers',
          array( $this, 'manage_transfers_page' )
        );

      }//end of plugin_menu

      /**
       * The Output of the Page
       */
      public function manage_transfers_page() {
        global $wpdb;
?>
    <div class="wrap">

      <h1 class="wp-heading-inline"><?php echo __( 'Player Transfers', 'bblm'); ?></h1>

<?php
      $submissionresult = 0;

      //Check to see if the final submission has been made
      if ( ( isset( $_POST[ 'bblm_transfer_confirm' ] ) ) && ( wp_verify_nonce( $_POST[ 'bblm_transfer_conf_nonce' ], basename(__FILE__) ) ) ) {

        $bblm_submit_season = (int) $_POST[ 'bblm_transfer_season' ];
        $bblm_submit_bank = (int) $_POST[ 'bblm_transfer_bank' ];
        $bblm_submit_cost = (int) $_POST[ 'bblm_transfer_cost' ];
        $bblm_submit_hteam = (int) $_POST[ 'bblm_hiring_team' ];
        $bblm_submit_hteamid = (int) $_POST[ 'bblm_hiring_teamID' ];
        $bblm_submit_steam = (int) $_POST[ 'bblm_sending_team' ];
        $bblm_submit_player = (int) $_POST[ 'bblm_transfer_player' ];
        $bblm_submit_cash_main = (int) $_POST[ 'bblm_transfer_cash_main' ];
        $bblm_submit_cash_ten = (int) $_POST[ 'bblm_transfer_cash_ten' ];
        $bblm_submit_desc = wp_strip_all_tags( $_POST[ 'bblm_transfer_desc' ] );
        $bblm_submit_num = (int) $_POST[ 'bblm_transfer_num' ];

        //sanity check to make sure the cost of the player is not more than the team
        if ( $bblm_submit_cost > $bblm_submit_bank ) {

          $submissionresult = "2";

        }
        else {

          //The main submission

          //Grab the player name from the Database
          $playerWPsql = 'SELECT * FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' X WHERE J.prefix = "p_" AND J.tid = P.p_id AND J.pid = X.ID AND P.p_id = ' . $bblm_submit_player;
          $playerWP = $wpdb->get_row( $playerWPsql );
          $bblm_submit_steamID = $playerWP->t_id;


          //Format the data for the CPT
          $transfer_title = esc_html( get_the_title( $bblm_submit_hteam ) ) . ' hires ' . esc_html( get_the_title( $playerWP->ID ) ) . ' from '. esc_html( get_the_title( $bblm_submit_steam ) );

          $post_content = array(
            'post_title' => $transfer_title,
            'post_content' => $bblm_submit_desc,
            'post_type' => 'bblm_transfer',
            'post_status' => 'publish',
            'meta_input'   => array(
              'bblm_transfer_season' => $bblm_submit_season,
              'bblm_transfer_hteam' => $bblm_submit_hteam,
              'bblm_transfer_steam' => $bblm_submit_steam,
              'bblm_transfer_player' => $playerWP->ID,
              'bblm_transfer_cost' => $bblm_submit_cost,
            )
          );

          //Insert the CPT and post_meta
          if ( wp_insert_post( $post_content ) ) {

            //Update the hiring team (minus money from bank)
            $updaterteamsql = 'UPDATE '.$wpdb->prefix.'team SET t_bank = t_bank-"'.$bblm_submit_cost.'" WHERE t_id = '. $bblm_submit_hteamid;
            $wpdb->query( $updaterteamsql );

            //Update the receving team(s) (Money from sale)
            //Check both teams are not the same first!
            if ( $bblm_submit_cash_main == $bblm_submit_cash_ten ) {

              //Both teams are the same
              $updatesteamsql = 'UPDATE '.$wpdb->prefix.'team SET t_bank = t_bank+"'.$bblm_submit_cost.'" WHERE t_id = '. $bblm_submit_cash_main;
              $wpdb->query( $updatesteamsql );

            }
            else {

              //Determine what 10% is
              $bblm_ninty = $bblm_submit_cost*0.9;
              $bblm_ten = $bblm_submit_cost*0.1;
              $updatesteammsql = 'UPDATE '.$wpdb->prefix.'team SET t_bank = t_bank+"'.$bblm_ninty.'" WHERE t_id = '. $bblm_submit_cash_main;
              $updatesteamtsql = 'UPDATE '.$wpdb->prefix.'team SET t_bank = t_bank+"'.$bblm_ten.'" WHERE t_id = '. $bblm_submit_cash_ten;
              $wpdb->query( $updatesteammsql );
              $wpdb->query( $updatesteamtsql );

            }//end of profit sharing

            //Update the player (Team ID, and set FLAG)
            $playerupdatesql = 'UPDATE '.$wpdb->prefix.'player SET t_id = "' . $bblm_submit_hteamid . '", p_num = "' . $bblm_submit_num . '", p_former = "' . $bblm_submit_hteam . '" WHERE p_id = ' . $bblm_submit_player;
            $wpdb->query( $playerupdatesql );

            //Update the player WP post (page parent)
            wp_update_post( array( 'ID' => $playerWP->ID, 'post_parent' => $bblm_submit_hteam ) );

            //Update TV's for both teams.
            bblm_update_tv( $bblm_submit_hteamid );
            bblm_update_tv( $bblm_submit_steamID );

            $submissionresult = 1;

            do_action( 'bblm_post_submission' );

          }//end of if post insertion successful

        }//end of if cost is not more than the team has available


      }

      //Check to see if a transfer has been submitted
      if ( ( isset( $_POST[ 'bblm_transfer_playerselect' ] ) ) && ( wp_verify_nonce( $_POST[ 'bblm_transfer_player_nonce' ], basename(__FILE__) ) ) ) {

        //A playerer has been selected for transfer - enter confirmation screens
        $this->transfer_player_confirmation();

      }

      //Check to see if a team has been selected and verify the nonce
      else if ( ( isset( $_POST[ 'bblm_transfer_teamselect' ] ) ) && ( wp_verify_nonce( $_POST[ 'bblm_transfer_team_nonce' ], basename(__FILE__) ) ) ) {

        //a team has been selected, display the transfer confirmation form
        $this->transfer_select_player();

      }
      else {

        //No team has been selected, so display the default page

        /*First we check to see that a Season is active (Transfers can only
        happen during the season) */
        if ( $sea = BBLM_CPT_Season::get_current_season() ) {


          if ( $submissionresult ) {

            //a transfer was sumitted successfully!
            //echo success, and link to edit player
            echo '<div id="updated" class="updated fade"><p>' . __( 'Transfer is complete!', 'bblm') . '</p>';
            edit_post_link( __( 'Update the player description', 'bblm' ), '<p>', '</p>', $playerWP->ID );
            echo '</div>';

          }
          else if ( "2" === $submissionresult ) {

            //Something went wrong with the submission, inform the user
            echo '<div id="updated" class="updated fade"><p>' . __( 'An error has occured! Please try again.', 'bblm') . '</p></div>';

          }
?>
      <h2 class="title"><?php echo __( 'Step 1: Select the receiving Team', 'bblm'); ?></h2>
      <p><?php echo __( 'Please Select the team that is <strong>HIRING</strong> a player (the receiving team).', 'bblm'); ?></p>
      <p><?php echo __( 'Only active teams are displayed!', 'bblm'); ?></p>
      <p><?php echo __( 'Please ensure the hiring team has at least one active player on the roster before starting a transfer', 'bblm'); ?></p>

      <form id="transfer_team_select" method="post" action="">

        <p><label for="bblm_transfer_team"><?php echo __( 'Hiring / Receiving team', 'bblm'); ?></label>
          <select name="bblm_transfer_team" id="bblm_transfer_team">
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
                  <input type="hidden" name="bblm_transfer_season" size="10000" value="<?php echo $sea; ?>">
<?php
          wp_nonce_field( basename( __FILE__ ), 'bblm_transfer_team_nonce' );
?>
        <input type="submit" name="bblm_transfer_teamselect" id="bblm_transfer_teamselect" value="Submit Team" title="Submit Team" />
      </form>

<?php
        }//end if a season is active
        else {

          //No season is active. inform the user
          echo '<p>' . __( 'Transfers can only happen during a season. Please start a season before you transfer players!', 'bblm') . '</p>';

        }

      } //End of display default page (nothing submitted)
?>

    </div>


<?php
    } //end of manage_transfers_page

    public function transfer_select_player() {
      global $wpdb;
      $bblm_transfer_season = (int) $_POST[ 'bblm_transfer_season' ];
      $bblm_transfer_team = (int) $_POST[ 'bblm_transfer_team' ];
?>
      <h2 class="title"><?php echo __( 'Step 2: Select the Player to transfer', 'bblm'); ?></h2>
      <p><?php echo __( 'Please select a player who they want to hire', 'bblm'); ?></p>
      <p><?php echo __( 'The following players will NOT be listed', 'bblm'); ?></p>
      <ul>
        <li> - <?php echo __( 'Players the team cannot afford', 'bblm'); ?></li>
        <li> - <?php echo __( 'The team is already at the maximum for that position', 'bblm'); ?></li>
        <li> - <?php echo __( 'Dead / Dead and Risen players', 'bblm'); ?></li>
        <li> - <?php echo __( 'Star Players', 'bblm'); ?></li>
        <li> - <?php echo __( 'Mercs and Journeymen', 'bblm'); ?></li>
<?php

      //Get details of the team
      $teamdetailssql = 'SELECT * from '.$wpdb->prefix.'team WHERE t_id = ' . $bblm_transfer_team;
      $teamdetail = $wpdb->get_row( $teamdetailssql );
?>
        <li> - <?php echo __( 'Players who cost more then', 'bblm') . ' ' . number_format( $teamdetail->t_bank ) . 'GP ' . __( '(The cash available to the team)', 'bblm'); ?></li>
      </ul>
<?php


      //Obtain a list of positions available for the tams race
      $racepositionsql = 'SELECT * FROM '.$wpdb->prefix.'position WHERE r_id = ' . $teamdetail->r_id .' ORDER BY pos_id ASC';

      //determine the current number of players in each position on the team
      $teamplayerpossql = 'SELECT pos_id, COUNT(*) AS COUNT FROM '.$wpdb->prefix.'player WHERE t_id = ' . $bblm_transfer_team . ' AND p_status = 1 AND pos_id != 1 GROUP BY pos_id ORDER BY pos_id';

      //if there are both positions for that team AND the team has active players then continue
      if ( ( $raceposition = $wpdb->get_results( $racepositionsql ) ) && ( $teamplayerpos = $wpdb->get_results( $teamplayerpossql , 'OBJECT_K' ) ) ) {
        //we have positions and players, so continue

        //Loop through each of the positions available for the race and compare against the players currently on the team, if space add it to an array
        //It checks the position is filled first to suporess an error
        $posfree = array();

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

        }//end of each player omn existing TEAM

        //Now we get the list of available players who match the above Positions
        $playerselectsql = 'SELECT * FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'position X WHERE X.pos_id = P.pos_ID AND T.t_id = P.t_id AND P.p_status = 1 AND (';

        //generate the sql for the positions that are eligable
        $is_first = 1;
        foreach ( $posfree as $pf ) {
          if ( 1 !== $is_first ) {
            $playerselectsql .= ' OR';
          }
          $playerselectsql .= ' P.pos_id = ' . $pf;
          $is_first = 0;
        }

        $playerselectsql .= ') AND T.ID != ' . $teamdetail->ID . ' AND P.p_cost <= ' . $teamdetail->t_bank . ' ORDER BY T.t_name, P.pos_id, P.p_id';

?>
      <form id="transfer_player_select" method="post" action="">

        <p><?php echo __( 'Please make sure a player is selected, and not a team name!', 'bblm'); ?></p>

<?php
        //Drop down box for players
        if ( $playerselect = $wpdb->get_results( $playerselectsql ) ) {
?>
        <h3><?php echo __( 'Hiring for: ', 'bblm') . esc_html( get_the_title( $teamdetail->WPID ) ) ; ?></h3>
        <p><label for="bblm_transfer_player"><?php echo __( 'Player to transfer:', 'bblm'); ?></label>
          <select name="bblm_transfer_player" id="bblm_transfer_player">
<?php
          $last_team = 0;
          foreach ( $playerselect as $ps ) {
            if ( $last_team !== $ps->WPID ) { //first player from this team - display the team name

              echo '<option value="X">' . esc_html( get_the_title( $ps->WPID ) ) . '</option>';
              echo '<option value="' . $ps->p_id . '">&nbsp;&nbsp;- ' . esc_html( $ps->p_name ) . ' - ' . esc_html( $ps->pos_name ) . ' - ' . esc_html( number_format( $ps->p_cost ) ) . 'GP </option>';
              $last_team = $ps->WPID;

            }
            else {

              echo '<option value="' . $ps->p_id . '">&nbsp;&nbsp;- ' . esc_html( $ps->p_name ) . ' - ' . esc_html( $ps->pos_name ) . ' - ' . esc_html( number_format( $ps->p_cost ) ) . 'GP </option>';

            }

          }
?>
          </select>
        </p>
<?php
} //end of if players are avalable
        else {

          //No players meet the criteria
          echo '<div id="updated" class="updated fade"><p>' . __( 'Sorry, no Players eligible!', 'bblm') . '</p></div>';

        }

?>

        <input type="hidden" name="bblm_transfer_season" size="10000" value="<?php echo $bblm_transfer_season; ?>">
        <input type="hidden" name="bblm_transfer_team" size="10000" value="<?php echo $bblm_transfer_team; ?>">
        <?php wp_nonce_field( basename( __FILE__ ), 'bblm_transfer_player_nonce' ); ?>
        <p><input type="submit" name="bblm_transfer_playerselect" id="bblm_transfer_playerselect" value="Submit Transfer" title="Submit Transfer" /></p>

      </form>
<?php
}//end of if positions are available ible for the team and the team has players
      else {

        //We only get here if there is a real error!
        echo '<div id="updated" class="updated fade"><p>' . __( 'Sorry, no Players eligible!', 'bblm') . '</p></div>';

      }//end of if else positions are available for the team and the team has players

    } //end of transfer_select_player()

    public function transfer_player_confirmation() {
      global $wpdb;
      $bblm_transfer_season = (int) $_POST[ 'bblm_transfer_season' ];
      $bblm_hiring_team = (int) $_POST[ 'bblm_transfer_team' ];
      $bblm_transfer_player = (int) $_POST[ 'bblm_transfer_player' ];

?>
      <h2 class="title"><?php echo __( 'Step 3: Confirm Transfers Details', 'bblm'); ?></h2>

<?php
      if ( "X" == $_POST[ 'bblm_transfer_player' ] ) {

        //A team has been selectedm bail!
        echo '<div id="updated" class="updated fade"><p>' . __( 'A team has been selected and not a player! Please try again', 'bblm') . '</p></div>';

      }
      else {

        //Get details of the team
        $rteamdetailssql = 'SELECT * from '.$wpdb->prefix.'team WHERE t_id = ' . $bblm_hiring_team;
        $rteamdetail = $wpdb->get_row( $rteamdetailssql );
        //Get the details of the player and the team they are on
        $playerdetailsql = 'SELECT * FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'position X WHERE X.pos_id = P.pos_id AND T.t_id = P.t_id AND P.p_id = ' . $bblm_transfer_player;
        $playerdetail = $wpdb->get_row( $playerdetailsql );

        //Determine who the money will go to!
        /*If the players team is active, it will all go to them
        If they are not, then 10% will go to the owners active team
        If they do not have an active team, then all goes to the original team */
        $bblm_transfer_money_main = $playerdetail->t_id; // by default it goes to the owning team
        $bblm_transfer_money_ten = $playerdetail->t_id; // by default it goes to the owning team
        if ( "0" == $playerdetail->t_active ) {

          //Team is not active, determine if the owner has another team that is
          $ownerteamsql = 'SELECT * FROM '.$wpdb->prefix.'team T WHERE ID = ' . $playerdetail->ID . ' AND t_active = 1';
          if ( $ownerteam = $wpdb->get_row( $ownerteamsql ) ) {

            //Owner does have another active team, so the 10% goes to them
            $bblm_transfer_money_ten = $ownerteam->t_id;
            $bblm_transfer_money_ten_ID = $ownerteam->WPID;

          }

        }


?>
        <p><?php echo __( 'Please review the details below carefully', 'bblm'); ?></p>
        <ul>
          <li> - <?php echo __( 'Hiring team:', 'bblm'); ?> <strong><?php echo esc_html( get_the_title( $rteamdetail->WPID ) ); ?></strong></li>
          <li> - <?php echo __( 'Player being transfered:', 'bblm'); ?> <strong><?php echo esc_html( $playerdetail->p_name ); ?></strong></li>
          <li> - <?php echo __( 'Player position:', 'bblm'); ?> <strong><?php echo esc_html( $playerdetail->pos_name ); ?></strong></li>
          <li> - <?php echo __( 'Player minimum cost:', 'bblm'); ?> <strong><?php echo esc_html( number_format($playerdetail->p_cost ) ); ?></strong>GP</li>

<?php
        if ( $bblm_transfer_money_main == $bblm_transfer_money_ten ) {

          //The transfer cash is going to the same team
          $transferrecep = "100% to ";
          $transferrecep .= esc_html( $playerdetail->t_name );

        }
        else {

          //The transfer cash is getting split
          $transferrecep = "90% to ";
          $transferrecep .= esc_html( $playerdetail->t_name );
          $transferrecep .= ", 10% to ";
          $transferrecep .= esc_html( get_the_title( $bblm_transfer_money_ten_ID ) );

        }
?>
          <li> - <?php echo __( 'Money will go to:', 'bblm'); ?> <strong><?php echo $transferrecep; ?></strong></li>
        </ul>

        <form id="transfer_player_confirm" method="post" action="">

          <input type="hidden" name="bblm_transfer_bank" id="bblm_transfer_bank" size="10000" value="<?php echo $rteamdetail->t_bank; ?>">

          <script type="text/javascript">
          function BBLM_Transfer_Watch() {

            var tot_a = document.getElementById('bblm_transfer_bank').value;
            var tot_b = document.getElementById('bblm_transfer_cost').value;
            //If value of the transfer is greater than the bank availiblke
            if( parseFloat( tot_b ) > parseFloat( tot_a ) ) {
              //disable submit submit_button
              document.getElementById("bblm_transfer_confirm").disabled = true;
            }
            else {
              //enable the submit button!
              document.getElementById("bblm_transfer_confirm").disabled = false;
            }
          }
          </script>

<?php

        //Determine if the player position number needs to change
        $playernum = $playerdetail->p_num;
        //Looks for active players on the recei team for players with the same number
        $existingnumsql = 'SELECT * FROM '.$wpdb->prefix.'player WHERE p_num = ' . $playernum . ' AND p_status = 1 AND t_id = ' . $rteamdetail->t_id;
        $existingnum = $wpdb->get_row( $existingnumsql  );
        //Checks something has been returned
        if ( count( $existingnum ) > 0 ) {

          //A clashing player has been found - display the change position box
          //gather the list of positions that ARE available
          $steamnumsql = 'SELECT p_num FROM '.$wpdb->prefix.'player WHERE p_status = 1 and t_id = ' . $rteamdetail->t_id . ' ORDER BY p_num ASC';
          $steamnum = $wpdb->get_results( $steamnumsql);
          //format them into a smaller array
          $teamnuminuse = array();
          foreach ( $steamnum as $tn ) {
            array_push( $teamnuminuse, $tn->p_num );
          }
          //default list of positions
          $teamnumbers = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16);
          //compare the arrays and spit out the available numbers
          $available = array_diff( $teamnumbers, $teamnuminuse );
?>
          <p><label for="bblm_transfer_num"><?php echo __( 'The player being transfered has a position number that is already in use on the recieving team. please select a free number:', 'bblm'); ?></label></p>
          <p><select name="bblm_transfer_num" id="bblm_transfer_num">
<?php
          //output the list of available positions
          foreach ( $available as $a ) {
            echo '<option value="' . $a . '">' . $a . '</option>';
          }
?>
            </select></p>
<?php
        }//end of if there is a pre-existing position
?>
          <p><label for="bblm_transfer_cost"><?php echo __( 'Would you like to adjust the amount the team will pay? There is ', 'bblm') . number_format( $rteamdetail->t_bank ) . __( 'GP in the Treasury', 'bblm'); ?></label>
            <input type="text" name="bblm_transfer_cost" id="bblm_transfer_cost" size="11" tabindex="1" value="<?php echo esc_html( $playerdetail->p_cost ); ?>" maxlength="10" onChange="BBLM_Transfer_Watch()"> GP <strong><?php echo __( 'DO NOT hit ENTER on your keyboard after changing!!!', 'bblm'); ?></strong></p>
          <p><textarea name="bblm_transfer_desc" id="bblm_transfer_desc" placeholder="Enter any comments, or details of the trade here (optional)" rows="5" cols="50"></textarea></p>

          <input type="hidden" name="bblm_transfer_season" size="10000" value="<?php echo $bblm_transfer_season; ?>">
          <input type="hidden" name="bblm_hiring_team" size="10000" value="<?php echo $rteamdetail->WPID; ?>">
          <input type="hidden" name="bblm_hiring_teamID" size="10000" value="<?php echo $bblm_hiring_team; ?>">
          <input type="hidden" name="bblm_sending_team" size="10000" value="<?php echo $playerdetail->WPID; ?>">
          <input type="hidden" name="bblm_transfer_player" size="10000" value="<?php echo $bblm_transfer_player; ?>">
          <input type="hidden" name="bblm_transfer_cash_main" size="10000" value="<?php echo $bblm_transfer_money_main; ?>">
          <input type="hidden" name="bblm_transfer_cash_ten" size="10000" value="<?php echo $bblm_transfer_money_ten; ?>">
          <?php wp_nonce_field( basename( __FILE__ ), 'bblm_transfer_conf_nonce' ); ?>

          <p><input type="submit" name="bblm_transfer_confirm" id="bblm_transfer_confirm" value="Confirm Transfer" title="Confirm Transfer" /></p>

        </form>
<?php
      }//End of else - player has been selected and not a team

    } // end of transfer_player_confirmation()


   } // end of CLASS


  ?>
