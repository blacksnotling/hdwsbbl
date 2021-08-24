<?php
/*
Template Name: List Resuts
*/
/*
*	Filename: bb.core.matches.php
*	Description: Page template to list the matches.
*/
?>
<?php get_header(); ?>
<div id="primary" class="content-area content-area-right-sidebar">
  <main id="main" class="site-main" role="main">
  <?php do_action( 'bblm_template_before_posts' ); ?>
	<?php if (have_posts()) : ?>
		<?php do_action( 'bblm_template_before_loop' ); ?>
			<?php do_action( 'bblm_template_before_content' ); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

				<header class="page-header entry-header">

					<h2 class="entry-title"><?php echo __( 'Match Results', 'bblm'); ?></h2>
          <div class="archive-description"><?php echo bblm_echo_archive_desc( 'match' ) ?></div>

				</header><!-- .page-header -->

					<div class="entry-content">


<?php
    if ( !empty( $_POST['bblm_flayout'] ) ) {
      $bblm_flayout = $_POST['bblm_flayout'];
    }
    else {
      $bblm_flayout = "";
    }
?>
				<form name="bblm_filterlayout" method="post" id="post" action="" class="selectbox">
				<p><?php echo __( 'Order Resuts by','bblm' ); ?>
					<select name="bblm_flayout" id="bblm_flayout">
						<option value="bycomp" <?php selected( $bblm_flayout, 'bycomp' ); ?>><?php echo __( 'Competition','bblm' ); ?></option>
						<option value="bydate" <?php selected( $bblm_flayout, 'bydate' ); ?>><?php echo __( 'Date','bblm' ); ?></option>
					</select>
				<input name="bblm_filter_submit" type="submit" id="bblm_filter_submit" value="Filter" /></p>
				</form>

