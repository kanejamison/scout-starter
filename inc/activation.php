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
		'home'             => 'Welcome to Pack 1234!',
		'about'            => 'About',
		'events'           => 'Events',
		'contact'          => 'Contact',
		'join-us'          => 'Join Us',
		'website-policies' => 'Website Policies',
		'privacy-policy'   => 'Privacy Policy',
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

	// Enable hero section on the home page and set default excerpt and buttons.
	if ( ! empty( $page_ids['home'] ) ) {
		update_post_meta( $page_ids['home'], '_scout_hero_enabled', 1 );
		update_post_meta( $page_ids['home'], '_scout_hero_show_excerpt', 1 );

		wp_update_post( array(
			'ID'           => $page_ids['home'],
			'post_excerpt' => 'Cub Scout Pack 1234 was chartered in 1999 and serves boys and girls in grades K-5 in the Anacortes, WA area.',
		) );

		update_post_meta( $page_ids['home'], '_scout_hero_btn1_label', 'Upcoming Events' );
		update_post_meta( $page_ids['home'], '_scout_hero_btn1_url',   '/events' );
		update_post_meta( $page_ids['home'], '_scout_hero_btn1_bg',    '#ffffff' );
		update_post_meta( $page_ids['home'], '_scout_hero_btn1_text',  'var(--color-primary)' );

		update_post_meta( $page_ids['home'], '_scout_hero_btn2_label', 'Join Pack 1234' );
		update_post_meta( $page_ids['home'], '_scout_hero_btn2_url',   '/join-us' );
		update_post_meta( $page_ids['home'], '_scout_hero_btn2_bg',    'var(--color-accent)' );
		update_post_meta( $page_ids['home'], '_scout_hero_btn2_text',  'var(--color-primary)' );
	}

	// Disable comments and pingbacks by default.
	update_option( 'default_comment_status', 'closed' );
	update_option( 'default_ping_status', 'closed' );
	update_option( 'default_pingback_flag', 0 );

	// Set static front page if not already configured.
	if ( 'posts' === get_option( 'show_on_front' ) && ! empty( $page_ids['home'] ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $page_ids['home'] );
	}

	// Create and assign primary nav menu if location is empty.
	$locations = get_theme_mod( 'nav_menu_locations', array() );

	if ( empty( $locations['primary'] ) ) {
		$menu_id = wp_create_nav_menu( 'Primary Menu' );

		// Slugs excluded from the primary nav menu.
		$nav_exclude = array( 'home', 'website-policies', 'privacy-policy' );

		if ( ! is_wp_error( $menu_id ) ) {
			foreach ( $page_ids as $slug => $page_id ) {
				if ( in_array( $slug, $nav_exclude, true ) ) {
					continue;
				}
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

	// Pre-populate footer widgets if sidebars are empty.
	$sidebars = get_option( 'sidebars_widgets', array() );

	if ( empty( $sidebars['footer-1'] ) && empty( $sidebars['footer-2'] ) && empty( $sidebars['footer-3'] ) ) {

		$block_widgets = get_option( 'widget_block', array( '_multiwidget' => 1 ) );
		if ( ! is_array( $block_widgets ) ) {
			$block_widgets = array( '_multiwidget' => 1 );
		}

		// Find next available IDs.
		$existing_ids = array_filter( array_keys( $block_widgets ), 'is_int' );
		$next_id      = $existing_ids ? max( $existing_ids ) + 1 : 2;

		// Widget 1 — Unit branding (footer-1).
		$block_widgets[ $next_id ] = array(
			'content' => '<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column {"width":"33.33%"} -->
<div class="wp-block-column" style="flex-basis:33.33%"><!-- wp:paragraph -->
<p>🏕️</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --><!-- wp:column {"width":"66.66%"} -->
<div class="wp-block-column" style="flex-basis:66.66%"><!-- wp:paragraph -->
<p><strong>Pack 1234</strong><br>Anytown, USA</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->',
		);
		$id1 = $next_id++;

		// Widget 2 — Footer links (footer-2).
		$block_widgets[ $next_id ] = array(
			'content' => '<!-- wp:list -->
<ul class="wp-block-list"><!-- wp:list-item -->
<li><a href="/website-policies/">Website Policies</a></li>
<!-- /wp:list-item --><!-- wp:list-item -->
<li><a href="/privacy-policy/">Privacy Policy</a></li>
<!-- /wp:list-item --><!-- wp:list-item -->
<li><a href="/contact/">Contact Us</a></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->',
		);
		$id2 = $next_id++;

		// Widget 3 — Meeting address (footer-3).
		$block_widgets[ $next_id ] = array(
			'content' => '<!-- wp:paragraph -->
<p><strong><span style="text-decoration:underline;">Pack Meeting Address:</span></strong><br><strong>Anytown Community Center</strong><br>123 Main Street<br>Anytown, USA 00000</p>
<!-- /wp:paragraph -->',
		);
		$id3 = $next_id;

		update_option( 'widget_block', $block_widgets );

		$sidebars['footer-1'] = array( 'block-' . $id1 );
		$sidebars['footer-2'] = array( 'block-' . $id2 );
		$sidebars['footer-3'] = array( 'block-' . $id3 );

		update_option( 'sidebars_widgets', $sidebars );
	}
}
add_action( 'after_switch_theme', 'scout_starter_activate' );
