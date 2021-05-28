<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Two classes
 * The first hanbdles the generation of the Brackets table,
 * THe Second inputs and displays the data and handles the form and POST / GET action
 *
 * @class 		BBLM_Brackets_list
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/Admin
 * @version   1.0
 */

 if ( ! class_exists( 'WP_List_Table' ) ) {
 	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
 }

 class BBLM_Brackets_List extends WP_List_Table {

   /** Class constructor */
 	public function __construct() {

 		parent::__construct( [
 			'singular' => __( 'Bracket', 'bblm' ), //singular name of the listed records
 			'plural'   => __( 'Brackets', 'bblm' ), //plural name of the listed records
 			'ajax'     => false //does this table support ajax?
 		] );

 	}

  /**
   * Retrieve Tournament Bracket data from the database
   *
   * @param int $per_page
   * @param int $page_number
   *
   * @return mixed
   */
  public static function get_brackets( $per_page = 1000, $page_number = 1 ) {

    global $wpdb;

		$sql = 'SELECT c_id, COUNT(*) AS bcount FROM ' . $wpdb->prefix . 'comp_brackets GROUP BY c_id ORDER BY c_id DESC';

    $sql .= " LIMIT $per_page";
    $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

    $result = $wpdb->get_results( $sql, 'ARRAY_A' );

    return $result;
  }

  /**
   * Returns the count of records in the database.
   *
   * @return null|string
   */
  public static function record_count() {
    global $wpdb;

    $sql = "SELECT COUNT(DISTINCT(c_id)) FROM {$wpdb->prefix}comp_brackets";

    return $wpdb->get_var( $sql );
  }


  /** Text displayed when no Tournament Bracket data is available */
  public function no_items() {

    _e( 'No brackets available.', 'bblm' );

  }


  /**
   * Render a column when no column specific method exist.
   *
   * @param array $item
   * @param string $column_name
   *
   * @return mixed
   */
  public function column_default( $item, $column_name ) {
    switch ( $column_name ) {

      case 'comp';
        return bblm_get_competition_name( $item[ 'c_id' ] );

      case 'match';
        return $item[ 'bcount' ];

      case 'edit';
      return '<a href="'.admin_url().'admin.php?page=bblm_brackets&bblm_action=edit&cid='.$item[ 'c_id' ].'">Edit</a>';

      default:
        return print_r( $item, true ); //Show the whole array for troubleshooting purposes
    }
  }

  /**
   * Render the bulk edit checkbox
   *
   * @param array $item
   *
   * @return string
   */
  function column_cb( $item ) {

    return sprintf(
      '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['c_id']

    );

  }


  /**
   * Method for name column
   *
   * @param array $item an array of DB data
   *
   * @return string
   */
  function column_name( $item ) {

    $title = bblm_get_team_name( $item[ 'teamA' ] ) . " vs " . bblm_get_team_name( $item[ 'teamB' ] );

    return $title;
  }

  /**
   *  Associative array of columns
   *
   * @return array
   */
  function get_columns() {
    $columns = [
      'cb'      => '<input type="checkbox" />',
			'comp' => __( 'Competition', 'bblm' ),
      'match'    => __( '# Matches', 'bblm' ),
      'edit' => __( 'Edit', 'bblm' ),
    ];

    return $columns;
  }

  /**
   * Columns to make sortable.
   *
   * @return array
   */
  public function get_sortable_columns() {
    $sortable_columns = array(
      'comp' => array( 'comp', true ),
      'match' => array( 'match', true ),
    );

    return $sortable_columns;
  }

  /**
   * Returns an associative array containing the bulk action
   *
   * @return array
   */
  public function get_bulk_actions() {
    $actions = [
      'bulk-delete' => 'N/A'
    ];

    return $actions;
  }

  /**
   * Handles data query and filter, sorting, and pagination.
   */
  public function prepare_items() {

    $this->_column_headers = $this->get_column_info();

    /** Process bulk action */

    $per_page     = $this->get_items_per_page( 'brackets_per_page', 1000 );
    $current_page = $this->get_pagenum();
    $total_items  = self::record_count();

    $this->set_pagination_args( [
      'total_items' => $total_items, //WE have to calculate the total number of items
      'per_page'    => $per_page //WE have to determine how many items to show on a page
    ] );

    $this->items = self::get_brackets( $per_page, $current_page );
  }

 } //end of class

 /**
  *
  * @class 		BBLM_Manage_Brackets
  * @author 		Blacksnotling
  * @category 	Admin
  * @package 	BBowlLeagueMan/Admin
  * @version   1.0
  */

 class BBLM_Manage_Brackets {

  // class instance
  static $instance;

  // Tournament Brackets WP_List_Table object
  public $bracket_obj;

  // class constructor
  public function __construct() {
    add_filter( 'set-screen-option', [ __CLASS__, 'set_screen' ], 10, 3 );
    add_action( 'admin_menu', array( $this, 'plugin_menu' ) );
  }


  public static function set_screen( $status, $option, $value ) {

    return $value;

  }

  public function plugin_menu() {

     $hook = add_submenu_page(
       'bblm_main_menu',
       __( 'Tournament Brackets', 'bblm' ),
       __( 'Tournament Brackets', 'bblm' ),
       'manage_options',
       'bblm_brackets',
       array( $this, 'manage_brackets_page' )
     );

    add_action( "load-$hook", array( $this, 'screen_option' ) );

  }


  /**
   * The Output of the Page
   */
  public function manage_brackets_page() {
    global $wpdb;
 ?>
    <div class="wrap">
       <h1 class="wp-heading-inline"><?php echo __( 'Manage Tournament Brackets', 'bblm' ); ?></h1>

 <?php
     if ( isset( $_POST['bblm_bracket_add'] ) ) {

       //If we have submitted a new bracket then pass to the correct function to save
       $this->submit_handling_new();

     }

    if ( isset( $_POST['bblm_bracket_edit'] ) ) {

       //If we have submitted an updated bracket then pass to the correct function to save
       $this->submit_handling_update();

     }

    if ( isset( $_POST['bblm_comp_select'] ) ) {

       //we are adding brackets so we pass to the correct function
       $this->new_handling();

     }
		 else if ( isset( $_GET['bblm_action'] ) && 'edit' == $_GET['bblm_action'] ) {

				//we are updating brackets so we pass to the correct function
				$this->edit_handling();

			}
    else {
       //we are displaying the overall form
 ?>
      <div id=bracketaddform>
        <h2><?php echo __( 'Set-up new Tournament Brackets', 'bblm' ); ?></h2>
        <div id="addpost-body" class="metabox-holder columns-3">
          <div id="addpost-body-content">

            <form name="bblm_selectcomp" method="post" id="bblm_selectcomp">
              <p><?php echo __( 'Please chose the competition which you wish to add tournament brackets too. Only Competitions without brackets set-up, and without teams assigned will be displayed below.', 'bblm' ); ?></p>

              <table class="form-table">
                <tr valign="top">
                  <th scope="row"><label for="bblm_bcomp"><?php echo __( 'Competition', 'bblm' ); ?>:</label></th>
                  <td><select name="bblm_bcomp" id="bblm_bcomp">
 <?php
						$compssql = 'SELECT C.c_id FROM '.$wpdb->prefix.'team_comp C LEFT JOIN '.$wpdb->prefix.'comp_brackets B ON C.c_id = B.c_id WHERE B.cb_order IS NULL GROUP BY C.c_id ORDER BY C.c_id DESC';
            if ( $comps = $wpdb->get_results( $compssql ) ) {
              foreach ( $comps as $comp ) {
                echo '<option value="' . $comp->c_id . '">' . bblm_get_competition_name( $comp->c_id ) . '</option>';
              }
            }
 ?>
                  </select></td>
                </tr>
                <tr valign="top">
                  <th scope="row"><label for="bblm_bteams"><?php echo __( 'Number of teams taking part', 'bblm' ); ?>:</label></th>
                  <td><select name="bblm_bteams" id="bblm_bteams">
                  	  	<option value="4">0-4 <?php echo __( 'Teams (2 rounds, 3 games)', 'bblm' ); ?></option>
                  	  	<option value="8">5-8 <?php echo __( 'Teams (3 rounds, 7 games)', 'bblm' ); ?></option>
                  	  	<option value="16">9-16 <?php echo __( 'Teams (4 rounds, 15 games)', 'bblm' ); ?></option>
                  	  </select></td>
                </tr>
              </table>

              <p class="submit"><input type="submit" name="bblm_comp_select" id="bblm_comp_select" class="button-primary" value="Continue to set-up new tournament brackets" title="Continue to set-up new Brackets"  /></p>

            </form>

          </div>
        </div>
      </div>


      <div id="poststuff">
        <h2><?php echo __( 'Edit Existing Tournament Brackets', 'bblm' ); ?></h2>
        <div id="post-body" class="metabox-holder columns-3">
          <div id="post-body-content">

            <div class="meta-box-sortables ui-sortable">

              <form method="post" action="">
 <?php
              //set a nonce for the bulk actions
              wp_nonce_field( 'bblm_delete_tbracket', 'bblm_brackets' );
              $this->bracket_obj->prepare_items();
              $this->bracket_obj->display();
 ?>

              </form>
            </div>
          </div>
        </div>
        <br class="clear">
      </div>
    </div>
  <?php
       }
  }

  /**
   * Screen options pull down
   */
  public function screen_option() {

    $option = 'per_page';
    $args   = array(
      'label'   => 'Brackets',
      'default' => 1000,
      'option'  => 'brackets_per_page'
    );

    add_screen_option( $option, $args );

    $this->bracket_obj = new BBLM_Brackets_List();
  }

   /**
   * Displays the form for adding tournament brackets
   */
    public function new_handling() {
     global $wpdb;

     $bblm_isediting = 0;

 ?>
      <div class="form-wrap">
       <form id="addbracket" method="post" action="" class="validate">
          <?php wp_nonce_field( 'bblm_add_bracket', 'bblm_brackets' ); ?>

          <table>
            <tr>
              <td>
 <?php

		$numteams = intval($_POST['bblm_bteams']);
    $comp_id = (int) $_POST['bblm_bcomp'];

    echo '<h2>' . __( 'Add New Tournament Brackets', 'bblm') . ' for ' . bblm_get_competition_name( $comp_id ) . '</h2>';

    echo '<input type="hidden" name="bblm_bteams" size="20" value="' . $numteams . '" />';
    echo '<input type="hidden" name="bblm_bcomp" size="20" value="' . $comp_id . '" />';

    //Capture all the matches and Fixtures for this competition to populate the drop Downs
    $fixturesql = 'SELECT F.f_id, F.div_id, T.WPID AS TAWPID, R.WPID AS TBWPID FROM '.$wpdb->prefix.'fixture F, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'team R WHERE F.f_teamA = T.t_id AND F.f_teamB = R.t_id AND F.c_id = '.$comp_id.' AND F.f_complete = 0 ORDER BY F.div_id';
    $fixturelist = '<option value="0">' . __( 'To Be Determined', 'bblm' ) . '</option>\n';
    if ( $fixtures = $wpdb->get_results( $fixturesql, ARRAY_A ) ) {
      foreach ( $fixtures as $f ) {
        $fixturelist .= '<option value="' . $f['f_id'] . '">' . bblm_get_team_name( $f['TAWPID'] ) . ' vs ' . bblm_get_team_name( $f['TBWPID'] ) . '</option>';
      }
    }

    $matchsql = 'SELECT M.WPID AS MWPID, UNIX_TIMESTAMP(M.m_date) AS mdate, M.div_id from '.$wpdb->prefix.'match M WHERE M.c_id = '.$comp_id.' ORDER BY M.div_id DESC';
    $matchlist = "";
    if ( $matches = $wpdb->get_results( $matchsql, ARRAY_A ) ) {

      //generate output into a static string
      $matchlist = '<option value="0">' . __( 'Not Applicable', 'bblm' ) . '</option>\n';
      foreach ( $matches as $m ) {
        $matchlist .= '<option value="' . $m['MWPID'] . '">' . date("d.m.Y", $m['mdate']) . ' - ' . bblm_get_match_name_score( $m['MWPID'] ) . '</option>';
      }
    }
    else {
      $matchlist .= '<option value="x">' . __( 'No matches have been played, Please select a fixture', 'bblm' ) . '</option>';
    }

    //if there are no fixtures and no matches then instruct the user to go and set some up
    if ( ( empty( $matches ) ) && ( empty( $fixtures ) ) ) {
      echo '<p>' . __( 'There are no matches or fixtures set up for this competition. Set some up frst and then return here.', 'bblm' ) . '</p>';
    }
    else {
      //There are matches or fixtires so continue

      //now we go through each round and list the available matches / fixtures
      //we know the number of matches in each round (#teams / 2) so we need to use a count to print out that now before moving onto the next comp

      $games_this_round = ( $numteams / 2 );

      while ($games_this_round >= 1) {

        $div_id = $this->bblm_return_div_id( $games_this_round );
        echo '<h3>' . $this->bblm_return_div_name( $games_this_round ) . '</h3>';

        //we want to loop through this p times for each division (round)
        $p = 1;
        while ( $p <= $games_this_round ) {

          //set the identifier for each of the options selected
          $tid = $div_id . '-' . $p;

?>
          <h4><?php echo __( 'Match', 'bblm' ) . ' ' . $p; ?></h4>
          <ul>
            <li><input type="radio" value="M" name="bblm_game-<?php echo $tid; ?>">Match: <select name="bblm_match-<?php echo $tid; ?>" id="bblm_match-<?php echo $tid; ?>"><?php echo $matchlist; ?></select></li>
            <li><input type="radio" value="F" name="bblm_game-<?php echo $tid; ?>" checked="yes">Fixture: <select name="bblm_fixture-<?php echo $tid; ?>" id="bblm_fixture-<?php echo $tid; ?>"><?php echo $fixturelist; ?></select></li>
						<li><input type="radio" value="B" name="bblm_game-<?php echo $tid; ?>"><?php echo __( 'BYE: Match is a bye (no match played)', 'bblm' ); ?></li>
          </ul>
<?php
          $p++;

        } //end while $p

        $games_this_round = ( $games_this_round/2 );
      }


 ?>
      <p class="submit"><input type="submit" name="bblm_bracket_add" id="bblm_bracket_add" value="Save Bracket" title="Save Bracket" class="button button-primary" /> or <a href="<?php echo admin_url(); ?>admin.php?page=bblm_brackets">Cancel</a></p></form>

 <?php

    }//end of if matches and fixtures ARE set

  } //end of function new_handling

	/**
	* Displays the form for editing tournament brackets
	*/
	 public function edit_handling() {
		global $wpdb;

?>
				 <div class="form-wrap">
					<form id="addbracket" method="post" action="" class="validate">
						 <?php wp_nonce_field( 'bblm_edit_bracket', 'bblm_brackets' ); ?>

<?php

		$comp_id = (int) $_GET['cid'];

		echo '<h2>' . __( 'Editing Tournament Brackets', 'bblm') . ' for ' . bblm_get_competition_name( $comp_id ) . '</h2>';

		echo '<p>' . __('Below are the brackets for this competition.', 'bblm') . '</p>';

		//display the current brackets for this competition
		BBLM_CPT_Comp::display_comp_brackets( $comp_id );

    echo '<input type="hidden" name="bblm_bcomp" size="20" value="' . $comp_id . '" />';

		//Capture all the matches and Fixtures for this competition to populate the drop Downs
    $fixturesql = 'SELECT F.f_id, F.div_id, T.WPID AS TAWPID, R.WPID AS TBWPID FROM '.$wpdb->prefix.'fixture F, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'team R WHERE F.f_teamA = T.t_id AND F.f_teamB = R.t_id AND F.c_id = '.$comp_id.' AND F.f_complete = 0 ORDER BY F.div_id';
    $fixturelist = '<option value="X">' . __( 'To Be Determined', 'bblm' ) . '</option>\n';
    $fixtures = $wpdb->get_results( $fixturesql, ARRAY_A );

    $matchsql = 'SELECT M.WPID AS MWPID, UNIX_TIMESTAMP(M.m_date) AS mdate, M.div_id from '.$wpdb->prefix.'match M WHERE M.c_id = '.$comp_id.' ORDER BY mdate ASC, M.div_id DESC';
		$matchlist = '<option value="X">' . __( 'BYE / No Match or Fixture', 'bblm' ) . '</option>\n';
    $matches = $wpdb->get_results( $matchsql, ARRAY_A );


    //if there are no fixtures and no matches then instruct the user to go and set some up
    if ( ( empty( $matches ) ) && ( empty( $fixtures ) ) ) {
      echo '<p>' . __( 'There are no matches or fixtures set up for this competition. Set some up frst and then return here.', 'bblm' ) . '</p>';
    }
    else {
      //There are matches or fixtires so continue

			//Get the list of brackets in this competition
			$bracketssql = 'SELECT C.*, D.div_name FROM '.$wpdb->prefix.'comp_brackets C, '.$wpdb->prefix.'division D WHERE C.div_id = D.div_id AND C.c_id = '.$comp_id.' ORDER BY C.div_ID DESC, cb_order ASC';
			$brackets = $wpdb->get_results( $bracketssql, ARRAY_N );

			$is_first = 1;
			$current_div = "";
			$brackettype = "";
			$p = 1;

			//Loop through each of the brackets returned
			foreach ( $brackets as $b ) {

				//If the current division does not match the one currently in the loop we need to output it
				if ( $current_div !== $b[7] ) {
					$current_div = $b[7];
					$is_first = 1;
				}

				if ( $is_first ) {
					echo '<h3>' . $current_div . '</h3>';
					$is_first = 0;
				}

				//Determine if this is a fixture or a bracket
				//If both the fixture and match is zero we have a bye or a TBD
				if ( ( 0 == $b[4] ) && ( 0 == $b[3] ) ) {
					$brackettype = "F";
				}
				//if the match is zero we have a fixture
				else if ( 0 == $b[3] ) {
					$brackettype = "F";
				}
				//If the fixture is zero we have a match
				else {
					$brackettype = "M";
				}

				//Output the fixture / match selection
				echo '<input type="hidden" name="bblm_bracketid-' . $p . '" size="20" value="' . $b[0] . '" />';
				echo '<input type="hidden" name="bblm_divid-' . $p . '" size="20" value="' . $b[2] . '" />';

				echo '<ul>';

				echo '<li><input type="radio" value="M" name="bblm_game-' . $p .'" ';
				checked( $brackettype, "M" );
				echo '>Match: <select name="bblm_match-' . $p . '" id="bblm_match-' . $p . '">' . $matchlist;
				foreach ( $matches as $m ) {
					echo '<option value="' . $m['MWPID'] . '" ' . selected( $b[3], $m['MWPID'] ) . '>' . date("d.m.Y", $m['mdate']) . ' - ' . bblm_get_match_name_score( $m['MWPID'] ) . '</option>';
				}
				echo '</select></li>';

				echo '<li><input type="radio" value="F" name="bblm_game-' . $p .'" ';
				checked( $brackettype, "F" );
				echo '>Fixture: <select name="bblm_fixture-' . $p .'" id="bblm_fixture-' . $p .'">' . $fixturelist;
				foreach ( $fixtures as $f ) {
					echo '<option value="' . $f['f_id'] . '" ' . selected( $b[4], $f['f_id'] ) . '>' . bblm_get_team_name( $f['TAWPID'] ) . ' vs ' . bblm_get_team_name( $f['TBWPID'] ) . '</option>';
				}
				echo '</select></li>';
				echo '</ul>';

				$p++;
			} //end of foreach through each bracket
			echo '<input type="hidden" name="bblm_numgames" size="20" value="' . $p . '" />';
 ?>
      <p class="submit"><input type="submit" name="bblm_bracket_edit" id="bblm_bracket_edit" value="Save changes to Brackets" title="Save changes to Brackets" class="button button-primary" /> or <a href="<?php echo admin_url(); ?>admin.php?page=bblm_brackets"><?php echo __( 'Return','bblm' ); ?></a></p></form>
 <?php

    }//end of if matches and fixtures ARE set

?>
					</form>
				</div>
<?php
	} //end of function edit_handling

   /**
   * handles the submission of a new brackete
   */
    public function submit_handling_new() {
      global $wpdb;

			// Verify nonce
			if ( !isset( $_POST['bblm_bracket_add'] ) || !isset( $_POST['bblm_brackets'] ) || !wp_verify_nonce( $_POST['bblm_brackets'], 'bblm_add_bracket' ) ) {
				return false;
			}

			$insertsql = 'INSERT INTO '.$wpdb->prefix.'comp_brackets (`cb_id`, `c_id`, `div_id`, `m_id`, `f_id`, `cb_text`, `cb_order`) VALUES';
			//Initialize var to capture first input
			$is_first_bracket = 1;

			$games_this_round = ( $_POST['bblm_bteams'] / 2 );

			while ( $games_this_round >= 1 ) {
				$div_id = $this->bblm_return_div_id( $games_this_round );
				$bblm_tbd_team = bblm_get_tbd_team();

				//we want to loop through this p times for each division (round)
				$p = 1;
				while ( $p <= $games_this_round ) {
					$match_text = "";

					//check to see if a match_id was submitted
					if ( "F"== $_POST['bblm_game-'.$div_id.'-'.$p] ) {
						$match_id = 0;
						$fixture_id = $_POST['bblm_fixture-'.$div_id.'-'.$p];
						if ( 0 == $fixture_id ) {
							$match_text = __( 'To Be Determined','bblm' );
						}
						else {

							//check to see if either team_id matches the default TBD and build the link string.
							$fixturesql = 'SELECT F.f_teamA AS TA, F.f_teamB AS TB, T.WPID AS tAWPID, Y.WPID AS tBWPID FROM '.$wpdb->prefix.'fixture F, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'team Y WHERE F.f_teamA = T.t_id AND F.f_teamB = Y.t_id AND F.f_id = '. $fixture_id;
							$bblm_tbd_team = bblm_get_tbd_team();

							if ( $fixture = $wpdb->get_row( $fixturesql ) ) {

								if ( $bblm_tbd_team == $fixture->TA ) {
									$match_text .= __( 'To Be Determined', 'bblm' );
								}
								else {
									$match_text .= bblm_get_team_name( $fixture->tAWPID );
								}
								$match_text .= " vs <br/>";
								if ($bblm_tbd_team == $fixture->TB) {
									$match_text .= __( 'To Be Determined', 'bblm' );
								}
								else {
									$match_text .= bblm_get_team_name( $fixture->tBWPID );
								}
								$match_text = esc_sql( $match_text );
							}//end of if SQL

						}

					} //end of if fixture
					//Check for BYEs, include error handling for Match being set as X
					else if ( ( "B"== $_POST['bblm_game-'.$div_id.'-'.$p] ) || ('x' == $match_id) ) {
						$match_id = 0;
						$fixture_id = 0;
						$match_text = "&nbsp;";

					} //end of if Byes
					else {
						$match_id = $_POST['bblm_match-'.$div_id.'-'.$p];
						$fixture_id = 0;
						$match_text = bblm_get_match_link_score( $match_id, 2 );
					}//end of if match
					$match_text = esc_sql( $match_text );
					//we only want a comma added for all but the first
					if (1 !== $is_first_bracket) {
						$insertsql .= ",";
					}

					$insertsql .= ' (\'\', \''.intval( $_POST['bblm_bcomp'] ).'\', \''.$div_id.'\', \''.$match_id.'\', \''.$fixture_id.'\', \''.$match_text.'\', \''.$p.'\')';

					$p++;
					$is_first_bracket = 0;
				} //end while $p

				$games_this_round = ($games_this_round/2);

			} //end of while

      $sucess = "";
      if ( FALSE !== $wpdb->query( $insertsql ) ) {
        $sucess = TRUE;
        do_action( 'bblm_post_submission' );
      }

 ?>
   <div id="updated" class="notice notice-success">
     <p>
       <?php
       if ( $sucess ) {
         echo __( 'Tournament Brackets were added' , 'bblm' );
       }
       else {
        echo __( 'Something went wrong', 'bblm' );
       }
       ?>
     </p>
   </div>
 <?php
} //end of submit_handling_new

   /**
     * handles the submission of updates for an Tournament Bracket
     */
     public function submit_handling_update() {
       global $wpdb;

        // Verify nonce
        if ( !isset( $_POST['bblm_bracket_edit'] ) || !isset( $_POST['bblm_brackets'] ) || !wp_verify_nonce( $_POST['bblm_brackets'], 'bblm_edit_bracket' ) ) {
          return false;
        }

				$p = 1;
				$pmax = (int) $_POST['bblm_numgames'];

				$bracketsqla = array();

				while ($p < $pmax){

					//Check for "To Be Determined". match set to Fixture and Fixture is X
					if ( ( 0 < (int) $_POST['bblm_fixture-'.$p] ) && ( "F" == $_POST['bblm_game-'.$p] ) ) {
						$_POST['bblm_match-'.$p] = 0;
					}
					else if ( ( 0 < (int) $_POST['bblm_match-'.$p] ) && ( "M" == $_POST['bblm_game-'.$p] ) ) {
						$_POST['bblm_fixture-'.$p] = 0;
					}
					else if ( ( "X" == $_POST['bblm_fixture-'.$p] ) && ( "F" == $_POST['bblm_game-'.$p] ) ) {
						$_POST['bblm_match-'.$p] = 0;
					}
					//if both the fixture and match are empty, this was a BYE round
					else if ( ( "X" == $_POST['bblm_match-'.$p] ) && ( "M" == $_POST['bblm_game-'.$p] ) ) {
						$_POST['bblm_fixture-'.$p] = 0;
					}

					if ( BBLM_Admin_CPT_Competition::update_bracket_text( intval( $_POST['bblm_bracketid-'.$p] ), $_POST['bblm_match-'.$p], $_POST['bblm_fixture-'.$p] ) ) {
						$sucess = TRUE;
						do_action( 'bblm_post_submission' );
					}
					$p++;
				}

 ?>
      <div id="updated" class="notice notice-success">
        <p>
 <?php
        if ( $sucess ) {
          echo __( 'Brackets have been updated for this competition' , 'bblm' );
        }
        else {
          echo __( 'Something went wrong', 'bblm' );
        }
 ?>
        </p>
      </div>
  <?php
      } //emnd of submit_handling_update


  /** Singleton instance */
  public static function get_instance() {
    if ( ! isset( self::$instance ) ) {
      self::$instance = new self();
    }

    return self::$instance;
  }

  public function bblm_return_div_id( $games ) {
  //function takes in the number of games this round and returns the matching ID from the database.
  	switch ($games) {
  		case (1 == $games):
  	    	return 1;
  		    break;
  		case (2 == $games):
  	    	return 3;
  		    break;
  		case (4 == $games):
  	    	return 4;
  		    break;
  		case (8 == $games):
  	    	return 5;
  		    break;
  		case (16 == $games):
  	    	return 7;
  		    break;
  	}
  }
  public function bblm_return_div_name( $games ) {
  //function takes in the number of games this round and returns the matching name from the database.
  	switch ($games) {
  		case (1 == $games):
  	    	return "Final";
  		    break;
  		case (2 == $games):
  	    	return "Semi Final";
  		    break;
  		case (4 == $games):
  	    	return "Quarter-Final";
  		    break;
  		case (8 == $games):
  	    	return "Second Round";
  		    break;
  		case (16 == $games):
  	    	return "Opening Round";
  		    break;
  	}
  }

 }


 add_action( 'plugins_loaded', function () {
  BBLM_Manage_Brackets::get_instance();
 } );