<?php
				$matchsql = 'SELECT M.m_id, UNIX_TIMESTAMP(M.m_date) AS mdate, M.m_gate, M.m_teamAtd, M.m_teamBtd, M.m_teamAcas, M.m_teamBcas, M.WPID AS MWPID, C.sea_id, M.c_id, D.div_name, C.WPID AS CWPID FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'division D WHERE M.div_id = D.div_id AND M.c_id = C.WPID ORDER BY ';
				$layout = "";
				//determine the required Layout
				if ( isset( $_POST['bblm_flayout'] ) ) {
					$flay = $_POST['bblm_flayout'];
					switch ( $flay ) {
						case ( "bycomp" == $flay ):
					    	$layout .= 1;
					    	$matchsql .= 'C.sea_id DESC, M.c_id DESC, D.div_id ASC, M.m_date DESC';
						    break;
						case ( "bydate" == $flay ):
					    	$layout .= 0;
					    	$matchsql .= 'M.m_date DESC, M.c_id DESC, D.div_id ASC';
						    break;
						default:
					    	$layout .= 1;
					    	$matchsql .= 'C.sea_id DESC, M.c_id DESC, D.div_id ASC, M.m_date DESC';
						    break;
					}
				}
				else {
					//form not submitted so load in default values
					$layout .= 1;
					$matchsql .= 'C.sea_id DESC, M.c_id DESC, D.div_id ASC, M.m_date DESC';
				}


				//Run the Query. If successfull
				if ( $match = $wpdb->get_results( $matchsql ) ) {

					if ( 1 == $layout ) {
						//Load the default by Competition

						$is_first_comp = 0;
						$is_first_div = 0;
						$is_first_sea = 1;
						$current_comp = "";
						$current_div ="";
						$current_sea = "";
						$zebracount = 1;

						foreach ( $match as $m ) {
							if ( $m->sea_id !== $current_sea ) {
								$current_sea = $m->sea_id;
								$current_comp = $m->c_id;
								$current_div = $m->div_name;
								if ( 1 !== $is_first_sea ) {
									echo '</tbody></table>';
									$zebracount = 1;
								}
								$is_first_sea = 1;
							}
							if ( $m->c_id !== $current_comp ) {
								$current_comp = $m->c_id;
								if ( ( 1 !== $is_first_comp ) && ( 1 !== $is_first_sea ) ) {
									echo '</tbody></table>';
									$zebracount = 1;
								}
								$is_first_comp = 1;
							}
							if ( $m->div_name !== $current_div ) {
								$current_div = $m->div_name;
								if ( 1 !== $is_first_div ) {
									echo '</tbody></table>';
									$zebracount = 1;
								}
								$is_first_div = 1;
							}
							if ( $is_first_sea ) {
?>
								<h3><?php echo bblm_get_season_name( $m->sea_id ); ?></h3>
                <h4><?php echo bblm_get_competition_link( $m->CWPID ); ?></h4>
                <h5><?php echo $m->div_name; ?></h5>
                <table class="bblm_table">
                  <thead>
                    <tr>
                      <th class="bblm_tbl_matchdate"><?php echo __( 'Date','bblm' ); ?></th>
                      <th class="bblm_tbl_matchname"><?php echo __( 'Match','bblm' ); ?></th>
                      <th class="bblm_tbl_matchresult"><?php echo __( 'Result','bblm' ); ?></th>
                      <th class="bblm_tbl_matchdgate"><?php echo __( 'Gate','bblm' ); ?></th>
                    </tr>
                  </thead>
                  <tbody>
<?php
								$is_first_sea = 0;
								$is_first_comp = 0;
								$is_first_div = 0;
							}
							if ( $is_first_comp ) {
?>
                <h4><?php echo bblm_get_competition_link( $m->CWPID ); ?></h4>
                <h5><?php echo $m->div_name; ?></h5>
                <table class="bblm_table">
                  <thead>
                    <tr>
                      <th class="bblm_tbl_matchdate"><?php echo __( 'Date','bblm' ); ?></th>
                      <th class="bblm_tbl_matchname"><?php echo __( 'Match','bblm' ); ?></th>
                      <th class="bblm_tbl_matchresult"><?php echo __( 'Result','bblm' ); ?></th>
                      <th class="bblm_tbl_matchdgate"><?php echo __( 'Gate','bblm' ); ?></th>
                    </tr>
                  </thead>
                  <tbody>
<?php
								$is_first_comp = 0;
								$is_first_div = 0;
							}
							if ( $is_first_div ) {
?>
                <h5><?php echo $m->div_name; ?></h5>
                <table class="bblm_table">
                  <thead>
                    <tr>
                      <th class="bblm_tbl_matchdate"><?php echo __( 'Date','bblm' ); ?></th>
                      <th class="bblm_tbl_matchname"><?php echo __( 'Match','bblm' ); ?></th>
                      <th class="bblm_tbl_matchresult"><?php echo __( 'Result','bblm' ); ?></th>
                      <th class="bblm_tbl_matchdgate"><?php echo __( 'Gate','bblm' ); ?></th>
                    </tr>
                  </thead>
                  <tbody>
<?php
								$is_first_div = 0;
							}
							if ( $zebracount % 2 ) {
                echo '<tr>';
							}
							else {
                echo '<tr class="bblm_tbl_alt">';
							}
?>
                      <td><?php echo date("d.m.y", $m->mdate); ?></td>
                      <td><?php echo bblm_get_match_link( $m->MWPID ); ?></td>
                      <td><?php echo $m->m_teamAtd." - ".$m->m_teamBtd." (".$m->m_teamAcas." - ".$m->m_teamBcas.")"; ?></td>
                      <td><em><?php echo number_format( $m->m_gate ); ?></em></td>
                    </tr>
<?php
							$zebracount++;
						}
						echo '</tbody></table>';
					}//end of if layout 1
					else {
						//The Second Layout has been selected
						$zebracount = 1;
?>
				<table class="bblm_table bblm_sortable">
					<thead>
  					<tr>
  						<th class="bblm_tbl_matchdate"><?php echo __( 'Date','bblm' ); ?></th>
  						<th class="bblm_tbl_matchname"><?php echo __( 'Match','bblm' ); ?></th>
  						<th><?php echo __( 'Result','bblm' ); ?></th>
  						<th><?php echo __( 'Atten','bblm' ); ?></th>
  						<th class="bblm_tbl_name"><?php echo __( 'Comp','bblm' ); ?></th>
  						<th><?php echo __( 'Round','bblm' ); ?></th>
  						<th><?php echo __( 'Season','bblm' ); ?></th>
  					</tr>
					</thead>
					<tbody>
<?php
						foreach ( $match as $m ) {
							if ( $zebracount % 2 ) {
								echo '<tr id="' . $m->m_id . '">';
							}
							else {
                echo '<tr class="bblm_tbl_alt" id="' . $m->m_id . '">';
							}
?>
  						<td><?php echo date( "d.m.y", $m->mdate ); ?></td>
  						<td><?php echo bblm_get_match_link( $m->MWPID ); ?></td>
  						<td><?php echo $m->m_teamAtd . ' - ' . $m->m_teamBtd . ' (' . $m->m_teamAcas . ' - ' . $m->m_teamBcas . ')'; ?></td>
  						<td><?php echo number_format( $m->m_gate ); ?></td>
  						<td><?php echo bblm_get_competition_link( $m->CWPID ) ?></td>
  						<td><?php echo $m->div_name; ?></td>
  						<td><?php echo bblm_get_season_name( $m->sea_id ); ?></td>
            </tr>
<?php
							$zebracount++;
						}//end of for each
						echo '</tbody></table>';
					}//end of Layout 2
				}//end of if SQL worked
				else {
          echo '<p>' . __('Sorry, but no Matches could be retrieved at this time, please try again later.','bblm' ) . '</p>';
				}

?>

</div><!-- .entry-content -->

<footer class="entry-footer">
	<p class="postmeta"><?php bblm_display_page_edit_link(); ?></p>
</footer><!-- .entry-footer -->

</article><!-- .post-ID -->

<?php do_action( 'bblm_template_after_content' ); ?>
<?php do_action( 'bblm_template_after_loop' ); ?>
<?php endif; ?>
<?php do_action( 'bblm_template_after_posts' ); ?>
</main><!-- #main -->
</div><!-- #primary -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>
