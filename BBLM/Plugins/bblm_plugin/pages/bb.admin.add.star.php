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
					<th scope="row"><label for="bblm_plyd"><?php echo __('Available for','bblm'); ?></label></th>
					<?php
							//Grabs a list of 'posts' from the Stadiums CPT
							$oposts = get_posts(
								array(
									'post_type' => 'bblm_race',
									'numberposts' => -1,
									'orderby' => 'post_title',
									'order' => 'ASC'
								)
							);
							if( ! $oposts ) return;
							echo '<td>';
							echo '<ul>';
							$p = 1;
							foreach( $oposts as $o ) {
								echo '<li><input type="checkbox" name="bblm_plyd' . $p . '"/> ' . bblm_get_race_name( $o->ID ) .' <input type="hidden" name="bblm_raceid' . $p . '" id="bblm_raceid' . $p . '" value="' . $o->ID .'"></li>';
								$p++;
							}
							echo '</ul>';
							echo '</td>';

	?>
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

			 //Determine the parent page
			 $options = get_option('bblm_config');
			 $bblm_page_parent = htmlspecialchars($options['page_stars'], ENT_QUOTES);

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
				 'post_type' => 'page',
				 'post_status' => 'publish',
				 'comment_status' => 'closed',
				 'ping_status' => 'closed',
				 'post_parent' => $bblm_page_parent
			 );
			 if ($bblm_submission = wp_insert_post( $my_post )) {
				 add_post_meta( $bblm_submission, '_wp_page_template', BBLM_TEMPLATE_PATH . 'single-bblm_starplayers.php' );

				 $bblmdatasql = 'INSERT INTO `'.$wpdb->prefix.'player` (`p_id`, `t_id`, `pos_id`, `p_name`, `p_num`, `p_ma`, `p_st`, `p_ag`, `p_pa`, `p_av`, `p_spp`, `p_skills`, `p_mng`, `p_injuries`, `p_cost`, `p_cost_ng`, `p_status`, `p_img`, `p_former`) VALUES (\'\', \''.$bblm_team_star.'\', \''.$posnum.'\', \''.wp_filter_nohtml_kses($_POST['bblm_pname']).'\', \'00\', \''.$_POST['bblm_pma'].'\', \''.$_POST['bblm_pst'].'\', \''.$_POST['bblm_pag'].'\', \''.$_POST['bblm_ppa'].'\', \''.$_POST['bblm_pav'].'\', \'0\', \''.$_POST['bblm_pskills'].'\', \'0\', \'none\', \''.$_POST['bblm_pcost'].'\', \''.$_POST['bblm_pcost'].'\', \'1\', \'\', \'0\')';
				 $wpdb->query( $bblmdatasql );

				 //Store the player ID (p_id)
				 $bblm_player_id = $wpdb->insert_id;

				 $bblmmappingsql = 'INSERT INTO `'.$wpdb->prefix.'bb2wp` (`bb2wp_id`, `tid`, `pid`, `prefix`) VALUES (\'\',\''.$bblm_player_id.'\', \''.$bblm_submission.'\', \'p_\')';
				 $wpdb->query( $bblmmappingsql );

				 //Update the stars WPID value
				 $playerupdatesql = 'UPDATE  `'.$wpdb->prefix.'player` SET  `WPID` =  "'.$bblm_submission.'" WHERE  `p_id` ='.$bblm_player_id;
				 $wpdb->query( $playerupdatesql );

				 $success = 1;
				 $addattempt = 1;
				 do_action( 'bblm_post_submission' );

				 //Now we populate the race2star table in the database
				 $p = 1;
				 $race2starsqla = array();
				 while ( $p <= $_POST['bblm_numofraces'] ) {
					 //if  "on" result for a field then generate SQL
					 if ( on == $_POST['bblm_plyd'.$p] ) {

						 $insertstarracesql = 'INSERT INTO `'.$wpdb->prefix.'race2star` (`r_id`, `p_id`) VALUES (\''.$_POST['bblm_raceid'.$p].'\', \''.$bblm_player_id.'\')';
						 $race2starsqla[$p] = $insertstarracesql;
					 }
					 $p++;
				 }

				 foreach ( $race2starsqla as $ps ) {
					 $addstar2race = $wpdb->query( $ps );
				 }
			 } //end of if post insertion was successful

			}//end of if submitted, and nonce is valid

			if ( $success ) {
				echo '<div id="updated" class="notice notice-success inline">';
				echo '<p>';
				echo __( 'Star Player has been added.','bblm' ) . ' <a href="' . get_permalink( $bblm_submission ) .'" title="View the match page">' . __( 'View the Star Player','bblm' ) . '</a>';
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
