<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Makes the DB changes required to convert v1.X to the latest version
 *
 * @author 		Blacksnotling
 * @category 	Cutover
 * @package 	BBowlLeagueMan/Cutover
 * @version   1.4
 */
 ?>
 <div class="wrap">
 	<h2>League Manager Cutover</h2>
<?php
/**
 *
 * CONVERTING DYK ENTIRES INTO CPT
 */
if (isset($_POST['bblm_dyk_convert'])) {

  //First we grab a copy of the current entries
  $dyksql = "SELECT * FROM `".$wpdb->prefix."dyk`";
  //check that data was returned
  if ($dykposts = $wpdb->get_results($dyksql)) {

    //echo '</ul>';
    foreach ($dykposts as $dyk) {

      //print("<li>".$dyk->dyk_type." = ".$dyk->dyk_title." -> ".$dyk->dyk_desc."</li>");
      $post_id = wp_insert_post( array( 'post_title'=>$dyk->dyk_title, 'post_type'=>'bblm_dyk', 'post_content'=>$dyk->dyk_desc, 'post_status'=>'publish' ) );
      if( !is_wp_error( $post_id ) ){
        //the post is valid

        if ($dyk->dyk_type) {
          $dyktype = "Trivia";
        }
        else {
          $dyktype = "Fact";
        }


        add_post_meta( $post_id, 'dyk_type', $dyktype, true );
        $result = true;
      }
      else {
        $result = false;
      }

    } //end of foreach
    //echo '</ul>';

    if ( $result ) {
      print("<div id=\"updated\" class=\"updated fade\"><p>Did You Knows have been converted! <strong>Now you can delete the Did You know page if not done so already page!</strong></p></div>\n");
    }

  } // end of if DYK posts exist

} // END OF ONVERTING DYK ENTIRES INTO CPT
/**
 *
 * CONVERTING OWNER ENTIRES INTO CPT
 */
if (isset($_POST['bblm_owner_convert'])) {

	//First we grab a list of the current users
	$ownersql = "SELECT ID, display_name FROM `".$wpdb->prefix."users`";
	//We check something was returned
	if ($ownerlist = $wpdb->get_results($ownersql)) {

		//echo '<ul>';
		//Then we loop through them
		foreach ($ownerlist as $owl) {
			//We add the new owner to the database
			//echo '<li>'.$owl->ID.' - '.$owl->display_name.'</li>';
			$post_id = wp_insert_post( array( 'post_title'=>$owl->display_name, 'post_type'=>'bblm_owner', 'post_content'=>'', 'post_status'=>'publish' ) );

			if( !is_wp_error( $post_id ) ){

				//Then use that ID to update the teams table
				$oupdatesql = "UPDATE  `".$wpdb->prefix."team` SET  `ID` =  '".$post_id."' WHERE  `".$wpdb->prefix."team`.`ID` = ".$owl->ID;

				if ( $wpdb->query($oupdatesql) ) {
					$result = true;
				}
				else {

					//Updating the team table failed!
					$result = false;

				}


				echo '<ul><li>'.$oupdatesql.'</li></ul>';

				$result = true;

			}
			else {

				//failed to insert post
				$result = false;

			}


		}//end of foreach
		//echo '</ul>';
	}//end of if sql was correct
	if ( $result ) {
		print("<div id=\"updated\" class=\"updated fade\"><p>Owners have been converted!</p></div>\n");
	}


} // END OF ONVERTING OWNER ENTIRES INTO CPT

/**
 *
 * updating the teams db table with wpid
 */
