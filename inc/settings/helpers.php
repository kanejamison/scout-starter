<?php
/**
 * Shared helpers for the Scout Starter settings tabs.
 *
 * @package Scout_Starter
 */

/**
 * Return saved config from wp_options, with empty-string defaults.
 *
 * @return array
 */
function scout_starter_get_saved_config() {
	return array(
		'unit_type'          => get_option( 'scout_unit_type', 'Pack' ),
		'unit_number'        => get_option( 'scout_unit_number', '' ),
		'location'           => get_option( 'scout_location', '' ),
		'meeting_place'      => get_option( 'scout_meeting_place', '' ),
		'meeting_street'     => get_option( 'scout_meeting_street', '' ),
		'meeting_city'       => get_option( 'scout_meeting_city', '' ),
		'scoutbook_calendar' => get_option( 'scout_scoutbook_calendar', '' ),
	);
}

/**
 * Build the home page excerpt string for a given config.
 *
 * Mirrors the logic in scout_starter_run_activation() so the two stay in sync.
 *
 * @param array $config
 * @return string
 */
function scout_starter_build_excerpt( $config ) {
	$unit_type   = $config['unit_type'];
	$unit_number = $config['unit_number'];
	$location    = $config['location'];

	switch ( $unit_type ) {
		case 'Troop':
			return sprintf( 'Scouts BSA Troop %s serves youth in the %s area.', $unit_number, $location );
		case 'Crew':
			return sprintf( 'Venturing Crew %s serves young adults in the %s area.', $unit_number, $location );
		case 'Ship':
			return sprintf( 'Sea Scout Ship %s serves young adults in the %s area.', $unit_number, $location );
		case 'Post':
			return sprintf( 'Exploring Post %s serves youth in the %s area.', $unit_number, $location );
		default:
			return sprintf( 'Cub Scout Pack %s serves youth in grades K–5 in the %s area.', $unit_number, $location );
	}
}

/**
 * Update the block content of the footer widgets created during activation,
 * using the IDs stored in scout_footer_widget_ids.
 *
 * Widget 1 (unit branding) and widget 3 (meeting address) are regenerated.
 * Widget 2 (footer links) contains no user-specific data and is left alone.
 *
 * @param array $config Resolved config array (unit_type, unit_number, etc.).
 */
function scout_starter_update_footer_widget_content( $config ) {
	$ids = get_option( 'scout_footer_widget_ids' );
	if ( empty( $ids ) || count( $ids ) < 3 ) {
		return;
	}

	list( $id1, $id2, $id3 ) = $ids;

	$footer_brand = sprintf(
		'<strong>%s %s</strong><br>%s',
		$config['unit_type'],
		$config['unit_number'],
		$config['location']
	);

	$logo_attach_id = get_option( 'scout_starter_logo_attachment_id' );
	$logo_src       = $logo_attach_id ? wp_get_attachment_url( $logo_attach_id ) : '';
	$logo_img       = $logo_src
		? '<img src="' . esc_url( $logo_src ) . '" alt="Unit Logo" style="width:96px;height:auto"/>'
		: '';

	$block_widgets = get_option( 'widget_block', array() );
	if ( ! is_array( $block_widgets ) ) {
		$block_widgets = array();
	}

	$block_widgets[ $id1 ] = array(
		'content' => '<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column {"width":"33.33%"} -->
<div class="wp-block-column" style="flex-basis:33.33%"><!-- wp:image {"sizeSlug":"full"} -->
<figure class="wp-block-image size-full">' . $logo_img . '</figure>
<!-- /wp:image --></div>
<!-- /wp:column --><!-- wp:column {"width":"66.66%"} -->
<div class="wp-block-column" style="flex-basis:66.66%"><!-- wp:paragraph -->
<p>' . $footer_brand . '</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->',
	);

	$block_widgets[ $id3 ] = array(
		'content' => '<!-- wp:paragraph -->
<p><strong><span style="text-decoration:underline;">Meeting Address:</span></strong><br><strong>' . esc_html( $config['meeting_place'] ) . '</strong><br>' . esc_html( $config['meeting_street'] ) . '<br>' . esc_html( $config['meeting_city'] ) . '</p>
<!-- /wp:paragraph -->',
	);

	update_option( 'widget_block', $block_widgets );
}
