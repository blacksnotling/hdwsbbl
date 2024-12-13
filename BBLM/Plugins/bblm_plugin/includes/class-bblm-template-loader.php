<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Template Loader
 *
 * @class 		BBLM_Template_Loader
 * @version		1.4
 * @package		BBowlLeagueMan/Templates
 * @category	Class
 * @author 		Blacksnotliung
 */
class BBLM_Template_Loader {

	/**
	 * Constructor
	 */
	public function __construct() {

		add_filter( 'template_include', array( $this, 'template_loader' ) );

	}

	/**
	 * Load a template.
	 *
	 * Handles template usage so that we can load our own templates from a plugin instead of the themes.
	 *
	 * Templates are in the 'templates' folder.
	 *
	 *
	 * @param mixed $template
	 * @return string
	 */
	public function template_loader( $template ) {
		$find = array();
		$file = '';

		if ( is_single() ) {

			$post_type = get_post_type();

			if ($post_type == "bblm_stadium") {
				$file = 'single-' . $post_type . '.php';
				$find[] = $file;
				$find[] = BBLM_TEMPLATE_PATH . $file;
			}
      elseif ($post_type == "bblm_cup") {
				$file = 'single-' . $post_type . '.php';
				$find[] = $file;
				$find[] = BBLM_TEMPLATE_PATH . $file;
			}
      elseif ($post_type == "bblm_season") {
        $file = 'single-' . $post_type . '.php';
        $find[] = $file;
        $find[] = BBLM_TEMPLATE_PATH . $file;
      }
			elseif ($post_type == "bblm_owner") {
				$file = 'single-' . $post_type . '.php';
				$find[] = $file;
				$find[] = BBLM_TEMPLATE_PATH . $file;
			}
			elseif ($post_type == "bblm_comp") {
				$file = 'single-' . $post_type . '.php';
				$find[] = $file;
				$find[] = BBLM_TEMPLATE_PATH . $file;
			}
			elseif ($post_type == "bblm_race") {
				$file = 'single-' . $post_type . '.php';
				$find[] = $file;
				$find[] = BBLM_TEMPLATE_PATH . $file;
			}
			elseif ($post_type == "bblm_match") {
				$file = 'single-' . $post_type . '.php';
				$find[] = $file;
				$find[] = BBLM_TEMPLATE_PATH . $file;
			}
			elseif ($post_type == "bblm_star") {
				$file = 'single-' . $post_type . 'players.php';
				$find[] = $file;
				$find[] = BBLM_TEMPLATE_PATH . $file;
			}
			elseif ($post_type == "bblm_team") {
				$file = 'single-' . $post_type . '.php';
				$find[] = $file;
				$find[] = BBLM_TEMPLATE_PATH . $file;
			}
			elseif ($post_type == "bblm_player") {
				$file = 'single-' . $post_type . '.php';
				$find[] = $file;
				$find[] = BBLM_TEMPLATE_PATH . $file;
			}
			elseif ($post_type == "bblm_roster") {
				$file = 'single-' . $post_type . '.php';
				$find[] = $file;
				$find[] = BBLM_TEMPLATE_PATH . $file;
			}
		}
		elseif ( is_post_type_archive( 'bblm_stadium' ) ) {

				$file = 'archive-bblm_stadium.php';
				$find[] = $file;
				$find[] = BBLM_TEMPLATE_PATH . $file;
		}
    elseif ( is_post_type_archive( 'bblm_season' ) ) {

        $file = 'archive-bblm_season.php';
        $find[] = $file;
        $find[] = BBLM_TEMPLATE_PATH . $file;
    }
    elseif ( is_post_type_archive( 'bblm_dyk' ) ) {

        $file = 'archive-bblm_dyk.php';
        $find[] = $file;
        $find[] = BBLM_TEMPLATE_PATH . $file;
    }
    elseif ( is_post_type_archive( 'bblm_cup' ) ) {

        $file = 'archive-bblm_cup.php';
        $find[] = $file;
        $find[] = BBLM_TEMPLATE_PATH . $file;
    }
    elseif ( is_post_type_archive( 'bblm_race' ) ) {

        $file = 'archive-bblm_race.php';
        $find[] = $file;
        $find[] = BBLM_TEMPLATE_PATH . $file;
    }
		elseif ( is_post_type_archive( 'bblm_owner' ) ) {

				$file = 'archive-bblm_owner.php';
				$find[] = $file;
				$find[] = BBLM_TEMPLATE_PATH . $file;
		}
		elseif ( is_post_type_archive( 'bblm_transfer' ) ) {

				$file = 'archive-bblm_transfer.php';
				$find[] = $file;
				$find[] = BBLM_TEMPLATE_PATH . $file;
		}
		elseif ( is_post_type_archive( 'bblm_comp' ) ) {

				$file = 'archive-bblm_comp.php';
				$find[] = $file;
				$find[] = BBLM_TEMPLATE_PATH . $file;
		}
		elseif ( is_post_type_archive( 'bblm_match' ) ) {

				$file = 'archive-bblm_match.php';
				$find[] = $file;
				$find[] = BBLM_TEMPLATE_PATH . $file;
		}
		elseif ( is_post_type_archive( 'bblm_star' ) ) {

				$file = 'archive-bblm_star.php';
				$find[] = $file;
				$find[] = BBLM_TEMPLATE_PATH . $file;
		}
		elseif ( is_post_type_archive( 'bblm_team' ) ) {

				$file = 'archive-bblm_team.php';
				$find[] = $file;
				$find[] = BBLM_TEMPLATE_PATH . $file;
		}
		elseif ( is_post_type_archive( 'bblm_player' ) ) {

				$file = 'archive-bblm_player.php';
				$find[] = $file;
				$find[] = BBLM_TEMPLATE_PATH . $file;
		}
		elseif ( is_post_type_archive( 'bblm_roster' ) ) {

				$file = 'archive-bblm_roster.php';
				$find[] = $file;
				$find[] = BBLM_TEMPLATE_PATH . $file;
		}

		if ( $file ) {
			if ( file_exists( $find[1] ) ) {
				$template = $find[1];
			}
		}

		return $template;
	}

}

new BBLM_Template_Loader();
