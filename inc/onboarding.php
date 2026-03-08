<?php
/**
 * Onboarding wizard: redirect, admin page, form handling, and render.
 *
 * @package Scout_Starter
 */

/**
 * Register the hidden onboarding admin page under Appearance.
 */
function scout_starter_register_onboarding_page() {
	add_submenu_page(
		'themes.php',
		__( 'Scout Starter Setup', 'scout-starter' ),
		__( 'Scout Starter Setup', 'scout-starter' ),
		'manage_options',
		'scout-onboarding',
		'scout_starter_render_onboarding'
	);
}
add_action( 'admin_menu', 'scout_starter_register_onboarding_page' );

/**
 * Redirect to the onboarding wizard when the pending flag is set.
 */
function scout_starter_onboarding_redirect() {
	if ( ! get_option( 'scout_onboarding_pending' ) ) {
		return;
	}

	// Avoid redirect loops and don't redirect during AJAX.
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		return;
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$current_page = isset( $_GET['page'] ) ? sanitize_key( $_GET['page'] ) : '';
	if ( 'scout-onboarding' === $current_page ) {
		return;
	}

	delete_option( 'scout_onboarding_pending' );
	wp_safe_redirect( admin_url( 'themes.php?page=scout-onboarding' ) );
	exit;
}
add_action( 'admin_init', 'scout_starter_onboarding_redirect' );

/**
 * Enqueue onboarding CSS and JS only on the wizard page.
 *
 * @param string $hook_suffix Current admin page hook.
 */
function scout_starter_onboarding_assets( $hook_suffix ) {
	if ( 'appearance_page_scout-onboarding' !== $hook_suffix ) {
		return;
	}

	wp_enqueue_style(
		'scout-onboarding',
		get_template_directory_uri() . '/assets/css/onboarding.css',
		array(),
		SCOUT_STARTER_VERSION
	);

	wp_enqueue_script(
		'scout-onboarding',
		get_template_directory_uri() . '/assets/js/onboarding.js',
		array(),
		SCOUT_STARTER_VERSION,
		true
	);
}
add_action( 'admin_enqueue_scripts', 'scout_starter_onboarding_assets' );

/**
 * Handle onboarding form submission.
 */
function scout_starter_handle_onboarding() {
	check_admin_referer( 'scout_onboarding' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to do this.', 'scout-starter' ) );
	}

	$config = array(
		'unit_type'      => isset( $_POST['unit_type'] )      ? sanitize_text_field( wp_unslash( $_POST['unit_type'] ) )      : '',
		'unit_number'    => isset( $_POST['unit_number'] )    ? sanitize_text_field( wp_unslash( $_POST['unit_number'] ) )    : '',
		'location'       => isset( $_POST['location'] )       ? sanitize_text_field( wp_unslash( $_POST['location'] ) )       : '',
		'meeting_place'  => isset( $_POST['meeting_place'] )  ? sanitize_text_field( wp_unslash( $_POST['meeting_place'] ) )  : '',
		'meeting_street' => isset( $_POST['meeting_street'] ) ? sanitize_text_field( wp_unslash( $_POST['meeting_street'] ) ) : '',
		'meeting_city'   => isset( $_POST['meeting_city'] )   ? sanitize_text_field( wp_unslash( $_POST['meeting_city'] ) )   : '',
	);

	scout_starter_run_activation( $config );
	update_option( 'scout_onboarding_complete', 1 );

	wp_safe_redirect( admin_url( 'index.php?scout_setup=complete' ) );
	exit;
}
add_action( 'admin_post_scout_onboarding', 'scout_starter_handle_onboarding' );

/**
 * Handle "skip setup" — run activation with all defaults.
 */
function scout_starter_handle_onboarding_skip() {
	check_admin_referer( 'scout_onboarding_skip' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to do this.', 'scout-starter' ) );
	}

	scout_starter_run_activation( array() );
	update_option( 'scout_onboarding_complete', 1 );

	wp_safe_redirect( admin_url( 'index.php?scout_setup=complete' ) );
	exit;
}
add_action( 'admin_post_scout_onboarding_skip', 'scout_starter_handle_onboarding_skip' );

/**
 * Show a success notice on the Dashboard after setup completes.
 */
function scout_starter_onboarding_success_notice() {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! isset( $_GET['scout_setup'] ) || 'complete' !== $_GET['scout_setup'] ) {
		return;
	}
	?>
	<div class="notice notice-success is-dismissible">
		<p>
			<?php
			printf(
				/* translators: %s: link to the site front end */
				esc_html__( 'Your Scout Starter site is ready! %s', 'scout-starter' ),
				'<a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'View your site &rarr;', 'scout-starter' ) . '</a>'
			);
			?>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'scout_starter_onboarding_success_notice' );

/**
 * Render the onboarding wizard page.
 */
