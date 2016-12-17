<?php
/*
*	Filename: bb.admin.edit.options.php
*	Description: Page to manage options and settings of the League.
*/
?>
<div class="wrap">
	<h2>League Options</h2>
	<p>Use the following Page to manage settings required by the system.</p>

<?php
	//Mapping of options to Vars.
	$options = get_option('bblm_config');
	if ( !is_array($options) ) {
		$options = array('title'=>'', 'topics'=>'');
	}

	//handle form sumit now
	if ( isset( $_POST['bblm_options_submit'] ) ) {


		// Remember to sanitize and format use input appropriately.
		$options['page_race'] = strip_tags(stripslashes($_POST['bblm_options_page_race']));
		$options['page_team'] = strip_tags(stripslashes($_POST['bblm_options_page_team']));
		$options['page_series'] = strip_tags(stripslashes($_POST['bblm_options_page_series']));
		$options['page_comp'] = strip_tags(stripslashes($_POST['bblm_options_page_comp']));
		$options['page_match'] = strip_tags(stripslashes($_POST['bblm_options_page_match']));
		$options['page_stadium'] = strip_tags(stripslashes($_POST['bblm_options_page_stadium']));
		$options['page_stats'] = strip_tags(stripslashes($_POST['bblm_options_page_stats']));
		$options['page_season'] = strip_tags(stripslashes($_POST['bblm_options_page_season']));
		$options['page_stars'] = strip_tags(stripslashes($_POST['bblm_options_page_stars']));
		$options['display_stats'] = strip_tags(stripslashes($_POST['bblm_options_display_stats']));
		$options['league_name'] = strip_tags(stripslashes($_POST['bblm_options_league_name']));
		$options['team_tbd'] = strip_tags(stripslashes($_POST['bblm_options_team_tbd']));
		$options['team_star'] = strip_tags(stripslashes($_POST['bblm_options_team_star']));
		$options['race_star'] = strip_tags(stripslashes($_POST['bblm_options_race_star']));
		$options['cat_warzone'] = strip_tags(stripslashes($_POST['bblm_options_cat_warzone']));
		$options['site_dir'] = strip_tags(stripslashes($_POST['bblm_options_site_dir']));
		$options['player_merc'] = strip_tags(stripslashes($_POST['bblm_options_player_merc']));
		update_option('bblm_config', $options);
	?>
	<div id="updated" class="updated fade"><p>Settings have been saved.</p></div>
	<?php

	}

	// Be sure to format the options to be valid HTML attributes.
	$page_race = htmlspecialchars($options['page_race'], ENT_QUOTES);
	$page_team = htmlspecialchars($options['page_team'], ENT_QUOTES);
	$page_series = htmlspecialchars($options['page_series'], ENT_QUOTES);
	$page_comp = htmlspecialchars($options['page_comp'], ENT_QUOTES);
	$page_match = htmlspecialchars($options['page_match'], ENT_QUOTES);
	$page_stadium = htmlspecialchars($options['page_stadium'], ENT_QUOTES);
	$page_stats = htmlspecialchars($options['page_stats'], ENT_QUOTES);
	$page_season = htmlspecialchars($options['page_season'], ENT_QUOTES);
	$page_stars = htmlspecialchars($options['page_stars'], ENT_QUOTES);
	$display_stats = htmlspecialchars($options['display_stats'], ENT_QUOTES);
	$league_name = htmlspecialchars($options['league_name'], ENT_QUOTES);
	$cat_warzone = htmlspecialchars($options['cat_warzone'], ENT_QUOTES);
	$team_tbd = htmlspecialchars($options['team_tbd'], ENT_QUOTES);
	$team_star = htmlspecialchars($options['team_star'], ENT_QUOTES);
	$race_star = htmlspecialchars($options['race_star'], ENT_QUOTES);
	$site_dir = htmlspecialchars($options['site_dir'], ENT_QUOTES);
	$player_merc = htmlspecialchars($options['player_merc'], ENT_QUOTES);

?>
<form name="bblm_test" method="post" id="post">

	<h3>League Settings</h3>
		<table class="form-table">
	 	<tr>
	 		<th scope="row" valign="top">League name</th>
	 		<td>
	 			<input id="bblm_options_league_name" name="bblm_options_league_name" type="text" value="<?php echo $league_name ?>" maxlength="20" size="10" />
	  	 	<label for="bblm_options_league_name">Displayed on various pages - nest to use the short name eg. *bbl.</label>
	 		</td>
	 	</tr>
		</table>

<h3>Display Settings</h3>
<table class="form-table">
 <tr>
 	<th scope="row" valign="top">Statistics Limit</th>
 	<td>
 		<input id="bblm_options_display_stats" name="bblm_options_display_stats" type="text" value="<?php echo $display_stats ?>" maxlength="3" size="2" />
  	 	<label for="bblm_options_display_stats">The number of players and teams to display in the "top 10 stats".</label>
 	</td>
 </tr>
</table>

