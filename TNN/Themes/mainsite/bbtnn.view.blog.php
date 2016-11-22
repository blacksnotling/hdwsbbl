<?php
/*
Template Name: Main Site Blog Page
*/
/*
*	Filename: bbtn.view.blog.php
*	Version: 1.0
*	Description: .The Template for the news item of the "main-site"
*/
/* -- Change History --
20100815 - 1.0b - Initial creation and completion of file.(beta)
20100816 - 1.1b - the number of posts is grabbed from the options table rather then being a static number
20100826 - 0.1 - rename of file and removal of beta tag. development is underway
20100904 - 0.2 - Updated the Breadcrumb path
20100910 - 0.3 - Updated the category exclusion part of query_posts to get it working in Live
20100913 - 1.0 - bump to V1.0
*/
	require(TEMPLATEPATH . "/header.php"); ?>
	<?php if (have_posts()) : ?>

		<div id="breadcrumb">
			<p><a href="<?php echo get_option('home'); ?>" title="Back to the front of the HDWSBBL Team News Network">Team News Network</a> &raquo; <?php the_title(); ?></p>
		</div>
		<ul class="subnav">
			<li><a href="<?php echo get_option('home'); ?>/category/main-site/page/2/" title="View Previous News entries">&laquo; Previous Entries</a></li>
		</ul>


<?php
		$posts_per_page = get_option('posts_per_page ');
	    $recentPosts = new WP_Query();
	    $recentPosts->query('category_name=Main Site&showposts='.$posts_per_page);
?>
		<?php while ($recentPosts->have_posts()) : $recentPosts->the_post(); ?>
			<div class="entry">
				<h2 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
				<p class="postdate"><?php the_time('F jS, Y') ?> <!-- by <?php the_author(); ?> --></p>

				<?php the_content('Read the rest of this entry &raquo;'); ?>

				<p class="postmeta"><?php maintheme_posted_in() ?> <strong>|</strong> <?php edit_post_link('Edit', ' <strong>[</strong> ', ' <strong>]</strong> '); ?>  <?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?></p>

			</div>


		<?php endwhile; else: ?>
			<p><?php _e('Sorry, no posts have been filed under this topic.'); ?></p>
		<?php endif; ?>



		<ul class="subnav">
			<?php next_posts_link('<li>&laquo; Previous Entries</li>') ?>
			<?php previous_posts_link('<li>Next Entries &raquo;</li>') ?>
		</ul>

</div><!-- end of #maincontent -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>