<?php
/**
 * Page hero meta box.
 *
 * Registers per-page hero settings: enable/disable hero section,
 * and whether to show the page excerpt as the hero subtitle.
 *
 * @package Scout_Starter
 */

/**
 * Register post meta fields so they are available in the REST API / block editor.
 */
function scout_starter_register_meta() {
	$args = array(
		'type'          => 'boolean',
		'single'        => true,
		'show_in_rest'  => true,
		'auth_callback' => function() {
			return current_user_can( 'edit_posts' );
		},
	);
	register_post_meta( 'page', '_scout_hero_enabled', $args );
	register_post_meta( 'page', '_scout_hero_show_excerpt', $args );

	$string_args = array(
		'type'          => 'string',
		'single'        => true,
		'show_in_rest'  => true,
		'auth_callback' => function() {
			return current_user_can( 'edit_posts' );
		},
	);
	register_post_meta( 'page', '_scout_hero_btn1_label', $string_args );
	register_post_meta( 'page', '_scout_hero_btn1_url',   $string_args );
	register_post_meta( 'page', '_scout_hero_btn1_bg',    $string_args );
	register_post_meta( 'page', '_scout_hero_btn1_text',  $string_args );
	register_post_meta( 'page', '_scout_hero_btn2_label', $string_args );
	register_post_meta( 'page', '_scout_hero_btn2_url',   $string_args );
	register_post_meta( 'page', '_scout_hero_btn2_bg',    $string_args );
	register_post_meta( 'page', '_scout_hero_btn2_text',  $string_args );
}
add_action( 'init', 'scout_starter_register_meta' );

/**
 * Add the Hero Section meta box to the page editor sidebar.
 */
