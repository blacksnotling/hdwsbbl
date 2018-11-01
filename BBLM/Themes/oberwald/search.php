<?php get_header(); ?>
	<?php if (have_posts()) : ?>
	<h2 class="pagetitle"><h2 class="entry-title">Search Results for '<?php the_search_query() ?>'</h2></h2>
	<?php while (have_posts()) : the_post(); ?>
			<div class="entry">
				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<h2 class="entry-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
					<p class="postdate"><?php oberwald_posted_on() ?></p>

					<?php the_excerpt(); ?>

					<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'oberwald' ), 'after' => '</div>' ) ); ?>

					<p class="postmeta"><?php oberwald_posted_in() ?> <?php edit_post_link( __( 'Edit', 'oberwald' ), ' <strong>[</strong> ', ' <strong>]</strong> '); ?> <?php oberwald_comments_link(); ?></p>
				</div>
			</div>


	<?php endwhile; else: ?>			<h2 class="entry-title">No search results for '<?php the_search_query() ?>'</h2>
			<p>Sorry, but nothing was found that matched that search. For now the best thing to do is check your spelling or try a different word and try again:</p>
			<p><?php get_search_form(); ?></p>
		<?php endif; ?>

<?php if (  $wp_query->max_num_pages > 1 ) : ?>
				<div id="nav-below" class="subnav">
					<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&laquo;</span> Older Entries', 'oberwald' ) ); ?></div>
					<div class="nav-next"><?php previous_posts_link( __( 'Newer Entries <span class="meta-nav">&raquo;</span>', 'oberwald' ) ); ?></div>
				</div><!-- #nav-below -->
<?php endif; ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
