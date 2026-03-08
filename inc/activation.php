<?php
/**
 * Theme activation: create default pages, set front page, build nav menu.
 *
 * Starter content for each page lives in inc/page-content/*.html.
 *
 * @package Scout_Starter
 */

/**
 * On theme switch, flag that the onboarding wizard should run.
 *
 * The actual site-setup logic is deferred to scout_starter_run_activation(),
 * which is called after the user completes (or skips) the onboarding wizard.
 */
function scout_starter_activate() {
	update_option( 'scout_onboarding_pending', 1 );
}
add_action( 'after_switch_theme', 'scout_starter_activate' );

/**
 * Create default pages, set static front page, and build primary nav.
 *
 * Called by the onboarding wizard after the user submits the setup form
 * or clicks "Skip". Skips pages that already exist by slug, and skips
 * front-page / menu setup if already configured.
 *
 * @param array $config {
 *     Optional configuration values. All keys have built-in defaults.
 *
 *     @type string $unit_type      Unit programme type: Pack, Troop, Crew, Ship, or Post.
 *     @type string $unit_number    Unit number, e.g. "1234".
 *     @type string $location       City / region shown in footer, e.g. "Anytown, WA".
 *     @type string $meeting_place  Venue name, e.g. "Anytown Community Center".
 *     @type string $meeting_street Street address, e.g. "123 Main Street".
 *     @type string $meeting_city   City / state / ZIP, e.g. "Anytown, WA 00000".
 * }
 */