function scout_starter_render_onboarding() {
	$skip_url = wp_nonce_url(
		admin_url( 'admin-post.php?action=scout_onboarding_skip' ),
		'scout_onboarding_skip'
	);
	?>
	<div class="scout-onboarding">
		<div class="scout-onboarding__card">

			<!-- Header -->
			<div class="scout-onboarding__header">
				<h1><?php esc_html_e( 'Scout Starter Setup', 'scout-starter' ); ?></h1>
				<p><?php esc_html_e( "Let's get your unit's website ready. This takes about a minute.", 'scout-starter' ); ?></p>
			</div>

			<!-- Progress bar -->
			<div class="scout-onboarding__progress" role="tablist" aria-label="<?php esc_attr_e( 'Setup steps', 'scout-starter' ); ?>">
				<div class="scout-onboarding__step active" data-step="1"><?php esc_html_e( 'Unit', 'scout-starter' ); ?></div>
				<div class="scout-onboarding__step" data-step="2"><?php esc_html_e( 'Meeting', 'scout-starter' ); ?></div>
				<div class="scout-onboarding__step" data-step="3"><?php esc_html_e( 'Review', 'scout-starter' ); ?></div>
			</div>

			<!-- Form -->
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="scout_onboarding">
				<?php wp_nonce_field( 'scout_onboarding' ); ?>

				<!-- Pane 1: Your Unit -->
				<div class="scout-onboarding__pane active" data-pane="1">
					<h2><?php esc_html_e( 'Your Unit', 'scout-starter' ); ?></h2>

					<div class="scout-onboarding__field">
						<label for="scout-unit-type"><?php esc_html_e( 'Unit Type', 'scout-starter' ); ?></label>
						<select id="scout-unit-type" name="unit_type">
							<option value="Pack"><?php esc_html_e( 'Pack (Cub Scouts)', 'scout-starter' ); ?></option>
							<option value="Troop"><?php esc_html_e( 'Troop (Scouts BSA)', 'scout-starter' ); ?></option>
							<option value="Crew"><?php esc_html_e( 'Crew (Venturing)', 'scout-starter' ); ?></option>
							<option value="Ship"><?php esc_html_e( 'Ship (Sea Scouts)', 'scout-starter' ); ?></option>
							<option value="Post"><?php esc_html_e( 'Post (Exploring)', 'scout-starter' ); ?></option>
						</select>
					</div>

					<div class="scout-onboarding__field">
						<label for="scout-unit-number"><?php esc_html_e( 'Unit Number', 'scout-starter' ); ?></label>
						<input
							type="text"
							id="scout-unit-number"
							name="unit_number"
							placeholder="1234"
							required
						>
					</div>

					<div class="scout-onboarding__field">
						<label for="scout-location"><?php esc_html_e( 'City / Location', 'scout-starter' ); ?></label>
						<input
							type="text"
							id="scout-location"
							name="location"
							placeholder="Anytown, WA"
							required
						>
						<span class="scout-onboarding__hint"><?php esc_html_e( 'Shown in the site footer', 'scout-starter' ); ?></span>
					</div>
				</div>

				<!-- Pane 2: Meeting Details -->
				<div class="scout-onboarding__pane" data-pane="2">
					<h2><?php esc_html_e( 'Meeting Details', 'scout-starter' ); ?></h2>

					<div class="scout-onboarding__field">
						<label for="scout-meeting-place"><?php esc_html_e( 'Meeting Place Name', 'scout-starter' ); ?></label>
						<input
							type="text"
							id="scout-meeting-place"
							name="meeting_place"
							placeholder="Anytown Community Center"
						>
					</div>

					<div class="scout-onboarding__field">
						<label for="scout-meeting-street"><?php esc_html_e( 'Street Address', 'scout-starter' ); ?></label>
						<input
							type="text"
							id="scout-meeting-street"
							name="meeting_street"
							placeholder="123 Main Street"
						>
					</div>

					<div class="scout-onboarding__field">
						<label for="scout-meeting-city"><?php esc_html_e( 'City, State ZIP', 'scout-starter' ); ?></label>
						<input
							type="text"
							id="scout-meeting-city"
							name="meeting_city"
							placeholder="Anytown, WA 98221"
						>
					</div>
				</div>

				<!-- Pane 3: Review & Launch -->
				<div class="scout-onboarding__pane" data-pane="3">
					<h2><?php esc_html_e( "Here's what we'll set up", 'scout-starter' ); ?></h2>

					<ul class="scout-onboarding__review-list">
						<li><?php esc_html_e( 'Default pages: Home, About, Events, Contact, Join Us, Website Policies, Privacy Policy', 'scout-starter' ); ?></li>
						<li><?php esc_html_e( 'Primary navigation menu with your pages', 'scout-starter' ); ?></li>
						<li><?php esc_html_e( 'Footer widgets: unit branding, quick links, and meeting address', 'scout-starter' ); ?></li>
						<li><?php esc_html_e( 'Comments and pingbacks disabled by default', 'scout-starter' ); ?></li>
					</ul>

					<p class="scout-onboarding__review-note">
						<?php esc_html_e( 'Everything can be edited from the WordPress admin at any time.', 'scout-starter' ); ?>
					</p>
				</div>

				<!-- Action bar -->
				<div class="scout-onboarding__actions">
					<button
						type="button"
						class="scout-onboarding__btn scout-onboarding__btn--back"
						id="scout-btn-back"
						style="display:none"
					><?php esc_html_e( 'Back', 'scout-starter' ); ?></button>

					<button
						type="button"
						class="scout-onboarding__btn scout-onboarding__btn--next"
						id="scout-btn-next"
					><?php esc_html_e( 'Next', 'scout-starter' ); ?></button>

					<button
						type="submit"
						class="scout-onboarding__btn scout-onboarding__btn--submit"
						id="scout-btn-submit"
						style="display:none"
					><?php esc_html_e( 'Set Up My Site &rarr;', 'scout-starter' ); ?></button>
				</div>
			</form>

			<!-- Skip link -->
			<div class="scout-onboarding__skip">
				<a href="<?php echo esc_url( $skip_url ); ?>"><?php esc_html_e( 'Skip setup and use defaults', 'scout-starter' ); ?></a>
			</div>

		</div><!-- /.scout-onboarding__card -->
	</div><!-- /.scout-onboarding -->
	<?php
}
