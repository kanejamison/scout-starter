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
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 1200, 675, true );
	add_image_size( 'scout-hero', 1920, 800, true );
	add_image_size( 'scout-card', 600, 338, true );

	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'scout-starter' ),
		'footer'  => __( 'Footer Menu', 'scout-starter' ),
	) );

	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'style',
		'script',
	) );

	add_theme_support( 'custom-logo', array(
		'height'      => 100,
		'width'       => 100,
		'flex-height' => true,
		'flex-width'  => true,
	) );

	add_theme_support( 'custom-background', array(
		'default-color' => 'ffffff',
	) );

	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );

	global $content_width;
	if ( ! isset( $content_width ) ) {
		$content_width = 1140;
	}
}
add_action( 'after_setup_theme', 'scout_starter_setup' );

/**
 * Output a default favicon when no site icon has been set via Customizer.
 */
function scout_starter_default_favicon() {
	if ( ! has_site_icon() ) {
		$url = get_template_directory_uri() . '/assets/images/default-favicon.png';
		printf( '<link rel="icon" href="%s" sizes="300x300" type="image/png">' . "\n", esc_url( $url ) );
		printf( '<link rel="apple-touch-icon" href="%s">' . "\n", esc_url( $url ) );
	}
}
add_action( 'wp_head', 'scout_starter_default_favicon' );

/**
 * Add preconnect resource hints for Google Fonts.
 */
function scout_starter_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = 'https://fonts.googleapis.com';
		$urls[] = array(
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => true,
		);
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'scout_starter_resource_hints', 10, 2 );

/**
 * Enqueue styles and scripts.
 */
function scout_starter_scripts() {
	wp_enqueue_style(
		'scout-starter-fonts',
		'https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@100..900&display=swap',
		array(),
		null
	);

	wp_enqueue_style(
		'scout-starter-style',
		get_stylesheet_uri(),
		array( 'scout-starter-fonts' ),
		SCOUT_STARTER_VERSION
	);

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
 * Customizer color settings.
 *
 * BSA official palette:
 *   Scouting Blue  #003F87  (Pantone 294)
 *   Scouting Red   #CE1126  (Pantone 186)
 *   Yellow         #FFCC00  (Pantone 116)
 *   Brown          #996633  (Pantone 463)
 *   Light Gray     #eae6e6
 */
function scout_starter_customize_register( $wp_customize ) {

	$wp_customize->add_section( 'scout_colors', array(
		'title'    => __( 'Scout Colors', 'scout-starter' ),
		'priority' => 20,
	) );

	$color_settings = array(
		'scout_color_primary'   => array(
			'label'   => __( 'Primary Color', 'scout-starter' ),
			'default' => '#003F87',
		),
		'scout_color_accent'    => array(
			'label'   => __( 'Accent Color', 'scout-starter' ),
			'default' => '#FFCC00',
		),
		'scout_color_nav_bg'    => array(
			'label'   => __( 'Navigation Background', 'scout-starter' ),
			'default' => '#003F87',
		),
		'scout_color_hero_bg'   => array(
			'label'   => __( 'Hero Background', 'scout-starter' ),
			'default' => '#003F87',
		),
		'scout_color_footer_bg' => array(
			'label'   => __( 'Footer Background', 'scout-starter' ),
			'default' => '#003F87',
		),
	);

	foreach ( $color_settings as $id => $args ) {
		$wp_customize->add_setting( $id, array(
			'default'           => $args['default'],
			'sanitize_callback' => 'sanitize_hex_color',
			'transport'         => 'refresh',
		) );

		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $id, array(
			'label'   => $args['label'],
			'section' => 'scout_colors',
		) ) );
	}
}
add_action( 'customize_register', 'scout_starter_customize_register' );

/**
 * Output customizer color overrides as inline CSS custom properties.
 */
function scout_starter_customizer_css() {
	$primary   = get_theme_mod( 'scout_color_primary', '#003F87' );
	$accent    = get_theme_mod( 'scout_color_accent', '#FFCC00' );
	$nav_bg    = get_theme_mod( 'scout_color_nav_bg', '#003F87' );
	$hero_bg   = get_theme_mod( 'scout_color_hero_bg', '#003F87' );
	$footer_bg = get_theme_mod( 'scout_color_footer_bg', '#003F87' );

	printf(
		'<style id="scout-starter-colors">:root{--color-primary:%s;--color-primary-dark:%s;--color-accent:%s;--color-accent-dark:%s;--color-nav-bg:%s;--color-hero-bg:%s;--color-footer-bg:%s;}</style>',
		esc_attr( $primary ),
		esc_attr( scout_starter_darken_color( $primary ) ),
		esc_attr( $accent ),
		esc_attr( scout_starter_darken_color( $accent ) ),
		esc_attr( $nav_bg ),
		esc_attr( $hero_bg ),
		esc_attr( $footer_bg )
	);
}
add_action( 'wp_head', 'scout_starter_customizer_css' );

/**
 * Darken a hex color by reducing each RGB channel by $amount.
 */
function scout_starter_darken_color( $hex, $amount = 30 ) {
	$hex = ltrim( $hex, '#' );
	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}
	$r = max( 0, hexdec( substr( $hex, 0, 2 ) ) - $amount );
	$g = max( 0, hexdec( substr( $hex, 2, 2 ) ) - $amount );
	$b = max( 0, hexdec( substr( $hex, 4, 2 ) ) - $amount );
	return sprintf( '#%02x%02x%02x', $r, $g, $b );
}

/**
 * Include template tags.
 */
require get_template_directory() . '/inc/template-tags.php';
