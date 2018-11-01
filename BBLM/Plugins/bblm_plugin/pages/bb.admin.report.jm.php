<?php
/**
 * BBowlLeagueMan Freebooter Report AKA the Journeyman Report
 *
 * Executes the functinon to display all Journeymen and Mercenarys currently active.
 *
 * @author 		Blacksnotling
 * @category 	Core
 * @package 	BBowlLeagueMan/Pages
 */

//Check the file is not being accessed directly
if (!function_exists('add_action')) die('You cannot run this file directly. Naughty Person');
?>
<div class="wrap">
	<h2>Journeyman and Mercenary report</h2>

	<p>Below is a list of all the Journeymen and Mercenarys active in the league. If you want to retire or hire a player then use the quick links below. To edit anything else, you will need to go through the <a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.edit.player.php" title="Edit player details">Manage Players</a> page.

	<?php bblm_jm_report() ?>

</div>
