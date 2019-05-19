<?php
/**
 * The template for displaying Transfers - Archive View
 *
 * @package		BBowlLeagueMan/Templates
 * @category	Template
 * @author 		Blacksnotliung
 */

 get_header(); ?>
   <?php do_action( 'bblm_template_before_posts' ); ?>
 	<?php if (have_posts()) : ?>
     <?php do_action( 'bblm_template_before_loop' ); ?>

     <header class="page-header entry-header">

       <h2 class="entry-title"><?php echo __( 'Transfers', 'bblm'); ?></h2>
       <div class="archive-description"><?php echo bblm_echo_archive_desc( 'transfer' ) ?></div>

     </header><!-- .page-header -->

     <?php $own = new BBLM_CPT_Owner; ?>
     <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
       <?php $lastseason = "";
             $is_first = true; ?>


 		<?php while (have_posts()) : the_post(); ?>
       <?php do_action( 'bblm_template_before_content' ); ?>
<?php
        //If the season this transfer occured does not match the previous one, oitput the season name
        if ( $post->bblm_transfer_season !== $lastseason ) {

          if ( false == $is_first ) {

            //This will happen for all APART FROM the first
            echo '</ul>';

          }
          $is_notfirst = false;

          echo '<h3><a title="Read more about this Season" href="' . get_post_permalink( $post->bblm_transfer_season ) . '">' . esc_html( get_the_title( $post->bblm_transfer_season ) ) . '</a></h3>';
          echo '<ul>';
          $lastseason = $post->bblm_transfer_season;
        }

        //Output the transfer itself
        echo '<li>';
        echo '<a title="' . __( ' Read more about this team ', 'bblm') . '" href="' . get_post_permalink( $post->bblm_transfer_hteam ) . '">' . esc_html( get_the_title( $post->bblm_transfer_hteam ) ) . '</a>';
        echo __( ' hires ', 'bblm');
        echo '<a title="' . __( ' Read more about this player ', 'bblm') . '" href="' . get_post_permalink( $post->bblm_transfer_player ) . '">' . esc_html( get_the_title( $post->bblm_transfer_player ) ) . '</a>';
        echo __( ' from ', 'bblm');
        echo '<a title="' . __( ' Read more about this team ', 'bblm') . '" href="' . get_post_permalink( $post->bblm_transfer_steam ) . '">' . esc_html( get_the_title( $post->bblm_transfer_steam ) ) . '</a>';
        echo __( ' for ', 'bblm');
        echo number_format( $post->bblm_transfer_cost );
        echo 'GP</li>';
?>

     <?php do_action( 'bblm_template_after_content' ); ?>
     <?php endwhile; ?>
          </ul>

       <footer class="entry-footer">
         <p class="postmeta">&nbsp;</p>
       </footer><!-- .entry-footer -->

     </article><!-- .post-ID -->

   <?php do_action( 'bblm_template_after_loop' ); ?>
 	<?php endif; ?>

 <?php do_action( 'bblm_template_after_posts' ); ?>
 <?php get_sidebar(); ?>
 <?php get_footer(); ?>
