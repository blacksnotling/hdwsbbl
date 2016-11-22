<?php get_header(); ?>
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
				<?php the_content('Read the rest of this entry &raquo;'); ?>
				<p class="readmorelink">Continue Reading: <a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?> &raquo;</a></p>

				<p class="postmeta">Filed as <?php the_category(',') ?> <?php edit_post_link('Edit', ' <strong>[</strong> ', ' <strong>]</strong> '); ?></p>

			</div>

		<?php endwhile; ?>
		<?php endif; ?>

<?php
		//Did You Know Display Code
		if (function_exists(bblm_display_dyk)) {
			bblm_display_dyk();
		}
?>
		<ul class="subnav">
			<?php next_posts_link('<li>&laquo; Previous Entries</li>') ?>
			<?php previous_posts_link('<li>Next Entries &raquo;</li>') ?>
		</ul>


<?php get_sidebar(); ?>
<?php get_footer(); ?>