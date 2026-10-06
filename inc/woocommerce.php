<?php
/**
 * WooCommerce integration.
 *
 * @package Corralon_Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function corralon_pro_woocommerce_setup() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}
	remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
	remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
	remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
}
add_action( 'after_setup_theme', 'corralon_pro_woocommerce_setup', 11 );

function corralon_pro_shop_columns() {
	return 4;
}
add_filter( 'loop_shop_columns', 'corralon_pro_shop_columns' );

function corralon_pro_add_query_filters( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! ( is_shop() || is_product_taxonomy() ) ) {
		return;
	}

	$meta_query = (array) $query->get( 'meta_query' );
	$tax_query  = (array) $query->get( 'tax_query' );

	if ( isset( $_GET['min_price'] ) && is_numeric( wp_unslash( $_GET['min_price'] ) ) ) {
		$meta_query[] = array( 'key' => '_price', 'value' => (float) wp_unslash( $_GET['min_price'] ), 'compare' => '>=', 'type' => 'NUMERIC' );
	}
	if ( isset( $_GET['max_price'] ) && is_numeric( wp_unslash( $_GET['max_price'] ) ) ) {
		$meta_query[] = array( 'key' => '_price', 'value' => (float) wp_unslash( $_GET['max_price'] ), 'compare' => '<=', 'type' => 'NUMERIC' );
	}
	if ( ! empty( $_GET['stock_status'] ) && in_array( sanitize_key( wp_unslash( $_GET['stock_status'] ) ), array( 'instock', 'outofstock' ), true ) ) {
		$meta_query[] = array( 'key' => '_stock_status', 'value' => sanitize_key( wp_unslash( $_GET['stock_status'] ) ) );
	}
	if ( ! empty( $_GET['product_brand'] ) && taxonomy_exists( 'product_brand' ) ) {
		$tax_query[] = array( 'taxonomy' => 'product_brand', 'field' => 'slug', 'terms' => sanitize_title( wp_unslash( $_GET['product_brand'] ) ) );
	}
	if ( ! empty( $_GET['on_sale'] ) ) {
		$on_sale_ids = function_exists( 'wc_get_product_ids_on_sale' ) ? wc_get_product_ids_on_sale() : array();
		$query->set( 'post__in', $on_sale_ids ? $on_sale_ids : array( 0 ) );
	}

	$query->set( 'meta_query', $meta_query );
	$query->set( 'tax_query', $tax_query );
}
add_action( 'pre_get_posts', 'corralon_pro_add_query_filters' );

function corralon_pro_custom_sorting( $sort_args ) {
	if ( isset( $_GET['orderby'] ) && 'newest' === sanitize_key( wp_unslash( $_GET['orderby'] ) ) ) {
		$sort_args['orderby'] = 'date';
		$sort_args['order']   = 'DESC';
	}
	return $sort_args;
}
add_filter( 'woocommerce_get_catalog_ordering_args', 'corralon_pro_custom_sorting' );

function corralon_pro_checkout_fields( $fields ) {
	$fields['billing']['billing_dni_cuit'] = array(
		'type'        => 'text',
		'label'       => __( 'DNI / CUIT', 'corralon-pro' ),
		'required'    => true,
		'class'       => array( 'form-row-wide' ),
		'priority'    => 32,
		'maxlength'   => 20,
		'input_class' => array( 'input-text' ),
	);
	$fields['billing']['billing_delivery_method'] = array(
		'type'     => 'select',
		'label'    => __( 'Forma de entrega', 'corralon-pro' ),
		'required' => true,
		'class'    => array( 'form-row-wide' ),
		'priority' => 33,
		'options'  => array(
			''        => __( 'Seleccionar una opción', 'corralon-pro' ),
			'pickup'  => __( 'Retiro en local', 'corralon-pro' ),
			'delivery' => __( 'Envío a domicilio', 'corralon-pro' ),
		),
	);
	return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'corralon_pro_checkout_fields' );

function corralon_pro_save_checkout_fields( $order_id ) {
	if ( isset( $_POST['billing_dni_cuit'] ) ) {
		update_post_meta( $order_id, '_billing_dni_cuit', sanitize_text_field( wp_unslash( $_POST['billing_dni_cuit'] ) ) );
	}
	if ( isset( $_POST['billing_delivery_method'] ) ) {
		update_post_meta( $order_id, '_billing_delivery_method', sanitize_key( wp_unslash( $_POST['billing_delivery_method'] ) ) );
	}
}
add_action( 'woocommerce_checkout_update_order_meta', 'corralon_pro_save_checkout_fields' );

function corralon_pro_cart_count_fragment( $fragments ) {
	ob_start();
	?>
	<span class="cart-count"><?php echo esc_html( corralon_pro_cart_count() ); ?></span>
	<?php
	$fragments['.cart-count'] = ob_get_clean();
	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'corralon_pro_cart_count_fragment' );