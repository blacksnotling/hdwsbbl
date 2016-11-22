<?php
/*
Plugin Name: Blood Bowl Team News Network Child Plugin
Description: Provides all the functions and options required to run the child sites. At present it provides an options page to map the Team ID
Author: Andy M
Version: 1.1.1
Author URI: http://www.hdwsbbl.co.uk

*	Filename: plugin_bbtnn_child.php
*/
/* -- Change History --
20100816 - 0.1b - Intital creation of file - completed the options page
20100817 - 0.2b - Added the Team Identity widget
20100816 - 0.1 - Added the Recent matches Widget (tracker item [284])
20100901 - 0.2 - Add the TNN Toolbar function (tracker item [312])
20100906 - 0.3 - refactored the code to make it neater (tracker [325]) and updated the wording on team identity widget (tracker [324])
		 - 0.4 - Added a "HDWSBBL Championships" Widget
		 - 0.5 - Added a team sponsor widget
20100907 - 0.6 - Custom Login screen for all blogs!
20100909 - 0.7 - restored the admin menu [338]. a missing closing comment was missing!
20100913 - 1.0 - bump to V1.0 for go-live
20100917 - 1.0.1 - modified the class names of the widgets so I can style them!
20100918 - 1.1 - Added the Upcoming Matches (Fixtures Widget (Tracker item [308])
		 - 1.1.1 - Added the teams status to the team identity widget (tracker item [339])
*/
//////////////////////////////////////
 // Options page - Blog2Team mapping //
//////////////////////////////////////
//http://ottopress.com/2009/wordpress-settings-api-tutorial/
//add the admin options page
add_action('admin_menu', 'bbtnn_child_admin_add_page');

function bbtnn_child_admin_add_page() {
	add_options_page('HDWSBBL Options Page', 'HDWSBBL TNN Options', 'promote_users', 'bbtnn_child_options', 'bbtnn_child_options_page');
}

// display the admin options page
function bbtnn_child_options_page() {
?>
<div>
	<h2>HDWSBBL Team News Network</h2>
	The Options displayed on this page releate to your blog. If the options are visible, please don't make ammendments unless instructed too!
<?php if (is_super_admin()) { ?>
	<form action="options.php" method="post">

		<?php settings_fields('bbtnn_child_options'); ?>
		<?php do_settings_sections('bbtnn_child_options'); ?>

		<input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
	</form>
<?php	}
		else {
			print("<p>Sorry, you need to be a Super Admin to access this page.</p>");
		}
?>
</div>
<?php
}
// add the admin settings and such
add_action('admin_init', 'bbtnn_child_options_admin_init');
function bbtnn_child_options_admin_init(){
	register_setting( 'bbtnn_child_options', 'bbtnn_child_options', 'bbtnn_child_options_validate' );
	add_settings_section('bbtnn_child_options_resticted', 'Restricted Settings', 'bbtnn_restricted_section_text', 'bbtnn_child_options');
	add_settings_field('bbtn_child_tid', 'HDWSBBL Team ID', 'bbtn_child_option_setting_string', 'bbtnn_child_options', 'bbtnn_child_options_resticted');
	add_settings_field('bbtn_child_tbl_pre', 'HDWSBBL BB Tbl Prefix', 'bbtn_child_option_prefix_string', 'bbtnn_child_options', 'bbtnn_child_options_resticted');
	add_settings_field('bbtn_child_tbl_pre2', 'HDWSBBL WP Tbl Prefix', 'bbtn_child_option_prefix2_string', 'bbtnn_child_options', 'bbtnn_child_options_resticted');
}
function bbtnn_restricted_section_text() {
	echo '<p>Really, DO NOT mess with this!.</p>';
}
function bbtn_child_option_setting_string() {
	$options = get_option('bbtnn_child_options');
	echo "<input id='bbtn_child_tid' name='bbtnn_child_options[t_id]' size='5' type='text' value='{$options['t_id']}' />";

}
function bbtn_child_option_prefix_string() {
	$options = get_option('bbtnn_child_options');
	echo "<input id='bbtn_child_bbtbl_pre' name='bbtnn_child_options[bb_tbl_pre]' size='5' type='text' value='{$options['bb_tbl_pre']}' />";

}
function bbtn_child_option_prefix2_string() {
	$options = get_option('bbtnn_child_options');
	echo "<input id='bbtn_child_wptbl_pre' name='bbtnn_child_options[wp_tbl_pre]' size='5' type='text' value='{$options['wp_tbl_pre']}' />";

}
// validate our options
function bbtnn_child_options_validate($input) {
//	$options = get_option('plugin_options');
//	$options['text_string'] = trim($input['text_string']);
//	if(!preg_match('/^[a-z0-9]{32}$/i', $options['text_string'])) {
//		$options['text_string'] = '';
//	}
//	return $options;
	return $input;
}
  /////////////////////////
 // End of Options Page //
