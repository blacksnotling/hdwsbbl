<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Awards CPT functions
 *
 * Defines the functions related to the Awards CPT (archive page logic, display functions etc)
 * For admin functions reloated to the CPT see the includes/admin/post-types directory
 *
 * @class 		BBLM_CPT_Award
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/CPT
 * @version   1.1
 */

class BBLM_CPT_Award {

	/**
	 * Constructor
	 */
	public function __construct() {

    add_action( 'pre_get_posts', array( $this, 'manage_archives' ) );

	}

	/**
	 * stops the CPT archive pages pagenating
	 *
	 * @param wordpress $query
	 * @return none
	 */
	 public function manage_archives( $query ) {

	    if( is_post_type_archive( 'bblm_award' ) && !is_admin() && $query->is_main_query() ) {
	        $query->set( 'posts_per_page', -1 );
          $query->set( 'orderby', 'title' );
          $query->set( 'order', 'asc' );
          $query->set( 'post_status', 'publish' );
	    }

	  }

 /**
  * Outputs the winners of a championship Cup
  *
  * @param int $id the championship cup in question
  * @return int $count
  */
  public function display_cup_winners() {
		global $wpdb;
		global $post;

		$post_type = get_post_type(); //Determine the CPT that is calling this function

		$itemid = get_the_ID(); //The ID of the Page being displayed

		if ( $post_type == "bblm_cup" ) {

			$winnerssql = 'SELECT T.WPID, COUNT(*) as wins FROM '.$wpdb->prefix.'awards_team_comp A, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'comp C WHERE C.WPID = A.c_id AND A.t_id = T.t_id AND A.a_id = 1 AND C.series_id = '.$itemid.' GROUP BY A.t_id ORDER BY wins DESC';

		} //end of if ( $post_type == "bblm_cup" )

		if ( $winners = $wpdb->get_results( $winnerssql ) ) {
			$zebracount = 1;
?>
			<table class="bblm_table">
				<thead>
					<tr>
						<th class="bblm_tbl_name"><?php echo __( 'Team', 'bblm' ); ?></th>
						<th class="bblm_tbl_stat"><?php echo __( '# Wins', 'bblm' ); ?></th>
					</tr>
				</thead>
				<tbody>
<?php

			foreach ( $winners as $wi ) {

				if ( $zebracount % 2 ) {
					echo '<tr>';
				}
				else {
					echo '<tr class="bblm_tbl_alt">';
				}
				echo '<td>' . bblm_get_team_link( $wi->WPID ) . '</td>';
				echo '<td>' . $wi->wins . '</td>';
				echo '</tr>';

				$zebracount++;
			} //end of foreach

			echo '</tbody>';
			echo '</table>';
		}
		else {

			if ( $post_type == "bblm_cup" ) {

				echo '<p>' . __( 'No team has yet claimed this Championship Cup!', 'bblm' ) . '</p>';

			}

		}

  } //end of display_cup_winners()

/**
 * Outputs the winners of a championship Cup during a season
 *
 * @param int $id the season cup in question
 * @return int $count
 */
 public function display_cup_winners_in_a_season() {
	 global $wpdb;
	 global $post;

	 $post_type = get_post_type(); //Determine the CPT that is calling this function

	 $itemid = get_the_ID(); //The ID of the Page being displayed

	 if ( $post_type == "bblm_season" ) {

		 $winnerssql = 'SELECT COUNT(*) AS wins, X.WPID FROM '.$wpdb->prefix.'awards_team_comp T, '.$wpdb->prefix.'awards A, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'team X WHERE X.t_id = T.t_id AND T.c_id = C.WPID AND C.c_counts = 1 AND C.type_id = 1 AND A.a_id = 1 AND A.a_id = T.a_id AND C.sea_id = ' . $itemid . ' GROUP BY T.t_id ORDER BY wins DESC, X.WPID DESC';

	 } //end of if ( $post_type == "bblm_cup" )
	 if ( $winners = $wpdb->get_results( $winnerssql ) ) {
		 $zebracount = 1;
?>
 			<table class="bblm_table">
 				<thead>
 					<tr>
 						<th class="bblm_tbl_name"><?php echo __( 'Team', 'bblm' ); ?></th>
 						<th class="bblm_tbl_stat"><?php echo __( '# Wins', 'bblm' ); ?></th>
 					</tr>
 				</thead>
 				<tbody>
 <?php

			foreach ( $winners as $wi ) {

 				if ( $zebracount % 2 ) {
 					echo '<tr>';
 				}
 				else {
 					echo '<tr class="bblm_tbl_alt">';
 				}
 				echo '<td>' . bblm_get_team_link( $wi->WPID ) . '</td>';
 				echo '<td>' . $wi->wins . '</td>';
 				echo '</tr>';

 				$zebracount++;
 			} //end of foreach

 			echo '</tbody>';
 			echo '</table>';
 		}
 		else {

 			if ( $post_type == "bblm_cup" ) {

 				echo '<p>' . __( 'No team has won a Championship Cup this season!', 'bblm' ) . '</p>';

 			}

 		}

	} //end of display_cup_winners_in_a_season()

