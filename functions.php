<?php
/**
 * Scout Starter theme functions and definitions.
 *
 * @package Scout_Starter
 */

if ( ! defined( 'SCOUT_STARTER_VERSION' ) ) {
	define( 'SCOUT_STARTER_VERSION', '1.0.0' );
}

/**
 * Theme setup.
 */
function scout_starter_setup() {
	// Automatic feed links.
	add_theme_support( 'automatic-feed-links' );

	// Let WP manage the document title.
	add_theme_support( 'title-tag' );

	// Post thumbnails.
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 1200, 675, true );
	add_image_size( 'scout-hero', 1920, 800, true );
	add_image_size( 'scout-card', 600, 338, true );

	// Navigation menus.
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'scout-starter' ),
		'footer'  => __( 'Footer Menu', 'scout-starter' ),
	) );

	// HTML5 markup.
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'style',
		'script',
	) );

	// Custom logo.
	add_theme_support( 'custom-logo', array(
		'height'      => 100,
		'width'       => 100,
		'flex-height' => true,
		'flex-width'  => true,
	) );

	// Custom background.
	add_theme_support( 'custom-background', array(
		'default-color' => 'ffffff',
	) );

	// Block editor support.
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );

	// Content width.
	global $content_width;
	if ( ! isset( $content_width ) ) {
		$content_width = 1140;
	}
}
add_action( 'after_setup_theme', 'scout_starter_setup' );

/**
 * Enqueue styles and scripts.
 */
