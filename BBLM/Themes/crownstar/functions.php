<?php
/**
 * Crownstar functions and definitions
 *
 * @package Crownstar
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 620; /* pixels */
}
if ( ! isset( $full_content_width ) ) {
	$full_content_width = 960; /* pixels */
}

if ( ! function_exists( 'crownstar_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function crownstar_setup() {

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	// Add featured image support.
	add_theme_support( 'post-thumbnails' );

	// Add title tag support.
	add_theme_support( 'title-tag' );

	// Add custom header support.
	add_theme_support( 'custom-header', array(
		'default-image'          => '',
		'width'                  => 1000,
		'height'                 => 150,
		'flex-height'            => true,
		'flex-width'             => true,
		'uploads'                => true,
		'random-default'         => false,
		'header-text'            => true,
		'default-text-color'     => apply_filters( 'crownstar_default_header_text_color', '222222' ),
	) );

	add_editor_style();

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => __( 'Primary Navigation', 'crownstar' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption',
	) );

	/*
	 * Enable support for Post Formats.
	 * See http://codex.wordpress.org/Post_Formats
	 */
	add_theme_support( 'post-formats', array(
		'aside', 'image', 'video', 'quote', 'link',
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'crownstar_custom_background_args', array(
		'default-color' => 'e8e8e8',
		'default-image' => '',
	) ) );

}
endif; // crownstar_setup
add_action( 'after_setup_theme', 'crownstar_setup' );

if ( ! function_exists( 'crownstar_get_search_form' ) ):
function crownstar_get_search_form( $form ) {
	//return $untranslated_text;
	$form = str_replace( 'value="' . esc_attr_x( 'Search', 'submit button' ) . '"', 'value="&#61817;" title="' . esc_attr_x( 'Search', 'submit button' ) . '"', $form );
	return $form;
}
add_filter( 'get_search_form', 'crownstar_get_search_form' );
endif;

/**
 * Register widget area.
 */
