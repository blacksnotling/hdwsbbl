<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Two classes
 * The first hanbdles the generation of the Fixtures table,
 * THe Second inputs and displays the data and handles the form and POST / GET action
 *
 * @class 		BBLM_Fixtures_list
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/Admin
 * @version   1.0
 */

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class BBLM_Fixtures_List extends WP_List_Table {

	/** Class constructor */
	public function __construct() {

		parent::__construct( [
			'singular' => __( 'Fixture', 'bblm' ), //singular name of the listed records
			'plural'   => __( 'Fixtures', 'bblm' ), //plural name of the listed records
			'ajax'     => false //does this table support ajax?
		] );

	}


	/**
	 * Retrieve Fixture data from the database
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */
	public static function get_fixtures( $per_page = 25, $page_number = 1 ) {

		global $wpdb;

		$sql = "SELECT F.*, D.div_name, UNIX_TIMESTAMP(F.f_date) AS fdate, T.WPID AS teamA, S.WPID AS teamB FROM {$wpdb->prefix}fixture F, {$wpdb->prefix}division D, {$wpdb->prefix}team T, {$wpdb->prefix}team S WHERE F.f_teamA = T.t_id AND F.f_teamB  = S.t_id AND D.div_id = F.div_id AND f_complete = 0";
    if ( ( isset( $_REQUEST[ 'bblm_filter' ] ) ) && ( 0 !== absint( $_REQUEST[ 'bblm_filter' ] ) ) ) {

      $sql .= " AND c_id = ". absint( $_REQUEST[ 'bblm_filter' ] ) ;

    }

    $sql .= " ORDER BY f_date ASC, f_id ASC";
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

		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}fixture WHERE f_complete = 0";

		return $wpdb->get_var( $sql );
	}


	/** Text displayed when no fixture data is available */
	public function no_items() {

		_e( 'No Fixtures avaliable.', 'bblm' );

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

			case 'date';
				return date("d-m-Y", $item[ 'fdate' ] );

			case 'comp';
				return bblm_get_competition_name( $item[ 'c_id' ] );

			case 'div';
				return $item[ 'div_name' ];

			case 'status';
				return "To be played";

			case 'edit';
			return '<a href="'.admin_url().'admin.php?page=bblm_fixtures&bblm_action=edit&fid='.$item[ 'f_id' ].'">Edit</a>';

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
			'<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['f_id']

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
      'date'    => __( 'Date', 'bblm' ),
			'name'    => __( 'Fixture', 'bblm' ),
      'comp' => __( 'Competition', 'bblm' ),
			'div'    => __( 'Division', 'bblm' ),
      'status'    => __( 'Status', 'bblm' ),
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
			'name' => array( 'date', true ),
      'date' => array( 'name', true ),
			'comp' => array( 'comp', true ),
			'div' => array( 'div', true ),
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
			'bulk-delete' => 'Delete'
		];

		return $actions;
	}

	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {

		$this->_column_headers = $this->get_column_info();

		/** Process bulk action */
		$this->process_bulk_action();

		$per_page     = $this->get_items_per_page( 'fixtures_per_page', 25 );
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();

		$this->set_pagination_args( [
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $per_page //WE have to determine how many items to show on a page
		] );

		$this->items = self::get_fixtures( $per_page, $current_page );
	}

	public function process_bulk_action() {
		global $wpdb;

		// If the delete bulk action is triggered
		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
		     || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
		) {

			//verify the nonce
			if ( !wp_verify_nonce( $_POST['bblm_fixtures'], 'bblm_delete_fixture' ) ) {
				return $post_id;
			}

			else {
				$delete_ids = esc_sql( $_POST['bulk-delete'] );

				// loop over the array of record IDs and deactivate them
				$sucess = "";
				foreach ( $delete_ids as $id ) {

					if ( BBLM_Admin_CPT_Competition::update_fixture_complete( $id ) ) {
						$sucess = TRUE;
						do_action( 'bblm_post_submission' );
					}

				}

?>
				<div id="updated" class="notice notice-success">
					<p>
						<?php
						if ( $sucess ) {
							echo __( 'Fixture(s) was removed' , 'bblm' );
						}
						else {
							echo __( 'Something went wrong', 'bblm' );
						}
						?>
					</p>
				</div>
<?php

			} //end of else

		} //end of if action set

	} //end of process_bulk_action()

} //end of class

