<?php
/**
 * Footer template.
 *
 * @package Corralon_Pro
 */

$whatsapp = corralon_pro_get_setting( 'whatsapp' );
$address  = corralon_pro_get_setting( 'address' );
$hours    = corralon_pro_get_setting( 'hours' );
$email    = corralon_pro_get_setting( 'email' );
$phone    = corralon_pro_get_setting( 'phone' );
?>
<footer class="site-footer">

	<!-- Newsletter -->
	<div class="footer-newsletter">
		<div class="container footer-newsletter-inner">
			<div>
				<h3><?php esc_html_e( 'Recibí ofertas y novedades', 'corralon-pro' ); ?></h3>
				<p><?php esc_html_e( 'Suscribite y enterate primero de nuestros descuentos.', 'corralon-pro' ); ?></p>
			</div>
			<form class="footer-newsletter-form" method="post" action="#">
				<label for="footer-newsletter-email" class="screen-reader-text">
					<?php esc_html_e( 'Tu correo electrónico', 'corralon-pro' ); ?>
				</label>
				<input
					type="email"
					id="footer-newsletter-email"
					name="newsletter_email"
					placeholder="<?php esc_attr_e( 'Tu correo electrónico', 'corralon-pro' ); ?>"
					required
				>
				<button type="submit"><?php esc_html_e( 'Suscribirme', 'corralon-pro' ); ?></button>
			</form>
		</div>
	</div>

	<!-- Columnas -->
	<div class="container footer-main">

		<!-- Marca + descripción + redes -->
		<div class="footer-col">
			<a class="footer-brand-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php if ( has_custom_logo() ) : ?>
					<?php the_custom_logo(); ?>
				<?php else : ?>
					<?php bloginfo( 'name' ); ?>
				<?php endif; ?>
			</a>
			<p class="footer-brand-desc">
				<?php echo esc_html( corralon_pro_get_setting( 'footer_description', __( 'Materiales y herramientas para construir mejor.', 'corralon-pro' ) ) ); ?>
			</p>

			<div class="footer-social">
				<a href="#" aria-label="<?php esc_attr_e( 'Facebook', 'corralon-pro' ); ?>" rel="noopener">
					<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
						<path d="M22 12.06C22 6.5 17.52 2 12 2S2 6.5 2 12.06c0 5 3.66 9.15 8.44 9.94v-7.03H7.9v-2.9h2.54V9.85c0-2.52 1.5-3.9 3.78-3.9 1.1 0 2.24.2 2.24.2v2.46h-1.26c-1.24 0-1.63.78-1.63 1.57v1.88h2.78l-.45 2.9h-2.33V22c4.78-.79 8.44-4.94 8.44-9.94z"/>
					</svg>
				</a>
				<a href="#" aria-label="<?php esc_attr_e( 'Instagram', 'corralon-pro' ); ?>" rel="noopener">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
						<rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
						<path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
						<line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
					</svg>
				</a>
				<a href="<?php echo esc_url( corralon_pro_whatsapp_url() ); ?>" aria-label="<?php esc_attr_e( 'WhatsApp', 'corralon-pro' ); ?>" target="_blank" rel="noopener">
					<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
						<path d="M20.52 3.48A11.9 11.9 0 0 0 12 0C5.37 0 0 5.37 0 12a11.9 11.9 0 0 0 1.63 6L0 24l6.18-1.62A11.9 11.9 0 0 0 12 24c6.63 0 12-5.37 12-12a11.9 11.9 0 0 0-3.48-8.52zM12 21.8a9.8 9.8 0 0 1-5-1.37l-.36-.21-3.67.96.98-3.58-.23-.37A9.8 9.8 0 1 1 12 21.8z"/>
					</svg>
				</a>
			</div>
		</div>

		<!-- Categorías -->
		<div class="footer-col">
			<h3><?php esc_html_e( 'Categorías', 'corralon-pro' ); ?></h3>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'footer_categories',
					'menu_class'     => 'footer-menu',
					'container'      => false,
					'fallback_cb'    => 'corralon_pro_footer_category_fallback',
					'depth'          => 1,
				)
			);
			?>
		</div>

		<!-- Información -->
		<div class="footer-col">
			<h3><?php esc_html_e( 'Información', 'corralon-pro' ); ?></h3>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'footer_info',
					'menu_class'     => 'footer-menu',
					'container'      => false,
					'fallback_cb'    => 'corralon_pro_footer_info_fallback',
					'depth'          => 1,
				)
			);
			?>
		</div>

		<!-- Contacto -->
		<div class="footer-col">
			<h3><?php esc_html_e( 'Contacto', 'corralon-pro' ); ?></h3>
			<ul class="footer-contact">
				<?php if ( $address ) : ?>
					<li>
						<span class="footer-contact-icon" aria-hidden="true">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
								<circle cx="12" cy="10" r="3"></circle>
							</svg>
						</span>
						<div>
							<strong><?php esc_html_e( 'Dirección', 'corralon-pro' ); ?></strong>
							<?php echo esc_html( $address ); ?>
						</div>
					</li>
				<?php endif; ?>

				<?php if ( $hours ) : ?>
					<li>
						<span class="footer-contact-icon" aria-hidden="true">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<circle cx="12" cy="12" r="10"></circle>
								<polyline points="12 6 12 12 16 14"></polyline>
							</svg>
						</span>
						<div>
							<strong><?php esc_html_e( 'Horarios', 'corralon-pro' ); ?></strong>
							<?php echo esc_html( $hours ); ?>
						</div>
					</li>
				<?php endif; ?>

				<?php if ( $phone ) : ?>
					<li>
						<span class="footer-contact-icon" aria-hidden="true">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
							</svg>
						</span>
						<div>
							<strong><?php esc_html_e( 'Teléfono', 'corralon-pro' ); ?></strong>
							<a href="<?php echo esc_url( 'tel:' . preg_replace( '/[^0-9+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a>
						</div>
					</li>
				<?php endif; ?>

				<?php if ( $email ) : ?>
					<li>
						<span class="footer-contact-icon" aria-hidden="true">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
								<polyline points="22,6 12,13 2,6"></polyline>
							</svg>
						</span>
						<div>
							<strong><?php esc_html_e( 'Email', 'corralon-pro' ); ?></strong>
							<a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
						</div>
					</li>
				<?php endif; ?>
			</ul>
		</div>
	</div>

	<!-- Barra inferior -->
	<div class="footer-bottom">
		<div class="container footer-bottom-inner">
			<p>
				&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>
			</p>
			<div class="footer-payment">
				<span>Visa</span>
				<span>Mastercard</span>
				<span>Mercado Pago</span>
				<span>Transferencia</span>
			</div>
		</div>
	</div>
</footer>

<button
	type="button"
	class="back-to-top"
	aria-label="<?php esc_attr_e( 'Volver arriba', 'corralon-pro' ); ?>"
	hidden
>
	<span aria-hidden="true">↑</span>
</button>

<?php wp_footer(); ?>
</body>
</html>