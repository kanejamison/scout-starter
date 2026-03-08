<?php
/**
 * General settings tab: unit information and meeting details.
 *
 * @package Scout_Starter
 */

/**
 * Handle General tab form submission.
 */
function scout_starter_handle_settings_general() {
	check_admin_referer( 'scout_settings_general' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to do this.', 'scout-starter' ) );
	}

	update_option( 'scout_unit_type',      sanitize_text_field( wp_unslash( $_POST['unit_type']      ?? '' ) ) );
	update_option( 'scout_unit_number',    sanitize_text_field( wp_unslash( $_POST['unit_number']    ?? '' ) ) );
	update_option( 'scout_location',       sanitize_text_field( wp_unslash( $_POST['location']       ?? '' ) ) );
	update_option( 'scout_meeting_place',  sanitize_text_field( wp_unslash( $_POST['meeting_place']  ?? '' ) ) );
	update_option( 'scout_meeting_street', sanitize_text_field( wp_unslash( $_POST['meeting_street'] ?? '' ) ) );
	update_option( 'scout_meeting_city',   sanitize_text_field( wp_unslash( $_POST['meeting_city']   ?? '' ) ) );

	$config = scout_starter_get_saved_config();

	scout_starter_update_footer_widget_content( $config );

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

	wp_safe_redirect( admin_url( 'admin.php?page=scout-settings&tab=general&scout_saved=1' ) );
	exit;
}
add_action( 'admin_post_scout_settings_general', 'scout_starter_handle_settings_general' );

/**
 * Render the General tab content.
 */
function scout_starter_render_settings_general() {
	$config     = scout_starter_get_saved_config();
	$unit_types = array(
		'Pack'  => __( 'Pack (Cub Scouts)', 'scout-starter' ),
		'Troop' => __( 'Troop (Scouts BSA)', 'scout-starter' ),
		'Crew'  => __( 'Crew (Venturing)', 'scout-starter' ),
		'Ship'  => __( 'Ship (Sea Scouts)', 'scout-starter' ),
		'Post'  => __( 'Post (Exploring)', 'scout-starter' ),
	);
	?>
	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="scout_settings_general">
		<?php wp_nonce_field( 'scout_settings_general' ); ?>

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

		<?php submit_button( __( 'Save Settings', 'scout-starter' ) ); ?>
	</form>
	<?php
}
