<?php
if ( ! defined( 'ABSPATH' ) ) {
 exit; // Exit if accessed directly
}
/**
 * A simple class that allows registering Page templates from plugins for non-CPT page types.
 * Modified version of http://www.wpexplorer.com/wordpress-page-templates-plugin/
 * and https://github.com/codelight-eu/wp-page-templates
 *
 * @class 		Codelight_PageTemplates
 * @version		1.0
 * @package		Templates
 * @category	Class
 * @author 		Codelight
 */

class Codelight_PageTemplates
{
    /* @var array */
    protected $templates = [];

    /**
     * PageTemplateManager constructor.
     */
    public function __construct()
    {
        // Add our custom templates to page template dropdown in WP Admin
        add_filter('theme_page_templates', [$this, 'registerTemplate']);

        // On saving a post, inject our templates into the page cache
        add_filter('wp_insert_post_data', [$this, 'cacheTemplate']);

        // Render our custom template, if applicable
        add_filter('template_include', [$this, 'renderTemplate']);
    }

    /**
     * Add a new custom template.
     *
     * @param $file string Full path to the template file
     * @param $name string Human-readable template name
     */
    public function addTemplate($file, $name)
    {
        $this->templates[$file] = $name;
    }

    /**
     * Add our custom templates to page template dropdown in WP Admin
     *
     * @param $templates
     * @return array
     */
    public function registerTemplate($templates)
    {
        return array_merge($templates, $this->templates);
    }

    /**
     * Add our template to the pages cache in order to trick WordPress
     * into thinking the template file exists
     *
     * @param $data
     * @return mixed
     */
    public function cacheTemplate($data)
    {
        // Create the key used for the themes cache
        $cacheKey = 'page_templates-' . md5(get_theme_root() . '/' . get_stylesheet());

        // Retrieve the cache list. If it doesn't exist or if it's empty, set up a new one
        $templates = wp_get_theme()->get_page_templates();
        if (empty($templates)) {
            $templates = [];
        }

        // Remove the old cache
        wp_cache_delete($cacheKey, 'themes');

        // Add our custom templates to the list of WP's own templates
        $templates = array_merge($templates, $this->templates);

        // Add the modified cache to allow WordPress to pick it up for listing available templates
        wp_cache_add($cacheKey, $templates, 'themes', 1800);

        return $data;
    }

    /**
     * Check if one of the registered templates is assigned to the current page.
     * If it is, then render it.
     *
     * @param $template
     * @return string
     */
    public function renderTemplate($template)
    {
        // If we're searching, bail.
        if (is_search()) {
            return $template;
        }

        // If we're viewing something that's not a post, bail.
        global $post;
        if (!$post) {
            return $template;
        }

        // If the page doesn't have one of our custom templates assigned, bail.
        $currentTemplate = get_post_meta($post->ID, '_wp_page_template', true);
        if (!isset($this->templates[$currentTemplate])) {
            return $template;
        }

        // Now we've made sure that this is one of our custom templates.
        // If the template file actually exists, include it.
        if (file_exists($currentTemplate)) {
            return $currentTemplate;
        }

        // Otherwise, trigger an error and return the default template.
        trigger_error("Template file {$currentTemplate} does not exist.", E_USER_WARNING);

        return $template;
    }
} //end of class Codelight_PageTemplates

$pageTemplates = new Codelight_PageTemplates();
$pageTemplates->addTemplate( BBLM_TEMPLATE_PATH . 'bb.core.graveyard.php', 'BBLM Graveyard' );
$pageTemplates->addTemplate( BBLM_TEMPLATE_PATH . 'bb.view.stats.php', 'BBLM Statistics - Main Page' );
$pageTemplates->addTemplate( BBLM_TEMPLATE_PATH . 'bb.view.stats.cas.php', 'BBLM Statistics - Casualities' );
$pageTemplates->addTemplate( BBLM_TEMPLATE_PATH . 'bb.view.stats.misc.php', 'BBLM Statistics -Misc' );
$pageTemplates->addTemplate( BBLM_TEMPLATE_PATH . 'bb.view.stats.td.php', 'BBLM Statistics - Touchdowns' );
$pageTemplates->addTemplate( BBLM_TEMPLATE_PATH . 'archive-bblm_award.php', 'BBLM Awards Archive' );
$pageTemplates->addTemplate( BBLM_TEMPLATE_PATH . 'bb.core.fixtures.php', 'BBLM Fixtures' );
$pageTemplates->addTemplate( BBLM_TEMPLATE_PATH . 'archive-bblm_match.php', 'BBLM Match Archive' );
$pageTemplates->addTemplate( BBLM_TEMPLATE_PATH . 'single-bblm_match.php', 'BBLM Match Details' );
$pageTemplates->addTemplate( BBLM_TEMPLATE_PATH . 'archive-bblm_race.php', 'BBLM Race Archive' );
$pageTemplates->addTemplate( BBLM_TEMPLATE_PATH . 'single-bblm_race.php', 'BBLM Race Details' );
$pageTemplates->addTemplate( BBLM_TEMPLATE_PATH . 'archive-bblm_team.php', 'BBLM Team Archive' );
$pageTemplates->addTemplate( BBLM_TEMPLATE_PATH . 'single-bblm_team.php', 'BBLM Team Details' );
$pageTemplates->addTemplate( BBLM_TEMPLATE_PATH . 'single-bblm_player.php', 'BBLM Player Details' );
$pageTemplates->addTemplate( BBLM_TEMPLATE_PATH . 'single-bblm_roster.php', 'BBLM Roster Details' );
$pageTemplates->addTemplate( BBLM_TEMPLATE_PATH . 'archive-bblm_starplayers.php', 'BBLM Star Players Archive' );
$pageTemplates->addTemplate( BBLM_TEMPLATE_PATH . 'single-bblm_starplayers.php', 'BBLM Star Players Details' );
