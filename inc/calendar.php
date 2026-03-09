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
	wp_register_script(
		'ical-js',
		'https://cdn.jsdelivr.net/npm/ical.js@2/build/ical.min.js',
		array(),
		'2',
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
 * @param string $view FullCalendar initialView value.
 * @return string HTML output.
 */
function scout_starter_render_calendar( $view ) {
	if ( ! get_option( 'scout_scoutbook_calendar' ) ) {
		return '';
	}

	wp_enqueue_script( 'scout-calendar' );

	$feed_url = admin_url( 'admin-ajax.php?action=scout_ical_proxy' );

	return sprintf(
		'<div class="scout-calendar" data-view="%s" data-feed="%s"></div>',
		esc_attr( $view ),
		esc_url( $feed_url )
	);
}

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
 * [scout_agenda] — upcoming events in a scrollable list.
 */
add_shortcode( 'scout_agenda', function () {
	return scout_starter_render_calendar( 'listYear' );
} );
