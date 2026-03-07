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
	// Default show_excerpt to true if the meta has never been saved.
	$show_excerpt = ( '' === $show_excerpt ) ? true : (bool) $show_excerpt;
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
}
add_action( 'save_post_page', 'scout_starter_hero_meta_save' );
