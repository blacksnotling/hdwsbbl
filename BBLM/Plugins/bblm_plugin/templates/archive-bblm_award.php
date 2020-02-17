<?php
/**
 * BBowlLeagueMan Teamplate List Awards
 *
 * Page Template to List all awards and winners
 *
 * @author 		Blacksnotling
 * @category 	Template
 * @package 	BBowlLeagueMan/Templates
 */
/*
 * Template Name: list Awards
 */
?>
<?php get_header(); ?>
<div id="primary" class="content-area content-area-right-sidebar">
  <main id="main" class="site-main" role="main">
  <?php do_action( 'bblm_template_before_posts' ); ?>
	<?php if (have_posts()) : ?>
		<?php do_action( 'bblm_template_before_loop' ); ?>
		<?php while (have_posts()) : the_post(); ?>
			<?php do_action( 'bblm_template_before_content' ); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

		<header class="page-header entry-header">

			<h2 class="entry-title"><?php the_title(); ?></h2>

		</header><!-- .page-header -->

			<div class="entry-content">

					<?php the_content(); ?>
<?php
			$awardssql = 'SELECT * FROM '.$wpdb->prefix.'awards WHERE a_id !=4 ORDER BY a_id ASC';
			if ($awards = $wpdb->get_results($awardssql)) {
				foreach ($awards as $aw) {
					$aoutput = "";

					if ($aw->a_cup) {
						//The award in question is a Championship
						$compmajorawardssql = 'SELECT P.post_title, P.guid, H.post_title AS CompName, H.guid AS CompLink FROM '.$wpdb->prefix.'awards A, '.$wpdb->prefix.'awards_team_comp B, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp Y, '.$wpdb->posts.' H WHERE C.c_id = Y.tid AND Y.prefix = \'c_\' AND Y.pid = H.ID AND A.a_id = B.a_id AND a_cup = 1 AND B.t_id = J.tid AND J.prefix = \'t_\' AND J.pid = P.ID AND B.c_id = C.c_id AND C.c_show = 1 AND C.c_counts = 1 AND A.a_id = '.$aw->a_id.' ORDER BY C.c_id DESC';
						if (($cmawards = $wpdb->get_results($compmajorawardssql)) && (0 < count($cmawards))) {
							$aoutput .= "					<table class=\"bblm_table\">\n						<tr>\n							<th class=\"bblm_tbl_name\">Team</th>\n							<th class=\"bblm_tbl_name\">Competition</th>\n						</tr>\n";
							$zebracount = 1;
							foreach ($cmawards as $cma) {
								if ($zebracount % 2) {
									$aoutput .="						<tr>\n";
								}
								else {
									$aoutput .= "						<tr class=\"bblm_tbl_alt\">\n";
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
						$compteamawardssql = 'SELECT T.WPID, B.ats_value AS value, B.sea_id FROM '.$wpdb->prefix.'awards A, '.$wpdb->prefix.'awards_team_sea B, '.$wpdb->prefix.'team T WHERE A.a_id = B.a_id AND a_cup = 0 AND B.t_id = T.t_id AND A.a_id = '.$aw->a_id.' ORDER BY B.sea_id DESC';
						if ( $ctawards = $wpdb->get_results( $compteamawardssql ) ) {
							$aoutput .= '<h4>Team recipients during a Season</h4>';
							$aoutput .= '<table class="bblm_table">
													<thead>
														<tr>
															<th class="bblm_tbl_name">' . __( "Team", "bblm" ) . '</th>
															<th class="bblm_tbl_name">' . __( "Season", "bblm" ) . '</th>
															<th class="bblm_tbl_stat">' . __( "Value", "bblm" ) . '</th>
														</tr>
													</thead>
													<tbody>';
							$zebracount = 1;
							foreach ( $ctawards as $cta ) {
								if ( $zebracount % 2 ) {
									$aoutput .= '<tr>';
								}
								else {
									$aoutput .= '<tr class="bblm_tbl_alt">';
								}
								$aoutput .= '<td>' . bblm_get_season_link( $cta->WPID ) . '</td>
														 <td>' . bblm_get_season_link( $cta->sea_id ) . '</td>
														 <td>';
								if ( 0 < $cta->value ) {
									$aoutput .= $cta->value;
								}
								else {
									$aoutput .= "n/a";
								}
								$aoutput .= '</td>
														</tr>';
								$zebracount++;
							}
							$aoutput .= '</tbody>
													</table>';
						}

						//2. Awards to Players in a season
						$compteamawardssql = 'SELECT P.WPID, B.aps_value AS value, B.sea_id FROM '.$wpdb->prefix.'awards A, '.$wpdb->prefix.'awards_player_sea B, '.$wpdb->prefix.'player P WHERE A.a_id = B.a_id AND a_cup = 0 AND B.p_id = P.p_id AND A.a_id = '.$aw->a_id.' ORDER BY B.sea_id DESC';
						if ( $ctawards = $wpdb->get_results( $compteamawardssql ) ) {
							$aoutput .= '<h4>Player recipients during a Season</h4>';
							$aoutput .= '<table class="bblm_table">
													<thead>
														<tr>
															<th class="bblm_tbl_name">' . __( "Team", "bblm" ) . '</th>
															<th class="bblm_tbl_name">' . __( "Season", "bblm" ) . '</th>
															<th class="bblm_tbl_stat">' . __( "Value", "bblm" ) . '</th>
														</tr>
													</thead>
													<tbody>';
							$zebracount = 1;
							foreach ( $ctawards as $cta ) {
								if ( $zebracount % 2 ) {
									$aoutput .= '<tr>';
								}
								else {
									$aoutput .= '<tr class="bblm_tbl_alt">';
								}
								$aoutput .= '<td>' . bblm_get_season_link( $cta->WPID ) . '</td>
														 <td>' . bblm_get_season_link( $cta->sea_id ) . '</td>
														 <td>';
								if ( 0 < $cta->value ) {
									$aoutput .= $cta->value;
								}
								else {
									$aoutput .= "n/a";
								}
								$aoutput .= '</td>
														</tr>';
								$zebracount++;
							}
							$aoutput .= '</tbody>
													</table>';
						}

						//3. Awards to teams in a competition
						$compteamawardssql = 'SELECT P.post_title, P.guid, B.atc_value AS value, Y.post_title AS Comp, Y.guid AS CompLink FROM '.$wpdb->prefix.'awards A, '.$wpdb->prefix.'awards_team_comp B, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P, '.$wpdb->prefix.'bb2wp T, '.$wpdb->posts.' Y WHERE B.c_id = T.tid AND T.prefix = \'c_\' AND T.pid = Y.ID AND A.a_id = B.a_id AND a_cup = 0 AND B.t_id = J.tid AND J.prefix = \'t_\' AND J.pid = P.ID AND A.a_id = '.$aw->a_id.' ORDER BY B.c_id DESC';
						if ($ctawards = $wpdb->get_results($compteamawardssql)) {
							$aoutput .= "					<h4>Team recipients during a Competition</h4>\n					<table class=\"bblm_table\">\n						<tr>\n							<th class=\"bblm_tbl_name\">Team</th>\n							<th class=\"bblm_tbl_name\">Competition</th>\n							<th class=\"bblm_tbl_stat\">Value</th>\n						</tr>\n";
							$zebracount = 1;
							foreach ($ctawards as $cta) {
								if ($zebracount % 2) {
									$aoutput .= "						<tr>\n";
								}
								else {
									$aoutput .= "						<tr class=\"bblm_tbl_alt\">\n";
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
						$compteamawardssql = 'SELECT P.post_title, P.guid, B.apc_value AS value, Y.post_title AS Comp, Y.guid AS CompLink, D.WPID FROM '.$wpdb->prefix.'awards A, '.$wpdb->prefix.'awards_player_comp B, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P, '.$wpdb->prefix.'bb2wp T, '.$wpdb->posts.' Y, '.$wpdb->prefix.'team D, '.$wpdb->prefix.'player X WHERE X.p_id = B.p_id AND X.t_id = D.t_id AND B.c_id = T.tid AND T.prefix = \'c_\' AND T.pid = Y.ID AND A.a_id = B.a_id AND a_cup = 0 AND B.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = P.ID AND A.a_id = '.$aw->a_id.' ORDER BY B.c_id DESC';
						if ($ctawards = $wpdb->get_results($compteamawardssql)) {
							$aoutput .= "					<h4>Player recipients during a Competition</h4>\n					<table class=\"bblm_table\">\n						<tr>\n							<th class=\"bblm_tbl_name\">Player</th>\n							<th class=\"bblm_tbl_name\">Competition</th>\n							<th class=\"bblm_tbl_name\">Team</th>\n							<th class=\"bblm_tbl_stat\">Value</th>\n						</tr>\n";
							$zebracount = 1;
							foreach ($ctawards as $cta) {
								if ($zebracount % 2) {
									$aoutput .= "						<tr>\n";
								}
								else {
									$aoutput .= "						<tr class=\"bblm_tbl_alt\">\n";
								}
								$aoutput .= "							<td><a href=\"".$cta->guid."\" title=\"Read more about ".$cta->post_title."\">".$cta->post_title."</a></td>\n							<td><a href=\"".$cta->CompLink."\" title=\"Read more about ".$cta->Comp."\">".$cta->Comp."</a></td>\n							<td><a href=\"".  get_post_permalink( $cta->WPID ) ."\" title=\"Read more about this team\">" . esc_html( get_the_title( $cta->WPID ) ) . "</a></td>\n						<td>";
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
						print("	<div class=\"bblm_details\">\n		".wpautop($aw->a_desc)."\n	</div>\n");

						print($aoutput."\n<hr>\n\n");
					}


				} // end of for each
			}
			else {
				print("	<div class=\"bblm_info\">\n		<p>There are currently no awards to be won in the League!</p>\n	</div>\n");
			}


?>

</div><!-- .entry-content -->

<footer class="entry-footer">
	<p class="postmeta"><?php bblm_display_page_edit_link(); ?></p>
</footer><!-- .entry-footer -->

</article><!-- .post-ID -->

<?php do_action( 'bblm_template_after_content' ); ?>
<?php endwhile; ?>
<?php do_action( 'bblm_template_after_loop' ); ?>
<?php endif; ?>
<?php do_action( 'bblm_template_after_posts' ); ?>
</main><!-- #main -->
</div><!-- #primary -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>
