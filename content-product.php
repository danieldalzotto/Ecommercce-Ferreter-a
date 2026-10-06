<?php
/**
 * Product card template.
 *
 * Card unificada para front-page, tienda, categoría y ofertas.
 *
 * @package Corralon_Pro
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product instanceof WC_Product ) {
	$product = wc_get_product( get_the_ID() );
}

if ( ! $product || ! $product->is_visible() ) {
	return;
}

$product_id    = $product->get_id();
$product_link  = get_permalink( $product_id );
$product_title = $product->get_name();
$is_on_sale    = $product->is_on_sale();
$is_in_stock   = $product->is_in_stock();
$is_featured   = $product->is_featured();
$image_id      = $product->get_image_id();
$image_url     = $image_id
	? wp_get_attachment_image_url( $image_id, 'woocommerce_thumbnail' )
	: wc_placeholder_img_src( 'woocommerce_thumbnail' );

// Categoría principal.
$terms    = get_the_terms( $product_id, 'product_cat' );
$cat_name = '';
if ( $terms && ! is_wp_error( $terms ) ) {
	$cat_name = $terms[0]->name;
}

// Fallback del link.
if ( ! $product_link ) {
	$product_link = '#';
}
?>
<li <?php wc_product_class( 'product-card', $product ); ?>>

	<div class="product-card-image">

		<div class="product-badge">
			<?php if ( $is_on_sale ) : ?>
				<span class="product-badge badge-sale"><?php esc_html_e( 'Oferta', 'corralon-pro' ); ?></span>
			<?php elseif ( $is_featured ) : ?>
				<span class="product-badge badge-featured"><?php esc_html_e( 'Destacado', 'corralon-pro' ); ?></span>
			<?php endif; ?>

			<?php if ( $is_in_stock ) : ?>
				<span class="product-badge badge-instock"><?php esc_html_e( 'En stock', 'corralon-pro' ); ?></span>
			<?php else : ?>
				<span class="product-badge badge-outofstock"><?php esc_html_e( 'Sin stock', 'corralon-pro' ); ?></span>
			<?php endif; ?>
		</div>

		<a href="<?php echo esc_url( $product_link ); ?>" class="product-image-link" aria-label="<?php echo esc_attr( $product_title ); ?>" tabindex="-1">
			<img src="<?php echo esc_url( $image_url ); ?>"
			     alt="<?php echo esc_attr( $product_title ); ?>"
			     loading="lazy">
		</a>

		<button
			type="button"
			class="wishlist-button"
			aria-label="<?php esc_attr_e( 'Agregar a favoritos', 'corralon-pro' ); ?>"
			data-wishlist-id="<?php echo esc_attr( $product_id ); ?>"
		>
			<svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
				<path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
			</svg>
		</button>
	</div>

	<div class="product-card-content">

		<?php if ( $cat_name ) : ?>
			<span class="product-cat-label"><?php echo esc_html( $cat_name ); ?></span>
		<?php endif; ?>

		<h3 class="product-title">
			<a href="<?php echo esc_url( $product_link ); ?>"><?php echo esc_html( $product_title ); ?></a>
		</h3>

		<div class="product-price">
			<?php echo wp_kses_post( $product->get_price_html() ); ?>
		</div>

		<?php if ( $product->get_sku() ) : ?>
			<div class="product-meta">
				<span><?php esc_html_e( 'SKU:', 'corralon-pro' ); ?> <strong><?php echo esc_html( $product->get_sku() ); ?></strong></span>
			</div>
		<?php endif; ?>

		<div class="product-actions">
			<?php woocommerce_template_loop_add_to_cart( array( 'class' => 'button btn-add' ) ); ?>
			<a href="<?php echo esc_url( $product_link ); ?>" class="button outline btn-view">
				<?php esc_html_e( 'Ver', 'corralon-pro' ); ?>
			</a>
		</div>
	</div>
</li>