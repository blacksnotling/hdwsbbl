<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Competitions CPT Meta-Boxes
 *
 * THe output and save functions for Meta-Boxes linked to the Competitions CPT
 * For the other admin functions see the admin/post-meta directory
 * For front end functions reloated to the CPT see the includes/post-types directory
 *
 * @class 		BBLM_Meta_COMPETITION
 * @version		1.0
 * @package		BBowlLeagueMan/Admin/CPT/Meta_Boxes
 * @category	Class
 * @author 		blacksnotling
 */

class BBLM_Meta_COMPETITION {

  /**
	 * Constructor
	 */
	public function __construct() {

    add_action( 'add_meta_boxes', array( $this, 'register_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_meta_boxes' ),  10, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'add_competition_date_picker' ) );

	}

	public function add_competition_date_picker( $hook_suffix ) {
		$cpt = 'bblm_comp';

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
			'comp_cup',
      __( 'Championship Information', 'bblm' ),
			array( $this, 'render_meta_boxes' ),
			'bblm_comp',
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
	 $comp_season = ! isset( $meta['comp_season'][0] ) ? '0' : $meta['comp_season'][0];
	 $comp_cup = ! isset( $meta['comp_cup'][0] ) ? '0' : $meta['comp_cup'][0];
	 $comp_format = ! isset( $meta['comp_format'][0] ) ? '0' : $meta['comp_format'][0];
   $sdate = ! isset( $meta['comp_sdate'][0] ) ? '0000-00-00' : $meta['comp_sdate'][0];
	 $fdate = ! isset( $meta['comp_fdate'][0] ) ? '0000-00-00' : $meta['comp_fdate'][0];
	 $comp_counts = ! isset( $meta['comp_counts'][0] ) ? '1' : $meta['comp_counts'][0];
	 $comp_pw = ! isset( $meta['comp_pw'][0] ) ? '0' : $meta['comp_pw'][0];
	 $comp_pl = ! isset( $meta['comp_pl'][0] ) ? '0' : $meta['comp_pl'][0];
	 $comp_pd = ! isset( $meta['comp_pd'][0] ) ? '0' : $meta['comp_pd'][0];
	 $comp_ptd = ! isset( $meta['comp_ptd'][0] ) ? '0' : $meta['comp_ptd'][0];
	 $comp_pcas = ! isset( $meta['comp_pcas'][0] ) ? '0' : $meta['comp_pcas'][0];
	 $comp_pround = ! isset( $meta['comp_pround'][0] ) ? '0' : $meta['comp_pround'][0];
   wp_nonce_field( basename( __FILE__ ), 'competition_info' );
?>
<script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery('.custom_date').datepicker({
        dateFormat : 'yy-mm-dd'
        });
    });

</script>
<div class="field">
		<p><label for="comp_season">Season:</label><br>
			<select name="comp_season" id="comp_season">
				<?php
						//Grabs a list of 'posts' from the bblm_cup CPT
						$oposts = get_posts(
							array(
								'post_type' => 'bblm_season',
								'numberposts' => -1,
								'orderby' => 'post_title',
								'order' => 'DESC'
							)
						);
						if( ! $oposts ) return;
						foreach( $oposts as $o ) {
							echo '<option value="' . $o->ID . '"';
							if ( $o->ID == $comp_season ) {
								echo ' selected="selected"';
							}
							echo '>' . bblm_get_season_name( $o->ID ) . '</option>';
						}

				?></select></p>
</div>
<div class="field">
		<p><label for="comp_cup">Championsip Cup:</label><br>
			<select name="comp_cup" id="comp_cup">
			<?php
					//Grabs a list of 'posts' from the bblm_cup CPT
					$oposts = get_posts(
						array(
							'post_type' => 'bblm_cup',
							'numberposts' => -1,
							'orderby' => 'post_title',
							'order' => 'ASC'
						)
					);
					if( ! $oposts ) return;
					foreach( $oposts as $o ) {
						echo '<option value="' . $o->ID . '"';
						if ( $o->ID == $comp_cup ) {
							echo ' selected="selected"';
						}
						echo '>' . bblm_get_cup_name( $o->ID ) . '</option>';
					}

			?></select></p>
</div>
<div class="field">
		<p><label for="comp_format">Format:</label><br>
			<select name="comp_format" id="comp_format">
	<?php
	$typesql = 'SELECT ct_id, ct_name FROM '.$wpdb->prefix.'comp_type ORDER BY ct_name';

	if ( $types = $wpdb->get_results( $typesql ) ) {
		foreach ( $types as $type ) {
			echo '<option value="' . $type->ct_id . '"';
			if ( $type->ct_id == $comp_format ) {
				echo ' selected="selected"';
			}
			echo '>' . $type->ct_name . '</option>';
		}
	}
	?>
	</select></p>
