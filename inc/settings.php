<?php
/**
 * Settings page for ongoing theme configuration.
 *
 * Provides Appearance > Scout Starter Settings for editing unit info,
 * meeting details, and integrations after the onboarding wizard has run.
 *
 * @package Scout_Starter
 */

/**
 * Register the Scout Starter Settings admin page under Appearance.
 */
function scout_starter_register_settings_page() {
	add_submenu_page(
		'themes.php',
		__( 'Scout Starter Settings', 'scout-starter' ),
		__( 'Scout Starter Settings', 'scout-starter' ),
		'manage_options',
		'scout-settings',
		'scout_starter_render_settings'
	);
}
add_action( 'admin_menu', 'scout_starter_register_settings_page' );

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

	$unit_type      = $config['unit_type'];
	$unit_number    = $config['unit_number'];
	$location       = $config['location'];
	$meeting_place  = $config['meeting_place'];
	$meeting_street = $config['meeting_street'];
	$meeting_city   = $config['meeting_city'];

	$footer_brand = sprintf( '<strong>%s %s</strong><br>%s', $unit_type, $unit_number, $location );

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
<p><strong><span style="text-decoration:underline;">Meeting Address:</span></strong><br><strong>' . esc_html( $meeting_place ) . '</strong><br>' . esc_html( $meeting_street ) . '<br>' . esc_html( $meeting_city ) . '</p>
<!-- /wp:paragraph -->',
	);

	update_option( 'widget_block', $block_widgets );
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
 * Handle settings form submission.
 */
function scout_starter_handle_settings() {
	check_admin_referer( 'scout_settings' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to do this.', 'scout-starter' ) );
	}

	$config = array(
		'unit_type'          => isset( $_POST['unit_type'] )          ? sanitize_text_field( wp_unslash( $_POST['unit_type'] ) )          : '',
		'unit_number'        => isset( $_POST['unit_number'] )        ? sanitize_text_field( wp_unslash( $_POST['unit_number'] ) )        : '',
		'location'           => isset( $_POST['location'] )           ? sanitize_text_field( wp_unslash( $_POST['location'] ) )           : '',
		'meeting_place'      => isset( $_POST['meeting_place'] )      ? sanitize_text_field( wp_unslash( $_POST['meeting_place'] ) )      : '',
		'meeting_street'     => isset( $_POST['meeting_street'] )     ? sanitize_text_field( wp_unslash( $_POST['meeting_street'] ) )     : '',
		'meeting_city'       => isset( $_POST['meeting_city'] )       ? sanitize_text_field( wp_unslash( $_POST['meeting_city'] ) )       : '',
		'scoutbook_calendar' => isset( $_POST['scoutbook_calendar'] ) ? esc_url_raw( wp_unslash( $_POST['scoutbook_calendar'] ) )        : '',
	);

	// Persist all config values.
	update_option( 'scout_unit_type',          $config['unit_type'] );
	update_option( 'scout_unit_number',        $config['unit_number'] );
	update_option( 'scout_location',           $config['location'] );
	update_option( 'scout_meeting_place',      $config['meeting_place'] );
	update_option( 'scout_meeting_street',     $config['meeting_street'] );
	update_option( 'scout_meeting_city',       $config['meeting_city'] );
	update_option( 'scout_scoutbook_calendar', $config['scoutbook_calendar'] );

	// Update footer widget content in-place.
	scout_starter_update_footer_widget_content( $config );

	// Update home page hero excerpt.
	$home_page = get_posts( array(
		'post_type'              => 'page',
		'name'                   => 'home',
		'post_status'            => 'publish',
		'numberposts'            => 1,
		'update_post_term_cache' => false,
		'update_post_meta_cache' => false,
	) );

	if ( $home_page ) {
		wp_update_post( array(
			'ID'           => $home_page[0]->ID,
			'post_excerpt' => scout_starter_build_excerpt( $config ),
		) );
	}

	wp_safe_redirect( admin_url( 'themes.php?page=scout-settings&scout_saved=1' ) );
	exit;
}
add_action( 'admin_post_scout_settings', 'scout_starter_handle_settings' );

/**
 * Handle "Re-apply Setup" — force-rebuild footer widgets using saved config.
 *
 * Clears the footer sidebar assignments so scout_starter_run_activation()
 * will recreate them fresh, then redirects back to the settings page.
 */
function scout_starter_handle_redo_setup() {
	check_admin_referer( 'scout_redo_setup' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to do this.', 'scout-starter' ) );
	}

	$config = scout_starter_get_saved_config();

	// Clear footer sidebar slots so run_activation() will re-populate them.
	$sidebars = get_option( 'sidebars_widgets', array() );
	unset( $sidebars['footer-1'], $sidebars['footer-2'], $sidebars['footer-3'] );
	update_option( 'sidebars_widgets', $sidebars );
	delete_option( 'scout_footer_widget_ids' );

	scout_starter_run_activation( $config );

	wp_safe_redirect( admin_url( 'themes.php?page=scout-settings&scout_saved=redo' ) );
	exit;
}
add_action( 'admin_post_scout_redo_setup', 'scout_starter_handle_redo_setup' );

/**
 * Show success notices on the settings page.
 */
