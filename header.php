<?php
/**
 * The header template.
 *
 * @package Scout_Starter
 */
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div class="site-container" id="page">
	<a class="screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'scout-starter' ); ?></a>

	<header class="site-header" role="banner">
		<div class="container">
			<div class="site-branding">
				<?php if ( has_custom_logo() ) : ?>
					<?php the_custom_logo(); ?>
				<?php endif; ?>

				<div>
					<p class="site-title">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
							<?php bloginfo( 'name' ); ?>
						</a>
					</p>
					<?php
					$description = get_bloginfo( 'description', 'display' );
					if ( $description ) :
						?>
						<p class="site-description"><?php echo esc_html( $description ); ?></p>
					<?php endif; ?>
				</div>
			</div>

			<nav class="main-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Primary Menu', 'scout-starter' ); ?>">
				<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
					<?php esc_html_e( 'Menu', 'scout-starter' ); ?>
				</button>
				<?php
				wp_nav_menu( array(
					'theme_location' => 'primary',
					'menu_id'        => 'primary-menu',
					'container'      => false,
					'fallback_cb'    => false,
					'depth'          => 2,
				) );
				?>
			</nav>
		</div>
	</header>

	<main id="content" class="site-content">