function scout_starter_hero_meta_box() {
	add_meta_box(
		'scout_hero_settings',
		__( 'Hero Section', 'scout-starter' ),
		'scout_starter_hero_meta_box_render',
		'page',
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes', 'scout_starter_hero_meta_box' );

/**
 * Render the Hero Section meta box fields.
 *
 * @param WP_Post $post Current post object.
 */
function scout_starter_hero_meta_box_render( $post ) {
	wp_nonce_field( 'scout_hero_meta', 'scout_hero_nonce' );

	$enabled      = (bool) get_post_meta( $post->ID, '_scout_hero_enabled', true );
	$show_excerpt = get_post_meta( $post->ID, '_scout_hero_show_excerpt', true );
	$show_excerpt = ( '' === $show_excerpt ) ? true : (bool) $show_excerpt;

	$btn1_label = get_post_meta( $post->ID, '_scout_hero_btn1_label', true );
	$btn1_url   = get_post_meta( $post->ID, '_scout_hero_btn1_url',   true );
	$btn1_bg    = get_post_meta( $post->ID, '_scout_hero_btn1_bg',    true ) ?: 'var(--color-accent)';
	$btn1_text  = get_post_meta( $post->ID, '_scout_hero_btn1_text',  true ) ?: '#ffffff';

	$btn2_label = get_post_meta( $post->ID, '_scout_hero_btn2_label', true );
	$btn2_url   = get_post_meta( $post->ID, '_scout_hero_btn2_url',   true );
	$btn2_bg    = get_post_meta( $post->ID, '_scout_hero_btn2_bg',    true ) ?: 'transparent';
	$btn2_text  = get_post_meta( $post->ID, '_scout_hero_btn2_text',  true ) ?: '#ffffff';

	$bg_options = array(
		'var(--color-primary)' => __( 'Primary', 'scout-starter' ),
		'var(--color-accent)'  => __( 'Accent', 'scout-starter' ),
		'#ffffff'              => __( 'White', 'scout-starter' ),
		'transparent'          => __( 'Transparent', 'scout-starter' ),
	);

	$text_options = array(
		'#ffffff'              => __( 'White', 'scout-starter' ),
		'var(--color-primary)' => __( 'Primary', 'scout-starter' ),
		'var(--color-accent)'  => __( 'Accent', 'scout-starter' ),
		'#222222'              => __( 'Dark', 'scout-starter' ),
	);
	?>
	<p>
		<label>
			<input type="checkbox" name="scout_hero_enabled" value="1" <?php checked( $enabled ); ?>>
			<?php esc_html_e( 'Enable hero section', 'scout-starter' ); ?>
		</label>
	</p>
	<p>
		<label>
			<input type="checkbox" name="scout_hero_show_excerpt" value="1" <?php checked( $show_excerpt ); ?>>
			<?php esc_html_e( 'Show excerpt as subtitle', 'scout-starter' ); ?>
		</label>
	</p>
	<p class="description"><?php esc_html_e( 'Set a Featured Image to use as the hero background.', 'scout-starter' ); ?></p>

	<hr>
	<p><strong><?php esc_html_e( 'Button 1', 'scout-starter' ); ?></strong></p>
	<p>
		<label><?php esc_html_e( 'Label', 'scout-starter' ); ?><br>
			<input type="text" name="scout_hero_btn1_label" value="<?php echo esc_attr( $btn1_label ); ?>" style="width:100%">
		</label>
	</p>
	<p>
		<label><?php esc_html_e( 'URL', 'scout-starter' ); ?><br>
			<input type="text" name="scout_hero_btn1_url" value="<?php echo esc_attr( $btn1_url ); ?>" style="width:100%">
		</label>
	</p>
	<p>
		<label><?php esc_html_e( 'Background', 'scout-starter' ); ?><br>
			<select name="scout_hero_btn1_bg" style="width:100%">
				<?php foreach ( $bg_options as $val => $label ) { ?>
					<option value="<?php echo esc_attr( $val ); ?>" <?php selected( $btn1_bg, $val ); ?>><?php echo esc_html( $label ); ?></option>
				<?php } ?>
			</select>
		</label>
	</p>
	<p>
		<label><?php esc_html_e( 'Text Color', 'scout-starter' ); ?><br>
			<select name="scout_hero_btn1_text" style="width:100%">
				<?php foreach ( $text_options as $val => $label ) { ?>
					<option value="<?php echo esc_attr( $val ); ?>" <?php selected( $btn1_text, $val ); ?>><?php echo esc_html( $label ); ?></option>
				<?php } ?>
			</select>
		</label>
	</p>

	<hr>
	<p><strong><?php esc_html_e( 'Button 2', 'scout-starter' ); ?></strong></p>
	<p>
		<label><?php esc_html_e( 'Label', 'scout-starter' ); ?><br>
			<input type="text" name="scout_hero_btn2_label" value="<?php echo esc_attr( $btn2_label ); ?>" style="width:100%">
		</label>
	</p>
	<p>
		<label><?php esc_html_e( 'URL', 'scout-starter' ); ?><br>
			<input type="text" name="scout_hero_btn2_url" value="<?php echo esc_attr( $btn2_url ); ?>" style="width:100%">
		</label>
	</p>
	<p>
		<label><?php esc_html_e( 'Background', 'scout-starter' ); ?><br>
			<select name="scout_hero_btn2_bg" style="width:100%">
				<?php foreach ( $bg_options as $val => $label ) { ?>
					<option value="<?php echo esc_attr( $val ); ?>" <?php selected( $btn2_bg, $val ); ?>><?php echo esc_html( $label ); ?></option>
				<?php } ?>
			</select>
		</label>
	</p>
	<p>
		<label><?php esc_html_e( 'Text Color', 'scout-starter' ); ?><br>
			<select name="scout_hero_btn2_text" style="width:100%">
				<?php foreach ( $text_options as $val => $label ) { ?>
					<option value="<?php echo esc_attr( $val ); ?>" <?php selected( $btn2_text, $val ); ?>><?php echo esc_html( $label ); ?></option>
				<?php } ?>
			</select>
		</label>
	</p>
	<?php
}

/**
 * Save hero meta fields on page save.
 *
 * @param int $post_id Post ID.
 */
function scout_starter_hero_meta_save( $post_id ) {
	if ( ! isset( $_POST['scout_hero_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['scout_hero_nonce'] ), 'scout_hero_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	update_post_meta( $post_id, '_scout_hero_enabled', isset( $_POST['scout_hero_enabled'] ) ? 1 : 0 );
	update_post_meta( $post_id, '_scout_hero_show_excerpt', isset( $_POST['scout_hero_show_excerpt'] ) ? 1 : 0 );

	$allowed_bg   = array( 'var(--color-primary)', 'var(--color-accent)', '#ffffff', 'transparent' );
	$allowed_text = array( '#ffffff', 'var(--color-primary)', 'var(--color-accent)', '#222222' );

	// phpcs:disable WordPress.Security.NonceVerification -- nonce verified above.
	update_post_meta( $post_id, '_scout_hero_btn1_label', sanitize_text_field( wp_unslash( $_POST['scout_hero_btn1_label'] ?? '' ) ) );
	update_post_meta( $post_id, '_scout_hero_btn1_url',   esc_url_raw( wp_unslash( $_POST['scout_hero_btn1_url'] ?? '' ) ) );
	update_post_meta( $post_id, '_scout_hero_btn1_bg',    in_array( $_POST['scout_hero_btn1_bg'] ?? '', $allowed_bg, true ) ? $_POST['scout_hero_btn1_bg'] : 'var(--color-accent)' );
	update_post_meta( $post_id, '_scout_hero_btn1_text',  in_array( $_POST['scout_hero_btn1_text'] ?? '', $allowed_text, true ) ? $_POST['scout_hero_btn1_text'] : '#ffffff' );
	update_post_meta( $post_id, '_scout_hero_btn2_label', sanitize_text_field( wp_unslash( $_POST['scout_hero_btn2_label'] ?? '' ) ) );
	update_post_meta( $post_id, '_scout_hero_btn2_url',   esc_url_raw( wp_unslash( $_POST['scout_hero_btn2_url'] ?? '' ) ) );
	update_post_meta( $post_id, '_scout_hero_btn2_bg',    in_array( $_POST['scout_hero_btn2_bg'] ?? '', $allowed_bg, true ) ? $_POST['scout_hero_btn2_bg'] : 'transparent' );
	update_post_meta( $post_id, '_scout_hero_btn2_text',  in_array( $_POST['scout_hero_btn2_text'] ?? '', $allowed_text, true ) ? $_POST['scout_hero_btn2_text'] : '#ffffff' );
	// phpcs:enable
}
add_action( 'save_post_page', 'scout_starter_hero_meta_save' );
