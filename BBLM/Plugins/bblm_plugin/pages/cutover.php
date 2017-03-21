<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Makes the DB changes required to conver v1.X to 2.0
 *
 * @author 		Blacksnotling
 * @category 	Cutover
 * @package 	BBowlLeagueMan/Cutover
 * @version   1.0
 */
 ?>
 <div class="wrap">
 	<h2>League Manager Cutover</h2>

<?php
//SQL to get the WP ID and Stad ID for Stadiums
//$sql = "SELECT P.ID, R.stad_id, P.post_title FROM hdbb_stadium R, hdbb_posts P, hdbb_bb2wp J WHERE R.stad_id = J.tid AND P.ID = J.pid and J.prefix = \'stad_\' ORDER BY P.ID ASC";

//SQL to update the bblm_stadium CPT
//UPDATE `hdwsbbl_v2dev`.`hdbb_posts` SET `post_parent` = '0', `post_type` = 'bblm_stadium' WHERE `hdbb_posts`.`ID` = 102;
//$sql = "UPDATE `hdwsbbl_v2dev`.`hdbb_posts` SET `post_parent` = \'0\', `post_type` = \'bblm_stadium\' WHERE `hdbb_posts`.`ID` = 102;";

 /**
  *
  * UPDATING MAIN POST TABLE FOR THE STADIUM CPT
  */
if (isset($_POST['bblm_stadium_stadcpt'])) {

  $stadpostsql = "SELECT P.ID, R.stad_id, P.post_title FROM ".$wpdb->prefix."stadium R, ".$wpdb->posts." P, ".$wpdb->prefix."bb2wp J WHERE R.stad_id = J.tid AND P.ID = J.pid and J.prefix = 'stad_' ORDER BY P.ID ASC";
    if ($stadposts = $wpdb->get_results($stadpostsql)) {
      //echo '<ul>';
      foreach ($stadposts as $stad) {
        $stadupdatesql = "UPDATE `".$wpdb->posts."` SET `post_parent` = '0', `post_type` = 'bblm_stadium' WHERE `".$wpdb->posts."`.`ID` = '".$stad->ID."';";
        //print("<li>".$stadupdatesql."</li>");
        if ( $wpdb->query($stadupdatesql) ) {
          $result = true;
        }
        else {
          $result = false;
        }

      } //end of foreach
      //echo '</ul>';
      if ( $result ) {
        print("<div id=\"updated\" class=\"updated fade\"><p>Posts table updated for Stadiums! <strong>Now you can delete the stadiums page!</strong></p></div>\n");
      }
    }//end of if sql was successful
} //end of if (isset($_POST['bblm_stadium_stadcpt']))

/**
 *
 * UPDATING TEAMS TABLE FOR THE NEW STADIUM IDs
 */
if (isset($_POST['bblm_stadium_teams'])) {

  $teampostsql = "SELECT T.t_id, T.stad_id, P.ID FROM ".$wpdb->prefix."team T, ".$wpdb->posts." P, ".$wpdb->prefix."bb2wp J WHERE T.stad_id = J.tid AND P.ID = J.pid and J.prefix = 'stad_'";
    if ($teamposts = $wpdb->get_results($teampostsql)) {
      //echo '<ul>';
      foreach ($teamposts as $stad) {
        $stadupdatesql = "UPDATE `".$wpdb->prefix."team` SET `stad_id` = '".$stad->ID."' WHERE t_id` = $stad->t_id;";
        //print("<li>".$stad->t_id." = ".$stad->stad_id." -> ".$stad->ID."</li>");
        //print("<li>".$stadupdatesql."</li>");
        if ( $wpdb->query($stadupdatesql) ) {
          $result = true;
        }
        else {
          $result = false;
        }

      } //end of foreach
      //echo '</ul>';
      if ( $result ) {
        print("<div id=\"updated\" class=\"updated fade\"><p>Teams table updated with the new Stadiums!</p></div>\n");
      }
    }//end of if sql was successful

} // end of if (isset($_POST['bblm_stadium_teams'])) {

  /**
   *
   * UPDATING Matches TABLE FOR THE NEW STADIUM IDs
   */
  if (isset($_POST['bblm_stadium_match'])) {

    $matchpostsql = "SELECT M.m_id, M.stad_id, P.ID FROM ".$wpdb->prefix."match M, ".$wpdb->posts." P, ".$wpdb->prefix."bb2wp J WHERE M.stad_id = J.tid AND P.ID = J.pid and J.prefix = 'stad_'";
      if ($matchposts = $wpdb->get_results($matchpostsql)) {
        //echo '<ul>';
        foreach ($matchposts as $stad) {
          $stadupdatesql = "UPDATE `".$wpdb->prefix."match` SET `stad_id` = '".$stad->ID."' WHERE `".$wpdb->prefix."match`.`m_id` = ".$stad->m_id.";";
          //print("<li>".$stad->m_id." = ".$stad->stad_id." -> ".$stad->ID."</li>");
          //print("<li>".$stadupdatesql."</li>");
          if ( $wpdb->query($stadupdatesql) ) {
            $result = true;
          }
          else {
            $result = false;
          }

        } //end of foreach
        //echo '</ul>';
        if ( $result ) {
          print("<div id=\"updated\" class=\"updated fade\"><p>Matches table updated with the new Stadiums!</p></div>\n");
        }
      }//end of if sql was successful

  } // end of if (isset($_POST['bblm_stadium_match'])) {
