<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * BBowlLeagueMan Common Template Functions
 *
 * Common functions used in BBLM templates in order to call custom functionality
 *
 * @author 		Blacksnotling
 * @category 	Core
 * @package 	BBowlLeagueMan/Functions
 * @version   1.0
 */

/**
 * Outputs the edit linl, formatted for the bllm plugin
 */
function bblm_display_page_edit_link() {

	edit_post_link( __( 'Edit', 'bblm' ), ' <strong>[</strong> ', ' <strong>]</strong> ');

}// end of bblm_display_page_edit_link()

/**
* Outouts a list of all the championship cups
*/
function bblm_template_display_cup_listing() {

	BBLM_CPT_Cup::get_cup_listing();

} //end of bblm_template_display_cup_listing

/**
 * Outouts a single DYK
 * This only works on the DYK pages
 * ASSUMPTION is that it is called within a post loop
 */
function bblm_template_display_single_dyk() {
	global $post;

	$type = get_post_meta( get_the_ID(), 'dyk_type', true );

?>
	<div class="dykcontainer dyk<?php echo strtolower( $type ); ?>" id="dyk<?php echo the_ID(); ?>">
	<h3 class="dykheader"><?php echo bblm_get_league_name(); ?> - <?php if( "Trivia" == $type ) { print("Did You Know"); } else { print("Fact"); } ?></h3>
<?php

		if ( ( strlen( get_the_title() ) !== 0 ) && ( "none" !== strtolower( get_the_title() ) ) ) {
?>
			<h4><?php the_title(); ?></h4>
<?php
		}

		the_content();
?>
			<p><?php bblm_display_page_edit_link(); ?></p>
	</div><!-- .dykcontainer .dyk -->
<?php

} //end of bblm_template_display_single_dyk()

/**
 * Outouts a list of the top players for a championship cup
 */
function bblm_template_display_top_players_table_cup() {

	$bblm_stats = new BBLM_Stat;
	$stat_limit = bblm_get_stat_limit();
	$bblm_stats->display_top_players_table( $cupid, 'bblm_cup', $stat_limit );

} //end of bblm_template_display_top_players_table_cup

/**
 * Outouts a list of the top killers for a championship cup
 */
function bblm_template_display_top_killers_table_cup() {

	$bblm_stats = new BBLM_Stat;
	$stat_limit = bblm_get_stat_limit();
	$bblm_stats->display_top_killers_table( $cupid, 'bblm_cup', $stat_limit );

} //end of bblm_template_display_top_killers_table_cup
