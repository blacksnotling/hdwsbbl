<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Star Players CPT Meta-Boxes
 *
 * THe output and save functions for Meta-Boxes linked to the Star Players CPT
 * For the other admin functions see the admin/post-meta directory
 * For front end functions reloated to the CPT see the includes/post-types directory
 *
 * @class 		BBLM_Meta_STARS
 * @version		1.0
 * @package		BBowlLeagueMan/Admin/CPT/Meta_Boxes
 * @category	Class
 * @author 		blacksnotling
 */

class BBLM_Meta_STARS {

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
			'star_status',
      __( 'Star Player Status', 'bblm' ),
			array( $this, 'render_meta_box_status' ),
			'bblm_star',
			'side',
			'high'
		);
		add_meta_box(
			'star_srules',
			__( 'Star Player Special Rules', 'bblm' ),
			array( $this, 'render_meta_box_srules' ),
			'bblm_star',
			'normal',
			'high'
		);
		add_meta_box(
			'star_stats',
			__( 'Star Player Statistics', 'bblm' ),
			array( $this, 'render_meta_box_stats' ),
			'bblm_star',
			'normal',
			'low'
		);
		add_meta_box(
			'star_mhistory',
			__( 'Edit Star Player Match History', 'bblm' ),
			array( $this, 'render_meta_box_mhistory' ),
			'bblm_star',
			'normal',
			'low'
		);


	}

