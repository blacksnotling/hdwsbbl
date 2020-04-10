<?php
/** Tell WordPress to run oberwald_theme_setup() when the 'after_setup_theme' hook is run. */
add_action( 'after_setup_theme', 'oberwald_theme_setup' );

function oberwald_theme_setup() {

	/*
	 * Make theme available for translation.
	 * Translations can be filed at WordPress.org. See: https://translate.wordpress.org/projects/wp-themes/twentysixteen
	 * If you're building a theme based on Twenty Sixteen, use a find and replace
	 * to change 'twentysixteen' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'oberwald' );
	// This theme styles the visual editor with editor-style.css to match the theme style.
	//add_editor_style();

	// This theme uses post thumbnails
	//add_theme_support( 'post-thumbnails' );

	// Add default posts and comments RSS feed links to head
	add_theme_support( 'automatic-feed-links' );
	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => __( 'Primary Navigation', 'oberwald' ),
	) );
}

/**
 * Register widgetized areas,
 *
 * To override oberwald_widgets_init() in a child theme, remove the action hook and add your own
 * function tied to the init hook.
 *
 * @uses register_sidebar
 */
function oberwald_widgets_init() {
	register_sidebar(array(
		'name'=> __( 'sidebar-posts', 'oberwald' ),
		'id'=> 'sidebar-posts',
		'description' => __( 'Appears at the top of the sidebar area for all non-warzone pages and posts (unless the template blocks it).', 'oberwald' ),
		'before_widget' => '<li id="%1$s" class="widget %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h2>',
		'after_title' => '</h2>',
	));
	register_sidebar(array(
		'name'=> __( 'sidebar-common', 'oberwald' ),
		'id'=> 'sidebar-common',
		'description' => __( 'Appears below the page / post specific content on ALL Pages in the sidebar area.', 'oberwald' ),
    'before_widget' => '<li id="%1$s" class="widget %2$s">',
    'after_widget' => '</li>',
    'before_title' => '<h2>',
    'after_title' => '</h2>',
  ));
	register_sidebar(array(
		'name'=> __( 'sidebar-warzone', 'oberwald' ),
		'id'=> 'sidebar-warzone',
		'description' => __( 'Appears at the top of the sidebar for all WarZone pages (posts / category / warzone page).', 'oberwald' ),
    'before_widget' => '<li id="%1$s" class="widget %2$s">',
    'after_widget' => '</li>',
    'before_title' => '<h2>',
    'after_title' => '</h2>',
  ));
	register_sidebar(array(
		'name'=> __( 'maincontent-bottom', 'oberwald' ),
		'id'=> 'maincontent-bottom',
		'description' => __( 'Appears at the bottom of the maincontent - above the footer', 'oberwald' ),
    'before_widget' => '<div id="%1$s" class="content-bottom-dynamic %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<h3>',
    'after_title' => '</h3>',
  ));
}

/** Register sidebars by running bblm_widgets_init() on the widgets_init hook. */
add_action( 'widgets_init', 'oberwald_widgets_init' );

/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 *
 * To override this in a child theme, remove the filter and optionally add
 * your own function tied to the wp_page_menu_args filter hook.
 *
 */
function oberwald_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
};
add_filter( 'wp_page_menu_args', 'oberwald_page_menu_args' );

/**
 * Returns a "Continue Reading" link for excerpts
 *
 * @return string "Continue Reading" link
 */
function oberwald_continue_reading_link() {
	return '<p class="readmorelink">Continue reading <a href="'. get_permalink() . '">'.get_the_title().' &raquo;</a></p>';
}

/**
 * Replaces "[...]" (appended to automatically generated excerpts) with an ellipsis and oberwald_continue_reading_link().
 *
 * To override this in a child theme, remove the filter and add your own
 * function tied to the excerpt_more filter hook.
 *
 * @return string An ellipsis
 */
function oberwald_auto_excerpt_more( $more ) {
	return ' &hellip;' . oberwald_continue_reading_link();
}
add_filter( 'excerpt_more', 'oberwald_auto_excerpt_more' );

/**
 * Adds a pretty "Continue Reading" link to custom post excerpts.
 *
 * To override this link in a child theme, remove the filter and add your own
 * function tied to the get_the_excerpt filter hook.
 *
 * @return string Excerpt with a pretty "Continue Reading" link
 */
