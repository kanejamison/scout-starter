<?php
/**
 * Scout Starter theme functions and definitions.
 *
 * @package Scout_Starter
 */

if ( ! defined( 'SCOUT_STARTER_VERSION' ) ) {
	define( 'SCOUT_STARTER_VERSION', '1.1.1' );
}

require get_template_directory() . '/inc/setup.php';
require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/activation.php';
require get_template_directory() . '/inc/meta.php';
require get_template_directory() . '/inc/template-tags.php';