/**
 *
 * @class 		BBLM_Manage_Fixtures
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/Admin
 * @version   1.0
 */

class BBLM_Manage_Fixtures {

	// class instance
	static $instance;

	// Fixtures WP_List_Table object
	public $fixture_obj;

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
      __( 'Fixtures', 'bblm' ),
      __( 'Fixtures', 'bblm' ),
      'manage_options',
      'bblm_fixtures',
      array( $this, 'manage_fixtures_page' )
    );

		add_action( "load-$hook", array( $this, 'screen_option' ) );

	}


	/**
	 * The Output of the Page
	 */
	public function manage_fixtures_page() {
		global $wpdb;
?>
		<div class="wrap">
      <h1 class="wp-heading-inline"><?php echo __( 'Manage Fixtures', 'bblm' ); ?></h1>

<?php
    if ( isset( $_POST['bblm_fixture_add'] ) ) {

      //If we have submitted a new fixture then pass to the correct function to save
      $this->submit_handling_new();

    }

		if ( isset( $_POST['bblm_fixture_edit'] ) ) {

      //If we have submitted an updated fixture then pass to the correct function to save
      $this->submit_handling_update();

    }

		if ( isset( $_POST['bblm_comp_select'] ) || ( isset( $_GET['bblm_action'] ) && 'edit' == $_GET['bblm_action'] ) ) {

      //we are adding or updating a postion so we pass to the correct function
      $this->neworupdate_handling();

    }
		else {
      //we are displaying the overall form
?>
			<div id=fixtureaddform>
				<h2><?php echo __( 'Add Fixture(s)', 'bblm' ); ?></h2>
				<div id="addpost-body" class="metabox-holder columns-3">
					<div id="addpost-body-content">

						<form name="bblm_selectcomp" method="post" id="bblm_selectcomp">
							<p><?php echo __( 'Only those competitions which have a team assigned as listed below. If the Competition you want is missing then please assign some teams.', 'bblm' ); ?></p>

							<table class="form-table">
								<tr valign="top">
									<th scope="row"><label for="bblm_fcomp"><?php echo __( 'Competition', 'bblm' ); ?>:</label></th>
									<td><select name="bblm_fcomp" id="bblm_fcomp">
<?php
						$compssql = 'SELECT DISTINCT c_id FROM '.$wpdb->prefix.'team_comp ORDER BY c_id DESC';
						if ( $comps = $wpdb->get_results( $compssql ) ) {
							foreach ( $comps as $comp ) {
								print("					<option value=\"" . $comp->c_id . "\">" . bblm_get_competition_name( $comp->c_id ) . "</option>\n");
							}
						}
?>
									</select></td>
								</tr>
								<tr valign="top">
									<th scope="row"><label for="bblm_fdiv"><?php echo __( 'Division', 'bblm' ); ?>:</label></th>
									<td><select name="bblm_fdiv" id="bblm_fdiv">
<?php
						$divsql = 'SELECT * FROM '.$wpdb->prefix.'division ORDER BY div_id ASC';
						if ( $divs = $wpdb->get_results( $divsql ) ) {
							foreach ( $divs as $div ) {
								print("					<option value=\"".$div->div_id."\">".$div->div_name."</option>\n");
							}
						}
?>
									</select></td>
								</tr>
								<tr valign="top">
									<th scope="row"><label for="bblm_fgames"><?php echo __( 'Number of fixtures you wish to add', 'bblm' ); ?>:</label></th>
									<td><input type="text" name="bblm_fgames" size="3" value="1" id="bblm_fgames" maxlength="2"></td>
								</tr>
							</table>

							<p class="submit"><input type="submit" name="bblm_comp_select" id="bblm_comp_select" class="button-primary" value="Continue to set-up new Fixtures" title="Continue to set-up new Fixtures"  /></p>

						</form>

					</div>
				</div>
			</div>


			<div id="poststuff">
				<h2><?php echo __( 'Edit Existing Fixture(s)', 'bblm' ); ?></h2>
				<div id="post-body" class="metabox-holder columns-3">
					<div id="post-body-content">

						<div class="meta-box-sortables ui-sortable">

							<form method="post" action="">
<?php
							//set a nonce for the bulk actions
							wp_nonce_field( 'bblm_delete_fixture', 'bblm_fixtures' );
		          $this->fixture_obj->prepare_items();
			        $this->fixture_obj->display();
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
			'label'   => 'Fixtures',
			'default' => 25,
			'option'  => 'fixtures_per_page'
		);

		add_screen_option( $option, $args );

		$this->fixture_obj = new BBLM_Fixtures_List();
	}

  /**
	 * Displays the form for adding and updating a new Fixture
	 */
   public function neworupdate_handling() {
		 global $wpdb;

		 $bblm_isediting = 0;
		 //ininalise array to hold the details of the Fixture
		 $pos = array( array(
			 'f_id' => '',
			 'c_id' => '',
			 'div_id' => '',
			 'f_date' => '',
			 'f_teamA' => '',
			 'f_teamB' => '',
			 'f_complete' => '',
		 ),
	 		);

?>
     <div class="form-wrap">
			 <form id="addfixture" method="post" action="" class="validate">
         <?php wp_nonce_field( 'bblm_add_fixture', 'bblm_fixtures' ); ?>

         <table>
           <tr>
             <td>
<?php
		if ( 'edit' == $_GET['bblm_action'] && isset( $_GET['fid'] ) ) {
			//we are editing a Fixture

			$bblm_isediting = 1;
			echo '<h2>' . __( 'Edit Fixture', 'bblm') . '</h2>';

			//retrieve the fixtures details from the database and populate an array
			$sql = "SELECT *, UNIX_TIMESTAMP(f_date) AS fdate  FROM ".$wpdb->prefix."fixture where f_id = ".absint( $_GET['fid'] );
			$fix = $wpdb->get_results( $sql, 'ARRAY_A' );

			$countmax = 1;
			$fixdiv = (int) $fix[0][ 'div_id' ];
			$fixcomp = (int) $fix[0][ 'c_id' ];

		}
		else {
			//we are adding a new Fixture

			echo '<h2>' . __( 'Add New Fixture(s)', 'bblm') . '</h2>';

			$countmax = (int) $_POST['bblm_fgames'];
			$fixdiv = (int) $_POST['bblm_fdiv'];
			$fixcomp = (int) $_POST['bblm_fcomp'];

		}

		echo '<input type="hidden" name="bblm_fgames" size="20" value="' . $countmax . '" />';
		echo '<input type="hidden" name="bblm_fcomp" size="20" value="' . $fixcomp . '" />';
		echo '<input type="hidden" name="bblm_fdiv" size="20" value="' . $fixdiv . '" />';

		//before we generate the list of fixtures, we need to grab the teams into an array
		if (13 == $fixdiv) {
			//Cross Division has been selected, All the teams in the compeition are slected
			$teamsql = "SELECT T.t_id, T.WPID AS TWPID FROM ".$wpdb->prefix."team T, ".$wpdb->prefix."team_comp C WHERE T.t_id = C.t_id AND C.c_id = " . $fixcomp;
		}
		else {
			//Just select the temas in this division
			$teamsql = "SELECT T.t_id, T.WPID AS TWPID FROM ".$wpdb->prefix."team T, ".$wpdb->prefix."team_comp C WHERE T.t_id = C.t_id AND C.c_id = " . $fixcomp . " AND C.div_id = " . $fixdiv;
		}
		$teams = $wpdb->get_results( $teamsql, ARRAY_A );

		if ( empty( $teams ) ) {
			echo '<p>'. __( 'No Teams have been entered into this stage of the Competition. You will need to add some first!', 'bblm').'</p>';
		}
		else {
			//generate output into a static string

			//Grab the ID of the 'To Be Determined Team'
			$bblm_tbd_team = bblm_get_tbd_team();

			if ( $bblm_isediting ) {

				$teamlistA = "";
				$teamlistA .= "<option value=\"" . $bblm_tbd_team . "\">To be Determined</option>\n";
				foreach ($teams as $t) {
					$teamlistA .= "<option ";
					if ( (int) $t['t_id'] == (int) $fix[0][ 'f_teamA' ] ) {
						$teamlistA .= 'selected ';
					}
					$teamlistA .= "value=\"" . $t['t_id'] . "\">" . bblm_get_team_name( $t['TWPID'] ) . "</option>\n";
				}

				$teamlistB = "";
				$teamlistB .= "<option value=\"" . $bblm_tbd_team . "\">To be Determined</option>\n";
				foreach ($teams as $t) {
					$teamlistB .= "<option ";
					if ( (int) $t['t_id'] == (int) $fix[0][ 'f_teamB' ] ) {
						$teamlistB .= 'selected ';
					}
					$teamlistB .= "value=\"" . $t['t_id'] . "\">" . bblm_get_team_name( $t['TWPID'] ) . "</option>\n";
				}

			}
			else {

				$teamlist = "";
				$teamlist .= "<option value=\"" . $bblm_tbd_team . "\">To be Determined</option>\n";
				foreach ($teams as $t) {
					$teamlist .= "<option value=\"" . $t['t_id'] . "\">" . bblm_get_team_name( $t['TWPID'] ) . "</option>\n";
				}
			}
			//Now we set our counter up
			$p = 1;

			//continue with page generation
?>
			<table class="widefat">
				<thead>
					<tr>
<?php
						if ( $bblm_isediting ) {
							echo '<th>' . __( 'Edit', 'bblm' ) . '</th>';
						}
						else {
							echo '<th>' . __( 'Add', 'bblm' ) . '</th>';
						}
?>
						<th><?php echo __( 'Home', 'bblm' ); ?></th>
						<th><?php echo __( 'Away', 'bblm' ); ?></th>
						<th><?php echo __( 'Date', 'bblm' ); ?></th>
					</tr>
				</thead>
				<tbody>

<?php
			while ( $p < ( $countmax+1 ) ) {
?>
					<tr>
						<td><input type="checkbox" checked="checked" name="bblm_fadd<?php echo $p; ?>"></td>
<?php
				if ( $bblm_isediting ) {
					//We print a slightly different team listing with the teams preselected
?>
						<td><select name="bblm_teamA<?php echo $p; ?>" id="bblm_teamA<?php echo $p; ?>"><?php echo $teamlistA; ?></select></td>
						<td><select name="bblm_teamB<?php echo $p; ?>" id="bblm_teamB<?php echo $p; ?>"><?php echo $teamlistB; ?></select></td>
						<td><input name="fdate<?php echo $p; ?>" id="fdate<?php echo $p; ?>" type="text" size="12" maxlength="10" value="<?php echo date( 'Y-m-d', $fix[0][ 'fdate' ] ); ?>" class="custom_date">
						<input type="hidden" name="fid<?php echo $p; ?>" size="20" value="<?php echo $fix[0][ 'f_id' ]?>" /></td>
<?php
				}
				else {
?>
						<td><select name="bblm_teamA<?php echo $p; ?>" id="bblm_teamA<?php echo $p; ?>"><?php echo $teamlist; ?></select></td>
						<td><select name="bblm_teamB<?php echo $p; ?>" id="bblm_teamB<?php echo $p; ?>"><?php echo $teamlist; ?></select></td>
						<td><input name="fdate<?php echo $p; ?>" id="fdate<?php echo $p; ?>" type="text" size="12" maxlength="10" value="<?php echo date( 'Y-m-d', strtotime('next thursday' ) ); ?>" class="custom_date"></td>
<?php
				}
?>
					</tr>
<?php
				$p++;
			} //emd of while
?>
				</tbody>
			</table>

<?php
			//Only display these if we are editing an existing fixture
			if ( $bblm_isediting ) {
?>
			<p class="submit"><input type="submit" name="bblm_fixture_edit" id="bblm_fixture_edit" value="Save changes to Fixture" title="Save changes to Fixture" class="button button-primary" /> or <a href="<?php echo admin_url(); ?>admin.php?page=bblm_fixtures">Return</a></p></form>
<?php
			} // end of the "if editing" chunk
			else {
				//We are adding a fixture so show the add fixture button
?>
			<p class="submit"><input type="submit" name="bblm_fixture_add" id="bblm_fixture_add" value="Add Fixture" title="Add Fixtiure" class="button button-primary" /> or <a href="<?php echo admin_url(); ?>admin.php?page=bblm_fixtures">Cancel</a></p></form>

<?php
			}//end of else not editing

		} //end else teams are assigned to the competition

	} //end of function

  /**
	 * handles the submission of a new fixture
	 */
   public function submit_handling_new() {
     global $wpdb;

      // Verify nonce
   		if ( !isset( $_POST['bblm_fixture_add'] ) || !isset( $_POST['bblm_fixtures'] ) || !wp_verify_nonce( $_POST['bblm_fixtures'], 'bblm_add_fixture' ) ) {
   			return false;
   		}

			$insertsql = 'INSERT INTO `'.$wpdb->prefix.'fixture` (`f_id`, `c_id`, `div_id`, `f_date`, `f_teamA`, `f_teamB`, `f_complete`) VALUES ';
			$p = 1;
			$is_first_fixture = 1;
			while ($p < ($_POST['bblm_fgames']+1)) {
				//we only want a comma added for all but the first
				if ($_POST['bblm_fadd'.$p]) {
					if (1 !== $is_first_fixture) {
						$insertsql .= ", ";
					}
					$insertsql .= '(\'\', \'' . (int) $_POST['bblm_fcomp'] . '\', \''. (int) $_POST['bblm_fdiv'] . '\', \''. $_POST['fdate'.$p] .' 12:00:01\', \'' . (int) $_POST['bblm_teamA'.$p] . '\', \'' . (int) $_POST['bblm_teamB'.$p] . '\', \'0\')';
				}

				$p++;
				$is_first_fixture = 0;
			}

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
        echo __( 'Fixture(s) was added' , 'bblm' );
      }
      else {
				echo __( 'Something went wrong', 'bblm' );
      }
      ?>
    </p>
  </div>