if (isset($_POST['bblm_team_tbupdate'])) {
	$result = false;

	//First we grab a list of the current users
	$teamdeetssql = "SELECT T.t_id, T.t_name, J.pid AS WPID FROM `".$wpdb->prefix."team` T, ".$wpdb->prefix."bb2wp J WHERE J.prefix = 't_' AND J.tid = T.t_id";
	echo '<p>'.$teamdeetssql.'</p>';

	//We check something was returned
	if ($teamdeets = $wpdb->get_results($teamdeetssql)) {

		//echo '<ul>';
		//Then we loop through them
		foreach ($teamdeets as $tdeet) {

			//We use this value to update the team tables
			$teamupsql = "UPDATE `".$wpdb->prefix."team` SET `WPID` = '".$tdeet->WPID."' WHERE `".$wpdb->prefix."team`.`t_id` = ".$tdeet->t_id;
			//echo '<li>' . $teamupsql . '</li>';

			if ( $wpdb->query($teamupsql) ) {
				$result = true;
			}
			else {

				//Updating the team table failed!
				$result = false;

			}

		}
		//echo '</ul>';


	}

	//Update the DB table to with the new values

	if ( $result ) {
		print("<div id=\"updated\" class=\"updated fade\"><p>The Database Has been updated!</p></div>\n");
	}
	else {
		print("<div id=\"updated\" class=\"updated fade\"><p>Something went wrong!</p></div>");
	}

} // END OF Updateing Team Database table

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
	        $stadupdatesql = "UPDATE `".$wpdb->prefix."team` SET `stad_id` = '".$stad->ID."' WHERE `hdbb_team`.`t_id` = $stad->t_id;";
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
	          $stadupdatesql = "UPDATE `".$wpdb->prefix."match` SET `stad_id` = '".$stad->ID."' WHERE `hdbb_match`.`m_id` = ".$stad->m_id.";";
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

			/**
			 *
			 * updating the players db table with wpid
			 */
			if (isset($_POST['bblm_player_tbupdate'])) {
				$result = false;

				//First we grab a list of the current users
				$playerdeetssql = "SELECT T.p_id, T.p_name, J.pid AS WPID FROM `".$wpdb->prefix."player` T, ".$wpdb->prefix."bb2wp J WHERE J.prefix = 'p_' AND J.tid = T.p_id";
				//echo '<p>'.$playerdeetssql.'</p>';

				//We check something was returned
				if ($playerdeets = $wpdb->get_results($playerdeetssql)) {

					//echo '<ul>';
					//Then we loop through them
					foreach ($playerdeets as $pdeet) {

						//We use this value to update the team tables
						$playerupsql = "UPDATE `".$wpdb->prefix."player` SET `WPID` = '".$pdeet->WPID."' WHERE `".$wpdb->prefix."player`.`p_id` = ".$pdeet->p_id;
						//echo '<li>' . $playerupsql . '</li>';

						if ( $wpdb->query($playerupsql) ) {
							$result = true;
						}
						else {

							//Updating the team table failed!
							$result = false;

						}

					}
					//echo '</ul>';


				}

				//Update the DB table to with the new values

				if ( $result ) {
					print("<div id=\"updated\" class=\"updated fade\"><p>The Database Has been updated!</p></div>\n");
				}
				else {
					print("<div id=\"updated\" class=\"updated fade\"><p>Something went wrong!</p></div>");
				}

			} // END OF Updateing Team Database table

 /*
	* START OF Championship Cups conversion to CPT
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

	/*
	 * START OF Championship Cups Update in Competitions
	 */

	 if (isset($_POST['bblm_cup_cupincomp'])) {

		 $cuppostsql = "SELECT C.c_id, C.series_id, P.ID, P.post_title FROM ".$wpdb->prefix."comp C, ".$wpdb->prefix."bb2wp J, ".$wpdb->posts." P WHERE P.ID = J.pid AND C.series_id = J.tid AND J.prefix = 'series_'";
		 if ($cuppost = $wpdb->get_results($cuppostsql)) {
			 //echo '<ul>';
			 foreach ($cuppost as $cup) {
				 $compcupupdate = "UPDATE ".$wpdb->prefix."comp SET `series_id` = '".$cup->ID."' WHERE `c_id` = ".$cup->c_id.";";
				 //print("<li>".$compcupupdate."</li>");
				 if ( $wpdb->query($compcupupdate) ) {
					 $result = true;
				 }
				 else {
					 $result = false;
				 }
			 }

      //echo '</ul>';
			if ( $result ) {
				print("<div id=\"updated\" class=\"updated fade\"><p>Competitions have been updated with new Championship Cup Numbers!</strong></p></div>\n");
			}
		 }//end of if sql successful

	 } //end of if (isset($_POST['bblm_cup_cupincomp'])) {
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

				 /**
			    *
			    * UPDATING Team Awards Season TABLE FOR THE NEW SEASONS IDs
			    */
			   if (isset($_POST['bblm_season_awardteam'])) {

			       $comppostsql = "SELECT T.*, P.ID FROM ".$wpdb->prefix."awards_team_sea T, ".$wpdb->posts." P, ".$wpdb->prefix."bb2wp J WHERE T.sea_id = J.tid AND P.ID = J.pid and J.prefix = 'sea_'";
			       if ($teamposts = $wpdb->get_results($comppostsql)) {
			         foreach ($teamposts as $stad) {
			           $stadupdatesql = "UPDATE `".$wpdb->prefix."awards_team_sea` SET `sea_id` = '".$stad->ID."' WHERE `a_id` = '".$stad->a_id."' AND `t_id` = ".$stad->t_id." AND `sea_id` = ".$stad->sea_id;
			           if ( $wpdb->query($stadupdatesql) ) {
			             $result = true;
			           }
			           else {
			             $result = false;
			           }

			         } //end of foreach
			         if ( $result ) {
			           print("<div id=\"updated\" class=\"updated fade\"><p>The Team Awards for Seasons table updated with the new Seasons!</p></div>\n");
			         }
			       }//end of if sql was successful

			   } // end of if (isset($_POST['bblm_season_awardteam'])) {
					 /**
				    *
				    * UPDATING Player Awards Season TABLE FOR THE NEW SEASONS IDs
				    */
				   if (isset($_POST['bblm_season_awardplayer'])) {

				       $comppostsql = "SELECT T.*, P.ID FROM ".$wpdb->prefix."awards_player_sea T, ".$wpdb->posts." P, ".$wpdb->prefix."bb2wp J WHERE T.sea_id = J.tid AND P.ID = J.pid and J.prefix = 'sea_'";
				       if ($teamposts = $wpdb->get_results($comppostsql)) {
				         foreach ($teamposts as $stad) {
				           $stadupdatesql = "UPDATE `".$wpdb->prefix."awards_player_sea` SET `sea_id` = '".$stad->ID."' WHERE `a_id` = '".$stad->a_id."' AND `p_id` = ".$stad->p_id." AND `sea_id` = ".$stad->sea_id;
				           if ( $wpdb->query($stadupdatesql) ) {
				             $result = true;
				           }
				           else {
				             $result = false;
				           }

				         } //end of foreach
				         if ( $result ) {
				           print("<div id=\"updated\" class=\"updated fade\"><p>The Player Awards for Seasons table updated with the new Seasons!</p></div>\n");
				         }
				       }//end of if sql was successful

				   } // end of if (isset($_POST['bblm_season_awardplayer'])) {
					if ( isset( $_POST[ 'bblm_template_updatemeta' ] ) ) {

						$templatemetasql = 'SELECT * FROM `'.$wpdb->prefix.'postmeta` WHERE meta_key = "_wp_page_template" ORDER BY `hdbb_postmeta`.`meta_value` ASC';
						if ( $templatemeta = $wpdb->get_results( $templatemetasql ) ) {
							echo '<ul>';
							foreach ( $templatemeta as $tm ) {

								if ( "bb.core.comp.php" == $tm->meta_value ) {

									$newpath = BBLM_TEMPLATE_PATH . "archive-bblm_comp.php";
									echo '<li><strong>' . $tm->meta_id . ' / ' . $tm->post_id . '</strong> - <strong>Was:</strong> ' . $tm->meta_value . ' - <strong>Becomes:</strong> ' . $newpath . '</li>';
									update_post_meta( $tm->post_id, "_wp_page_template", $newpath );

								}
								else if ( "bb.core.graveyard.php" == $tm->meta_value ) {

									$newpath = BBLM_TEMPLATE_PATH . "bb.core.graveyard.php";
									echo '<li><strong>' . $tm->meta_id . '</strong> - <strong>Was:</strong> ' . $tm->meta_value . ' - <strong>Becomes:</strong> ' . $newpath . '</li>';
									update_post_meta( $tm->post_id, "_wp_page_template", $newpath );

								}
								else if ( "bb.view.stats.cas.php" == $tm->meta_value ) {

									$newpath = BBLM_TEMPLATE_PATH . "bb.view.stats.cas.php";
									echo '<li><strong>' . $tm->meta_id . '</strong> - <strong>Was:</strong> ' . $tm->meta_value . ' - <strong>Becomes:</strong> ' . $newpath . '</li>';
									update_post_meta( $tm->post_id, "_wp_page_template", $newpath );

								}
								else if ( "bb.view.stats.misc.php" == $tm->meta_value ) {

									$newpath = BBLM_TEMPLATE_PATH . "bb.view.stats.misc.php";
									echo '<li><strong>' . $tm->meta_id . '</strong> - <strong>Was:</strong> ' . $tm->meta_value . ' - <strong>Becomes:</strong> ' . $newpath . '</li>';
									update_post_meta( $tm->post_id, "_wp_page_template", $newpath );

								}
								else if ( "bb.view.stats.php" == $tm->meta_value ) {

									$newpath = BBLM_TEMPLATE_PATH . "bb.view.stats.php";
									echo '<li><strong>' . $tm->meta_id . '</strong> - <strong>Was:</strong> ' . $tm->meta_value . ' - <strong>Becomes:</strong> ' . $newpath . '</li>';
									update_post_meta( $tm->post_id, "_wp_page_template", $newpath );

								}
								else if ( "bb.view.stats.td.php" == $tm->meta_value ) {

									$newpath = BBLM_TEMPLATE_PATH . "bb.view.stats.td.php";
									echo '<li><strong>' . $tm->meta_id . '</strong> - <strong>Was:</strong> ' . $tm->meta_value . ' - <strong>Becomes:</strong> ' . $newpath . '</li>';
									update_post_meta( $tm->post_id, "_wp_page_template", $newpath );

								}
								else if ( "bb.core.awards.php" == $tm->meta_value ) {

									$newpath = BBLM_TEMPLATE_PATH . "archive-bblm_award.php";
									echo '<li><strong>' . $tm->meta_id . '</strong> - <strong>Was:</strong> ' . $tm->meta_value . ' - <strong>Becomes:</strong> ' . $newpath . '</li>';
									update_post_meta( $tm->post_id, "_wp_page_template", $newpath );

								}
								else if ( "bb.core.fixtures.php" == $tm->meta_value ) {

									$newpath = BBLM_TEMPLATE_PATH . "bb.core.fixtures.php";
									echo '<li><strong>' . $tm->meta_id . '</strong> - <strong>Was:</strong> ' . $tm->meta_value . ' - <strong>Becomes:</strong> ' . $newpath . '</li>';
									update_post_meta( $tm->post_id, "_wp_page_template", $newpath );

								}
								else if ( "bb.core.matches.php" == $tm->meta_value ) {

									$newpath = BBLM_TEMPLATE_PATH . "archive-bblm_match.php";
									echo '<li><strong>' . $tm->meta_id . '</strong> - <strong>Was:</strong> ' . $tm->meta_value . ' - <strong>Becomes:</strong> ' . $newpath . '</li>';
									update_post_meta( $tm->post_id, "_wp_page_template", $newpath );

								}
								else if ( "bb.core.races.php" == $tm->meta_value ) {

									$newpath = BBLM_TEMPLATE_PATH . "archive-bblm_race.php";
									echo '<li><strong>' . $tm->meta_id . '</strong> - <strong>Was:</strong> ' . $tm->meta_value . ' - <strong>Becomes:</strong> ' . $newpath . '</li>';
									update_post_meta( $tm->post_id, "_wp_page_template", $newpath );

								}
								else if ( "bb.core.teams.php" == $tm->meta_value ) {

									$newpath = BBLM_TEMPLATE_PATH . "archive-bblm_team.php";
									echo '<li><strong>' . $tm->meta_id . '</strong> - <strong>Was:</strong> ' . $tm->meta_value . ' - <strong>Becomes:</strong> ' . $newpath . '</li>';
									update_post_meta( $tm->post_id, "_wp_page_template", $newpath );

								}
								else if ( "bb.view.comp.php" == $tm->meta_value ) {

									$newpath = BBLM_TEMPLATE_PATH . "single-bblm_comp.php";
									echo '<li><strong>' . $tm->meta_id . '</strong> - <strong>Was:</strong> ' . $tm->meta_value . ' - <strong>Becomes:</strong> ' . $newpath . '</li>';
									update_post_meta( $tm->post_id, "_wp_page_template", $newpath );

								}
								else if ( "bb.view.match.php" == $tm->meta_value ) {

									$newpath = BBLM_TEMPLATE_PATH . "single-bblm_match.php";
									echo '<li><strong>' . $tm->meta_id . '</strong> - <strong>Was:</strong> ' . $tm->meta_value . ' - <strong>Becomes:</strong> ' . $newpath . '</li>';
									update_post_meta( $tm->post_id, "_wp_page_template", $newpath );

								}
								else if ( "bb.view.player.php" == $tm->meta_value ) {

									$newpath = BBLM_TEMPLATE_PATH . "single-bblm_player.php";
									echo '<li><strong>' . $tm->meta_id . '</strong> - <strong>Was:</strong> ' . $tm->meta_value . ' - <strong>Becomes:</strong> ' . $newpath . '</li>';
									update_post_meta( $tm->post_id, "_wp_page_template", $newpath );

								}
								else if ( "bb.view.roster.php" == $tm->meta_value ) {

									$newpath = BBLM_TEMPLATE_PATH . "single-bblm_roster.php";
									echo '<li><strong>' . $tm->meta_id . '</strong> - <strong>Was:</strong> ' . $tm->meta_value . ' - <strong>Becomes:</strong> ' . $newpath . '</li>';
									update_post_meta( $tm->post_id, "_wp_page_template", $newpath );

								}
								else if ( "bb.view.race.php" == $tm->meta_value ) {

									$newpath = BBLM_TEMPLATE_PATH . "single-bblm_race.php";
									echo '<li><strong>' . $tm->meta_id . '</strong> - <strong>Was:</strong> ' . $tm->meta_value . ' - <strong>Becomes:</strong> ' . $newpath . '</li>';
									update_post_meta( $tm->post_id, "_wp_page_template", $newpath );

								}
								else if ( "bb.view.team.php" == $tm->meta_value ) {

									$newpath = BBLM_TEMPLATE_PATH . "single-bblm_team.php";
									echo '<li><strong>' . $tm->meta_id . '</strong> - <strong>Was:</strong> ' . $tm->meta_value . ' - <strong>Becomes:</strong> ' . $newpath . '</li>';
									update_post_meta( $tm->post_id, "_wp_page_template", $newpath );

								}
								else if ( "bb.view.starplayer.php" == $tm->meta_value ) {

									$newpath = BBLM_TEMPLATE_PATH . "single-bblm_starplayers.php";
									echo '<li><strong>' . $tm->meta_id . '</strong> - <strong>Was:</strong> ' . $tm->meta_value . ' - <strong>Becomes:</strong> ' . $newpath . '</li>';
									update_post_meta( $tm->post_id, "_wp_page_template", $newpath );

								}
								else {
									echo '<li><strong>' . $tm->meta_id . '</strong> - <strong>Was:</strong> ' . $tm->meta_value . ' - <strong>Becomes:</strong> ' . $tm->meta_value . '</li>';
								}

							}
							echo '</ul>';
						}
					} //end of if ( isset( $_POST[ 'bblm_template_updatemeta' ] ) ) {
						/**
						 *
						 * UPDATING team_comp table with new column
						 */
						if (isset($_POST['bblm_comp_populate_teamcomp'])) {

								$comppostsql = "SELECT * FROM ".$wpdb->prefix."team_comp T, ".$wpdb->prefix."comp C WHERE C.c_id = T.c_id";
								if ($teamposts = $wpdb->get_results($comppostsql)) {
									foreach ($teamposts as $tc) {
										$tcupdate = "UPDATE `".$wpdb->prefix."team_comp` SET `tc_counts` = '".$tc->c_counts."' WHERE `".$wpdb->prefix."team_comp`.`tc_id` = ".$tc->tc_id.";";
										if ( $wpdb->query($tcupdate) ) {
											$result = true;
										}
										else {
											$result = false;
										}

									} //end of foreach
									if ( $result ) {
										print("<div id=\"updated\" class=\"updated fade\"><p>The team_comp table updated with the new value!</p></div>\n");
									}
								}//end of if sql was successful

						} // end of if (isset($_POST['bblm_comp_populate_teamcomp'])) {
							/**
							 *
							 * UPDATING team_comp table with new column
							 */
							if (isset($_POST['bblm_comp_populate_matchcol'])) {

									$comppostsql = "SELECT * FROM ".$wpdb->prefix."match M, ".$wpdb->prefix."comp C WHERE C.c_id = M.c_id";
									if ($teamposts = $wpdb->get_results($comppostsql)) {
										foreach ($teamposts as $tc) {
											$tcupdate = "UPDATE `".$wpdb->prefix."match` SET `m_counts` = '".$tc->c_counts."' WHERE `".$wpdb->prefix."match`.`m_id` = ".$tc->m_id.";";
											if ( $wpdb->query($tcupdate) ) {
												$result = true;
											}
											else {
												$result = false;
											}

										} //end of foreach
										if ( $result ) {
											print("<div id=\"updated\" class=\"updated fade\"><p>The team_comp table updated with the new value!</p></div>\n");
										}
									}//end of if sql was successful

							} // end of if (isset($_POST['bblm_comp_populate_matchcol'])) {
								/**
					 		    *
					 		    * UPDATING WP Posts TABLE FOR THE NEW COMPETITIONS CPT
					 		    */
					 		   if (isset($_POST['bblm_comp_compcpt'])) {

					 		     //$cuppostsql = "SELECT P.ID, R.sea_id, P.post_title, UNIX_TIMESTAMP(R.sea_sdate) AS sdate, UNIX_TIMESTAMP(R.sea_fdate) AS fdate FROM ".$wpdb->prefix."season R, ".$wpdb->posts." P, ".$wpdb->prefix."bb2wp J WHERE R.sea_id = J.tid AND P.ID = J.pid and J.prefix = 'sea_' ORDER BY P.ID ASC";
									 $cuppostsql = "SELECT P.ID, R.c_id, R.ct_id, R.c_counts, R.series_id, R.sea_id, R.c_pW, R.c_pL, R.c_pD, R.c_ptd, R.c_pcas, R.c_pround, R.c_showstandings, P.post_title, UNIX_TIMESTAMP(R.c_sdate) AS sdate, UNIX_TIMESTAMP(R.c_edate) AS fdate FROM ".$wpdb->prefix."comp R, ".$wpdb->posts." P, ".$wpdb->prefix."bb2wp J WHERE R.c_id = J.tid AND P.ID = J.pid and J.prefix = 'c_' ORDER BY P.ID ASC";
					 		       if ($stadposts = $wpdb->get_results($cuppostsql)) {
					 		         echo '<ul>';
					 		         foreach ($stadposts as $stad) {
					 		           $stadupdatesql = "UPDATE `".$wpdb->posts."` SET `post_parent` = '0', `post_type` = 'bblm_comp' WHERE `".$wpdb->posts."`.`ID` = '".$stad->ID."';";
	//				 		           print("<li>".$stadupdatesql."</li>");
					 		           if ( date("Y-m-d", $stad->fdate) == '1970-01-01' ) {
					 		             $fdate = '0000-00-00';
					 		           }
					 		           else {
					 		             $fdate = date("Y-m-d", $stad->fdate);
					 		           }
/*					 		           print("<li>Meta -> '".$stad->ID."', 'comp_sdate', '".date("Y-m-d", $stad->sdate)."'</li>");
					 		           print("<li>Meta -> '".$stad->ID."', 'comp_fdate', '".$fdate."'</li>");
												 print("<li>Meta -> '".$stad->ID."', 'comp_season', '".$stad->sea_id."'</li>");
												 print("<li>Meta -> '".$stad->ID."', 'comp_cup', '".$stad->series_id."'</li>");
												 print("<li>Meta -> '".$stad->ID."', 'comp_format', '".$stad->ct_id."'</li>");
												 print("<li>Meta -> '".$stad->ID."', 'comp_counts', '".$stad->c_counts."'</li>");
												 print("<li>Meta -> '".$stad->ID."', 'comp_pw', '".$stad->c_pW."'</li>");
												 print("<li>Meta -> '".$stad->ID."', 'comp_pl', '".$stad->c_pL."'</li>");
												 print("<li>Meta -> '".$stad->ID."', 'comp_pd', '".$stad->c_pD."'</li>");
												 print("<li>Meta -> '".$stad->ID."', 'comp_ptd', '".$stad->c_ptd."'</li>");
												 print("<li>Meta -> '".$stad->ID."', 'comp_pcas', '".$stad->c_pcas."'</li>");
												 print("<li>Meta -> '".$stad->ID."', 'comp_pround', '".$stad->c_pround."'</li>");
												 print("<li>Meta -> '".$stad->ID."', 'comp_showstandings', '".$stad->c_showstandings."'</li>");
*/

					 		           if ( $wpdb->query($stadupdatesql) ) {
					 		             $result = true;
					 		             add_post_meta( $stad->ID, 'comp_sdate', date("Y-m-d", $stad->sdate), true );
					 		             add_post_meta( $stad->ID, 'comp_fdate', $fdate, true );
													 add_post_meta( $stad->ID, 'comp_season', $stad->sea_id, true );
													 add_post_meta( $stad->ID, 'comp_cup', $stad->series_id, true );
													 add_post_meta( $stad->ID, 'comp_format', $stad->ct_id, true );
													 add_post_meta( $stad->ID, 'comp_counts', $stad->c_counts, true );
													 add_post_meta( $stad->ID, 'comp_pw', $stad->c_pW, true );
													 add_post_meta( $stad->ID, 'comp_pl', $stad->c_pL, true );
													 add_post_meta( $stad->ID, 'comp_pd', $stad->c_pD, true );
													 add_post_meta( $stad->ID, 'comp_ptd', $stad->c_ptd, true );
													 add_post_meta( $stad->ID, 'comp_pcas', $stad->c_pcas, true );
													 add_post_meta( $stad->ID, 'comp_pround', $stad->c_pround, true );
													 add_post_meta( $stad->ID, 'comp_showstandings', $stad->c_showstandings, true );

													 $stadupdatesql = "UPDATE `".$wpdb->prefix."comp` SET `WPID` = '".$stad->ID."' WHERE `c_id` = '".$stad->c_id."'";
													 $wpdb->query($stadupdatesql);
					 		           }
					 		           else {
					 		             $result = false;
					 		           }

					 		         } //end of foreach
					 		 //        echo '</ul>';
					 		         if ( $result ) {
					 		           print("<div id=\"updated\" class=\"updated fade\"><p>Posts table updated for Competitions! <strong>Now you can delete the Competitions page!</strong></p></div>\n");
					 		         }
					 		       }//end of if sql was successful

					 		   } //end of if (isset($_POST['bblm_comp_compcpt']))
								 /**
									*
									* UPDATING Team Awards Comp TABLE FOR THE NEW comp IDs
									*/
								 if (isset($_POST['bblm_comp_awardteam'])) {

										 $comppostsql = "SELECT T.*, P.ID FROM ".$wpdb->prefix."awards_team_comp T, ".$wpdb->posts." P, ".$wpdb->prefix."bb2wp J WHERE T.c_id = J.tid AND P.ID = J.pid and J.prefix = 'c_'";
										 if ($teamposts = $wpdb->get_results($comppostsql)) {
											 foreach ($teamposts as $stad) {
												 $stadupdatesql = "UPDATE `".$wpdb->prefix."awards_team_comp` SET `c_id` = '".$stad->ID."' WHERE `a_id` = '".$stad->a_id."' AND `t_id` = ".$stad->t_id." AND `c_id` = ".$stad->c_id;
												 if ( $wpdb->query($stadupdatesql) ) {
													 $result = true;
												 }
												 else {
													 $result = false;
												 }

											 } //end of foreach
											 if ( $result ) {
												 print("<div id=\"updated\" class=\"updated fade\"><p>The Team Awards for Competitions table updated with the new Competition!</p></div>\n");
											 }
										 }//end of if sql was successful

								 } // end of if (isset($_POST['bblm_comp_awardteam'])) {
									 /**
										*
										* UPDATING Player Awards Comp TABLE FOR THE NEW comp IDs
										*/
									 if (isset($_POST['bblm_comp_awardplayer'])) {

											 $comppostsql = "SELECT T.*, P.ID FROM ".$wpdb->prefix."awards_player_comp T, ".$wpdb->posts." P, ".$wpdb->prefix."bb2wp J WHERE T.c_id = J.tid AND P.ID = J.pid and J.prefix = 'c_'";
											 if ($teamposts = $wpdb->get_results($comppostsql)) {
												 foreach ($teamposts as $stad) {
													 $stadupdatesql = "UPDATE `".$wpdb->prefix."awards_player_comp` SET `c_id` = '".$stad->ID."' WHERE `a_id` = '".$stad->a_id."' AND `p_id` = ".$stad->p_id." AND `c_id` = ".$stad->c_id;
													 if ( $wpdb->query($stadupdatesql) ) {
														 $result = true;
													 }
													 else {
														 $result = false;
													 }

												 } //end of foreach
												 if ( $result ) {
													 print("<div id=\"updated\" class=\"updated fade\"><p>The Player Awards for Competitions table updated with the new Competition!</p></div>\n");
												 }
											 }//end of if sql was successful

									 } // end of if (isset($_POST['bblm_comp_awardplayer'])) {
										 /**
											*
											* UPDATING Comp Brackets TABLE FOR THE NEW comp IDs
											*/
										 if (isset($_POST['bblm_comp_brackets'])) {

												 $comppostsql = "SELECT T.*, P.ID FROM ".$wpdb->prefix."comp_brackets T, ".$wpdb->posts." P, ".$wpdb->prefix."bb2wp J WHERE T.c_id = J.tid AND P.ID = J.pid and J.prefix = 'c_'";
												 if ($teamposts = $wpdb->get_results($comppostsql)) {
													 foreach ($teamposts as $stad) {
														 $stadupdatesql = "UPDATE `".$wpdb->prefix."comp_brackets` SET `c_id` = '".$stad->ID."' WHERE `cb_id` = '".$stad->cb_id."'";
														 if ( $wpdb->query($stadupdatesql) ) {
															 $result = true;
														 }
														 else {
															 $result = false;
														 }

													 } //end of foreach
													 if ( $result ) {
														 print("<div id=\"updated\" class=\"updated fade\"><p>The Competition Brackets table updated with the new Competition!</p></div>\n");
													 }
												 }//end of if sql was successful

										 } // end of if (isset($_POST['bblm_comp_brackets'])) {
											 /**
												*
												* UPDATING Comp Brackets TABLE FOR THE NEW comp IDs
												*/
											 if (isset($_POST['bblm_comp_fixtures'])) {

													 $comppostsql = "SELECT T.*, P.ID FROM ".$wpdb->prefix."fixture T, ".$wpdb->posts." P, ".$wpdb->prefix."bb2wp J WHERE T.c_id = J.tid AND P.ID = J.pid and J.prefix = 'c_'";
													 if ($teamposts = $wpdb->get_results($comppostsql)) {
														 foreach ($teamposts as $stad) {
															 $stadupdatesql = "UPDATE `".$wpdb->prefix."fixture` SET `c_id` = '".$stad->ID."' WHERE `f_id` = '".$stad->f_id."'";
															 if ( $wpdb->query($stadupdatesql) ) {
																 $result = true;
															 }
															 else {
																 $result = false;
															 }

														 } //end of foreach
														 if ( $result ) {
															 print("<div id=\"updated\" class=\"updated fade\"><p>The Fixtures table updated with the new Competition!</p></div>\n");
														 }
													 }//end of if sql was successful

											 } // end of if (isset($_POST['bblm_comp_fixtures'])) {
												 /**
													*
													* UPDATING Comp Brackets TABLE FOR THE NEW comp IDs
													*/
												 if (isset($_POST['bblm_comp_match'])) {

														 $comppostsql = "SELECT T.*, P.ID FROM ".$wpdb->prefix."match T, ".$wpdb->posts." P, ".$wpdb->prefix."bb2wp J WHERE T.c_id = J.tid AND P.ID = J.pid and J.prefix = 'c_'";
														 if ($teamposts = $wpdb->get_results($comppostsql)) {
															 foreach ($teamposts as $stad) {
																 $stadupdatesql = "UPDATE `".$wpdb->prefix."match` SET `c_id` = '".$stad->ID."' WHERE `m_id` = '".$stad->m_id."'";
																 if ( $wpdb->query($stadupdatesql) ) {
																	 $result = true;
																 }
																 else {
																	 $result = false;
																 }

															 } //end of foreach
															 if ( $result ) {
																 print("<div id=\"updated\" class=\"updated fade\"><p>The Matches table updated with the new Competition!</p></div>\n");
															 }
														 }//end of if sql was successful

												 } // end of if (isset($_POST['bblm_comp_match'])) {
													 /**
														*
														* UPDATING Comp Brackets TABLE FOR THE NEW comp IDs
														*/
													 if (isset($_POST['bblm_comp_teamcomp'])) {

															 $comppostsql = "SELECT T.*, P.ID FROM ".$wpdb->prefix."team_comp T, ".$wpdb->posts." P, ".$wpdb->prefix."bb2wp J WHERE T.c_id = J.tid AND P.ID = J.pid and J.prefix = 'c_'";
															 if ($teamposts = $wpdb->get_results($comppostsql)) {
																 foreach ($teamposts as $stad) {
																	 $stadupdatesql = "UPDATE `".$wpdb->prefix."team_comp` SET `c_id` = '".$stad->ID."' WHERE `tc_id` = '".$stad->tc_id."'";
																	 if ( $wpdb->query($stadupdatesql) ) {
																		 $result = true;
																	 }
																	 else {
																		 $result = false;
																	 }

																 } //end of foreach
																 if ( $result ) {
																	 print("<div id=\"updated\" class=\"updated fade\"><p>The Matches table updated with the new Competition!</p></div>\n");
																 }
															 }//end of if sql was successful

													 } // end of if (isset($_POST['bblm_comp_teamcomp'])) {
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

													    } // end of if (isset($_POST['bblm_race_teams'])) {
																/**
														     *
														     * UPDATING Teams TABLE FOR THE NEW Race IDs
														     */
														    if (isset($_POST['bblm_race_teams'])) {

														      $teampostsql = "SELECT T.r_id, T.t_id, P.ID FROM ".$wpdb->prefix."team T, ".$wpdb->posts." P, ".$wpdb->prefix."bb2wp J WHERE T.r_id = J.tid AND P.ID = J.pid and J.prefix = 'r_'";
														        if ($teamposts = $wpdb->get_results($teampostsql)) {
													//	          echo '<ul>';
														          foreach ($teamposts as $stad) {
														            $stadupdatesql = "UPDATE `".$wpdb->prefix."team` SET `r_id` = '".$stad->ID."' WHERE t_id = '".$stad->t_id."';";
													//	            print("<li>".$stad->t_id." = ".$stad->r_id." -> ".$stad->ID."</li>");
													//	            print("<li>".$stadupdatesql."</li>");

														            if ( $wpdb->query($stadupdatesql) ) {
																		      $result = true;
														            }
														            else {
														              $result = false;
														            }

														          } //end of foreach
													//	          echo '</ul>';
														          if ( $result ) {
														            print("<div id=\"updated\" class=\"updated fade\"><p>Teams table updated with the new Races!</p></div>\n");
														          }
														        }//end of if sql was successful

														    } // end of if (isset($_POST['bblm_race_teams'])) {

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
																	/**
																		*
																		* UPDATING WP Posts TABLE FOR THE NEW Matches CPT
																		*/
																	 if (isset($_POST['bblm_match_matchcpt'])) {

																		 $cuppostsql = "SELECT P.ID, R.m_id, P.post_title, P.post_name FROM ".$wpdb->prefix."match R, ".$wpdb->posts." P, ".$wpdb->prefix."bb2wp J WHERE R.m_id = J.tid AND P.ID = J.pid and J.prefix = 'm_' ORDER BY P.ID ASC";
																			 if ($stadposts = $wpdb->get_results($cuppostsql)) {
										//							        echo '<ul>';
																				 foreach ($stadposts as $stad) {
																					 $stadupdatesql = "UPDATE `".$wpdb->posts."` SET `post_parent` = '0', `post_type` = 'bblm_match' WHERE `".$wpdb->posts."`.`ID` = '".$stad->ID."';";
											//						          print("<li>".$stadupdatesql."</li>");

																	          if ( $wpdb->query($stadupdatesql) ) {
																						 $result = true;

																					 }
																					 else {
																						 $result = false;
																					 }

																				 } //end of foreach
		//															          echo '</ul>';
																				 if ( $result ) {
																					 print("<div id=\"updated\" class=\"updated fade\"><p>Posts table updated for Matches Page! <strong>Now you can delete the Matches page!</strong></p></div>\n");
																				 }
																			 }//end of if sql was successful

																	 } //end of if (isset($_POST['bblm_match_matchcpt']))
																	 /**
																		*
																		* updating the matches db table with wpid
																		*/
																	 if (isset($_POST['bblm_match_tbupdate'])) {
																		 $result = false;

																		 //First we grab a list of the current users
																		 $playerdeetssql = "SELECT T.m_id, J.pid AS WPID FROM `".$wpdb->prefix."match` T, ".$wpdb->prefix."bb2wp J WHERE J.prefix = 'm_' AND J.tid = T.m_id";
																		 //echo '<p>'.$playerdeetssql.'</p>';

																		 //We check something was returned
																		 if ($playerdeets = $wpdb->get_results($playerdeetssql)) {

																			 echo '<ul>';
																			 //Then we loop through them
																			 foreach ($playerdeets as $pdeet) {

																				 //We use this value to update the team tables
																				 $playerupsql = "UPDATE `".$wpdb->prefix."match` SET `WPID` = '".$pdeet->WPID."' WHERE `".$wpdb->prefix."match`.`m_id` = ".$pdeet->m_id;
																				 echo '<li>' . $playerupsql . '</li>';

																				 if ( $wpdb->query($playerupsql) ) {
																					 $result = true;
																				 }
																				 else {

																					 //Updating the team table failed!
																					 $result = false;

																				 }

																			 }
																			 echo '</ul>';


																		 }

																		 //Update the DB table to with the new values

																		 if ( $result ) {
																			 print("<div id=\"updated\" class=\"updated fade\"><p>The Database Has been updated!</p></div>\n");
																		 }
																		 else {
																			 print("<div id=\"updated\" class=\"updated fade\"><p>Something went wrong!</p></div>");
																		 }

																	 } // END OF Updateing Team Database table
																	 /**
																		*
																		* updating the comp brackets db table for matches
																		*/
																	 if (isset($_POST['bblm_match_compbracket'])) {
																		 $result = false;

																		 //First we grab a list of the current users
																		 $playerdeetssql = "SELECT M.m_id AS mid, M.WPID AS MWPID, T.cb_id FROM `".$wpdb->prefix."match` M, `".$wpdb->prefix."comp_brackets` T WHERE M.m_id = T.m_id ORDER BY M.m_id";
																		 //echo '<p>'.$playerdeetssql.'</p>';

																		 //We check something was returned
																		 if ($playerdeets = $wpdb->get_results($playerdeetssql)) {

																			 echo '<ul>';
																			 //Then we loop through them
																			 foreach ($playerdeets as $pdeet) {

																				 //We use this value to update the team tables
																				 $playerupsql = "UPDATE `".$wpdb->prefix."comp_brackets` SET `m_id` = '".$pdeet->MWPID."' WHERE cb_id = ".$pdeet->cb_id;
																				 echo '<li>' . $playerupsql . '</li>';

																				 if ( $wpdb->query($playerupsql) ) {
																					 $result = true;
																				 }
																				 else {

																					 //Updating the team table failed!
																					 $result = false;

																				 }

																			 }
																			 echo '</ul>';


																		 }

																		 //Update the DB table to with the new values

																		 if ( $result ) {
																			 print("<div id=\"updated\" class=\"updated fade\"><p>The Database Has been updated!</p></div>\n");
																		 }
																		 else {
																			 print("<div id=\"updated\" class=\"updated fade\"><p>Something went wrong!</p></div>");
																		 }

																	 } // END OF bblm_match_compbracket
																	 /**
																		*
																		* updating the match playrr db table for matches
																		*/
																	 if (isset($_POST['bblm_match_matchplayer'])) {
																		 $result = false;

																		 //First we grab a list of the current users
																		 $playerdeetssql = "SELECT M.m_id AS mid, M.WPID AS MWPID, T.p_id FROM `".$wpdb->prefix."match` M, `".$wpdb->prefix."match_player` T WHERE M.m_id = T.m_id ORDER BY M.m_id";
																		 //echo '<p>'.$playerdeetssql.'</p>';

																		 //We check something was returned
																		 if ($playerdeets = $wpdb->get_results($playerdeetssql)) {

																			 echo '<ul>';
																			 //Then we loop through them
																			 foreach ($playerdeets as $pdeet) {

																				 //We use this value to update the team tables
																				 $playerupsql = "UPDATE `".$wpdb->prefix."match_player` SET `m_id` = '".$pdeet->MWPID."' WHERE m_id = ".$pdeet->mid." AND p_id = ".$pdeet->p_id;
																				 echo '<li>' . $playerupsql . '</li>';

																				 if ( $wpdb->query($playerupsql) ) {
																					 $result = true;
																				 }
																				 else {

																					 //Updating the team table failed!
																					 $result = false;

																				 }

																			 }
																			 echo '</ul>';


																		 }

																		 //Update the DB table to with the new values

																		 if ( $result ) {
																			 print("<div id=\"updated\" class=\"updated fade\"><p>The Database Has been updated!</p></div>\n");
																		 }
																		 else {
																			 print("<div id=\"updated\" class=\"updated fade\"><p>Something went wrong!</p></div>");
																		 }

																	 } // END OF bblm_match_matchplayer
																	 /**
																		*
																		* updating the match team db table for matches
																		*/
																	 if (isset($_POST['bblm_match_matchteam'])) {
																		 $result = false;

																		 //First we grab a list of the current users
																		 $playerdeetssql = "SELECT M.m_id AS mid, M.WPID AS MWPID, T.t_id FROM `".$wpdb->prefix."match` M, `".$wpdb->prefix."match_team` T WHERE M.m_id = T.m_id ORDER BY M.m_id DESC";
																		 //echo '<p>'.$playerdeetssql.'</p>';

																		 //We check something was returned
																		 if ($playerdeets = $wpdb->get_results($playerdeetssql)) {

																			 echo '<ul>';
																			 //Then we loop through them
																			 foreach ($playerdeets as $pdeet) {

																				 //We use this value to update the team tables
																				 $playerupsql = "UPDATE `".$wpdb->prefix."match_team` SET `m_id` = '".$pdeet->MWPID."' WHERE m_id = ".$pdeet->mid." AND t_id = ".$pdeet->t_id;
																				 echo '<li>' . $playerupsql . '</li>';

																				 if ( $wpdb->query($playerupsql) ) {
																					 $result = true;
																				 }
																				 else {

																					 //Updating the team table failed!
																					 $result = false;

																				 }

																			 }
																			 echo '</ul>';


																		 }

																		 //Update the DB table to with the new values

																		 if ( $result ) {
																			 print("<div id=\"updated\" class=\"updated fade\"><p>The Database Has been updated!</p></div>\n");
																		 }
																		 else {
																			 print("<div id=\"updated\" class=\"updated fade\"><p>Something went wrong!</p></div>");
																		 }

																	 } // END OF bblm_match_matchteam
																	 /**
																		*
																		* updating the player fate db table for matches
																		*/
																	 if (isset($_POST['bblm_match_playerfate'])) {
																		 $result = false;

																		 //First we grab a list of the current users
																		 $playerdeetssql = "SELECT M.m_id AS mid, M.WPID AS MWPID, T.p_id, T.f_id FROM `".$wpdb->prefix."match` M, `".$wpdb->prefix."player_fate` T WHERE M.m_id = T.m_id ORDER BY M.m_id DESC";
																		 //echo '<p>'.$playerdeetssql.'</p>';

																		 //We check something was returned
																		 if ($playerdeets = $wpdb->get_results($playerdeetssql)) {

																			 echo '<ul>';
																			 //Then we loop through them
																			 foreach ($playerdeets as $pdeet) {

																				 //We use this value to update the team tables
																				 $playerupsql = "UPDATE `".$wpdb->prefix."player_fate` SET `m_id` = '".$pdeet->MWPID."' WHERE f_id = ".$pdeet->f_id." AND p_id = ".$pdeet->p_id;
																				 echo '<li>' . $playerupsql . '</li>';

																				 if ( $wpdb->query($playerupsql) ) {
																					 $result = true;
																				 }
																				 else {

																					 //Updating the team table failed!
																					 $result = false;

																				 }

																			 }
																			 echo '</ul>';


																		 }

																		 //Update the DB table to with the new values

																		 if ( $result ) {
																			 print("<div id=\"updated\" class=\"updated fade\"><p>The Database Has been updated!</p></div>\n");
																		 }
																		 else {
																			 print("<div id=\"updated\" class=\"updated fade\"><p>Something went wrong!</p></div>");
																		 }

																	 } // END OF bblm_match_playerfate

																	 if (isset($_POST['bblm_legacy_comp_meta'])) {
																		 $result = false;

																		 //First we grab a list of the current users
																		 $comppostsql = "SELECT P.ID FROM ".$wpdb->posts." P WHERE P.post_type = 'bblm_comp'";
																		 //echo '<p>'.$comppostsql.'</p>';

																		 //We check something was returned
																		 if ($comppost = $wpdb->get_results($comppostsql)) {

																			 //Then we loop through them
																			 foreach ($comppost as $pdeet) {

																				 if ( add_post_meta( $pdeet->ID, 'comp_legacy', '1', true ) ) {
																					 $result = true;
																				 }
																				 else {

																					 //Updating the team table failed!
																					 $result = false;

																				 }

																			 }


																		 }

																		 //Update the DB table to with the new values

																		 if ( $result ) {
																			 print("<div id=\"updated\" class=\"updated fade\"><p>The Meta has been added</p></div>\n");
																		 }
																		 else {
																			 print("<div id=\"updated\" class=\"updated fade\"><p>Something went wrong!</p></div>");
																		 }

																	 } // END OF bblm_legacy_comp_meta
																	 /*
																	 * updating the player legacy flag
																	 */
																	if (isset($_POST['bblm_legacy_player_flag'])) {
																		$result = false;

																		//First we grab a list of the current users
																		$playerdeetssql = "SELECT P.p_id FROM `".$wpdb->prefix."player` P  WHERE t_id != 52";
																		//echo '<p>'.$playerdeetssql.'</p>';

																		//We check something was returned
																		if ($playerdeets = $wpdb->get_results($playerdeetssql)) {

																			//echo '<ul>';
																			//Then we loop through them
																			foreach ($playerdeets as $pdeet) {

																				//We use this value to update the team tables
																				$playerupsql = "UPDATE `".$wpdb->prefix."player` SET `p_legacy` = '1' WHERE p_id = ".$pdeet->p_id;
																				//echo '<li>' . $playerupsql . '</li>';

																				if ( $wpdb->query($playerupsql) ) {
																					$result = true;
																				}
																				else {

																					//Updating the team table failed!
																					$result = false;

																				}

																			}
																			//echo '</ul>';


																		}

																		//Update the DB table to with the new values

																		if ( $result ) {
																			print("<div id=\"updated\" class=\"updated fade\"><p>The Database Has been updated!</p></div>\n");
																		}
																		else {
																			print("<div id=\"updated\" class=\"updated fade\"><p>Something went wrong!</p></div>");
																		}

																	} // END OF bblm_legacy_player_flag

																	/*
																	* updating the team legacy flag
																	*/
																 if (isset($_POST['bblm_legacy_team_flag'])) {
																	 $result = false;

																	 //First we grab a list of the current users
																	 $playerdeetssql = "SELECT T.t_id FROM `".$wpdb->prefix."team` T";
																	 //echo '<p>'.$playerdeetssql.'</p>';

																	 //We check something was returned
																	 if ($playerdeets = $wpdb->get_results($playerdeetssql)) {

																		 //echo '<ul>';
																		 //Then we loop through them
																		 foreach ($playerdeets as $pdeet) {

																			 //We use this value to update the team tables
																			 $playerupsql = "UPDATE `".$wpdb->prefix."team` SET `t_legacy` = '1' WHERE t_id = ".$pdeet->t_id;
																			 //echo '<li>' . $playerupsql . '</li>';

																			 if ( $wpdb->query($playerupsql) ) {
																				 $result = true;
																			 }
																			 else {

																				 //Updating the team table failed!
																				 $result = false;

																			 }

																		 }
																		 //echo '</ul>';


																	 }

																	 //Update the DB table to with the new values

																	 if ( $result ) {
																		 print("<div id=\"updated\" class=\"updated fade\"><p>The Database Has been updated!</p></div>\n");
																	 }
																	 else {
																		 print("<div id=\"updated\" class=\"updated fade\"><p>Something went wrong!</p></div>");
																	 }

																 } // END OF bblm_legacy_team_flag

																 /*
																 * updating the match legacy flag
																 */
																if (isset($_POST['bblm_legacy_match_flag'])) {
																	$result = false;

																	//First we grab a list of the current users
																	$playerdeetssql = "SELECT M.m_id FROM `".$wpdb->prefix."match` M";
																	//echo '<p>'.$playerdeetssql.'</p>';

																	//We check something was returned
																	if ($playerdeets = $wpdb->get_results($playerdeetssql)) {

																		//echo '<ul>';
																		//Then we loop through them
																		foreach ($playerdeets as $pdeet) {

																			//We use this value to update the team tables
																			$playerupsql = "UPDATE `".$wpdb->prefix."match` SET `m_legacy` = '1' WHERE m_id = ".$pdeet->m_id;
																			//echo '<li>' . $playerupsql . '</li>';

																			if ( $wpdb->query($playerupsql) ) {
																				$result = true;
																			}
																			else {

																				//Updating the team table failed!
																				$result = false;

																			}

																		}
																		//echo '</ul>';


																	}

																	//Update the DB table to with the new values

																	if ( $result ) {
																		print("<div id=\"updated\" class=\"updated fade\"><p>The Database Has been updated!</p></div>\n");
																	}
																	else {
																		print("<div id=\"updated\" class=\"updated fade\"><p>Something went wrong!</p></div>");
																	}

																} // END OF bblm_legacy_match_flag

																if (isset($_POST['bblm_race_status_meta'])) {
																	$result = false;

																	//First we grab a list of the current users
																	$comppostsql = "SELECT P.ID FROM ".$wpdb->posts." P WHERE P.post_type = 'bblm_race'";
																	//echo '<p>'.$comppostsql.'</p>';

																	//We check something was returned
																	if ($comppost = $wpdb->get_results($comppostsql)) {

																		//Then we loop through them
																		foreach ($comppost as $pdeet) {

																			if ( add_post_meta( $pdeet->ID, 'race_rstatus', '1', true ) ) {
																				$result = true;
																			}
																			else {

																				//Updating the team table failed!
																				$result = false;

																			}

																		}


																	}

																	//Update the DB table to with the new values

																	if ( $result ) {
																		print("<div id=\"updated\" class=\"updated fade\"><p>The Meta has been added</p></div>\n");
																	}
																	else {
																		print("<div id=\"updated\" class=\"updated fade\"><p>Something went wrong!</p></div>");
																	}

																} // END OF bblm_race_status_meta
																/*
																* Updating all teams Current Team Value
																*/
															 if (isset($_POST['bblm_team_ctv'])) {
																 $result = false;

																 //First we grab a list of the current users
																 $playerdeetssql = "SELECT T.t_id FROM `".$wpdb->prefix."team` T";
																 //echo '<p>'.$playerdeetssql.'</p>';

																 //We check something was returned
																 if ($playerdeets = $wpdb->get_results($playerdeetssql)) {

																	 //Then we loop through them
																	 foreach ($playerdeets as $pdeet) {

																		 //We use this value to update the team tables
																		 bblm_update_tv( $pdeet->t_id );
																		 //echo '<li>' . $playerupsql . '</li>';

																			 $result = true;

																	 }

																 }

																 if ( $result ) {
																	 print("<div id=\"updated\" class=\"updated fade\"><p>The Database Has been updated!</p></div>\n");
																 }

															 } // END OF bblm_team_ctv
															 /*
															 * Update SPP of players
															 */
															if (isset($_POST['bblm_player_spp_update'])) {
																$result = false;

																//First we grab a list of the current users
																$playerdeetssql = "SELECT P.p_id, P.p_spp FROM `".$wpdb->prefix."player` P  WHERE t_id != 52";

																//We check something was returned
																if ($playerdeets = $wpdb->get_results($playerdeetssql)) {

																	//echo '<ul>';
																	//Then we loop through them
																	foreach ($playerdeets as $pdeet) {

																		//We use this value to update the team tables
																		$playerupsql = "UPDATE `".$wpdb->prefix."player` SET `p_cspp` = '" . $pdeet->p_spp . "' WHERE p_id = ".$pdeet->p_id;
																		//echo '<li>' . $playerupsql . '</li>';

																		if ( $wpdb->query($playerupsql) ) {
																			$result = true;
																		}
																		else {

																			//Updating the team table failed!
																			$result = false;

																		}

																	}
																	//echo '</ul>';


																}

																//Update the DB table to with the new values

																if ( $result ) {
																	print("<div id=\"updated\" class=\"updated fade\"><p>The Database Has been updated!</p></div>\n");
																}
																else {
																	print("<div id=\"updated\" class=\"updated fade\"><p>Something went wrong!</p></div>");
																}

															} // END OF bblm_player_spp_update
															/**
																  *
																  * UPDATING MAIN POST TABLE FOR THE STADIUM CPT
																  */
																if (isset($_POST['bblm_star_starcpt'])) {
																	$bblm_team_star = bblm_get_star_player_team();

																  $stadpostsql = "SELECT P.ID, R.*, R.WPID AS SWPID, P.post_title FROM ".$wpdb->prefix."player R, ".$wpdb->posts." P, ".$wpdb->prefix."bb2wp J WHERE R.p_id = J.tid AND P.ID = J.pid and J.prefix = 'p_' AND R.t_id = $bblm_team_star ORDER BY P.ID ASC";
																    if ($stadposts = $wpdb->get_results($stadpostsql)) {
																      //echo '<ul>';
																      foreach ($stadposts as $stad) {
																        $stadupdatesql = "UPDATE `".$wpdb->posts."` SET `post_parent` = '0', `post_type` = 'bblm_star' WHERE `".$wpdb->posts."`.`ID` = '".$stad->SWPID."';";
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
																        print("<div id=\"updated\" class=\"updated fade\"><p>Posts table updated for Star Players! <strong>Now you can delete the star platers page!</strong></p></div>\n");
																      }
																    }//end of if sql was successful
																} //end of if (isset($_POST['bblm_star_starcpt'])) {