function scout_starter_settings_notices() {
	$screen = get_current_screen();
	if ( ! $screen || 'appearance_page_scout-settings' !== $screen->id ) {
		return;
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$saved = isset( $_GET['scout_saved'] ) ? sanitize_key( $_GET['scout_saved'] ) : '';

	if ( '1' === $saved ) {
		echo '<div class="notice notice-success is-dismissible"><p>' .
			esc_html__( 'Settings saved.', 'scout-starter' ) .
			'</p></div>';
	} elseif ( 'redo' === $saved ) {
		echo '<div class="notice notice-success is-dismissible"><p>' .
			esc_html__( 'Setup re-applied. Footer widgets and home page have been updated.', 'scout-starter' ) .
			'</p></div>';
	}
}
add_action( 'admin_notices', 'scout_starter_settings_notices' );

/**
 * Render the settings page.
 */
function scout_starter_render_settings() {
	$config     = scout_starter_get_saved_config();
	$unit_types = array(
		'Pack'  => __( 'Pack (Cub Scouts)', 'scout-starter' ),
		'Troop' => __( 'Troop (Scouts BSA)', 'scout-starter' ),
		'Crew'  => __( 'Crew (Venturing)', 'scout-starter' ),
		'Ship'  => __( 'Ship (Sea Scouts)', 'scout-starter' ),
		'Post'  => __( 'Post (Exploring)', 'scout-starter' ),
	);
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Scout Starter Settings', 'scout-starter' ); ?></h1>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="scout_settings">
			<?php wp_nonce_field( 'scout_settings' ); ?>

			<h2><?php esc_html_e( 'Unit Information', 'scout-starter' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">
						<label for="unit_type"><?php esc_html_e( 'Unit Type', 'scout-starter' ); ?></label>
					</th>
					<td>
						<select id="unit_type" name="unit_type">
							<?php foreach ( $unit_types as $value => $label ) : ?>
								<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $config['unit_type'], $value ); ?>>
									<?php echo esc_html( $label ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="unit_number"><?php esc_html_e( 'Unit Number', 'scout-starter' ); ?></label>
					</th>
					<td>
						<input type="text" id="unit_number" name="unit_number"
							value="<?php echo esc_attr( $config['unit_number'] ); ?>"
							class="regular-text" placeholder="1234">
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="location"><?php esc_html_e( 'City / Location', 'scout-starter' ); ?></label>
					</th>
					<td>
						<input type="text" id="location" name="location"
							value="<?php echo esc_attr( $config['location'] ); ?>"
							class="regular-text" placeholder="Anytown, WA">
						<p class="description"><?php esc_html_e( 'Shown in the site footer.', 'scout-starter' ); ?></p>
					</td>
				</tr>
			</table>

			<h2><?php esc_html_e( 'Meeting Details', 'scout-starter' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">
						<label for="meeting_place"><?php esc_html_e( 'Meeting Place Name', 'scout-starter' ); ?></label>
					</th>
					<td>
						<input type="text" id="meeting_place" name="meeting_place"
							value="<?php echo esc_attr( $config['meeting_place'] ); ?>"
							class="regular-text" placeholder="Anytown Community Center">
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="meeting_street"><?php esc_html_e( 'Street Address', 'scout-starter' ); ?></label>
					</th>
					<td>
						<input type="text" id="meeting_street" name="meeting_street"
							value="<?php echo esc_attr( $config['meeting_street'] ); ?>"
							class="regular-text" placeholder="123 Main Street">
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="meeting_city"><?php esc_html_e( 'City, State ZIP', 'scout-starter' ); ?></label>
					</th>
					<td>
						<input type="text" id="meeting_city" name="meeting_city"
							value="<?php echo esc_attr( $config['meeting_city'] ); ?>"
							class="regular-text" placeholder="Anytown, WA 98221">
					</td>
				</tr>
			</table>

			<h2><?php esc_html_e( 'Integrations', 'scout-starter' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">
						<label for="scoutbook_calendar"><?php esc_html_e( 'ScoutBook Calendar URL', 'scout-starter' ); ?></label>
					</th>
					<td>
						<input type="url" id="scoutbook_calendar" name="scoutbook_calendar"
							value="<?php echo esc_attr( $config['scoutbook_calendar'] ); ?>"
							class="large-text" placeholder="https://...">
						<p class="description">
							<?php esc_html_e( 'Paste the public iCal feed URL from your ScoutBook unit calendar. Leave blank to skip. You can add this later — calendar display is coming in a future update.', 'scout-starter' ); ?>
						</p>
					</td>
				</tr>
			</table>

			<?php submit_button( __( 'Save Settings', 'scout-starter' ) ); ?>
		</form>

		<hr>

		<h2><?php esc_html_e( 'Re-apply Setup', 'scout-starter' ); ?></h2>
		<p>
			<?php esc_html_e( 'Rebuild footer widgets and update the home page tagline using your saved settings above. Your pages and navigation will not be changed.', 'scout-starter' ); ?>
		</p>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="scout_redo_setup">
			<?php wp_nonce_field( 'scout_redo_setup' ); ?>
			<?php submit_button( __( 'Re-apply Setup', 'scout-starter' ), 'secondary', 'submit', false ); ?>
		</form>
	</div>
	<?php
}
