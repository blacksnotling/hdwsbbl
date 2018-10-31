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
 * MAIN PAGE CONTENT FOLLOWS
 */
?>
<p>This screen should only be used when performing the cutover. Use each option <strong>only once</strong>.</p>

<form name="bblm_cutovermain" method="post" id="post">
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

</form>

</div>
