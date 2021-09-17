<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Inducement CPT Meta-Boxes
 *
 * THe output and save functions for Meta-Boxes linked to the Inducement CPT
 * For the other admin functions see the admin/post-meta directory
 * For front end functions reloated to the CPT see the includes/post-types directory
 *
 * @class 		BBLM_Meta_INDUCEMENT
 * @version		1.0
 * @package		BBowlLeagueMan/Admin/CPT/Meta_Boxes
 * @category	Class
 * @author 		blacksnotling
 */

class BBLM_Meta_INDUCEMENT {

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
			'player_needed',
      __( 'Player Needed?', 'bblm' ),
			array( $this, 'render_meta_boxes' ),
			'bblm_inducement',
			'side',
			'high'
		);

	}

/**
 * The HTML for the Meta Box(s)
 *
 */
 function render_meta_boxes( $post ) {
	 global $wpdb;

   $meta = get_post_custom( $post->ID );
   $type = ! isset( $meta['player_needed'][0] ) ? '' : $meta['player_needed'][0];
	 $type_pos = ! isset( $meta['player_needed_pos'][0] ) ? '0' : $meta['player_needed_pos'][0];
   wp_nonce_field( basename( __FILE__ ), 'player_needed' );
?>
		<p><?php echo __('Is a player record needed for this Inducement for recording stats or taking part in matches?','bblm'); ?></p>
		<input type="checkbox" id="player_needed_tbox" name="player_needed_tbox" <?php checked( $type, 1); ?>>
		<label for="player_needed_tbox"><?php echo __('Yes, a player record is required','bblm'); ?></label>

		<select name="player_needed_pos" id="player_needed_pos">
			<option value="0"><?php echo __('If needed, please selected a position that represents them','bblm'); ?></option>
<?php
		$poslistsql = 'SELECT * FROM '.$wpdb->prefix.'position WHERE r_id = 0';
		if ( $poslist = $wpdb->get_results( $poslistsql ) ) {
			foreach ( $poslist as $pl ) {
?>
				<option value="<?php echo $pl->pos_id; ?>"<?php selected( $type_pos, $pl->pos_id ) ?>><?php echo $pl->pos_name; ?></option>
<?php
			}
		}
?>
	  </select>

<?php

 }

 /**
 	* Action when Saving the post type
 	*
 	*/
 	function save_meta_boxes( $post_id ) {
 		global $post;
		global $wpdb;

 		// Verify nonce
 		if ( !isset( $_POST['player_needed'] ) || !wp_verify_nonce( $_POST['player_needed'], basename(__FILE__) ) ) {
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
 		$meta['player_needed'] = ( isset( $_POST['player_needed_tbox'] ) ? 1 : 0 );
		$meta['player_needed_pos'] = ( isset( $_POST['player_needed_pos'] ) ? (int) $_POST['player_needed_pos'] : '' );
 		foreach ( $meta as $key => $value ) {
 			update_post_meta( $post->ID, $key, $value );
 		}

		if ( !metadata_exists( 'post', $post->ID, 'iduc_pos_num' ) && $meta['player_needed'] ) {

			//If set, add a player for the new record - check one does not already exist
			$playerargs = array (
				't_id'			=> bblm_get_tbd_team(),
				'pos'				=> $meta['player_needed_pos'],
				'name'			=> get_the_title( $post->ID ),
				'num'				=> 0,
				'ma'				=> 0,
				'st'				=> 0,
				'ag'				=> 0,
				'pa'				=> 0,
				'av'				=> 0,
				'spp'				=> 0,
				'cspp'			=> 0,
				'skills'		=> '',
				'mng'				=> 0,
				'inj'				=> '',
				'cost'			=> 0,
				'costng'		=> 0,
				'status'		=> '1',
				'img'				=> '',
				'former'		=> 0,
				'WPID'			=> $post->ID,
				'legacy'		=> 0,
				'tr'				=> 0,
			);

			$playersql = 'INSERT INTO `'.$wpdb->prefix.'player` (`p_id`, `t_id`, `pos_id`, `p_name`, `p_num`, `p_ma`, `p_st`, `p_ag`, `p_pa`, `p_av`, `p_spp`, `p_cspp`, `p_skills`, `p_mng`, `p_injuries`, `p_cost`, `p_cost_ng`, `p_status`, `p_img`, `p_former`, `WPID`, `p_legacy`, `p_tr`) VALUES (NULL, \''.$playerargs['t_id'].'\', \''.$playerargs['pos'].'\', \''.$playerargs['name'].'\', \''.$playerargs['num'].'\', \''.$playerargs['ma'].'\', \''.$playerargs['st'].'\', \''.$playerargs['ag'].'\', \''.$playerargs['pa'].'\', \''.$playerargs['av'].'\', \''.$playerargs['spp'].'\', \''.$playerargs['cspp'].'\', \''.$playerargs['skills'].'\', \''.$playerargs['mng'].'\', \''.$playerargs['inj'].'\', \''.$playerargs['cost'].'\', \''.$playerargs['costng'].'\', \''.$playerargs['status'].'\', \''.$playerargs['img'].'\', \''.$playerargs['former'].'\', \''.$playerargs['WPID'].'\', \''.$playerargs['legacy'].'\', \''.$playerargs['tr'].'\');';
			//Add a meta flag to match the player record with this inducement

			if ( $wpdb->query( $playersql ) ) {
				update_post_meta( $post->ID, 'iduc_pos_num', $wpdb->insert_id );
				//Finally, add a meta flag so we do not end up with multipole entries
				update_post_meta( $post->ID, 'iduc_pos_set', 1 );
			}
		}//end if meta key does NOT exist
		//If the key does exist, and a player is needed then update
		else if ( metadata_exists( 'post', $post->ID, 'iduc_pos_set' ) && $meta['player_needed'] ) {

			$playernum = get_post_meta( $post->ID, 'iduc_pos_num', true );

			$playerargs = array (
				'pos'				=> $meta['player_needed_pos'],
				'name'			=> get_the_title( $post->ID ),
			);
			$playersql = 'UPDATE '.$wpdb->prefix.'player SET `pos_id` = \''.$playerargs['pos'].'\', `p_name` = \''.$playerargs['name'].'\' WHERE `p_id` = '.$playernum.';';
			$wpdb->query( $playersql );
		}//end of if a player record DOES exist

 	}

}
new BBLM_Meta_INDUCEMENT();
