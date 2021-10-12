<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class used to add Star Players to the league
 *
 * @class 		BBLM_Add_StarPlayers
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/Admin
 * @version   1.0
 */

class BBLM_Add_StarPlayers {

	// class constructor
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'plugin_menu' ) );
	}

	public function plugin_menu() {

		$hook = add_submenu_page(
			'bblm_main_menu',
			__( 'Add Star', 'bblm' ),
			__( 'Add Star', 'bblm' ),
			'manage_options',
			'bblm_addstars',
			array( $this, 'add_stars_page' )
		);

	}

	/**
	 * The Output of the Page
	 */
	public function add_stars_page() {
		global $wpdb;
?>
	<div class="wrap">
		<h1 class="wp-heading-inline"><?php echo __( 'Add a Star Player','bblm' ); ?></h1>
<?php
		if ( isset( $_POST['bblm_star_submit'] ) ) {
			//If we have submitted a new Star then pass to the correct function to save
			$this->submit_handling();
		} //end of if ( isset( $_POST['bblm_star_submit'] ) ) {

		//Now we move onto displaying the form
?>
		<p><?php echo __( 'Use the following page to add a new Star Player to the League.','bblm' ); ?></p>

		<form name="bblm_addstar" method="post" id="post">
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label id="title-prompt-text" for="bblm_pname"><?php echo __('Star Player Name','bblm'); ?></label></th>
					<td><input type="text" name="bblm_pname" size="30" maxlength="30" value="" id="bblm_pname" spellcheck="true" autocomplete="off" placeholder="<?php echo __('Star Player Name','bblm'); ?>"/></td>
				</tr>
				<tr valign="top">
					<th colspan="2" scope="row"><?php echo __('Statistics','bblm'); ?></th>
				</tr>
				<tr valign="top">
					<td colspan="2">
						<table class="bblm_admin_table">
							<tr>
								<th><label for="bblm_pma">MA</label></th>
								<th><label for="bblm_pst">ST</label></th>
								<th><label for="bblm_pag">AG</label></th>
								<th><label for="bblm_ppa">PA</label></th>
								<th><label for="bblm_pav">AV</label></th>
							</tr>
							<tr>
								<td><input type="text" name="bblm_pma" size="2" value="6" maxlength="1" id="bblm_pma" class="small-text" autocomplete="off" /></td>
								<td><input type="text" name="bblm_pst" size="2" value="4" maxlength="1" id="bblm_pst" class="small-text" autocomplete="off" /></td>
								<td><input type="text" name="bblm_pag" size="2" value="4" maxlength="1" id="bblm_pag" class="small-text" autocomplete="off" />+</td>
								<td><input type="text" name="bblm_ppa" size="2" value="4" maxlength="1" id="bblm_ppa" class="small-text" autocomplete="off" />+</td>
								<td><input type="text" name="bblm_pav" size="2" value="8" maxlength="2" id="bblm_pav" class="small-text" autocomplete="off" />+</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="bblm_pcost"><?php echo __('Cost Per Match','bblm'); ?></label></th>
					<td><input type="text" name="bblm_pcost" size="10" value="100000" maxlength="6" id="bblm_pcost" class="regular-text" autocomplete="off" />gp</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="bblm_pskills"><?php echo __('Skills','bblm'); ?></label></th>
					<td><p><textarea rows="10" cols="50" name="bblm_pskills" id="bblm_pskills" class="large-text"></textarea></p></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="bblm_racerule"><?php echo __('Available for','bblm'); ?></label></th>
					<td>
<?php
					$args = array( 'taxonomy' => 'race_rules', 'hide_empty' => false );

					$terms = get_terms( $args );
					echo '<ul>';
					$p = 1;
					foreach($terms as $term){
						echo '<li>';
						echo '<input type="checkbox" id="'.$term->slug.'" name="bblm_racerule'.$p.'"> '.$term->name;
						echo '<input type="hidden" name="bblm_ruleid' . $p . '" id="bblm_ruleid' . $p . '" value="' . $term->slug .'">';
						echo '</li>';

						$p++;
					}
					echo '</ul>';

	?>
					</td>
				</tr>
			</table>
			<input type="hidden" name="bblm_numofraces" id="bblm_numofraces" value="<?php echo $p-1; ?>">
			<p class="submit"><input type="submit" name="bblm_star_submit" value="Add Star" title="Add the Star Player" class="button-primary"/></p>
			<?php wp_nonce_field( basename( __FILE__ ), 'bblm_starplayer_submission' ); ?>
		</form>

	</div>

<?php

	} //end of add_stars_page()

	/**
	 * handles the submission of a new Star Player
	 */
	 public function submit_handling() {
		 global $wpdb;
		 $success = 0;

		 if ( ( isset( $_POST[ 'bblm_star_submit' ] ) ) && ( wp_verify_nonce( $_POST[ 'bblm_starplayer_submission' ], basename(__FILE__) ) ) ) {

			 //Determine other need options
			 $bblm_race_star = htmlspecialchars($options['race_star'], ENT_QUOTES);
			 $bblm_team_star = bblm_get_star_player_team();

			 //Determine Star Position
			 $posnumsql = "SELECT pos_id FROM ".$wpdb->prefix."position WHERE r_id = ".$bblm_race_star;
			 $posnum = $wpdb->get_var( $posnumsql );

			 $pdesc = wp_filter_nohtml_kses( $_POST['bblm_pname'] )." is a Star Player!";

			 $my_post = array(
				 'post_title' => wp_filter_nohtml_kses( $_POST['bblm_pname'] ),
				 'post_content' => $pdesc,
				 'post_type' => 'bblm_star',
				 'post_status' => 'publish',
				 'comment_status' => 'closed',
				 'ping_status' => 'closed'
			 );
			 if ( $bblm_submission = wp_insert_post( $my_post ) ) {
				 add_post_meta( $bblm_submission, '_wp_page_template', BBLM_TEMPLATE_PATH . 'single-bblm_starplayers.php' );

				 $playerargs = array (
					 't_id'			=> $bblm_team_star,
					 'pos'				=> $posnum,
					 'name'			=> get_the_title( $bblm_submission ),
					 'num'				=> 0,
					 'ma'				=> (int) $_POST['bblm_pma'],
					 'st'				=> (int) $_POST['bblm_pst'],
					 'ag'				=> (int) $_POST['bblm_pag'],
					 'pa'				=> (int) $_POST['bblm_ppa'],
					 'av'				=> (int) $_POST['bblm_pav'],
					 'spp'				=> 0,
					 'cspp'			=> 0,
					 'skills'		=> esc_textarea( $_POST['bblm_pskills'] ),
					 'mng'				=> 0,
					 'inj'				=> '',
					 'cost'			=> (int) $_POST['bblm_pcost'],
					 'costng'		=> (int) $_POST['bblm_pcost'],
					 'status'		=> '1',
					 'img'				=> '',
					 'former'		=> 0,
					 'WPID'			=> $bblm_submission,
					 'legacy'		=> 0,
					 'tr'				=> 0,
				 );

				 $playersql = 'INSERT INTO `'.$wpdb->prefix.'player` (`p_id`, `t_id`, `pos_id`, `p_name`, `p_num`, `p_ma`, `p_st`, `p_ag`, `p_pa`, `p_av`, `p_spp`, `p_cspp`, `p_skills`, `p_mng`, `p_injuries`, `p_cost`, `p_cost_ng`, `p_status`, `p_img`, `p_former`, `WPID`, `p_legacy`, `p_tr`) VALUES (NULL, \''.$playerargs['t_id'].'\', \''.$playerargs['pos'].'\', \''.$playerargs['name'].'\', \''.$playerargs['num'].'\', \''.$playerargs['ma'].'\', \''.$playerargs['st'].'\', \''.$playerargs['ag'].'\', \''.$playerargs['pa'].'\', \''.$playerargs['av'].'\', \''.$playerargs['spp'].'\', \''.$playerargs['cspp'].'\', \''.$playerargs['skills'].'\', \''.$playerargs['mng'].'\', \''.$playerargs['inj'].'\', \''.$playerargs['cost'].'\', \''.$playerargs['costng'].'\', \''.$playerargs['status'].'\', \''.$playerargs['img'].'\', \''.$playerargs['former'].'\', \''.$playerargs['WPID'].'\', \''.$playerargs['legacy'].'\', \''.$playerargs['tr'].'\');';

				 $wpdb->query( $playersql );

				 //Store the player ID (p_id)
				 $bblm_player_id = $wpdb->insert_id;

				 $success = 1;
				 do_action( 'bblm_post_submission' );

				 //Now we assign the player to the race special rules / traits
				 $p = 1;
				 $race2stars = array();

				 while ( $p <= $_POST['bblm_numofraces'] ) {
					 //if  "on" result for a field then generate SQL
					 if ( 'on' == $_POST['bblm_racerule'.$p] ) {

						 array_push( $race2stars ,$_POST['bblm_ruleid'.$p] );

					 }
					 $p++;
				 }

				 //Overwrite any existing terms for this ID
				 wp_set_object_terms( $bblm_submission, $race2stars, 'race_rules' );

			 } //end of if post insertion was successful

			}//end of if submitted, and nonce is valid

			if ( $success ) {
				echo '<div id="updated" class="notice notice-success inline">';
				echo '<p>';
				echo __( 'Star Player has been added.','bblm' ) . ' <a href="' . get_permalink( $bblm_submission ) .'" title="View the match page">' . __( 'View the Star Player','bblm' ) . '</a> or further <a href="' . admin_url( 'post.php?post=' . $bblm_submission . '&action=edit' ) . '">edit the star</a>';
				echo '</p>';
				echo '</div>';
			}
			else {
				echo '<div id="updated" class="notice notice-error inline">';
				echo '<p>';
				echo __( 'Something went wrong! Please try again.','bblm' );
				echo '</p>';
				echo '</div>';
			}

	 } //end of submit_handling_new

}//end of class BBLM_Add_StarPlayers
