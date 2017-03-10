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
 * @class 		BBLM_Meta_Comp
 * @version		1.0
 * @package		BBowlLeagueMan/Admin/CPT/Meta_Boxes
 * @category	Class
 * @author 		blacksnotling
 */

class BBLM_Meta_Comp {

  /**
	 * Constructor
	 */
	public function __construct() {

    add_action( 'add_meta_boxes', array( $this, 'register_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_meta_boxes' ),  10, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'add_date_picker' ) );

	}

	public function add_date_picker( $hook_suffix ) {
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
			'comp_info',
      __( 'Competition Information', 'bblm' ),
			array( $this, 'render_meta_boxes_info' ),
			'bblm_comp',
			'normal',
			'low'
		);

		add_meta_box(
			'comp_close',
      __( 'End the Competition', 'bblm' ),
			array( $this, 'render_meta_boxes_close' ),
			'bblm_comp',
			'side',
			'low'
		);

	}

/**
 * The HTML for the 'comp_close' Meta Box
 *
 */
 function render_meta_boxes_close( $post ) {

	 $meta = get_post_custom( $post->ID );
	 if ( isset( $meta['comp_complete'][0] ) ) {

		 //The competition is over
		 echo '<p>This Competition is over!</p>';

	 }
	 else {
	 		//Displaya link to close the Competition
?>
	 <p>Close the Competition</p>
<?php
	 }

 }


/**
 * The HTML for the 'comp_info' Meta Box
 *
 */
 function render_meta_boxes_info( $post ) {
	 global $wpdb;

	 $pos = ""; // holds the data from the database for the competition
	 $comp_complete = 1; //used to supress the end date showing if the competition is still active

	 $sql = "SELECT *, UNIX_TIMESTAMP(c_sdate) AS sdate, UNIX_TIMESTAMP(c_edate) AS edate FROM ".$wpdb->prefix."comp where ID = ".$post->ID;
	 if ( $pos = $wpdb->get_results( $sql, 'ARRAY_A' ) ) {
		 //we are editing an existing compeition
	 }
	 else {
		 //ininalise array to hold dummy data as this is a new coompetition
		 $pos = array( array(
  		 'c_sdate' => '0000-00-00 00:00:00',
			 'c_edate' => '0000-00-00 00:00:00',
  		 'c_pround' => '',
  		 'sea_id' => '',
  		 'series_id' => '',
			 'c_showstandings' => '',
  	 	),
  	);

	 }

	 //Checks to see if dates are set. If they are convert them, if not set to defaults
	 if ( ("0000-00-00 00:00:00" == $pos[0][ 'c_edate' ]) ) {

		 $fdate = '0000-00-00';
		 $comp_complete = 0;

	 }
	 else {

	 	$fdate = date("Y-m-d", $pos[0][ 'edate' ]);

	 }
	 if ( ("0000-00-00 00:00:00" == $pos[0][ 'c_sdate' ]) ) {

		 $sdate = '0000-00-00';

	 }
	 else {

		$sdate = date("Y-m-d", $pos[0][ 'sdate' ]);

	 }

   wp_nonce_field( basename( __FILE__ ), 'comp_info' );
?>
<script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery('.custom_date').datepicker({
        dateFormat : 'yy-mm-dd'
        });
    });

</script>
<div class="field">
		<p><label for="comp_sdate">Start Date: </label><br><input type="text" class="custom_date" name="comp_sdate" value="<?php echo $sdate; ?>"/></p>
<?php
 		if ( $comp_complete ) {
			//Shows the end date if the competition is over
?>
		<p><label for="comp_edate">End Date: </label><br><input type="text" class="custom_date" name="comp_edate" value="<?php echo $fdate; ?>"/></p>
<?php
 		}
		else {
			//otherwise hide the end date field so it cannot be "closed" manually
?>
		<input type="hidden" name="comp_edate" value="<?php echo $fdate; ?>"/>
<?php
		}
?>
	<input type="hidden" name="comp_cid" value="<?php if ( isset( $pos[0][ 'c_id' ] ) ) { echo $pos[0][ 'c_id' ]; } else { echo 'x'; } ?>"/>
	<table>
		<tr>
			<td colspan="4"><label for="comp_series">Championship Cup</label></td>
		</tr>
		<tr>
			<td colspan="4">
