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


    /**
     *
     * MAIN PAGE CONTENT FOLLOWS
     */
?>

  <p>This screen should only be used when performing the cutover. Use eachn option <strong>only once</strong>.</p>

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
  </form>



</div>
