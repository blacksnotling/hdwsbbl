<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Two classes
 * The first hanbdles the generation of the Matches table,
 * THe Second inputs and displays the data and handles the form and POST / GET action
 *
 * @class 		BBLM_Match_list
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/Admin
 * @version   1.0
 */

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class BBLM_Match_List extends WP_List_Table {

	/** Class constructor */
	public function __construct() {

		parent::__construct( [
			'singular' => __( 'Match', 'bblm' ), //singular name of the listed records
			'plural'   => __( 'Matchs', 'bblm' ), //plural name of the listed records
			'ajax'     => false //does this table support ajax?
		] );

	}


	/**
	 * Retrieve Match data from the database
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */
	public static function get_matches( $per_page = 25, $page_number = 1 ) {

		global $wpdb;

		$sql = 'SELECT M.m_id, D.div_name, M.c_id, UNIX_TIMESTAMP(M.m_date) AS mdate, M.m_teamAtd, M.m_teamBtd, M.m_teamAcas, M.m_teamBcas, J.PID FROM '.$wpdb->prefix.'match M, '.$wpdb->prefix.'bb2wp J, '.$wpdb->prefix.'division D WHERE M.div_id = D.div_ID AND M.m_id = J.tid AND J.prefix=\'m_\'';
    if ( ( isset( $_REQUEST[ 'bblm_filter' ] ) ) && ( 0 !== absint( $_REQUEST[ 'bblm_filter' ] ) ) ) {

      $sql .= " AND c_id = ". absint( $_REQUEST[ 'bblm_filter' ] ) ;


    }

    $sql .= " ORDER BY m_date DESC, c_id ASC";
		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;


		$result = $wpdb->get_results( $sql, 'ARRAY_A' );

		return $result;
	}

	/**
	 * Returns the count of records in the database for pagnation
	 *
	 * @return null|string
	 */
	public static function record_count() {
		global $wpdb;

		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}match";

		return $wpdb->get_var( $sql );
	}


	/** Text displayed when no Match data is available */
	public function no_items() {

		_e( 'No Matches avaliable.', 'bblm' );

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

			case 'date':
			return date("d-m-Y (25y)", $item[ 'mdate' ] );

			case 'name':
			return get_the_title( $item[ 'PID' ] );

			case 'comp':
			return bblm_get_competition_name( $item[ 'c_id' ] );

			case 'div':
			return $item[ 'div_name' ];

			case 'edit':
			return '<a href="'.admin_url().'admin.php?page=bblm_edit_match&bblm_action=edit&match='.$item[ 'm_id' ].'">Edit</a>';

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
			'<input type="checkbox" name="bulk-delete[]" value="%s" />', 'null'

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

		$delete_nonce = wp_create_nonce( 'bblm_delete_match' );

		$title = '<strong>' . get_the_title( $item[ 'PID' ] ) . '</a></strong>';

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
      'name' => __( 'Match', 'bblm' ),
      'comp' => __( 'Competition', 'bblm' ),
			'div'    => __( 'Division', 'bblm' ),
			'edit'    => __( 'Edit Match', 'bblm' ),
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
			'date' => array( 'date', true ),
      'comp' => array( 'comp', true ),
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
		$this->process_bulk_action();

		$per_page     = $this->get_items_per_page( 'matches_per_page', 25 );
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();

		$this->set_pagination_args( [
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $per_page //WE have to determine how many items to show on a page
		] );

		$this->items = self::get_matches( $per_page, $current_page );
	}

	public function process_bulk_action() {

		//Detect when a bulk action is being triggered...
		if ( 'N/A' === $this->current_action() ) {

			//Not implemented, just return back to the page

			// esc_url_raw() is used to prevent converting ampersand in url to "#038;"
			// add_query_arg() return the current url
			wp_redirect( esc_url_raw(add_query_arg()) );
			exit;

		}

	}

}

/**
 *
 * @class 		BBLM_Edit_Match
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/Admin
 * @version   1.0
 */

class BBLM_Edit_Match {

	// class instance
	static $instance;

