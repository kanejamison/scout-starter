<?php
/**
 * Customizer settings and color output.
 *
 * BSA official palette:
 *   Scouting Blue  #003F87  (Pantone 294)
 *   Scouting Red   #CE1126  (Pantone 186)
 *   Yellow         #FFCC00  (Pantone 116)
 *   Brown          #996633  (Pantone 463)
 *   Light Gray     #eae6e6
 *
 * @package Scout_Starter
 */

/**
 * Register Customizer sections, settings, and controls.
 */
function scout_starter_customize_register( $wp_customize ) {

	// ── Latest News Section ───────────────────────────────────

	$wp_customize->add_section( 'scout_news_settings', array(
		'title'    => __( 'Latest News Section', 'scout-starter' ),
		'priority' => 25,
	) );

	$wp_customize->add_setting( 'scout_news_enabled', array(
		'default'           => false,
		'sanitize_callback' => 'scout_starter_sanitize_checkbox',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( 'scout_news_enabled', array(
		'label'   => __( 'Show Latest News section on homepage', 'scout-starter' ),
		'section' => 'scout_news_settings',
		'type'    => 'checkbox',
	) );

	$wp_customize->add_setting( 'scout_news_heading', array(
		'default'           => __( 'Latest News', 'scout-starter' ),
		'sanitize_callback' => 'sanitize_text_field',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( 'scout_news_heading', array(
		'label'   => __( 'Section Heading', 'scout-starter' ),
		'section' => 'scout_news_settings',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'scout_news_show_dates', array(
		'default'           => false,
		'sanitize_callback' => 'scout_starter_sanitize_checkbox',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( 'scout_news_show_dates', array(
		'label'   => __( 'Show post dates on cards', 'scout-starter' ),
		'section' => 'scout_news_settings',
		'type'    => 'checkbox',
	) );

	// ── Colors ────────────────────────────────────────────────

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
 * Sanitize a checkbox value to boolean.
 */
function scout_starter_sanitize_checkbox( $value ) {
	return (bool) $value;
}

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
