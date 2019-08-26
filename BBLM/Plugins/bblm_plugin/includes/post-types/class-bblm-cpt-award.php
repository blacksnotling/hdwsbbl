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
 * @version   1.0
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

			$winnerssql = 'SELECT T.WPID, COUNT(*) as wins FROM '.$wpdb->prefix.'awards_team_comp A, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'comp C WHERE C.c_id = A.c_id AND A.t_id = T.t_id AND A.a_id = 1 AND C.series_id = '.$itemid.' GROUP BY A.t_id ORDER BY wins DESC';

		} //end of if ( $post_type == "bblm_cup" )

		if ( $winners = $wpdb->get_results( $winnerssql ) ) {
			$zebracount = 1;
?>
			<table class="bblm_tbl">
				<thead>
					<tr>
						<th class="tbl_name bblm_tbl_name"><?php echo __( 'Team', 'bblm' ); ?></th>
						<th class="tbl_stat bblm_tbl_stat"><?php echo __( '# Wins', 'bblm' ); ?></th>
					</tr>
				</thead>
				<tbody>
<?php

			foreach ( $winners as $wi ) {

				if ( $zebracount % 2 ) {
					echo '<tr>';
				}
				else {
					echo '<tr class="tbl_alt bblm_tbl_alt">';
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

			$compmajorawardssql = 'SELECT A.a_name, T.WPID, H.post_title AS CompName, H.guid AS CompLink FROM '.$wpdb->prefix.'awards A, '.$wpdb->prefix.'awards_team_comp B, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp Y, '.$wpdb->posts.' H WHERE C.c_id = Y.tid AND Y.prefix = \'c_\' AND Y.pid = H.ID AND A.a_id = B.a_id AND a_cup = 1 AND B.t_id = T.t_id AND B.c_id = C.c_id AND C.series_id = '.$itemid.' ORDER BY A.a_id ASC, C.c_id DESC';
			$compteamawardssql = 'SELECT A.a_name, T.WPID, B.atc_value AS value, U.post_title AS CompName, U.guid AS CompLink FROM '.$wpdb->prefix.'awards A, '.$wpdb->prefix.'awards_team_comp B, '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp Y, '.$wpdb->posts.' U, '.$wpdb->prefix.'team T WHERE Y.tid = C.c_id AND Y.prefix = \'c_\' AND Y.pid = U.ID AND C.c_id = B.c_id AND A.a_id = B.a_id AND a_cup = 0 AND B.t_id = T.t_id AND C.series_id = '.$itemid.' ORDER BY A.a_id ASC, C.c_id DESC';
			$compplayerawardssql = 'SELECT A.a_name, R.WPID AS PID, B.apc_value AS value, T.WPID, U.post_title AS CompName, U.guid AS CompLink FROM '.$wpdb->prefix.'awards A, '.$wpdb->prefix.'awards_player_comp B, '.$wpdb->prefix.'player R, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'bb2wp Y, '.$wpdb->posts.' U, '.$wpdb->prefix.'comp C WHERE Y.tid = C.c_id AND Y.prefix = \'c_\' AND Y.pid = U.ID AND C.c_id = B.c_id AND R.t_id = T.t_id AND R.p_id = B.p_id AND A.a_id = B.a_id AND a_cup = 0 AND C.series_id = '.$itemid.' ORDER BY A.a_id ASC, C.c_id DESC';

		} //end of if ( $post_type == "bblm_cup" )

		if ( $cmawards = $wpdb->get_results( $compmajorawardssql ) ) {
			$zebracount = 1;

?>
			<h4><?php echo __( 'Main Awards', 'bblm' ); ?></h4>
			<table class="bblm_tbl">
				<thead>
					<tr>
						<th class="tbl_name bblm_tbl_name"><?php echo __( 'Award', 'bblm' ); ?></th>
						<th class="tbl_name bblm_tbl_name"><?php echo __( 'Team', 'bblm' ); ?></th>
						<th class="tbl_name bblm_tbl_name"><?php echo __( 'Competition', 'bblm' ); ?></th>
					</tr>
	      </thead>
	      <tbody>
<?php
			foreach ( $cmawards as $cma ) {
				if ($zebracount % 2) {
					echo '<tr>';
				}
				else {
					echo '<tr class="tbl_alt bblm_tbl_alt">';
				}
				echo '<td>' . $cma->a_name . '</td>';
				echo '<td>' . bblm_get_team_link( $cma->WPID ) . '</td>';
				echo '<td><a href="' . $cma->CompLink . '" title="Read more about ' . $cma->CompName . '">' . $cma->CompName . '</a></td>';
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
			<table class="bblm_tbl">
				<thead>
					<tr>
						<th class="tbl_name bblm_tbl_name"><?php echo __( 'Award', 'bblm' ); ?></th>
						<th class="tbl_name bblm_tbl_name"><?php echo __( 'Team', 'bblm' ); ?></th>
						<th class="tbl_name bblm_tbl_name"><?php echo __( 'Competition', 'bblm' ); ?></th>
						<th class="tbl_stat bblm_tbl_stat"><?php echo __( 'Value', 'bblm' ); ?></th>
					</tr>
				</thead>
				<tbody>
<?php
			foreach ($ctawards as $cta) {
				if ($zebracount % 2) {
					echo '<tr>';
				}
				else {
					echo '<tr class="tbl_alt bblm_tbl_alt">';
				}
				echo '<td>' . $cta->a_name . '</td>';
				echo '<td>' . bblm_get_team_link( $cta->WPID ) . '</td>';
				echo '<td><a href="' . $cta->CompLink . '" title="Read more about ' . $cta->CompName . '">' . $cta->CompName . '</a></td>';
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
			<table class="bblm_tbl">
				<thead>
					<tr>
						<th class="tbl_name bblm_tbl_name"><?php echo __( 'Award', 'bblm' ); ?></th>
						<th class="tbl_name bblm_tbl_name"><?php echo __( 'Player', 'bblm' ); ?></th>
						<th class="tbl_name bblm_tbl_name"><?php echo __( 'Team', 'bblm' ); ?></th>
						<th class="tbl_name bblm_tbl_name"><?php echo __( 'Competition', 'bblm' ); ?></th>
						<th class="tbl_stat bblm_tbl_stat"><?php echo __( 'Value', 'bblm' ); ?></th>
					</tr>
				</thead>
				<tbody>
<?php
			foreach ( $cpawards as $cpa ) {
				if ($zebracount % 2) {
					echo '<tr>';
				}
				else {
					echo '<tr class="tbl_alt bblm_tbl_alt">';
				}
				echo '<td>' . $cpa->a_name . '</td>';
				echo '<td>' . bblm_get_player_link( $cpa->PID ) . '</td>';
				echo '<td>' . bblm_get_team_link( $cpa->WPID ) . '</td>';
				echo '<td><a href="' . $cpa->CompLink . '" title="Read more about ' . $cpa->CompName . '">' . $cpa->CompName . '</a></td>';
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
