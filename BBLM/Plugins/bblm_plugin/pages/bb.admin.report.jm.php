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
	<h2><?php echo __( 'Journeyman and Mercenary report','bblm' ); ?></h2>

	<p><?php echo __( 'Below is a list of all the Journeymen, Mercenaries, and Riotous Rookies active in the league. If you want to retire or hire a player then use the quick links below.','bblm' ); ?></p>

	<?php bblm_jm_report() ?>

</div>
