<?php
/**
 * Corralón Pro theme bootstrap.
 *
 * @package Corralon_Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CORRALON_PRO_VERSION', '1.6.1' );
define( 'CORRALON_PRO_DIR', get_template_directory() );
define( 'CORRALON_PRO_URI', get_template_directory_uri() );

require_once CORRALON_PRO_DIR . '/inc/setup.php';
require_once CORRALON_PRO_DIR . '/inc/customizer.php';
require_once CORRALON_PRO_DIR . '/inc/enqueue.php';
require_once CORRALON_PRO_DIR . '/inc/template-functions.php';
require_once CORRALON_PRO_DIR . '/inc/woocommerce.php';

/**
 * AJAX: filtrar productos por categoría + paginación sin recargar.
 */
function corralon_pro_ajax_filter_products() {
	check_ajax_referer( 'corralon_pro_filter', 'nonce' );

	$cat_slug = isset( $_POST['cat'] ) ? sanitize_title( wp_unslash( $_POST['cat'] ) ) : '';
	$paged    = isset( $_POST['paged'] ) ? max( 1, (int) $_POST['paged'] ) : 1;
	$per_page = 20;

	$args = array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => $per_page,
		'paged'          => $paged,
	);

	if ( $cat_slug ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'product_cat',
				'field'    => 'slug',
				'terms'    => $cat_slug,
			),
		);
	}

	$query = new WP_Query( $args );

	ob_start();

	if ( $query->have_posts() ) {
		echo '<div class="product-grid">';
		while ( $query->have_posts() ) {
			$query->the_post();
			wc_get_template_part( 'content', 'product' );
		}
		echo '</div>';

		$total_pages = (int) $query->max_num_pages;
		if ( $total_pages > 1 ) {
			$base = $cat_slug
				? add_query_arg( array( 'product_cat' => $cat_slug, 'paged' => '%#%' ), home_url( '/' ) )
				: add_query_arg( 'paged', '%#%', home_url( '/' ) );

			$pagination = paginate_links( array(
				'base'      => $base,
				'format'    => '',
				'current'   => $paged,
				'total'     => $total_pages,
				'mid_size'  => 2,
				'end_size'  => 1,
				'prev_text' => '&larr; ' . esc_html__( 'Anterior', 'corralon-pro' ),
				'next_text' => esc_html__( 'Siguiente', 'corralon-pro' ) . ' &rarr;',
				'type'      => 'list',
			) );

			echo '<nav class="shop-pagination" aria-label="' . esc_attr__( 'Paginación de productos', 'corralon-pro' ) . '">' . $pagination . '</nav>';
		}

		wp_reset_postdata();
	} else {
		echo '<div class="no-products-found">';
		echo '<strong>' . esc_html__( 'No hay productos en esta categoría.', 'corralon-pro' ) . '</strong>';
		echo '<p>' . esc_html__( 'Probá con otra categoría o volvé a ver todo el catálogo.', 'corralon-pro' ) . '</p>';
		echo '<a class="button" href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Ver todos los productos', 'corralon-pro' ) . '</a>';
		echo '</div>';
	}

	wp_send_json_success( array(
		'html' => ob_get_clean(),
	) );
}
add_action( 'wp_ajax_corralon_pro_filter', 'corralon_pro_ajax_filter_products' );
add_action( 'wp_ajax_nopriv_corralon_pro_filter', 'corralon_pro_ajax_filter_products' );

/**
 * Encolar JS para filtrado AJAX.
 */
function corralon_pro_enqueue_filter_script() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	wp_enqueue_script(
		'corralon-pro-filter',
		get_template_directory_uri() . '/assets/js/filter.js',
		array( 'jquery' ),
		CORRALON_PRO_VERSION,
		true
	);

	wp_localize_script(
		'corralon-pro-filter',
		'corralonProFilter',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'corralon_pro_filter' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'corralon_pro_enqueue_filter_script' );