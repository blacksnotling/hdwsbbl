<?php
/*
*	Filename: bb.admin.core.teamm.php
*	Version: 1.0.2
*	Description: The front page to the Team Management section.
*/
/* -- Change History --
20080303 - 0.1b - Creation of initial file with some generic welcome text.
20080319 - 0.2b - Added add.position
20080320 - 0.3b - Added add.player
20080322 - 0.4b - Added hire.journeyman
20080719 - 0.5b - added link to rename player
20080730 - 1.0 - bump to Version 1 for public release.
20080808 - 1.0.1 - added links to edit.player and edit.team
20080813 - 1.0.2 - removed a few links and renamed edit players and edit teams to manage players/teams
20080822 - 1.0.3 - added the link to the JM report

*/
?>
<div class="wrap">
<h2>Team Management</h2>
<p>From these pages you can administrate the Teams and Races that can be selected in your Blood Bowl League.</p>
<ul>
  <li><a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.add.team.php" title="Add a new Team">Add a new team to the league</a></li>
  <li><a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.edit.team.php" title="Manage Teams">Manage Teams</a></li>
  <li><a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.add.race.php" title="Add a new Race">Add a new Race to the league</a></li>
  <li>Edit an existing Race</li>
  <li><a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.add.position.php" title="Add a new Race">Assign a new position to a Race.</a></li>
  <li><a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.add.player.php" title="Add a new Player">Add a new player to a team.</a></li>
  <li><a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.edit.player.php" title="Manage Players">Manage Players on a team.</a></li>
  <li><a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.report.jm.php" title="Run the JM report">Run the Journeyman report.</a></li>
</ul>
</div>