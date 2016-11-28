<?php
	//Define the var so the front page specific stuff is activated in the header
	$iswarzonepage = 1;
	require(TEMPLATEPATH . "/header.php"); ?>
	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
		<div id="breadcrumb">
			<p><a href="<?php echo home_url(); ?>" title="Back to the front of the HDWSBBL">HDWSBBL</a> &raquo; <a href="<?php echo home_url(); ?>/warzone/" title="Back to the HDWSBBL Warzone">Warzone</a> &raquo; <?php the_title(); ?></p>
		</div>

		<ul class="subnav">
			<?php next_posts_link('<li>&laquo; Previous Entries</li>') ?>
			<?php previous_posts_link('<li>Next Entries &raquo;</li>') ?>
		</ul>

			<div class="entry">
				<h2 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
				<p class="postdate"><?php the_time('F jS, Y') ?> <!-- by <?php the_author(); ?> --></p>

				<?php the_content('Read the rest of this entry &raquo;'); ?>

<?php
		//Did You Know Display Code
		if (function_exists(bblm_display_dyk)) {
			bblm_display_dyk();
		}
?>

				<p class="postmeta">Posted in <?php the_category(',') ?> <strong>|</strong> <?php edit_post_link('Edit', ' <strong>[</strong> ', ' <strong>]</strong> '); ?>  <?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?></p>

			</div>
	<?php comments_template(); ?>

		<?php endwhile; ?>
		<?php endif; ?>


</div><!-- end of #maincontent -->
	<div id="subcontent">
		<ul>
			<li><h2>About the Warzone</h2>
				<p>The Warzone is the HDWSBBL's source for the latest team news a gossip. It presents a weekly update on the league and its happenings.</p>
				<p><a href="<?php echo home_url(); ?>/about/#warzone" title="Read more about the Warzone">Read More about the Warzone</a></p>
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
<?php get_footer(); ?>