function oberwald_custom_excerpt_more( $output ) {
	if ( has_excerpt() && ! is_attachment() ) {
		$output .= oberwald_continue_reading_link();
	}
	return $output;
}
add_filter( 'get_the_excerpt', 'oberwald_custom_excerpt_more' );

/**
 * Prints HTML with meta information for the current post date/time and author.
 *
 */
function oberwald_posted_on() {
	printf( __( 'Posted on %1$s', 'oberwald' ),
		sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><span class="entry-date">%3$s</span></a>',
			get_permalink(),
			esc_attr( get_the_time() ),
			get_the_date()
		)
	);
}

/**
 * Prints HTML with meta information for the current post (category, tags and permalink).
 *
 */
function oberwald_posted_in() {
global $post;
	// Retrieves tag list of current post, separated by commas.
	$tag_list = get_the_tag_list( '', ', ' );

	if ( taxonomy_exists('post_teams') && taxonomy_exists('post_competitions') ) {
		//If the custom BBLM taxonomy exist
		$team_list = get_the_term_list( $post->ID, 'post_teams', '', ', ', '' );
		$comp_list = get_the_term_list( $post->ID, 'post_competitions', '', ', ', '' );
		if ( $tag_list && $team_list && $comp_list ) {
			$posted_in = __( 'This entry was posted in %1$s and tagged %2$s. It mentions %5$s in the %6$s. &lt;<a href="%3$s" title="Permalink to %4$s" rel="bookmark">Permalink</a>&gt;.', 'oberwald' );
		} else if ( $tag_list && $team_list ) {
			$posted_in = __( 'This entry was posted in %1$s and tagged %2$s. It mentions %5$s. &lt;<a href="%3$s" title="Permalink to %4$s" rel="bookmark">Permalink</a>&gt;.', 'oberwald' );
		} else if ( $tag_list && $comp_list ) {
			$posted_in = __( 'This entry was posted in %1$s and tagged %2$s. It discusses the %6$s. &lt;<a href="%3$s" title="Permalink to %4$s" rel="bookmark">Permalink</a>&gt;.', 'oberwald' );
		} else if ( $tag_list ) {
			$posted_in = __( 'This entry was posted in %1$s and tagged %2$s. &lt;<a href="%3$s" title="Permalink to %4$s" rel="bookmark">Permalink</a>&gt;.', 'oberwald' );
		} else if ( $comp_list && $team_list ) {
			$posted_in = __( 'This entry was posted in %1$s. It mentions %5$s in the %6$s. &lt;<a href="%3$s" title="Permalink to %4$s" rel="bookmark">Permalink</a>&gt;.', 'oberwald' );
		} else if ( $team_list ) {
			$posted_in = __( 'This entry was posted in %1$s. It mentions %5$s. &lt;<a href="%3$s" title="Permalink to %4$s" rel="bookmark">Permalink</a>&gt;.', 'oberwald' );
		} else if ( $comp_list ) {
			$posted_in = __( 'This entry was posted in %1$s. It discusses the %6$s. &lt;<a href="%3$s" title="Permalink to %4$s" rel="bookmark">Permalink</a>&gt;.', 'oberwald' );
		} elseif ( is_object_in_taxonomy( get_post_type(), 'category' ) ) {
			$posted_in = __( 'This entry was posted in %1$s. &lt;<a href="%3$s" title="Permalink to %4$s" rel="bookmark">Permalink</a>&gt;.', 'oberwald' );
		} else {
			$posted_in = __( '&lt;<a href="%3$s" title="Permalink to %4$s" rel="bookmark">Permalink</a>&gt;.', 'oberwald' );
		}
		// Prints the string, replacing the placeholders.
		printf(
			$posted_in,
			get_the_category_list( ', ' ),
			$tag_list,
			get_permalink(),
			the_title_attribute( 'echo=0' ),
			$team_list,
			$comp_list
		);
	}
	else {
		//The custom BBLM taxonomy don't exist
		if ( $tag_list ) {
			$posted_in = __( 'This entry was posted in %1$s and tagged %2$s. &lt;<a href="%3$s" title="Permalink to %4$s" rel="bookmark">Permalink</a>&gt;.', 'oberwald' );
		} elseif ( is_object_in_taxonomy( get_post_type(), 'category' ) ) {
			$posted_in = __( 'This entry was posted in %1$s. &lt;<a href="%3$s" title="Permalink to %4$s" rel="bookmark">Permalink</a>&gt;.', 'oberwald' );
		} else {
			$posted_in = __( '&lt;<a href="%3$s" title="Permalink to %4$s" rel="bookmark">Permalink</a>&gt;.', 'oberwald' );
		}
		// Prints the string, replacing the placeholders.
		printf(
			$posted_in,
			get_the_category_list( ', ' ),
			$tag_list,
			get_permalink(),
			the_title_attribute( 'echo=0' )
		);
	}
}

