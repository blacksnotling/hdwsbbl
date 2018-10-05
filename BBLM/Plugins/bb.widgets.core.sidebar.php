<?php
/*
Plugin Name: bblm_sidebar widgits
Plugin URI: http://www.hdwsbbl.co.uk/
Description: Provides a list of "other pages" and cutom "topic/cat listing"
Author: Blacksnotling
Version: 1.2
Author URI: http://www.blacksnotling.com/
*/

/*
*	Filename: bb.widgets.core.sidebar.php
*/

  //////////////////////////////
 // List Competitions Widgit //
//////////////////////////////
function widget_bblm_listcomps_init() {
	if ( !function_exists('wp_register_sidebar_widget') )
		return;

	function widget_bblm_listcomps($args) {
		global $wpdb;
		extract($args);

		//Just print out the before_widget bit. note we are skipping the normal title part
		echo $before_widget;

		//meaty content goes here!
		//determine current Season
		$seasonsql = 'SELECT S.sea_id FROM '.$wpdb->prefix.'season S, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE S.sea_id = J.tid AND J.prefix = \'sea_\' AND J.pid = P.ID AND S.sea_active = 1 ORDER BY S.sea_sdate DESC LIMIT 1';
		$sea_id = $wpdb->get_var($seasonsql);

		$compsql = 'SELECT P.post_title, P.guid, C.c_active, UNIX_TIMESTAMP(C.c_sdate) AS sdate  FROM '.$wpdb->prefix.'comp C, '.$wpdb->prefix.'bb2wp J, '.$wpdb->posts.' P WHERE C.c_id = J.tid AND J.prefix = \'c_\' AND J.pid = P.ID AND C.c_counts = 1 AND C.type_id = 1 AND C.sea_id = '.$sea_id.' ORDER BY C.c_active DESC, C.c_sdate ASC';
		if ($complisting = $wpdb->get_results($compsql)) {
			//set up the code below
			$is_first = 1;
			$last_stat = 0;
			$today = date("U");

			foreach ($complisting as $cl) {
				if (($cl->c_active) && ($cl->sdate < $today)) {

						if ((1 !== $last_stat) && (!$is_first)) {
							print("		</ul>\n	</div>\n");
							$is_first = 1;
						}
						if ($is_first) {
							print("	<div>\n	<h2>Active Competitions</h2>\n		<ul>\n");
							$is_first = 0;
						}
						print("			<li><a href=\"".$cl->guid."\" title=\"View more about ".$cl->post_title."\">".$cl->post_title."</a></li>\n");
						$last_stat = 1;
				}//end of active comp
				else if (($cl->c_active) && ($cl->sdate > $today)) {

						if ((2 !== $last_stat) && (!$is_first)) {
							print("		</ul>\n	</div>\n");
							$is_first = 1;
						}
						if ($is_first) {
							print("	<div>\n	<h2>Upcoming Competitions</h2>\n		<ul>\n");
							$is_first = 0;
						}
						print("			<li><a href=\"".$cl->guid."\" title=\"View more about ".$cl->post_title."\">".$cl->post_title."</a></li>\n");
						$last_stat = 2;
				}//end of upcoming comp
				else {

						if ((3 !== $last_stat) && (!$is_first)) {
							print("		</ul>\n	</div>\n");
							$is_first = 1;
						}
						if ($is_first) {
							print("	<div>\n	<h2>Recent Competitions</h2>\n		<ul>\n");
							$is_first = 0;
						}
						print("			<li><a href=\"".$cl->guid."\" title=\"View more about ".$cl->post_title."\">".$cl->post_title."</a></li>\n");
						$last_stat = 3;
				}//end of recent comp
			}//end of for each
			print("		</ul>\n	</div>\n");
		}//end of if sql


		//meaty content ends here!
		echo $after_widget;

	}
	wp_register_sidebar_widget(
		'bblm_List Comps',			// your unique widget id
		'bblm_List Comps',			// widget name
		'widget_bblm_listcomps',	// callback function to display widget
			array(							// options
					'description' => 'Displays a list of active and recent Competitions'
			)
	);
}

  ///////////////////////////
 // Restricted Cat Widgit //
///////////////////////////

function widget_bblm_restricted_cat_init() {

	if ( !function_exists('wp_register_sidebar_widget') )
		return;
	function widget_bblm_restricted_cat($args) {

		extract($args);
		$options = get_option('widget_bblm_restricted_cat');
		$title = $options['title'];
		$restricted = $options['rescats'];

		// These lines generate our output. Widgets can be very complex
		// but as you can see here, they can also be very, very simple.

		echo $before_widget . $before_title . $title . $after_title;
		?>


<?php

		//print("<ul>\n");
		//wp_list_categories('orderby=name&title_li=&exclude='.$restricted);
		//print("</ul>\n");

?>
		<form action="<?php bloginfo('url'); ?>/" method="get">
<?php
	$select = wp_dropdown_categories('orderby=name&title_li=&hide_empty=1&depth=1&echo=0&exclude='.$restricted);
	$select = preg_replace("#<select([^>]*)>#", "<select$1 onchange='return this.form.submit()'>", $select);
	echo $select;
?>
	<noscript><input type="submit" value="View" /></noscript>
	</form>


		<?php
		echo $after_widget;
	}

	function widget_bblm_restricted_cat_control() {

		$options = get_option('widget_bblm_restricted_cat');
		if ( !is_array($options) )
			$options = array('title'=>'', 'rescats'=>'');
		if ( $_POST['bblm_rc-submit'] ) {

			$options['title'] = strip_tags(stripslashes($_POST['bblm_rc-title']));
			$options['rescats'] = strip_tags(stripslashes($_POST['bblm_rc-restricted']));
			update_option('widget_bblm_restricted_cat', $options);
		}

		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		$restricted = htmlspecialchars($options['rescats'], ENT_QUOTES);

				echo '<p style="text-align:right;"><label for="bblm_rc-title">' . __('Title:') . ' <input style="width: 200px;" id="bblm_rc-title" name="bblm_rc-title" type="text" value="'.$title.'" /></label></p>';
		echo '<p style="text-align:right;"><label for="bblm_rc-restricted">' . __('Hide Cats::') . ' <input style="width: 200px;" id="bblm_rc-restricted" name="bblm_rc-restricted" type="text" value="'.$restricted.'"/></label></p>';
				echo '<input type="hidden" id="bblm_rc-submit" name="bblm_rc-submit" value="1" />';
	}


	wp_register_sidebar_widget(
		'bblm_Categories',				// your unique widget id
		'bblm_Categories',				// widget name
		'widget_bblm_restricted_cat',	// callback function to display widget
	    array(							// options
	        'description' => 'Restricts Caregories from displaying'
	    )
	);
	wp_register_widget_control(
		'bblm_Categories',						// id
		'bblm_Categories',						// name
		'widget_bblm_restricted_cat_control'	// callback function
	);
}


// Run our code later in case this loads prior to any required plugins.
add_action('widgets_init', 'widget_bblm_restricted_cat_init');
add_action('widgets_init', 'widget_bblm_listcomps_init');


?>