<?php
			//generate the list of Cups into a dropdown
			$cupargs = array(
					'post_type' => 'bblm_cup',
					'orderby' => 'title',
					'order'   => 'ASC',
					'posts_per_page'=> -1,
				);

				$query = new WP_Query( $cupargs );

				if ( $query->have_posts() ) : ?>
				<select name="comp_series" id="comp_series">
					<?php while ( $query->have_posts() ) : $query->the_post(); ?>
						<option value="<?php the_ID(); ?>"<?php if ( get_the_ID() == $pos[0][ 'series_id' ] ) { echo ' selected="selected"'; } ?>><?php the_title(); ?></option>
					<?php endwhile; wp_reset_postdata();?>
				</select>
			<?php endif; ?>
			</td>
		</tr>
		<tr>
			<td colspan="4">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="4"><label for="comp_sea">Season</label></td>
		</tr>
		<tr>
			<td colspan="4">
<?php
			//generate the list of Seasons into a dropdown
			$seasonargs = array(
					'post_type' => 'bblm_season',
					'orderby' => 'title',
					'order'   => 'DESC',
					'posts_per_page'=> -1,
				);

				$query = new WP_Query( $seasonargs );

				if ( $query->have_posts() ) : ?>
				<select name="comp_sea" id="comp_sea">
					<?php while ( $query->have_posts() ) : $query->the_post(); ?>
						<option value="<?php the_ID(); ?>"<?php if ( get_the_ID() == $pos[0][ 'sea_id' ] ) { echo ' selected="selected"'; } ?>><?php the_title(); ?></option>
					<?php endwhile; wp_reset_postdata();?>
				</select>
			<?php endif; ?>
			</td>
		</tr>
		<tr>
			<td colspan="4">&nbsp;</td>
		</tr>
		<tr>
			<td>Points for a...</td>
			<td><label for="comp_pw">Win</label></td>
			<td><label for="comp_pl">Loss</label></td>
			<td><label for="comp_pd">Draw</label></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="text" name="comp_pw" id="comp_pw" size="3" maxlength="2" value="<?php if ( isset( $pos[0][ 'c_pW' ] ) ) { echo $pos[0][ 'c_pW' ]; } ?>"></td>
			<td><input type="text" name="comp_pl" id="comp_pl" size="3" maxlength="2" value="<?php if ( isset( $pos[0][ 'c_pL' ] ) ) { echo $pos[0][ 'c_pL' ]; } ?>"></td>
			<td><input type="text" name="comp_pd" id="comp_pd" size="3" maxlength="2" value="<?php if ( isset( $pos[0][ 'c_pD' ] ) ) { echo $pos[0][ 'c_pD' ]; } ?>"></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td><label for="comp_ptd">TD</label></td>
			<td><label for="comp_pcas">CAS</label></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td><input type="text" name="comp_ptd" id="comp_ptd" size="3" maxlength="2" value="<?php if ( isset( $pos[0][ 'c_ptd' ] ) ) { echo $pos[0][ 'c_ptd' ]; } ?>"></td>
			<td><input type="text" name="comp_pcas" id="comp_pcas" size="3" maxlength="2" value="<?php if ( isset( $pos[0][ 'c_pcas' ] ) ) { echo $pos[0][ 'c_pcas' ]; } ?>"></td>
		</tr>
		<tr>
			<td colspan="4">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="4"><label for="comp_pround">Round points by # of games played?</label></td>
		</tr>
		<tr>
			<td colspan="4"><select name="comp_pround" id="comp_pround">
				<option value="0"<?php if ( 0 == $pos[0][ 'c_pround' ] ) { print(" selected=\"selected\""); } ?>>No</option>
				<option value="1"<?php if ( 1 == $pos[0][ 'c_pround' ] ) { print(" selected=\"selected\""); } ?>>Yes</option>
			</select></td>
		</tr>
<?php
		if ( isset( $pos[0][ 'c_id' ] ) ) {
			//allows the admin to flip the "show standings flag"
?>
<tr>
	<td colspan="4"><label for="comp_showstandings">Display the League Table / brackets?</label></td>
</tr>
<tr>
	<td colspan="4"><select name="comp_showstandings" id="comp_showstandings">
		<option value="0"<?php if ( 0 == $pos[0][ 'c_showstandings' ] ) { print(" selected=\"selected\""); } ?>>No</option>
		<option value="1"<?php if ( 1 == $pos[0][ 'c_showstandings' ] ) { print(" selected=\"selected\""); } ?>>Yes</option>
	</select></td>
</tr>
<?php
 		}
		else {
			//otherwise hide the option so it can only be changed once the competition is set
?>
		<input type="hidden" name="comp_showstandings" value="0"/>
<?php
		}
?>
	</table>

