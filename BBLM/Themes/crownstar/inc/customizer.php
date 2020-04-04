<?php
/**
 * Crownstar Theme Customizer
 *
 * @package Crownstar
 */

if ( ! function_exists( 'crownstar_customize_register' ) ) :
/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function crownstar_customize_register( $wp_customize ) {
    $wp_customize->get_setting( 'blogname' )->transport            = 'postMessage';
    $wp_customize->get_setting( 'blogdescription' )->transport     = 'postMessage';
    $wp_customize->get_setting( 'header_textcolor' )->transport    = 'postMessage';

    /**
     * Logo Image
     */
    $wp_customize->add_setting( 'crownstar[logo_url]', array(
        'sanitize_callback' => 'esc_url',
        'capability'    => 'edit_theme_options',
        'type'          => 'option',
    ));

    $wp_customize->add_control( new WP_Customize_Image_Control($wp_customize, 'crownstar_logo_url', array(
        'label'     => __('Logo', 'crownstar'),
        'section'   => 'title_tagline',
        'settings' => 'crownstar[logo_url]',
    )));

    /**
     * Navigation Menu Search
     */
    $wp_customize->add_setting( 'crownstar[nav_menu_search]', array(
        'default'       => 'yes',
        'sanitize_callback' => 'crownstar_sanitize_checkbox',
        'capability'    => 'edit_theme_options',
        'type'          => 'option',
    ) );
    $wp_customize->add_control( 'crownstar_nav_menu_search', array(
        'label'     => __('Display Search Form', 'crownstar'),
        'section'   => 'title_tagline',
        'settings'  => 'crownstar[nav_menu_search]',
        'type'      => 'checkbox',
        'std'       => 'yes'
    ) );

    /**
     * Content Text Color
     */
    $wp_customize->add_setting( 'crownstar[content]', array(
        'default'           => apply_filters( 'crownstar_default_content_color', '#222222' ),
        'sanitize_callback' => 'crownstar_sanitize_hex_color',
        'capability'        => 'edit_theme_options',
        'type'              => 'option',
    ) );

    $wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, 'crownstar_content', array(
        'label'    => __('Content Text Color', 'crownstar'),
        'section'  => 'colors',
        'settings' => 'crownstar[content]',
    ) ) );

    /**
     * Content Background Color
     */
    $wp_customize->add_setting( 'crownstar[content_background]', array(
        'default'           => apply_filters( 'crownstar_default_content_background_color', '#ffffff' ),
        'sanitize_callback' => 'crownstar_sanitize_hex_color',
        'capability'        => 'edit_theme_options',
        'type'              => 'option',
    ) );

    $wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, 'crownstar_content_background', array(
        'label'    => __('Content Background Color', 'crownstar'),
        'section'  => 'colors',
        'settings' => 'crownstar[content_background]',
    ) ) );

    /**
     * Customize colors
     */
    $wp_customize->add_setting( 'crownstar[customize]', array(
        'default'       => ( 'yes' == get_option( 'crownstar_enable_frontend_css', 'no' ) ),
        'sanitize_callback' => 'crownstar_sanitize_checkbox',
        'capability'    => 'edit_theme_options',
        'type'          => 'option',
        'transport'   => 'refresh',
    ) );

    $wp_customize->add_control( 'crownstar_customize', array(
        'label'     => __( 'Customize', 'crownstar' ),
        'section'   => 'colors',
        'settings'  => 'crownstar[customize]',
        'type'      => 'checkbox',
        'std'       => 'no'
    ) );

    /**
     * Primary Color
     */
    $wp_customize->add_setting( 'crownstar[primary]', array(
        'default'           => apply_filters( 'crownstar_default_primary_color', '#2b353e' ),
        'sanitize_callback' => 'crownstar_sanitize_hex_color',
        'capability'        => 'edit_theme_options',
        'type'              => 'option',
    ) );

    $wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, 'crownstar_primary', array(
        'label'    => __('Primary Color', 'crownstar'),
        'section'  => 'colors',
        'settings' => 'crownstar[primary]',
    ) ) );

    /**
     * Link Color
     */
    $wp_customize->add_setting( 'crownstar[link]', array(
        'default'           => apply_filters( 'crownstar_default_link_color', '#ba0000' ),
        'sanitize_callback' => 'crownstar_sanitize_hex_color',
        'capability'        => 'edit_theme_options',
        'type'              => 'option',
    ) );

    $wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, 'crownstar_link', array(
        'label'    => __('Link Color', 'crownstar'),
        'section'  => 'colors',
        'settings' => 'crownstar[link]',
    ) ) );

    /**
     * Text Color
     */
    $wp_customize->add_setting( 'crownstar[text]', array(
        'default'           => apply_filters( 'crownstar_default_text_color', '#222222' ),
        'sanitize_callback' => 'crownstar_sanitize_hex_color',
        'capability'        => 'edit_theme_options',
        'type'              => 'option',
    ) );

    $wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, 'crownstar_text', array(
        'label'    => __('Text Color', 'crownstar'),
        'section'  => 'colors',
        'settings' => 'crownstar[text]',
    ) ) );

    /**
     * Widget Background Color
     */
    $wp_customize->add_setting( 'crownstar[background]', array(
        'default'           => apply_filters( 'crownstar_default_background_color', '#f4f4f4' ),
        'sanitize_callback' => 'crownstar_sanitize_hex_color',
        'capability'        => 'edit_theme_options',
        'type'              => 'option',
    ) );

    $wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, 'crownstar_background', array(
        'label'    => __('Widget Background Color', 'crownstar'),
        'section'  => 'colors',
        'settings' => 'crownstar[background]',
    ) ) );

    /**
     * Widget Heading Color
     */
    $wp_customize->add_setting( 'crownstar[heading]', array(
        'default'           => apply_filters( 'crownstar_default_heading_color', '#ffffff' ),
        'sanitize_callback' => 'crownstar_sanitize_hex_color',
        'capability'        => 'edit_theme_options',
        'type'              => 'option',
    ) );

    $wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, 'crownstar_heading', array(
        'label'    => __('Widget Heading Color', 'crownstar'),
        'section'  => 'colors',
        'settings' => 'crownstar[heading]',
    ) ) );

    /*
     * Header Image Style
     */
    $options = apply_filters( 'crownstar_header_image_style_options', array(
        'background' => __( 'Background', 'crownstar' ),
        'image' => __( 'Image', 'crownstar' ),
    ) );

    if ( sizeof( $options ) > 1 ) {
        $wp_customize->add_setting( 'crownstar[header_image_style]', array(
            'default'           => 'background',
            'sanitize_callback' => 'crownstar_sanitize_header_image_style',
            'capability'        => 'edit_theme_options',
            'type'              => 'option',
        ) );

        $wp_customize->add_control( 'crownstar_header_image_style', array(
            'label'     => __( 'Style', 'crownstar' ),
            'section'   => 'header_image',
            'settings'  => 'crownstar[header_image_style]',
            'type'      => 'select',
            'choices'   => $options,
        ) );
    }

    /*
     * Posts Section
     */
    $wp_customize->add_section( 'crownstar_posts' , array(
        'title'      => __( 'Posts', 'crownstar' ),
    ) );

    /**
     * Display Post Date
     */
    $wp_customize->add_setting( 'crownstar[show_post_date]', array(
        'default'       => true,
        'sanitize_callback' => 'crownstar_sanitize_checkbox',
        'capability'    => 'edit_theme_options',
        'type'          => 'option',
    ) );

    $wp_customize->add_control( 'crownstar_show_post_date', array(
        'label'     => __('Display post date?', 'crownstar'),
        'section'   => 'crownstar_posts',
        'settings'  => 'crownstar[show_post_date]',
        'type'      => 'checkbox',
        'std'       => 'yes',
    ) );

    /**
     * Display Post Author
     */
    $wp_customize->add_setting( 'crownstar[show_post_author]', array(
        'default'       => false,
        'sanitize_callback' => 'crownstar_sanitize_checkbox',
        'capability'    => 'edit_theme_options',
        'type'          => 'option',
    ) );

    $wp_customize->add_control( 'crownstar_show_post_author', array(
        'label'     => __('Display post author?', 'crownstar'),
        'section'   => 'crownstar_posts',
        'settings'  => 'crownstar[show_post_author]',
        'type'      => 'checkbox',
        'std'       => 'no',
    ) );

    /*
     * Layout Section
     */
    $wp_customize->add_section( 'crownstar_layout' , array(
        'title'      => __( 'Layout', 'crownstar' ),
    ) );

    /**
     * Content width
     */
    $wp_customize->add_setting( 'crownstar[content_width]', array(
        'default'       => 1000,
        'sanitize_callback' => 'crownstar_sanitize_content_width',
        'capability'    => 'edit_theme_options',
        'type'          => 'option',
    ) );

    $wp_customize->add_control( 'crownstar_content_width', array(
        'label'       => __('Content Width', 'crownstar'),
        'description' => '<a class="button button-small" href="#minus">-</a> <span>px</span> <a class="button button-small" href="#plus">+</a>',
        'section'     => 'crownstar_layout',
        'settings'    => 'crownstar[content_width]',
        'type'        => 'range',
        'input_attrs' => array(
            'min'  => 1000,
            'max'  => 2000,
            'step' => 10,
        ),
    ) );

    $wp_customize->get_setting( 'crownstar[content_width]' )->transport = 'postMessage';

    /**
     * Sidebar
     */
    $wp_customize->add_setting( 'crownstar[sidebar]', array(
        'default'       => is_rtl() ? 'left' : 'right',
        'sanitize_callback' => 'crownstar_sanitize_choices',
        'capability'    => 'edit_theme_options',
        'type'          => 'option',
    ) );

    $wp_customize->add_control( 'crownstar_sidebar', array(
        'label'     => __('Sidebar', 'crownstar'),
        'section'   => 'crownstar_layout',
        'settings'  => 'crownstar[sidebar]',
        'type'      => 'radio',
        'choices'   => array(
            '' => __( 'Default', 'crownstar' ) . ' (' . ( is_rtl() ? __( 'Left', 'crownstar' ) : __( 'Right', 'crownstar' ) ) . ')',
            'left' => __( 'Left', 'crownstar' ),
            'right' => __( 'Right', 'crownstar' ),
            'double' => __( 'Both', 'crownstar' ),
            'no' => __( 'None', 'crownstar' ),
        ),
    ) );

    do_action( 'crownstar_customize_register', $wp_customize );
}
add_action( 'customize_register', 'crownstar_customize_register' );
endif;

if ( ! function_exists( 'crownstar_customize_preview_js' ) ) :
/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function crownstar_customize_preview_js() {
    wp_register_script( 'crownstar_customizer', get_template_directory_uri() . '/js/customizer.js', array( 'jquery', 'customize-preview' ), '1.4', true );

    $vars = apply_filters( 'crownstar_customizer_vars', array(
        'content_width_selector' => '.site-header, .site-content, .site-footer, .site-info',
        'content_width_adjustment' => 0,
    ) );
    wp_localize_script( 'crownstar_customizer', 'vars', $vars );

    wp_enqueue_script( 'crownstar_customizer' );
}
add_action( 'customize_preview_init', 'crownstar_customize_preview_js' );
endif;
