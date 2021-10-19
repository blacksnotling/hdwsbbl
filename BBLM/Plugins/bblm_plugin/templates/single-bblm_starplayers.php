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
  if ( $pd->p_legacy ) {
    $legacy = 1;
    bblm_display_legacy_notice( "Star Player" );
  }
?>

      <h3 class="bblm-table-caption"><?php echo __( 'On the Pitch','bblm' ); ?></h3>
      <div role="region" aria-labelledby="Caption01" tabindex="0">
      <table class="bblm_table bblm_table_collapsable">
        <thead>
				<tr>
					<th class="bblm_tbl_name"><?php echo __( 'Position','bblm' ); ?></th>
					<th class="bblm_tbl_stat"><?php echo __( 'MA','bblm' ); ?></th>
					<th class="bblm_tbl_stat"><?php echo __( 'ST','bblm' ); ?></th>
					<th class="bblm_tbl_stat"><?php echo __( 'AG','bblm' ); ?></th>
<?php
          if ( !$legacy ) {
?>
          <th class="bblm_tbl_stat"><?php echo __( 'PA', 'bblm' ); ?></th>
<?php
          }
?>
					<th class="bblm_tbl_stat"><?php echo __( 'AV','bblm' ); ?></th>
					<th class="bblm_tbl_collapse"><?php echo __( 'Skills','bblm' ); ?></th>
					<th><?php echo __( 'Cost Per Match','bblm' ); ?></th>
				</tr>
      </thead>
      <tbody>
				<tr class="bblm_tbl_alt">
					<td><?php echo __( 'Star Player','bblm' ); ?></td>
					<td><?php echo $pd->p_ma; ?></td>
					<td><?php echo $pd->p_st; ?></td>
<?php
          if ( $legacy ) {
?>
          <td><?php echo $pd->p_ag; ?></td>
          <td><?php echo $pd->p_av; ?></td>
<?php
          }
          else {
?>
          <td><?php echo $pd->p_ag; ?>+</td>
          <td><?php echo $pd->p_pa; ?>+</td>
          <td><?php echo $pd->p_av; ?>+</td>
<?php
          }
?>
					<td class="bblm_tbl_skills bblm_tbl_collapse"><?php  echo $pd->p_skills; ?></td>
					<td><?php  echo number_format( $pd->p_cost ); ?> gp</td>
				</tr>

<?php
    if ( !$legacy ) {
      //Only show this is the star is active

      //Display the player special rules if set
      $star_rules = esc_textarea( get_post_meta( $post->ID, 'star_srules', true ) );

      echo '<tr>';
      echo '<td colspan="2"><strong>' . __( 'Special Rules','bblm' ) . '</strong></td>';
      echo '<td colspan="6"><em>' . $star_rules . '</em></td>';
      echo '</tr>';

      //Grab the list of Race Special Rules / Traits assigned to this race
      $term_obj_list = get_the_terms( $post->ID, 'race_rules' );

      echo '<tr class="bblm_tbl_alt">';

      if ( $term_obj_list && ! is_wp_error( $term_obj_list ) ) {
        //Loop through them and add them to an array
        $race_terms = array();
        foreach ( $term_obj_list as $term ) {
          $race_terms[] = $term->slug;
        }
        //Form the custom query, looking for races who have the same traits as the Star
        $args = array(
          'post_type' => 'bblm_race',
          'orderby'   => 'title',
          'order' => 'ASC',
          'tax_query' => array(
            array(
              'taxonomy' => 'race_rules',
              'field' => 'slug',
              'terms' => $race_terms
            )
          )
        );

        $starsqlarray = array();
        $racelist = "";

        // The Query
        $the_query = new WP_Query( $args );

        // The Loop
        if ( $the_query->have_posts() ) {
          while ( $the_query->have_posts() ) {
            $the_query->the_post();

            //Loop though each one and form the sql
            $starsqlarray[] = bblm_get_race_link( get_the_ID() );
          }
          $racelist .= join( ", ", $starsqlarray );
          echo '<td colspan="2"><strong>' . __( 'Plays for ', 'bblm') . '</strong></td>';
          echo '<td colspan="6">' . $racelist . '</td>';
        }
        else {
          // no posts found
          echo '<td colSpan="8">' . __( 'There are currently no Races assigned to this Star Players','bblm' ) . '</td>';
        }
        /* Restore original Post Data */
        wp_reset_postdata();

      }//end of if the race has any terms assigned
      else {
        //nothing is returned
        echo '<td colSpan="8">' . __( 'There are currently no Races assigned to this Star Players','bblm' ) . '</td>';
      }
      echo '</tr>';

    }//end of if not legacy