/////////////////////////

  /////////////
 // Widgets //
/////////////
// http://codex.wordpress.org/Widgets_API
// http://justintadlock.com/archives/2009/05/26/the-complete-guide-to-creating-widgets-in-wordpress-28
/**
 * bbtnnn_TeamIdentity Class
 */
class bbtnnn_teamidentity extends WP_Widget {
    /** constructor */
    function bbtnnn_teamidentity() {
		$widget_ops = array( 'classname' => 'team_identity', 'description' => 'Displays the name of your team and provides a link to their page on the main HDWSBBL site..' );
		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'bbtnn_teamidentity' );
		parent::WP_Widget( 'bbtnn_teamidentity', 'HDWSBBL - Team Identity', $widget_ops, $control_ops );
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {
    	global $wpdb;
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
        ?>
		<?php
		$options = get_option('bbtnn_child_options');
		$identsql = "SELECT T.t_name AS teamname, T.t_guid AS turl, T.t_active FROM ".$options['bb_tbl_pre']."team T WHERE T.t_id = ".$options['t_id'];
		if ($ident = $wpdb->get_row($identsql)) {

			echo $before_widget;

			$tname = $ident->teamname;
			$link = $ident->turl;
			$tstatus = $ident->t_active;

			print("<p>This is the news site for <strong>".$tname."</strong>, ");
			if ($ident->t_active) {
				print("an active team in ");
			}
			else {
				print("a former team of ");
			}
			print("the HDWSBBL. You can find more details on <a href=\"".$link."\" title=\"Visit the page on ".$tname." on the official HDWSBBL site\" class=\"external\">".$tname."</a> on the HDWSBBL website.</p>");
			echo $after_widget;
		}
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
	$instance = $old_instance;
	$instance['title'] = strip_tags($new_instance['title']);
        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {
        //I have to fudge this one!
        $title = "Team Identity"
        ?>
            <p><?php _e($title); ?></p>
        <?php
    }

} // class bbtnn_TeamIdentity

// register FooWidget widget
add_action('widgets_init', create_function('', 'return register_widget("bbtnnn_teamidentity");'));


/**
 * bbtnnn_RecentMatches Class
 */
class bbtnnn_recentmatches extends WP_Widget {
    /** constructor */
    function bbtnnn_recentmatches() {
		$widget_ops = array( 'classname' => 'recent_matches', 'description' => 'Displays the last X number of matches your team has played in.' );
		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'bbtnn_recentmatches' );
		parent::WP_Widget( 'bbtnn_recentmatches', 'HDWSBBL - Recent Matches', $widget_ops, $control_ops );
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {
    	global $wpdb;
        extract( $args );
        $title = "Recent Matches";
        $nummatches = apply_filters('widget_title', $instance['nummatches']);
        ?>
		<?php echo $before_widget;
		if ( $title )
                        echo $before_title . $title . $after_title;
		$options = get_option('bbtnn_child_options');

		$matchssql = "SELECT T.t_name AS TAname, R.t_name AS TBname, N.mt_result, M.m_teamA, M.m_teamB, M.m_teamAtd, M.m_teamBtd, M.m_teamAcas, M.m_teamBcas FROM ".$options['bb_tbl_pre']."match_team N, ".$options['bb_tbl_pre']."match M, ".$options['bb_tbl_pre']."team T, ".$options['bb_tbl_pre']."team R WHERE N.m_id = M.m_id AND M.m_teamA = T.t_id AND M.m_teamB = R.t_id AND N.t_id = ".$options['t_id']." ORDER BY M.m_date DESC LIMIT ".$nummatches;
		if ($matchs = $wpdb->get_results($matchssql)) {
			print("		<ul>\n");
			foreach ($matchs as $ms) {
				print("			<li>");
				if ("D" == $ms->mt_result) {
					print("<strong>Draw</strong> vs ");
				}
				else if ("W" == $ms->mt_result) {
					print("<strong>Win</strong> vs ");
				}
				else {
					print("<strong>Loss</strong> vs ");
				}
				if ($options['t_id'] == $ms->m_teamA) {
					print($ms->TBname." ".$ms->m_teamAtd." - ".$ms->m_teamBtd." (".$ms->m_teamAcas." - ".$ms->m_teamBcas.")</li>\n");
				}
				else if ($options['t_id'] == $ms->m_teamB) {
					print($ms->TAname." ".$ms->m_teamBtd." - ".$ms->m_teamAtd." (".$ms->m_teamBcas." - ".$ms->m_teamAcas.")</li>\n");
				}
			}
			print("		</ul>\n");
		}
		else {
			print("<p>This team has not made their HDWSBBL debut yet!</p>\n");
		}

			echo $after_widget;
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
	$instance = $old_instance;
	$instance['nummatches'] = strip_tags($new_instance['nummatches']);
        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {
        $nummatches = esc_attr($instance['nummatches']);
        ?>
            <p><label for="<?php echo $this->get_field_id('nummatches'); ?>"><?php _e('How many Matches do you want to display? (Numbers only please!):'); ?> <input class="widefat" id="<?php echo $this->get_field_id('nummatches'); ?>" name="<?php echo $this->get_field_name('nummatches'); ?>" type="text" value="<?php echo $nummatches; ?>" /></label></p>
        <?php
    }

} // class bbtnn_recentmatches

// register FooWidget widget
add_action('widgets_init', create_function('', 'return register_widget("bbtnnn_recentmatches");'));

/**
 * HDWSBBL achievements Class
 */
class bbtn_hdwsbblachievements extends WP_Widget {
	/** constructor */
	function bbtn_hdwsbblachievements() {
		$widget_ops = array( 'classname' => 'hdwsbbl_achievements', 'description' => 'Has your team won any Championships (or runners up, 2rd place etc) or won any devisions? If so display them! If you have not won any then nothing will be displayed!' );
		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'bbtnn_hdwsbblachievements' );
		parent::WP_Widget( 'bbtnn_hdwsbblachievements', 'HDWSBBL - Achievements', $widget_ops, $control_ops );
	}

