<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Template Loader
 *
 * @class 		BBLM_Rewrites
 * @version		1.0
 * @package		BBowlLeagueMan/Templates
 * @category	Class
 * @author 		Blacksnotliung
 */
class BBLM_Rewrites {

	/**
	 * Constructor
	 */
	public function __construct() {

		//add_action( 'init', array( $this, 'add_rewrite_rules' ) );
		//add_filter( 'post_type_link', array( $this, 'set_permalinks' ), 10, 3);
		//add_filter( 'pre_get_posts', array( $this, 'study_query' ) );

	}

	/**
	 * Adds the Rewrite rules for Players and Rosters
	 *
	 */
	public function add_rewrite_rules() {

		add_rewrite_tag('%bblm_player%', '([^/]+)', 'bblm_player=');
		add_permastruct('bblm_player', 'teams/%team%/%bblm_player%', false);
		add_rewrite_rule('^teams/([^/]+)/([^/]+)/?','index.php?post_type=bblm_player&name=$matches[2]','top');

	}

	function set_permalinks($permalink, $post, $leavename) {
		$post_id = $post->ID;

		if($post->post_type != 'bblm_player' || empty($permalink) || in_array($post->post_status, array('draft', 'pending', 'auto-draft')))
	 		return $permalink;
			$parent = $post->post_parent;
			$parent_post = get_post( $parent );
			$permalink = str_replace('%team%', $parent_post->post_name, $permalink);
			return $permalink;

		}


		function study_query( $query ) {
			// run this code only when we are on the public archive
		  if( 'bblm_player' != $query->query_vars['post_type'] || ! $query->is_main_query() || is_admin() ) {
		     return;
		  }
		  // fix query for hierarchical study permalinks
		  if ( isset( $query->query_vars['name']) && isset( $query->query_vars['study'] ) ) {
		  	// remove the parent name
				$query->set( 'name', basename( untrailingslashit( $query->query_vars['name'] ) ));
		    // unset this
		    $query->set( 'study', null );
			}
		}

}

new BBLM_Rewrites();
