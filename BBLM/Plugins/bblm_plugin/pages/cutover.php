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
 * @version   1.2
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

</form>

</div>
