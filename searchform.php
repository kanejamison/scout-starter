<?php
/**
 * Search form template.
 *
 * @package Scout_Starter
 */
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label>
		<span class="screen-reader-text"><?php esc_html_e( 'Search for:', 'scout-starter' ); ?></span>
		<input type="search" class="search-field" placeholder="<?php esc_attr_e( 'Search&hellip;', 'scout-starter' ); ?>" value="<?php echo get_search_query(); ?>" name="s">
	</label>
	<button type="submit" class="search-submit btn"><?php esc_html_e( 'Search', 'scout-starter' ); ?></button>
</form>
