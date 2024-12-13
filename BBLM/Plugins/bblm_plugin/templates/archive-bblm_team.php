<?php
/**
 * BBowlLeagueMan Teamplate View Team List
 *
 * Page Template to List the Teams in the League.
 *
 * @author 		Blacksnotling
 * @category 	Template
 * @package 	BBowlLeagueMan/Templates
 */
 /*
  * Template Name: View Team List
  */
?>
<?php get_header(); ?>
<div id="primary" class="content-area content-area-right-sidebar">
  <main id="main" class="site-main" role="main">
  <?php do_action( 'bblm_template_before_posts' ); ?>
	<?php if (have_posts()) : ?>
		<?php do_action( 'bblm_template_before_loop' ); ?>
			<?php do_action( 'bblm_template_before_content' ); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

		<header class="page-header entry-header">

			<h2 class="entry-title"><?php echo __( 'Teams', 'bblm'); ?></h2>

		</header><!-- .page-header -->

			<div class="entry-content">

					<div class="archive-description"><?php echo bblm_echo_archive_desc( 'team' ) ?></div>

<?php
	//Start of Custom content
  $teamsql = 'SELECT T.r_id, T.t_active, T.t_tv, T.t_ctv, T.t_sname, X.type_name, T.t_id, T.WPID AS TWPID FROM '.$wpdb->prefix.'team T, '.$wpdb->prefix.'team_type X WHERE T.type_id = X.type_id AND T.r_id AND T.t_show = 1 ORDER BY T.type_id ASC, T.t_active DESC, T.t_name ASC';

if ( $teams = $wpdb->get_results( $teamsql ) ) {
	$is_first_status = 0;
	$current_status = "";
	$is_first_type = 1;
	$current_type = "";

	$zebracount = 1;
	foreach ( $teams as $team ) {
		if ( $team->type_name !== $current_type ) {
			$current_type = $team->type_name;
			$current_status = $team->t_active;
			if ( 1 !== $is_first_type ) {
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
				$zebracount = 1;
			}
			$is_first_type = 1;
		}
		if ( $team->t_active !== $current_status ) {
			$current_status = $team->t_active;
			if ( 1 !== $is_first_status ) {
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
				$zebracount = 1;
			}
			$is_first_status = 1;
		}

		if ( 1 == $current_status ) {
			$status_title = "Active Teams";
		}
		else {
			$status_title = "Inactive Teams";
		}


		if ( $is_first_type ) {
?>
      <h3><?php echo $team->type_name . __( ' Teams', 'bblm' ); ?></h3>
      <h4 class="bblm-table-caption"><?php echo $status_title ?></h4>
      <div role="region" aria-labelledby="Caption01" tabindex="0">
        <table class="bblm__table bblm_sortable">
          <thead>
            <tr>
              <th>&nbsp;</th>
              <th class="bblm_tbl_name"><?php echo __( 'Team', 'bblm' ); ?></th>
              <th class="bblm_tbl_teamrace"><?php echo __( 'Race', 'bblm' ); ?></th>
              <th class="bblm_tbl_teamvalue"><?php echo __( 'Team Value', 'bblm' ); ?></th>
              <th class="bblm_tbl_stat"><?php echo __( 'Games', 'bblm' ); ?></th>
              <th class="bblm_tbl_teamcup"><?php echo __( 'Championships', 'bblm' ); ?></th>
            </tr>
          </thead>
        <tbody>
<?php
			$is_first_type = 0;
			$is_first_status = 0;
		}
		if ( $is_first_status ) {
?>
      <h4 class="bblm-table-caption"><?php echo $status_title ?></h4>
      <div role="region" aria-labelledby="Caption01" tabindex="0">
        <table class="bblm__table bblm_sortable">
          <thead>
            <tr>
              <th>&nbsp;</th>
              <th class="bblm_tbl_name"><?php echo __( 'Team', 'bblm' ); ?></th>
              <th class="bblm_tbl_teamrace"><?php echo __( 'Race', 'bblm' ); ?></th>
              <th class="bblm_tbl_teamvalue"><?php echo __( 'Team Value', 'bblm' ); ?></th>
              <th class="bblm_tbl_stat"><?php echo __( 'Games', 'bblm' ); ?></th>
              <th class="bblm_tbl_teamcup"><?php echo __( 'Championships', 'bblm' ); ?></th>
            </tr>
          </thead>
        <tbody>
<?php
			$is_first_status = 0;
		}
		if ( $zebracount % 2 ) {
      echo '<tr class="bblm_tbl_alt" id="' . $team->t_id . '">';
		}
		else {
			echo '<tr id="' . $team->t_id . '">';
		}
		echo '<td>';
    BBLM_CPT_Team::display_team_logo( $team->TWPID, 'icon' );
    echo '</td>';
    echo '<td>' . bblm_get_team_link( $team->TWPID ) .  '</td>';
    echo '<td>' . bblm_get_race_name( $team->r_id ) . '</td>';
    echo '<td>' . number_format( $team->t_ctv ) . 'gp</td>';


		$nummatchsql = 'SELECT COUNT(*) AS NMATCH FROM '.$wpdb->prefix.'match_team T, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C WHERE T.m_id = M.WPID AND M.c_id = C.WPID AND C.c_counts = 1 AND T.t_id = '.$team->t_id;
		$nummatch = $wpdb->get_var( $nummatchsql );
		//If not more than 1 then team is new, set to 0 as the default result will be null).
		if ( NULL == $nummatch ) {
			$nummatch = 0;
		}

    echo '<td>' . $nummatch . '</td>';

		$cupscountsql = 'SElECT B.a_id, A.a_name, COUNT(*) AS ANUM FROM '.$wpdb->prefix.'awards_team_comp AS B, '.$wpdb->prefix.'awards AS A WHERE A.a_id = B.a_id AND (B.a_id = 1 or B.a_id = 2 or B.a_id = 3) AND B.t_id = '.$team->t_id.' GROUP BY B.a_id ORDER BY B.a_id ASC';
		if ($cups = $wpdb->get_results($cupscountsql)) {
			echo '<td class="bblm_tbl_teamcup">';
			foreach ( $cups as $cup ) {
			print("<img src=\"".home_url()."/images/misc/cup".$cup->a_id."-".$cup->ANUM.".gif\" alt=\"".$cup->ANUM." ".$cup->a_name." Trophy\" />");
			}
			print("</td>\n	</tr>\n");
		}
		else {
			//No Cups won, or error
			echo '<td>&nbsp;</td>';
      echo '</tr>';
		}

		$zebracount++;
	}
  echo '</tbody>';
  echo '</table>';
  echo '</div>';
}
else {
	echo '<div class="bblm_info"> <p>There are no Teams currently set-up!</p>	</div>';
}

//End of Custom content

?>

</div><!-- .entry-content -->

<footer class="entry-footer">
	<p class="postmeta"><?php bblm_display_page_edit_link(); ?></p>
</footer><!-- .entry-footer -->

</article><!-- .post-ID -->

<?php do_action( 'bblm_template_after_content' ); ?>
<?php do_action( 'bblm_template_after_loop' ); ?>
<?php endif; ?>
<?php do_action( 'bblm_template_after_posts' ); ?>
</main><!-- #main -->
</div><!-- #primary -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>