	// Matches WP_List_Table object
	public $match_obj;

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
      __( 'Edit Match', 'bblm' ),
      __( 'Edit Match', 'bblm' ),
      'manage_options',
      'bblm_edit_match',
      array( $this, 'edit_match_page' )
    );

		add_action( "load-$hook", array( $this, 'screen_option' ) );

	}


	/**
	 * The Output of the Page
	 */
	public function edit_match_page() {
?>
		<div class="wrap">
      <h1 class="wp-heading-inline"><?php echo __( 'Edit Match', 'bblm' ); ?></h1>
 			<a href="<?php echo admin_url(); ?>admin.php?page=bblm_add_match" class="page-title-action">Add Match</a>

<?php

		if ( isset( $_POST['bblm_match_edit'] ) ) {

      //If we have submitted an updated match then pass to the correct function to save
      $this->submit_handling_update();

    }

		if ( isset( $_GET['bblm_action'] ) && 'edit' == $_GET['bblm_action'] ) {

      //we are updating a match so we pass to the correct function
      $this->update_handling();

    }
		else {
      //we are displaying the overall form
?>
    <p><form id="filtercompbutton" method="post" action="">
			<select name="bblm_filter" id="bblm_filter">
				<option value="x">Show all Competitions</option>
<?php
				$oposts = get_posts(
					array(
						'post_type' => 'bblm_comp',
						'numberposts' => -1,
						'orderby' => 'ID',
						'order' => 'DESC'
					)
				);
				if( ! $oposts ) return;
				foreach( $oposts as $o ) {
					echo '<option value="' . $o->ID . '">' . bblm_get_competition_name( $o->ID ) . '</option>';
				}
?>
			</select>

      <input type="submit" name="bblm_comp_filter" id="bblm_comp_filter" value="Filter" title="Filter on the selected Competition" class="page-title-action" /></form></p>

			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-3">
					<div id="post-body-content">
						<div class="meta-box-sortables ui-sortable">

							<form method="post" action="">
<?php
		          $this->match_obj->prepare_items();
			        $this->match_obj->display();
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
			'label'   => 'Matches',
			'default' => 25,
			'option'  => 'matches_per_page'
		);

		add_screen_option( $option, $args );

		$this->match_obj = new BBLM_Match_List();
	}

  /**
	 * Displays the form for updating a match
	 */
   public function update_handling() {
		 global $wpdb;

		 $bblm_isediting = 0;
		 //ininalise array to hold the details of the Matches

?>
		<div class="notice"><p><strong><?php echo __( 'You cannot edit the following via this page: TV, Treasury, Fan Factor', 'bblm' ); ?></strong></p></div>
		<div class="form-wrap">
			<form id="bblm_edit_match_form" method="post" action="" class="validate">
			<?php wp_nonce_field( 'bblm_edit_match', 'bblm_matches' ); ?>

<?php
		if ( 'edit' == $_GET['bblm_action'] && isset( $_GET['match'] ) ) {
			//we are editing a Match

			$bblm_isediting = 1;
			$match_id = (int) $_GET['match'];

			//retrieve the match and team details from the database and populate an array
			$matchsql = "SELECT M.m_id, M.m_gate, UNIX_TIMESTAMP(M.m_date) AS mdate, M.m_teamA, M.m_teamB, M.stad_id, M.c_id, D.div_id, D.div_name, M.weather_id, M.weather_id2, M.m_trivia FROM ".$wpdb->prefix."match M, ".$wpdb->prefix."division D WHERE M.div_id = D.div_id AND M.m_id = ".$match_id;
			$m = $wpdb->get_row( $matchsql );
			$teamAsql = "SELECT M.*, T.WPID AS TWPID FROM ".$wpdb->prefix."match_team M, ".$wpdb->prefix."team T WHERE M.t_id = T.t_id AND M.m_id = ".$match_id." AND M.t_id = ".$m->m_teamA;
			$mA = $wpdb->get_row( $teamAsql );
			$teamBsql = "SELECT M.*, T.WPID AS TWPID FROM ".$wpdb->prefix."match_team M, ".$wpdb->prefix."team T WHERE M.t_id = T.t_id AND M.m_id = ".$match_id." AND M.t_id = ".$m->m_teamB;
			$mB = $wpdb->get_row( $teamBsql );


?>
				<ul>
					<li><strong><?php echo __( 'Competition', 'bblm'); ?></strong>: <?php echo bblm_get_competition_name( $m->c_id ); ?></li>
					<li><strong><?php echo __( 'Division', 'bblm'); ?></strong>: <?php echo $m->div_name; ?></li>
				</ul>

				<table>
					<thead>
						<tr>
							<th>&nbsp;</th>
							<th><?php echo __( 'Home', 'bblm'); ?></th>
							<th>&nbsp;</th>
							<th><?php echo __( 'Away', 'bblm'); ?></th>
							<th>&nbsp;</th>
						</tr>
					</thead>
					<tbody>
						<tr>
								<td><?php echo __( 'Teams', 'bblm'); ?></td>
							<td><strong><?php echo bblm_get_team_name( $mA->TWPID ); ?></strong><input name="bblm_teama" type="hidden" value="<?php echo $mA->t_id; ?>"></td>
							<td>vs</td>
							<td><strong><?php echo bblm_get_team_name( $mB->TWPID ); ?></strong><input name="bblm_teamb" type="hidden" value="<?php echo $mB->t_id; ?>"></td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td><?php echo __( 'Team Value:', 'bblm' ); ?></td>
							<td><?php echo number_format( $mA->mt_tv) ; ?></td>
							<td>vs</td>
							<td><?php echo number_format( $mB->mt_tv) ; ?></td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td><?php echo __( 'Date', 'bblm' ); ?></td>
							<td colspan="3"><input name="mdate" type="text" size="12" maxlength="10" value="<?php echo date("Y-m-d", $m->mdate ); ?>" class="custom_date"></td>
							<td class="comment"><?php echo __( 'The date the game took place.', 'bblm'); ?></td>
						</tr>
						<tr>
							<td><?php echo __( 'Location', 'bblm' ); ?></td>
							<td colspan="3">
								<select name="mstad" id="mstad">
									<?php
									$oposts = get_posts(
										array(
											'post_type' => 'bblm_stadium',
											'numberposts' => -1,
											'orderby' => 'post_title',
											'order' => 'ASC'
										)
									);
									if( ! $oposts ) return;
									foreach( $oposts as $o ) {
										echo '<option value="' . $o->ID . '" ' . selected( $m->stad_id, $o->ID ) . '>' . bblm_get_stadium_name( $o->ID ) . '</option>';
									}
									?>
								</select>
							</td>
							<td class="comment"><?php echo __( 'The Stadium where this game took place.', 'bblm'); ?></td>
						</tr>
						<tr>
							<td><?php echo __( 'Score', 'bblm' ); ?></td>
							<td><input name="tAtd" type="text" size="3" maxlength="2" value="<?php echo $mA->mt_td; ?>"></td>
							<td>vs</td>
							<td><input name="tBtd" type="text" size="3" maxlength="2" value="<?php echo $mB->mt_td; ?>"></td>
							<td class="comment"><?php echo __( 'The final score', 'bblm' ); ?></td>
						</tr>
						<tr>
							<td><?php echo __( 'Casualities', 'bblm' ); ?></td>
							<td><input name="tAcas" type="text" size="3" maxlength="2" value="<?php echo $mA->mt_cas; ?>"></td>
							<td>vs</td>
							<td><input name="tBcas" type="text" size="3" maxlength="2" value="<?php echo $mB->mt_cas; ?>"></td>
							<td class="comment"><?php echo __( 'Casualities caused by each team (that count for SPP)', 'bblm' ); ?></td>
						</tr>
						<tr>
							<td><?php echo __( 'Interceptions', 'bblm' ); ?></td>
							<td><input name="tAint" type="text" size="3" maxlength="2" value="<?php echo $mA->mt_int; ?>"></td>
							<td>vs</td>
							<td><input name="tBint" type="text" size="3" maxlength="2" value="<?php echo $mB->mt_int; ?>"></td>
							<td class="comment"><?php echo __( 'Number of Incerceptions made', 'bblm' ); ?></td>
						</tr>
						<tr>
							<td><?php echo __( 'Completions', 'bblm' ); ?></td>
							<td><input name="tAcomp" type="text" size="3" maxlength="2" value="<?php echo $mA->mt_comp; ?>"></td>
							<td>vs</td>
							<td><input name="tBcomp" type="text" size="3" maxlength="2" value="<?php echo $mB->mt_comp; ?>"></td>
							<td class="comment"><?php echo __( 'Number of Completions made', 'bblm' ); ?></td>
						</tr>
						<tr>
							<td><?php echo __( 'Attendance', 'bblm' ); ?></td>
							<td><input name="tAatt" id="tAatt" type="text" size="6" maxlength="6" value="<?php echo $mA->mt_att; ?>" onChange="BBLM_UpdateGate()"></td>
							<td>vs</td>
							<td><input name="tBatt" id="tBatt" type="text" size="6" maxlength="6" value="<?php echo $mB->mt_att; ?>" onChange="BBLM_UpdateGate()"></td>
							<td class="comment"><?php echo __( 'Number of fans attending for each team', 'bblm' ); ?></td>
						</tr>
						<tr>
							<td><?php echo __( 'Gate', 'bblm' ); ?></td>
							<td colspan="3"><input name="gate" id="gate" type="text" size="6" maxlength="6" value="<?php echo $m->m_gate; ?>"></td>
							<td class="comment"><?php echo __( 'The total number of fans', 'bblm' ); ?></td>
						<tr>
							<td><?php echo __( 'Winnings', 'bblm' ); ?></td>
							<td><input name="tAwin" type="text" size="6" maxlength="6" value="<?php echo $mA->mt_winnings; ?>"> GP</td>
							<td>vs</td>
							<td><input name="tBwin" type="text" size="6" maxlength="6" value="<?php echo $mB->mt_winnings; ?>"> GP</td>
							<td class="comment"><?php echo __( 'Changing these will <strong>NOT</strong> update the teams Treasury', 'bblm' ); ?></td>
						</tr>
						<tr>
							<td><?php echo __( 'Match Trivia', 'bblm' ); ?></td>
							<td colspan="3"><textarea name="matchtrivia" cols="80" rows="6" placeholder="Points of note, notable injuries, deaths, or debuts, etc."><?php echo esc_textarea( $m->m_trivia ); ?></textarea></td>
							<td class="comment">&nbsp;</td>
						</tr>
						<tr>
							<td><?php echo __( 'Weather', 'bblm' ); ?></td>
							<td>
								<select id="mweather" name="mweather">
									<option value="1" <?php selected( $m->weather_id, 1 ) ?>>Nice</option>
									<option value="2" <?php selected( $m->weather_id, 2 ) ?>>Very Sunny</option>
									<option value="3" <?php selected( $m->weather_id, 3 ) ?>>Blizzard</option>
									<option value="4" <?php selected( $m->weather_id, 4 ) ?>>Pouring Rain</option>
									<option value="5" <?php selected( $m->weather_id, 5 ) ?>>Sweltering Heat</option>
								</select>
							</td>
							<td>&nbsp;</td>
							<td>
								<select id="mweather2" name="mweather2">
									<option value="1" <?php selected( $m->weather_id2, 1 ) ?>>Nice</option>
									<option value="2" <?php selected( $m->weather_id2, 2 ) ?>>Very Sunny</option>
									<option value="3" <?php selected( $m->weather_id2, 3 ) ?>>Blizzard</option>
									<option value="4" <?php selected( $m->weather_id2, 4 ) ?>>Pouring Rain</option>
									<option value="5" <?php selected( $m->weather_id2, 5 ) ?>>Sweltering Heat</option>
								</select>
							</td>
							<td class="comment"><?php echo __( 'The weather during the match', 'bblm' ); ?></td>
						</tr>
						<tr>
							<td><?php echo __( 'Change in Fan Factor', 'bblm' ); ?></td>
							<td>
								<select id="tAff" name="tAff">
									<option value="0" <?php selected( $mA->mt_ff, 0 ) ?>>No change</option>
									<option value="1" <?php selected( $mA->mt_ff, -1 ) ?>>Minus One (-1)</option>
									<option value="2" <?php selected( $mA->mt_ff, 1 ) ?>>Plus One (+1)</option>
								</select>
							</td>
							<td>vs</td>
							<td>
								<select id="tBff" name="tBff">
									<option value="0" <?php selected( $mB->mt_ff, 0 ) ?>>No change</option>
									<option value="1" <?php selected( $mB->mt_ff, -1 ) ?>>Minus One (-1)</option>
									<option value="2" <?php selected( $mB->mt_ff, 1 ) ?>>Plus One (+1)</option>
								</select>
							</td>
							<td class="comment"><?php echo __( 'Changing these will <strong>NOT</strong> update the teams Fan Factor', 'bblm' ); ?></td>
						</tr>
						<tr>
							<td><?php echo __( 'Coach Comments', 'bblm' ); ?></td>
							<td><textarea name="tAnotes" cols="40" rows="8"><?php echo esc_textarea( $mA->mt_comment ); ?></textarea></td>
							<td>vs</td>
							<td><textarea name="tBnotes" cols="40" rows="8"><?php echo esc_textarea( $mB->mt_comment ); ?></textarea></td>
							<td class="comment"><?php echo __( 'Any team specific comments, coach comments etc', 'bblm' ); ?></td>
						</tr>
					</tbody>
				</table>

				<input type="hidden" name="bblm_mid" size="20" value="<?php echo $m->m_id; ?>" />
				<input type="hidden" name="bblm_comp" size="20" value="<?php echo $m->c_id; ?>" />
				<input type="hidden" name="bblm_div" size="10" value="<?php echo $m->div_id; ?>" />
				<input type="hidden" name="bblm_mid" value="<?php echo $m->m_id; ?>" />
<?php
				//make sure we capture the original divisions of this is a cross division game
				if ( 13 == $m->div_id ) {
					//We have a cross divisional game - We need to pull all the records from the team_comp table for that team / comp
					$teamAacdivsql = "SELECT div_id FROM ".$wpdb->prefix."team_comp C WHERE C.t_id = " . $mA->t_id . " AND C.c_id = " . $m->c_id ." ORDER BY div_id DESC";
					$teamBacdivsql = "SELECT div_id FROM ".$wpdb->prefix."team_comp C WHERE C.t_id = " . $mB->t_id . " AND C.c_id = " . $m->c_id ." ORDER BY div_id DESC";
					$tAadiv = $wpdb->get_var( $teamAacdivsql );
					$tBadiv = $wpdb->get_var( $teamBacdivsql );
					echo '<input type="hidden" name="tAcddiv" size="10" value="' . $tAadiv . '" />';
					echo '<input type="hidden" name="tBcddiv" size="10" value="' . $tBadiv . '" />';
				}
?>
				<p class="submit">
					<input type="submit" name="bblm_match_edit" id="bblm_match_edit" value="Save changes to the match" title="Save changes to the match" class="button button-primary" /> or <a href="<?php echo admin_url(); ?>admin.php?page=bblm_edit_match">Cancel</a>
				</p>
			</form>
<?php
					} // end of the "if editing" chunk
   } //end of function

	 /**
 	 * handles the submission of updates for an Match
	 * Can only get here if bblm_match_edit was previously submitted
 	 */
    public function submit_handling_update() {
      global $wpdb;

       // Verify nonce
    		if ( !wp_verify_nonce( $_POST['bblm_matches'], 'bblm_edit_match' ) ) {
    			return false;
    		}

				$bblm_submit_tA = array();
				$bblm_submit_tB = array();
				$bblm_submit_match = array();

				$bblm_submit_tA['id'] = (int) $_POST['bblm_teama'];
				$bblm_submit_tA['td'] = (int) $_POST['tAtd'];
				$bblm_submit_tA['cas'] = (int) $_POST['tAcas'];
				$bblm_submit_tA['int'] = (int) $_POST['tAint'];
				$bblm_submit_tA['comp'] = (int) $_POST['tAcomp'];
				$bblm_submit_tA['att'] = (int) $_POST['tAatt'];
				$bblm_submit_tA['winning'] = (int) $_POST['tAwin'];
				$bblm_submit_tA['ff'] = (int) $_POST['tAff'];
				$bblm_submit_tA['comment'] = sanitize_textarea_field( esc_textarea( $_POST['tAnotes'] ) );
				if ( isset( $_POST['tAcddiv'] ) ) {
					$bblm_submit_tA['div'] = (int) $_POST['tAcddiv'];
				}
				else {
					$bblm_submit_tA['div'] = 0;
				}

				$bblm_submit_tB['id'] = (int) $_POST['bblm_teamb'];
				$bblm_submit_tB['td'] = (int) $_POST['tBtd'];
				$bblm_submit_tB['cas'] = (int) $_POST['tBcas'];
				$bblm_submit_tB['int'] = (int) $_POST['tBint'];
				$bblm_submit_tB['comp'] = (int) $_POST['tBcomp'];
				$bblm_submit_tB['att'] = (int) $_POST['tBatt'];
				$bblm_submit_tB['winning'] = (int) $_POST['tBwin'];
				$bblm_submit_tB['ff'] = (int) $_POST['tBff'];
				$bblm_submit_tB['comment'] = sanitize_textarea_field( esc_textarea( $_POST['tBnotes'] ) );
				if ( isset( $_POST['tBcddiv'] ) ) {
					$bblm_submit_tB['div'] = (int) $_POST['tBcddiv'];
				}
				else {
					$bblm_submit_tB['div'] = 0;
				}

				$bblm_submit_match['id'] = (int) $_POST['bblm_mid'];
				$bblm_submit_match['date'] = sanitize_text_field( esc_textarea( $_POST['mdate'] ) ) . " 12:00:00";
				$bblm_submit_match['comp'] = (int) $_POST['bblm_comp'];
				$bblm_submit_match['div'] = (int) $_POST['bblm_div'];
				$bblm_submit_match['stad'] = (int) $_POST['mstad'];
				$bblm_submit_match['gate'] = (int) $_POST['gate'];
				$bblm_submit_match['trivia'] = sanitize_textarea_field( esc_textarea( $_POST['matchtrivia'] ) );
				$bblm_submit_match['weather1'] = (int) $_POST['mweather'];
				$bblm_submit_match['weather2'] = (int) $_POST['mweather2'];
				$bblm_submit_match['content'] = "No Report Filed Yet";
				if ( isset( $_POST['bblm_fid'] ) ) {
					$bblm_submit_match['fixture'] = (int) $_POST['bblm_fid'];
				}
				else {
					$bblm_submit_match['fixture'] = 0;
				}

				///Match Calculations
				$bblm_submit_match['td'] = $bblm_submit_tA['td'] + $bblm_submit_tB['td'];
				$bblm_submit_match['cas'] = $bblm_submit_tA['cas'] + $bblm_submit_tB['cas'];
				$bblm_submit_match['int'] = $bblm_submit_tA['int'] + $bblm_submit_tB['int'];
				$bblm_submit_match['completions'] = $bblm_submit_tA['comp'] + $bblm_submit_tB['comp'];

				if ($bblm_submit_tA['td'] > $bblm_submit_tB['td']) {
					$bblm_submit_tA['result'] = "W";
					$bblm_submit_tB['result'] = "L";
				}
				else if ($bblm_submit_tA['td'] < $bblm_submit_tB['td']) {
					$bblm_submit_tA['result'] = "L";
					$bblm_submit_tB['result'] = "W";
				}
				else {
					$bblm_submit_tA['result'] = "D";
					$bblm_submit_tB['result'] = "D";
				}

				//Calculate change in FF
				$bblm_ff_options = array('0','-1','+1');
				$bblm_submit_tA['ff'] = $bblm_ff_options[$bblm_submit_tA['ff']];
				$bblm_submit_tB['ff'] = $bblm_ff_options[$bblm_submit_tB['ff']];

				//If cross devisional download the match results from match_team for the teams original devision
				if ( 13 == $bblm_submit_match['div'] ) {
					$tAdiv = $bblm_submit_tA['div'];
					$tBdiv = $bblm_submit_tB['div'];
				}
				else {
					//If not cross devisional than use the one submitted
					$tAdiv = $bblm_submit_match['div'];
					$tBdiv = $bblm_submit_match['div'];
				}

				//Update the records for the match itself
				$matchupdatesql = "UPDATE ".$wpdb->prefix."match SET `m_teamAtd` = '" . $bblm_submit_tA['td'] . "', `m_teamBtd` = '" . $bblm_submit_tB['td'] . "', `m_teamAcas` = '" . $bblm_submit_tA['cas'] . "', `m_teamBcas` = '" . $bblm_submit_tB['cas'] . "', `m_tottd` = '" . $bblm_submit_match['td'] . "', `m_totint` = '" . $bblm_submit_match['int']  . "', `m_totcomp` = '" . $bblm_submit_match['completions'] . "', `weather_id` = '" . $bblm_submit_match['weather1'] . "', `weather_id2` = '" . $bblm_submit_match['weather1'] . "', `m_trivia` = '" . $bblm_submit_match['trivia'] . "', `stad_id` = '" . $bblm_submit_match['stad'] . "' WHERE m_id = " . $bblm_submit_match['id'];

				//Update the match records for each of the teams
				$teamAmatchupdatesql = "UPDATE ".$wpdb->prefix."match_team SET `mt_td` = '" . $bblm_submit_tA['td'] . "', `mt_cas` = '" . $bblm_submit_tA['cas'] . "', `mt_int` = '" . $bblm_submit_tA['int'] . "', `mt_comp` = '" . $bblm_submit_tA['comp'] . "', `mt_winnings` = '" . $bblm_submit_tA['winning'] . "', `mt_att` = '" . $bblm_submit_tA['att'] . "', `mt_ff` = '" . $bblm_submit_tA['ff'] . "', `mt_result` = '" . $bblm_submit_tA['result'] . "', `mt_comment` = '" . $bblm_submit_tA['comment'] . "' WHERE m_id = " . $bblm_submit_match['id'] . " AND t_id = " . $bblm_submit_tA['id'];
				$teamBmatchupdatesql = "UPDATE ".$wpdb->prefix."match_team SET `mt_td` = '" . $bblm_submit_tB['td'] . "', `mt_cas` = '" . $bblm_submit_tB['cas'] . "', `mt_int` = '" . $bblm_submit_tB['int'] . "', `mt_comp` = '" . $bblm_submit_tB['comp'] . "', `mt_winnings` = '" . $bblm_submit_tB['winning'] . "', `mt_att` = '" . $bblm_submit_tB['att'] . "', `mt_ff` = '" . $bblm_submit_tB['ff'] . "', `mt_result` = '" . $bblm_submit_tB['result'] . "', `mt_comment` = '" . $bblm_submit_tB['comment'] . "' WHERE m_id = " . $bblm_submit_match['id'] . " AND t_id = " . $bblm_submit_tB['id'];

				//Updates the Database
				$wpdb->query( $matchupdatesql );
				$wpdb->query( $teamAmatchupdatesql );
				$wpdb->query( $teamBmatchupdatesql );

				BBLM_Admin_CPT_Competition::update_team_standings( $bblm_submit_tA['id'], $bblm_submit_match['comp'], $tAdiv );
				BBLM_Admin_CPT_Competition::update_team_standings( $bblm_submit_tB['id'], $bblm_submit_match['comp'], $tBdiv );

				//If we get to this point we have added a match to the database!
				$sucess = TRUE;
				do_action( 'bblm_post_submission' );

 ?>
 				<div id="updated" class="notice notice-success">
 					<p>
 <?php
 				if ( $sucess ) {
 					print("Match has been updated.");
 				}
 				else {
 					print("Something went wrong! Please try again.");
 				}
 ?>
 					</p>
 				</div>
 <?php
    }


	/** Singleton instance */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}

add_action( 'plugins_loaded', function () {
	BBLM_Edit_Match::get_instance();
} );
