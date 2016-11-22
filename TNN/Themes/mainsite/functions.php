<?php
//Register Sidebar as Dynamic
if ( function_exists('register_sidebar') )
	register_sidebar(array('name'=>'sidebar-posts',
        'before_widget' => '<li id="%1$s" class="widget %2$s">',
        'after_widget' => '</li>',
        'before_title' => '<h2>',
        'after_title' => '</h2>',
    ));
	register_sidebar(array('name'=>'sidebar-common',
        'before_widget' => '<li id="%1$s" class="widget %2$s">',
        'after_widget' => '</li>',
        'before_title' => '<h2>',
        'after_title' => '</h2>',
    ));

function TimeAgoInWords($from_time, $include_seconds = false) {
//http://yoast.com/wordpress-functions-supercharge-theme/
  $to_time = time();
  $mindist = round(abs($to_time - $from_time) / 60);
  $secdist = round(abs($to_time - $from_time));

  if ($mindist >= 0 and $mindist <= 1) {
    if (!$include_seconds) {
      return ($mindist == 0) ? 'under a minute' : '1 minute';
	} else {
      if ($secdist >= 0 and $secdist <= 4) {
        return 'less than 5 seconds';
      } elseif ($secdist >= 5 and $secdist <= 9) {
        return 'less than 10 seconds';
      } elseif ($secdist >= 10 and $secdist <= 19) {
        return 'less than 20 seconds';
      } elseif ($secdist >= 20 and $secdist <= 39) {
        return 'half a minute';
      } elseif ($secdist >= 40 and $secdist <= 59) {
        return 'less than a minute';
      } else {
        return '1 minute';
      }
    }
  } elseif ($mindist >= 2 and $mindist <= 44) {
    return $mindist . ' minutes';
  } elseif ($mindist >= 45 and $mindist <= 89) {
    return '1 hour';
  } elseif ($mindist >= 90 and $mindist <= 1439) {
    return round(floatval($mindist) / 60.0) . ' hours';
  } elseif ($mindist >= 1440 and $mindist <= 2879) {
    return '1 day';
  } elseif ($mindist >= 2880 and $mindist <= 43199) {
    return round(floatval($mindist) / 1440) . ' days';
  } elseif ($mindist >= 43200 and $mindist <= 86399) {
    return '1 month';
  } elseif ($mindist >= 86400 and $mindist <= 525599) {
    return round(floatval($mindist) / 43200) . ' months';
  } elseif ($mindist >= 525600 and $mindist <= 1051199) {
    return '1 year ish';
  } else {
    return 'over ' . round(floatval($mindist) / 525600) . ' years';
  }
}

function in_array_recursive($needle, $haystack) {

    $it = new RecursiveIteratorIterator(new RecursiveArrayIterator($haystack));

    foreach($it AS $element) {
        if($element == $needle) {
            return true;
        }
    }

    return false;
}

// Custom callback to list comments in the your-theme style
// based off http://themeshaper.com/wordpress-theme-comments-template-tutorial/
function custom_comments($comment, $args, $depth) {
  $GLOBALS['comment'] = $comment;
        $GLOBALS['comment_depth'] = $depth;
  ?>
        <li id="comment-<?php comment_ID() ?>" <?php comment_class() ?>>
                <div class="comment-author vcard"><?php commenter_link() ?></div>
                <div class="comment-meta"><?php printf(__('Posted %1$s at %2$s <span class="meta-sep">|</span> <a href="%3$s" title="Permalink to this comment">Permalink</a>', 'your-theme'),
                                        get_comment_date(),
                                        get_comment_time(),
                                        '#comment-' . get_comment_ID() );
                                        edit_comment_link(__('Edit', 'your-theme'), ' <span class="meta-sep">|</span> <span class="edit-link">', '</span>'); ?></div>

	       		<div class="comment-content">
	                	<?php comment_text() ?>
	                </div>

                <?php // echo the comment reply link
                        if($args['type'] == 'all' || get_comment_type() == 'comment') :
                                comment_reply_link(array_merge($args, array(
                                        'reply_text' => __('Reply','your-theme'),
                                        'login_text' => __('Log in to reply.','your-theme'),
                                        'depth' => $depth,
                                        'before' => '<div class="comment-reply-link">',
                                        'after' => '</div>'
                                )));
                        endif;
?>

  				<?php if ($comment->comment_approved == '0') _e("\t\t\t\t\t<span class='info'>Your comment is awaiting moderation.</span>\n", 'your-theme') ?>



<?php } // end custom_comments

// Produces an avatar image with the hCard-compliant photo class
// http://themeshaper.com/wordpress-theme-comments-template-tutorial/
function commenter_link() {
        $commenter = get_comment_author_link();
        if ( ereg( '<a[^>]* class=[^>]+>', $commenter ) ) {
                $commenter = ereg_replace( '(<a[^>]* class=[\'"]?)', '\\1url ' , $commenter );
        } else {
                $commenter = ereg_replace( '(<a )/', '\\1class="url "' , $commenter );
        }
        $avatar_email = get_comment_author_email();
        $avatar = str_replace( "class='avatar", "class='photo avatar", get_avatar( $avatar_email, 38 ) );
        echo $avatar . ' <span class="fn n">' . $commenter . '</span>';
} // end commenter_link