if ( ! function_exists( 'crownstar_widgets_init' ) ):
function crownstar_widgets_init() {
	$sidebar = crownstar_get_sidebar_setting();

	if ( in_array( $sidebar, array( 'left', 'right' ) ) ) {
		register_sidebar( array(
			'name'          => __( 'Sidebar', 'crownstar' ),
			'id'            => 'sidebar-1',
			'description'   => '',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h1 class="widget-title">',
			'after_title'   => '</h1>',
		) );
	} else if ( 'double' === $sidebar ) {
		register_sidebar( array(
			'name'          => sprintf( __( 'Sidebar %d', 'crownstar' ), 1 ),
			'id'            => 'sidebar-1',
			'description'   => '',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h1 class="widget-title">',
			'after_title'   => '</h1>',
		) );

		register_sidebar( array(
			'name'          => sprintf( __( 'Sidebar %d', 'crownstar' ), 2 ),
			'id'            => 'sidebar-2',
			'description'   => '',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h1 class="widget-title">',
			'after_title'   => '</h1>',
		) );
	}

	register_sidebar( array(
		'name'          => __( 'Header', 'crownstar' ),
		'id'            => 'header-1',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );

	register_sidebar( array(
		'name'          => __( 'Homepage', 'crownstar' ),
		'id'            => 'homepage-1',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );

	for ( $i = 1; $i <= 3; $i++ ) {
		register_sidebar( array(
			'name' 				=> sprintf( __( 'Footer %d', 'crownstar' ), $i ),
			'id' 				=> sprintf( 'footer-%d', $i ),
			'description' 		=> sprintf( __( 'Widgetized Footer Region %d.', 'crownstar' ), $i ),
			'before_widget' 	=> '<aside id="%1$s" class="widget %2$s">',
			'after_widget' 		=> '</aside>',
			'before_title' 		=> '<h3 class="widget-title">',
			'after_title' 		=> '</h3>',
		) );
	}
}
add_action( 'widgets_init', 'crownstar_widgets_init' );
endif;

/**
 * Enqueue scripts and styles.
 */
if ( ! function_exists( 'crownstar_scripts' ) ):
function crownstar_scripts() {
	// Load icon font.
	wp_enqueue_style( 'dashicons' );

	// Load web fonts.
	//wp_enqueue_style( 'crownstar-lato', add_query_arg( array( 'family' => 'Lato:400,700,400italic,700italic', 'subset' => 'latin-ext' ), "//fonts.googleapis.com/css", array(), null ) );
	//wp_enqueue_style( 'crownstar-oswald', add_query_arg( array( 'family' => 'Oswald:400,700', 'subset' => 'latin-ext' ), "//fonts.googleapis.com/css", array(), null ) );
	wp_enqueue_style( 'crownstar-graduate', add_query_arg( array( 'family' => 'Graduate', 'subset' => 'latin-ext' ), "//fonts.googleapis.com/css", array(), null ) );
	wp_enqueue_style( 'crownstar-montserrat', add_query_arg( array( 'family' => 'Montserrat:400,700', 'subset' => 'latin-ext' ), "//fonts.googleapis.com/css", array(), null ) );

	// Load our framework stylesheet.
	wp_enqueue_style( 'crownstar-framework-style', get_template_directory_uri() . '/framework.css' );

	// Load our main stylesheet.
	wp_enqueue_style( 'crownstar-style', get_stylesheet_uri() );

	// Custom colors
	add_action( 'wp_print_scripts', 'crownstar_custom_colors', 30 );

	wp_enqueue_script( 'crownstar-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20200404', true );

	wp_enqueue_script( 'crownstar-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20200404', true );

	crownstar_enqueue_timeago();

	wp_enqueue_script( 'crownstar-scripts', get_template_directory_uri() . '/js/scripts.js', array( 'jquery', 'jquery-timeago' ), '0.9', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'crownstar_scripts' );
endif;

/**
 * Enqueue customize scripts.
 */
if ( ! function_exists( 'crownstar_customize_scripts' ) ):
function crownstar_customize_scripts() {
	$screen = get_current_screen();
	if ( 'customize' == $screen->id ) {
		wp_enqueue_script( 'crownstar-customize-panel', get_template_directory_uri() . '/js/customize-panel.js', array( 'jquery' ), '1.3.2', true );
	}
}
add_action( 'admin_enqueue_scripts', 'crownstar_customize_scripts' );
endif;

/**
 * Enqueue jQuery timeago if locale available.
 */
if ( ! function_exists( 'crownstar_enqueue_timeago' ) ):
function crownstar_enqueue_timeago() {
	$locale = get_locale();
	$locale = str_replace( '_', '-', $locale );
	$file = '/js/locales/jquery.timeago.' . $locale . '.js';

	// Check if locale exists with country code
	if ( ! is_readable( get_template_directory() . $file ) ) {
		$locale = substr( $locale, 0, 2 );
		$file = '/js/locales/jquery.timeago.' . $locale . '.js';

		// Check if locale exists without country code
		if ( ! is_readable( get_template_directory() . $file ) ) {
			return;
		}
	}

	// Enqueue script
	wp_enqueue_script( 'jquery-timeago', get_template_directory_uri() . '/js/jquery.timeago.js', array( 'jquery' ), '1.4.1', true );

	// Enqueue locale
	wp_enqueue_script( 'jquery-timeago-' . $locale, get_template_directory_uri() . $file, array( 'jquery', 'jquery-timeago' ), '1.4.1', true );
}
endif;

/**
 * Enqueue scripts and styles.
 */
if ( ! function_exists( 'crownstar_custom_colors' ) ):
function crownstar_custom_colors() {

	/*
	 * Get color options set via Customizer.
	 * @see crownstar_customize_register()
	 */
	$colors = (array) get_option( 'crownstar', array() );
	$colors = array_map( 'esc_attr', $colors );

	// Get layout options
	if ( empty( $colors['content_width'] ) ) {
		$width = 1000;
	} else {
		$width = crownstar_sanitize_content_width( $colors['content_width'] );
	}

	global $content_width;

	if ( empty( $colors['sidebar'] ) ) {
		$sidebar = '';
	} else {
		$sidebar = $colors['sidebar'];
	}

	if ( 'no' == $sidebar || is_page_template( 'template-fullwidth.php' ) ) {
		$content_width = $width - 40;
	} elseif ( 'double' === $sidebar )  {
		$content_width = $width * .52 - 40;
	} else {
		$content_width = $width * .66 - 40;
	}

	?>
	<style type="text/css"> /* crownstar Custom Layout */
	@media screen and (min-width: 1025px) {
		.site-header, .site-content, .site-footer, .site-info {
			width: <?php echo $width; ?>px; }
	}
	</style>
	<?php

	// Return if colors not customized
	if ( ! isset( $colors['customize'] ) ) {
		$enabled = get_option( ' crownstar_enable_frontend_css', 'no' );
		if ( 'yes' !== $enabled ) return;
	} elseif ( ! $colors['customize'] ) {
		return;
	}

	$colors['sponsors_background'] = get_option( 'crownstar_footer_sponsors_css_background', '#f4f4f4' );

	// Defaults
	if ( empty( $colors['primary'] ) ) $colors['primary'] = '#2b353e';
	if ( empty( $colors['background'] ) ) $colors['background'] = '#f4f4f4';
	if ( empty( $colors['content'] ) ) $colors['content'] = '#222222';
	if ( empty( $colors['text'] ) ) $colors['text'] = '#222222';
	if ( empty( $colors['heading'] ) ) $colors['heading'] = '#ffffff';
	if ( empty( $colors['link'] ) ) $colors['link'] = '#ba0000';
	if ( empty( $colors['content_background'] ) ) $colors['content_background'] = '#ffffff';

	// Calculate colors
	$colors['highlight'] = crownstar_hex_lighter( $colors['background'], 30, true );
	$colors['border'] = crownstar_hex_darker( $colors['background'], 20, true );
	$colors['text_lighter'] = crownstar_hex_mix( $colors['text'], $colors['background'] );
	$colors['heading_alpha'] = 'rgba(' . implode( ', ', crownstar_rgb_from_hex( $colors['heading'] ) ) . ', 0.7)';
	$colors['link_dark'] = crownstar_hex_darker( $colors['link'], 30, true );
	$colors['link_hover'] = crownstar_hex_darker( $colors['link'], 30, true );
	$colors['sponsors_border'] = crownstar_hex_darker( $colors['sponsors_background'], 20, true );
	$colors['content_border'] = crownstar_hex_darker( $colors['content_background'], 31, true );

	?>
	<style type="text/css"> /* Crownstar Custom Colors */
	.site-content,
	.main-navigation .nav-menu > .menu-item-has-children:hover > a,
	.main-navigation li.menu-item-has-children:hover a,
	.main-navigation ul ul { background: <?php echo $colors['content_background']; ?>; }
	pre,
	code,
	kbd,
	tt,
	var,
	table,
	.main-navigation li.menu-item-has-children:hover a:hover,
	.main-navigation ul ul li.page_item_has_children:hover > a,
	.entry-footer-links,
	.comment-content,
	.bblm-table-wrapper .dataTables_paginate,
	.bblm-event-staff,
	.bblm-template-countdown .event-name,
	.bblm-template-countdown .event-venue,
	.bblm-template-countdown .event-league,
	.bblm-template-countdown time span,
	.bblm-template-details dl,
	.mega-slider__row,
	.opta-widget-container form {
		background: <?php echo $colors['background']; ?>; }
	.comment-content:after {
		border-right-color: <?php echo $colors['background']; ?>; }
	.widget_calendar #today,
	.bblm-highlight,
	.bblm-template-event-calendar #today,
	.bblm-template-event-blocks .event-title,
	.mega-slider__row:hover {
		background: <?php echo $colors['highlight']; ?>; }
	.bblm-tournament-bracket .bblm-team .bblm-team-name:before {
		border-left-color: <?php echo $colors['highlight']; ?>;
		border-right-color: <?php echo $colors['highlight']; ?>; }
	.bblm-tournament-bracket .bblm-event {
		border-color: <?php echo $colors['highlight']; ?> !important; }
	caption,
	.main-navigation,
	.site-footer,
	.bblm-heading,
	.bblm-table-caption,
	.bblm-template-gallery .gallery-caption,
	.bblm-template-event-logos .bblm-team-result,
	.bblm-statistic-bar,
	.opta-widget-container h2 {
		background: <?php echo $colors['primary']; ?>; }
	pre,
	code,
	kbd,
	tt,
	var,
	table,
	th,
	td,
	tbody td,
	th:first-child, td:first-child,
	th:last-child, td:last-child,
	input[type="text"],
	input[type="email"],
	input[type="url"],
	input[type="password"],
	input[type="search"],
	textarea,
	.entry-footer-links,
	.comment-metadata .edit-link,
	.comment-content,
	.bblm-table-wrapper .dataTables_paginate,
	.bblm-event-staff,
	.bblm-template-countdown .event-name,
	.bblm-template-countdown .event-venue,
	.bblm-template-countdown .event-league,
	.bblm-template-countdown time span,
	.bblm-template-countdown time span:first-child,
	.bblm-template-event-blocks .event-title,
	.bblm-template-details dl,
	.bblm-template-tournament-bracket table,
	.bblm-template-tournament-bracket thead th,
	.mega-slider_row,
	.opta-widget-container form {
		border-color: <?php echo $colors['border']; ?>; }
	.comment-content:before {
		border-right-color: <?php echo $colors['border']; ?>; }
	.bblm-tab-menu {
		border-bottom-color: <?php echo $colors['content_border']; ?>; }
	body,
	button,
	input,
	select,
	textarea,
	.main-navigation .nav-menu > .menu-item-has-children:hover > a,
	.main-navigation ul ul a,
	.widget_recent_entries ul li:before,
	.widget_pages ul li:before,
	.widget_categories ul li:before,
	.widget_archive ul li:before,
	.widget_recent_comments ul li:before,
	.widget_nav_menu ul li:before,
	.widget_links ul li:before,
	.widget_meta ul li:before,
	.entry-title a,
	a .entry-title,
	.page-title a,
	a .page-title,
	.entry-title a:hover,
	a:hover .entry-title,
	.page-title a:hover,
	a:hover .page-title:hover {
		color: <?php echo $colors['content']; ?>; }
	pre,
	code,
	kbd,
	tt,
	var,
	table,
	.main-navigation li.menu-item-has-children:hover a:hover,
	.main-navigation ul ul li.page_item_has_children:hover > a,
	.entry-meta,
	.entry-footer-links,
	.comment-content,
	.bblm-data-table,
	.site-footer .bblm-data-table,
	.bblm-table-wrapper .dataTables_paginate,
	.bblm-template,
	.bblm-template-countdown .event-venue,
	.bblm-template-countdown .event-league,
	.bblm-template-countdown .event-name a,
	.bblm-template-countdown time span,
	.bblm-template-details dl,
	.bblm-template-event-blocks .event-title,
	.bblm-template-event-blocks .event-title a,
	.bblm-tournament-bracket .bblm-event .bblm-event-date {
		color: <?php echo $colors['text']; ?>; }
	.widget_recent_entries ul li a,
	.widget_pages ul li a,
	.widget_categories ul li a,
	.widget_archive ul li a,
	.widget_recent_comments ul li a,
	.widget_nav_menu ul li a,
	.widget_links ul li a,
	.widget_meta ul li a,
	.widget_calendar #prev a,
	.widget_calendar #next a,
	.nav-links a,
	.comment-metadata a,
	.comment-body .reply a,
	.wp-caption-text,
	.bblm-view-all-link,
	.bblm-template-event-calendar #prev a,
	.bblm-template-event-calendar #next a,
	.bblm-template-tournament-bracket .bblm-event-venue {
		color: <?php echo $colors['text_lighter']; ?>; }
	caption,
	button,
	input[type="button"],
	input[type="reset"],
	input[type="submit"],
	.main-navigation .nav-menu > li:hover > a,
	.main-navigation.toggled .menu-toggle,
	.site-footer,
	.bblm-template .gallery-caption,
	.bblm-template .gallery-caption a,
	.bblm-heading,
	.bblm-heading:hover,
	.bblm-heading a:hover,
	.bblm-table-caption,
	.bblm-template-event-logos .bblm-team-result,
	.bblm-template-tournament-bracket .bblm-result,
	.single-bblm_player .entry-header .entry-title strong {
		color: <?php echo $colors['heading']; ?>; }
	.main-navigation a,
	.main-navigation .menu-toggle {
		color: <?php echo $colors['heading_alpha']; ?>; }
	a,
	blockquote:before,
	q:before,
	.main-navigation ul ul .current-menu-item > a,
	.main-navigation ul ul .current-menu-parent > a,
	.main-navigation ul ul .current-menu-ancestor > a,
	.main-navigation ul ul .current_page_item > a,
	.main-navigation ul ul .current_page_parent > a,
	.main-navigation ul ul .current_page_ancestor > a,
	.main-navigation li.menu-item-has-children:hover ul .current-menu-item > a:hover,
	.main-navigation li.menu-item-has-children:hover ul .current-menu-parent > a:hover,
	.main-navigation li.menu-item-has-children:hover ul .current-menu-ancestor > a:hover,
	.main-navigation li.menu-item-has-children:hover ul .current_page_item > a:hover,
	.main-navigation li.menu-item-has-children:hover ul .current_page_parent > a:hover,
	.main-navigation li.menu-item-has-children:hover ul .current_page_ancestor > a:hover,
	.widget_recent_entries ul li a:hover,
	.widget_pages ul li a:hover,
	.widget_categories ul li a:hover,
	.widget_archive ul li a:hover,
	.widget_recent_comments ul li a:hover,
	.widget_nav_menu ul li a:hover,
	.widget_links ul li a:hover,
	.widget_meta ul li a:hover,
	.widget_calendar #prev a:hover,
	.widget_calendar #next a:hover,
	.nav-links a:hover,
	.sticky .entry-title:before,
	.comment-metadata a:hover,
	.comment-body .reply a:hover,
	.bblm-view-all-link:hover,
	.bblm-message {
		color: <?php echo $colors['link']; ?>; }
	cite:before,
	button,
	input[type="button"],
	input[type="reset"],
	input[type="submit"],
	.main-navigation .nav-menu > li:hover > a,
	.main-navigation .search-form .search-submit:hover,
	.nav-links .meta-nav,
	.entry-footer a,
	.bblm-template-player-gallery .gallery-item strong,
	.bblm-template-tournament-bracket .bblm-result,
	.single-bblm_player .entry-header .entry-title strong,
	.bblm-statistic-bar-fill,
	.mega-slider__row--active,
	.mega-slider__row--active:hover {
		background: <?php echo $colors['link']; ?>; }
	.bblm-message {
		border-color: <?php echo $colors['link']; ?>; }
	caption,
	.bblm-table-caption,
	.opta-widget-container h2 {
		border-top-color: <?php echo $colors['link']; ?>; }
	.bblm-tab-menu-item-active a {
		border-bottom-color: <?php echo $colors['link']; ?>; }
	button:hover,
	input[type="button"]:hover,
	input[type="reset"]:hover,
	input[type="submit"]:hover,
	button:focus,
	input[type="button"]:focus,
	input[type="reset"]:focus,
	input[type="submit"]:focus,
	button:active,
	input[type="button"]:active,
	input[type="reset"]:active,
	input[type="submit"]:active,
	.entry-footer a:hover,
	.nav-links a:hover .meta-nav,
	.bblm-template-tournament-bracket .bblm-event-title:hover .bblm-result {
		background: <?php echo $colors['link_dark']; ?>; }
	.widget_search .search-submit {
		border-color: <?php echo $colors['link_dark']; ?>; }
	a:hover {
		color: <?php echo $colors['link_hover']; ?>; }
	.bblm-template-event-logos {
		color: inherit; }
	.bblm-footer-sponsors .bblm-sponsors {
		border-color: <?php echo $colors['sponsors_border']; ?>; }
	@media screen and (max-width: 600px) {
		.main-navigation .nav-menu > li:hover > a,
		.main-navigation ul ul li.page_item_has_children:hover > a {
			color: <?php echo $colors['heading']; ?>;
			background: transparent; }
		.main-navigation .nav-menu li a:hover,
		.main-navigation .search-form .search-submit {
			color: <?php echo $colors['heading']; ?>;
			background: <?php echo $colors['link']; ?>; }
		.main-navigation .nav-menu > .menu-item-has-children:hover > a,
		.main-navigation li.menu-item-has-children:hover a {
			background: transparent; }
		.main-navigation ul ul {
			background: rgba(0, 0, 0, 0.1); }
		.main-navigation .nav-menu > .menu-item-has-children:hover > a:hover,
		.main-navigation li.menu-item-has-children:hover a:hover {
			background: <?php echo $colors['link']; ?>;
			color: #fff;
		}
		.main-navigation ul ul a,
		.main-navigation .nav-menu > .menu-item-has-children:hover > a {
			color: <?php echo $colors['heading_alpha']; ?>; }
		.main-navigation .nav-menu > .current-menu-item > a,
		.main-navigation .nav-menu > .current-menu-parent > a,
		.main-navigation .nav-menu > .current-menu-ancestor > a,
		.main-navigation .nav-menu > .current_page_item > a,
		.main-navigation .nav-menu > .current_page_parent > a,
		.main-navigation .nav-menu > .current_page_ancestor > a,
		.main-navigation .nav-menu > .current-menu-item:hover > a,
		.main-navigation .nav-menu > .current-menu-parent:hover > a,
		.main-navigation .nav-menu > .current-menu-ancestor:hover > a,
		.main-navigation .nav-menu > .current_page_item:hover > a,
		.main-navigation .nav-menu > .current_page_parent:hover > a,
		.main-navigation .nav-menu > .current_page_ancestor:hover > a,
		.main-navigation ul ul .current-menu-parent > a,
		.main-navigation ul ul .current-menu-ancestor > a,
		.main-navigation ul ul .current_page_parent > a,
		.main-navigation ul ul .current_page_ancestor > a,
		.main-navigation li.menu-item-has-children:hover ul .current-menu-item > a:hover,
		.main-navigation li.menu-item-has-children:hover ul .current-menu-parent > a:hover,
		.main-navigation li.menu-item-has-children:hover ul .current-menu-ancestor > a:hover,
		.main-navigation li.menu-item-has-children:hover ul .current_page_item > a:hover,
		.main-navigation li.menu-item-has-children:hover ul .current_page_parent > a:hover,
		.main-navigation li.menu-item-has-children:hover ul .current_page_ancestor > a:hover {
			color: #fff;
		}
	}
	@media screen and (min-width: 601px) {
		.content-area,
		.widecolumn {
			box-shadow: 1px 0 0 <?php echo $colors['content_border']; ?>;
		}
		.widget-area {
			box-shadow: inset 1px 0 0 <?php echo $colors['content_border']; ?>; }
		.widget-area-left {
			box-shadow: inset -1px 0 0 <?php echo $colors['content_border']; ?>; }
		.rtl .content-area,
		.rtl .widecolumn {
			box-shadow: -1px 0 0 <?php echo $colors['content_border']; ?>;
		}

		.rtl .widget-area,
		.rtl .widget-area-left {
			box-shadow: inset -1px 0 0 <?php echo $colors['content_border']; ?>; }
		.rtl .widget-area-right {
			box-shadow: inset 1px 0 0 <?php echo $colors['content_border']; ?>; }
	}
	@media screen and (max-width: 1199px) {
		.social-sidebar {
			box-shadow: inset 0 1px 0 <?php echo $colors['content_border']; ?>; }
	}

	</style>
	<?php
}
endif;

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Move crownstar header sponsors selector.
 */
if ( ! function_exists( 'crownstar_header_sponsors' ) ):
function crownstar_header_sponsors() {
	return '.site-branding hgroup';
}
add_filter( 'crownstar_header_sponsors_selector', 'crownstar_header_sponsors' );
endif;

/**
 * Display footer elements
 */
if ( ! function_exists( 'crownstar_footer' ) ):
function crownstar_footer() {
	crownstar_footer_copyright();
}
endif;

/**
 * Display footer copyright notice
 */
if ( ! function_exists( 'crownstar_footer_copyright' ) ):
function crownstar_footer_copyright() {
	?>
	<div class="site-copyright">
		<?php echo apply_filters( 'crownstar_footer_copyright', sprintf( _x( '&copy; %1$s %2$s', 'copyright info', 'crownstar' ), date( 'Y' ), get_bloginfo( 'name' ) ) ); ?>
	</div><!-- .site-copyright -->
	<?php
}
endif;

/**
 * Helper functions
 */

/**
 * Sanitizes a hex color. Identical to core's sanitize_hex_color(), which is not available on the wp_head hook.
 *
 * Returns either '', a 3 or 6 digit hex color (with #), or null.
 * For sanitizing values without a #, see sanitize_hex_color_no_hash().
 */
if ( ! function_exists( 'crownstar_sanitize_hex_color' ) ) {
    function crownstar_sanitize_hex_color( $color ) {
        if ( '' === $color )
            return '';

        // 3 or 6 hex digits, or the empty string.
        if ( preg_match( '|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) )
            return $color;

        return null;
    }
}

/**
 * Sanitizes a checkbox option. Defaults to 'no'.
 */
if ( ! function_exists( 'crownstar_sanitize_checkbox' ) ) {
    function crownstar_sanitize_checkbox( $value ) {
    	return true == $value;
    }
}

/**
 * Sanitizes a radio option. Defaults to setting default from customize API.
 */
if ( ! function_exists( 'crownstar_sanitize_choices' ) ) {
    function crownstar_sanitize_choices( $value, $setting ) {
    	global $wp_customize;

    	$control = $wp_customize->get_control( $setting->id );

    	return $value;

    	if ( array_key_exists( $value, $control->choices ) ) {
	        return $value;
	    } else {
        	return $setting->default;
    	}
    }
}

/**
 * Sanitizes content width option. Defaults to 1000.
 */
if ( ! function_exists( 'crownstar_sanitize_content_width' ) ) {
    function crownstar_sanitize_content_width( $value ) {
    	$value = absint( $value );
    	if ( 500 > $value ) {
    		$value = 1000;
    	}
    	return round( $value, -1 );
    }
}

/**
 * Sanitizes a header image style option. Defaults to first element in options array.
 */
if ( ! function_exists( 'crownstar_sanitize_header_image_style' ) ) {
    function crownstar_sanitize_header_image_style( $value ) {
		$style_options = apply_filters( 'crownstar_header_image_style_options', array(
	        'background' => __( 'Background', 'crownstar' ),
	        'image' => __( 'Image', 'crownstar' ),
	    ) );

		// Return given value if it's a valid option
		if ( array_key_exists( $value, $style_options ) ) {
			return $value;
		}

		// Otherwise, return the first valid option
		reset( $style_options );
		$value = key( $style_options );
		return $value;
    }
}


if ( ! function_exists( 'crownstar_get_sidebar_setting' ) ) {
    function crownstar_get_sidebar_setting() {
		// Get theme options
		$options = (array) get_option( 'crownstar', array() );
		$options = array_map( 'esc_attr', $options );

		// Apply default setting
		if ( empty( $options['sidebar'] ) ) {
		    $options['sidebar'] = is_rtl() ? 'left' : 'right';
		}

		return $options['sidebar'];
	}
}

if ( ! function_exists( 'crownstar_rgb_from_hex' ) ) {
	function crownstar_rgb_from_hex( $color ) {
		$color = str_replace( '#', '', $color );
		// Convert shorthand colors to full format, e.g. "FFF" -> "FFFFFF"
		$color = preg_replace( '~^(.)(.)(.)$~', '$1$1$2$2$3$3', $color );

		$rgb['r'] = hexdec( $color{0}.$color{1} );
		$rgb['g'] = hexdec( $color{2}.$color{3} );
		$rgb['b'] = hexdec( $color{4}.$color{5} );
		return $rgb;
	}
}

if ( ! function_exists( 'crownstar_hex_darker' ) ) {
	function crownstar_hex_darker( $color, $factor = 30, $absolute = false ) {
		$base = crownstar_rgb_from_hex( $color );
		$color = '#';

		foreach ($base as $k => $v) :
	    	if ( $absolute ) {
	    		$amount = $factor;
	    	} else {
		        $amount = $v / 100;
		        $amount = round($amount * $factor);
		    }
	        $new_decimal = max( $v - $amount, 0 );

	        $new_hex_component = dechex($new_decimal);
	        if(strlen($new_hex_component) < 2) :
	        	$new_hex_component = "0" . $new_hex_component;
	        endif;
	        $color .= $new_hex_component;
		endforeach;

		return $color;
	}
}

if ( ! function_exists( 'crownstar_hex_lighter' ) ) {
	function crownstar_hex_lighter( $color, $factor = 30, $absolute = false ) {
		$base = crownstar_rgb_from_hex( $color );
		$color = '#';

	    foreach ($base as $k => $v) :
	    	if ( $absolute ) {
	    		$amount = $factor;
	    	} else {
		        $amount = 255 - $v;
		        $amount = $amount / 100;
		        $amount = round($amount * $factor);
		    }
	        $new_decimal = min( $v + $amount, 255 );

	        $new_hex_component = dechex($new_decimal);
	        if(strlen($new_hex_component) < 2) :
	        	$new_hex_component = "0" . $new_hex_component;
	        endif;
	        $color .= $new_hex_component;
	   	endforeach;

	   	return $color;
	}
}

if ( ! function_exists( 'crownstar_hex_mix' ) ) {
	function crownstar_hex_mix( $x, $y ) {
		$rgbx = crownstar_rgb_from_hex( $x );
		$rgby = crownstar_rgb_from_hex( $y );
		$r = str_pad( dechex( ( $rgbx['r'] + $rgby['r'] ) / 2 ), 2, '0', STR_PAD_LEFT );
		$g = str_pad( dechex( ( $rgbx['g'] + $rgby['g'] ) / 2 ), 2, '0', STR_PAD_LEFT );
		$b = str_pad( dechex( ( $rgbx['b'] + $rgby['b'] ) / 2 ), 2, '0', STR_PAD_LEFT );
		return '#' . $r . $g . $b;
	}
}

/**
 * Detect the brightness of a hex color
 * Adapted from http://www.webmasterworld.com/forum88/9769.htm
 */
if ( ! function_exists( 'crownstar_hex_brightness' ) ) {
	function crownstar_hex_brightness( $color = 'ffffff' ) {
		$color = str_replace( '#', '', $color );
		$rgb = crownstar_rgb_from_hex( $color );

		return ( ( $rgb['r'] * 0.299 ) + ( $rgb['g'] * 0.587 ) + ( $rgb['b'] * 0.114 ) );
	}
}
