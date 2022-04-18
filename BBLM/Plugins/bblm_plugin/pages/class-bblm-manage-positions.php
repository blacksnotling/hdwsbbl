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
 * @version   1.1.1
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

          return bblm_get_race_name( $item[ 'r_id' ] );

        }


      case 'edit':
			return '<a href="'.admin_url().'admin.php?page=bblm_positions&bblm_action=edit&pos_id='.$item[ 'pos_id' ].'">Edit</a>';

      case 'pos_ma':
      case 'pos_st':
      case 'pos_ag':
      case 'pos_av':
      case 'pos_skills':
				return $item[ $column_name ];

			case 'pos_pa':
				if ( $item[ $column_name ] == 0 ){
					return '-';
				}
				else {
					return $item[ $column_name ];
				}

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

		$title = '<strong>' . $item['pos_name'] . '</a></strong>';

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
			'name'    => __( 'Position Name', 'bblm' ),
      'edit' => __( 'Edit', 'bblm' ),
      'r_id' => __( 'Race', 'bblm' ),
			'pos_ma'    => __( 'MA', 'bblm' ),
      'pos_st'    => __( 'ST', 'bblm' ),
      'pos_ag'    => __( 'AG', 'bblm' ),
			'pos_pa'    => __( 'PA', 'bblm' ),
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
		global $wpdb;

		// If the delete bulk action is triggered
		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
		     || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
		) {

			//verify the nonce
			if ( !wp_verify_nonce( $_POST['bblm_positions'], 'bblm_delete_position' ) ) {
				return $post_id;
			}

			else {
				$delete_ids = esc_sql( $_POST['bulk-delete'] );

				// loop over the array of record IDs and deactivate them
				$sucess = "";
				foreach ( $delete_ids as $id ) {

					$deletesql = "DELETE FROM ".$wpdb->prefix."position WHERE pos_id = " . $id;
					if ( FALSE !== $wpdb->query( $deletesql ) ) {
						$sucess = TRUE;
						do_action( 'bblm_post_submission' );
					}

				}

	?>
				<div id="updated" class="notice notice-success">
					<p>
						<?php
						if ( $sucess ) {
							echo __( 'Position(s) was removed' , 'bblm' );
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
      <h1 class="wp-heading-inline"><?php echo __( 'Positions', 'bblm' ); ?></h1>
 			<a href="<?php echo admin_url(); ?>admin.php?page=bblm_positions&bblm_action=new" class="page-title-action">Add Position</a>

<?php
    if ( isset( $_POST['bblm_position_add'] ) ) {

      //If we have submitted a new position then pass to the correct function to save
      $this->submit_handling_new();

    }

		if ( isset( $_POST['bblm_position_edit'] ) ) {

      //If we have submitted an updated position then pass to the correct function to save
      $this->submit_handling_update();

    }

		if ( isset( $_GET['bblm_action'] ) && ( 'new' == $_GET['bblm_action'] || 'edit' == $_GET['bblm_action'] ) ) {

      //we are adding or updating a postion so we pass to the correct function
      $this->neworupdate_handling();

    }
		else {
      //we are displaying the overall form
?>
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
							wp_nonce_field( 'bblm_delete_position', 'bblm_positions' );
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
		 global $wpdb;

		 $bblm_isediting = 0;
		 //ininalise array to hold the details of the position
		 $pos = array( array(
			 'pos_id' => '',
			 'pos_name' => '',
			 'r_id' => '',
			 'pos_limit' => '',
			 'pos_ma' => '',
			 'pos_st' => '',
			 'pos_ag' => '',
			 'pos_pa' => '',
			 'pos_av' => '',
			 'pos_skills' => '',
			 'pos_cost' => '',
			 'pos_freebooter' => '',
			 'pos_status' => '',
		 ),
	 		);
?>
     <div class="form-wrap">
			 <form id="addposition" method="post" action="" class="validate">
         <?php wp_nonce_field( 'bblm_add_position', 'bblm_positions' ); ?>
         <table>
           <tr>
             <td>
<?php
		if ( 'edit' == $_GET['bblm_action'] && isset( $_GET['pos_id'] ) ) {
			//we are editing a position

			$bblm_isediting = 1;
			echo '<h2>' . __( 'Edit Position', 'bblm') . '</h2>';

			//retrieve the position details from the database and populate an array
			$sql = "SELECT * FROM ".$wpdb->prefix."position where pos_id = ".absint( $_GET['pos_id'] );
			$pos = $wpdb->get_results( $sql, 'ARRAY_A' );

		}
		else {
			//we are adding a new position

			echo '<h2>' . __( 'Add New Position', 'bblm') . '</h2>';

		}

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
         <p><label for="bblm_rid"><?php echo __( 'Race', 'bblm' ); ?>: </label><select name="bblm_rid" id="bblm_rid">
         <?php while ( $query->have_posts() ) : $query->the_post(); ?>
             <option value="<?php the_ID(); ?>"<?php if ( get_the_ID() == $pos[0][ 'r_id' ] ) { echo ' selected="selected"'; } ?>><?php the_title(); ?></option>
         <?php endwhile; wp_reset_postdata();?>
       </select></p>
       <?php endif; ?>
             </td>
           </tr>
           <tr>
             <td colspan="5"><label for="bblm_pname"><?php echo __( 'Position Name', 'bblm' ); ?>: </label><input type="text" name="bblm_pname" size="20" maxlength="20" value="<?php if ( '' !== $pos[0][ 'pos_name' ] ) { echo $pos[0][ 'pos_name' ]; } ?>" id="bblm_pname"></td>
           </tr>
           <tr>
             <td><label for="bblm_plimit"><?php echo __( 'Limit', 'bblm' ); ?>: </label>0 - <input type="text" name="bblm_plimit" size="3" maxlength="2" value="<?php if ( '' == $pos[0][ 'pos_limit' ] ) { echo '0'; } else { echo $pos[0][ 'pos_limit' ]; } ?>" id="bblm_plimit"></td>
             <td><label for="bblm_pma"><?php echo __( 'MA', 'bblm' ); ?>: </label><input type="text" name="bblm_pma" size="3" maxlength="2" value="<?php if ( '' == $pos[0][ 'pos_ma' ] ) { echo '4'; } else { echo $pos[0][ 'pos_ma' ]; } ?>" id="bblm_pma"></td>
             <td><label for="bblm_pst"><?php echo __( 'ST', 'bblm' ); ?>: </label><input type="text" name="bblm_pst" size="3" maxlength="2" value="<?php if ( '' == $pos[0][ 'pos_st' ] ) { echo '4'; } else { echo $pos[0][ 'pos_st' ]; } ?>" id="bblm_pst"></td>
             <td><label for="bblm_pag"><?php echo __( 'AG', 'bblm' ); ?>: </label><input type="text" name="bblm_pag" size="3" maxlength="2" value="<?php if ( '' == $pos[0][ 'pos_ag' ] ) { echo '4'; } else { echo $pos[0][ 'pos_ag' ]; } ?>" id="bblm_pag">+</td>
						 <td><label for="bblm_ppa"><?php echo __( 'PA', 'bblm' ); ?>: </label><input type="text" name="bblm_ppa" size="3" maxlength="2" value="<?php if ( '' == $pos[0][ 'pos_pa' ] ) { echo '4'; } else { echo $pos[0][ 'pos_pa' ]; } ?>" id="bblm_ppa">+</td>
             <td><label for="bblm_pav"><?php echo __( 'AV', 'bblm' ); ?>: </label><input type="text" name="bblm_pav" size="3" maxlength="2" value="<?php if ( '' == $pos[0][ 'pos_av' ] ) { echo '4'; } else { echo $pos[0][ 'pos_av' ]; } ?>" id="bblm_pav">+</td>
           </tr>
           <tr>
             <td colspan="5"><label for="bblm_pskills"><?php echo __( 'Skills', 'bblm' ); ?>: </label><textarea name="pskills" cols="100" rows="3"><?php if ( '' == $pos[0][ 'pos_skills' ] ) { echo 'none'; } else { echo $pos[0][ 'pos_skills' ]; } ?></textarea></td>
           </tr>
           <tr>
             <td colspan="5"><label for="bblm_pcost"><?php echo __( 'Cost', 'bblm' ); ?>: </label><input type="text" name="bblm_pcost" size="7" maxlength="6" value="<?php if ( '' == $pos[0][ 'pos_cost' ] ) { echo '50000'; } else { echo $pos[0][ 'pos_cost' ]; } ?>" id="bblm_pcost">GP
                     <p>(<?php echo __( 'No Commas', 'bblm' ); ?>)</p></td>
           </tr>
<?php
					//Only display these if we are editing an existing position
					if ( $bblm_isediting ) {
?>
					<tr>
						<td colspan="5"><label for="bblm_pfreebooter"><?php echo __( 'Freebooter', 'bblm' ); ?>: </label><input type="text" name="bblm_pfreebooter" size="3" maxlength="1" value="<?php echo $pos[0][ 'pos_freebooter' ]; ?>" id="bblm_pfreebooter">
						<p><?php echo __( 'Do Journeymen use this position? There should only be ONE for each race! 1 = Yes, 0 = No', 'bblm' ); ?></p></td>
					</tr>
					<tr>
						<td colspan="5"><label for="bblm_pstatus"><?php echo __( 'Still Active', 'bblm' ); ?>? </label><input type="text" name="bblm_pstatus" size="3" maxlength="1" value="<?php echo $pos[0][ 'pos_status' ]; ?>" id="bblm_pstatus">
						<p><?php echo __( '1 = Yes, 0 = No. Yes will allow new players to be hired into this position. Set to 0 for legacy positions', 'bblm' ); ?></p></td>
					</tr>
					</table>
					<input type="hidden" name="bblm_ppid" value="<?php echo $pos[0][ 'pos_id' ]; ?>" />
					<p class="submit"><input type="submit" name="bblm_position_edit" id="bblm_position_edit" value="Save changes to Position" title="Save changes to Position" class="button button-primary" /> or <a href="<?php echo admin_url(); ?>admin.php?page=bblm_positions">Cancel</a></p></form>
<?php
					} // end of the "if editing" chunk
					else {
						//We are adding a position so end the table and show the add position button
?>

				 </table>
         <p class="submit"><input type="submit" name="bblm_position_add" id="bblm_position_add" value="Add Position to Race" title="Add position to Race" class="button button-primary" /></p></form>

<?php
					}//end of else not editing
   }

  /**
	 * handles the submission of a new position
	 */
   public function submit_handling_new() {
     global $wpdb;

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
			$bblm_ppa = (int) $_POST['bblm_ppa'];
      $bblm_pav = (int) $_POST['bblm_pav'];
      $bblm_pcost = (int) str_replace(',', '', $_POST['bblm_pcost'] );

      $addsql = 'INSERT INTO `'.$wpdb->prefix.'position` (`pos_id`, `pos_name`, `r_id`, `pos_limit`, `pos_ma`, `pos_st`, `pos_ag`, `pos_pa`, `pos_av`, `pos_skills`, `pos_cost`, `pos_freebooter`, `pos_status`) VALUES (\'\', \''.$bblm_pname.'\', \''.$bblm_race.'\', \''.$bblm_limit.'\', \''.$bblm_pma.'\', \''.$bblm_pst.'\', \''.$bblm_pag.'\', \''.$bblm_ppa.'\', \''.$bblm_pav.'\', \''.$bblm_pskills.'\', \''.$bblm_pcost.'\', \'0\', \'1\')';

        $sucess = "";
    	if ( FALSE !== $wpdb->query( $addsql ) ) {
    		$sucess = TRUE;
    	}
    	else {
    		$wpdb->print_error();
    	}
?>
  <div id="updated" class="updated fade">
    <p>
      <?php
      if ( $sucess ) {
        echo __( 'Position was added' , 'bblm' );
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
 	 * handles the submission of updates for an position
 	 */
    public function submit_handling_update() {
      global $wpdb;

       // Verify nonce
    		if ( !isset( $_POST['bblm_position_edit'] ) || !isset( $_POST['bblm_positions'] ) || !wp_verify_nonce( $_POST['bblm_positions'], 'bblm_add_position' ) ) {
    			return false;
    		}

     	//think about a for each
     	$bblm_pname = wp_kses( esc_sql( $_POST['bblm_pname'] ), array() );
     	$bblm_pskills = wp_kses( esc_sql( $_POST['pskills'] ), array() );

     	//sanitise vars
			 $bblm_ppid = (int) $_POST['bblm_ppid'];
			 $bblm_race = (int) $_POST['bblm_rid'];
       $bblm_limit = (int) $_POST['bblm_plimit'];
       $bblm_pma = (int) $_POST['bblm_pma'];
       $bblm_pst = (int) $_POST['bblm_pst'];
       $bblm_pag = (int) $_POST['bblm_pag'];
			 $bblm_ppa = (int) $_POST['bblm_ppa'];
       $bblm_pav = (int) $_POST['bblm_pav'];
       $bblm_pcost = (int) str_replace(',', '', $_POST['bblm_pcost'] );
			 $bblm_pfreebooter = (int) $_POST['bblm_pfreebooter'];
			 $bblm_pstatus = (int) $_POST['bblm_pstatus'];

			 $updatesql = 'UPDATE `'.$wpdb->prefix.'position` SET `pos_name` = \''.$bblm_pname.'\', `r_id` = \''.$bblm_race.'\', `pos_limit` = \''.$bblm_limit.'\', `pos_ma` = \''.$bblm_pma.'\', `pos_st` = \''.$bblm_pst.'\', `pos_ag` = \''.$bblm_pag.'\', `pos_pa` = \''.$bblm_ppa.'\', `pos_av` = \''.$bblm_pav.'\', `pos_skills` = \''.$bblm_pskills.'\', `pos_cost` = \''.$bblm_pcost.'\', `pos_freebooter` = \''.$bblm_pfreebooter.'\', `pos_status` = \''.$bblm_pstatus.'\' WHERE `pos_id` = '.$bblm_ppid.';';

         $sucess = "";
     	if ( FALSE !== $wpdb->query( $updatesql ) ) {
     		$sucess = TRUE;
     	}
     	else {
     		$wpdb->print_error();
     	}
 ?>
   <div id="updated" class="updated fade">
     <p>
       <?php
       if ( $sucess ) {
         echo __( 'Position was updated' , 'bblm' );
       }
       else {
         echo __( 'Something went wrong', 'bblm' );
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
