<?php
/**
 * 404 template.
 *
 * @package Scout_Starter
 */

get_header();
?>

<div class="section">
	<div class="container text-center">
		<h1><?php esc_html_e( 'Page Not Found', 'scout-starter' ); ?></h1>
		<p><?php esc_html_e( 'The page you are looking for does not exist. Try searching or return to the homepage.', 'scout-starter' ); ?></p>
		<p>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn">
				<?php esc_html_e( 'Go Home', 'scout-starter' ); ?>
			</a>
		</p>
	</div>
</div>

<?php
get_footer();