	/** @see WP_Widget::widget */
	function widget($args, $instance) {
	   	global $wpdb;
		extract( $args );
		$title = apply_filters('widget_title', $instance['title']);

		$options = get_option('bbtnn_child_options');

		$awardssql = "SELECT A.a_id, A.a_name, C.c_name FROM ".$options['bb_tbl_pre']."awards A, ".$options['bb_tbl_pre']."team T, ".$options['bb_tbl_pre']."awards_team_comp S, ".$options['bb_tbl_pre']."comp C WHERE C.c_id = S.c_id AND S.t_id = T.t_id AND A.a_id = S.a_id AND (A.a_id = 1 OR A.a_id = 2 OR A.a_id = 3 OR A.a_id = 5 OR A.a_id = 6 OR A.a_id = 7 OR A.a_id = 8) AND T.t_id = ".$options['t_id']." ORDER BY A.a_id ASC";
		if ($awards = $wpdb->get_results($awardssql)) {

			echo $before_widget;
			if ( $title )
				echo $before_title . $title . $after_title;
			print("		<ul>\n");

			foreach ($awards as $aw) {
				print("			<li><strong>".$aw->a_name."</strong> - ".$aw->c_name."</li>\n");
			}
			print("		</ul>\n");
			echo $after_widget;
		}
	}

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
	$instance = $old_instance;
	$instance['title'] = strip_tags($new_instance['title']);
        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {
        //I have to fudge this one!
        $title = "HDWSBBL Achievements"
        ?>
            <p><?php _e($title); ?></p>
			<p><input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="hidden" value="<?php echo $title; ?>" /></p>
        <?php
    }
} // class hdwsbblachievements

// register hdwsbblachievements widget
add_action('widgets_init', create_function('', 'return register_widget("bbtn_hdwsbblachievements");'));

/**
 * Recent Team Sponsor Class
 */
class bbtn_teamsponsor extends WP_Widget {
	/** constructor */
	function bbtn_teamsponsor() {
		$widget_ops = array( 'classname' => 'team_sponsor', 'description' => 'Does your team have a sponsor? If so, enter their name here.' );
		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'bbtnn_recentpostsres' );
		parent::WP_Widget( 'bbtnn_recentpostsres', 'HDWSBBL - Team Sponsor', $widget_ops, $control_ops );
	}

