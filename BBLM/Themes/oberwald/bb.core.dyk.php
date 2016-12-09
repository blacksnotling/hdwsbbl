<?php
/*
Template Name: Did You Know Listing.
*/
/*
*	Filename: bb.core.dyk.php
*	Description: .Page template to display the DYK listing
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
		$dyksql = 'SELECT dyk_id, dyk_type, dyk_title, dyk_desc FROM '.$wpdb->prefix.'dyk WHERE dyk_show = 1 ORDER BY dyk_id DESC';
		if ($dyks = $wpdb->get_results($dyksql)) {

			foreach ($dyks as $d) {
?>
				<div class="dykcontainer <?php if ($d->dyk_type) { print("dyktrivia"); } else { print("dykfact"); } ?>" id="dyk<?php print($d->dyk_id); ?>">
					<h3 class="dykheader">HDWSBBL - <?php if($d->dyk_type) { print("Did You Know"); } else { print("Fact"); } ?></h3>
<?php
				if ("none" !== $d->dyk_title) {
					print("					<h4>".$d->dyk_title."</h4>\n");
				}
?>
					<?php print(wpautop($d->dyk_desc)); ?>
				</div>
<?php
			}//end of for each
		}//end of if SQL
?>


				<p class="postmeta"><?php edit_post_link( __( 'Edit', 'oberwald' ), ' <strong>[</strong> ', ' <strong>]</strong> '); ?></p>

			</div>
		</div>


		<?php endwhile;?>
	<?php endif; ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
