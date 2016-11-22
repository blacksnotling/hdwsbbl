<?php get_header(); ?>
<h2>Welcome to the Team News Network</h2>
<div class="details team">
	<p>Welcome to the latest evolution from the HDWSBBL, the Team News Network! The blogs and sites attached to this blog are maintained by the teams themselves to let us give to you the latest team news, in their own words</p>
	<p>Below are the latest entries from the network. By Clicking on a entry title, it will take you through to the teams site.</p>
</div>
<p>&nbsp;</p>
<?php query_posts('cat=-3'); ?>
	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>

			<div class="entry">
				<h2 id="post-<?php the_ID(); ?>">
<?php 			if ($bbt_blog_id = get_post_meta($post->ID, 'blogid', true)) {
					print(get_blog_option($bbt_blog_id, 'blogname').": ");
				}
?>
				<a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
				<p class="postdate">Posted <?php the_time('F jS, Y') ?> by <?php the_author(); ?></p>
				<?php the_excerpt('Read the rest of this entry &raquo;'); ?>
				<p class="readmorelink">Continue Reading: <a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?> &raquo;</a></p>

				<p class="postmeta"><?php maintheme_posted_in() ?> <?php edit_post_link('Edit', ' <strong>[</strong> ', ' <strong>]</strong> '); ?></p>

			</div>


		<?php endwhile; ?>
		<?php endif; ?>


<?php get_sidebar(); ?>
<?php get_footer(); ?>