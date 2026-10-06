<?php
/**
 * Cart template override.
 *
 * @package Corralon_Pro
 */

defined( 'ABSPATH' ) || exit;
do_action( 'woocommerce_before_cart' );
?>
<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
	<?php do_action( 'woocommerce_before_cart_table' ); ?>
	<table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
		<thead><tr><th><?php esc_html_e( 'Producto', 'corralon-pro' ); ?></th><th><?php esc_html_e( 'Precio', 'corralon-pro' ); ?></th><th><?php esc_html_e( 'Cantidad', 'corralon-pro' ); ?></th><th><?php esc_html_e( 'Subtotal', 'corralon-pro' ); ?></th><th><span class="screen-reader-text"><?php esc_html_e( 'Eliminar', 'corralon-pro' ); ?></span></th></tr></thead>
		<tbody>
			<?php do_action( 'woocommerce_before_cart_contents' ); ?>
			<?php foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) : ?>
				<?php
				$product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
				if ( ! $product || ! $product->exists() || $cart_item['quantity'] <= 0 ) {
					continue;
				}
				?>
				<tr class="woocommerce-cart-form__cart-item cart_item">
					<td class="product-name" data-title="<?php esc_attr_e( 'Producto', 'corralon-pro' ); ?>">
						<?php echo wp_kses_post( apply_filters( 'woocommerce_cart_item_remove_link', sprintf( '<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">×</a>', esc_url( wc_get_cart_remove_url( $cart_item_key ) ), esc_attr__( 'Eliminar este producto', 'corralon-pro' ), esc_attr( $product->get_id() ), esc_attr( $product->get_sku() ) ), $cart_item_key ) ); ?>
						<?php echo wp_kses_post( $product->get_image( 'woocommerce_thumbnail' ) ); ?>
						<a href="<?php echo esc_url( $product->get_permalink( $cart_item ) ); ?>"><?php echo esc_html( $product->get_name() ); ?></a>
					</td>
					<td class="product-price" data-title="<?php esc_attr_e( 'Precio', 'corralon-pro' ); ?>"><?php echo wp_kses_post( WC()->cart->get_product_price( $product ) ); ?></td>
					<td class="product-quantity" data-title="<?php esc_attr_e( 'Cantidad', 'corralon-pro' ); ?>"><?php echo woocommerce_quantity_input( array( 'input_name' => "cart[{$cart_item_key}][qty]", 'input_value' => $cart_item['quantity'], 'min_value' => 0, 'max_value' => $product->get_max_purchase_quantity() ), $product, false ); ?></td>
					<td class="product-subtotal" data-title="<?php esc_attr_e( 'Subtotal', 'corralon-pro' ); ?>"><?php echo wp_kses_post( WC()->cart->get_product_subtotal( $product, $cart_item['quantity'] ) ); ?></td>
					<td></td>
				</tr>
			<?php endforeach; ?>
			<?php do_action( 'woocommerce_cart_contents' ); ?>
			<tr><td colspan="5"><?php do_action( 'woocommerce_cart_actions' ); ?><button type="submit" class="button" name="update_cart" value="<?php esc_attr_e( 'Actualizar carrito', 'corralon-pro' ); ?>"><?php esc_html_e( 'Actualizar carrito', 'corralon-pro' ); ?></button><?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?></td></tr>
			<?php do_action( 'woocommerce_after_cart_contents' ); ?>
		</tbody>
	</table>
	<?php do_action( 'woocommerce_after_cart_table' ); ?>
</form>
<?php do_action( 'woocommerce_after_cart' ); ?>