<h3>Technical and Other settings</h3>
<table class="form-table">
 <tr>
 	<th scope="row" valign="top">Site Directory</th>
 	<td>
 		<input id="bblm_options_site_dir" name="bblm_options_site_dir" type="text" value="<?php echo $site_dir ?>" maxlength="20" size="10" />
  	 	<label for="bblm_options_site_dir">The location of the wordpress install from the SERVER root WITHOUT Slashes. eg hdwsbbl</label>
 	</td>
 </tr>
 <tr>
 	<th scope="row" valign="top">Page # - Races</th>
 	<td>
 		<input id="bblm_options_page_race" name="bblm_options_page_race" type="text" value="<?php echo $page_race ?>" maxlength="6" size="2" />
  	 	<label for="bblm_options_page_race">The number in the DB for the Race Page.</label>
 	</td>
 </tr>
 <tr>
 	<th scope="row" valign="top">Page # - Teams</th>
 	<td>
 		<input id="bblm_options_page_team" name="bblm_options_page_team" type="text" value="<?php echo $page_team ?>" maxlength="6" size="2" />
  	 	<label for="bblm_options_page_team">The number in the DB for the Team Page.</label>
 	</td>
 </tr>
 <tr>
 	<th scope="row" valign="top">Page # - Championship Cups</th>
 	<td>
 		<input id="bblm_options_page_series" name="bblm_options_page_series" type="text" value="<?php echo $page_series ?>" maxlength="6" size="2" />
  	 	<label for="bblm_options_page_series">The number in the DB for the Series Page.</label>
 	</td>
 </tr>
 <tr>
 	<th scope="row" valign="top">Page # - Competitions</th>
 	<td>
 		<input id="bblm_options_page_comp" name="bblm_options_page_comp" type="text" value="<?php echo $page_comp ?>" maxlength="6" size="2" />
  	 	<label for="bblm_options_page_comp">The number in the DB for the Competitions Page.</label>
 	</td>
 </tr>

 <tr>
 	<th scope="row" valign="top">Page # - Match / Results</th>
 	<td>
 		<input id="bblm_options_page_match" name="bblm_options_page_match" type="text" value="<?php echo $page_match ?>" maxlength="6" size="2" />
  	 	<label for="bblm_options_page_match">The number in the DB for the Match / Results Page.</label>
 	</td>
 </tr>

 <tr>
  	<th scope="row" valign="top">Page # - Stadiums</th>
  	<td>
  		<input id="bblm_options_page_stadium" name="bblm_options_page_stadium" type="text" value="<?php echo $page_stadium ?>" maxlength="6" size="2" />
   	 	<label for="bblm_options_page_stadium">The number in the DB for the Stadiums Page.</label>
  	</td>
 </tr>
 <tr>
  	<th scope="row" valign="top">Page # - Statistics</th>
  	<td>
  		<input id="bblm_options_page_stats" name="bblm_options_page_stats" type="text" value="<?php echo $page_stats ?>" maxlength="6" size="2" />
   	 	<label for="bblm_options_page_stats">The number in the DB for the Statistics Page.</label>
  	</td>
 </tr>
  <tr>
   	<th scope="row" valign="top">Page # - Season</th>
   	<td>
   		<input id="bblm_options_page_season" name="bblm_options_page_season" type="text" value="<?php echo $page_season ?>" maxlength="6" size="2" />
    	 	<label for="bblm_options_page_season">The number in the DB for the Seasons Page.</label>
   	</td>
 </tr>
  <tr>
   	<th scope="row" valign="top">Page # - Star Players</th>
   	<td>
   		<input id="bblm_options_page_stars" name="bblm_options_page_stars" type="text" value="<?php echo $page_stars ?>" maxlength="6" size="2" />
    	 	<label for="bblm_options_page_stars">The number in the DB for the Star Players Team Page.</label>
   	</td>
 </tr>
  <tr>
   	<th scope="row" valign="top">Category - Warzone</th>
   	<td>
   		<input id="bblm_options_cat_warzone" name="bblm_options_cat_warzone" type="text" value="<?php echo $cat_warzone ?>" maxlength="3" size="2" />
    	 	<label for="bblm_options_cat_warzone">The Cat ID for the Warzone (so it can be filtered).</label>
   	</td>
 </tr>
 <tr>
  	<th scope="row" valign="top">Team_id - To be Determined team</th>
  	<td>
  		<input id="bblm_options_team_tbd" name="bblm_options_team_tbd" type="text" value="<?php echo $team_tbd ?>" maxlength="3" size="2" />
   	 	<label for="bblm_options_team_tbd">The ID number for the team "To be determined".</label>
  	</td>
 </tr>
 <tr>
  	<th scope="row" valign="top">Team_id - Star Player Team</th>
  	<td>
  		<input id="bblm_options_team_star" name="bblm_options_team_star" type="text" value="<?php echo $team_star ?>" maxlength="3" size="2" />
   	 	<label for="bblm_options_team_star">The ID number for the team "Star Players".</label>
  	</td>
 </tr>
  <tr>
   	<th scope="row" valign="top">Race_id - Star Player Race</th>
   	<td>
   		<input id="bblm_options_race_star" name="bblm_options_race_star" type="text" value="<?php echo $race_star ?>" maxlength="3" size="2" />
    	 	<label for="bblm_options_race_star">The ID number for the race "Stars".</label>
   	</td>
 </tr>
  <tr>
   	<th scope="row" valign="top">Player ID - Mercenary</th>
   	<td>
   		<input id="bblm_options_player_merc" name="bblm_options_player_merc" type="text" value="<?php echo $player_merc ?>" maxlength="3" size="2" />
    	<label for="bblm_options_player_merc">The ID number for the Merc position.</label>
   	</td>
 </tr>

</table>

<p class="submit"><input type="submit" name="bblm_options_submit" value="Save Options" title="Save Options" class="button-primary"/></p>

</form>

</div>
