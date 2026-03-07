<?php
/**
 * Theme setup: supports, enqueues, widgets, favicon, font hints.
 *
 * @package Scout_Starter
 */

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

	add_post_type_support( 'page', 'excerpt' );

	global $content_width;
	if ( ! isset( $content_width ) ) {
		$content_width = 1140;
	}
}
add_action( 'after_setup_theme', 'scout_starter_setup' );

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
 * Add a CSS class to the Join Us nav menu item so it can be styled as a CTA.
 *
 * @param string[] $classes Array of CSS classes for the nav item.
 * @param WP_Post  $item    The nav menu item object.
 * @return string[]
 */
function scout_starter_join_nav_class( $classes, $item ) {
	if ( 'post_type' === $item->type && 'page' === $item->object ) {
		$page = get_post( $item->object_id );
		if ( $page && 'join-us' === $page->post_name ) {
			$classes[] = 'menu-item-join';
		}
	}
	return $classes;
}
add_filter( 'nav_menu_css_class', 'scout_starter_join_nav_class', 10, 2 );

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
