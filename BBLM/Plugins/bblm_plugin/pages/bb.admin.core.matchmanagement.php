<?php
/**
 * BBowlLeagueMan Match Management Menu Page
 *
 * A generic welcome page to the match management section of the BBLM Plugin.
 *
 * @author 		Blacksnotling
 * @category 	Core
 * @package 	BBowlLeagueMan/Pages
 */
?>
<div class="wrap">
<h2>Match Management Pages</h2>
<p>From these pages you can perform match related actions such as record a new match, record players actions, add / edit fixtures and edit match details.</p>
<ul>
  <li><a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_add_match" title="Record match details">Record details of a new match.</a></li>
  <li><a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.add.match_player.php" title="Record a players actions for a match">Record a players actions for a match.</a></li>
  <li><a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.edit.match.php" title="Edit match details">Edit match details (report, comments and facts).</a></li>
  <li><a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.add.fixture.php" title="Add a new fixture">Add a fixture.</a></li>
  <li><a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.edit.fixture.php" title="Edit a fixture">Edit a fixture.</a></li>
</ul>
</div>
