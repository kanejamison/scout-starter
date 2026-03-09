<?php
/**
 * Calendar settings tab: ScoutBook and future calendar integrations.
 *
 * @package Scout_Starter
 */

/**
 * Handle Calendar tab form submission.
 */
function scout_starter_handle_settings_calendar() {
	check_admin_referer( 'scout_settings_calendar' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to do this.', 'scout-starter' ) );
	}

	update_option(
		'scout_scoutbook_calendar',
		esc_url_raw( wp_unslash( $_POST['scoutbook_calendar'] ?? '' ) )
	);

	wp_safe_redirect( admin_url( 'admin.php?page=scout-settings&tab=calendar&scout_saved=1' ) );
	exit;
}
add_action( 'admin_post_scout_settings_calendar', 'scout_starter_handle_settings_calendar' );

/**
 * Render the Calendar tab content.
 */
function scout_starter_render_settings_calendar() {
	$config = scout_starter_get_saved_config();
	?>
	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="scout_settings_calendar">
		<?php wp_nonce_field( 'scout_settings_calendar' ); ?>

		<h2><?php esc_html_e( 'ScoutBook Calendar', 'scout-starter' ); ?></h2>
		<table class="form-table" role="presentation">
			<tr>
				<th scope="row">
					<label for="scoutbook_calendar"><?php esc_html_e( 'Calendar URL', 'scout-starter' ); ?></label>
				</th>
				<td>
					<input type="url" id="scoutbook_calendar" name="scoutbook_calendar"
						value="<?php echo esc_attr( $config['scoutbook_calendar'] ); ?>"
						class="large-text" placeholder="https://...">
					<p class="description">
						<?php esc_html_e( 'Paste the public iCal feed URL from your ScoutBook unit calendar. Once saved, the calendar will appear automatically on your Events page. You can also embed it anywhere using the shortcodes [scout_calendar] (month view) or [scout_agenda] (upcoming events list).', 'scout-starter' ); ?>
					</p>
				</td>
			</tr>
		</table>

		<?php submit_button( __( 'Save Settings', 'scout-starter' ) ); ?>
	</form>
	<?php
}
