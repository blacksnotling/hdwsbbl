<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Two classes
 * The first hanbdles the generation of the Positions table,
 * THe Second inputs and displays the data and handles the form and POST / GET action
 *
 * @class 		BBLM_Positions_list
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/Admin
 * @version   1.0
 */

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class BBLM_Positions_List extends WP_List_Table {

	/** Class constructor */
	public function __construct() {

		parent::__construct( [
			'singular' => __( 'Position', 'bblm' ), //singular name of the listed records
			'plural'   => __( 'Positions', 'bblm' ), //plural name of the listed records
			'ajax'     => false //does this table support ajax?
		] );

	}


	/**
	 * Retrieve Position data from the database
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */
	public static function get_positions( $per_page = 25, $page_number = 1 ) {

		global $wpdb;

		$sql = "SELECT * FROM {$wpdb->prefix}position";
    if ( ( isset( $_REQUEST[ 'bblm_filter' ] ) ) && ( 0 !== absint( $_REQUEST[ 'bblm_filter' ] ) ) ) {

      $sql .= " WHERE r_id = ". absint( $_REQUEST[ 'bblm_filter' ] ) ;


    }

    $sql .= " ORDER BY r_id ASC, pos_name ASC";
		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;


		$result = $wpdb->get_results( $sql, 'ARRAY_A' );

		return $result;
	}


	/**
	 * Delete a Position.
	 *
	 * @param int $id position ID
	 */
	public static function delete_position( $id ) {
		global $wpdb;

		$wpdb->delete(
			"{$wpdb->prefix}position",
			[ 'pos_id' => $id ],
			[ '%d' ]
		);
	}


	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public static function record_count() {
		global $wpdb;

		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}position";

		return $wpdb->get_var( $sql );
	}


	/** Text displayed when no Position data is available */
	public function no_items() {

		_e( 'No Positions avaliable.', 'bblm' );

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

      case 'r_id':
        if ( 0 == $item[ 'r_id' ] ) {

          return "System Default";
        }
        else {

          return get_the_title( $item[ 'r_id' ] );

        }


      case 'edit':
      return '<input type="submit" name="bblm_position_editform" id="bblm_position_editform" value="Edit" title="Edit" />';

      case 'pos_ma':
      case 'pos_st':
      case 'pos_ag':
      case 'pos_av':
      case 'pos_skills':
				return $item[ $column_name ];

      case 'pos_cost':
        return number_format( $item[ 'pos_cost' ] ) .' GP';

      case 'pos_status':
        if ( $item[ 'pos_status' ] ) {
          return "Active";
        }
        else {
          return "Not used";
        }

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
			'<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['pos_id']

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

		$delete_nonce = wp_create_nonce( 'bblm_delete_position' );

		$title = '<strong>' . $item['pos_name'] . '</a></strong>';

		$actions = [
			'delete' => sprintf( '<a href="?page=%s&action=%s&position=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['pos_id'] ), $delete_nonce )
		];

		return $title . $this->row_actions( $actions );
	}


	/**
	 *  Associative array of columns
	 *
	 * @return array
	 */
	function get_columns() {
		$columns = [
			'cb'      => '<input type="checkbox" />',
			'name'    => __( 'Position Name', 'bblm' ),
      'edit' => __( 'Edit', 'bblm' ),
      'r_id' => __( 'Race', 'bblm' ),
			'pos_ma'    => __( 'MA', 'bblm' ),
      'pos_st'    => __( 'ST', 'bblm' ),
      'pos_ag'    => __( 'AG', 'bblm' ),
      'pos_av'    => __( 'AV', 'bblm' ),
      'pos_skills'    => __( 'Skills', 'bblm' ),
      'pos_cost'    => __( 'Cost', 'bblm' ),
      'pos_status'    => __( 'Status', 'bblm' )
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
			'name' => array( 'name', true ),
      'r_id' => array( 'r_id', true ),
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

		$per_page     = $this->get_items_per_page( 'positions_per_page', 25 );
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();

		$this->set_pagination_args( [
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $per_page //WE have to determine how many items to show on a page
		] );

		$this->items = self::get_positions( $per_page, $current_page );
	}

	public function process_bulk_action() {

		//Detect when a bulk action is being triggered...
		if ( 'delete' === $this->current_action() ) {

			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );

			if ( ! wp_verify_nonce( $nonce, 'bblm_delete_position' ) ) {
				return $post_id;
			}
			else {
				self::delete_position( absint( $_GET['position'] ) );

		                // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
		                // add_query_arg() return the current url
		                wp_redirect( esc_url_raw(add_query_arg()) );
				exit;
			}

		}

		// If the delete bulk action is triggered
		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
		     || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
		) {

			$delete_ids = esc_sql( $_POST['bulk-delete'] );

			// loop over the array of record IDs and delete them
			foreach ( $delete_ids as $id ) {
				self::delete_position( $id );

			}

			// esc_url_raw() is used to prevent converting ampersand in url to "#038;"
		        // add_query_arg() return the current url
		        wp_redirect( esc_url_raw(add_query_arg()) );
			exit;
		}
	}

}