//Prints a list of blogs on the network in updated order (newly updated at the top)
function bbtnn_print_sites_list() {
	$blogs = get_last_updated();
		if( is_array( $blogs ) ) {
?>
		<ul>
<?php 		foreach( $blogs as $details ) {
?>			<li><a href="http://<?php echo $details[ 'domain' ] . $details[ 'path' ] ?>">
<?php 			echo get_blog_option( $details[ 'blog_id' ], 'blogname' ) ?></a></li>
<?php
	        }
?>
		</ul>
<?php
		}
}

function bbtnn_print_sites_list_detailed() {
	$blogs = get_last_updated();
		if( is_array( $blogs ) ) {
?>
		<ul>
<?php 		foreach( $blogs as $details ) {
				if (1 != $details[ 'blog_id' ]) {
?>			<li><a href="http://<?php echo $details[ 'domain' ] . $details[ 'path' ] ?>">
<?php	 			$blog_details = get_blog_details($details[ 'blog_id' ]);
					echo $blog_details->blogname; ?></a> - <em><?php echo date("F j, Y",convert_datetime($blog_details->last_updated)); ?></em></li>
<?php
				}
	        }
?>
		</ul>
<?php
		}
}

// from MySQL to UNIX timestamp
function convert_datetime($str)
{

list($date, $time) = explode(' ', $str);
list($year, $month, $day) = explode('-', $date);
list($hour, $minute, $second) = explode(':', $time);

$timestamp = mktime($hour, $minute, $second, $month, $day, $year);

return $timestamp;
}



  /////////////
 // Widgets //
/////////////
// http://codex.wordpress.org/Widgets_API
// http://justintadlock.com/archives/2009/05/26/the-complete-guide-to-creating-widgets-in-wordpress-28
/**
 * Recent Posts Restricted Class
 */
class bbtn_RecentPostsRes extends WP_Widget {
	/** constructor */
	function bbtn_RecentPostsRes() {
		$widget_ops = array( 'classname' => 'Recent Posts from a specific category', 'description' => 'Displays the last X number of posts from a specific category.' );
		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'bbtnn_recentpostsres' );
		parent::WP_Widget( 'bbtnn_recentpostsres', 'HDWSBBL - Recent Posts (restricted)', $widget_ops, $control_ops );
	}

	/** @see WP_Widget::widget */
	function widget($args, $instance) {
		extract( $args );
		$title = apply_filters('widget_title', $instance['title']);
		$cat = apply_filters('widget_title', $instance['rpr_cat']);
		$postnum = apply_filters('widget_title', $instance['rpr_postnum']);
		?>
			<?php echo $before_widget; ?>
				<?php if ( $title )
					echo $before_title . $title . $after_title; ?>
					<ul>
<?php				global $post;
					$input = 'numberposts='.$postnum.'&category='.$cat.'&order=ASC&orderby=title';
					$postslist = get_posts($input);
					foreach ($postslist as $post) :
						setup_postdata($post);
 ?>
						<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
					<?php endforeach; ?>
					</ul>
				<?php echo $after_widget; ?>
		<?php
	}

	/** @see WP_Widget::update */
	function update($new_instance, $old_instance) {
	$instance = $old_instance;
	$instance['title'] = strip_tags($new_instance['title']);
	$instance['rpr_cat'] = strip_tags($new_instance['rpr_cat']);
	$instance['rpr_postnum'] = strip_tags($new_instance['rpr_postnum']);
		return $instance;
	}

	/** @see WP_Widget::form */
	function form($instance) {
		$title = esc_attr($instance['title']);
		$cat = esc_attr($instance['rpr_cat']);
		$postnum = esc_attr($instance['rpr_postnum']);
		?>
			<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label><br />
			<label for="<?php echo $this->get_field_id('rpr_cat'); ?>"><?php _e('Category to list from:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('rpr_cat'); ?>" name="<?php echo $this->get_field_name('rpr_cat'); ?>" type="text" value="<?php echo $cat; ?>" /></label><br />
			<label for="<?php echo $this->get_field_id('rpr_postnum'); ?>"><?php _e('Number of Posts to show:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('rpr_postnum'); ?>" name="<?php echo $this->get_field_name('rpr_postnum'); ?>" type="text" value="<?php echo $postnum; ?>" /></label></p>
		<?php
	}
} // class FooWidget

// register FooWidget widget
add_action('widgets_init', create_function('', 'return register_widget("bbtn_RecentPostsRes");'));



  ////////////////////
 // End of Widgets //
////////////////////
/**
 * Prints HTML with meta information for the current post (category, tags and permalink).
 *
 * @since 1.1
 */
function maintheme_posted_in() {
	// Retrieves tag list of current post, separated by commas.
	$tag_list = get_the_tag_list( '', ', ' );
	if ( $tag_list ) {
		$posted_in = __( 'Filed under %1$s and Tagged %2$s. ', 'twentyten' );
	} elseif ( is_object_in_taxonomy( get_post_type(), 'category' ) ) {
		$posted_in = __( 'Filed under %1$s.', 'twentyten' );
	} else {
		$posted_in = __( '', 'twentyten' );
	}
	// Prints the string, replacing the placeholders.
	printf(
		$posted_in,
		get_the_category_list( ', ' ),
		$tag_list,
		the_title_attribute( 'echo=0' )
	);
}
?>