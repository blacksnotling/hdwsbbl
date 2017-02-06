<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Loads the settings page, and lets the user edit the settings.
 * Code adapted from https://www.smashingmagazine.com/2016/04/three-approaches-to-adding-configurable-fields-to-your-plugin/
 *
 * @class 		BBLM_Settings_Admin
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/Admin
 * @version   1.0
 */

class BBLM_Settings_Admin {

	/**
	 * Constructor
	 */
    public function __construct() {

    	// Hook into the admin menu
    	add_action( 'admin_menu', array( $this, 'bblm_create_plugin_settings_page' ) );

      // Add Settings and Fields
    	add_action( 'admin_init', array( $this, 'setup_sections' ) );
    	add_action( 'admin_init', array( $this, 'setup_fields' ) );

    }

	/**
	 * Creats a submenu page for this options page
   */
    public function bblm_create_plugin_settings_page() {

      add_submenu_page(
        'bblm_main_menu',
        __( 'League Settings', 'bblm' ),
        __( 'League Settings', 'bblm' ),
        'manage_options',
        'bblm_settings',
        array( $this, 'plugin_settings_page_content' )
      );

    }

	/**
	 * Constructs the start of the form, and kicks off the Settings API
   */
    public function plugin_settings_page_content() {
?>

    	<div class="wrap">
    		<h2>League Settings</h2>
        <p>Use the following page to define the various settings required to maintain the Blood Bowl league.</p>
        <?php
            if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] ) {
                  $this->admin_notice();
            }
        ?>
    		<form method="POST" action="options.php">
                <?php
                    settings_fields( 'bblm_options' );
                    do_settings_sections( 'bblm_options' );
                    submit_button();
                ?>
    		</form>
    	</div>