/****
* END OF Stadiums
*
* START OF Championship Cups
*/
/**
 *
 * UPDATING WP Posts TABLE FOR THE NEW CHAMPIONSHIPS CPT
 */
if (isset($_POST['bblm_cup_cupcpt'])) {

  $cuppostsql = "SELECT P.ID, R.series_id, P.post_title, R.series_type FROM ".$wpdb->prefix."series R, ".$wpdb->posts." P, ".$wpdb->prefix."bb2wp J WHERE R.series_id = J.tid AND P.ID = J.pid and J.prefix = 'series_' ORDER BY P.ID ASC";
    if ($stadposts = $wpdb->get_results($cuppostsql)) {
//      echo '<ul>';
      foreach ($stadposts as $stad) {
        $stadupdatesql = "UPDATE `".$wpdb->posts."` SET `post_parent` = '0', `post_type` = 'bblm_cup' WHERE `".$wpdb->posts."`.`ID` = '".$stad->ID."';";
//        print("<li>".$stadupdatesql."</li>");
//        print("<li>Meta -> '".$stad->ID."', 'cup_type', '".$stad->series_type."'</li>");
        if ( $wpdb->query($stadupdatesql) ) {
          $result = true;
          add_post_meta( $stad->ID, 'cup_type', $stad->series_type, true );
        }
        else {
          $result = false;
        }

      } //end of foreach
//      echo '</ul>';
      if ( $result ) {
        print("<div id=\"updated\" class=\"updated fade\"><p>Posts table updated for Championships Page! <strong>Now you can delete the Championship Cups page!</strong></p></div>\n");
      }
    }//end of if sql was successful

} //end of if (isset($_POST['bblm_cup_cupcpt']))

/**
 *
 * UPDATING COMPETITIONS TABLE FOR THE NEW CHAMPIONSHIPS IDs
 */
