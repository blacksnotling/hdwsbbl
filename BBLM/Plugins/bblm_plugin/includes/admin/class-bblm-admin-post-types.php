<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Import the admin handler files and Meta-Boxes for the defined CPT's
 *
 * @class 		BBLM_Admin_Post_Types
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/Admin
 * @version   1.7
 */

 class BBLM_Admin_Post_Types {

  /**
 	 * Constructor
 	 */
 	public function __construct() {

 		add_action( 'admin_init', array( $this, 'include_post_type_handlers' ) );
		add_action( 'admin_init', array( $this, 'include_post_meta_boxes' ) );
		add_action( 'admin_init', array( $this, 'convert_race_rules_terms_to_integers' ) );
		add_filter( 'post_edit_category_parent_dropdown_args', array( $this, 'hide_race_rules_dropdown_select' ) );

 	}

  /**
	 * Loads all the CPT handler classes for admin screen functions
	 */
	public function include_post_type_handlers() {

    include_once( 'post-types/class-bblm-admin-cpt-dyk.php' );
		include_once( 'post-types/class-bblm-admin-cpt-owner.php' );
		include_once( 'post-types/class-bblm-admin-cpt-stadium.php' );
		include_once( 'post-types/class-bblm-admin-cpt-cup.php' );
		include_once( 'post-types/class-bblm-admin-cpt-season.php' );
		include_once( 'post-types/class-bblm-admin-cpt-comp.php' );
		include_once( 'post-types/class-bblm-admin-cpt-race.php' );
		include_once( 'post-types/class-bblm-admin-cpt-match.php' );
		include_once( 'post-types/class-bblm-admin-cpt-team.php' );
		include_once( 'post-types/class-bblm-admin-cpt-player.php' );
		include_once( 'post-types/class-bblm-admin-cpt-stars.php' );
		include_once( 'post-types/class-bblm-admin-cpt-inducement.php' );

 }

 /**
 	* Loads all the CPT Meta-Boxes for admin screen functions
 	*/
	public function include_post_meta_boxes() {

		include_once( 'post-types/meta-boxes/class-bblm-meta-dyk.php' );
		include_once( 'post-types/meta-boxes/class-bblm-meta-stadium.php' );
		include_once( 'post-types/meta-boxes/class-bblm-meta-cup.php' );
		include_once( 'post-types/meta-boxes/class-bblm-meta-season.php' );
		include_once( 'post-types/meta-boxes/class-bblm-meta-comp.php' );
		include_once( 'post-types/meta-boxes/class-bblm-meta-race.php' );
		include_once( 'post-types/meta-boxes/class-bblm-meta-stars.php' );
		include_once( 'post-types/meta-boxes/class-bblm-meta-inducement.php' );
		include_once( 'post-types/meta-boxes/class-bblm-meta-player.php' );

	}

	 /**
	  * Displays the Race Rules Taxonomy as a selection box rather than a tag cloud
	  * despite it being non-hierarchical
	  */
	  function hide_race_rules_dropdown_select( $args ) {
	 	 if ( 'race_rules' == $args['taxonomy'] ) {
	 		 $args['echo'] = false;
	 	 }
	 	 return $args;
	  } //end of hide_parent_dropdown_select

		/**
 	  * converts the taxonomy back to intigets to save correctly
 	  */
		function convert_race_rules_terms_to_integers() {
			$taxonomy = 'race_rules';
			if ( isset( $_POST['tax_input'][ $taxonomy ] ) && is_array( $_POST['tax_input'][ $taxonomy ] ) ) {
				$terms = $_POST['tax_input'][ $taxonomy ];
				$new_terms = array_map( 'intval', $terms );
				$_POST['tax_input'][ $taxonomy ] = $new_terms;
			}
		}


}

return new BBLM_Admin_Post_Types();
