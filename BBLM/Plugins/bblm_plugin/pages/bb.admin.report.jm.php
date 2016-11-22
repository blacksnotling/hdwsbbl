<?php
/*
*	Filename: bb.admin.report.jm.php
*	Version: 1.2
*	Description: Lists the Journeymen of the league. based of the old add.journeyman
*/
/* -- Change History --
20080822 - 1.0 - Initial creation of file. based of the old add. journeymen
20090902 - 1.1 - added support for Mercs and direct links to remove the player from the respective team
20100124 - 1.2 - Updated the prefix for the custom bb tables in the Database (tracker [224])
*/


//Check the file is not being accessed directly
if (!function_exists('add_action')) die('You cannot run this file directly. Naughty Person');
?>
<div class="wrap">
	<h2>Journeyman and Merc report</h2>

	<p>Below is a list of all the Journeymen and Mercs active in the league. If you want to retire  a player then use the link below. To edit anything else, you will need to go through the <a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.edit.player.php" title="Edit player details">Manage Players</a> page.


	<?php
	//call the options from the table
	$options = get_option('bblm_config');
	$merc_pos = htmlspecialchars($options['player_merc'], ENT_QUOTES);

	$jmsql = 'SELECT P.post_title AS Player, O.post_title AS Team, X.p_num, Z.pos_name, X.p_id FROM '.$wpdb->prefix.'player X, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P, '.$wpdb->prefix.'bb2wp I, '.$wpdb->posts.' O, '.$wpdb->prefix.'position Z WHERE X.pos_id = Z.pos_id AND X.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = P.ID AND X.t_id = I.tid AND I.prefix = \'t_\' AND I.pid = O.ID AND X.p_status = 1 AND (X.pos_id = 1 OR X.pos_id = '.$merc_pos.') ORDER BY X.t_id, X.p_num';

	if ($journeymen = $wpdb->get_results($jmsql)) {
		$is_first = 1;
		$current_team = "";

		foreach ($journeymen as $jm) {
			if ($jm->Team !== $current_team) {
				$current_team = $jm->Team;
				if (1 !== $is_first) {
					print(" </ul>\n");
				}
				$is_first = 1;
			}
			if ($is_first) {
				print("<h3>".$jm->Team."</h3>\n <ul>\n");
				$is_first = 0;
			}
			print ("   <li># ".$jm->p_num." - ".$jm->Player." (<em>".$jm->pos_name."</em>) -- <<a href=\"");
			bloginfo('url');
			print("/wp-admin/admin.php?page=bblm_plugin/pages/bb.admin.edit.player.php&action=edit&item=remove&id=".$jm->p_id."\" title=\"Remove this freebooter from the team\">Retire / Remove</a>></li>\n");
		}
		print("</ul>\n");
	}
	else {
		print("<p><strong>There are no Journeymen or Mercs currently active in the league!</strong></p>\n");
	}
?>


</div>