if (isset($_POST['bblm_cup_comp'])) {

    $comppostsql = "SELECT T.c_id, T.series_id, P.ID FROM ".$wpdb->prefix."comp T, ".$wpdb->posts." P, ".$wpdb->prefix."bb2wp J WHERE T.series_id = J.tid AND P.ID = J.pid and J.prefix = 'series_'";
    if ($teamposts = $wpdb->get_results($comppostsql)) {
      //echo '<ul>';
      foreach ($teamposts as $stad) {
        $stadupdatesql = "UPDATE `".$wpdb->prefix."comp` SET `series_id` = '".$stad->ID."' WHERE `c_id` = $stad->c_id;";
        //print("<li>".$stad->c_id." = ".$stad->series_id." -> ".$stad->ID."</li>");
        //print("<li>".$stadupdatesql."</li>");
        if ( $wpdb->query($stadupdatesql) ) {
          $result = true;
        }
        else {
          $result = false;
        }

      } //end of foreach
      //echo '</ul>';
      if ( $result ) {
        print("<div id=\"updated\" class=\"updated fade\"><p>Competitions table updated with the new Championships!</p></div>\n");
      }
    }//end of if sql was successful

} // end of if (isset($_POST['bblm_cup_comp'])) {
  /****
  * END OF Championship Cups
  *
  * START OF Seasons
  */
  /**
   *
   * UPDATING WP Posts TABLE FOR THE NEW SEASONS CPT
   */
  if (isset($_POST['bblm_season_seacpt'])) {

    $cuppostsql = "SELECT P.ID, R.sea_id, P.post_title, UNIX_TIMESTAMP(R.sea_sdate) AS sdate, UNIX_TIMESTAMP(R.sea_fdate) AS fdate FROM ".$wpdb->prefix."season R, ".$wpdb->posts." P, ".$wpdb->prefix."bb2wp J WHERE R.sea_id = J.tid AND P.ID = J.pid and J.prefix = 'sea_' ORDER BY P.ID ASC";
      if ($stadposts = $wpdb->get_results($cuppostsql)) {
//        echo '<ul>';
        foreach ($stadposts as $stad) {
          $stadupdatesql = "UPDATE `".$wpdb->posts."` SET `post_parent` = '0', `post_type` = 'bblm_season' WHERE `".$wpdb->posts."`.`ID` = '".$stad->ID."';";
//          print("<li>".$stadupdatesql."</li>");
          if ( date("Y-m-d", $stad->fdate) == '1970-01-01' ) {
            $fdate = '0000-00-00';
          }
          else {
            $fdate = date("Y-m-d", $stad->fdate);
          }
//          print("<li>Meta -> '".$stad->ID."', 'season_sdate', '".date("Y-m-d", $stad->sdate)."'</li>");
//          print("<li>Meta -> '".$stad->ID."', 'season_fdate', '".$fdate."'</li>");
          if ( $wpdb->query($stadupdatesql) ) {
            $result = true;
            add_post_meta( $stad->ID, 'season_sdate', date("Y-m-d", $stad->sdate), true );
            add_post_meta( $stad->ID, 'season_fdate', $fdate, true );
          }
          else {
            $result = false;
          }

        } //end of foreach
//        echo '</ul>';
        if ( $result ) {
          print("<div id=\"updated\" class=\"updated fade\"><p>Posts table updated for Seasons Page! <strong>Now you can delete the Seasons page!</strong></p></div>\n");
        }
      }//end of if sql was successful

  } //end of if (isset($_POST['bblm_season_seacpt']))

  /**
   *
   * UPDATING COMPETITIONS TABLE FOR THE NEW SEASONS IDs
   */
  if (isset($_POST['bblm_season_comp'])) {

      $comppostsql = "SELECT T.c_id, T.sea_id, P.ID FROM ".$wpdb->prefix."comp T, ".$wpdb->posts." P, ".$wpdb->prefix."bb2wp J WHERE T.sea_id = J.tid AND P.ID = J.pid and J.prefix = 'sea_'";
      if ($teamposts = $wpdb->get_results($comppostsql)) {
        //echo '<ul>';
        foreach ($teamposts as $stad) {
          $stadupdatesql = "UPDATE `".$wpdb->prefix."comp` SET `sea_id` = '".$stad->ID."' WHERE `c_id` = $stad->c_id;";
          //print("<li>".$stad->c_id." = ".$stad->sea_id." -> ".$stad->ID."</li>");
          //print("<li>".$stadupdatesql."</li>");
          if ( $wpdb->query($stadupdatesql) ) {
            $result = true;
          }
          else {
            $result = false;
          }

        } //end of foreach
        //echo '</ul>';
        if ( $result ) {
          print("<div id=\"updated\" class=\"updated fade\"><p>Competitions table updated with the new Seasons!</p></div>\n");
        }
      }//end of if sql was successful

  } // end of if (isset($_POST['bblm_season_comp'])) {
    /****
    * END OF Seasons
    *
    * START OF Races
    */
    /**
     *
     * UPDATING WP Posts TABLE FOR THE NEW CHAMPIONSHIPS CPT
     */
    if (isset($_POST['bblm_race_racecpt'])) {

      $cuppostsql = "SELECT P.ID, R.r_id, P.post_title, R.r_rrcost, R.r_show FROM ".$wpdb->prefix."race R, ".$wpdb->posts." P, ".$wpdb->prefix."bb2wp J WHERE R.r_id = J.tid AND P.ID = J.pid and J.prefix = 'r_' ORDER BY P.ID ASC";
        if ($stadposts = $wpdb->get_results($cuppostsql)) {
  //        echo '<ul>';
          foreach ($stadposts as $stad) {
            $stadupdatesql = "UPDATE `".$wpdb->posts."` SET `post_parent` = '0', `post_type` = 'bblm_race' WHERE `".$wpdb->posts."`.`ID` = '".$stad->ID."';";
  //          print("<li>".$stadupdatesql."</li>");
  //          print("<li>Meta -> '".$stad->ID."', 'race_rrcost', '".$stad->r_rrcost."'</li>");
  /*          if ( ! $stad->r_show ) {
              print("<li>Meta -> '".$stad->ID."', 'race_hide', '1'</li>");
            }
  */          if ( $wpdb->query($stadupdatesql) ) {
              $result = true;
              add_post_meta( $stad->ID, 'race_rrcost', $stad->r_rrcost, true );
              if ( ! $stad->r_show ) {
                add_post_meta( $stad->ID, 'race_hide', '1', true );
              }
            }
            else {
              $result = false;
            }

          } //end of foreach
//          echo '</ul>';
          if ( $result ) {
            print("<div id=\"updated\" class=\"updated fade\"><p>Posts table updated for Championships Page! <strong>Now you can delete the Races page!</strong></p></div>\n");
          }
        }//end of if sql was successful

    } //end of if (isset($_POST['bblm_raceracecpt']))
    /**
     *
     * UPDATING positions TABLE FOR THE NEW Race IDs
     */
    if (isset($_POST['bblm_race_positions'])) {

      $teampostsql = "SELECT T.r_id, T.pos_id, P.ID FROM ".$wpdb->prefix."position T, ".$wpdb->posts." P, ".$wpdb->prefix."bb2wp J WHERE T.r_id = J.tid AND P.ID = J.pid and J.prefix = 'r_'";
        if ($teamposts = $wpdb->get_results($teampostsql)) {
//          echo '<ul>';
          foreach ($teamposts as $stad) {
            $stadupdatesql = "UPDATE `".$wpdb->prefix."position` SET `r_id` = '".$stad->ID."' WHERE pos_id = '".$stad->pos_id."';";
//            print("<li>".$stad->pos_id." = ".$stad->r_id." -> ".$stad->ID."</li>");
//            print("<li>".$stadupdatesql."</li>");
            if ( $wpdb->query($stadupdatesql) ) {
              $result = true;
            }
            else {
              $result = false;
            }

          } //end of foreach
//          echo '</ul>';
          if ( $result ) {
            print("<div id=\"updated\" class=\"updated fade\"><p>Positions table updated with the new Races!</p></div>\n");
          }
        }//end of if sql was successful

    } // end of if (isset($_POST['bblm_race_positions'])) {

      /**
       *
       * UPDATING race2star TABLE FOR THE NEW Race IDs
       */
      if (isset($_POST['bblm_race_race2star'])) {

        $teampostsql = "SELECT T.r_id, T.p_id, P.ID FROM ".$wpdb->prefix."race2star T, ".$wpdb->posts." P, ".$wpdb->prefix."bb2wp J WHERE T.r_id = J.tid AND P.ID = J.pid and J.prefix = 'r_'";
          if ($teamposts = $wpdb->get_results($teampostsql)) {
//            echo '<ul>';
            foreach ($teamposts as $stad) {
              $stadupdatesql = "UPDATE `".$wpdb->prefix."race2star` SET `r_id` = '".$stad->ID."' WHERE r_id = ".$stad->r_id." AND p_id = '".$stad->p_id."';";
//              print("<li>".$stad->r_id." and ".$stad->p_id." -> ".$stad->ID." and ".$stad->p_id."</li>");
//              print("<li>".$stadupdatesql."</li>");
              if ( $wpdb->query($stadupdatesql) ) {
                $result = true;
              }
              else {
                $result = false;
              }

            } //end of foreach
  //          echo '</ul>';
            if ( $result ) {
              print("<div id=\"updated\" class=\"updated fade\"><p>race2star table updated with the new Races!</p></div>\n");
            }
          }//end of if sql was successful

      } // end of if (isset($_POST['bblm_race_race2star'])) {

				/****
		    * END OF Races
		    *
		    * START OF Competitions
		    */
				/**
		     *
		     * UPDATING WP Posts TABLE FOR THE NEW Competitions
		     */
		    if (isset($_POST['bblm_comp_compcpt'])) {

		      $comppostsql = "SELECT P.ID, R.ct_id, R.c_active, R.sea_id FROM ".$wpdb->prefix."comp R, ".$wpdb->posts." P, ".$wpdb->prefix."bb2wp J WHERE R.c_id = J.tid AND P.ID = J.pid and J.prefix = 'c_' ORDER BY P.ID ASC";
		        if ($stadposts = $wpdb->get_results($comppostsql)) {
							//Define the array to handle the competition types
							$comptypes = array(
								1 => 'open-league',
								2 => 'scheduled-league',
								3 => 'knockout-tournament',
								4 => 'ko-tourny-return',
								5 => 'round-robin',
								6 => 'world-series',
							);

//							echo '<pre>'.var_dump($comptypes).'</pre>';
//		          echo '<ul>';
		          foreach ($stadposts as $stad) {
		            $stadupdatesql = "UPDATE `".$wpdb->posts."` SET `post_parent` = '0', `post_type` = 'bblm_comp' WHERE `".$wpdb->posts."`.`ID` = '".$stad->ID."';";
//		            print("<li>".$stadupdatesql."</li>");
								$category = get_term_by( 'slug', $comptypes[ $stad->ct_id ], 'comp_type' );
				        $cat = $category->slug;
				        //wp_set_object_terms($stad->ID, $cat, 'comp_type');
//								echo 'Comp Type => set term ('.$stad->ID.', '.$cat.', "comp_type")';

//		            if ( ! $stad->c_active ) {
//		              print("<li>Meta -> '".$stad->ID."', 'comp_complete', '1'</li>");
//		            }
		            if ( $wpdb->query($stadupdatesql) ) {
		              $result = true;
		              wp_set_object_terms($stad->ID, $cat, 'comp_type');
		              if ( ! $stad->c_active ) {
		                add_post_meta( $stad->ID, 'comp_complete', '1', true );
		              }
									add_post_meta( $stad->ID, 'comp_season', $stad->sea_id, true );
		            }
		            else {
		              $result = false;
		            }

		          } //end of foreach
//		          echo '</ul>';
		          if ( $result ) {
		            print("<div id=\"updated\" class=\"updated fade\"><p>Posts table updated for Competitions! <strong>Now you can delete the Competitions page!</strong></p></div>\n");
		          }
		        }//end of if sql was successful

		    } //end of if (isset($_POST['bblm_comp_compcpt']))
				/**
	       *
	       * UPDATING competitions TABLE FOR THE NEW Comps IDs
	       */
	      if (isset($_POST['bblm_comp_comptbl'])) {

	        $teampostsql = "SELECT T.c_id, P.ID FROM ".$wpdb->prefix."comp T, ".$wpdb->posts." P, ".$wpdb->prefix."bb2wp J WHERE T.c_id = J.tid AND P.ID = J.pid and J.prefix = 'c_'";
	          if ($teamposts = $wpdb->get_results($teampostsql)) {
	//            echo '<ul>';
	            foreach ($teamposts as $stad) {
	              $stadupdatesql = "UPDATE `".$wpdb->prefix."comp` SET `ID` = '".$stad->ID."' WHERE c_id = ".$stad->c_id.";";
	//              print("<li>".$stad->r_id." and ".$stad->p_id." -> ".$stad->ID." and ".$stad->p_id."</li>");
	//              print("<li>".$stadupdatesql."</li>");
	              if ( $wpdb->query($stadupdatesql) ) {
	                $result = true;
	              }
	              else {
	                $result = false;
	              }

	            } //end of foreach
	  //          echo '</ul>';
	            if ( $result ) {
	              print("<div id=\"updated\" class=\"updated fade\"><p>The Competition table updated with the WP IDs!</p></div>\n");
	            }
	          }//end of if sql was successful

	      } // end of if (isset($_POST['bblm_comp_comptbl'])) {


				/****
				* END OF Competitions
				*
				* START OF TEAMS
				*/
				/**
		     *
		     * UPDATING WP Posts TABLE FOR THE NEW Team CPT
		     */
		    if (isset($_POST['bblm_team_teamcpt'])) {

		      $cuppostsql = "SELECT P.ID, R.r_id, R.t_id, R.t_show, R.t_active FROM ".$wpdb->prefix."team R, ".$wpdb->posts." P, ".$wpdb->prefix."bb2wp J WHERE R.t_id = J.tid AND P.ID = J.pid and J.prefix = 't_' ORDER BY P.ID ASC";
		        if ($stadposts = $wpdb->get_results($cuppostsql)) {
//		          echo '<ul>';
		          foreach ($stadposts as $stad) {
		            $stadupdatesql = "UPDATE `".$wpdb->posts."` SET `post_parent` = '0', `post_type` = 'bblm_team' WHERE `".$wpdb->posts."`.`ID` = '".$stad->ID."';";
/*		            print("<li>".$stadupdatesql."</li>");
		            print("<li>Meta -> '".$stad->ID."', 'team_race', '".$stad->r_id."'</li>");
		            if ( ! $stad->t_show ) {
		              print("<li>Meta -> '".$stad->ID."', 'team_hide', '1'</li>");
		            }
								if ( ! $stad->t_active ) {
		              print("<li>Meta -> '".$stad->ID."', 'team_retired', '1'</li>");
		            }
*/		            if ( $wpdb->query($stadupdatesql) ) {
		              $result = true;
		              add_post_meta( $stad->ID, 'team_race', $stad->r_id, true );
		              if ( ! $stad->t_show ) {
		                add_post_meta( $stad->ID, 'team_hide', '1', true );
		              }
									if ( ! $stad->t_active ) {
		                add_post_meta( $stad->ID, 'team_retired', '1', true );
		              }
		            }
		            else {
		              $result = false;
		            }

		          } //end of foreach
//		          echo '</ul>';
		          if ( $result ) {
		            print("<div id=\"updated\" class=\"updated fade\"><p>Posts table updated for Team CPT! <strong>Now you can delete the team page!</strong></p></div>\n");
		          }
		        }//end of if sql was successful

		    } //end of if (isset($_POST['bblm_team_teamcpt']))

				/**
	       *
	       * UPDATING teams TABLE FOR THE NEW team IDs
	       */
	      if (isset($_POST['bblm_team_teamtbl'])) {

	        $teampostsql = "SELECT T.t_id, P.ID FROM ".$wpdb->prefix."team T, ".$wpdb->posts." P, ".$wpdb->prefix."bb2wp J WHERE T.t_id = J.tid AND P.ID = J.pid and J.prefix = 't_'";
	          if ($teamposts = $wpdb->get_results($teampostsql)) {
//	            echo '<ul>';
	            foreach ($teamposts as $stad) {
	              $stadupdatesql = "UPDATE `".$wpdb->prefix."team` SET `tID` = '".$stad->ID."' WHERE t_id = ".$stad->t_id.";";
	//              print("<li>".$stadupdatesql."</li>");
	              if ( $wpdb->query($stadupdatesql) ) {
	                $result = true;
	              }
	              else {
	                $result = false;
	              }

	            } //end of foreach
//	            echo '</ul>';
	            if ( $result ) {
	              print("<div id=\"updated\" class=\"updated fade\"><p>The Teams table updated with the WP IDs!</p></div>\n");
	            }
	          }//end of if sql was successful

	      } // end of if (isset($_POST['bblm_team_teamtbl'])) {

				/****
				* END OF TEAMS
				*
				* START OF PLAYERS and ROSTERS
				*/
				/**
		     *
		     * UPDATING WP Posts TABLE FOR THE NEW Player CPT
		     */
		    if (isset($_POST['bblm_team_playercpt'])) {

		      $cuppostsql = "SELECT P.ID, R.p_id, R.t_id, R.p_status FROM ".$wpdb->prefix."player R, ".$wpdb->posts." P, ".$wpdb->prefix."bb2wp J WHERE R.p_id = J.tid AND P.ID = J.pid and J.prefix = 'p_' ORDER BY P.ID ASC";
		        if ($stadposts = $wpdb->get_results($cuppostsql)) {
//		          echo '<ul>';
		          foreach ($stadposts as $stad) {
		            $stadupdatesql = "UPDATE `".$wpdb->posts."` SET `post_type` = 'bblm_player' WHERE `".$wpdb->posts."`.`ID` = '".$stad->ID."';";
//								print("<li>".$stadupdatesql."</li>");
//		            print("<li>Meta -> '".$stad->ID."', 'player_team', '".$stad->t_id."'</li>");
/*		            if ( 52 == $stad->t_id ) {
		              print("<li>Meta -> '".$stad->ID."', 'player_star', '1'</li>");
		            }
								if ( ! $stad->p_status ) {
		              print("<li>Meta -> '".$stad->ID."', 'player_retired', '1'</li>");
		            }
*/	            if ( $wpdb->query($stadupdatesql) ) {
		              $result = true;
		              add_post_meta( $stad->ID, 'player_team', $stad->t_id, true );
		              if ( 52 == $stad->t_id ) {
		                add_post_meta( $stad->ID, 'player_star', '1', true );
		              }
									if ( ! $stad->p_status ) {
		                add_post_meta( $stad->ID, 'player_retired', '1', true );
		              }
		            }
		            else {
		              $result = false;
		            }

		          } //end of foreach
//		          echo '</ul>';
		          if ( $result ) {
		            print("<div id=\"updated\" class=\"updated fade\"><p>Posts table updated for Player CPT!</p></div>\n");
		          }
		        }//end of if sql was successful

		    } //end of if (isset($_POST['bblm_team_playercpt']))
				/**
		     *
		     * UPDATING WP Posts TABLE FOR THE NEW ROSTER CPT
		     */
		    if (isset($_POST['bblm_team_rostercpt'])) {

		      $cuppostsql = "SELECT T.t_id, P.ID FROM ".$wpdb->prefix."team T, ".$wpdb->posts." P, ".$wpdb->prefix."bb2wp J WHERE T.t_id = J.tid AND P.ID = J.pid and J.prefix = 'roster'";
		        if ($stadposts = $wpdb->get_results($cuppostsql)) {
//		          echo '<ul>';
		          foreach ($stadposts as $stad) {
		            $stadupdatesql = "UPDATE `".$wpdb->posts."` SET `post_type` = 'bblm_roster' WHERE `".$wpdb->posts."`.`ID` = '".$stad->ID."';";
//								print("<li>".$stadupdatesql."</li>");
	            if ( $wpdb->query($stadupdatesql) ) {
		              $result = true;
		            }
		            else {
		              $result = false;
		            }

		          } //end of foreach
//		          echo '</ul>';
		          if ( $result ) {
		            print("<div id=\"updated\" class=\"updated fade\"><p>Posts table updated for Roster CPT!</p></div>\n");
		          }
		        }//end of if sql was successful

		    } //end of if (isset($_POST['bblm_team_rostercpt']))
				/**
	       *
	       * UPDATING Player TABLE FOR THE NEW team IDs
	       */
	      if (isset($_POST['bblm_team_playertbl'])) {

	        $teampostsql = "SELECT T.p_id, P.ID FROM ".$wpdb->prefix."player T, ".$wpdb->posts." P, ".$wpdb->prefix."bb2wp J WHERE T.p_id = J.tid AND P.ID = J.pid and J.prefix = 'p_'";
	          if ($teamposts = $wpdb->get_results($teampostsql)) {
//	            echo '<ul>';
	            foreach ($teamposts as $stad) {
	              $stadupdatesql = "UPDATE `".$wpdb->prefix."player` SET `ID` = '".$stad->ID."' WHERE p_id = ".$stad->p_id.";";
	//              print("<li>".$stadupdatesql."</li>");
	              if ( $wpdb->query($stadupdatesql) ) {
	                $result = true;
	              }
	              else {
	                $result = false;
	              }

	            } //end of foreach
//	            echo '</ul>';
	            if ( $result ) {
	              print("<div id=\"updated\" class=\"updated fade\"><p>The Player table updated with the WP IDs!</p></div>\n");
	            }
	          }//end of if sql was successful

	      } // end of if (isset($_POST['bblm_team_playertbl'])) {



				/****
				* END OF PLAYERS AND ROSTERS
				*
				* START OF ?
				*/


    /**
     *
     * MAIN PAGE CONTENT FOLLOWS
     */
