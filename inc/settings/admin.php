<?php
/**
 * Admin settings tab: re-apply setup and danger zone (reset).
 *
 * @package Scout_Starter
 */

/**
 * Handle "Re-apply Setup" form submission.
 *
 * Clears the footer sidebar assignments so scout_starter_run_activation()
 * will recreate them fresh, then redirects back to the Admin tab.
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

	wp_safe_redirect( admin_url( 'admin.php?page=scout-settings&tab=admin&scout_saved=redo' ) );
	exit;
}
add_action( 'admin_post_scout_redo_setup', 'scout_starter_handle_redo_setup' );

/**
 * Render the Admin tab content.
 */
function scout_starter_render_settings_admin() {
	?>
	<h2><?php esc_html_e( 'Re-apply Setup', 'scout-starter' ); ?></h2>
	<p><?php esc_html_e( 'Use this if your footer widgets are broken or missing, or if you updated your unit or meeting info on the General tab and want those changes reflected in the footer.', 'scout-starter' ); ?></p>
	<p><?php esc_html_e( 'This will overwrite the three footer widgets (unit branding, quick links, and meeting address) and update the home page hero tagline. Your pages, navigation menu, posts, and all other content will not be touched.', 'scout-starter' ); ?></p>
	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="scout_redo_setup">
		<?php wp_nonce_field( 'scout_redo_setup' ); ?>
		<?php submit_button( __( 'Re-apply Setup', 'scout-starter' ), 'secondary', 'submit', false ); ?>
	</form>

	<br>

	<?php scout_starter_render_reset_zone(); ?>
	<?php
}
