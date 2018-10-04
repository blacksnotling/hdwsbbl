<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Teams CPT Meta-Boxes
 *
 * THe output and save functions for Meta-Boxes linked to the Teams CPT
 * For the other admin functions see the admin/post-meta directory
 * For front end functions reloated to the CPT see the includes/post-types directory
 *
 * @class 		BBLM_Meta_Team
 * @version		1.0
 * @package		BBowlLeagueMan/Admin/CPT/Meta_Boxes
 * @category	Class
 * @author 		blacksnotling
 */

class BBLM_Meta_Team {

  /**
	 * Constructor
	 */
	public function __construct() {

    add_action( 'add_meta_boxes', array( $this, 'register_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_meta_boxes' ),  10, 2 );

	}

  /**
	 * Register the metaboxes to be used for the post type
	 *
	 */
	public function register_meta_boxes() {

		add_meta_box(
			'team_info',
			__( 'Team Information', 'bblm' ),
			array( $this, 'render_meta_box_info' ),
			'bblm_team',
			'normal',
			'low'
		);

	}

 /**
  * The HTML for the Meta Box to display the Team Information
  *
  */
  function render_meta_box_info( $post ) {
		global $wpdb;

 	 $pos = ""; // holds the data from the database for the team
	 $newteam = 0; //variable to determine if this is a new team or an existing one

 	 $sql = "SELECT * FROM ".$wpdb->prefix."team where tID = ".$post->ID;
 	 if ( $pos = $wpdb->get_results( $sql, 'ARRAY_A' ) ) {
 		 //we are editing an existing team
 	 }
	 else {
		 //ininalise array to hold dummy data as this is a new team
		 $pos = array( array(
  		 't_hcoach' => 'Unkown',
  		 't_rr' => '0',
  		 't_ff' => '0',
			 't_cl' => '0',
			 't_ac' => '0',
			 't_bank' => '0',
			 't_tv' => '0',
			 't_sname' => '',
			 'r_id' => '',
  	 	),
  	);
		$newteam = 1;

	 }
	 //Grab the cost of Rerolls from the corresponding Race Post ID
	 $meta = get_post_custom( $pos[0][ 'r_id' ] );
	 $rrcost = ! isset( $meta['race_rrcost'][0] ) ? '00000' : $meta['race_rrcost'][0];
 ?>
 <div class="field">
	 	<p>Administrator View</p>
		<input type="hidden" name="comp_cid" value="<?php if ( isset( $pos[0][ 't_id' ] ) ) { echo $pos[0][ 't_id' ]; } else { echo 'x'; } ?>"/>
		<input type="hidden" name="bblm_ttv" value="<?php if ( isset( $pos[0][ 't_tv' ] ) ) { echo $pos[0][ 't_tv' ]; } ?>"/>

		<table>
			<?php
						if ( $newteam ) {
							//Print a hidden field that sets the activ e status to true (we don't create disbanded teams)
?>
		<input type="hidden" name="bblm_tactive" value="1"/>
<?php
						}
						else {
							//this is an existing team so we can use this to change the teams active status
			?>
			<tr>
				<td><label for="bblm_tactive">Status</label></td>
				<td><select name="bblm_tactive" id="bblm_tactive">
					<option value="1"<?php if ( 1 == $pos[0][ 't_active' ] ) { echo ' selected="selected"'; } ?>>Active</option>
					<option value="0"<?php if ( 0 == $pos[0][ 't_active' ] ) { echo ' selected="selected"'; } ?>>Disbanded</option>
				</select></td>
			</tr>
<?php }//end of if team active - show status dropdown ?>
			<tr>
				<td><label for="bblm_tsname">Short Name</label></td>
				<td><input type="text" name="bblm_tsname" size="7" value="<?php if ( isset( $pos[0][ 't_sname' ] ) ) { echo $pos[0][ 't_sname' ]; } ?>" maxlength="5" id="bblm_tsname"> Used on summary pages. Does not have to be unique</td>
			</tr>
			<tr>
				<td><label for="bblm_tstad">Stadium</label></td>
				<td>
<?php
				//generate the list of Cups into a dropdown
				$stadargs = array(
						'post_type' => 'bblm_stadium',
						'orderby' => 'title',
						'order'   => 'ASC',
						'posts_per_page'=> -1,
						);

				$query = new WP_Query( $stadargs );

				if ( $query->have_posts() ) : ?>
				<select name="bblm_tstad" id="bblm_tstad">
					<?php while ( $query->have_posts() ) : $query->the_post(); ?>
						<option value="<?php the_ID(); ?>"<?php if ( get_the_ID() == $pos[0][ 'stad_id' ] ) { echo ' selected="selected"'; } ?>><?php the_title(); ?></option>
					<?php endwhile; wp_reset_postdata();?>
				</select>
				<?php endif; ?>
				<br>
				ALEX BUTTON TO CREATE A NEW STADIUM IF THE ADMIN HAS FORGOTTN....</td>
			</tr>
			<tr>
				<td><label for="bblm_trace">Race</label></td>
				<td>
<?php
						$type = get_post_meta( $post->ID, 'team_race', true );
						if ( empty( $type ) ) {

							//This is a new team so we need to let a user select the race
							$raceargs = array(
									'post_type' => 'bblm_race',
									'orderby' => 'title',
									'order'   => 'ASC',
									'posts_per_page'=> -1,
									);

							$query = new WP_Query( $raceargs );

							if ( $query->have_posts() ) : ?>
							<select name="bblm_tstad" id="bblm_tstad">
								<?php while ( $query->have_posts() ) : $query->the_post(); ?>
									<option value="<?php the_ID(); ?>"><?php the_title(); ?></option>
								<?php endwhile; wp_reset_postdata();?>
							</select>
							<?php endif;

						}

						else {

							//This is a preexisting team so pull the race info from post-meta
							echo get_the_title( $type );

						}

				?></td>
			</tr>
			<tr>
				<td><label for="bblm_thcoach">Head Coach</label></td>
				<td><input type="text" name="bblm_thcoach" size="25" value="<?php if ( isset( $pos[0][ 't_hcoach' ] ) ) { echo $pos[0][ 't_hcoach' ]; } else { echo 'Unkown'; } ?>" maxlength="25"/></td>
			</tr>
			<tr>
				<td><label for="bblm_tuser">Owner (Real Player)</label></td>
				<td><select name="bblm_tuser" id="bblm_tuser">
<?php
					$blogusers = get_users( array( 'fields' => array( 'display_name', 'ID' ) ) );
					foreach ( $blogusers as $user ) {
?>
						<option value="<?php echo $user->ID; ?>"<?php if ( $user->ID == $pos[0][ 'ID' ] ) { echo ' selected="selected"'; } ?>><?php echo esc_html( $user->display_name ); ?></option>
<?php
					}
?>
				</select> (<a href="<?php echo admin_url(); ?>users.php">Manage Users</a>)</td>
			</tr>
			<tr>
				<td><label for="bblm_trr">Re-Rolls</label></td>
				<td><input type="text" name="bblm_trr" size="2" value="<?php if ( isset( $pos[0][ 't_rr' ] ) ) { echo $pos[0][ 't_rr' ]; } ?>" maxlength="1" id="bblm_trr">
				@ <?php echo number_format( $rrcost ); ?>gp each - remember that they cost double when bought during a season</td>
			</tr>
			<tr>
				<td><label for="bblm_tff">Fan Factor</label></td>
				  <td><input type="text" name="bblm_tff" size="2" value="<?php if ( isset( $pos[0][ 't_ff' ] ) ) { echo $pos[0][ 't_ff' ]; } ?>" maxlength="2" id="bblm_tff">
				  @ 10,000gp each</td>
			</tr>
			<tr>
				<td><label for="bblm_tcl">Cheerleaders</label></td>
				  <td><input type="text" name="bblm_tcl" size="2" value="<?php if ( isset( $pos[0][ 't_cl' ] ) ) { echo $pos[0][ 't_cl' ]; } ?>" maxlength="2" id="bblm_tcl">
				  @ 10,000gp each</td>
			</tr>
			<tr>
				<td><label for="bblm_tac">Assistant Coaches</label></td>
				  <td><input type="text" name="bblm_tac" size="2" value="<?php if ( isset( $pos[0][ 't_ac' ] ) ) { echo $pos[0][ 't_ac' ]; } ?>" maxlength="3" id="bblm_tac">
				  @ 10,000gp each</td>
			</tr>
			<tr>
				<td><label for="bblm_tapoc">Apothecary</label></td>
				<td><select name="bblm_tapoc" id="bblm_tapoc">
					<option value="0"<?php if ( 0 == $pos[0][ 't_apoc' ] ) { echo ' selected="selected"'; } ?>>No</option>
					<option value="1"<?php if ( 1 == $pos[0][ 't_apoc' ] ) { echo ' selected="selected"'; } ?>>Yes</option>
				</select> @ 50,000 each</td>
			</tr>
			<tr>
				<td><label for="bblm_tbank">Treasury</label></td>
			  <td><input type="text" name="bblm_tbank" size="7" value="<?php if ( isset( $pos[0][ 't_bank' ] ) ) { echo $pos[0][ 't_bank' ]; } ?>" maxlength="10" id="bblm_tbank">gp</td>
			</tr>
		</table>

 </div>
 <?php

  }

 /**
 	* Action when Saving the post type
 	*
 	*/
 	function save_meta_boxes( $post_id ) {
 		global $post;

 		// Verify nonce
 		if ( !isset( $_POST['team_info'] ) || !wp_verify_nonce( $_POST['team_info'], basename(__FILE__) ) ) {
 			return $post_id;
 		}
 		// Check Autosave
 		if ( (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || ( defined('DOING_AJAX') && DOING_AJAX) || isset($_REQUEST['bulk_edit']) ) {
 			return $post_id;
 		}
 		// Don't save if only a revision
 		if ( isset( $post->post_type ) && $post->post_type == 'revision' ) {
 			return $post_id;
 		}
 		// Check permissions
 		if ( !current_user_can( 'edit_post', $post->ID ) ) {
 			return $post_id;
 		}
 		$meta['season_sdate'] = ( isset( $_POST['season_sdate'] ) ? esc_textarea( $_POST['season_sdate'] ) : '' );
		$meta['season_fdate'] = ( isset( $_POST['season_fdate'] ) ? esc_textarea( $_POST['season_fdate'] ) : '' );
 		foreach ( $meta as $key => $value ) {
 			update_post_meta( $post->ID, $key, $value );
 		}

 	}

}
new BBLM_Meta_Team();
