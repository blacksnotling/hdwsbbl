<?php
/*
*	Filename: bb.admin.core.welcome.php
*	Version: 1.0.2
*	Description: A generic welcome page to the BBLM section of the admin section.
*/
/* -- Change History --
20080227 - 0.1b - Creation of initial file with some generic welcome text.
20080307 - 0.2b - Added some links to options and add series.
20080310 - 0.3b - Added link to add comp + corrected series link
20080311 - 0.4b - Added link to edit comp/team
20080311 - 0.5b - Added link to Add matchs
20080326 - 0.6b - Added link to Add matchs_player
20080402 - 0.7b - Added link to Add Stadium
20080407 - 0.8b - Added Link to Add Award and end.comp
20080420 - 0.9b - Added Link to Add Season
20080425 - 0.10b - Added link to "set up tourney brackets"
20080602 - 0.11b - Added link to end season
20080727 - 0.12b - added link to generate.summery
20080730 - 1.0 - bump to Version 1 for public release.
20080823 - 1.0.1 - Added link to edit.comp_brackets
20090130 - 2.0.2 - Added Link to Did YOu Know Management

*/
?>
<div class="wrap">
<h2>Welcome to the Blood Blowl League Manager</h2>
<p>From these pages you can administrate your Blood Bowl Pages.</p>
<ul>
  <li><a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.edit.options.php" title="View the options page">Edit the league options.</a></li>
  <li><a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.manage.dyk.php" title="Manage DYK">Did You Know?</a></li>
  <li><a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.add.series.php" title="Add a new series / cup">Add a new Championship Series / Cup.</a></li>
  <li><a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.add.season.php" title="Start a new Season">Start a new season.</a></li>
  <li><a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.add.comp.php" title="Add a new Competition">Add a new Competition.</a></li>
  <li><a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.edit.comp_team.php" title="Assign teams to a Competition">Assign teams (to a Competition).</a></li>
  <li><a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.add.comp_brackets.php" title="Set up Tourney Brackets">Set up Tourney Brackets.</a></li>
  <li><a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.edit.comp_brackets.php" title="Edit Tourney Brackets">Edit Tourney Brackets.</a></li>
  <li><a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.add.stadium.php" title="Add a Stadium">Add a Stadium.</a></li>
  <li><a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.add.award.php" title="Create an Award">Create an Award.</a></li>
  <li><a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.end.comp.php" title="Close a competition">Close a Somptition.</a></li>
  <li><a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.end.season.php" title="Close a competition">Close a Season.</a></li>
  <li><a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.generate.summary.php" title="Generate the Weekly Summary">Generate the Weekly Summary.</a></li>
</ul>
</div>