<?php get_header(); ?>
  <?php do_action( 'bblm_template_before_posts' ); ?>
	<?php if (have_posts()) : ?>
    <?php do_action( 'bblm_template_before_loop' ); ?>

    <header class="page-header entry-header">

      <h2 class="entry-title"><?php echo __( 'Team Owners', 'bblm'); ?></h2>
      <div class="archive-description"><?php echo bblm_echo_archive_desc( 'owner' ) ?></div>

    </header><!-- .page-header -->

    <?php $own = new BBLM_CPT_Owner; ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
      <table class="sortable bblm_tbl bblm_sortable">
        <tr>
          <th class="tbl_title"><?php echo __( 'Owner', 'bblm'); ?></th>
          <th><?php echo __( 'Teams', 'bblm'); ?></th>
          <th><?php echo __( 'Championships', 'bblm'); ?></th>
          <th class="tbl_stat">P</th>
          <th class="tbl_stat">W</th>
          <th class="tbl_stat">L</th>
          <th class="tbl_stat">D</th>
          <th class="tbl_stat">Tf</th>
          <th class="tbl_stat">Ta</th>
          <th class="tbl_stat">Cf</th>
          <th class="tbl_stat">Ca</th>
          <th class="tbl_stat">Comp</th>
          <th class="tbl_stat">Int</th>
          <th class="tbl_stat">%</th>
        </tr>

		<?php while (have_posts()) : the_post(); ?>
      <?php do_action( 'bblm_template_before_content' ); ?>

          <tr>
            <td><a href="<?php the_permalink(); ?>" title="<?php echo __( 'Read more about', 'bblm'); ?> <?php the_title(); ?>"><?php the_title(); ?></a></td>
            <td><?php echo $own->get_number_teams() ?></td>
            <td><?php echo $own->get_number_championships(); ?></td>
            <?php $own->individual_stat_tbl_part() ?>
          </tr>





    <?php do_action( 'bblm_template_after_content' ); ?>
    <?php endwhile; ?>

        </table>

      <footer class="entry-footer">
        <p class="postmeta">&nbsp;</p>
      </footer><!-- .entry-footer -->

    </article><!-- .post-ID -->

  <?php do_action( 'bblm_template_after_loop' ); ?>
	<?php endif; ?>

<?php do_action( 'bblm_template_after_posts' ); ?>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
