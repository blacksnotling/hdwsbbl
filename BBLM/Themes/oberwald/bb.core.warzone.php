<?php
/*
Template Name: Warzone Front Page
*/
/*
*	Filename: bb.core.warzone.php
*	Version: 1.0.1
*	Description: .The Template for the front page of the Warzone
*/
/* -- Change History --
20080821 - 1.0 - Initial creation and completion of file.
20090712 - 1.0.1 - Added DYK code to page

*/
//Define the var so the front page specific stuff is activated in the header
	$iswarzonepage = 1;
	require(TEMPLATEPATH . "/header.php"); ?>
	<?php if (have_posts()) : ?>

		<h2>The HDWSBBL:WarZone</h2>
		<div id="breadcrumb">
			<p><a href="<?php echo get_option('home'); ?>" title="Back to the front of the HDWSBBL">HDWSBBL</a> &raquo; Warzone</p>
		</div>
		<ul class="subnav">
			<li><a href="<?php echo get_option('home'); ?>/category/warzone/page/2/" title="View Previous Warzone entries">&laquo; Previous Entries</a></li>
		</ul>


<?php
	    $recentPosts = new WP_Query();
	    $recentPosts->query('cat=13&showposts=6');
?>
		<?php while ($recentPosts->have_posts()) : $recentPosts->the_post(); ?>
			<div class="entry">
				<h2 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
				<p class="postdate"><?php the_time('F jS, Y') ?> <!-- by <?php the_author(); ?> --></p>

				<?php the_content('Read the rest of this entry &raquo;'); ?>

				<p class="postmeta">Posted in <?php the_category(',') ?> <strong>|</strong> <?php edit_post_link('Edit', ' <strong>[</strong> ', ' <strong>]</strong> '); ?>  <?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?></p>

			</div>


		<?php endwhile; else: ?>
			<p><?php _e('Sorry, no posts have been filed under this topic.'); ?></p>
		<?php endif; ?>



		<ul class="subnav">
			<?php next_posts_link('<li>&laquo; Previous Entries</li>') ?>
			<?php previous_posts_link('<li>Next Entries &raquo;</li>') ?>
		</ul>
<?php
		//Did You Know Display Code
		if (function_exists(bblm_display_dyk)) {
			bblm_display_dyk();
		}
?>

</div><!-- end of #maincontent -->
	<div id="subcontent">
		<ul>
			<li><h2>About the Warzone</h2>
				<p>The Warzone is the HDWSBBL's source for the latest team news a gossip. It presents a weekly update on the league and its happenings.</p>
				<p><a href="<?php echo get_option('home'); ?>/about/#warzone" title="Read more about the Warzone">Read More about the Warzone</a></p>
			</li>
			<li><h2>Latest From the Warzone</h2>
<?php
			query_posts('cat=13&showposts=6');
			if (have_posts()) :
				print("<ul>\n");
				while (have_posts()) : the_post();
	?>
					<li><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></li>
	<?php
				endwhile;
				print("</ul>\n");
			endif;
	?>
			</li>

		<!-- note, no container div due to widget printing them -->
			<?php widget_bblm_listcomps(array()) ?>
			<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('sidebar-common') ) : ?>
			<li><h2 class="widgettitle">Opps</h2>
			  <ul>
			   <li>Something has gone wrong and you have lost your widget settings. better log in quick and fix it!</li>
			  </ul>
			</li>
<?php endif;
?>

		</ul>
	</div><!-- end of #subcontent -->
<?php get_footer(); ?>