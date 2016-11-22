<?php get_header(); ?>
	<?php if (have_posts()) : ?>

<?php
		$post = $posts[0]; // Hack. Set $post so that the_date() works
		 /* If this is a daily archive */
		 if (is_day()) { ?>
		<h2 class="pagetitle">Archive for <?php the_time('F jS, Y'); ?></h2>
 	  <?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
		<h2 class="pagetitle">Archive for <?php the_time('F, Y'); ?></h2>
 	  <?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
		<h2 class="pagetitle">Archive for <?php the_time('Y'); ?></h2>
	  <?php }
 ?>

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

				<p class="postmeta">Filed as <?php the_category(',') ?> <?php edit_post_link('Edit', ' <strong>[</strong> ', ' <strong>]</strong> '); ?></p>

			</div>

		<?php endwhile; else: ?>
			<p><?php _e('Sorry, There where no items posted during this time period.'); ?></p>
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


<?php get_sidebar(); ?>
<?php get_footer(); ?>