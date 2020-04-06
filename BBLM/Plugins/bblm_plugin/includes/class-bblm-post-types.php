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
 * @version		1.5
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
    add_action( 'init', array( __CLASS__, 'register_taxonomies' ), 10 );
    add_action( 'init', array( $this, 'include_post_type_handlers' ) );

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
					'add_new' 			=> __( 'New Did You Know', 'bblm' ),
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
		register_post_type( 'bblm_owner',
			array(
				'labels' => array(
					'name' 					=> __( 'Owners', 'bblm' ),
					'singular_name' 		=> __( 'Owner', 'bblm' ),
					'add_new_item' 			=> __( 'Add New Owner', 'bblm' ),
					'add_new' 			=> __( 'New Owner', 'bblm' ),
					'edit_item' 			=> __( 'Edit Owner', 'bblm' ),
					'new_item' 				=> __( 'New', 'bblm' ),
					'view_item' 			=> __( 'View Owner', 'bblm' ),
					'view_items' 			=> __( 'View Owners', 'bblm' ),
					'search_items' 			=> __( 'Search', 'bblm' ),
					'not_found' 			=> __( 'No results found.', 'bblm' ),
					'not_found_in_trash' 	=> __( 'No results found.', 'bblm' ),
					'all_items' 			=> __( 'Owners', 'bblm' ),
				),
				'public' 				=> true,
				'show_ui' 				=> true,
				'map_meta_cap' 			=> true,
				'publicly_queryable' 	=> true,
				'exclude_from_search' 	=> true, //exclude from search
				'hierarchical' 			=> false,
				'rewrite' 				=> array( 'slug' => 'owners' ),
				'supports' 				=> array( 'title'),
				'has_archive' 			=> true,
				'show_in_nav_menus' 	=> true,
				'show_in_menu' => 'bblm_main_menu',
			)
		); //end of owner
		register_post_type( 'bblm_transfer',
			array(
				'labels' => array(
					'name' 					=> __( 'Transfers', 'bblm' ),
					'singular_name' 		=> __( 'Transfer', 'bblm' ),
					'add_new_item' 			=> __( 'New Transfer', 'bblm' ),
					'add_new' 			=> __( 'New Transfer', 'bblm' ),
					'edit_item' 			=> __( 'Edit Transfer', 'bblm' ),
					'new_item' 				=> __( 'New Transfer', 'bblm' ),
					'view_item' 			=> __( 'View Transfer', 'bblm' ),
					'view_items' 			=> __( 'View Transfers', 'bblm' ),
					'search_items' 			=> __( 'Search', 'bblm' ),
					'not_found' 			=> __( 'No Transfers found.', 'bblm' ),
					'not_found_in_trash' 	=> __( 'No Transfers found.', 'bblm' ),
					'all_items' 			=> __( 'Transfers', 'bblm' ),
				),
				'public' 				=> true,
				'description' 				=> 'true',
				'show_ui' 				=> false, //hide from the admin side
				'map_meta_cap' 			=> true,
				'publicly_queryable' 	=> true,
				'exclude_from_search' 	=> true, //exclude from search
				'hierarchical' 			=> false,
				'rewrite' 				=> array( 'slug' => 'transfers' ),
				'supports' 				=> array( 'title', 'editor'),
				'has_archive' 			=> true,
				'show_in_nav_menus' 	=> true,
				'show_in_menu' => 'bblm_main_menu',
			)
		); //end of Transfer
		register_post_type( 'bblm_stadium',
			array(
				'labels' => array(
					'name' 					=> __( 'Stadiums', 'bblm' ),
					'singular_name' 		=> __( 'Stadium', 'bblm' ),
					'add_new_item' 			=> __( 'Add New Stadium', 'bblm' ),
					'add_new' 			=> __( 'New Stadium', 'bblm' ),
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
				'exclude_from_search' 	=> false, //exclude from search
				'hierarchical' 			=> false,
				'rewrite' 				=> array( 'slug' => 'stadiums' ),
				'supports' 				=> array( 'title', 'editor', 'author', 'thumbnail' ),
				'has_archive' 			=> true,
				'show_in_nav_menus' 	=> true,
				'show_in_menu' => 'bblm_main_menu',
				'menu_icon' 			=> 'dashicons-store',
			)
		); //end of bblm_stadium
		register_post_type( 'bblm_cup',
			array(
				'labels' => array(
					'name' 					=> __( 'Championship Cups', 'bblm' ),
					'singular_name' 		=> __( 'Championship', 'bblm' ),
					'add_new_item' 			=> __( 'Add New Champsionship Cup', 'bblm' ),
					'add_new' 			=> __( 'New Championship', 'bblm' ),
					'edit_item' 			=> __( 'Edit Championship', 'bblm' ),
					'new_item' 				=> __( 'New', 'bblm' ),
					'view_item' 			=> __( 'View Championship Cup', 'bblm' ),
					'view_items' 			=> __( 'View Championship Cup', 'bblm' ),
					'search_items' 			=> __( 'Search', 'bblm' ),
					'not_found' 			=> __( 'No results found.', 'bblm' ),
					'not_found_in_trash' 	=> __( 'No results found.', 'bblm' ),
					'all_items' 			=> __( 'Championship Cups', 'bblm' ),
				),
				'public' 				=> true,
				'show_ui' 				=> true,
				'map_meta_cap' 			=> true,
				'publicly_queryable' 	=> true,
				'exclude_from_search' 	=> false, //don't exclude from search
				'hierarchical' 			=> false,
				'rewrite' 				=> array( 'slug' => 'cups' ),
				'supports' 				=> array( 'title', 'editor', 'author', 'thumbnail' ),
				'has_archive' 			=> true,
				'show_in_nav_menus' 	=> true,
				'show_in_menu' => 'bblm_main_menu',
				'menu_icon' 			=> 'dashicons-shield',
			)
		); //end of bblm_cup
		register_post_type( 'bblm_season',
			array(
				'labels' => array(
					'name' 					=> __( 'Seasons', 'bblm' ),
					'singular_name' 		=> __( 'Season', 'bblm' ),
					'add_new_item' 			=> __( 'Start a New Season', 'bblm' ),
					'add_new' 			=> __( 'New Season', 'bblm' ),
					'edit_item' 			=> __( 'Edit Season', 'bblm' ),
					'new_item' 				=> __( 'New', 'bblm' ),
					'view_item' 			=> __( 'View Season', 'bblm' ),
					'view_items' 			=> __( 'View Seasons', 'bblm' ),
					'search_items' 			=> __( 'Search', 'bblm' ),
					'not_found' 			=> __( 'No results found.', 'bblm' ),
					'not_found_in_trash' 	=> __( 'No results found.', 'bblm' ),
					'all_items' 			=> __( 'Seasons', 'bblm' ),
				),
				'public' 				=> true,
				'show_ui' 				=> true,
				'map_meta_cap' 			=> true,
				'publicly_queryable' 	=> true,
				'exclude_from_search' 	=> false, //don't exclude from search
				'hierarchical' 			=> false,
				'rewrite' 				=> array( 'slug' => 'season' ),
				'supports' 				=> array( 'title', 'editor', 'author' ),
				'has_archive' 			=> true,
				'show_in_nav_menus' 	=> true,
				'show_in_menu' => 'bblm_main_menu',
				'menu_icon' 			=> 'dashicons-calendar',
			)
		); //end of bblm_season
		register_post_type( 'bblm_comp',
			array(
				'labels' => array(
					'name' 					=> __( 'Competitions', 'bblm' ),
					'singular_name' 		=> __( 'Competition', 'bblm' ),
					'add_new_item' 			=> __( 'Start a New Competition', 'bblm' ),
					'add_new' 			=> __( 'New Competition', 'bblm' ),
					'edit_item' 			=> __( 'Edit Competition', 'bblm' ),
					'new_item' 				=> __( 'New', 'bblm' ),
					'view_item' 			=> __( 'View Competition', 'bblm' ),
					'view_items' 			=> __( 'View Competition', 'bblm' ),
					'search_items' 			=> __( 'Search', 'bblm' ),
					'not_found' 			=> __( 'No results found.', 'bblm' ),
					'not_found_in_trash' 	=> __( 'No results found.', 'bblm' ),
					'all_items' 			=> __( 'Competitions', 'bblm' ),
				),
				'public' 				=> true,
				'show_ui' 				=> true,
				'map_meta_cap' 			=> true,
				'publicly_queryable' 	=> true,
				'exclude_from_search' 	=> false, //don't exclude from search
				'hierarchical' 			=> false,
				'rewrite' 				=> array( 'slug' => 'competitions' ),
				'supports' 				=> array( 'title', 'editor', 'author' ),
				'has_archive' 			=> true,
				'show_in_nav_menus' 	=> true,
				'show_in_menu' => 'bblm_main_menu',
				'menu_icon' 			=> 'dashicons-list-view',
			)
		); //end of bblm_comp
		register_post_type( 'bblm_race',
			array(
				'labels' => array(
					'name' 					=> __( 'Races', 'bblm' ),
					'singular_name' 		=> __( 'Race', 'bblm' ),
					'add_new_item' 			=> __( 'Add a new Race', 'bblm' ),
					'add_new' 			=> __( 'New Race', 'bblm' ),
					'edit_item' 			=> __( 'Edit Race', 'bblm' ),
					'new_item' 				=> __( 'New', 'bblm' ),
					'view_item' 			=> __( 'View Race', 'bblm' ),
					'view_items' 			=> __( 'View Race', 'bblm' ),
					'search_items' 			=> __( 'Search', 'bblm' ),
					'not_found' 			=> __( 'No results found.', 'bblm' ),
					'not_found_in_trash' 	=> __( 'No results found.', 'bblm' ),
					'all_items' 			=> __( 'Races', 'bblm' ),
					'featured_image'		=> __( 'Race Icon', 'bblm' ),
					'set_featured_image' 	=> __( 'Select Icon', 'bblm' ),
					'remove_featured_image' => __( 'Remove Icon', 'bblm' ),
					'use_featured_image' 	=> __( 'Select Icon', 'bblm' ),
				),
				'public' 				=> true,
				'show_ui' 				=> true,
				'map_meta_cap' 			=> true,
				'publicly_queryable' 	=> true,
				'exclude_from_search' 	=> false, //don't exclude from search
				'hierarchical' 			=> false,
				'rewrite' 				=> array( 'slug' => 'races' ),
				'supports' 				=> array( 'title', 'editor', 'thumbnail' ),
				'has_archive' 			=> true,
				'show_in_nav_menus' 	=> true,
				'show_in_menu' => 'bblm_main_menu',
				'menu_icon' 			=> 'dashicons-universal-access-alt',
			)
		); //end of bblm_race
	}

  /**
   * Register taxonomies.
   */
   public static function register_taxonomies() {

     // Teams Tax for posts
     register_taxonomy(
       'post_teams',
       'post',
       array(
         'label' => __( 'Teams', 'bblm'),
         'sort' => true,
         'args' => array( 'orderby' => 'term_order' ),
         'rewrite' => array( 'slug' => 'team-post' ),
       )
     );

     // Competitions Tax for posts
     register_taxonomy(
       'post_competitions',
       'post',
       array(
         'label' => __( 'Competitions', 'bblm' ),
         'sort' => true,
         'args' => array( 'orderby' => 'term_order' ),
         'rewrite' => array( 'slug' => 'competition-post' ),
       )
     );

   }


  /**
	 * Loads all the CPT handler classes front end
	 */
	public function include_post_type_handlers() {

    include_once( 'post-types/class-bblm-cpt-dyk.php' );
		include_once( 'post-types/class-bblm-cpt-owner.php' );
		include_once( 'post-types/class-bblm-cpt-transfer.php' );
		include_once( 'post-types/class-bblm-cpt-stadium.php' );
		include_once( 'post-types/class-bblm-cpt-match.php' );
		include_once( 'post-types/class-bblm-cpt-cup.php' );
		include_once( 'post-types/class-bblm-cpt-comp.php' );
		include_once( 'post-types/class-bblm-cpt-award.php' );
		include_once( 'post-types/class-bblm-cpt-team.php' );
		include_once( 'post-types/class-bblm-cpt-season.php' );
		include_once( 'post-types/class-bblm-cpt-race.php' );

 }

} //End of Class

new BBLM_Post_types();
