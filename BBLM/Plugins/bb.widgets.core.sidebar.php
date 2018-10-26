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


?>
