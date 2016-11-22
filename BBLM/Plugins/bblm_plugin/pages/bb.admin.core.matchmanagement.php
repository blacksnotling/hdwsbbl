<?php
/*
*	Filename: bb.admin.core.welcome.php
*	Version: 1.0
*	Description: A generic welcome page to the BBLM section of the admin section.
*/
/* -- Change History --
20080420 - 0.1b - Creation of initial file with some generic welcome text.
20080421 - 0.2b - Added Link to add.fixture
20080723 - 0.3b - added link to edit fixture
20080730 - 1.0 - bump to Version 1 for public release.

*/
?>
<div class="wrap">
<h2>Match Management Pages</h2>
<p>From these pages you can perform match related actions such as record a new match, recprd players actions and edit match details.</p>
<ul>
  <li><a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.add.match.php" title="Record match details">Record details of a match.</a></li>
  <li><a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.add.match_player.php" title="Record a players actions for a match">Record a players actions for a match.</a></li>
  <li><a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.edit.match.php" title="Edit match details">Edit match details (report, comments and facts).</a></li>
  <li><a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.add.fixture.php" title="Add a new fixture">Add a fixture.</a></li>
  <li><a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.edit.fixture.php" title="Edit a fixture">Edit a fixture.</a></li>
</ul>
</div>