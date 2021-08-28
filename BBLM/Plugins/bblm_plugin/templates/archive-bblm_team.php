<?php
/*
Template Name: List Teams
*/
/*
*	Filename: bb.core.teams.php
*	Description: Page template to list the teams.
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
	//Start of Custom content
	//$teamsql = "SELECT P.post_title, P.guid FROM '.$wpdb->prefix.'team AS R, $wpdb->posts AS P, '.$wpdb->prefix.'bb2wp AS J WHERE R.t_id = J.tid AND P.ID = J.pid AND J.prefix = 't_' AND R.t_show = 1 ORDER BY t_name ASC";
	$teamsql = 'SELECT P.post_title, T.r_id, P.guid, T.t_active, T.t_tv, T.t_ctv, T.t_sname, X.type_name, T.t_id FROM '.$wpdb->prefix.'team T, '.$wpdb->posts.' P, '.$wpdb->prefix.'bb2wp J, '.$wpdb->prefix.'team_type X WHERE T.type_id = X.type_id AND T.r_id AND T.t_id = J.tid AND P.ID = J.pid AND J.prefix = \'t_\' AND T.t_show = 1 ORDER BY T.type_id ASC, T.t_active DESC, P.post_title ASC';


if ($teams = $wpdb->get_results($teamsql)) {
	$is_first_status = 0;
	$current_status = "";
	$is_first_type = 1;
	$current_type = "";

	$zebracount = 1;
	foreach ($teams as $team) {
		if ($team->type_name !== $current_type) {
			$current_type = $team->type_name;
			$current_status = $team->t_active;
			if (1 !== $is_first_type) {
				print(" 	</tbody>\n	</table>\n");
				$zebracount = 1;
			}
			$is_first_type = 1;
		}
		if ($team->t_active !== $current_status) {
			$current_status = $team->t_active;
			if (1 !== $is_first_status) {
				print(" 	</tbody>\n	</table>\n");
				$zebracount = 1;
			}
			$is_first_status = 1;
		}

		if (1 == $current_status) {
			$status_title = "Active Teams";
		}
		else {
			$status_title = "Inactive Teams";
		}


		if ($is_first_type) {
			print("<h3>".$team->type_name." Teams</h3>\n <h4>".$status_title."</h4>\n  <table class=\"bblm__table bblm_sortable\">\n	<thead>\n	<tr>\n		<th>&nbsp;</th>\n		<th class=\"bblm_tbl_name\">Team</th>\n		<th class=\"bblm_tbl_teamrace\">Race</th>\n		<th class=\"bblm_tbl_teamvalue\">Team Value</th>\n		<th class=\"bblm_tbl_stat\">Games</th>\n		<th class=\"bblm_tbl_teamcup\">Championships</th>\n	</tr>\n	</thead>\n	<tbody>\n");
			$is_first_type = 0;
			$is_first_status = 0;
		}
		if ($is_first_status) {
			print("<h4 class=\"bblm-table-caption\">".$status_title."</h4>\n  <table class=\"bblm_table bblm_sortable\">\n	<thead>\n	<tr>\n		<th>&nbsp;</th>\n		<th class=\"bblm_tbl_name\">Team</th>\n		<th class=\"bblm_tbl_teamrace\">Race</th>\n		<th class=\"bblm_tbl_teamvalue\">Team Value</th>\n		<th class=\"bblm_tbl_stat\">Games</th>\n		<th class=\"bblm_tbl_teamcup\">Championships</th>\n	</tr>\n	</thead>\n	<tbody>\n");
			$is_first_status = 0;
		}
		if ($zebracount % 2) {
			print("		<tr id=\"".$team->t_id."\">\n");
		}
		else {
			print("		<tr class=\"bblm_tbl_alt\" id=\"".$team->t_id."\">\n");
		}
		print("		<td>");

		$filename = $_SERVER['DOCUMENT_ROOT']."/images/teams/".$team->t_sname."_small.gif";
		if (file_exists($filename)) {
			print("<img src=\"".home_url()."/images/teams/".$team->t_sname."_small.gif\" alt=\"".$team->t_sname." Logo\" />");
		}
		else {
			BBLM_CPT_Race::display_race_icon( $team->r_id, 'icon' );
		}
		print("</td>\n		<td><a href=\"".$team->guid."\" title=\"View more informaton about ".$team->post_title."\">".$team->post_title."</a></td>\n		<td>" . bblm_get_race_name( $team->r_id ) . "</td>\n		<td>".number_format($team->t_ctv)."gp</td>\n");


		$nummatchsql = 'SELECT COUNT(*) AS NMATCH FROM '.$wpdb->prefix.'match_team T, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C WHERE T.m_id = M.WPID AND M.c_id = C.WPID AND C.c_counts = 1 AND T.t_id = '.$team->t_id;
		$nummatch = $wpdb->get_var($nummatchsql);
		//If not more than 1 then team is new, set to 0 as the default result will be null).
		if (NULL == $nummatch) {
			$nummatch = 0;
		}

		print("		<td>".$nummatch."</td>\n");

		$cupscountsql = 'SElECT B.a_id, A.a_name, COUNT(*) AS ANUM FROM '.$wpdb->prefix.'awards_team_comp AS B, '.$wpdb->prefix.'awards AS A WHERE A.a_id = B.a_id AND (B.a_id = 1 or B.a_id = 2 or B.a_id = 3) AND B.t_id = '.$team->t_id.' GROUP BY B.a_id ORDER BY B.a_id ASC';
		if ($cups = $wpdb->get_results($cupscountsql)) {
			print("		<td class=\"bblm_tbl_teamcup\">");
			foreach ($cups as $cup) {
			print("<img src=\"".home_url()."/images/misc/cup".$cup->a_id."-".$cup->ANUM.".gif\" alt=\"".$cup->ANUM." ".$cup->a_name." Trophy\" />");
			}
			print("</td>\n	</tr>\n");
		}
		else {
			//No Cups won, or error
			print("		<td>&nbsp;</td>\n	</tr>\n");
		}

		$zebracount++;
	}
	print("	</tbody>\n	</table>\n");
}
else {
	print("	<div class=\"bblm_info\">\n		<p>There are no Teams currently set-up!</p>	</div>\n");
}

//End of Custom content

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
