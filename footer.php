<?php
/**
 * The footer template.
 *
 * @package Scout_Starter
 */
?>

	</main><!-- #content -->

	<footer class="site-footer" role="contentinfo">
		<div class="container">
			<?php if ( is_active_sidebar( 'footer-1' ) || is_active_sidebar( 'footer-2' ) || is_active_sidebar( 'footer-3' ) ) : ?>
				<div class="footer-widgets">
					<?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
						<div class="footer-widget-area">
							<?php dynamic_sidebar( 'footer-1' ); ?>
						</div>
					<?php endif; ?>

					<?php if ( is_active_sidebar( 'footer-2' ) ) : ?>
						<div class="footer-widget-area">
							<?php dynamic_sidebar( 'footer-2' ); ?>
						</div>
					<?php endif; ?>

					<?php if ( is_active_sidebar( 'footer-3' ) ) : ?>
						<div class="footer-widget-area">
							<?php dynamic_sidebar( 'footer-3' ); ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<div class="site-info">
				<p>
					&copy; <?php echo esc_html( date( 'Y' ) ); ?>
					<?php bloginfo( 'name' ); ?>.
					<?php esc_html_e( 'All rights reserved.', 'scout-starter' ); ?>
				</p>
			</div>
		</div>
	</footer>

</div><!-- .site-container -->

<?php wp_footer(); ?>
</body>
</html>
