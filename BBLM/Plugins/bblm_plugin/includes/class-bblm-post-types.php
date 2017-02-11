<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Register Custom Post types
 *
 * Registers post types and taxonomies required for BBowlLeagueMan
 *
 * @class 		BBLM_Post_types
 * @version		1.0
 * @package		BBowlLeagueMan/CPTCore
 * @category	Class
 * @author 		blacksnotling
 */
class BBLM_Post_types {

	/**
	 * Constructor
	 */
	public function __construct() {

		add_action( 'init', array( __CLASS__, 'register_post_types' ), 5 );

	}

	/**
	 * Register core post types
	 */
	public static function register_post_types() {

		register_post_type( 'bblm_dyk',
			array(
				'labels' => array(
					'name' 					=> __( 'Did You Know?', 'bblm' ),
					'singular_name' 		=> __( 'Did You Know?', 'bblm' ),
					'add_new_item' 			=> __( 'Add New Did You Know', 'bblm' ),
					'edit_item' 			=> __( 'Edit Did You Know', 'bblm' ),
					'new_item' 				=> __( 'New', 'bblm' ),
					'view_item' 			=> __( 'View Did You Know', 'bblm' ),
					'view_items' 			=> __( 'View Did You Knows', 'bblm' ),
					'search_items' 			=> __( 'Search', 'bblm' ),
					'not_found' 			=> __( 'No results found.', 'bblm' ),
					'not_found_in_trash' 	=> __( 'No results found.', 'bblm' ),
					'all_items' 			=> __( 'Did You Know?', 'bblm' ),
				),
				'public' 				=> true,
				'show_ui' 				=> true,
				'map_meta_cap' 			=> true,
				'publicly_queryable' 	=> true,
				'exclude_from_search' 	=> true, //exclude from search
				'hierarchical' 			=> false,
				'rewrite' 				=> array( 'slug' => 'did-you-know' ),
				'supports' 				=> array( 'title', 'editor'),
				'has_archive' 			=> true,
				'show_in_nav_menus' 	=> true,
        'show_in_menu' => 'bblm_main_menu',
			)
		); //end of Did You Know
    register_post_type( 'bblm_stadium',
			array(
				'labels' => array(
					'name' 					=> __( 'Stadiums', 'bblm' ),
					'singular_name' 		=> __( 'Stadium', 'bblm' ),
					'add_new_item' 			=> __( 'Add New Stadium', 'bblm' ),
					'edit_item' 			=> __( 'Edit Stadium', 'bblm' ),
					'new_item' 				=> __( 'New', 'bblm' ),
					'view_item' 			=> __( 'View Stadium', 'bblm' ),
					'view_items' 			=> __( 'View Stadiums', 'bblm' ),
					'search_items' 			=> __( 'Search', 'bblm' ),
					'not_found' 			=> __( 'No results found.', 'bblm' ),
					'not_found_in_trash' 	=> __( 'No results found.', 'bblm' ),
					'all_items' 			=> __( 'Stadiums', 'bblm' ),
				),
				'public' 				=> true,
				'show_ui' 				=> true,
				'map_meta_cap' 			=> true,
				'publicly_queryable' 	=> true,
				'exclude_from_search' 	=> false,
				'hierarchical' 			=> false,
				'rewrite' 				=> array( 'slug' => 'stadiums' ),
				'supports' 				=> array( 'title', 'editor', 'author'),
				'has_archive' 			=> true,
				'show_in_nav_menus' 	=> true,
        'menu_icon' 			=> 'dashicons-store',
        'show_in_menu' => 'bblm_main_menu',
			)
		); //end of Stadiums

	}

} //End of Class

new BBLM_Post_types();