function scout_starter_run_activation( $config ) {
	// --- Resolve config with fallbacks -------------------------------------------
	$unit_type      = ! empty( $config['unit_type'] )      ? $config['unit_type']      : 'Pack';
	$unit_number    = ! empty( $config['unit_number'] )    ? $config['unit_number']    : '1234';
	$location       = ! empty( $config['location'] )       ? $config['location']       : 'Anytown, WA';
	$meeting_place  = ! empty( $config['meeting_place'] )  ? $config['meeting_place']  : 'Anytown Community Center';
	$meeting_street = ! empty( $config['meeting_street'] ) ? $config['meeting_street'] : '123 Main Street';
	$meeting_city   = ! empty( $config['meeting_city'] )   ? $config['meeting_city']   : 'Anytown, WA 00000';

	// --- Build derived strings ----------------------------------------------------
	$home_title   = sprintf( 'Welcome to %s %s!', $unit_type, $unit_number );
	$btn2_label   = sprintf( 'Join %s %s', $unit_type, $unit_number );
	$footer_brand = sprintf( '<strong>%s %s</strong><br>%s', $unit_type, $unit_number, $location );

	switch ( $unit_type ) {
		case 'Troop':
			$excerpt = sprintf(
				'Scouts BSA Troop %s serves youth in the %s area.',
				$unit_number,
				$location
			);
			break;
		case 'Crew':
			$excerpt = sprintf(
				'Venturing Crew %s serves young adults in the %s area.',
				$unit_number,
				$location
			);
			break;
		case 'Ship':
			$excerpt = sprintf(
				'Sea Scout Ship %s serves young adults in the %s area.',
				$unit_number,
				$location
			);
			break;
		case 'Post':
			$excerpt = sprintf(
				'Exploring Post %s serves youth in the %s area.',
				$unit_number,
				$location
			);
			break;
		case 'Pack':
		default:
			$excerpt = sprintf(
				'Cub Scout Pack %s serves youth in grades K–5 in the %s area.',
				$unit_number,
				$location
			);
			break;
	}

	// --- Create default pages -----------------------------------------------------
	$content_dir = __DIR__ . '/page-content';

	$default_pages = array(
		'home'             => $home_title,
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

	// --- Hero meta for home page --------------------------------------------------
	if ( ! empty( $page_ids['home'] ) ) {
		update_post_meta( $page_ids['home'], '_scout_hero_enabled', 1 );
		update_post_meta( $page_ids['home'], '_scout_hero_show_excerpt', 1 );

		wp_update_post( array(
			'ID'           => $page_ids['home'],
			'post_excerpt' => $excerpt,
		) );

		update_post_meta( $page_ids['home'], '_scout_hero_btn1_label', 'Upcoming Events' );
		update_post_meta( $page_ids['home'], '_scout_hero_btn1_url',   '/events' );
		update_post_meta( $page_ids['home'], '_scout_hero_btn1_bg',    '#ffffff' );
		update_post_meta( $page_ids['home'], '_scout_hero_btn1_text',  'var(--color-primary)' );

		update_post_meta( $page_ids['home'], '_scout_hero_btn2_label', $btn2_label );
		update_post_meta( $page_ids['home'], '_scout_hero_btn2_url',   '/join-us' );
		update_post_meta( $page_ids['home'], '_scout_hero_btn2_bg',    'var(--color-accent)' );
		update_post_meta( $page_ids['home'], '_scout_hero_btn2_text',  'var(--color-primary)' );
	}

	// --- Site defaults ------------------------------------------------------------
	update_option( 'default_comment_status', 'closed' );
	update_option( 'default_ping_status', 'closed' );
	update_option( 'default_pingback_flag', 0 );

	// --- Static front page --------------------------------------------------------
	if ( 'posts' === get_option( 'show_on_front' ) && ! empty( $page_ids['home'] ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $page_ids['home'] );
	}

	// --- Primary nav menu ---------------------------------------------------------
	$locations   = get_theme_mod( 'nav_menu_locations', array() );
	$nav_exclude = array( 'home', 'website-policies', 'privacy-policy' );

	if ( empty( $locations['primary'] ) ) {
		$menu_id = wp_create_nav_menu( 'Primary Menu' );

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

	// --- Import bundled BSA logo --------------------------------------------------
	$logo_attach_id = get_option( 'scout_starter_logo_attachment_id' );
	$logo_src       = '';

	if ( ! $logo_attach_id || ! wp_get_attachment_url( $logo_attach_id ) ) {
		$source     = get_template_directory() . '/assets/images/bsa-logo-wborder2x.png';
		$upload_dir = wp_upload_dir();
		$filename   = 'bsa-logo-wborder2x.png';
		$dest       = $upload_dir['path'] . '/' . $filename;

		if ( copy( $source, $dest ) ) {
			$filetype  = wp_check_filetype( $filename, null );
			$attach_id = wp_insert_attachment( array(
				'guid'           => $upload_dir['url'] . '/' . $filename,
				'post_mime_type' => $filetype['type'],
				'post_title'     => 'BSA Logo',
				'post_status'    => 'inherit',
			), $dest );

			require_once ABSPATH . 'wp-admin/includes/image.php';
			wp_update_attachment_metadata( $attach_id, wp_generate_attachment_metadata( $attach_id, $dest ) );
			update_option( 'scout_starter_logo_attachment_id', $attach_id );
			$logo_src = $upload_dir['url'] . '/' . $filename;
		}
	} else {
		$logo_src = wp_get_attachment_url( $logo_attach_id );
	}

	// --- Footer widgets -----------------------------------------------------------
	$sidebars = get_option( 'sidebars_widgets', array() );

	if ( empty( $sidebars['footer-1'] ) && empty( $sidebars['footer-2'] ) && empty( $sidebars['footer-3'] ) ) {

		$block_widgets = get_option( 'widget_block', array( '_multiwidget' => 1 ) );
		if ( ! is_array( $block_widgets ) ) {
			$block_widgets = array( '_multiwidget' => 1 );
		}

		$existing_ids = array_filter( array_keys( $block_widgets ), 'is_int' );
		$next_id      = $existing_ids ? max( $existing_ids ) + 1 : 2;

		$logo_img = $logo_src
			? '<img src="' . esc_url( $logo_src ) . '" alt="Unit Logo" style="width:96px;height:auto"/>'
			: '';

		// Widget 1 — Unit branding (footer-1).
		$block_widgets[ $next_id ] = array(
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
<p><strong><span style="text-decoration:underline;">Meeting Address:</span></strong><br><strong>' . esc_html( $meeting_place ) . '</strong><br>' . esc_html( $meeting_street ) . '<br>' . esc_html( $meeting_city ) . '</p>
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
