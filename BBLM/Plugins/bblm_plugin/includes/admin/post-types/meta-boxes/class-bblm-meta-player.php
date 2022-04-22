<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Players CPT Meta-Boxes
 *
 * THe output and save functions for Meta-Boxes linked to the Players CPT
 * For the other admin functions see the admin/post-meta directory
 * For front end functions reloated to the CPT see the includes/post-types directory
 *
 * @class 		BBLM_Meta_PlAYER
 * @version		1.0
 * @package		BBowlLeagueMan/Admin/CPT/Meta_Boxes
 * @category	Class
 * @author 		blacksnotling
 */

class BBLM_Meta_PLAYER {

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
			'player_status',
      __( 'Player Status', 'bblm' ),
			array( $this, 'render_meta_box_status' ),
			'bblm_player',
			'side',
			'high'
		);
		add_meta_box(
			'player_increases',
			__( 'Player Increases and Injuries', 'bblm' ),
			array( $this, 'render_meta_box_increase' ),
			'bblm_player',
			'normal',
			'high'
		);


	}

/**
 * The HTML for the Star Player Status Meta Box
 *
 */
 function render_meta_box_status( $post ) {
	 global $wpdb;

   $meta = get_post_custom( $post->ID );
   wp_nonce_field( basename( __FILE__ ), 'player_status' );
?>
		<select name="player_sstatusddown" id="player_sstatusddown">
<?php

			$starstatussql = 'SELECT P.p_status FROM '.$wpdb->prefix.'player P WHERE WPID = '.$post->ID;
			if ( $starstatus = $wpdb->get_row( $starstatussql ) ) {
?>
				<option value="1"<?php selected( $starstatus->p_status, 1 ) ?>>Available</option>
				<option value="0"<?php selected( $starstatus->p_status, 0 ) ?>>Legacy / Retired / Fired / Dead</option>
<?php
	 	 }//end of if sql works
?>
	 </select>

<?php

} //end of render_meta_box_status

/**
 * The HTML for the Star Special Rules Meta Box(s)
 *
 */
 function render_meta_box_increase( $post ) {
	 global $wpdb;

   $meta = get_post_custom( $post->ID );
	 $srules = ! isset( $meta['star_srules'][0] ) ? '' : $meta['star_srules'][0];
?>
	<form method="post" id="player_add_skill" name="player_add_skill">
		<h3><?php echo __( 'Add Skills', 'bblm' ); ?></h3>
<?php
		BBLM_Admin_CPT_Player::display_skill_selection_form( $post->ID );
?>

		<h3><?php echo __( 'Add Injuries', 'bblm' ); ?></h3>

<?php
		BBLM_Admin_CPT_Player::display_injury_selection_form( $post->ID );
?>

<?php wp_nonce_field( basename( __FILE__ ), 'bblm_playertest_submit' ); ?>

		<input type="hidden" name="bblm_player_id" id="bblm_player_id" value="<?php echo $post->ID; ?>">
		<p class="submit">
			<input type="submit" name="bblm_player_addincrease" id="bblm_player_addincrease" value="Add Skils and Injuries" class="button button-primary" />
		</p>
	</form>

<?php

} //end of render_meta_box_increase


 /**
 	* Action when Saving the post type
 	*
 	*/
 	function save_meta_boxes( $post_id ) {
 		global $post;
		global $wpdb;

 		// Verify nonce
 		if ( !isset( $_POST['player_status'] ) || !wp_verify_nonce( $_POST['player_status'], basename(__FILE__) ) ) {
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
 		$meta['player_status'] = ( isset( $_POST['player_sstatusddown'] ) ? (int) $_POST['Player_sstatusddown'] : '' );
 		foreach ( $meta as $key => $value ) {
 			update_post_meta( $post->ID, $key, $value );
 		}
		//Update the player record for Status
		if ( $meta['player_status'] ) {
			$starstatusupdatesql = 'UPDATE '.$wpdb->prefix.'player SET `p_status` = "1", `p_legacy` = "0" WHERE `WPID` = '.$post->ID;
		}
		else {
			$starstatusupdatesql = 'UPDATE '.$wpdb->prefix.'player SET `p_status` = "0", `p_legacy` = "1" WHERE `WPID` = '.$post->ID;
		}
		$wpdb->query( $starstatusupdatesql );

		//Add Skills and Injuries
		//Increases have been submitted
		$increase = false;
		$injury = false;
		$incdetails = array();
		$injdetails = array();

		if ( ( isset( $_POST[ 'bblm_player_test_addincrease' ] ) ) && ( wp_verify_nonce( $_POST[ 'bblm_playertest_submit' ], basename(__FILE__) ) ) ) {
			//Check that something has been submitted
			if ( "X" != $_POST[ 'bblm_sselect_s1' ] ) {
				$increase = true;
				$incdetails = array(
					'player' => (int) $_POST[ 'bblm_player_id' ],
					'match' => (int) $_POST[ 'bblm_mselect_s1' ],
					'skill' => (int) $_POST[ 'bblm_sselect_s1' ],
					'incdetails' => (int) $_POST[ 'bblm_tselect_s1' ]
				);
			}
			if ( "X" != $_POST[ 'bblm_sselect_i1' ] ) {
				$injury = true;
				$injdetails = array(
					'player' => (int) $_POST[ 'bblm_player_id' ],
					'match' => (int) $_POST[ 'bblm_mselect_i1' ],
					'injury' => (int) $_POST[ 'bblm_sselect_i1' ],
				);
			}

			//Generate SQL
			//Injuries are done first to make sure current TV is captured correctly
			if ( $injury ) {

				if ( BBLM_Admin_CPT_Player::player_add_injury( $injdetails['player'], $injdetails ) ) {
					echo '<div id="updated" class="notice notice-success inline">';
					echo '<p>' . __( 'Changes have been captured.','bblm' ) . '</p>';
					echo '</div>';
				}
				else {
					echo '<div id="updated" class="notice notice-error inline">';
					echo '<p>' . __( 'Something went wrong! Please try again.','bblm' ) . '</p>';
					echo '</div>';
				}
			} //end of injury
			if ( $increase ) {
				if ( BBLM_Admin_CPT_Player::player_add_skill( $incdetails['player'], $incdetails) ) {
					echo '<div id="updated" class="notice notice-success inline">';
					echo '<p>' . __( 'Changes have been captured.','bblm' ) . '</p>';
					echo '</div>';
				}
				else {
					echo '<div id="updated" class="notice notice-error inline">';
					echo '<p>' . __( 'Something went wrong! Please try again.','bblm' ) . '</p>';
					echo '</div>';
				}
			}
		}// end of form submission


 	} //end of save_meta_boxes

} //end of class BBLM_Meta_STARS
new BBLM_Meta_STARS();
