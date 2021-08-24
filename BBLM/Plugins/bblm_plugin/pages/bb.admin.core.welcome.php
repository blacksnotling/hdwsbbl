<?php
/**
 * BBowlLeagueMan Welcome Menu Page
 *
 * A generic welcome page to the BBLM section of the admin section.
 *
 * @author 		Blacksnotling
 * @category 	Core
 * @package 	BBowlLeagueMan/Pages
 */

?>
<div class="wrap">
<h2>Welcome to the Blood Blowl League Manager</h2>
<p>From these pages you can administrate your Blood Bowl League!</p>
<ul>
  <li><a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_settings" title="View the options page">Manage your Leagues options.</a></li>
  <li><a href="<?php bloginfo('url');?>/wp-admin/edit.php?post_type=bblm_dyk" title="Manage DYK">Manage your &quot;Did You Know?&quot; facts</a></li>
  <li><a href="<?php bloginfo('url');?>/wp-admin/edit.php?post_type=bblm_cup" title="Add a new series / cup">Add a new Championship Cup.</a></li>
  <li><a href="<?php bloginfo('url');?>/wp-admin/edit.php?post_type=bblm_season" title="Start a new Season">Start a new season.</a></li>
  <li><a href="<?php bloginfo('url');?>/wp-admin/edit.php?post_type=bblm_comp" title="Start a new Competition">Start a new Competition.</a></li>
  <li><a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.edit.comp_team.php" title="Assign teams to a Competition">Assign teams (to a Competition).</a></li>
  <li><a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_brackets" title="Manage Tournament Brackets">Manage Tournament Brackets (Add or Edit).</a></li>
  <li><a href="<?php bloginfo('url');?>/wp-admin/edit.php?post_type=bblm_stadium" title="Add a Stadium">Add a Stadium.</a></li>
  <li><a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.add.award.php" title="Create an Award">Create an Award.</a></li>
  <li><a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.end.comp.php" title="Close a competition">Close a comptition.</a></li>
  <li><a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.end.season.php" title="Close a competition">Close a Season.</a></li>
  <li><a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.generate.summary.php" title="Generate the Weekly Summary">Generate the Weekly Summary.</a></li>
  <li><a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_transfers" title="Transfer a player">Transfer a player</a></li>
</ul>
</div>