	/** @see WP_Widget::widget */
	function widget($args, $instance) {
	   	global $wpdb;
		extract( $args );
		$title = apply_filters('widget_title', $instance['title']);
		$sponsor = apply_filters('widget_title', $instance['sponsor']);
		?>
			<?php echo $before_widget; ?>
<?php		$options = get_option('bbtnn_child_options');
		$teamsql = "SELECT T.t_name AS teamname FROM ".$options['bb_tbl_pre']."team T WHERE T.t_id = ".$options['t_id'];
		if ($team = $wpdb->get_row($teamsql)) {
			$tname = $team->teamname;
		}
		print("<p>".$tname." are proudly Sponsored by <strong>".$sponsor."</strong></p>");
?>
				<?php echo $after_widget; ?>
		<?php
	}

	/** @see WP_Widget::update */
	function update($new_instance, $old_instance) {
	$instance = $old_instance;
	$instance['title'] = strip_tags($new_instance['title']);
	$instance['sponsor'] = strip_tags($new_instance['sponsor']);
		return $instance;
	}

	/** @see WP_Widget::form */
	function form($instance) {
		$title = esc_attr($instance['title']);
		$sponsor = esc_attr($instance['sponsor']);
		?>
<p><?php _e($title); ?></p>
			<p><input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="hidden" value="<?php echo $title; ?>" /></p>
			<p><label for="<?php echo $this->get_field_id('sponsor'); ?>"><?php _e('Your Sponsor:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('sponsor'); ?>" name="<?php echo $this->get_field_name('sponsor'); ?>" type="text" value="<?php echo $sponsor; ?>" /></label></p>
		<?php
	}
} // class FooWidget

// register FooWidget widget
add_action('widgets_init', create_function('', 'return register_widget("bbtn_teamsponsor");'));

/**
 * bbtnnn_UpcomingMatches Class
 */
class bbtnnn_upcomingmatches extends WP_Widget {
    /** constructor */
    function bbtnnn_upcomingmatches() {
		$widget_ops = array( 'classname' => 'upcoming_matches', 'description' => 'Displays the next X upcoming fixtures your team will play in.(If there are any!)' );
		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'bbtnnn_upcomingmatches' );
		parent::WP_Widget( 'bbtnnn_upcomingmatches', 'HDWSBBL - Upcoming Fixtures', $widget_ops, $control_ops );
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {
    	global $wpdb;
        extract( $args );
        $title = "Upcoming Matches";
        $nummatches = apply_filters('widget_title', $instance['nummatches']);
        ?>
		<?php echo $before_widget;
		if ( $title )
                        echo $before_title . $title . $after_title;
		$options = get_option('bbtnn_child_options');

		$matchssql = "SELECT F.f_teamA, F.f_teamB, T.t_name AS tA, Y.t_name AS tB, C.c_name AS Comp FROM ".$options['bb_tbl_pre']."fixture F, ".$options['bb_tbl_pre']."division D, ".$options['bb_tbl_pre']."team T, ".$options['bb_tbl_pre']."team Y, ".$options['bb_tbl_pre']."comp C WHERE (F.f_teamA = ".$options['t_id']." OR F.f_teamB = ".$options['t_id'].") AND F.div_id = D.div_id AND F.f_teamA = T.t_id AND F.f_teamB = Y.t_id AND C.c_id = F.c_id AND C.c_counts = 1 AND F.f_complete = 0 ORDER BY F.f_date ASC LIMIT ".$nummatches;
		if ($matchs = $wpdb->get_results($matchssql)) {
			print("		<ul>\n");
			foreach ($matchs as $ms) {
				print("			<li>VS ");
				if ($options['t_id'] == $ms->f_teamA) {
					print("<strong>".$ms->tB."</strong> - ");
				}
				else if ($options['t_id'] == $ms->f_teamB) {
					print("<strong>".$ms->tA."</strong> - ");
				}
				print($ms->Comp."</li>\n");
			}
			print("		</ul>\n");
		}
		else {
			print("<p>No fixtures are scheduled at present!</p>\n");
		}

			echo $after_widget;
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
	$instance = $old_instance;
	$instance['nummatches'] = strip_tags($new_instance['nummatches']);
        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {
        $nummatches = esc_attr($instance['nummatches']);
        ?>
            <p><label for="<?php echo $this->get_field_id('nummatches'); ?>"><?php _e('How many Fixtures do you want to display? (Numbers only please!):'); ?> <input class="widefat" id="<?php echo $this->get_field_id('nummatches'); ?>" name="<?php echo $this->get_field_name('nummatches'); ?>" type="text" value="<?php echo $nummatches; ?>" /></label></p>
        <?php
    }

} // class bbtnn_upcomingmatches

// register FooWidget widget
add_action('widgets_init', create_function('', 'return register_widget("bbtnnn_upcomingmatches");'));


