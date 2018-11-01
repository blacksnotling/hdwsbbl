<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Did You Know (DYK) CPT Meta-Boxes
 *
 * THe output and save functions for Meta-Boxes linked to the DYK CPT
 * For the other admin functions see the admin/post-meta directory
 * For front end functions reloated to the CPT see the includes/post-types directory
 *
 * @class 		BBLM_Meta_DYK
 * @version		1.0
 * @package		BBowlLeagueMan/Admin/CPT/Meta_Boxes
 * @category	Class
 * @author 		blacksnotling
 */

class BBLM_Meta_DYK {

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
			'dyk_type',
      __( 'Type of Did You Know', 'bblm' ),
			array( $this, 'render_meta_boxes' ),
			'bblm_dyk',
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
   $type = ! isset( $meta['dyk_type'][0] ) ? '' : $meta['dyk_type'][0];
   wp_nonce_field( basename( __FILE__ ), 'dyk_type' );
?>
    <select name="dyk_typeddown" id="dyk_typeddown">
      <option value="Trivia"<?php if ("Trivia" == $type) { print(" selected=\"selected\""); } ?>>Trivia</option>
      <option value="Fact"<?php if ("Fact" == $type) { print(" selected=\"selected\""); } ?>>Fact</option>
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
 		if ( !isset( $_POST['dyk_type'] ) || !wp_verify_nonce( $_POST['dyk_type'], basename(__FILE__) ) ) {
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
 		$meta['dyk_type'] = ( isset( $_POST['dyk_typeddown'] ) ? esc_textarea( $_POST['dyk_typeddown'] ) : '' );
 		foreach ( $meta as $key => $value ) {
 			update_post_meta( $post->ID, $key, $value );
 		}

 	}

}
new BBLM_Meta_DYK();
