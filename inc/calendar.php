<?php
/**
 * Calendar integration: iCal proxy and FullCalendar shortcodes.
 *
 * Shortcodes:
 *   [scout_calendar] — month grid view (dayGridMonth)
 *   [scout_agenda]   — upcoming events list view (listYear)
 *
 * Both shortcodes render nothing if no Scoutbook calendar URL is saved
 * under Settings > Calendar, so it is safe to include them in page content.
 *
 * The iCal feed is fetched server-side via the proxy endpoint to avoid
 * browser CORS restrictions when loading a remote .ics file.
 *
 * @package Scout_Starter
 */

// ---------------------------------------------------------------------------
// iCal proxy
// ---------------------------------------------------------------------------

/**
 * Serve the configured iCal feed as a proxied response.
 *
 * Hooked to both wp_ajax_ and wp_ajax_nopriv_ so the calendar works for
 * all visitors, not just logged-in users.
 */
function scout_starter_ical_proxy() {
	$url = get_option( 'scout_scoutbook_calendar' );

	if ( ! $url ) {
		wp_die( '', '', array( 'response' => 404 ) );
	}

	$response = wp_remote_get( $url, array( 'timeout' => 15 ) );

	if ( is_wp_error( $response ) ) {
		wp_die( '', '', array( 'response' => 502 ) );
	}

	header( 'Content-Type: text/calendar; charset=utf-8' );
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- raw iCal data
	echo wp_remote_retrieve_body( $response );
	exit;
}
add_action( 'wp_ajax_nopriv_scout_ical_proxy', 'scout_starter_ical_proxy' );
add_action( 'wp_ajax_scout_ical_proxy', 'scout_starter_ical_proxy' );

// ---------------------------------------------------------------------------
// Asset registration
// ---------------------------------------------------------------------------

/**
 * Register FullCalendar scripts. Enqueued on demand when a shortcode renders.
 *
 * Load order: FullCalendar global bundle → iCalendar plugin (self-contained
 * global build, bundles ical.js) → our initialisation script.
 */
function scout_starter_register_calendar_assets() {
	// @fullcalendar/icalendar requires ical.js ^1.x (peer dependency).
	// ical.js v2 removed the ICAL global that the plugin expects.
	wp_register_script(
		'ical-js',
		'https://cdn.jsdelivr.net/npm/ical.js@1/build/ical.min.js',
		array(),
		'1',
		true
	);

	wp_register_script(
		'fullcalendar',
		'https://cdn.jsdelivr.net/npm/fullcalendar@6/index.global.min.js',
		array( 'ical-js' ),
		'6',
		true
	);

	wp_register_script(
		'fullcalendar-icalendar',
		'https://cdn.jsdelivr.net/npm/@fullcalendar/icalendar@6/index.global.min.js',
		array( 'fullcalendar' ),
		'6',
		true
	);

	wp_register_script(
		'scout-calendar',
		get_template_directory_uri() . '/assets/js/scout-calendar.js',
		array( 'fullcalendar-icalendar' ),
		SCOUT_STARTER_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'scout_starter_register_calendar_assets' );

// ---------------------------------------------------------------------------
// Shared render helper
// ---------------------------------------------------------------------------

/**
 * Render a FullCalendar container for the given view.
 *
 * Returns an empty string if no calendar URL is configured, so the
 * shortcodes are safe to ship inside default page content.
 *
 * @param string $view  FullCalendar initialView value.
 * @param int    $limit Max events to show (agenda view only, 1–100).
 * @return string HTML output.
 */
function scout_starter_render_calendar( $view, $limit = 12 ) {
	if ( ! get_option( 'scout_scoutbook_calendar' ) ) {
		return '';
	}

	wp_enqueue_script( 'scout-calendar' );

	$limit    = max( 1, min( 100, (int) $limit ) );
	$feed_url = admin_url( 'admin-ajax.php?action=scout_ical_proxy' );

	return sprintf(
		'<div class="scout-calendar" data-view="%s" data-feed="%s" data-limit="%d"></div>',
		esc_attr( $view ),
		esc_url( $feed_url ),
		$limit
	);
}

// ---------------------------------------------------------------------------
// Gutenberg block
// ---------------------------------------------------------------------------

/**
 * Register the Scout Calendar block (dynamic, server-side rendered).
 */
function scout_starter_register_calendar_block() {
	wp_register_script(
		'scout-calendar-block-editor',
		get_template_directory_uri() . '/blocks/scout-calendar/editor.js',
		array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n' ),
		SCOUT_STARTER_VERSION,
		true
	);

	register_block_type(
		get_template_directory() . '/blocks/scout-calendar',
		array(
			'render_callback' => function ( $attributes ) {
				$view  = ! empty( $attributes['view'] )  ? $attributes['view']  : 'dayGridMonth';
				$limit = ! empty( $attributes['limit'] ) ? $attributes['limit'] : 12;
				return scout_starter_render_calendar( $view, $limit );
			},
		)
	);
}
add_action( 'init', 'scout_starter_register_calendar_block' );

// ---------------------------------------------------------------------------
// Shortcodes
// ---------------------------------------------------------------------------

/**
 * [scout_calendar] — month grid view.
 */
add_shortcode( 'scout_calendar', function () {
	return scout_starter_render_calendar( 'dayGridMonth' );
} );

/**
 * [scout_agenda events="12"] — upcoming events in a scrollable list.
 *
 * @param array $atts  events: number of events to show (1–100, default 12).
 */
add_shortcode( 'scout_agenda', function ( $atts ) {
	$atts = shortcode_atts( array( 'events' => 12 ), $atts, 'scout_agenda' );
	return scout_starter_render_calendar( 'listYear', $atts['events'] );
} );