<?php
    }
	/**
	 * The notification that is given to the user upon successful save
   */
    public function admin_notice() {
      ?>
        <div class="notice notice-success is-dismissible">
            <p>Your settings have been updated</p>
        </div>
        <?php
    }

	/**
	 * Define the different sections of the form
   */
    public function setup_sections() {

        add_settings_section( 'first_section', __( 'League Settings', 'bblm' ), array( $this, 'section_callback' ), 'bblm_options' );
        add_settings_section( 'second_section', __( 'Display Settings', 'bblm' ), array( $this, 'section_callback' ), 'bblm_options' );
        add_settings_section( 'third_section', __( 'Legacy Technical Settings', 'bblm' ), array( $this, 'section_callback' ), 'bblm_options' );

    }

	/**
	 * Define the text for the different sections of the form
   */
    public function section_callback( $arguments ) {

    	switch ( $arguments['id'] ){
    		case 'first_section':
    			break;
    		case 'second_section':
    			echo 'Various display settings for the websute';
    			break;
    		case 'third_section':
    			echo 'Obsolete settings which might be retired!';
    			break;

    	}

    }

	/**
	 * Define the fields that will appear on the page in array format
   */
    public function setup_fields() {

        $fields = array(
        	array(
        		'uid' => 'league_name',
        		'label' => 'League Name',
        		'section' => 'first_section',
        		'type' => 'text',
        		'placeholder' => 'League Name',
        		'helper' => 'Displayed on various pages - best to use the short name e.g. *BBL',
        	),
        	array(
        		'uid' => 'display_stats',
        		'label' => 'Statistics Limit',
        		'section' => 'second_section',
        		'type' => 'number',
            'placeholder' => '0',
            'helper' => 'The number of players and teams to display on the "top X" lists',
        	),
          array(
        		'uid' => 'site_dir',
        		'label' => 'Site Directory',
        		'section' => 'third_section',
        		'type' => 'text',
        		'placeholder' => '',
        		'helper' => 'The location of the wordpress install from the SERVER root WITHOUT Slashes. eg hdwsbbl',
        	),
          array(
        		'uid' => 'page_race',
        		'label' => 'Page # - Races',
        		'section' => 'third_section',
        		'type' => 'number',
            'placeholder' => '',
            'helper' => 'The Wordpress Page ID "Races" Page.',
        	),
          array(
        		'uid' => 'page_team',
        		'label' => 'Page # - Teams',
        		'section' => 'third_section',
        		'type' => 'number',
            'placeholder' => '',
            'helper' => 'The Wordpress Page ID "Teams" Page.',
        	),
          array(
        		'uid' => 'page_series',
        		'label' => 'Page # - Championship Cups',
        		'section' => 'third_section',
        		'type' => 'number',
            'placeholder' => '',
            'helper' => 'The Wordpress Page ID "Series" Page.',
        	),
          array(
        		'uid' => 'page_season',
        		'label' => 'Page # - Seasons',
        		'section' => 'third_section',
        		'type' => 'number',
            'placeholder' => '',
            'helper' => 'The Wordpress Page ID "Seasons" Page.',
        	),
          array(
        		'uid' => 'page_comp',
        		'label' => 'Page # - Competitions',
        		'section' => 'third_section',
        		'type' => 'number',
            'placeholder' => '',
            'helper' => 'The Wordpress Page ID "competitions" Page.',
        	),
          array(
        		'uid' => 'page_match',
        		'label' => 'Page # - Match / Results',
        		'section' => 'third_section',
        		'type' => 'number',
            'placeholder' => '',
            'helper' => 'The Wordpress Page ID "results" Page.',
        	),
          array(
        		'uid' => 'page_stadium',
        		'label' => 'Page # - Stadiums',
        		'section' => 'third_section',
        		'type' => 'number',
            'placeholder' => '',
            'helper' => 'The Wordpress Page ID "Stadiums" Page.',
        	),
          array(
        		'uid' => 'page_stats',
        		'label' => 'Page # - Statistics',
        		'section' => 'third_section',
        		'type' => 'number',
            'placeholder' => '',
            'helper' => 'The Wordpress Page ID "Statistics" Page.',
        	),
          array(
        		'uid' => 'page_stars',
        		'label' => 'Page # - Star Players',
        		'section' => 'third_section',
        		'type' => 'number',
            'placeholder' => '',
            'helper' => 'The Wordpress Page ID "Star Players" Page.',
        	),
          array(
        		'uid' => 'cat_warzone',
        		'label' => 'Category - Warzone',
        		'section' => 'third_section',
        		'type' => 'number',
            'placeholder' => '',
            'helper' => 'The cat ID for "warzone" so it can be filtered',
        	),
          array(
        		'uid' => 'team_tbd',
        		'label' => 'Team_ID - "To Be Determined"',
        		'section' => 'third_section',
        		'type' => 'number',
            'placeholder' => '',
            'helper' => 'The ID number for the team "To Be Determined"',
        	),
          array(
        		'uid' => 'team_star',
        		'label' => 'Team_ID - "Star Player" Team',
        		'section' => 'third_section',
        		'type' => 'number',
            'placeholder' => '',
            'helper' => 'The ID number for the team "Star Players"',
        	),
          array(
        		'uid' => 'race_star',
        		'label' => 'Race_ID - Star Player Race',
        		'section' => 'third_section',
        		'type' => 'number',
            'placeholder' => '',
            'helper' => 'The ID number for the race "Stars"',
        	),
          array(
        		'uid' => 'player_merc',
        		'label' => 'Player_ID - Mercenary',
        		'section' => 'third_section',
        		'type' => 'number',
            'placeholder' => '',
            'helper' => 'The ID number for the "Merc" Position',
        	),

/* saved for future use!
          array(
        		'uid' => 'awesome_textarea',
        		'label' => 'Sample Text Area',
        		'section' => 'first_section',
            'placeholder' => 'longer text goes here',
        		'type' => 'textarea',
            'helper' => 'Does this help?',
        	),
*/


        );

    	foreach ( $fields as $field ) {

         add_settings_field(
          $field['uid'],
          $field['label'],
          array( $this, 'field_callback' ),
          'bblm_options',
          $field['section'],
          $field );

    	}

      //slug, option-name
      register_setting( 'bblm_options', 'bblm_config' );

    }

	/**
	 * Output of the form itself, based on the array provided
   */
    public function field_callback( $arguments ) {

        $setting = (array) get_option( 'bblm_config' );
        $value = $setting[$arguments['uid']];

        if ( ! $value ) {
            $value = '';
        }

        switch ( $arguments['type'] ) {
            case 'text':
            case 'password':
            case 'number':
                printf( '<input name="bblm_config[%1$s]" id="bblm_config[%1$s]" type="%2$s" placeholder="%3$s" value="%4$s" />', $arguments['uid'], $arguments['type'], $arguments['placeholder'], $value );
                break;
            case 'textarea':
                printf( '<textarea name="bblm_config[%1$s]" id="bblm_config[%1$s]" placeholder="%2$s" rows="5" cols="50">%3$s</textarea>', $arguments['uid'], $arguments['placeholder'], $value );
                break;

        }

        if ( $helper = $arguments['helper'] ) {

            printf( '<span class="helper"> %s</span>', $helper );

        }

    }

}
