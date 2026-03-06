<?php
/**
 * Template part: no content found.
 *
 * @package Scout_Starter
 */
?>

<section class="no-results not-found">
	<header class="page-header">
		<h1 class="page-title"><?php esc_html_e( 'Nothing Found', 'scout-starter' ); ?></h1>
	</header>

	<div class="page-content">
		<?php if ( is_search() ) : ?>
			<p><?php esc_html_e( 'No results matched your search. Try different keywords.', 'scout-starter' ); ?></p>
		<?php else : ?>
			<p><?php esc_html_e( 'No content has been published yet.', 'scout-starter' ); ?></p>
		<?php endif; ?>

		<?php get_search_form(); ?>
	</div>
</section>
