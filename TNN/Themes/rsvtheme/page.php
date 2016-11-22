<?php get_header(); ?>
	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>

		<div id="breadcrumb">
			<p><a href="<?php echo get_option('home'); ?>" title="Back to the front of RSV Online">RSV Online</a> &raquo; <?php the_title(); ?></p>
		</div>

		<ul class="subnav">
			<?php next_posts_link('<li>&laquo; Previous Entries</li>') ?>
			<?php previous_posts_link('<li>Next Entries &raquo;</li>') ?>
		</ul>
		<div class="entry">
				<h2 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>

				<?php the_content('Read the rest of this entry &raquo;'); ?>

				<p class="postmeta"><?php edit_post_link('Edit', ' <strong>[</strong> ', ' <strong>]</strong> '); ?></p>
			</div>

		<?php endwhile; ?>
		<?php endif; ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>