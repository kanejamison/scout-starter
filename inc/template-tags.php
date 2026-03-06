<?php
/**
 * Custom template tags for Scout Starter.
 *
 * @package Scout_Starter
 */

if ( ! function_exists( 'scout_starter_posted_on' ) ) :
	/**
	 * Print posted date.
	 */
	function scout_starter_posted_on() {
		$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';

		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
		}

		printf(
			$time_string,
			esc_attr( get_the_date( DATE_W3C ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( DATE_W3C ) ),
			esc_html( get_the_modified_date() )
		);
	}
endif;

if ( ! function_exists( 'scout_starter_posted_by' ) ) :
	/**
	 * Print posted by author.
	 */
	function scout_starter_posted_by() {
		printf(
			'<span class="byline">%s</span>',
			esc_html( get_the_author() )
		);
	}
endif;

if ( ! function_exists( 'scout_starter_entry_footer' ) ) :
	/**
	 * Print entry footer with categories and tags.
	 */
	function scout_starter_entry_footer() {
		if ( 'post' === get_post_type() ) {
			$categories_list = get_the_category_list( ', ' );
			if ( $categories_list ) {
				printf( '<span class="cat-links">%s</span> ', $categories_list );
			}

			$tags_list = get_the_tag_list( '', ', ' );
			if ( $tags_list ) {
				printf( '<span class="tags-links">%s</span>', $tags_list );
			}
		}
	}
endif;

if ( ! function_exists( 'scout_starter_social_links' ) ) :
	/**
	 * Output social link icons.
	 */
	function scout_starter_social_links() {
		$networks = array(
			'facebook'  => 'Facebook',
			'instagram' => 'Instagram',
			'youtube'   => 'YouTube',
		);

		$has_links = false;
		foreach ( $networks as $key => $label ) {
			if ( get_theme_mod( "scout_social_{$key}", '' ) ) {
				$has_links = true;
				break;
			}
		}

		if ( ! $has_links ) {
			return;
		}

		echo '<ul class="social-links">';
		foreach ( $networks as $key => $label ) {
			$url = get_theme_mod( "scout_social_{$key}", '' );
			if ( $url ) {
				printf(
					'<li><a href="%s" target="_blank" rel="noopener noreferrer" aria-label="%s">%s</a></li>',
					esc_url( $url ),
					esc_attr( $label ),
					esc_html( substr( $label, 0, 2 ) )
				);
			}
		}
		echo '</ul>';
	}
endif;
