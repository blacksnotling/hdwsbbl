<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Stadiums CPT Meta-Boxes
 *
 * THe output and save functions for Meta-Boxes linked to the Stadiums CPT
 * For the other admin functions see the admin/post-meta directory
 * For front end functions reloated to the CPT see the includes/post-types directory
 *
 * @class 		BBLM_Meta_STADIUM
 * @version		1.0
 * @package		BBowlLeagueMan/Admin/CPT/Meta_Boxes
 * @category	Class
 * @author 		blacksnotling
 */

class BBLM_Meta_STADIUM {

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
			'stadium_featured',
      __( 'Featured Stadium?', 'bblm' ),
			array( $this, 'render_meta_boxes' ),
			'bblm_stadium',
			'side',
			'high'
		);

	}

/**
 * The HTML for the Meta Box(s)
 *
 */
 function render_meta_boxes( $post ) {

   $meta = get_post_custom( $post->ID );
   $type = ! isset( $meta['stadium_featured'][0] ) ? '' : $meta['stadium_featured'][0];
   wp_nonce_field( basename( __FILE__ ), 'stadium_featured' );
?>
    <select name="stadium_featured_ddown" id="stadium_featured_ddown">
      <option value="No"<?php if ("No" == $type) { print(" selected=\"selected\""); } ?>>No</option>
      <option value="Yes"<?php if ("Yes" == $type) { print(" selected=\"selected\""); } ?>>Yes</option>
    </select>

<?php

 }

 /**
 	* Action when Saving the post type
 	*
 	*/
 	function save_meta_boxes( $post_id ) {
 		global $post;

 		// Verify nonce
 		if ( !isset( $_POST['stadium_featured'] ) || !wp_verify_nonce( $_POST['stadium_featured'], basename(__FILE__) ) ) {
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
 		$meta['stadium_featured'] = ( isset( $_POST['stadium_featured_ddown'] ) ? esc_textarea( $_POST['stadium_featured_ddown'] ) : '' );
 		foreach ( $meta as $key => $value ) {
 			update_post_meta( $post->ID, $key, $value );
 		}

 	}

}
new BBLM_Meta_STADIUM();
