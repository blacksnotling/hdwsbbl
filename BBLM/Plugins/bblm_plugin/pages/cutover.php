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
     * MAIN PAGE CONTENT FOLLOWS
     */
?>

  <p>This screen should only be used when performing the cutover. Use eachn option <strong>only once</strong>.</p>

  <h3>Stadiums</h3>
  <form name="bblm_cutovermain" method="post" id="post">
    <ul>
  	<li><input type="submit" name="bblm_stadium_stadcpt" value="Convert Stadium Post Types" title="Convert the Stadium Post Types"/></li>
    <li>Now you can delete the Stadiums Page!</li>
    <li><input type="submit" name="bblm_stadium_teams" value="Update Stadium in Teams" title="Update Stadium in Teams"/></li>
    <li><input type="submit" name="bblm_stadium_match" value="Update Stadium in Matches" title="Update Stadium in Matches"/></li>
  </ul>
  </form>



</div>
