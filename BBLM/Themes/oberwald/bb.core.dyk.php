<?php
/*
Template Name: Did You Know Listing.
*/
/*
*	Filename: bb.core.dyk.php
*	Version: 1.1b
*	Description: .Page template to display the DYK listing
*/
/* -- Change History --
20090201 - 1.0 - Initial creation of file.
20100123 - 1.1 - Updated the prefix for the custom bb tables in the Database (tracker [225])

*/
?>
<?php get_header(); ?>
	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
		<div id="breadcrumb">
			<p><a href="<?php echo get_option('home'); ?>" title="Back to the front of the HDWSBBL">HDWSBBL</a> &raquo; <?php the_title(); ?></p>
		</div>
			<div class="entry">
				<h2><?php the_title(); ?></h2>

				<?php the_content('Read the rest of this entry &raquo;'); ?>
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


				<p class="postmeta"><?php edit_post_link('Edit', ' <strong>[</strong> ', ' <strong>]</strong> '); ?></p>

			</div>


		<?php endwhile;?>
	<?php endif; ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>