<?php get_header(); ?>
	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
			<div class="entry">
				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<h2 class="entry-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
					<p class="postdate"><?php oberwald_posted_on() ?></p>

					<?php the_content(); ?>

					<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'oberwald' ), 'after' => '</div>' ) ); ?>

					<p class="postmeta"><?php oberwald_posted_in() ?> <?php edit_post_link( __( 'Edit', 'oberwald' ), ' <strong>[</strong> ', ' <strong>]</strong> '); ?></p>
				</div>
			</div>
			<?php comments_template( '', true ); ?>


		<?php endwhile; ?>
		<?php endif; ?>


<?php get_sidebar(); ?>
<?php get_footer(); ?>
