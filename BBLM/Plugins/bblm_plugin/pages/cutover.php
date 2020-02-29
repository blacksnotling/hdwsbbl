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
			<li><input type="submit" name="bblm_cup_cupincomp" value="Update championship cup references" title="Update championship cup references"/></li>
		</ul>
		<h3>Seasons</h3>
		<ul>
			<li>First take a copy of the text at the top of the Seasons page</li>
			<li>Update the Competitions and awards-seasons tables to BIGINT(20)</li>
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
			<li><strong>MAKE SURE I HAVE DONE A FULL CUTOVER AGAIN TO TEST</strong></li>
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
			<li>Update the awards-comp tables to BIGINT(20)</li>
			<li>OTHER TABLES HERE</li>
			<li>Add column &quot;tc_counts&quot; (INT 1, Default 1) to *team_comp</li>
			<li>POPULATE COLUMN</li>
			<li>CONVERT COMPETITIONS</li>
			<li>Now you can delete the Competitions Page and update the menus!</li>
		</ul>

</form>

</div>
