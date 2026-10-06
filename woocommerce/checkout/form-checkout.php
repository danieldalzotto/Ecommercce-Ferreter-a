<?php
/**
 * Checkout layout override.
 *
 * @package Corralon_Pro
 */

defined( 'ABSPATH' ) || exit;
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html__( 'Debés iniciar sesión para finalizar la compra.', 'corralon-pro' );
	return;
}
?>
<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">
	<div class="checkout-fields"><?php if ( $checkout->get_checkout_fields() ) : ?><?php do_action( 'woocommerce_checkout_before_customer_details' ); ?><div id="customer_details"><?php do_action( 'woocommerce_checkout_billing' ); ?><?php do_action( 'woocommerce_checkout_shipping' ); ?></div><?php do_action( 'woocommerce_checkout_after_customer_details' ); ?><?php endif; ?></div>
	<div id="order_review" class="woocommerce-checkout-review-order"><?php do_action( 'woocommerce_checkout_order_review' ); ?></div>
</form>