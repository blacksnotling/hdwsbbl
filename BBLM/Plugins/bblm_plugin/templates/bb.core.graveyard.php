<?php
/**
 * BBowlLeagueMan Teamplate Graveyard
 *
 * Page Template for the Graveyard (All the dead players)
 *
 * @author 		Blacksnotling
 * @category 	Template
 * @package 	BBowlLeagueMan/Templates
 */
/*
 * Template Name: Graveyard
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
		//SQL to determine the Eagle Award WInners. This will be used to highlight the 'winners'
		$eaglesql = 'SELECT p_id FROM `'.$wpdb->prefix.'awards_player_sea` WHERE a_id = 16';
		if ($eagles = $wpdb->get_results($eaglesql, ARRAY_N)) {
			$eagles_exist = 1;

			//Do not mind me
/*			print("<pre>");
			print_r($eagles);
			print("</pre>");*/
		}

		//Main SQL Query to determine the Dead
		$deadsql = 'SELECT P.p_id, T.WPID, K.post_title, K.guid, P.p_num, O.pos_name, UNIX_TIMESTAMP(M.m_date) AS mdate, F.f_id FROM '.$wpdb->prefix.'player_fate F, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' K, '.$wpdb->prefix.'position O, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C WHERE (f_id = 1 OR f_id = 6 OR f_id = 7) AND F.p_id = P.p_id AND P.t_id = T.t_id AND P.p_id = J.tid AND J.prefix = \'p_\' AND J.pid = K.ID AND P.pos_id = O.pos_id AND M.m_id = F.m_id AND M.c_id = C.WPID AND T.t_show = 1 ORDER BY T.t_name ASC, P.p_num ASC, K.post_title ASC';
		if ($dead = $wpdb->get_results($deadsql)) {
			$is_first = 1;
			$last_team = "";

			foreach ($dead as $d) {
				if ($d->WPID != $last_team) {
					if (1 != $is_first) {
						//If the team is not the first, and we are here, we close the div
						print("	</div><!-- end of .gycontainer -->\n");
					}
					else {
						//This is the first so no longer have it set
						$is_first = 0;
					}
						$team_name = esc_html( get_the_title( $d->WPID ) );
						print("\n	<div class=\"gycontainer\">\n		<h3><a href=\"" . get_post_permalink( $d->WPID ) . "\" title=\"Read more about " . $team_name . "\">" . $team_name . "</a></h3>\n");
						$last_team = $d->WPID;
				}//end of if team does not match

?>
		<div class="gyplayer gyfate<?php print($d->f_id); ?>">
			<ul>
				<li><a href="<?php print($d->guid); ?>" title="See more on the career of <?php print($d->post_title); ?>"><?php print($d->post_title); ?></a> (#<?php print($d->p_num); ?> - <?php print( esc_html( $d->pos_name ) ); ?>)</li>
				<li>Died: <?php print(date("d.m.25y", $d->mdate));?></li>
<?php
			//If the player has won an eagle award, let everyone know
			//first we check something is in the eagles array
			if ($eagles_exist) {
				if (in_array_recursive($d->p_id, $eagles)) {
					print("				<li class=\"gyhighlight\">Eagle Award Winning Death!</li>\n");
				}
			}
?>
			</ul>
		</div><!-- end of .gyplayer -->
<?php

			}//end of foreach $dead
			print("	</div><!-- end of .gycontainer -->\n");
		}
		//nobody has died!
		else {
			print("	<p>Nobody has died!</p>\n");
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
