<?php
/*
Template Name: List Awards
*/
/*
*	Filename: bb.core.awards.php
*	Version: 1.2
*	Description: .Page template to display
*/
/* -- Change History --
20080606 - 1.0 - Initial creation of file.
20090712 - 1.0.1 - Added DYK code to page
20090830 - 1.1 - Moved the site under statistics
			   - removed the qualified award from the listing
			   - cleaned up the sql of the pages.
20100123 - 1.2 - Updated the prefix for the custom bb tables in the Database (tracker [225])

*/
?>
<?php get_header(); ?>
	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
		<div id="breadcrumb">
			<p><a href="<?php echo home_url(); ?>" title="Back to the front of the HDWSBBL">HDWSBBL</a> &raquo; <a href="<?php echo home_url(); ?>/stats" title="Back to the main Statistics page">Statistics</a> &raquo; <?php the_title(); ?></p>
		</div>
			<div class="entry">
				<h2><?php the_title(); ?></h2>
				<?php the_content('Read the rest of this entry &raquo;'); ?>
<?php
			$awardssql = 'SELECT * FROM '.$wpdb->prefix.'awards WHERE a_id !=4 ORDER BY a_id ASC';
			if ($awards = $wpdb->get_results($awardssql)) {
				foreach ($awards as $aw) {
					$aoutput = "";

					if ($aw->a_cup) {
						//The award in question is a Championship
						$compmajorawardssql = 'SELECT P.post_title, P.guid, H.post_title AS CompName, H.guid AS CompLink FROM '.$wpdb->prefix.'awards A, '.$wpdb->prefix.'awards_team_comp B, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp Y, '.$wpdb->posts.' H WHERE C.c_id = Y.tid AND Y.prefix = \'c_\' AND Y.pid = H.ID AND A.a_id = B.a_id AND a_cup = 1 AND B.t_id = J.tid AND J.prefix = \'t_\' AND J.pid = P.ID AND B.c_id = C.c_id AND C.c_show = 1 AND C.c_counts = 1 AND A.a_id = '.$aw->a_id.' ORDER BY C.c_id DESC';
						if (($cmawards = $wpdb->get_results($compmajorawardssql)) && (0 < count($cmawards))) {
							$aoutput .= "					<table>\n						<tr>\n							<th class=\"tbl_name\">Team</th>\n							<th class=\"tbl_name\">Competition</th>\n						</tr>\n";
							$zebracount = 1;
							foreach ($cmawards as $cma) {
								if ($zebracount % 2) {
									$aoutput .="						<tr>\n";
								}
								else {
									$aoutput .= "						<tr class=\"tbl_alt\">\n";
								}
									$aoutput .= "							<td><a href=\"".$cma->guid."\" title=\"Read more about ".$cma->post_title."\">".$cma->post_title."</a></td>\n						<td><a href=\"".$cma->CompLink."\" title=\"Read more about ".$cma->CompName."\">".$cma->CompName."</a></td>\n	</tr>\n";
								$zebracount++;
							}
							$aoutput .= "					</table>\n";
						}
					}// end of if cup
					else {
						/*
							We have a non-championship award. there wil be 4 checks:
							1. Awards to teams in a season
							2. Awards to Players in a season
							3. Awards to teams in a competition
							4. Awards to Players in a competition
						*/
						//1. Awards to teams in a season
						$compteamawardssql = 'SELECT P.post_title, P.guid, B.ats_value AS value, Y.post_title AS Sea, Y.guid AS SeaLink FROM '.$wpdb->prefix.'awards A, '.$wpdb->prefix.'awards_team_sea B, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P, '.$wpdb->prefix.'bb2wp T, '.$wpdb->posts.' Y WHERE B.sea_id = T.tid AND T.prefix = \'sea_\' AND T.pid = Y.ID AND A.a_id = B.a_id AND a_cup = 0 AND B.t_id = J.tid AND J.prefix = \'t_\' AND J.pid = P.ID AND A.a_id = '.$aw->a_id.' ORDER BY B.sea_id DESC';
						if ($ctawards = $wpdb->get_results($compteamawardssql)) {
							$aoutput .= "					<h4>Team recipients during a Season</h4>\n					<table>\n						<tr>\n							<th class=\"tbl_name\">Team</th>\n							<th class=\"tbl_name\">Season</th>\n							<th class=\"tbl_stat\">Value</th>\n						</tr>\n";
							$zebracount = 1;
							foreach ($ctawards as $cta) {
								if ($zebracount % 2) {
									$aoutput .= "						<tr>\n";
								}
								else {
									$aoutput .= "						<tr class=\"tbl_alt\">\n";
								}
								$aoutput .= "							<td><a href=\"".$cta->guid."\" title=\"Read more about ".$cta->post_title."\">".$cta->post_title."</a></td>\n							<td><a href=\"".$cta->SeaLink."\" title=\"Read more about ".$cta->Sea."\">".$cta->Sea."</a></td>\n						<td>";
								if (0 < $cta->value) {
									$aoutput .= $cta->value;
								}
								else {
									$aoutput .= "n/a";
								}
								$aoutput .= "</td>\n						</tr>\n";
								$zebracount++;
							}
							$aoutput .= "</table>";
						}

						//2. Awards to Players in a season
						$compteamawardssql = 'SELECT P.post_title, P.guid, B.aps_value AS value, Y.post_title AS Sea, Y.guid AS SeaLink, F.post_title AS Team, F.guid AS TeamLink FROM '.$wpdb->prefix.'awards A, '.$wpdb->prefix.'awards_player_sea B, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P, '.$wpdb->prefix.'bb2wp T, '.$wpdb->posts.' Y, '.$wpdb->prefix.'bb2wp D, '.$wpdb->posts.' F, '.$wpdb->prefix.'player X WHERE X.p_id = B.p_id AND X.t_id = D.tid AND D.prefix = \'t_\' AND D.pid = F.ID AND B.sea_id = T.tid AND T.prefix = \'sea_\' AND T.pid = Y.ID AND A.a_id = B.a_id AND a_cup = 0 AND B.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = P.ID AND A.a_id = '.$aw->a_id.' ORDER BY B.sea_id DESC';
						if ($ctawards = $wpdb->get_results($compteamawardssql)) {
							$aoutput .= "					<h4>Player recipients during a Season</h4>\n					<table>\n						<tr>\n							<th class=\"tbl_name\">Player</th>\n							<th class=\"tbl_name\">Season</th>\n							<th class=\"tbl_name\">Team</th>\n							<th class=\"tbl_stat\">Value</th>\n						</tr>\n";
							$zebracount = 1;
							foreach ($ctawards as $cta) {
								if ($zebracount % 2) {
									$aoutput .= "						<tr>\n";
								}
								else {
									$aoutput .= "						<tr class=\"tbl_alt\">\n";
								}
								$aoutput .= "							<td><a href=\"".$cta->guid."\" title=\"Read more about ".$cta->post_title."\">".$cta->post_title."</a></td>\n							<td><a href=\"".$cta->SeaLink."\" title=\"Read more about ".$cta->Sea."\">".$cta->Sea."</a></td>\n							<td><a href=\"".$cta->TeamLink."\" title=\"Read more about ".$cta->Team."\">".$cta->Team."</a></td>\n						<td>";
								if (0 < $cta->value) {
									$aoutput .= $cta->value;
								}
								else {
									$aoutput .= "n/a";
								}
								$aoutput .= "</td>\n						</tr>\n";
								$zebracount++;
							}
							$aoutput .= "</table>";
						}

						//3. Awards to teams in a competition
						$compteamawardssql = 'SELECT P.post_title, P.guid, B.atc_value AS value, Y.post_title AS Comp, Y.guid AS CompLink FROM '.$wpdb->prefix.'awards A, '.$wpdb->prefix.'awards_team_comp B, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P, '.$wpdb->prefix.'bb2wp T, '.$wpdb->posts.' Y WHERE B.c_id = T.tid AND T.prefix = \'c_\' AND T.pid = Y.ID AND A.a_id = B.a_id AND a_cup = 0 AND B.t_id = J.tid AND J.prefix = \'t_\' AND J.pid = P.ID AND A.a_id = '.$aw->a_id.' ORDER BY B.c_id DESC';
						if ($ctawards = $wpdb->get_results($compteamawardssql)) {
							$aoutput .= "					<h4>Team recipients during a Competition</h4>\n					<table>\n						<tr>\n							<th class=\"tbl_name\">Team</th>\n							<th class=\"tbl_name\">Competition</th>\n							<th class=\"tbl_stat\">Value</th>\n						</tr>\n";
							$zebracount = 1;
							foreach ($ctawards as $cta) {
								if ($zebracount % 2) {
									$aoutput .= "						<tr>\n";
								}
								else {
									$aoutput .= "						<tr class=\"tbl_alt\">\n";
								}
								$aoutput .= "							<td><a href=\"".$cta->guid."\" title=\"Read more about ".$cta->post_title."\">".$cta->post_title."</a></td>\n							<td><a href=\"".$cta->CompLink."\" title=\"Read more about ".$cta->Comp."\">".$cta->Comp."</a></td>\n						<td>";
								if (0 < $cta->value) {
									$aoutput .= $cta->value;
								}
								else {
									$aoutput .= "n/a";
								}
								$aoutput .= "</td>\n						</tr>\n";
								$zebracount++;
							}
							$aoutput .= "</table>";
						}

						//4. Awards to Players in a competition
						$compteamawardssql = 'SELECT P.post_title, P.guid, B.apc_value AS value, Y.post_title AS Comp, Y.guid AS CompLink, F.post_title AS Team, F.guid AS TeamLink FROM '.$wpdb->prefix.'awards A, '.$wpdb->prefix.'awards_player_comp B, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P, '.$wpdb->prefix.'bb2wp T, '.$wpdb->posts.' Y, '.$wpdb->prefix.'bb2wp D, '.$wpdb->posts.' F, '.$wpdb->prefix.'player X WHERE X.p_id = B.p_id AND X.t_id = D.tid AND D.prefix = \'t_\' AND D.pid = F.ID AND B.c_id = T.tid AND T.prefix = \'c_\' AND T.pid = Y.ID AND A.a_id = B.a_id AND a_cup = 0 AND B.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = P.ID AND A.a_id = '.$aw->a_id.' ORDER BY B.c_id DESC';
						if ($ctawards = $wpdb->get_results($compteamawardssql)) {
							$aoutput .= "					<h4>Player recipients during a Competition</h4>\n					<table>\n						<tr>\n							<th class=\"tbl_name\">Player</th>\n							<th class=\"tbl_name\">Competition</th>\n							<th class=\"tbl_name\">Team</th>\n							<th class=\"tbl_stat\">Value</th>\n						</tr>\n";
							$zebracount = 1;
							foreach ($ctawards as $cta) {
								if ($zebracount % 2) {
									$aoutput .= "						<tr>\n";
								}
								else {
									$aoutput .= "						<tr class=\"tbl_alt\">\n";
								}
								$aoutput .= "							<td><a href=\"".$cta->guid."\" title=\"Read more about ".$cta->post_title."\">".$cta->post_title."</a></td>\n							<td><a href=\"".$cta->CompLink."\" title=\"Read more about ".$cta->Comp."\">".$cta->Comp."</a></td>\n							<td><a href=\"".$cta->TeamLink."\" title=\"Read more about ".$cta->Team."\">".$cta->Team."</a></td>\n						<td>";
								if (0 < $cta->value) {
									$aoutput .= $cta->value;
								}
								else {
									$aoutput .= "n/a";
								}
								$aoutput .= "</td>\n						</tr>\n";
								$zebracount++;
							}
							$aoutput .= "</table>";
						}
					} //end of if else cup
					/*
						Now that we have the output stored in a table we can check to see if it is empty, if not then print
						the award title and description. we can check to see if the output var length is 1 or more.
					*/
					if(isset($aoutput{1})) {
						print("	<h3 class=\"awardtitle\">".$aw->a_name."</h3>\n");
						print("	<div class=\"details\">\n		".wpautop($aw->a_desc)."\n	</div>\n");

						print($aoutput."\n<hr>\n\n");
					}


				} // end of for each
			}
			else {
				print("	<div class=\"info\">\n		<p>There are currently no awards to be won in the HDWSBBL!</p>\n	</div>\n");
			}

		//Did You Know Display Code
		if (function_exists(bblm_display_dyk)) {
			bblm_display_dyk();
		}

?>

				<p class="postmeta"><?php edit_post_link('Edit', ' <strong>[</strong> ', ' <strong>]</strong> '); ?></p>

			</div>


		<?php endwhile;?>
	<?php endif; ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
