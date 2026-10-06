<?php
/**
 * Enqueue assets.
 *
 * @package Corralon_Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function corralon_pro_enqueue_assets() {

	wp_enqueue_style(
		'corralon-pro-style',
		get_stylesheet_uri(),
		array(),
		CORRALON_PRO_VERSION
	);

	wp_enqueue_script(
		'corralon-pro-theme',
		CORRALON_PRO_URI . '/assets/js/theme.js',
		array(),
		CORRALON_PRO_VERSION,
		true
	);

	wp_localize_script(
		'corralon-pro-theme',
		'corralonPro',
		array(
			'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
			'searchNonce'  => wp_create_nonce( 'corralon_pro_search' ),
			'favoritesUrl' => corralon_pro_get_favorites_url(),
			'emptySearch'  => __( 'Escribí al menos 2 caracteres.', 'corralon-pro' ),
			'noResults'    => __( 'No encontramos productos.', 'corralon-pro' ),
			'viewAll'      => __( 'Ver todos los resultados', 'corralon-pro' ),
		)
	);

	if ( is_front_page() || is_home() ) {
		wp_enqueue_script(
			'corralon-pro-hero-slider',
			CORRALON_PRO_URI . '/assets/js/hero-slider.js',
			array( 'corralon-pro-theme' ),
			CORRALON_PRO_VERSION,
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'corralon_pro_enqueue_assets' );