/**
 * A simple wrapper function to display the number of comments.
 * A wrapper function is used so that if the text needs to be updated in the future I only hve to change it in one place.
 *
 */
function oberwald_comments_link() {
	comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;');
}

/**
 * Add specific CSS class by filter to body_class
 *
 */
add_filter('body_class','oberwald_add_body_class');

function oberwald_add_body_class($classes) {
	if ( is_category( 'warzone' ) || is_page('warzone') || ( in_category( 'warzone' ) && is_single() ) ) {
		// add 'section-warzone' to the $classes array if it is part of the Warzone section
		$classes[] = 'section-warzone';
	}
	return $classes;
}

/**
 * Prints the breadcrumbs
 * <holder function for now>
 */
function oberwald_breadcrumb() {

	global $post;

	if ( is_404() ) {

		echo '<a href="' . home_url() . '" title="Back to the front of the ' . bblm_get_league_name() . '">' . bblm_get_league_name() . '</a> &raquo; Page not found ';

	} //end of is_404
	else {

	echo '<a href="' . home_url() . '" title="Back to the front of the ' . bblm_get_league_name() . '">' . bblm_get_league_name() . '</a> &raquo; ';

	    // If there is a parent, display the link.
	    $parent_title = get_the_title( $post->post_parent );

			if ( $parent_title != wp_get_post_parent_id( $post->ID )) {
					echo '<a href="' . esc_url( get_permalink( $post->post_parent ) ) . '" alt="' . esc_attr( $parent_title ) . '">' . $parent_title . '</a> » ';
			}
	    // Then give the title of the current page.
			if ( is_single() && !is_date() ) {
				echo the_title();
			}
			elseif ( is_page( 'Warzone' ) ) {
				echo bblm_get_league_name() . ':WarZone';
			}
			else {
				echo 'Archive';
			}

	} //end of is_404 else
	return true;

}


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

/**
 * Template for comments and pingbacks.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * Based off the twerntyeleven theme
 */
function oberwald_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p><?php _e( 'Pingback:', 'oberwald' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( 'Edit', 'oberwald' ), '<span class="edit-link">', '</span>' ); ?></p>
	<?php
			break;
		default :
	?>
	<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
		<div class="comment-author vcard">
			<?php echo get_avatar( $comment, 39 ); ?>
			</div><!-- .comment-author .vcard -->
			<div class="comment-meta">
				<?php
				printf( __( '<strong>%1$s</strong><br /> on %2$s <span class="says">said:</span>', 'oberwald' ),
					sprintf( '<span class="fn">%s</span>', get_comment_author_link() ),
					sprintf( '<a href="%1$s"><time pubdate datetime="%2$s">%3$s</time></a>',
						esc_url( get_comment_link( $comment->comment_ID ) ),
						get_comment_time( 'c' ),
						sprintf( __( '%1$s at %2$s', 'oberwald' ), get_comment_date(), get_comment_time() )
					)
				);
				?>
			</div><!-- end of .comment-meta -->

			<?php edit_comment_link( __( 'Edit', 'oberwald' ), '<span class="edit-link">', '</span>' ); ?>

			<?php if ( $comment->comment_approved == '0' ) : ?>
				<span class="bblm_info"><?php _e( 'Your comment is awaiting moderation.', 'oberwald' ); ?></span>
				<br />
			<?php endif; ?>

			<div class="comment-content"><?php comment_text(); ?></div>

			<div class="comment-reply-link">
				<?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply <span>&darr;</span>', 'oberwald' ), 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
			</div><!-- .reply -->

	<?php
			break;
	endswitch;
}
/************ Custom Login Box **********/

function bblm_custom_login() {
	echo '<link rel="stylesheet" type="text/css" href="' . get_stylesheet_directory_uri() . '/includes/oberwald_login.css" />';
}

add_action('login_head', 'bblm_custom_login');

function bblm_wp_login_url() {
    return home_url();
}

add_filter('login_headerurl', 'bblm_wp_login_url');


function bblm_wp_login_title() {
		return 'Powered by the ' . bblm_get_league_name();
}

add_filter('login_headertitle', 'bblm_wp_login_title');
