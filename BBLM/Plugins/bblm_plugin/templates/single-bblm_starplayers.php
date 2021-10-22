<?php
/**
 * BBowlLeagueMan Teamplate View Star Player
 *
 * Page Template to view Star Players details
 *
 * @author 		Blacksnotling
 * @category 	Template
 * @package 	BBowlLeagueMan/Templates
 */
/*
 * Template Name: View Star Player
 */
?>
<?php get_header(); ?>
<div id="primary" class="content-area content-area-right-sidebar">
  <main id="main" class="site-main" role="main">
  <?php do_action( 'bblm_template_before_posts' ); ?>
	<?php if (have_posts()) : ?>
    <?php do_action( 'bblm_template_before_loop' ); ?>
		<?php while (have_posts()) : the_post(); ?>
      <?php do_action( 'bblm_template_before_content' ); ?>
      <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<?php
			/*
			Gather Information for page
			*/
      $playersql = 'SELECT P.p_id, P.t_id, P.p_ma, P.p_st, P.p_ag, P.p_av, P.p_pa, P.p_spp, P.p_skills, P.p_cost, P.p_legacy FROM '.$wpdb->prefix.'player P WHERE P.WPID = '.$post->ID;
			$pd = $wpdb->get_row( $playersql );
      $legacy = 0;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

		<header class="entry-header">
			<h2 class="entry-title"><?php the_title(); ?></h2>
		</header><!-- .entry-header -->

		<div class="entry-content">

<?php
    if ( BBLM_CPT_Star::is_player_legacy( $post->ID ) ) {
      $legacy = 1;
      bblm_display_legacy_notice( "Star Player" );
    }
?>

    <h3 class="bblm-table-caption"><?php echo __( 'On the Pitch','bblm' ); ?></h3>
    <?php BBLM_CPT_Star::display_star_characteristics( $post->ID ); ?>


    <h3 class="bblm-table-caption"><?php echo __( 'Biography','bblm' ); ?></h3>
    <div class="bblm_details">
      <?php the_content(); ?>
    </div>


<?php
		//Career Stats
		if ( BBLM_CPT_Star::has_player_played( $post->ID ) ) {
			//The Star has played a match so continue
?>


      <h3 class="bblm-table-caption"><?php echo __( 'League Statistics','bblm' ); ?></h3>
      <?php BBLM_CPT_Star::display_player_career( $post->ID ); ?>

      <h3 class="bblm-table-caption"><?php echo __( 'Team Breakdown','bblm' ); ?></h3>
      <?php BBLM_CPT_Star::display_player_team_history( $post->ID ); ?>

      <?php BBLM_CPT_Star::display_player_kills( $post->ID ); ?>

      <h3 class="bblm-table-caption"><?php echo __( 'Performance by Season', 'bblm' ); ?></h3>
      <?php BBLM_CPT_Star::display_player_performance( $post->ID, 'Season' ) ?>

      <h3 class="bblm-table-caption"><?php echo __( 'Performance by Championship Cup', 'bblm' ); ?></h3>
      <?php BBLM_CPT_Star::display_player_performance( $post->ID, 'Cup' ) ?>

      <h3 class="bblm-table-caption"><?php echo __( 'Performance by Competition', 'bblm' ); ?></h3>
      <?php BBLM_CPT_Star::display_player_performance( $post->ID, 'Comp' ) ?>

      <h3 class="bblm-table-caption"><?php echo __( 'Recent Matches','bblm' ); ?></h3>
      <?php BBLM_CPT_Star::display_plyaer_matchhistory( $post->ID ); ?>

<?php

		}//End of if a player has played a match
		else {
			//Star has not made their debut yet
      echo '<div class="bblm_info">';
      echo '<p>' . __( 'This Star Player has not made their Debut yet. Stay tuned for further developments.', 'bblm' ) . '</p>';
      echo '</div>';
		}



?>
<footer class="entry-footer">
	<p class="postmeta"><?php bblm_display_page_edit_link(); ?></p>
</footer><!-- .entry-footer -->

</div><!-- .entry-content -->
</article>

<?php do_action( 'bblm_template_after_content' ); ?>
<?php endwhile; ?>
<?php do_action( 'bblm_template_after_loop' ); ?>
<?php endif; ?>
<?php do_action( 'bblm_template_after_posts' ); ?>
</main><!-- #main -->
</div><!-- #primary -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>
