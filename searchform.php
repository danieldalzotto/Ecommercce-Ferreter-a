<?php
/**
 * Search form template.
 *
 * @package Corralon_Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$search_id = 'search-' . wp_unique_id();
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label for="<?php echo esc_attr( $search_id ); ?>" class="screen-reader-text">
		<?php esc_html_e( 'Buscar:', 'corralon-pro' ); ?>
	</label>
	<input
		type="search"
		id="<?php echo esc_attr( $search_id ); ?>"
		class="search-field"
		placeholder="<?php esc_attr_e( 'Buscar productos, marcas y más…', 'corralon-pro' ); ?>"
		value="<?php echo esc_attr( get_search_query() ); ?>"
		name="s"
		autocomplete="off"
	>
	<input type="hidden" name="post_type" value="product">
	<button type="submit" class="search-submit" aria-label="<?php esc_attr_e( 'Buscar', 'corralon-pro' ); ?>">
		<svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
			<circle cx="11" cy="11" r="7"></circle>
			<line x1="21" y1="21" x2="16.65" y2="16.65"></line>
		</svg>
	</button>
</form>