?>
      </tbody>
    </table>
  </div>

    <h3 class="bblm-table-caption"><?php echo __( 'Biography','bblm' ); ?></h3>
    <div class="bblm_details">
      <?php the_content(); ?>
    </div>

<?php
		//Career Stats
		$careerstatssql = 'SELECT COUNT(*) AS GAMES, SUM(M.mp_td) AS TD, SUM(M.mp_cas) AS CAS, SUM(M.mp_comp) AS COMP, SUM(M.mp_int) AS MINT, SUM(M.mp_mvp) AS MVP, SUM(M.mp_spp) AS SPP FROM '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'team T WHERE M.t_id = T.t_id AND M.mp_counts = 1 AND M.p_id = '.$pd->p_id.' GROUP BY M.p_id ORDER BY T.t_name ASC';
		if ( $s = $wpdb->get_row( $careerstatssql ) ) {
			//The Star has played a match so continue
?>
      <h3 class="bblm-table-caption"><?php echo __( 'League Statistics','bblm' ); ?></h3>
      <div role="region" aria-labelledby="Caption01" tabindex="0">
      <table class="bblm_table bblm_table_collapsable">
        <thead>
				<tr class="bblm_tbl_alt">
					<th class="bblm_tbl_title bblm_tbl_collapse"><?php echo __( 'Career Total','bblm' ); ?></th>
					<th class="bblm_tbl_stat"><?php echo __( 'Pld','bblm' ); ?></th>
					<th class="bblm_tbl_stat"><?php echo __( 'TD','bblm' ); ?></th>
					<th class="bblm_tbl_stat"><?php echo __( 'CAS','bblm' ); ?></th>
					<th class="bblm_tbl_stat"><?php echo __( 'COMP','bblm' ); ?></th>
					<th class="bblm_tbl_stat"><?php echo __( 'INT','bblm' ); ?></th>
					<th class="bblm_tbl_stat"><?php echo __( 'MVP','bblm' ); ?></th>
					<th class="bblm_tbl_stat"><?php echo __( 'SPP','bblm' ); ?></th>
				</tr>
      </thead>
      <tbody>
				<tr>
					<td class="bblm_tbl_collapse"><?php the_title(); ?></th>
					<td><?php echo $s->GAMES; ?></th>
					<td><?php echo $s->TD; ?></th>
					<td><?php echo $s->CAS; ?></th>
					<td><?php echo $s->COMP; ?></th>
					<td><?php echo $s->MINT; ?></th>
					<td><?php echo $s->MVP; ?></th>
					<td><?php echo $s->SPP; ?></th>
				</tr>
      </tbody>
			</table>
    </div>
<?php

			//Breakdown by team
			$statssql = 'SELECT COUNT(*) AS GAMES, SUM(M.mp_td) AS TD, SUM(M.mp_cas) AS CAS, SUM(M.mp_comp) AS COMP, SUM(M.mp_int) AS MINT, SUM(M.mp_mvp) AS MVP, SUM(M.mp_spp) AS SPP, T.WPID AS TWPID FROM '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'team T WHERE M.t_id = T.t_id AND M.mp_counts = 1 AND M.p_id = '.$pd->p_id.' GROUP BY T.t_id ORDER BY GAMES DESC, T.t_name ASC';
			if ( $stats = $wpdb->get_results( $statssql ) ) {
				$zebracount = 1;
?>
      <h3 class="bblm-table-caption"><?php echo __( 'Team Breakdown','bblm' ); ?></h3>
      <div role="region" aria-labelledby="Caption01" tabindex="0">
			<table class="bblm_table bblm_table_collapsable">
        <thead>
				<tr>
					<th class="bblm_tbl_title"><?php echo __( 'Playing for','bblm' ); ?></th>
          <th class="bblm_tbl_stat"><?php echo __( 'Pld','bblm' ); ?></th>
					<th class="bblm_tbl_stat"><?php echo __( 'TD','bblm' ); ?></th>
					<th class="bblm_tbl_stat"><?php echo __( 'CAS','bblm' ); ?></th>
					<th class="bblm_tbl_stat bblm_tbl_collapse"><?php echo __( 'COMP','bblm' ); ?></th>
					<th class="bblm_tbl_stat bblm_tbl_collapse"><?php echo __( 'INT','bblm' ); ?></th>
					<th class="bblm_tbl_stat bblm_tbl_collapse"><?php echo __( 'MVP','bblm' ); ?></th>
					<th class="bblm_tbl_stat"><?php echo __( 'SPP','bblm' ); ?></th>
				</tr>
      </thead>
      <tbody>

<?php
				foreach ( $stats as $s ) {
					if ( $zebracount % 2 ) {
            echo '<tr class="bblm_tbl_alt">';
					}
					else {
						echo '<tr>';
					}
          echo '<td>' . bblm_get_team_link( $s->TWPID ) . '</td>';
          echo '<td>' . $s->GAMES . '</td>';
          echo '<td>' . $s->TD . '</td>';
          echo '<td>' . $s->CAS . '</td>';
          echo '<td class="bblm_tbl_collapse">' . $s->COMP . '</td>';
          echo '<td class="bblm_tbl_collapse">' . $s->MINT . '</td>';
          echo '<td class="bblm_tbl_collapse">' . $s->MVP . '</td>';
          echo '<td>' . $s->SPP . '</td>';
          echo '</tr>';
					$zebracount++;
				}
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
			}

			// -- KILLER --
      $killersql = 'SELECT P.WPID AS PWPID, T.WPID AS TWPID, X.pos_name FROM '.$wpdb->prefix.'player_fate F, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'position X WHERE F.p_id = P.p_id AND P.t_id = T.t_id AND P.pos_id = X.pos_id AND F.pf_killer = '.$pd->p_id.' AND F.p_id != '.$pd->p_id.' ORDER BY F.m_id ASC';
			if ( $killer = $wpdb->get_results( $killersql ) ) {
        $zebracount = 1;
				//If the player has killed people
?>
        <h3 class="bblm-table-caption"><?php echo __( 'Players Killed','bblm' ); ?></h3>
        <div role="region" aria-labelledby="Caption01" tabindex="0">
          <table class="bblm_table">
            <thead>
              <tr>
                <th class="bblm_tbl_title"><?php echo __( 'Player Killed','bblm' ); ?></th>
                <th class="bblm_tbl_stat"><?php echo __( 'Position','bblm' ); ?></th>
                <th class="bblm_tbl_stat"><?php echo __( 'Team','bblm' ); ?></th>
              </tr>
            </thead>
            <tbody>
<?php
				foreach ( $killer as $k ) {
          if ( $zebracount % 2 ) {
            echo '<tr class="bblm_tbl_alt">';
          }
          else {
            echo '<tr>';
          }
          echo '<td>' . bblm_get_player_link( $k->PWPID ) . '</td>';
          echo '<td>' . esc_html( $k->pos_name ) . '</td>';
          echo '<td>' . bblm_get_team_link( $k->TWPID ) . '</td>';
          echo '</tr>';
          $zebracount++;
				}
?>
            </tbody>
          </table>
        </div>
<?php
			}

?>
    <h3 class="bblm-table-caption"><?php echo __( 'Performance by Season', 'bblm' ); ?></h3>
    <div role="region" aria-labelledby="Caption01" tabindex="0">
    <table class="bblm_table bblm_table_collapsable">
      <thead>
        <tr>
          <th class="bblm_tbl_title"><?php echo __( 'Season', 'bblm' ); ?></th>
          <th class="bblm_tbl_stat"><?php echo __( 'P', 'bblm' ); ?></th>
          <th class="bblm_tbl_stat"><?php echo __( 'TD', 'bblm' ); ?></th>
          <th class="bblm_tbl_stat"><?php echo __( 'CAS', 'bblm' ); ?></th>
          <th class="bblm_tbl_stat bblm_tbl_collapse"><?php echo __( 'COMP','bblm' ); ?></th>
					<th class="bblm_tbl_stat bblm_tbl_collapse"><?php echo __( 'INT','bblm' ); ?></th>
					<th class="bblm_tbl_stat bblm_tbl_collapse"><?php echo __( 'MVP','bblm' ); ?></th>
          <th class="bblm_tbl_stat"><?php echo __( 'SPP', 'bblm' ); ?></th>
        </tr>
      </thead>
      <tbody>
<?php
   $playerseasql = 'SELECT C.sea_id, COUNT(*) AS GAMES, SUM(M.mp_td) AS TD, SUM(M.mp_cas) AS CAS, SUM(M.mp_comp) AS COMP, SUM(M.mp_int) AS MINT, SUM(M.mp_mvp) AS MVP, SUM(M.mp_spp) AS SPP FROM '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'match Q WHERE C.c_counts = 1 AND M.m_id = Q.WPID AND Q.c_id = C.WPID AND M.p_id = P.p_id AND M.p_id = '.$pd->p_id.' GROUP BY C.sea_id ORDER BY C.sea_id DESC';
   if ( $playersea = $wpdb->get_results( $playerseasql ) ) {
     $zebracount = 1;
     foreach ( $playersea as $pc ) {
       if ( $zebracount % 2 ) {
         echo '<tr class="bblm_tbl_alt">';
       }
       else {
         echo '<tr>';
       }
       echo '<td>' . bblm_get_season_link( $pc->sea_id ) . '</td>';
       echo '<td>' . $pc->GAMES . '</td>';
       echo '<td>' . $pc->TD . '</td>';
       echo '<td>' . $pc->CAS . '</td>';
       echo '<td class="bblm_tbl_collapse">' . $pc->COMP . '</td>';
       echo '<td class="bblm_tbl_collapse">' . $pc->MINT . '</td>';
       echo '<td class="bblm_tbl_collapse">' . $pc->MVP . '</td>';
       echo '<td>' . $pc->SPP . '</td>';
       echo '</tr>';

       $zebracount++;
     }
   }
?>
  </tbody>
  </table>
</div>

  <h3 class="bblm-table-caption"><?php echo __( 'Performance by Championship Cup', 'bblm' ); ?></h3>
  <div role="region" aria-labelledby="Caption01" tabindex="0">
  <table class="bblm_table bblm_table_collapsable">
    <thead>
      <tr>
        <th class="bblm_tbl_title"><?php echo __( 'Championsip Cup', 'bblm' ); ?></th>
        <th class="bblm_tbl_stat"><?php echo __( 'P', 'bblm' ); ?></th>
        <th class="bblm_tbl_stat"><?php echo __( 'TD', 'bblm' ); ?></th>
        <th class="bblm_tbl_stat"><?php echo __( 'CAS', 'bblm' ); ?></th>
        <th class="bblm_tbl_stat bblm_tbl_collapse"><?php echo __( 'COMP','bblm' ); ?></th>
        <th class="bblm_tbl_stat bblm_tbl_collapse"><?php echo __( 'INT','bblm' ); ?></th>
        <th class="bblm_tbl_stat bblm_tbl_collapse"><?php echo __( 'MVP','bblm' ); ?></th>
        <th class="bblm_tbl_stat"><?php echo __( 'SPP', 'bblm' ); ?></th>
      </tr>
    </thead>
    <tbody>
<?php
    $playerseasql = 'SELECT C.series_id AS SWPID, COUNT(*) AS GAMES, SUM(M.mp_td) AS TD, SUM(M.mp_cas) AS CAS, SUM(M.mp_comp) AS COMP, SUM(M.mp_int) AS MINT, SUM(M.mp_mvp) AS MVP, SUM(M.mp_spp) AS SPP FROM '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'match Q WHERE C.c_counts = 1 AND M.m_id = Q.WPID AND Q.c_id = C.WPID AND M.p_id = P.p_id AND M.p_id = '.$pd->p_id.' GROUP BY C.series_id ORDER BY C.series_id DESC';
    if ( $playersea = $wpdb->get_results( $playerseasql ) ) {
      $zebracount = 1;
      foreach ( $playersea as $pc ) {
        if ( $zebracount % 2 ) {
          echo '<tr class="bblm_tbl_alt">';
        }
        else {
          echo '<tr>';
        }
        echo '<td>' . bblm_get_cup_link( $pc->SWPID ) . '</td>';
        echo '<td>' . $pc->GAMES . '</td>';
        echo '<td>' . $pc->TD . '</td>';
        echo '<td>' . $pc->CAS . '</td>';
        echo '<td class="bblm_tbl_collapse">' . $pc->COMP . '</td>';
        echo '<td class="bblm_tbl_collapse">' . $pc->MINT . '</td>';
        echo '<td class="bblm_tbl_collapse">' . $pc->MVP . '</td>';
        echo '<td>' . $pc->SPP . '</td>';
        echo '</tr>';

        $zebracount++;
      }
    }
?>
    </tbody>
    </table>
  </div>

    <h3 class="bblm-table-caption"><?php echo __( 'Performance by Competition', 'bblm' ); ?></h3>
    <div role="region" aria-labelledby="Caption01" tabindex="0">
    <table class="bblm_table bblm_table_collapsable">
      <thead>
        <tr>
          <th class="bblm_tbl_title"><?php echo __( 'Competition', 'bblm' ); ?></th>
          <th class="bblm_tbl_stat"><?php echo __( 'P', 'bblm' ); ?></th>
          <th class="bblm_tbl_stat"><?php echo __( 'TD', 'bblm' ); ?></th>
          <th class="bblm_tbl_stat"><?php echo __( 'CAS', 'bblm' ); ?></th>
          <th class="bblm_tbl_stat bblm_tbl_collapse"><?php echo __( 'COMP','bblm' ); ?></th>
					<th class="bblm_tbl_stat bblm_tbl_collapse"><?php echo __( 'INT','bblm' ); ?></th>
					<th class="bblm_tbl_stat bblm_tbl_collapse"><?php echo __( 'MVP','bblm' ); ?></th>
          <th class="bblm_tbl_stat"><?php echo __( 'SPP', 'bblm' ); ?></th>
        </tr>
      </thead>
      <tbody>
<?php
    $playercompsql = 'SELECT COUNT(*) AS GAMES, SUM(M.mp_td) AS TD, SUM(M.mp_cas) AS CAS, SUM(M.mp_comp) AS COMP, SUM(M.mp_int) AS MINT, SUM(M.mp_mvp) AS MVP, SUM(M.mp_spp) AS SPP, C.WPID AS CWPID FROM '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'match Q WHERE M.m_id = Q.WPID AND Q.c_id = C.WPID AND M.p_id = P.p_id AND M.p_id = '.$pd->p_id.' GROUP BY C.c_id ORDER BY C.c_id DESC';
    if ( $playercomp = $wpdb->get_results( $playercompsql ) ) {
      $zebracount = 1;
      foreach ( $playercomp as $pc ) {
        if ( $zebracount % 2 ) {
          echo '<tr class="bblm_tbl_alt">';
        }
        else {
          echo '<tr>';
        }
        echo '<td>' . bblm_get_competition_link( $pc->CWPID  ) . '</td>';
        echo '<td>' . $pc->GAMES . '</td>';
        echo '<td>' . $pc->TD . '</td>';
        echo '<td>' . $pc->CAS . '</td>';
        echo '<td class="bblm_tbl_collapse">' . $pc->COMP . '</td>';
        echo '<td class="bblm_tbl_collapse">' . $pc->MINT . '</td>';
        echo '<td class="bblm_tbl_collapse">' . $pc->MVP . '</td>';
        echo '<td>' . $pc->SPP . '</td>';
        echo '</tr>';
        $zebracount++;
      }
    }
?>
  </tbody>
</table>
</div>

      <h3 class="bblm-table-caption"><?php echo __( 'Recent Matches','bblm' ); ?></h3>
      <div role="region" aria-labelledby="Caption01" tabindex="0">
			<table class="bblm_table bblm_sortable bblm_expandable bblm_table_collapsable">
				<thead>
				<tr>
					<th><?php echo __( 'Date', 'bblm' ); ?></th>
					<th><?php echo __( 'For', 'bblm' ); ?></th>
          <th class="bblm_tbl_stat" ><?php echo __( 'TD', 'bblm' ); ?></th>
          <th class="bblm_tbl_stat" ><?php echo __( 'CAS', 'bblm' ); ?></th>
          <th class="bblm_tbl_stat bblm_tbl_collapse"><?php echo __( 'COMP','bblm' ); ?></th>
					<th class="bblm_tbl_stat bblm_tbl_collapse"><?php echo __( 'INT','bblm' ); ?></th>
					<th class="bblm_tbl_stat bblm_tbl_collapse"><?php echo __( 'MVP','bblm' ); ?></th>
          <th class="bblm_tbl_stat" ><?php echo __( 'SPP', 'bblm' ); ?></th>
				</tr>
				</thead>
				<tbody>
<?php
      $playermatchsql = 'SELECT P.*, M.WPID AS MWPID, UNIX_TIMESTAMP(M.m_date) AS mdate, A.WPID as TA, A.t_id AS TAid, B.WPID AS TB, B.t_id AS TBid FROM '.$wpdb->prefix.'match_player P, '.$wpdb->prefix.'match M, '.$wpdb->prefix.'team A, '.$wpdb->prefix.'team B WHERE M.m_teamA = A.t_id AND M.m_teamB = B.t_id AND M.WPID = P.m_id AND P.p_id = ' . $pd->p_id . ' ORDER BY M.m_date DESC';
			if ( $playermatch = $wpdb->get_results( $playermatchsql ) ) {
			$zebracount = 1;
      foreach ( $playermatch as $pm ) {
				if ( ( $zebracount % 2 ) && ( 10 < $zebracount ) ) {
          echo '<tr class="bblm_tbl_alt bblm_tbl_hide">';
        }
        else if (($zebracount % 2) && (10 >= $zebracount)) {
          echo '<tr class="bblm_tbl_alt">';
        }
        else if ( 10 < $zebracount ) {
          echo '<tr class="bblm_tbl_hide">';
        }
        else {
          echo '<tr>';
        }
        echo '<td>';
        echo bblm_get_match_link_date( $pm->MWPID );
        echo '</td>';
        if ( $pm->TAid == $pm->t_id ) {
          echo '<td>' . bblm_get_team_link( $pm->TA ) . '</td>';
        }
        else {
          echo '<td>' . bblm_get_team_link( $pm->TB ) . '</td>';
        }
        echo '<td>';
        if ( 0 == $pm->mp_td ) {
          echo "0";
        }
        else {
          echo '<strong>' . $pm->mp_td . '</strong>';
        }
        echo '</td>';
        echo '<td>';
        if ( 0 == $pm->mp_cas ) {
          echo "0";
        }
        else {
          echo '<strong>' . $pm->mp_cas . '</strong>';
        }
        echo '</td>';
        echo '<td class="bblm_tbl_collapse">';
        if ( 0 == $pm->mp_comp ) {
          echo "0";
        }
        else {
          echo '<strong>' . $pm->mp_comp . '</strong>';
        }
        echo '</td>';
        echo '<td class="bblm_tbl_collapse">';
        if ( 0 == $pm->mp_int ) {
          echo "0";
        }
        else {
          echo '<strong>' . $pm->mp_int . '</strong>';
        }
        echo '</td>';
        echo '<td class="bblm_tbl_collapse">';
        if ( 0 == $pm->mp_mvp ) {
          echo "0";
        }
        else {
          echo '<strong>' . $pm->mp_mvp . '</strong>';
        }
        echo '</td>';
        echo '<td>';
        if ( 0 == $pm->mp_spp ) {
          echo "0";
        }
        else {
          echo '<strong>' . $pm->mp_spp . '</strong>';
        }
        echo '</td>';
        echo '</tr>';
        $zebracount++;
      }
      echo '</tbody>';
      echo '</table>';
      echo '</div>';
    }




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