  ////////////////////
 // End of Widgets //
////////////////////

  ////////////////////
 // TNN Header Bar //
////////////////////
/*
	At present, Displays a Grey bar (if the CSS Is present in the theme! that gives links back to the networked sites!
	TO DO: Pull in a style sheet!
*/

function bbtn_header_bar_init() {
	$current_site = get_current_site();
	//For now, this will simply print the header bar. In the future it may do all kinds of fancy admin stuff so I cam calling a generic _init version so I dont have to modify code further down the line!
?>
	<div id="hdwsbblbox">
		<p>This team is part of the <a href="http://www.hdwsbbl.co.uk" title="Lean more about the HDWSBBL (External Link)">HDWSBBL</a> | <a href="http://<?php print($current_site->domain); ?>" title="View more team blogs from the HDWSBBL">View More Team Blogs</a></p>
	</div><!-- end of #hdwsbblbox -->
<?php
}

  ///////////////////////////
 // End of TNN Header Bar //
///////////////////////////
/************ Custom Login Box **********/

function bblm_custom_login() {

	$path = dirname(plugin_basename(__FILE__));
	if ( $path == '.' ) {
		$path = '';
		$csslnk = trailingslashit( plugins_url( $path ) ) . 'bbtn_child_login.css';
	} else {
		$csslnk = trailingslashit( plugins_url( '', __FILE__) ) . 'bbtn_child_login.css';
	}
	echo '<link rel="stylesheet" type="text/css" href="'.$csslnk.'" />';
}

add_action('login_head', 'bblm_custom_login');

function change_wp_login_url() {
    echo  get_option('siteurl');
}

add_filter('login_headerurl', 'change_wp_login_url');


function change_wp_login_title() {
    echo  'Powered by ' . get_option('blogname');
}
?>