</div>
<div class="field">
		<p><label for="comp_sdate">Start Date: </label><br>
			<input type="text" class="custom_date" name="comp_sdate" value="<?php echo $sdate; ?>"/></p>
		<p><label for="comp_fdate">End Date: </label><br>
			<input type="text" class="custom_date" name="comp_fdate" value="<?php echo $fdate; ?>"/></p>
</div>
<div class="field">
		<p><label for="comp_counts">Does the competition count for stastics?:</label><br>
			<select name="comp_counts" id="comp_counts">
				<option value="1"<?php if ( 1 == $comp_counts) { echo ' selected="selected"'; } ?>>Yes - Games played count towards statistics</option>
				<option value="0"<?php if ( 0 == $comp_counts) { echo ' selected="selected"'; } ?>>No - This is for exhibition / friendly games</option>
			</select></p>
</div>
<div class="field">
		<p><label for="comp_pw">Points for a Win:</label><br>
			<input type="text" name="comp_pw" class="small-text" size="2" maxlength="2" class="regular-text" value="<?php echo $comp_pw; ?>"/></p>
		<p><label for="comp_pl">Points for a Loss:</label><br>
			<input type="text" name="comp_pl" class="small-text" size="2" maxlength="2" class="regular-text" value="<?php echo $comp_pl; ?>"/></p>
		<p><label for="comp_pd">Points for a Draw:</label><br>
			<input type="text" name="comp_pd" class="small-text" size="2" maxlength="2" class="regular-text" value="<?php echo $comp_pd; ?>"/></p>
		<p><label for="comp_ptd">Points for a TD:</label><br>
			<input type="text" name="comp_ptd" class="small-text" size="2" maxlength="2" class="regular-text" value="<?php echo $comp_ptd; ?>"/></p>
		<p><label for="comp_pcas">Points for a CAS:</label><br>
			<input type="text" name="comp_pcas" class="small-text" size="2" maxlength="2" class="regular-text" value="<?php echo $comp_pcas; ?>"/></p>
		<p><label for="comp_pround">Round points by number of games played?</label><br>
			<select name="comp_pround" id="comp_pround">
				<option value="0"<?php if ( 0 == $comp_pround) { echo ' selected="selected"'; } ?>>No</option>
				<option value="1"<?php if ( 1 == $comp_pround) { echo ' selected="selected"'; } ?>>Yes</option>
			</select></p>
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
 		if ( !isset( $_POST['competition_info'] ) || !wp_verify_nonce( $_POST['competition_info'], basename(__FILE__) ) ) {
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
 		$meta['comp_sdate'] = ( isset( $_POST['comp_sdate'] ) ? esc_textarea( $_POST['comp_sdate'] ) : '' );
		$meta['comp_fdate'] = ( isset( $_POST['comp_fdate'] ) ? esc_textarea( $_POST['comp_fdate'] ) : '' );
		$meta['comp_season'] = ( isset( $_POST['comp_season'] ) ? esc_textarea( $_POST['comp_season'] ) : '' );
		$meta['comp_cup'] = ( isset( $_POST['comp_cup'] ) ? esc_textarea( $_POST['comp_cup'] ) : '' );
		$meta['comp_format'] = ( isset( $_POST['comp_format'] ) ? esc_textarea( $_POST['comp_format'] ) : '' );
		$meta['comp_counts'] = ( isset( $_POST['comp_counts'] ) ? esc_textarea( $_POST['comp_counts'] ) : '' );
		$meta['comp_pw'] = ( isset( $_POST['comp_pw'] ) ? esc_textarea( $_POST['comp_pw'] ) : '' );
		$meta['comp_pl'] = ( isset( $_POST['comp_pl'] ) ? esc_textarea( $_POST['comp_pl'] ) : '' );
		$meta['comp_pd'] = ( isset( $_POST['comp_pd'] ) ? esc_textarea( $_POST['comp_pd'] ) : '' );
		$meta['comp_ptd'] = ( isset( $_POST['comp_ptd'] ) ? esc_textarea( $_POST['comp_ptd'] ) : '' );
		$meta['comp_pcas'] = ( isset( $_POST['comp_pcas'] ) ? esc_textarea( $_POST['comp_pcas'] ) : '' );
		$meta['comp_pround'] = ( isset( $_POST['comp_pround'] ) ? esc_textarea( $_POST['comp_pround'] ) : '' );
 		foreach ( $meta as $key => $value ) {
 			update_post_meta( $post->ID, $key, $value );
 		}

 	}

}
new BBLM_Meta_COMPETITION();
