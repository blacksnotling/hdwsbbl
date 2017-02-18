<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Championship Seasons CPT Meta-Boxes
 *
 * THe output and save functions for Meta-Boxes linked to the Seasons CPT
 * For the other admin functions see the admin/post-meta directory
 * For front end functions reloated to the CPT see the includes/post-types directory
 *
 * @class 		BBLM_Meta_Season
 * @version		1.0
 * @package		BBowlLeagueMan/Admin/CPT/Meta_Boxes
 * @category	Class
 * @author 		blacksnotling
 */

class BBLM_Meta_Season {

  /**
	 * Constructor
	 */
	public function __construct() {

    add_action( 'add_meta_boxes', array( $this, 'register_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_meta_boxes' ),  10, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'add_stadium_date_picker' ) );

	}

	public function add_stadium_date_picker( $hook_suffix ) {
		$cpt = 'bblm_season';

		if( in_array( $hook_suffix, array('post.php', 'post-new.php') ) ) {
			$screen = get_current_screen();

			if( is_object( $screen ) && $cpt == $screen->post_type ){

				//jQuery UI date picker file
				wp_enqueue_script('jquery-ui-datepicker');

				//jQuery UI theme css file
				wp_enqueue_style('e2b-admin-ui-css','http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.0/themes/base/jquery-ui.css',false,"1.9.0",false);

			}

		}


	}


  /**
	 * Register the metaboxes to be used for the post type
	 *
	 */
	public function register_meta_boxes() {

		add_meta_box(
			'season_dates',
      __( 'Season Dates', 'bblm' ),
			array( $this, 'render_meta_boxes' ),
			'bblm_season',
			'side',
			'low'
		);

	}

/**
 * The HTML for the Meta Box(s)
 *
 */
 function render_meta_boxes( $post ) {

   $meta = get_post_custom( $post->ID );
   $sdate = ! isset( $meta['season_sdate'][0] ) ? '0000-00-00' : $meta['season_sdate'][0];
	 $fdate = ! isset( $meta['season_fdate'][0] ) ? '0000-00-00' : $meta['season_fdate'][0];
   wp_nonce_field( basename( __FILE__ ), 'season_dates' );
?>
<script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery('.custom_date').datepicker({
        dateFormat : 'yy-mm-dd'
        });
    });

</script>
<div class="field">
		<p><label for="season_sdate">Start Date: </label><br><input type="text" class="custom_date" name="season_sdate" value="<?php echo $sdate; ?>"/></p>
		<p><label for="season_fdate">End Date: </label><br><input type="text" class="custom_date" name="season_fdate" value="<?php echo $fdate; ?>"/></p>
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
 		if ( !isset( $_POST['season_dates'] ) || !wp_verify_nonce( $_POST['season_dates'], basename(__FILE__) ) ) {
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
new BBLM_Meta_Season();