/**
 *
 * @class 		BBLM_Manage_Positions
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/Admin
 * @version   1.0
 */

class BBLM_Manage_Positions {

	// class instance
	static $instance;

	// Positions WP_List_Table object
	public $positions_obj;

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
      __( 'Positions', 'bblm' ),
      __( 'Positions', 'bblm' ),
      'manage_options',
      'bblm_positions',
      array( $this, 'manage_positions_page' )
    );

		add_action( "load-$hook", array( $this, 'screen_option' ) );

	}


	/**
	 * The Output of the Page
	 */
	public function manage_positions_page() {
?>
		<div class="wrap">
      <h1 class="wp-heading-inline">Positions</h1>

<?php
    if ( isset( $_POST['bblm_position_add'] ) ) {

      //If we have submitted a new or updated position then pass to the correc function
      $this->submit_handling();

    }

    //if ( isset( $_GET['action'] ) && 'new' == $_GET['action'] ) {
    if (( isset( $_POST['bblm_position_addform'] ) ) || ( isset( $_POST['bblm_position_editform'] ) )) {

      //we are adding or updating a postion so we pass to the correct function
      $this->neworupdate_handling();

    }
    else {
      //we are displaying the overall form
?>

    <form id="addpositionbutton" method="post" action=""><input type="submit" name="bblm_position_addform" id="bblm_position_addform" value="Add Position" title="Add New position to Race" class="page-title-action" /></form>
    <p>Don't delete any poistions that currently have players assigned to them!!!</p>
    <p><form id="filterracebutton" method="post" action="">
      <?php
               $raceargs = array(
                 'post_type' => 'bblm_race',
                 'orderby' => 'title',
                 'order'   => 'ASC',
                 'posts_per_page'=> -1,
                 'meta_query' => array(
                   array(
                     'key'     => 'race_hide',
                     'compare' => 'NOT EXISTS',
                   ),
                 ),
               );

               $query = new WP_Query( $raceargs );

               if ( $query->have_posts() ) : ?>
               <select name="bblm_filter" id="bblm_filter">
                 <option value="x">Show all Races</option>
               <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                   <option value="<?php the_ID(); ?>"><?php the_title(); ?></option>
               <?php endwhile; wp_reset_postdata();?>
               </select>
             <?php endif; ?>

      <input type="submit" name="bblm_position_filter" id="bblm_position_filter" value="Filter" title="Add New position to Race" class="page-title-action" /></form></p>

			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-3">
					<div id="post-body-content">
						<div class="meta-box-sortables ui-sortable">

							<form method="post" action="">
<?php
		          $this->positions_obj->prepare_items();
			        $this->positions_obj->display();
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
			'label'   => 'Positions',
			'default' => 25,
			'option'  => 'positions_per_page'
		);

		add_screen_option( $option, $args );

		$this->positions_obj = new BBLM_Positions_List();
	}

  /**
	 * Displays the form for adding and updating a new position
	 */
   public function neworupdate_handling() {
?>
     <div class="form-wrap">
       <h2>Add New Position</h2>
       <form id="addposition" method="post" action="" class="validate">
         <?php wp_nonce_field( 'bblm_add_position', 'bblm_positions' ); ?>
         <table>
           <tr>
             <td>
<?php
         $raceargs = array(
           'post_type' => 'bblm_race',
           'orderby' => 'title',
           'order'   => 'ASC',
           'posts_per_page'=> -1,
           'meta_query' => array(
             array(
               'key'     => 'race_hide',
               'compare' => 'NOT EXISTS',
             ),
           ),
         );

         $query = new WP_Query( $raceargs );

         if ( $query->have_posts() ) : ?>
         <select name="bblm_rid" id="bblm_rid">
         <?php while ( $query->have_posts() ) : $query->the_post(); ?>
             <option value="<?php the_ID(); ?>"><?php the_title(); ?></option>
         <?php endwhile; wp_reset_postdata();?>
         </select>
       <?php endif; ?>
             </td>
           </tr>
           <tr>
             <td colspan="5"><label for="bblm_pname">Position Name: </label><input type="text" name="bblm_pname" size="20" maxlength="20" value="" id="bblm_pname"></td>
           </tr>
           <tr>
             <td><label for="bblm_plimit">limit: </label>0-<input type="text" name="bblm_plimit" size="3" maxlength="2" value="0" id="bblm_plimit"></td>
             <td><label for="bblm_pma">MA: </label><input type="text" name="bblm_pma" size="3" maxlength="2" value="4" id="bblm_pma"></td>
             <td><label for="bblm_pst">ST: </label><input type="text" name="bblm_pst" size="3" maxlength="2" value="4" id="bblm_pst"></td>
             <td><label for="bblm_pag">AG: </label><input type="text" name="bblm_pag" size="3" maxlength="2" value="4" id="bblm_pag"></td>
             <td><label for="bblm_pav">AV: </label><input type="text" name="bblm_pav" size="3" maxlength="2" value="4" id="bblm_pav"></td>
           </tr>
           <tr>
             <td colspan="5"><label for="bblm_pskills">Skills: </label><textarea name="pskills" cols="100" rows="3">none</textarea></td>
           </tr>
           <tr>
             <td colspan="5"><label for="bblm_pcost">Cost: </label><input type="text" name="bblm_pcost" size="7" maxlength="6" value="50000" id="bblm_pcost">GP
                     <p>(No Commas)</p></td>
           </tr>
         </table>

         <p class="submit"><input type="submit" name="bblm_position_add" id="bblm_position_add" value="Add Position to Race" title="Add position to Race" class="button button-primary" /></p></form>

<?php
   }

  /**
	 * handles the any Post Data on this page
	 */
   public function submit_handling() {
     global $wpdb;

/*     print("<pre>");
     print_r($_POST);
     print("</pre>");
*/

      // Verify nonce
   		if ( !isset( $_POST['bblm_position_add'] ) || !isset( $_POST['bblm_positions'] ) || !wp_verify_nonce( $_POST['bblm_positions'], 'bblm_add_position' ) ) {
   			return false;
   		}

    	//think about a for each
    	$bblm_pname = wp_kses( esc_sql( $_POST['bblm_pname'] ), array() );
    	$bblm_pskills = wp_kses( esc_sql( $_POST['pskills'] ), array() );

    	//sanitise vars
    	$bblm_race = (int) $_POST['bblm_rid'];
      $bblm_limit = (int) $_POST['bblm_plimit'];
      $bblm_pma = (int) $_POST['bblm_pma'];
      $bblm_pst = (int) $_POST['bblm_pst'];
      $bblm_pag = (int) $_POST['bblm_pag'];
      $bblm_pav = (int) $_POST['bblm_pav'];
      $bblm_pcost = (int) str_replace(',', '', $_POST['bblm_pcost'] );

      $addsql = 'INSERT INTO `'.$wpdb->prefix.'position` (`pos_id`, `pos_name`, `r_id`, `pos_limit`, `pos_ma`, `pos_st`, `pos_ag`, `pos_av`, `pos_skills`, `pos_cost`, `pos_freebooter`, `pos_status`) VALUES (\'\', \''.$bblm_pname.'\', \''.$bblm_race.'\', \''.$bblm_limit.'\', \''.$bblm_pma.'\', \''.$bblm_pst.'\', \''.$bblm_pag.'\', \''.$bblm_pav.'\', \''.$bblm_pskills.'\', \''.$bblm_pcost.'\', \'0\', \'1\')';
//      echo '<p>'.$addsql.'</p>';



        $sucess = "";
    	if (FALSE !== $wpdb->query($addsql)) {
    		$sucess = TRUE;
    	}
    	else {
    		$wpdb->print_error();
    	}
?>
  <div id="updated" class="updated fade">
    <p>
      <?php
      if ($sucess) {
        print("Position was Added!");
      }
      else {
        print("Something went wrong");
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
	BBLM_Manage_Positions::get_instance();
} );