</div><!-- end of the meta-box -->
<?php

 }

 /**
 	* Action when Saving the post type
 	*
 	*/
 	function save_meta_boxes( $post_id ) {
 		global $post;

 		// Verify nonce
 		if ( !isset( $_POST['comp_info'] ) || !wp_verify_nonce( $_POST['comp_info'], basename(__FILE__) ) ) {
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
		//build up data
		$meta['comp_cid'] = ( isset( $_POST['comp_cid'] ) ? esc_textarea( $_POST['comp_cid'] ) : 'x' );
		$meta['comp_sdate'] = ( isset( $_POST['comp_sdate'] ) ? esc_textarea( $_POST['comp_sdate']. " 17:00:01" ) : '0000-00-00 00:00:00' );
		$meta['comp_edate'] = ( isset( $_POST['comp_edate'] ) ? esc_textarea( $_POST['comp_edate']. " 17:00:01" ) : '0000-00-00 00:00:00' );
		$meta['comp_series'] = ( isset( $_POST['comp_series'] ) ? (int) $_POST['comp_series'] : '1' );
		$meta['comp_sea'] = ( isset( $_POST['comp_sea'] ) ? (int) $_POST['comp_sea'] : '1' );
		$meta['comp_pw'] = ( isset( $_POST['comp_pw'] ) ? (int) $_POST['comp_pw'] : '0' );
		$meta['comp_pl'] = ( isset( $_POST['comp_pl'] ) ? (int) $_POST['comp_pl'] : '0' );
		$meta['comp_pd'] = ( isset( $_POST['comp_pd'] ) ? (int) $_POST['comp_pd'] : '0' );
		$meta['comp_ptd'] = ( isset( $_POST['comp_ptd'] ) ? (int) $_POST['comp_ptd'] : '0' );
		$meta['comp_pcas'] = ( isset( $_POST['comp_pcas'] ) ? (int) $_POST['comp_pcas'] : '0' );
		$meta['comp_pround'] = ( isset( $_POST['comp_pround'] ) ? (int) $_POST['comp_pround'] : 'x' );
		$meta['comp_showstandings'] = ( isset( $_POST['comp_showstandings'] ) ? (int) $_POST['comp_showstandings'] : '0' );

		if ( ! isset( $_POST[ 'comp_cid' ] ) || "x" == $_POST[ 'comp_cid' ] ) {
			//we are dealing with a new competition

			$this->add_competition( $post_id, $meta );

		}
		else {

			$this->update_competition( $post_id, $meta );

		}

 	}

/**
	* Add a new COmpetition to the Database
	*
	*/
	function add_competition( $post_id, $meta ) {
		global $post;
		global $wpdb;

		$bblmdatasql = 'INSERT INTO `'.$wpdb->prefix.'comp` (`c_id`, `ID`, `c_name`, `series_id`, `sea_id`, `ct_id`, `c_active`, `c_counts`, `c_pW`, `c_pL`, `c_pD`, `c_ptd`, `c_pcas`, `c_pround`, `c_sdate`, `c_edate`, `c_showstandings`, `c_show`, `type_id`) VALUES (\'\', \''.$post->ID.'\', \''.get_the_title().'\', \''.$meta['comp_series'].'\', \''.$meta['comp_sea'].'\', \'0\', \'1\', \'1\', \''.$meta['comp_pw'].'\', \''.$meta['comp_pl'].'\', \''.$meta['comp_pd'].'\', \''.$meta['comp_ptd'].'\', \''.$meta['comp_pcas'].'\', \''.$meta['comp_pround'].'\', \''.$meta['comp_sdate'].'\', \'0000-00-00 00:00:00\', \'0\', \'1\', \'1\')';

		$wpdb->query( $bblmdatasql );

	}

/**
	* Add a new Competition to the Database
	*
	*/
	function update_competition( $post_id, $meta ) {
		global $post;
		global $wpdb;

		$updatesql = 'UPDATE `'.$wpdb->prefix.'comp` SET `series_id` = \''.$meta['comp_series'].'\', `sea_id` = \''.$meta['comp_sea'].'\', `c_pW` = \''.$meta['comp_pw'].'\', `c_pL` = \''.$meta['comp_pl'].'\', `c_pD` = \''.$meta['comp_pd'].'\', `c_ptd` = \''.$meta['comp_ptd'].'\', `c_pcas` = \''.$meta['comp_pcas'].'\', `c_pround` = \''.$meta['comp_pround'].'\', `c_sdate` = \''.$meta['comp_sdate'].'\', `c_edate` = \''.$meta['comp_edate'].'\', `c_showstandings` = \''.$meta['comp_showstandings'].'\' WHERE c_id = '.$meta['comp_cid'].';';

	 	$wpdb->query( $updatesql );

 }

}
new BBLM_Meta_Comp();
