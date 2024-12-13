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
 * @version   1.6
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
<?php
				echo '<h2>'.__( 'League Settings', 'bblm' ).'</h2>';
				echo '<p>'.__( 'Use the following page to define the various settings required to maintain the Blood Bowl league.', 'bblm' ).'</p>';
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
            <p><?php echo __( 'Your settings have been updated', 'bblm' ); ?></p>
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
    			echo 'Deprecated settings which might be retired!';
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
        		'label' => __( 'League Name', 'bblm' ),
        		'section' => 'first_section',
        		'type' => 'text',
        		'placeholder' => 'League Name',
        		'helper' => __( 'Displayed on various pages - best to use the short name e.g. *BBL', 'bblm' ),
        	),
        	array(
        		'uid' => 'display_stats',
        		'label' => __( 'Statistics Limit', 'bblm' ),
        		'section' => 'second_section',
        		'type' => 'number',
            'placeholder' => '0',
            'helper' => __( 'The number of players and teams to display on the "top X" lists', 'bblm' ),
        	),
          array(
            'uid' => 'archive_stadium_text',
            'label' => __( 'Stadium Page Text', 'bblm' ),
            'section' => 'second_section',
            'placeholder' => 'Enter a description here',
            'type' => 'textarea',
            'helper' => __( 'Text entered here appears at the top of the Stadiums page', 'bblm' ),
          ),
          array(
            'uid' => 'archive_cup_text',
            'label' => __( 'Championship Cup Page Text', 'bblm' ),
            'section' => 'second_section',
            'placeholder' => 'Enter a description here',
            'type' => 'textarea',
            'helper' => __( 'Text entered here appears at the top of the Championship Cups page', 'bblm' ),
          ),
          array(
            'uid' => 'archive_season_text',
            'label' => __( 'Season Page Text', 'bblm' ),
            'section' => 'second_section',
            'placeholder' => 'Enter a description here',
            'type' => 'textarea',
            'helper' => __( 'Text entered here appears at the top of the Seasons page', 'bblm' ),
          ),
          array(
            'uid' => 'archive_race_text',
            'label' => __( 'Race Page Text', 'bblm' ),
            'section' => 'second_section',
            'placeholder' => 'Enter a description here',
            'type' => 'textarea',
            'helper' => __( 'Text entered here appears at the top of the Races page', 'bblm' ),
          ),
					array(
            'uid' => 'archive_comp_text',
            'label' => __( 'Competition Page Text', 'bblm' ),
            'section' => 'second_section',
            'placeholder' => 'Enter a description here',
            'type' => 'textarea',
            'helper' => __( 'Text entered here appears at the top of the Competitions page', 'bblm' ),
          ),
					array(
            'uid' => 'archive_team_text',
            'label' => __( 'Team Page Text', 'bblm' ),
            'section' => 'second_section',
            'placeholder' => 'Enter a description here',
            'type' => 'textarea',
            'helper' => __( 'Text entered here appears at the top of the Teams page', 'bblm' ),
          ),
					array(
						'uid' => 'archive_owner_text',
						'label' => __( 'Owners Page Text', 'bblm' ),
						'section' => 'second_section',
						'placeholder' => 'Enter a description here',
						'type' => 'textarea',
						'helper' => __( 'Text entered here appears at the top of the Owners page', 'bblm' ),
					),
					array(
						'uid' => 'archive_transfer_text',
						'label' => __( 'Transfers Page Text', 'bblm' ),
						'section' => 'second_section',
						'placeholder' => 'Enter a description here',
						'type' => 'textarea',
						'helper' => __( 'Text entered here appears at the top of the Transfers page', 'bblm' ),
					),
					array(
						'uid' => 'archive_match_text',
						'label' => __( 'Match / Results Page Text', 'bblm' ),
						'section' => 'second_section',
						'placeholder' => 'Enter a description here',
						'type' => 'textarea',
						'helper' => __( 'Text entered here appears at the top of the Matches / Results page', 'bblm' ),
					),
					array(
						'uid' => 'archive_award_text',
						'label' => __( 'Awards Page Text', 'bblm' ),
						'section' => 'second_section',
						'placeholder' => 'Enter a description here',
						'type' => 'textarea',
						'helper' => __( 'Text entered here appears at the top of the Awards page', 'bblm' ),
					),
					array(
						'uid' => 'archive_stars_text',
						'label' => __( 'Star Players Page Text', 'bblm' ),
						'section' => 'second_section',
						'placeholder' => 'Enter a description here',
						'type' => 'textarea',
						'helper' => __( 'Text entered here appears at the top of the Star Players page', 'bblm' ),
					),
          array(
        		'uid' => 'page_team',
        		'label' => __( 'Page # - Teams', 'bblm' ),
        		'section' => 'third_section',
        		'type' => 'number',
            'placeholder' => '',
            'helper' => __( 'The Wordpress Page ID "Teams" Page.', 'bblm' ),
        	),
          array(
        		'uid' => 'page_stars',
        		'label' => __( 'Page # - Star Players', 'bblm' ),
        		'section' => 'third_section',
        		'type' => 'number',
            'placeholder' => '',
            'helper' => __( 'The Wordpress Page ID "Star Players" Page.', 'bblm' ),
        	),
          array(
        		'uid' => 'cat_warzone',
        		'label' => __( 'Category - Warzone', 'bblm' ),
        		'section' => 'third_section',
        		'type' => 'number',
            'placeholder' => '',
            'helper' => __( 'The cat ID for "warzone" so it can be filtered', 'bblm' ),
        	),
          array(
        		'uid' => 'team_tbd',
        		'label' => __( 'Team_ID - "To Be Determined"', 'bblm' ),
        		'section' => 'third_section',
        		'type' => 'number',
            'placeholder' => '',
            'helper' => __( 'The ID number for the team "To Be Determined"', 'bblm' ),
        	),
          array(
        		'uid' => 'team_star',
        		'label' => __( 'Team_ID - "Star Player" Team', 'bblm' ),
        		'section' => 'third_section',
        		'type' => 'number',
            'placeholder' => '',
            'helper' => __( 'The ID number for the team "Star Players"', 'bblm' ),
        	),
          array(
        		'uid' => 'race_star',
        		'label' => __( 'Race_ID - Star Player Race', 'bblm' ),
        		'section' => 'third_section',
        		'type' => 'number',
            'placeholder' => '',
            'helper' => __( 'The ID number for the race "Stars"', 'bblm' ),
        	),
          array(
        		'uid' => 'player_merc',
        		'label' => __( 'Player_ID - Mercenary', 'bblm' ),
        		'section' => 'third_section',
        		'type' => 'number',
            'placeholder' => '',
            'helper' => __( 'The ID number for the "Merc" Position', 'bblm' ),
        	),
					array(
						'uid' => 'player_rrookie',
						'label' => __( 'Player_ID - Riotous Rookie', 'bblm' ),
						'section' => 'third_section',
						'type' => 'number',
						'placeholder' => '',
						'helper' => __( 'The ID number for the "Riotous Rookie" Position', 'bblm' ),
					),

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
