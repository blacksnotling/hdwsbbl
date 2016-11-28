<?php
/**
 * Register widgetized areas,
 *
 * To override tb1mainsite_widgets_init() in a child theme, remove the action hook and add your own
 * function tied to the init hook.
 *
 * @uses register_sidebar
 */
 function tb1mainsite_widgets_init() {
		register_sidebar(array(
			'name'=> __( 'sidebar-posts', 'tb1mainsite' ),
			'id'=> 'sidebar-1',
			'description' => __( 'Appears at the top of the sidebar area for all pages and posts (unless the teamplate blocks it).', 'tb1mainsite' ),
			'before_widget' => '<li id="%1$s" class="widget %2$s">',
			'after_widget' => '</li>',
			'before_title' => '<h2>',
			'after_title' => '</h2>',
		));
	}

	/** Register sidebars by running tnnmainsite_widgets_init() on the widgets_init hook. */
add_action( 'widgets_init', 'tb1mainsite_widgets_init' );

//Define what WP features are to be used
add_theme_support( 'menus' );


/**
 * Prints HTML with meta information for the current post—date/time and author.
 *
 * @since 1.0
 */
function teamtheme_posted_on() {
	printf( __( '<span class="%1$s">Posted on</span> %2$s <span class="meta-sep">by</span> %3$s', 'twentyten' ),
		'meta-prep meta-prep-author',
		sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><span class="entry-date">%3$s</span></a>',
			get_permalink(),
			esc_attr( get_the_time() ),
			get_the_date()
		),
		sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s">%3$s</a></span>',
			get_author_posts_url( get_the_author_meta( 'ID' ) ),
			sprintf( esc_attr__( 'View all posts by %s', 'twentyten' ), get_the_author() ),
			get_the_author()
		)
	);
}
/**
 * Prints HTML with meta information for the current post (category, tags and permalink).
 *
 * @since 1.0
 */
function teamtheme_posted_in() {
	// Retrieves tag list of current post, separated by commas.
	$tag_list = get_the_tag_list( '', ', ' );
	if ( $tag_list ) {
		$posted_in = __( 'This entry was posted in %1$s and tagged %2$s. ', 'twentyten' );
	} elseif ( is_object_in_taxonomy( get_post_type(), 'category' ) ) {
		$posted_in = __( 'This entry was posted in %1$s.', 'twentyten' );
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


?>