function scout_starter_scripts() {
	// Google Fonts (Roboto Slab for headings).
	wp_enqueue_style(
		'scout-starter-fonts',
		'https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@400;700&display=swap',
		array(),
		null
	);

	// Theme stylesheet.
	wp_enqueue_style(
		'scout-starter-style',
		get_stylesheet_uri(),
		array( 'scout-starter-fonts' ),
		SCOUT_STARTER_VERSION
	);

	// Navigation toggle.
	wp_enqueue_script(
		'scout-starter-navigation',
		get_template_directory_uri() . '/assets/js/navigation.js',
		array(),
		SCOUT_STARTER_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'scout_starter_scripts' );

/**
 * Register widget areas.
 */
function scout_starter_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Footer 1', 'scout-starter' ),
		'id'            => 'footer-1',
		'description'   => __( 'First footer widget area.', 'scout-starter' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => __( 'Footer 2', 'scout-starter' ),
		'id'            => 'footer-2',
		'description'   => __( 'Second footer widget area.', 'scout-starter' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => __( 'Footer 3', 'scout-starter' ),
		'id'            => 'footer-3',
		'description'   => __( 'Third footer widget area.', 'scout-starter' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => __( 'Sidebar', 'scout-starter' ),
		'id'            => 'sidebar-1',
		'description'   => __( 'Optional sidebar widget area.', 'scout-starter' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
}
add_action( 'widgets_init', 'scout_starter_widgets_init' );

/**
 * Add body classes.
 */
function scout_starter_body_classes( $classes ) {
	$unit_type = get_theme_mod( 'scout_unit_type', 'pack' );
	$classes[]  = 'scout-type-' . sanitize_html_class( $unit_type );

	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	return $classes;
}
add_filter( 'body_class', 'scout_starter_body_classes' );

/**
 * Customizer settings.
 */
function scout_starter_customize_register( $wp_customize ) {

	// ── Scout Unit Panel ──────────────────────────────────────

	$wp_customize->add_section( 'scout_unit_settings', array(
		'title'    => __( 'Scout Unit Settings', 'scout-starter' ),
		'priority' => 20,
	) );

	// Unit type (pack vs troop).
	$wp_customize->add_setting( 'scout_unit_type', array(
		'default'           => 'pack',
		'sanitize_callback' => 'scout_starter_sanitize_unit_type',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( 'scout_unit_type', array(
		'label'   => __( 'Unit Type', 'scout-starter' ),
		'section' => 'scout_unit_settings',
		'type'    => 'select',
		'choices' => array(
			'pack'  => __( 'Cub Scout Pack', 'scout-starter' ),
			'troop' => __( 'Scout Troop', 'scout-starter' ),
		),
	) );

	// Unit number.
	$wp_customize->add_setting( 'scout_unit_number', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( 'scout_unit_number', array(
		'label'       => __( 'Unit Number', 'scout-starter' ),
		'description' => __( 'e.g. 4084', 'scout-starter' ),
		'section'     => 'scout_unit_settings',
		'type'        => 'text',
	) );

	// Location.
	$wp_customize->add_setting( 'scout_location', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( 'scout_location', array(
		'label'       => __( 'Location', 'scout-starter' ),
		'description' => __( 'e.g. Anacortes, WA', 'scout-starter' ),
		'section'     => 'scout_unit_settings',
		'type'        => 'text',
	) );

	// Age range description.
	$wp_customize->add_setting( 'scout_age_range', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( 'scout_age_range', array(
		'label'       => __( 'Age/Grade Range', 'scout-starter' ),
		'description' => __( 'e.g. grades K-5 or ages 11-17', 'scout-starter' ),
		'section'     => 'scout_unit_settings',
		'type'        => 'text',
	) );

	// ── Hero Section ──────────────────────────────────────────

	$wp_customize->add_section( 'scout_hero_settings', array(
		'title'    => __( 'Homepage Hero', 'scout-starter' ),
		'priority' => 25,
	) );

	// Hero image.
	$wp_customize->add_setting( 'scout_hero_image', array(
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw',
	) );

	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'scout_hero_image', array(
		'label'   => __( 'Hero Background Image', 'scout-starter' ),
		'section' => 'scout_hero_settings',
	) ) );

	// Hero tagline.
	$wp_customize->add_setting( 'scout_hero_tagline', array(
		'default'           => __( 'Adventure starts here.', 'scout-starter' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );

	$wp_customize->add_control( 'scout_hero_tagline', array(
		'label'   => __( 'Hero Tagline', 'scout-starter' ),
		'section' => 'scout_hero_settings',
		'type'    => 'text',
	) );

	// CTA button text.
	$wp_customize->add_setting( 'scout_cta_text', array(
		'default'           => __( 'Join Us', 'scout-starter' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );

	$wp_customize->add_control( 'scout_cta_text', array(
		'label'   => __( 'CTA Button Text', 'scout-starter' ),
		'section' => 'scout_hero_settings',
		'type'    => 'text',
	) );

	// CTA button URL.
	$wp_customize->add_setting( 'scout_cta_url', array(
		'default'           => '#',
		'sanitize_callback' => 'esc_url_raw',
	) );

	$wp_customize->add_control( 'scout_cta_url', array(
		'label'   => __( 'CTA Button Link', 'scout-starter' ),
		'section' => 'scout_hero_settings',
		'type'    => 'url',
	) );

	// ── Social Links ──────────────────────────────────────────

	$wp_customize->add_section( 'scout_social_settings', array(
		'title'    => __( 'Social Links', 'scout-starter' ),
		'priority' => 30,
	) );

	$social_networks = array( 'facebook', 'instagram', 'youtube' );

	foreach ( $social_networks as $network ) {
		$wp_customize->add_setting( "scout_social_{$network}", array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		) );

		$wp_customize->add_control( "scout_social_{$network}", array(
			'label'   => ucfirst( $network ) . ' URL',
			'section' => 'scout_social_settings',
			'type'    => 'url',
		) );
	}
}
add_action( 'customize_register', 'scout_starter_customize_register' );

/**
 * Sanitize unit type.
 */
function scout_starter_sanitize_unit_type( $input ) {
	$valid = array( 'pack', 'troop' );
	return in_array( $input, $valid, true ) ? $input : 'pack';
}

/**
 * Get the unit display name.
 */
function scout_starter_unit_name() {
	$type   = get_theme_mod( 'scout_unit_type', 'pack' );
	$number = get_theme_mod( 'scout_unit_number', '' );

	if ( 'troop' === $type ) {
		$label = __( 'Scout Troop', 'scout-starter' );
	} else {
		$label = __( 'Cub Scout Pack', 'scout-starter' );
	}

	if ( $number ) {
		$label .= ' ' . $number;
	}

	return $label;
}

/**
 * Get the unit type label.
 */
function scout_starter_unit_type_label() {
	$type = get_theme_mod( 'scout_unit_type', 'pack' );
	return ( 'troop' === $type )
		? __( 'Scout Troop', 'scout-starter' )
		: __( 'Cub Scout Pack', 'scout-starter' );
}

/**
 * Get unit subtitle (serves ... in ...).
 */
function scout_starter_unit_subtitle() {
	$age_range = get_theme_mod( 'scout_age_range', '' );
	$location  = get_theme_mod( 'scout_location', '' );

	$parts = array();
	if ( $age_range ) {
		$parts[] = sprintf( __( 'Serving %s', 'scout-starter' ), $age_range );
	}
	if ( $location ) {
		$parts[] = sprintf( __( 'in %s', 'scout-starter' ), $location );
	}

	return implode( ' ', $parts );
}

/**
 * Include template tags.
 */
require get_template_directory() . '/inc/template-tags.php';
