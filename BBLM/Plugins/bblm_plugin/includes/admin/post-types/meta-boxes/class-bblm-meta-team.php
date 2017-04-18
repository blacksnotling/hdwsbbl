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
			'team_race',
      __( 'Race', 'bblm' ),
			array( $this, 'render_meta_box_race' ),
			'bblm_team',
			'side',
			'low'
		);

		add_meta_box(
			'team_stadium',
			__( 'Stadium', 'bblm' ),
			array( $this, 'render_meta_box_stad' ),
			'bblm_team',
			'side',
			'low'
		);

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
 * The HTML for the Meta Box to display the race of the Team (change not availible)
 *
 */
 function render_meta_box_race( $post ) {

   wp_nonce_field( basename( __FILE__ ), 'team_info' );
?>
<div class="field">
		<p>
<?php

		$type = get_post_meta( $post->ID, 'team_race', true );
		if ( empty( $type ) ) {

			echo __( 'Unknown', 'bblm' );

		}

		else {

			echo get_the_title( $type );

		}

?>
		</p>
</div>
<?php

 }

 /**
  * The HTML for the Meta Box to display the home stadium (and create a new one if needed)
  *
  */
  function render_meta_box_stad( $post ) {
 ?>
 <div class="field">
 		<p>Stadium (Drop Down)<br>
			ALEX BUTTON TO CREATE A NEW STADIUM IF THE ADMIN HAS FORGOTTN....</p>
 </div>
 <?php

  }

 /**
  * The HTML for the Meta Box to display the Team Information
  *
  */
  function render_meta_box_info( $post ) {
 ?>
 <div class="field">
	 	<p>Administrator View</p>

		<table>
			<tr>
				<td></td>
				<td></td>
			</tr>

			<tr>
				<td><label for="bblm_thcoach">Head Coach</label></td>
				<td><input type="text" name="bblm_thcoach" size="25" value="Unkown" maxlength="25"/></td>
			</tr>

			<tr>
				<td><label for="bblm_trr">Re-Rolls</label></td>
				<td><input type="text" name="bblm_trr" size="2" value="X" maxlength="1" id="bblm_trr">
				@ xxVALUExxgp each - remember that they cost double when bought during a season</td>
			</tr>
			<tr>
				<td><label for="bblm_tff">Fan Factor</label></td>
				  <td><input type="text" name="bblm_tff" size="2" value="X" maxlength="2" id="bblm_tff">
				  @ 10,000 each</td>
			</tr>
			<tr>
				<td><label for="bblm_tcl">Cheerleaders</label></td>
				  <td><input type="text" name="bblm_tcl" size="2" value="X" maxlength="2" id="bblm_tcl">
				  @ 10,000 each</td>
			</tr>
			<tr>
				<td><label for="bblm_tac">Assistant Coaches</label></td>
				  <td><input type="text" name="bblm_tac" size="2" value="X" maxlength="3" id="bblm_tac">
				  @ 10,000 each - SHOUKLD BE A DROP DOWN WITH A YES / NO</td>
			</tr>
			<tr>
				<td><label for="bblm_tapoc">Apothecary</label></td>
				  <td><input type="text" name="bblm_tapoc" size="1" value="X" maxlength="1" id="bblm_tapoc">
				  @ 50,000 each</td>
			</tr>
			<tr>
				<td><label for="bblm_tbank">Bank Value</label></td>
			  <td><input type="text" name="bblm_tbank" size="7" value="XXXXXXX" maxlength="7" id="bblm_tbank">gp</td>
			</tr>

		</table>

		<p>t_id (hidden)</p>
		<p>Owner (Real Player)</p>
		<p>TV</p>
		<p>Short Name</p>
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