<?php
   }

	 /**
 	 * handles the submission of updates for an fixture
 	 */
    public function submit_handling_update() {
      global $wpdb;

       // Verify nonce
    		if ( !isset( $_POST['bblm_fixture_edit'] ) || !isset( $_POST['bblm_fixtures'] ) || !wp_verify_nonce( $_POST['bblm_fixtures'], 'bblm_add_fixture' ) ) {
    			return false;
    		}

				//NOTE: the loop is required because the form is shared with add fixture so multiple values can be added
				$p = 1;
				$pmax = (int) $_POST['bblm_fgames'];
				//define array to hold playerupdate sql
				$fixturesqla = array();

				while ($p <= $pmax){
					//if  "on" result in "changed" then generate SQL
					if ("on" == $_POST['bblm_fadd'.$p]) {
						$updatesql = 'UPDATE `'.$wpdb->prefix.'fixture` SET `f_date` = \''.$_POST['fdate'.$p].' 12:00:01\', `f_teamA` = \'' . (int) $_POST['bblm_teamA'.$p] . '\', `f_teamB` = \'' . (int) $_POST['bblm_teamB'.$p] . '\' WHERE `f_id` = ' . (int) $_POST['fid'.$p] . ' LIMIT 1';
						$fixturesqla[$p] = $updatesql;

						//Check to see if this fixture is part of a tournament
						$checkbracketssql = 'SELECT cb_id FROM '.$wpdb->prefix.'comp_brackets WHERE f_id = ' . (int) $_POST['fid'.$p];
						$cb_id = $wpdb->get_var( $checkbracketssql );

						if ( !empty( $cb_id ) ) {
							BBLM_Admin_CPT_Competition::update_bracket_text( $cb_id, 0, (int) $_POST['fid'.$p] );
						}
					}
					$p++;
				}

				$sucess = "";
				foreach ($fixturesqla as $fs) {
					if ( FALSE !== $wpdb->query( $fs ) ) {
						$sucess = TRUE;
					}
				}

?>
			<div id="updated" class="notice notice-success">
				<p>
<?php
				if ( $sucess ) {
					echo __( 'Fixture was updated' , 'bblm' );
					//We only want to call this once to avoid excessive loads
					do_action( 'bblm_post_submission' );
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

}


add_action( 'plugins_loaded', function () {
	BBLM_Manage_Fixtures::get_instance();
} );
