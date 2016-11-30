<?php
/*
Template Name: List Competitions
*/
/*
*	Filename: bb.core.comp.php
*	Description: Page template to list the compettions.
*/
?>
<?php get_header(); ?>
	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
			<div class="entry">
				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<h2 class="entry-title"><?php the_title(); ?></h2>

					<?php the_content(); ?>
				<?php

				$compsql = 'SELECT P.post_title, P.guid, L.post_title AS SEAname, L.guid AS SEAlink, M.post_title AS CupName, C.c_active FROM '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P, '.$wpdb->prefix.'bb2wp K, '.$wpdb->posts.' L, '.$wpdb->prefix.'bb2wp N, '.$wpdb->posts.' M WHERE C.series_id = N.tid AND N.prefix = \'series_\' AND N.pid = M.ID AND K.tid = C.sea_id AND K.prefix = \'sea_\' AND K.pid = L.ID AND C.c_id = J.tid AND J.prefix = \'c_\' AND J.pid = P.ID AND C.c_show = 1 ORDER BY C.sea_id DESC, C.c_sdate DESC';
				if ($comps = $wpdb->get_results($compsql)) {
					$is_first = 1;
					$current_sea = "";

					foreach ($comps as $c) {
						if ($c->SEAname !== $current_sea) {
							$current_sea = $c->SEAname;
							if (1 !== $is_first) {
								print("				</ul>\n");
							}
							$is_first = 1;
						}
						if ($is_first) {
							print("				<h3><a href=\"".$c->SEAlink."\" title=\"View more about this Season\">".$c->SEAname."</a></h3>\n				<ul>\n");
							$is_first = 0;
						}
						print("					<li");
						if ($c->c_active) {
							print(" class=\"active\"");
						}
						print("><a href=\"".$c->guid."\" title=\"View the standings from the ".$c->post_title."\">".$c->post_title."</a> - (".$c->CupName.")</li>\n");
					}
					print("				</ul>\n");
				}
				else {
					print("  <p>Sorry, but no Competitions could be retrieved at this time, please try again later.</p>\n");
				}

?>
				<p class="postmeta"><?php edit_post_link( __( 'Edit', 'oberwald' ), ' <strong>[</strong> ', ' <strong>]</strong> '); ?></p>




			</div>
		</div>


		<?php endwhile;?>
		<?php endif; ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
