<?php
/**
 * BBowlLeagueMan Teamplate View Race
 *
 * Page Template to view Race's details
 *
 * @author 		Blacksnotling
 * @category 	Template
 * @package 	BBowlLeagueMan/Templates
 */
/*
 * Template Name: View Race
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

        <header class="entry-header">
          <h2 class="entry-title"><?php the_title(); ?></h2>
        </header><!-- .entry-header -->

				<div class="entry-content">

				<div class="bblm_details bblm_race_description">
					<?php the_content(); ?>
				</div>
<?php
				$racesql = "SELECT P.*, R.r_rrcost, R.r_id FROM ".$wpdb->prefix."race R, ".$wpdb->posts." P, ".$wpdb->prefix."bb2wp J WHERE R.r_id = J.tid AND P.ID = J.pid AND P.ID = ".$post->ID;
				if ($races = $wpdb->get_results($racesql)) {
					print("<ul\n>");
					foreach ($races as $race) {
						print("	<li><strong>Re-Roll Cost</strong>: ".number_format($race->r_rrcost)."gp</li>\n");
						$race_id = $race->r_id;
					}
					print("</ul>\n");
				}
				else {
					print("	<ul>\n	<li><strong>Re-Roll Cost</strong>: Not Available</li>\n</ul>\n");
				}

				if (isset($race_id)) {
					//we only want to continue if the above selection returned something.
					print("<h3>Positions available for Race</h3>\n");
					//Grab Positions
					$positionsql = 'SELECT * FROM '.$wpdb->prefix.'position WHERE pos_status = 1 AND r_id = '.$race_id.' ORDER by pos_cost ASC';
					if ($positions = $wpdb->get_results($positionsql)) {
						$zebracount = 1;
						print("<table class=\"bblm_table\">\n	<tr>\n		<th>Name</th>\n		<th>Limit</th>\n		<th class=\"bblm_tbl_stat\">MA</th>\n		<th class=\"bblm_tbl_stat\">ST</th>\n		<th class=\"bblm_tbl_stat\">AG</th>\n		<th class=\"bblm_tbl_stat\">AV</th>\n		<th>Skills</th>\n		<th>Cost</th>\n	</tr>\n");
						foreach ($positions as $pos) {
							if ($zebracount % 2) {
								print("		<tr id=\"pos-".$pos->pos_id."\">\n");
							}
							else {
								print("	<tr class=\"bblm_tbl_alt\" id=\"pos-".$pos->pos_id."\">\n");
							}
							print("		<td>" . esc_html( $pos->pos_name ) . "</td>\n		<td>0 - ".$pos->pos_limit."</td>\n		<td>".$pos->pos_ma."</td>\n		<td>".$pos->pos_st."</td>\n		<td>".$pos->pos_ag."</td>\n		<td>".$pos->pos_av."</td>\n		<td class=\"bblm_tbl_skills\">".$pos->pos_skills."</td>\n		<td>".number_format($pos->pos_cost)."gp</td>\n	</tr>\n");
							$zebracount++;
						}
						print("</table>\n");
					}
					else {
						print("	<div class=\"bblm_info\">\n		<p>Sorry, but no positions have been filled out for this race</p>\n	</div>\n");
					}

					//Availible Star Players
					$starplayersql = 'SELECT P.post_title, P.guid, X.p_ma, X.p_st, X.p_ag, X.p_av, X.p_skills, X.p_cost FROM '.$wpdb->prefix.'race2star S, '.$wpdb->prefix.'posts P, '.$wpdb->prefix.'bb2wp J, '.$wpdb->prefix.'player X WHERE J.pid = P.ID AND J.prefix = \'p_\' AND J.tid = S.p_id AND X.p_id = S.p_id AND S.r_id = '.$race_id.' AND X.p_status = 1 ORDER BY P.post_title ASC LIMIT 0, 30 ';
					if ($starplayer = $wpdb->get_results($starplayersql)) {
						$zebracount = 1;
						print("<h3>Star Players available for Race</h3>\n");
						print("<table class=\"bblm_table\">\n	<tr>\n		<th>Name</th>\n		<th class=\"bblm_tbl_stat\">MA</th>\n		<th class=\"bblm_tbl_stat\">ST</th>\n		<th class=\"bblm_tbl_stat\">AG</th>\n		<th class=\"bblm_tbl_stat\">AV</th>\n		<th>Skills</th>\n		<th>Cost</th>\n	</tr>\n");
						foreach ($starplayer as $star) {
							if ($zebracount % 2) {
								print("		<tr>\n");
							}
							else {
								print("	<tr class=\"bblm_tbl_alt\">\n");
							}
							print("		<td><a href=\"".$star->guid."\" title=\"See more details of this player\">".$star->post_title."</a></td>\n		<td>".$star->p_ma."</td>\n		<td>".$star->p_st."</td>\n		<td>".$star->p_ag."</td>\n		<td>".$star->p_av."</td>\n		<td class=\"bblm_tbl_skills\">".$star->p_skills."</td>\n		<td>".number_format($star->p_cost)."gp</td>\n	</tr>\n");
							$zebracount++;
						}
						print("</table>\n");
					}


					print("<h3>Teams belonging to this Race</h3>\n");
					$teamsql = 'SELECT T.WPID FROM '.$wpdb->prefix.'team T WHERE T.t_show = 1 AND T.r_id = '.$race_id.' ORDER by T.t_name ASC';
					if ($teams = $wpdb->get_results($teamsql)) {
						print("<ul>\n");
						foreach ($teams as $td) {
							print("<li><a href=\"" . get_post_permalink( $td->WPID ) . "\" title=\"View more details of this team\">" . esc_html( get_the_title( $td->WPID ) ) . "</a></li>\n");
						}
						print("</ul>\n");
					}
					else {
						print("	<div class=\"bblm_info\">\n		<p>There are currently no teams representing this Race.</p>\n	</div>.\n");
					}
				}
				else {
					print("	<div class=\"bblm_info\">\n		<p>There are currently no details set up for this Race.</p>\n </div>\n");
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
