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

	$wp_customize->add_setting( 'scout_color_preset', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( 'scout_color_preset', array(
		'label'       => __( 'Color Preset', 'scout-starter' ),
		'description' => __( 'Choose a preset to populate the colors below. You can still adjust individually after.', 'scout-starter' ),
		'section'     => 'scout_colors',
		'type'        => 'select',
		'priority'    => 1,
		'choices'     => array(
			''                 => __( '— Select a preset —', 'scout-starter' ),
			'cub_pack'         => __( 'Cub Scout Pack (Blue + Gold)', 'scout-starter' ),
			'troop'            => __( 'Scouts BSA Troop (Blue + Red)', 'scout-starter' ),
			'scouting_america' => __( 'Scouting America (Red + White)', 'scout-starter' ),
			'venturing'        => __( 'Venturing (Green + Gold)', 'scout-starter' ),
			'sea_scouts'       => __( 'Sea Scouts (Blue + White)', 'scout-starter' ),
		),
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
			'default' => '#eae6e6',
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
 * Enqueue Customizer panel JS for the color preset selector.
 */
function scout_starter_customize_controls_scripts() {
	wp_enqueue_script(
		'scout-starter-customizer-controls',
		get_template_directory_uri() . '/assets/js/customizer-controls.js',
		array( 'customize-controls' ),
		SCOUT_STARTER_VERSION,
		true
	);
}
add_action( 'customize_controls_enqueue_scripts', 'scout_starter_customize_controls_scripts' );

/**
 * Output customizer color overrides as inline CSS custom properties.
 */
function scout_starter_customizer_css() {
	$primary   = get_theme_mod( 'scout_color_primary', '#003F87' );
	$accent    = get_theme_mod( 'scout_color_accent', '#FFCC00' );
	$nav_bg    = get_theme_mod( 'scout_color_nav_bg', '#003F87' );
	$hero_bg   = get_theme_mod( 'scout_color_hero_bg', '#003F87' );
	$footer_bg = get_theme_mod( 'scout_color_footer_bg', '#eae6e6' );

	$footer_hex = ltrim( $footer_bg, '#' );
	if ( 3 === strlen( $footer_hex ) ) {
		$footer_hex = $footer_hex[0] . $footer_hex[0] . $footer_hex[1] . $footer_hex[1] . $footer_hex[2] . $footer_hex[2];
	}
	$fr                 = hexdec( substr( $footer_hex, 0, 2 ) );
	$fg                 = hexdec( substr( $footer_hex, 2, 2 ) );
	$fb                 = hexdec( substr( $footer_hex, 4, 2 ) );
	$footer_brightness  = ( $fr * 299 + $fg * 587 + $fb * 114 ) / 1000;
	$footer_text        = $footer_brightness > 128 ? '#333333' : '#ffffff';
	$footer_text_subtle = $footer_brightness > 128 ? 'rgba(0,0,0,0.5)' : 'rgba(255,255,255,0.6)';
	$footer_border      = $footer_brightness > 128 ? 'rgba(0,0,0,0.1)' : 'rgba(255,255,255,0.15)';

	printf(
		'<style id="scout-starter-colors">:root{--color-primary:%s;--color-primary-dark:%s;--color-accent:%s;--color-accent-dark:%s;--color-accent-text:%s;--color-nav-bg:%s;--color-hero-bg:%s;--color-footer-bg:%s;--color-footer-text:%s;--color-footer-text-subtle:%s;--color-footer-border:%s;}</style>',
		esc_attr( $primary ),
		esc_attr( scout_starter_darken_color( $primary ) ),
		esc_attr( $accent ),
		esc_attr( scout_starter_darken_color( $accent ) ),
		esc_attr( scout_starter_contrast_color( $accent, $primary ) ),
		esc_attr( $nav_bg ),
		esc_attr( $hero_bg ),
		esc_attr( $footer_bg ),
		esc_attr( $footer_text ),
		esc_attr( $footer_text_subtle ),
		esc_attr( $footer_border )
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
 * Return a readable text color (#ffffff or the primary color) for a given background hex.
 *
 * Uses perceived brightness to decide: light backgrounds get the primary color,
 * dark backgrounds get white.
 *
 * @param string $hex     Background hex color.
 * @param string $primary Primary color hex to use for light backgrounds.
 * @return string
 */
function scout_starter_contrast_color( $hex, $primary ) {
	$hex = ltrim( $hex, '#' );
	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}
	$r          = hexdec( substr( $hex, 0, 2 ) );
	$g          = hexdec( substr( $hex, 2, 2 ) );
	$b          = hexdec( substr( $hex, 4, 2 ) );
	$brightness = ( $r * 299 + $g * 587 + $b * 114 ) / 1000;
	return $brightness > 128 ? $primary : '#ffffff';
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