 /**
  * Outputs a full list of all award winners for a specified cup / comp, etc
  *
	* @param wordpress $query
  * @return string $output
  */
	public function display_list_award_winners() {
		global $wpdb;
		global $post;

		$post_type = get_post_type(); //Determine the CPT that is calling this function

		$itemid = get_the_ID(); //The ID of the Page being displayed

		if ( $post_type == "bblm_cup" ) {

			$compmajorawardssql = 'SELECT A.a_name, T.WPID, C.WPID AS CWPID FROM '.$wpdb->prefix.'awards A, '.$wpdb->prefix.'awards_team_comp B, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'comp C WHERE A.a_id = B.a_id AND a_cup = 1 AND B.t_id = T.t_id AND B.c_id = C.WPID AND C.series_id = '.$itemid.' ORDER BY A.a_id ASC, C.c_id DESC';
			$compteamawardssql = 'SELECT A.a_name, T.WPID, B.atc_value AS value, C.WPID AS CWPID FROM '.$wpdb->prefix.'awards A, '.$wpdb->prefix.'awards_team_comp B, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'team T WHERE C.WPID = B.c_id AND A.a_id = B.a_id AND a_cup = 0 AND B.t_id = T.t_id AND C.series_id = '.$itemid.' ORDER BY A.a_id ASC, C.c_id DESC';
			$compplayerawardssql = 'SELECT A.a_name, R.WPID AS PID, B.apc_value AS value, T.WPID, C.WPID AS CWPID FROM '.$wpdb->prefix.'awards A, '.$wpdb->prefix.'awards_player_comp B, '.$wpdb->prefix.'player R, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'comp C WHERE C.WPID = B.c_id AND R.t_id = T.t_id AND R.p_id = B.p_id AND A.a_id = B.a_id AND a_cup = 0 AND C.series_id = '.$itemid.' ORDER BY A.a_id ASC, C.c_id DESC';

		} //end of if ( $post_type == "bblm_cup" )
		else if ( $post_type == "bblm_season" ) {

			$compmajorawardssql = 'SELECT A.a_name, T.WPID, C.WPID AS CWPID FROM '.$wpdb->prefix.'awards A, '.$wpdb->prefix.'awards_team_comp B, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'comp C WHERE T.t_id = B.t_id AND A.a_id = B.a_id AND a_cup = 1 AND B.c_id = C.WPID AND C.c_counts = 1 AND C.type_id = 1 AND C.sea_id = ' . $itemid . ' ORDER BY C.c_id ASC, A.a_id ASC';
			$compteamawardssql = 'SELECT A.a_name, B.ats_value AS value, T.WPID FROM '.$wpdb->prefix.'awards A, '.$wpdb->prefix.'awards_team_sea B, '.$wpdb->prefix.'team T WHERE A.a_id = B.a_id AND B.t_id = T.t_id AND B.sea_id = '.$itemid.' ORDER BY A.a_id ASC';
			$compplayerawardssql = 'SELECT A.a_name, P.WPID AS PID, B.aps_value AS value, T.WPID FROM '.$wpdb->prefix.'awards A, '.$wpdb->prefix.'awards_player_sea B, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'player P WHERE P.t_id = T.t_id AND A.a_id = B.a_id AND B.p_id = P.p_id AND B.sea_id = '.$itemid.' ORDER BY A.a_id ASC';

		} //end of else if ( $post_type == "bblm_season" ) {

		if ( $cmawards = $wpdb->get_results( $compmajorawardssql ) ) {
			$zebracount = 1;

?>
			<h4><?php echo __( 'Main Awards', 'bblm' ); ?></h4>
			<table class="bblm_table">
				<thead>
					<tr>
						<th class="bblm_tbl_name"><?php echo __( 'Award', 'bblm' ); ?></th>
						<th class="bblm_tbl_name"><?php echo __( 'Team', 'bblm' ); ?></th>
						<th class="bblm_tbl_name"><?php echo __( 'Competition', 'bblm' ); ?></th>
					</tr>
	      </thead>
	      <tbody>
<?php
			foreach ( $cmawards as $cma ) {
				if ($zebracount % 2) {
					echo '<tr>';
				}
				else {
					echo '<tr class="bblm_tbl_alt">';
				}
				echo '<td>' . $cma->a_name . '</td>';
				echo '<td>' . bblm_get_team_link( $cma->WPID ) . '</td>';
				echo '<td>' . bblm_get_competition_link( $cma->CWPID ) . '</td>';
				echo '</tr>';
				$zebracount++;
			} //end of foreach
			echo '</tbody>';
			echo '</table>';
		}

		if ( $ctawards = $wpdb->get_results( $compteamawardssql ) ) {
			$zebracount = 1;

?>
			<h4><?php echo __( 'Awards assigned to Teams', 'bblm' ); ?></h4>
			<table class="bblm_table">
				<thead>
					<tr>
						<th class="bblm_tbl_name"><?php echo __( 'Award', 'bblm' ); ?></th>
						<th class="bblm_tbl_name"><?php echo __( 'Team', 'bblm' ); ?></th>
						<?php if ( $post_type == "bblm_cup" ) { ?>
							<th class="bblm_tbl_name"><?php echo __( 'Competition', 'bblm' ); ?></th>
							<?php } ?>
						<th class="bblm_tbl_stat"><?php echo __( 'Value', 'bblm' ); ?></th>
					</tr>
				</thead>
				<tbody>
<?php
			foreach ($ctawards as $cta) {
				if ($zebracount % 2) {
					echo '<tr>';
				}
				else {
					echo '<tr class="bblm_tbl_alt">';
				}
				echo '<td>' . $cta->a_name . '</td>';
				echo '<td>' . bblm_get_team_link( $cta->WPID ) . '</td>';
				if ( $post_type == "bblm_cup" ) {
					echo '<td>' . bblm_get_competition_link( $cta->CWPID ) . '</td>';
				}
				echo '<td>' . $cta->value . '</td>';
				echo '</tr>';
				$zebracount++;
			} //end of foreach
			echo '</tbody>';
			echo '</table>';
		}

		if ( $cpawards = $wpdb->get_results( $compplayerawardssql ) ) {
			$zebracount = 1;


?>
			<h4><?php echo __( 'Awards assigned to Players', 'bblm' ); ?></h4>
			<table class="bblm_table">
				<thead>
					<tr>
						<th class="bblm_tbl_name"><?php echo __( 'Award', 'bblm' ); ?></th>
						<th class="bblm_tbl_name"><?php echo __( 'Player', 'bblm' ); ?></th>
						<th class="bblm_tbl_name"><?php echo __( 'Team', 'bblm' ); ?></th>
						<?php if ( $post_type == "bblm_cup" ) { ?>
							<th class="bblm_tbl_name"><?php echo __( 'Competition', 'bblm' ); ?></th>
						<?php } ?>
						<th class="bblm_tbl_stat"><?php echo __( 'Value', 'bblm' ); ?></th>
					</tr>
				</thead>
				<tbody>
<?php
			foreach ( $cpawards as $cpa ) {
				if ($zebracount % 2) {
					echo '<tr>';
				}
				else {
					echo '<tr class="bblm_tbl_alt">';
				}
				echo '<td>' . $cpa->a_name . '</td>';
				echo '<td>' . bblm_get_player_link( $cpa->PID ) . '</td>';
				echo '<td>' . bblm_get_team_link( $cpa->WPID ) . '</td>';
				if ( $post_type == "bblm_cup" ) {
					echo '<td>' . bblm_get_competition_link( $cpa->CWPID ) . '</td>';
				}
				echo '<td>' . $cpa->value . '</td>';
				echo '</tr>';
				$zebracount++;
			} //end of foreach
			echo '</tbody>';
			echo '</table>';
		}

	} //end of display_list_award_winners()


} //end of class

new BBLM_CPT_Award();
