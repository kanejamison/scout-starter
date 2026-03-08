<?php
/**
 * Settings page bootstrap.
 *
 * Registers the Scout Starter top-level menu item, loads per-tab subfiles,
 * and renders the shared tab shell around each tab's content.
 *
 * @package Scout_Starter
 */

require __DIR__ . '/settings/helpers.php';
require __DIR__ . '/settings/general.php';
require __DIR__ . '/settings/calendar.php';
require __DIR__ . '/settings/admin.php';

/**
 * Register Scout Starter as a top-level admin menu item (after Appearance).
 */
function scout_starter_register_settings_page() {
	add_menu_page(
		__( 'Scout Starter', 'scout-starter' ),
		__( 'Scout Starter', 'scout-starter' ),
		'manage_options',
		'scout-settings',
		'scout_starter_render_settings',
		'dashicons-groups',
		62
	);
}
add_action( 'admin_menu', 'scout_starter_register_settings_page' );

/**
 * Hide the onboarding wizard from the Appearance submenu.
 * The page remains accessible via its direct URL when needed.
 */
function scout_starter_hide_onboarding_menu_item() {
	remove_submenu_page( 'themes.php', 'scout-onboarding' );
}
add_action( 'admin_menu', 'scout_starter_hide_onboarding_menu_item', 999 );

/**
 * Show success notices on the settings page.
 */
function scout_starter_settings_notices() {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! isset( $_GET['page'] ) || 'scout-settings' !== sanitize_key( $_GET['page'] ) ) {
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
 * Render the settings page: tab nav + delegate to per-tab render function.
 */
function scout_starter_render_settings() {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$current_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'general';

	$tabs = array(
		'general'  => __( 'General', 'scout-starter' ),
		'calendar' => __( 'Calendar', 'scout-starter' ),
		'admin'    => __( 'Admin', 'scout-starter' ),
	);
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Scout Starter', 'scout-starter' ); ?></h1>

		<nav class="nav-tab-wrapper">
			<?php foreach ( $tabs as $slug => $label ) : ?>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=scout-settings&tab=' . $slug ) ); ?>"
					class="nav-tab<?php echo $current_tab === $slug ? ' nav-tab-active' : ''; ?>">
					<?php echo esc_html( $label ); ?>
				</a>
			<?php endforeach; ?>
		</nav>

		<?php
		switch ( $current_tab ) {
			case 'calendar':
				scout_starter_render_settings_calendar();
				break;
			case 'admin':
				scout_starter_render_settings_admin();
				break;
			default:
				scout_starter_render_settings_general();
				break;
		}
		?>
	</div>
	<?php
}
