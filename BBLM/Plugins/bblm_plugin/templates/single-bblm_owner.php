<?php get_header(); ?>
  <?php do_action( 'bblm_template_before_posts' ); ?>
	<?php if (have_posts()) : ?>
    <?php do_action( 'bblm_template_before_loop' ); ?>
		<?php while (have_posts()) : the_post(); ?>
      <?php do_action( 'bblm_template_before_content' ); ?>
      <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

        <?php $own = new BBLM_CPT_Owner; ?>

        <header class="entry-header">
          <h2 class="entry-title"><?php the_title(); ?></h2>
        </header><!-- .entry-header -->

        <div class="entry-content">
          <?php /* the_content(); */ ?>
          <?php $has_team = $own->get_number_teams();
                $has_played = $own->get_number_games();
          if ( ( $has_team > 0 ) && ( $has_played > 0 ) ) { ?>

            <h3><?php echo __( 'Career Performance', 'bblm'); ?></h3>
            <table class="bblm_table">
              <thead>
              <tr>
                <th><?php echo __( 'Teams', 'bblm'); ?></th>
                <th class="bblm_tbl_stat">P</th>
                <th class="bblm_tbl_stat">W</th>
                <th class="bblm_tbl_stat">L</th>
                <th class="bblm_tbl_stat">D</th>
                <th class="bblm_tbl_stat">Tf</th>
                <th class="bblm_tbl_stat">Ta</th>
                <th class="bblm_tbl_stat">Cf</th>
                <th class="bblm_tbl_stat">Ca</th>
                <th class="bblm_tbl_stat">Comp</th>
                <th class="bblm_tbl_stat">Int</th>
                <th class="bblm_tbl_stat">%</th>
              </tr>
              </thead>
              <tbody>
              <tr>
                <td><?php echo $has_team; ?></td>
                <?php $own->individual_stat_tbl_part() ?>
              </tr>
              </tbody>
            </table>

            <?php $own->individual_stat_desc(); ?>

            <h3><?php echo __( 'Their Teams', 'bblm'); ?></h3>
            <table class="bblm_table bblm_sortable">
              <thead>
              <tr>
                <th><?php echo __( 'Team', 'bblm'); ?></th>
                <th class="bblm_tbl_stat">P</th>
                <th class="bblm_tbl_stat">W</th>
                <th class="bblm_tbl_stat">L</th>
                <th class="bblm_tbl_stat">D</th>
                <th class="bblm_tbl_stat">Tf</th>
                <th class="bblm_tbl_stat">Ta</th>
                <th class="bblm_tbl_stat">Cf</th>
                <th class="bblm_tbl_stat">Ca</th>
                <th class="bblm_tbl_stat">Comp</th>
                <th class="bblm_tbl_stat">Int</th>
                <th class="bblm_tbl_stat">%</th>
              </tr>
              </thead>
              <tbody>
                <?php $own->team_stat_tbl_row() ?>
              </tbody>
            </table>

            <h3><?php echo __( 'Races Used', 'bblm'); ?></h3>
            <table class="bblm_table bblm_sortable">
              <thead>
              <tr>
                <th><?php echo __( 'Race', 'bblm'); ?></th>
                <th class="bblm_tbl_stat">P</th>
                <th class="bblm_tbl_stat">W</th>
                <th class="bblm_tbl_stat">L</th>
                <th class="bblm_tbl_stat">D</th>
                <th class="bblm_tbl_stat">Tf</th>
                <th class="bblm_tbl_stat">Ta</th>
                <th class="bblm_tbl_stat">Cf</th>
                <th class="bblm_tbl_stat">Ca</th>
                <th class="bblm_tbl_stat">Comp</th>
                <th class="bblm_tbl_stat">Int</th>
                <th class="bblm_tbl_stat">%</th>
              </tr>
              </thead>
              <tbody>
                <?php $own->race_stat_tbl_row() ?>
              </tbody>
            </table>

            <h3><?php echo __( 'Season Performance', 'bblm'); ?>S</h3>
            <table class="bblm_table bblm_sortable">
              <thead>
              <tr>
                <th><?php echo __( 'Season', 'bblm'); ?></th>
                <th class="bblm_tbl_stat">P</th>
                <th class="bblm_tbl_stat">W</th>
                <th class="bblm_tbl_stat">L</th>
                <th class="bblm_tbl_stat">D</th>
                <th class="bblm_tbl_stat">Tf</th>
                <th class="bblm_tbl_stat">Ta</th>
                <th class="bblm_tbl_stat">Cf</th>
                <th class="bblm_tbl_stat">Ca</th>
                <th class="bblm_tbl_stat">Comp</th>
                <th class="bblm_tbl_stat">Int</th>
                <th class="bblm_tbl_stat">%</th>
              </tr>
              </thead>
              <tbody>
                <?php $own->season_stat_tbl_row() ?>
              </tbody>
            </table>

            <h3><?php echo __( 'Competition Performance', 'bblm'); ?></h3>
            <table class="bblm_table bblm_sortable">
              <thead>
              <tr>
                <th><?php echo __( 'Competition', 'bblm'); ?></th>
                <th class="bblm_tbl_stat">P</th>
                <th class="bblm_tbl_stat">W</th>
                <th class="bblm_tbl_stat">L</th>
                <th class="bblm_tbl_stat">D</th>
                <th class="bblm_tbl_stat">Tf</th>
                <th class="bblm_tbl_stat">Ta</th>
                <th class="bblm_tbl_stat">Cf</th>
                <th class="bblm_tbl_stat">Ca</th>
                <th class="bblm_tbl_stat">Comp</th>
                <th class="bblm_tbl_stat">Int</th>
                <th class="bblm_tbl_stat">%</th>
              </tr>
              </thead>
              <tbody>
                <?php $own->comp_stat_tbl_row() ?>
              </tbody>
            </table>

            <h3><?php echo __( 'Championship Cup Performance', 'bblm'); ?></h3>
            <table class="bblm_table bblm_sortable">
              <thead>
              <tr>
                <th><?php echo __( 'Competition', 'bblm'); ?></th>
                <th class="bblm_tbl_stat">P</th>
                <th class="bblm_tbl_stat">W</th>
                <th class="bblm_tbl_stat">L</th>
                <th class="bblm_tbl_stat">D</th>
                <th class="bblm_tbl_stat">Tf</th>
                <th class="bblm_tbl_stat">Ta</th>
                <th class="bblm_tbl_stat">Cf</th>
                <th class="bblm_tbl_stat">Ca</th>
                <th class="bblm_tbl_stat">Comp</th>
                <th class="bblm_tbl_stat">Int</th>
                <th class="bblm_tbl_stat">%</th>
              </tr>
              </thead>
              <tbody>
                <?php $own->cup_stat_tbl_row() ?>
              </tbody>
            </table>

            <h3><?php echo __( 'Top Players They Coached', 'bblm'); ?></h3>
            <table class="bblm_table bblm_expandable">
              <thead>
                <tr>
                  <th>#</th>
                  <th><?php echo __( 'Player', 'bblm'); ?></th>
                  <th><?php echo __( 'Position', 'bblm'); ?></th>
                  <th><?php echo __( 'Team', 'bblm'); ?></th>
                  <th class="bblm_tbl_stat"><?php echo __( 'SSP', 'bblm'); ?></th>
                </tr>
              </thead>
              <tbody>
            <?php $own->player_stat_tbl_row() ?>
              </tbody>
            </table>

            <h3><?php echo __( 'Star Players Hired', 'bblm'); ?></h3>
            <?php $own->star_stat_tbl_row() ?>

<?php     } /* End of if $has_team */
          else {
            //Owner has either not played any games, OR has not made their debut yet
            echo __( 'This Owner has not made their League debut yet!', 'bblm');
          }

?>

        </div><!-- .entry-content -->

        <footer class="entry-footer">
          <p class="postmeta">&nbsp;</p>
        </footer><!-- .entry-footer -->

      </article><!-- .post-ID -->

    <?php do_action( 'bblm_template_after_content' ); ?>
    <?php endwhile; ?>
  <?php do_action( 'bblm_template_after_loop' ); ?>
	<?php endif; ?>

<?php do_action( 'bblm_template_after_posts' ); ?>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
