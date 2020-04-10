<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Races CPT Meta-Boxes
 *
 * THe output and save functions for Meta-Boxes linked to the Races CPT
 * For the other admin functions see the admin/post-meta directory
 * For front end functions reloated to the CPT see the includes/post-types directory
 *
 * @class 		BBLM_Meta_Race
 * @version		1.0
 * @package		BBowlLeagueMan/Admin/CPT/Meta_Boxes
 * @category	Class
 * @author 		blacksnotling
 */

class BBLM_Meta_Race {

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
			'race_rrcost',
      __( 'ReRoll Cost', 'bblm' ),
			array( $this, 'render_meta_boxes_rrcost' ),
			'bblm_race',
			'side',
			'high'
		);
		add_meta_box(
			'race_stars',
			__( 'Star Players available for this Race', 'bblm' ),
			array( $this, 'render_meta_boxes_stars' ),
			'bblm_race',
			'normal',
			'low'
		);

	}

/**
 * The HTML for the RRcost Meta Box
 *
 */
 function render_meta_boxes_rrcost( $post ) {

   $meta = get_post_custom( $post->ID );
   $rrcost = ! isset( $meta['race_rrcost'][0] ) ? '50000' : $meta['race_rrcost'][0];
   wp_nonce_field( basename( __FILE__ ), 'race_rrcost' );
?>
<div class="field">
		<p><input type="text" class="race_rr" name="race_rr" value="<?php echo $rrcost; ?>"/>
		<em class="field-summary summary">GP<br>(no comma)</em></p>
</div>
<?php

 }

 /**
  * The HTML for the Star Players Meta Box(s)
  *
  */
  function render_meta_boxes_stars( $post ) {
		global $wpdb;

		$starssql = 'SELECT X.p_id, X.WPID AS PWPID FROM `'.$wpdb->prefix.'player` X WHERE X.t_id = ' . bblm_get_star_player_team() . ' order by p_name ASC';
		if ( $stars = $wpdb->get_results( $starssql ) ) {
			$p = 1;

			//Grab the stars currently assignedf to the race. If the page is new then a dummy empty array is used

			if ( isset( $post->ID ) ) {
				$starsinracesql = 'SELECT * FROM '.$wpdb->prefix.'race2star WHERE r_id = '.$post->ID;
				$starsinrace = $wpdb->get_results( $starsinracesql);
			}
			else {
				$starsinrace = array();
			}


			echo '<ul>';
			foreach ($stars as $star) {
				echo '<li>';
				echo '<input type="checkbox" name="bblm_plyd' . $p . '"';
				if ( in_array_field( $star->p_id, 'p_id', $starsinrace ) ) {
					echo ' checked';
				}
				echo '> ';
				echo bblm_get_player_name( $star->PWPID );
				echo ' <input type="hidden" name="bblm_spid' . $p . '" id="bblm_spid' . $p . '" value="' . $star->p_id . '">';
				echo '</li>';
				$p++;
			}
			echo '</ul>';
?>
			<input type="hidden" name="bblm_numofplayers" id="bblm_numofplayers" value="<?php echo $p-1; ?>">
<?php
		}

  }

 /**
 	* Action when Saving the post type
 	*
 	*/
 	function save_meta_boxes( $post_id ) {
 		global $post;
		global $wpdb;

 		// Verify nonce
 		if ( !isset( $_POST['race_rrcost'] ) || !wp_verify_nonce( $_POST['race_rrcost'], basename(__FILE__) ) ) {
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
 		$meta['race_rrcost'] = ( isset( $_POST['race_rr'] ) ? esc_textarea( $_POST['race_rr'] ) : '' );
 		foreach ( $meta as $key => $value ) {
 			update_post_meta( $post->ID, $key, $value );
 		}
		//Now we populate the race2star table in the database

		//First we delete eveything for this race
		$deletestarsql = 'DELETE FROM `'.$wpdb->prefix.'race2star` WHERE `r_id` = ' . $post->ID;
		$wpdb->get_row( $deletestarsql );
		
		$p = 1;
		$race2starsqla = array();
		while ($p <= $_POST['bblm_numofplayers']){
			//if  "on" result for a field then generate SQL
			if (on == $_POST[ 'bblm_plyd'.$p]) {

				$insertstarracesql = 'INSERT INTO `'.$wpdb->prefix.'race2star` (`r_id`, `p_id`) VALUES (\'' . $post->ID . '\', \''.$_POST['bblm_spid'.$p].'\')';
				$race2starsqla[$p] = $insertstarracesql;
				echo '<p>'.$insertstarracesql.'</p>';
			}
			$p++;
		}

		foreach ($race2starsqla as $ps) {
				$addstar2race = $wpdb->query($ps);
		}

 	}

}
new BBLM_Meta_Race();
