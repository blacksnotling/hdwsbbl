<?php get_header(); ?>
	<?php if (have_posts()) : ?>
		<?php $term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) ); ?>

		<h2><?php printf( __( 'Entries discussing %s', 'oberwald' ), '&quot;' . $term->name . '&quot;' ); ?></h2>

		<?php while (have_posts()) : the_post(); ?>
			<div class="entry">
				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<h2 class="entry-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
					<p class="postdate"><?php oberwald_posted_on() ?></p>

					<?php the_excerpt(); ?>

					<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'bblm' ), 'after' => '</div>' ) ); ?>

					<p class="postmeta"><?php oberwald_posted_in() ?> <?php edit_post_link( __( 'Edit', 'oberwald' ), ' <strong>[</strong> ', ' <strong>]</strong> '); ?> <?php oberwald_comments_link(); ?></p>
				</div>
			</div>


		<?php endwhile; else: ?>
					<p><?php _e('Sorry, no posts have been posted under this topic.', 'oberwald'); ?></p>
		<?php endif; ?>

<?php if (  $wp_query->max_num_pages > 1 ) : ?>
				<div id="nav-below" class="subnav">
					<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&laquo;</span> Older Entries', 'bblm' ) ); ?></div>
					<div class="nav-next"><?php previous_posts_link( __( 'Newer Entries <span class="meta-nav">&raquo;</span>', 'bblm' ) ); ?></div>
				</div><!-- #nav-below -->
<?php endif; ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
