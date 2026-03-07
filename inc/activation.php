<?php
/**
 * Theme activation: create default pages, set front page, build nav menu.
 *
 * Starter content for each page lives in inc/page-content/*.html.
 *
 * @package Scout_Starter
 */

/**
 * Create default pages, set static front page, and build primary nav on activation.
 *
 * Runs on after_switch_theme. Skips pages that already exist by slug,
 * and skips front-page/menu setup if already configured.
 */
function scout_starter_activate() {
	$content_dir = __DIR__ . '/page-content';

	$default_pages = array(
		'home'    => 'Home',
		'about'   => 'About',
		'events'  => 'Events',
		'contact' => 'Contact',
		'join-us' => 'Join Us',
	);

	$page_ids = array();

	foreach ( $default_pages as $slug => $title ) {
		$existing = get_posts( array(
			'post_type'              => 'page',
			'name'                   => $slug,
			'post_status'            => 'publish',
			'numberposts'            => 1,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
		) );

		if ( $existing ) {
			$page_ids[ $slug ] = $existing[0]->ID;
		} else {
			$content_file = $content_dir . '/' . $slug . '.html';
			$content      = file_exists( $content_file ) ? file_get_contents( $content_file ) : '';

			$page_ids[ $slug ] = wp_insert_post( array(
				'post_title'   => $title,
				'post_name'    => $slug,
				'post_content' => $content,
				'post_status'  => 'publish',
				'post_type'    => 'page',
			) );
		}
	}

	// Enable hero section on the home page (default on, show excerpt as subtitle).
	if ( ! empty( $page_ids['home'] ) ) {
		update_post_meta( $page_ids['home'], '_scout_hero_enabled', 1 );
		update_post_meta( $page_ids['home'], '_scout_hero_show_excerpt', 1 );
	}

	// Set static front page if not already configured.
	if ( 'posts' === get_option( 'show_on_front' ) && ! empty( $page_ids['home'] ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $page_ids['home'] );
	}

	// Create and assign primary nav menu if location is empty.
	$locations = get_theme_mod( 'nav_menu_locations', array() );

	if ( empty( $locations['primary'] ) ) {
		$menu_id = wp_create_nav_menu( 'Primary Menu' );

		if ( ! is_wp_error( $menu_id ) ) {
			foreach ( $page_ids as $slug => $page_id ) {
				wp_update_nav_menu_item( $menu_id, 0, array(
					'menu-item-object'    => 'page',
					'menu-item-object-id' => $page_id,
					'menu-item-type'      => 'post_type',
					'menu-item-status'    => 'publish',
				) );
			}

			$locations['primary'] = $menu_id;
			set_theme_mod( 'nav_menu_locations', $locations );
		}
	}
}
add_action( 'after_switch_theme', 'scout_starter_activate' );