/**
 * The HTML for the Star Player Status Meta Box
 *
 */
 function render_meta_box_status( $post ) {
	 global $wpdb;

   $meta = get_post_custom( $post->ID );
   wp_nonce_field( basename( __FILE__ ), 'star_status' );
?>
		<select name="star_sstatusddown" id="star_sstatusddown">
<?php

			$starstatussql = 'SELECT P.p_status FROM '.$wpdb->prefix.'player P WHERE WPID = '.$post->ID;
			if ( $starstatus = $wpdb->get_row( $starstatussql ) ) {
?>
				<option value="1"<?php selected( $starstatus->p_status, 1 ) ?>>Available</option>
				<option value="0"<?php selected( $starstatus->p_status, 0 ) ?>>Legacy / Retired</option>
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
 function render_meta_box_srules( $post ) {
	 global $wpdb;

   $meta = get_post_custom( $post->ID );
	 $srules = ! isset( $meta['star_srules'][0] ) ? '' : $meta['star_srules'][0];
?>
 	<label for="star_srules"><?php echo __('Star Player Special Rules','bblm' ) ?></label><br />
 	<textarea id="star_srules" name="star_srules" rows="3" cols="70" placeholder="<?php echo __('Any special rules for the Star Player can be added here.','bblm' ) ?>"><?php echo esc_textarea( $srules ); ?></textarea>

<?php

} //end of render_meta_box_srules

/**
 * The HTML for the StatisticsMeta Box(s)
 *
 */
 function render_meta_box_stats( $post ) {
	 global $wpdb;

	 $playersql = 'SELECT * FROM '.$wpdb->prefix.'player P WHERE WPID = '.$post->ID;
	 if ( $player = $wpdb->get_row( $playersql ) ) {


?>
<table class="form-table">
	<tr valign="top">
		<td colspan="2">
			<table class="bblm_admin_table">
				<tr>
					<th><label for="bblm_pma">MA</label></th>
					<th><label for="bblm_pst">ST</label></th>
					<th><label for="bblm_pag">AG</label></th>
					<th><label for="bblm_ppa">PA</label></th>
					<th><label for="bblm_pav">AV</label></th>
				</tr>
				<tr>
					<td><input type="text" name="bblm_pma" size="2" value="<?php echo (int) $player->p_ma; ?>" maxlength="1" id="bblm_pma" class="small-text" autocomplete="off" /></td>
					<td><input type="text" name="bblm_pst" size="2" value="<?php echo (int) $player->p_st; ?>" maxlength="1" id="bblm_pst" class="small-text" autocomplete="off" /></td>
					<td><input type="text" name="bblm_pag" size="2" value="<?php echo (int) $player->p_ag; ?>" maxlength="1" id="bblm_pag" class="small-text" autocomplete="off" />+</td>
					<td><input type="text" name="bblm_ppa" size="2" value="<?php echo (int) $player->p_pa; ?>" maxlength="1" id="bblm_ppa" class="small-text" autocomplete="off" />+</td>
					<td><input type="text" name="bblm_pav" size="2" value="<?php echo (int) $player->p_av; ?>" maxlength="2" id="bblm_pav" class="small-text" autocomplete="off" />+</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><label for="bblm_pcost"><?php echo __('Cost Per Match','bblm'); ?></label></th>
		<td><input type="text" name="bblm_pcost" size="10" value="<?php echo (int) $player->p_cost; ?>" maxlength="6" id="bblm_pcost" class="regular-text" autocomplete="off" />gp</td>
	</tr>
	<tr valign="top">
		<th scope="row"><label for="bblm_pskills"><?php echo __('Skills','bblm'); ?></label></th>
		<td><p><textarea rows="5" cols="50" name="bblm_pskills" id="bblm_pskills" class="large-text"><?php echo esc_textarea( $player->p_skills ); ?></textarea></p></td>
	</tr>
</table>
<?php
}// end of if player sql

} //end of render_meta_box_srules

/**
 * The HTML for the Star Special Match History Meta Box(s)
 *
 */
 function render_meta_box_mhistory( $post ) {
	 global $wpdb;

	 $playermatchsql = 'SELECT M.*, X.WPID AS MWPID, P.p_id FROM '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'match X WHERE M.p_id = P.p_id AND M.m_id = X.WPID AND P.WPID = '.$post->ID.' ORDER BY X.m_date DESC';
	 if ( $playermatch = $wpdb->get_results( $playermatchsql ) ) {
		 $count = 1;
?>
		 <table cellspacing="0" class="widefat">
			 <thead>
			 <tr>
				 <th><?php echo __( 'Match','bblm' ); ?></th>
				 <th>TD</th>
				 <th>CAS</th>
				 <th>INT</th>
				 <th><?php echo __( 'Deflection','bblm' ); ?></th>
				 <th>COMP</th>
				 <th>MVP</th>
				 <th><?php echo __( 'Throw Team Mate','bblm' ); ?></th>
				 <th><?php echo __( 'Kick Team Mate','bblm' ); ?></th>
				 <th><?php echo __( 'Eat Team Mate','bblm' ); ?></th>
				 <th><?php echo __( 'Prayers to Nuffle','bblm' ); ?></th>
				 <th><?php echo __( 'Fouls','bblm' ); ?></th>
			 </tr>
			 </thead>
			 </tbody>
<?php
		 foreach ( $playermatch as $pm ) {
			 		 $pid = $pm->p_id;
?>
			 <tr>
<?php
?>
				 <td><?php echo bblm_get_match_link_score( $pm->MWPID, 0 ); ?></td>
				 <td><input type="text" name="bblm_ptd<?php echo $count; ?>" size="3" value="<?php echo $pm->mp_td; ?>" id="bblm_ptd" maxlength="2"></td>
				 <td><input type="text" name="bblm_pcas<?php echo $count; ?>" size="3" value="<?php echo $pm->mp_cas; ?>" id="bblm_pcas" maxlength="2"></td>
				 <td><input type="text" name="bblm_pint<?php echo $count; ?>" size="3" value="<?php echo $pm->mp_int; ?>" id="bblm_pint" maxlength="2"></td>
				 <td><input type="text" name="bblm_pdef<?php echo $count; ?>" size="3" value="<?php echo $pm->mp_def; ?>" id="bblm_pdef" maxlength="2"></td>
				 <td><input type="text" name="bblm_pcomp<?php echo $count; ?>" size="3" value="<?php echo $pm->mp_comp; ?>" id="bblm_pma" maxlength="2"></td>
				 <td><input type="text" name="bblm_pmvp<?php echo $count; ?>" size="3" value="<?php echo $pm->mp_mvp; ?>" id="bblm_pmvp" maxlength="2"></td>
				 <td><input type="text" name="bblm_pttm<?php echo $count; ?>" size="3" value="<?php echo $pm->mp_ttm; ?>" id="bblm_pttm" maxlength="2"></td>
				 <td><input type="text" name="bblm_pktm<?php echo $count; ?>" size="3" value="<?php echo $pm->mp_ktm; ?>" id="bblm_pktm" maxlength="2"></td>
				 <td><input type="text" name="bblm_petm<?php echo $count; ?>" size="3" value="<?php echo $pm->mp_etm; ?>" id="bblm_petm" maxlength="2"></td>
				 <td><input type="text" name="bblm_pptn<?php echo $count; ?>" size="3" value="<?php echo $pm->mp_ptn; ?>" id="bblm_pptn" maxlength="2"></td>
				 <td>
					 <input type="text" name="bblm_pfoul<?php echo $count; ?>" size="3" value="<?php echo $pm->mp_foul; ?>" id="bblm_pfoul" maxlength="2">
					 <input type="hidden" name="bblm_pspp<?php echo $count; ?>" size="3" value="<?php echo $pm->mp_spp; ?>" id="bblm_pspp" maxlength="2">
					 <input type="hidden" name="bblm_pmng<?php echo $count; ?>" size="3" value="<?php echo $pm->mp_mng; ?>" id="bblm_pmng" maxlength="2">
					 <input type="hidden" name="bblm_pinc<?php echo $count; ?>" size="10" value="<?php echo $pm->mp_inc; ?>" id="bblm_pinc">
					 <input type="hidden" name="bblm_pinj<?php echo $count; ?>" size="10" value="<?php echo $pm->mp_inj; ?>" id="bblm_pinj">
					 <input type="hidden" name="bblm_mid<?php echo $count; ?>" size="5" value="<?php echo $pm->MWPID; ?>" id="bblm_mid" maxlength="5">
				 </td>
			 </tr>
<?php
			 $count++;
		 }
		 echo '</tbody>';
		 echo '</table>';
	 }
?>
	 <input type="hidden" name="bblm_pid" size="5" value="<?php echo $pid; ?>" id="bblm_pid" maxlength="5">
	 <input type="hidden" name="bblm_pmcount" size="5" value="<?php echo $count-1; ?>" id="bblm_pmcount" maxlength="3">

<?php

} //end of render_meta_box_mhistory

 /**
 	* Action when Saving the post type
 	*
 	*/
 	function save_meta_boxes( $post_id ) {
 		global $post;
		global $wpdb;

 		// Verify nonce
 		if ( !isset( $_POST['star_status'] ) || !wp_verify_nonce( $_POST['star_status'], basename(__FILE__) ) ) {
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
 		$meta['star_status'] = ( isset( $_POST['star_sstatusddown'] ) ? (int) $_POST['star_sstatusddown'] : '' );
		$meta['star_srules'] = ( isset( $_POST['star_srules'] ) ? esc_textarea( $_POST['star_srules'] ) : '' );
 		foreach ( $meta as $key => $value ) {
 			update_post_meta( $post->ID, $key, $value );
 		}
		//Update the player record for Status
		if ( $meta['star_status'] ) {
			$starstatusupdatesql = 'UPDATE '.$wpdb->prefix.'player SET `p_status` = "1", `p_legacy` = "0" WHERE `WPID` = '.$post->ID;
		}
		else {
			$starstatusupdatesql = 'UPDATE '.$wpdb->prefix.'player SET `p_status` = "0", `p_legacy` = "1" WHERE `WPID` = '.$post->ID;
		}
		$wpdb->query( $starstatusupdatesql );

		//Update player statistics
		$starstatsupdatesql = 'UPDATE '.$wpdb->prefix.'player SET `p_ma` = "'.(int) $_POST['bblm_pma'].'", `p_st` = "'.(int) $_POST['bblm_pst'].'", `p_ag` = "'.(int) $_POST['bblm_pag'].'", `p_pa` = "'.(int) $_POST['bblm_ppa'].'", `p_av` = "'.(int) $_POST['bblm_pav'].'", `p_skills` = "'.esc_textarea( $_POST['bblm_pskills'] ).'", `p_cost` = "'.(int) $_POST['bblm_pcost'].'", `p_cost_ng` = "'.(int) $_POST['bblm_pcost'].'" WHERE `WPID` = '.$post->ID;
		$wpdb->query( $starstatsupdatesql );

		//Update Player match History
		//initialise array to hold sql
		$mhistoryupdatesql = array();

		//Set initil values for loop
		$p = 1;
		$pmax = (int) $_POST['bblm_pmcount'];

		while ($p <= $pmax){

			$playerargs = array (
				'td'				=> (int) $_POST['bblm_ptd'.$p],
				'cas'				=> (int) $_POST['bblm_pcas'.$p],
				'comp'				=> (int) $_POST['bblm_pcomp'.$p],
				'pint'				=> (int) $_POST['bblm_pint'.$p],
				'def'				=> (int) $_POST['bblm_pdef'.$p],
				'mvp'				=> (int) $_POST['bblm_pmvp'.$p],
				'spp'				=> (int) $_POST['bblm_pspp'.$p],
				'mng'				=> (int) $_POST['bblm_pmng'.$p],
				'ing'				=> esc_textarea( $_POST['bblm_pinj'.$p] ),
				'ttm'				=> (int) $_POST['bblm_pttm'.$p],
				'ktm'				=> (int) $_POST['bblm_pktm'.$p],
				'etm'				=> (int) $_POST['bblm_petm'.$p],
				'ptn'				=> (int) $_POST['bblm_pptn'.$p],
				'foul'				=> (int) $_POST['bblm_pfoul'.$p],
				'inc'				=> esc_textarea( $_POST['bblm_pinc'.$p] ),
				'mid'				=> (int) $_POST['bblm_mid'.$p],
				'pid'				=> (int) $_POST['bblm_pid'],
			);

			$mhistoryupdatesql[$p] = 'UPDATE `'.$wpdb->prefix.'match_player` SET `mp_td` = \''.$playerargs['td'].'\', `mp_cas` = \''.$playerargs['cas'].'\', `mp_comp` = \''.$playerargs['comp'].'\', `mp_int` = \''.$playerargs['pint'].'\', `mp_def` = \''.$playerargs['def'].'\', `mp_mvp` = \''.$playerargs['mvp'].'\', `mp_spp` = \''.$playerargs['spp'].'\', `mp_mng` = \''.$playerargs['mng'].'\', `mp_inj` = \''.$playerargs['inj'].'\', `mp_ttm` = \''.$playerargs['ttm'].'\', `mp_ktm` = \''.$playerargs['ktm'].'\', `mp_etm` = \''.$playerargs['etm'].'\', `mp_ptn` = \''.$playerargs['ptn'].'\', `mp_foul` = \''.$playerargs['foul'].'\', `mp_inc` = \''.$playerargs['inc'].'\' WHERE `m_id` = '.$playerargs['mid'].' AND `p_id` = '.$playerargs['pid'].' LIMIT 1';

			//$mhistoryupdatesql[$p] = 'UPDATE `'.$wpdb->prefix.'match_player` SET `mp_td` = \''.$_POST['bblm_ptd'.$p].'\', `mp_cas` = \''.$_POST['bblm_pcas'.$p].'\', `mp_comp` = \''.$_POST['bblm_pcomp'.$p].'\', `mp_int` = \''.$_POST['bblm_pint'.$p].'\', `mp_def` = \''.$_POST['bblm_pdef'.$p].'\', `mp_mvp` = \''.$_POST['bblm_pmvp'.$p].'\', `mp_spp` = \''.$_POST['bblm_pspp'.$p].'\', `mp_mng` = \''.$_POST['bblm_pmng'.$p].'\', `mp_inj` = \''.$_POST['bblm_pinj'.$p].'\', `mp_ttm` = \''.$_POST['bblm_pttm'.$p].'\', `mp_ktm` = \''.$_POST['bblm_pktm'.$p].'\', `mp_etm` = \''.$_POST['bblm_petm'.$p].'\', `mp_ptn` = \''.$_POST['bblm_pptn'.$p].'\', `mp_foul` = \''.$_POST['bblm_pfoul'.$p].'\', `mp_inc` = \''.$_POST['bblm_pinc'.$p].'\' WHERE `m_id` = '.$_POST['bblm_mid'.$p].' AND `p_id` = '.$_POST['bblm_pid'].' LIMIT 1';
			$p++;
		}

		foreach ( $mhistoryupdatesql as $pmh ) {
			$wpdb->query( $pmh );
		}
		bblm_update_player($_POST['bblm_pid'], 1);

 	}

} //end of class BBLM_Meta_STARS
new BBLM_Meta_STARS();