?>

  <p>This screen should only be used when performing the cutover. Use each option <strong>only once</strong>.</p>

  <form name="bblm_cutovermain" method="post" id="post">
    <h3>Stadiums</h3>
    <ul>
       <li>First take a copy of the text at the top of the Stadiums page</li>
  	   <li><input type="submit" name="bblm_stadium_stadcpt" value="Convert Stadium Post Types" title="Convert the Stadium Post Types"/></li>
       <li>Now you can delete the Stadiums Page!</li>
       <li><input type="submit" name="bblm_stadium_teams" value="Update Stadium in Teams" title="Update Stadium in Teams"/></li>
       <li><input type="submit" name="bblm_stadium_match" value="Update Stadium in Matches" title="Update Stadium in Matches"/></li>
    </ul>


    <h3>Championship Cups</h3>
    <ul>
      <li>First take a copy of the text at the top of the Championships page</li>
      <li><input type="submit" name="bblm_cup_cupcpt" value="Convert Championship Post Types" title="Convert the Championship Post Types"/></li>
      <li>Now you can delete the Championship Cups Page!</li>
      <li>Also delete the BBBL sevens cup.... (sorry A)</li>
      <li><input type="submit" name="bblm_cup_comp" value="Update Championship Cups in Competitions" title="Update Championship Cups in Competitions"/></li>
    </ul>

    <h3>Seasons</h3>
    <ul>
      <li>First take a copy of the text at the top of the Seasons page.</li>
      <li><input type="submit" name="bblm_season_seacpt" value="Convert Season Post Types" title="Convert the Season Post Types"/></li>
      <li>Now you can delete the Seasons Page!</li>
      <li><input type="submit" name="bblm_season_comp" value="Update Seasons in Competitions" title="Update Seasons Cups in Competitions"/></li>

      <h3>Races</h3>
      <ul>
        <li>First take a copy of the text at the top of the Races page.</li>
        <li><input type="submit" name="bblm_race_racecpt" value="Convert Race Post Types" title="Convert the Race Post Types"/></li>
        <li>Now you can delete the Races Page!</li>
        <li><input type="submit" name="bblm_race_positions" value="Update Races in Positions" title="Update Races in Positions Table"/></li>
        <li><input type="submit" name="bblm_race_race2star" value="Update Races in Race2star" title="Update Races in Race2star Table"/></li>
      </ul>

			<h3>Competitions</h3>
      <ul>
        <li>First take a copy of the text at the top of the Competitons page.</li>
				<li>Add a new column ID (bigingt 20 - index on) to the *comp DB table</lI>
				<li>Define the Competition Types</lI>
        <li><input type="submit" name="bblm_comp_compcpt" value="Convert Competition Post Types" title="Convert the Competition Post Types"/></li>
        <li>Now you can delete the Competition Page!</li>
				<li><input type="submit" name="bblm_comp_comptbl" value="Update Competition Table" title="Update the Competition Table"/></li>
				<li>TODO- Update: matches, comp_brackets </li>
      </ul>

			<h3>Teams</h3>
			<ul>
				<li>First take a copy of the text at the top of the Teams page.</li>
				<li>Add a new column tID (bigingt 20 - index on) to the *team DB table</lI>
        <li><input type="submit" name="bblm_team_teamcpt" value="Convert Team Post Types" title="Convert the Team Post Types"/></li>
				<li>Now you can delete the Teams Page!</li>
				<li><input type="submit" name="bblm_team_teamtbl" value="Update Team Table" title="Update the Team Table"/></li>
			</ul>

			<h3>Players and Rosters</h3>
			<li>Add a new column ID (bigingt 20 - index on) to the *player DB table</li>
			<li><input type="submit" name="bblm_team_playercpt" value="Convert Player Post Types" title="Convert the Player Post Types"/></li>
			<li><input type="submit" name="bblm_team_rostercpt" value="Convert Roster Post Types" title="Convert the Roster Post Types"/></li>
			<li><input type="submit" name="bblm_team_playertbl" value="Update Player Table" title="Update the Player Table"/></li>
			<ul>
			</ul>

    <h3>Did You Know</h3>
    <ul>
      <li>Delete the Did You Know Page</li>
      <li>Import the existing DiD Yopu Knows - there is a reference availible here: -TODO!-</li>
    </ul>

    <h3>Next Steps</h3>
    <ul>
      <li>Create the main Nav Menu and assign it to the corect position</li>
      <li>Configure Widgets and their positions</li>

  </form>



</div>