/**
 *
 * MAIN PAGE CONTENT FOLLOWS
 */
?>
<p>This screen should only be used when performing the cutover. Use each option <strong>only once</strong>.</p>

<form name="bblm_cutovermain" method="post" id="post">

	<h2>1.x -> 1.7</h2>
  <h3>General</h3>
  <ul>
		<li>Create a new page called "front page" (if it does not already exist)</li>
		<li>Configure the static front page in settings -> reading</li>
		<li>remove custom template from the news page</li>
		<li>Create main Navigation Menu and insert along the top menu area</li>
		<li>Enter league name into settings</li>
		<li>Enter other new settings (Archive descriptions)</li>
		<li>Activate HDWSBBL Plugin</li>
		<li>Validate Settings</li>
    <li>Make sure all the widgets are configured (including DYK)</li>
  </ul>


  <h3>Did You Know</h3>
  <ul>
    <li><input type="submit" name="bblm_dyk_convert" value="Convert DYK" title="Convert the DYK entries"/></li>
    <li>Delete the &quot;Did You Know&quot; Page. - make sure you add it back into the Menu</li>
    <li>You can now delete the DYK table if you wish!</li>
  </ul>

	<h2>1.7 -> 1.8</h2>
	<h3>General</h3>
	<ul>
		<li>Update the settings page, with the text that goes at the top of the Owners / Coaches page</li>
		<li>Double check the teams assigned to users. The star players team (52) should be assigned to user 1, and Mycenaen Marauders (83) should be assigned to the correct coach</li>
	</ul>

	<h3>Owners (NEW)</h3>
	<ul>
		<li><input type="submit" name="bblm_owner_convert" value="Convert Owners" title="Convert the Owners"/></li>
		<li>Change Player 1 (Might be admin or similar) to THE HDWSBBL or something similar and set the visibility to private</li>
		<li>Clean up the names as required</li>
		<li>Any teams that need to be deleted? t_show = 0</li>
		<li>Doubloe check all the teams are assigned correctly-  no orphins etc</li>
	</ul>

	<h3>Teams Database Change</h3>
	<ul>
		<li>Add a new column to PREFIX_team - WPID Bigint (20)</li>
		<li><input type="submit" name="bblm_team_tbupdate" value="Update Team Table" title="Update Team Table"/></li>
	</ul>

	<h3>Transfers</h3>
	<ul>
		<li>Update the settings page with the archive page description</li>
	</ul>

	<h2>1.8 -> 1.9</h2>
	<ul>
		<li>Change the stad_id column to BIGINT(20)</li>
		<li>Update the settings page with the archive page description</li>
		<li><input type="submit" name="bblm_stadium_stadcpt" value="Convert Stadium Post Types" title="Convert the Stadium Post Types"/></li>
		<li>Now you can delete the Stadiums Page! and <strong>Update the other content widget</strong></li>
		<li><input type="submit" name="bblm_stadium_teams" value="Update Stadium in Teams" title="Update Stadium in Teams"/></li>
		<li><input type="submit" name="bblm_stadium_match" value="Update Stadium in Matches" title="Update Stadium in Matches"/></li>
		<li>Set the HDWSBBL World Stadium to be featured</li>
		<li>Remove old templates from theme Directory</li>
	  </ul>
		<h3>Players Database Change</h3>
		<ul>
			<li>Add a new column to PREFIX_player - WPID Bigint (30)</li>
			<li><input type="submit" name="bblm_player_tbupdate" value="Update Player Table" title="Update Player Table"/></li>
			<li>Add an index to the WPID column in the *_team table</li>
			<li>Add an index to the WPID column in the *_player table</li>
		</ul>

		<h2>1.8 -> 1.10</h2>
		<h3>Championship Cups</h3>
		<ul>
			<li>First take a copy of the text at the top of the Championships page</li>
			<li><input type="submit" name="bblm_cup_cupcpt" value="Convert Championship Post Types" title="Convert the Championship Post Types"/></li>
			<li>Now you can delete the Championship Cups Page!</li>
			<li>Update the Menus to include the new Cups Page</li>
			<li>Also delete the BBBL sevens cup.... (sorry A)</li>
			<li>Update the *comp tables series_id column to BIGINT(20)</li>
			<li><input type="submit" name="bblm_cup_cupincomp" value="Update championship cup references" title="Update championship cup references"/></li>
		</ul>
		<h3>Seasons</h3>
		<ul>
			<li>First take a copy of the text at the top of the Seasons page</li>
			<li>Update the Competitions and awards-seasons tables to BIGINT(20)</li>
			<li>Update the *comp tables sea_id column to BIGINT(20)</li>
			<li>Update the *_awards_player_sea and *awards_team_sea tables sea_id column to BIGINT(20)</li>
			<li><input type="submit" name="bblm_season_seacpt" value="Convert Season Post Types" title="Convert the Season Post Types"/></li>
			<li>Now you can delete the Championship Cups Page!</li>
			<li>Update the Menus to include the new Cups Page</li>
			<li><input type="submit" name="bblm_season_comp" value="Update Seasons in Competitions" title="Update Seasons in Competitions"/></li>
			<li><input type="submit" name="bblm_season_awardteam" value="Update Seasons in Team Awards" title="Update Seasons in Team Awards"/></li>
			<li><input type="submit" name="bblm_season_awardplayer" value="Update Seasons in Player Awards" title="Update Seasons in Player Awards"/></li>
		</ul>

		<h2>1.10 -> 1.11</h2>
		<h3>Templates</h3>
		<ul>
			<li><input type="submit" name="bblm_template_updatemeta" value="Update Template Meta" title="Update Template Meta"/></li>
			<li>Set the Star Player page to use the archive-bblm_starplayer template</li>
			<li>Move the old templates out of the theme root</li>
		</ul>
		<h3>Sidebars</h3>
		<ul>
			<li>Add the new TC widgets for the single page templates that used to have sidebars - teams, players, comps, matches</li>
		</ul>
		<h3>Competitions</h3>
		<ul>
			<li>First take a copy of the text at the top of the Competitions page</li>
			<li>Update the following tables c_id field to BIGINT(20):
				<ul>
					<li>*awards_player_comp</li>
					<li>*awards_team_comp</li>
					<li>*comp_brackets</li>
					<li>*fixture</li>
					<li>*match</li>
					<li>*team_comp</li>
				</ul>
			</li>
			<li>Add column &quot;WPiD&quot; (BIGINT 20) to comp</li>
			<li>Add column &quot;tc_counts&quot; (INT 1, Default 1) to *team_comp</li>
			<li><input type="submit" name="bblm_comp_populate_teamcomp" value="Populate New Field" title="Populate New Field"/></li>
			<li>Add column &quot;m_counts&quot; (INT 1, Default 1) to *match</li>
			<li><input type="submit" name="bblm_comp_populate_matchcol" value="Populate New Field" title="Populate New Field"/></li>
			<li><input type="submit" name="bblm_comp_compcpt" value="Convert Competition Post Types" title="Convert the Competition Post Types"/></li>
			<li>Now you can delete the Competitions Page and update the menus!</li>
			<li><input type="submit" name="bblm_comp_awardteam" value="Update Competitions in Team Awards" title="Update Competitions in Team Awards"/></li>
			<li><input type="submit" name="bblm_comp_awardplayer" value="Update Competitions in Player Awards" title="Update Competitions in Player Awards"/></li>
			<li><input type="submit" name="bblm_comp_brackets" value="Update Competition Brackets" title="Update Competition Brackets"/></li>
			<li><input type="submit" name="bblm_comp_fixtures" value="Update Fixtures" title="Update Fixtures"/></li>
			<li><input type="submit" name="bblm_comp_match" value="Update Matches" title="Update Matches"/></li>
			<li><input type="submit" name="bblm_comp_teamcomp" value="Update Teams in Comp" title="Update Teams in Comp"/></li>
		</ul>

		<h3>Other</h3>
		<ul>
			<li>Update the <em>t_hcoach</em> coloumn to length 50 in the *teams table</li>
		</ul>

		<h2>1.11 -> 1.12</h2>
		<h3>Database Changes</h3>
		<ul>
			<li>Change the following coluns to BIGINT(20):
			<ul>
				<li>*positions: r_id</li>
				<li>*race2star: r_id</li>
				<li>*teams: r_id</li>
				<li>*comp_brackets: m_id</li>
				<li>*match_player: m_id</li>
				<li>*match_team: m_id</li>
				<li>*player_fate: m_id</li>
			</ul></li>
			<li>Add the following columns to the following tables:
				<ul>
					<li>WPID (BIGINT(20) to *_match</li>
				</ul></li>
		</ul>

		<h3>League Settings</h3>
		<ul>
			<li>Update the settings page with text for the Match Results, Awards, and Races pages</li>
		</ul>

		<h3>Races</h3>
		<ul>
			<li><input type="submit" name="bblm_race_racecpt" value="Convert Race Post Types" title="Convert the Race Post Types"/></li>
			<li>Now you can delete the Races Page and update the menus!</li>
			<li>Upload all the images for the teams</li>
			<li><input type="submit" name="bblm_race_positions" value="Update Races in Positions" title="Update Races in Positions Table"/></li>
			<li><input type="submit" name="bblm_race_race2star" value="Update Races in Race2star" title="Update Races in Race2star Table"/></li>
			<li><input type="submit" name="bblm_race_teams" value="Update Races in Teams" title="Update Teams in Race2star Table"/></li>
		</ul>

		<h3>Matches</h3>
		<ul>
			<li><input type="submit" name="bblm_match_matchcpt" value="Convert Match Post Types" title="Convert the Match Post Types"/></li>
			<li><input type="submit" name="bblm_match_tbupdate" value="Update Match Table" title="Update Match Table"/></li>
			<li>Now you can delete the Matches / Results Page and update the menus!</li>
			<li><input type="submit" name="bblm_match_compbracket" value="Update Comp Brackets Table" title="Update Comp_Brackets Table"/></li>
			<li><input type="submit" name="bblm_match_matchplayer" value="Update Match Player Table" title="Update Match Player Table"/></li>
			<li><input type="submit" name="bblm_match_matchteam" value="Update Match Team Table" title="Update Match Team Table"/></li>
			<li><input type="submit" name="bblm_match_playerfate" value="Update Player Fate Table" title="Update Player Fate Table"/></li>
		</ul>

		<h3>Theme - Crownstar</h3>
		<ul>
			<li>Import Theme, set colours, and configure widgets</li>
		</ul>

		<h2>V1.12 -> V1.13 (New Edition Update)</h2>

		<h3>Passing Statistic</h3>
		<p>Add the following column to the *positions database table</p>
		<ul>
			<li>pos_pa</li>
			<li>
				<ul>
					<li>After pos_ag</li>
					<li>int (2)</li>
					<li>default: 0</li>
				</ul>
			</li>
		</ul>
		<p>Add the following column to the *player database table</p>
		<ul>
			<li>p_pa</li>
			<li>
				<ul>
					<li>After pos_ag</li>
					<li>int (2)</li>
					<li>default: 0</li>
				</ul>
			</li>
		</ul>
		<h3>Legacy marker for objects</h3>
		<p>Add meta field to competition: <input type="submit" name="bblm_legacy_comp_meta" value="Add Competition Meta" title="Add Competition Meta"/></p>
		<p>Add the following column to the *player database table</p>
		<ul>
			<li>p_legacy</li>
			<li>
				<ul>
					<li>At the end</li>
					<li>int (1)</li>
					<li>default: 0</li>
				</ul>
			</li>
		</ul>
		<p><input type="submit" name="bblm_legacy_player_flag" value="Set the Legacy flag for Players" title="Set the Legacy flag for Players"/></p>
		<p>Add the following column to the *team database table</p>
		<ul>
			<li>t_legacy</li>
			<li>
				<ul>
					<li>At the end</li>
					<li>int (1)</li>
					<li>default: 0</li>
				</ul>
			</li>
		</ul>
		<p><input type="submit" name="bblm_legacy_team_flag" value="Set the Legacy flag for Teams" title="Set the Legacy flag for Teams"/></p>
		<p>Make sure you set the legacy flag for the TBD team back to 0</p>
		<p>Add the following column to the *match database table</p>
		<ul>
			<li>m_legacy</li>
			<li>
				<ul>
					<li>At the end</li>
					<li>int (1)</li>
					<li>default: 0</li>
				</ul>
			</li>
		</ul>
		<p><input type="submit" name="bblm_legacy_match_flag" value="Set the Legacy flag for Matches" title="Set the Legacy flag for matches"/></p>

		<h3>Set all races as active</h3>
		<p><input type="submit" name="bblm_race_status_meta" value="Set all Races as active" title="Set all Races as active"/></p>
		<p>Remember to go and retire the races that are no longer availiblke</p>

		<h3>Current Team Value</h3>
		<p>Add the following column to the *team database table</p>
		<ul>
			<li>t_ctv</li>
			<li>
				<ul>
					<li>after t_tv</li>
					<li>bigint (20)</li>
					<li>default: 0</li>
				</ul>
			</li>
		</ul>
		<p><input type="submit" name="bblm_team_ctv" value="Set all teams Current Team Value" title="Set all teams Current Team Value"/></p>

		<h3>New Player characteristics</h3>
		<p>Add the following column to the *player database table</p>
		<ul>
			<li>p_tr</li>
			<li>
				<ul>
					<li>at the end</li>
					<li>int (1)</li>
					<li>default: 0</li>
				</ul>
			</li>
			<li>p_cspp</li>
			<li>
				<ul>
					<li>after p_spp</li>
					<li>int (3)</li>
					<li>default: 0</li>
				</ul>
			</li>
		</ul>
		<p><input type="submit" name="bblm_player_spp_update" value="Update SPP for legacy players" title="Update SPP for legacy players"/></p>
		<p>Add the following column to the *match_player database table</p>
		<ul>
			<li>mp_ttm, mp_ktm, mp_etm, mp_def, mp_ptn, mp_foul</li>
			<li>All at the end</li>
			<li>int (1) except mp_foul (2)</li>
			<li>default: 0</li>
		</ul>

		<h2>V1.13 -> V1.14</h2>
		<p>Add all the releevant race traits to the races</p>
		<p>Add a new position for 'Riotous Rookie' and set its owning race to 0</p>
		<p>Update the settings with the new Riotous Rookie Position</p>
		<p>Update the Fate db table to include Riotous Rookies along side JM</p>

		<h2>V1.14 -> V1.15</h2>
		<h3>Convert Star Players</h3>
		<ul>
			<li><input type="submit" name="bblm_star_starcpt" value="Convert Star Player Post Types" title="Convert the Star Player Post Types"/></li>
			<li>You can now delete the old star players page and update any menus</li>
		</ul>


</form>

</div>
