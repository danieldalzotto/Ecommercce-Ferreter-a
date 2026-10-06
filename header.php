<?php
/**
 * Header template.
 *
 * @package Corralon_Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$phone   = corralon_pro_get_setting( 'phone' );
$account = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : wp_login_url();
$cart    = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : '#';
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div class="site-topbar">
	<div class="container topbar-inner">
		<span><?php echo esc_html( corralon_pro_get_setting( 'topbar_text', __( 'Materiales, herramientas y soluciones para tu obra', 'corralon-pro' ) ) ); ?></span>
		<?php if ( $phone ) : ?>
			<a href="<?php echo esc_url( 'tel:' . preg_replace( '/[^0-9+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a>
		<?php endif; ?>
	</div>
</div>

<header class="site-header">
	<div class="container header-main">
		<div class="site-branding">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<div>
					<a class="site-title" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>
					<span class="site-tagline"><?php bloginfo( 'description' ); ?></span>
				</div>
			<?php endif; ?>
		</div>

		<div class="header-search">
			<?php get_search_form(); ?>
		</div>

		<div class="header-actions">
			<a class="header-action" href="<?php echo esc_url( $account ); ?>" aria-label="<?php esc_attr_e( 'Mi cuenta', 'corralon-pro' ); ?>">
				<svg class="header-action-icon" width="20" height="20" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
					<path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/>
				</svg>
				<span class="header-action-label"><?php esc_html_e( 'Mi cuenta', 'corralon-pro' ); ?></span>
			</a>
			<a class="header-action" href="<?php echo esc_url( corralon_pro_get_favorites_url() ); ?>" aria-label="<?php esc_attr_e( 'Favoritos', 'corralon-pro' ); ?>">
				<svg class="header-action-icon" width="20" height="20" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
					<path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
				</svg>
				<span class="header-action-label"><?php esc_html_e( 'Favoritos', 'corralon-pro' ); ?></span>
				<span class="fav-count" hidden>0</span>
			</a>
			<a class="header-action" href="<?php echo esc_url( $cart ); ?>" aria-label="<?php esc_attr_e( 'Carrito', 'corralon-pro' ); ?>">
				<svg class="header-action-icon" width="20" height="20" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
					<path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4H6zM3 6h18M16 10a4 4 0 0 1-8 0"/>
				</svg>
				<span class="header-action-label"><?php esc_html_e( 'Carrito', 'corralon-pro' ); ?></span>
				<span class="cart-count"><?php echo esc_html( corralon_pro_cart_count() ); ?></span>
			</a>
		</div>
	</div>

	<nav class="main-navigation" aria-label="<?php esc_attr_e( 'Navegación principal', 'corralon-pro' ); ?>">
		<div class="container nav-inner">

			<!-- Botón Categorías -->
			<button
				class="menu-toggle categories-toggle"
				type="button"
				aria-expanded="false"
				aria-controls="categories-menu-wrapper"
			>
				<span class="categories-toggle-icon" aria-hidden="true"></span>
				<span class="categories-toggle-label"><?php esc_html_e( 'Categorías', 'corralon-pro' ); ?></span>
			</button>

			<!-- Menú principal -->
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'menu_id'        => 'primary-menu',
					'menu_class'     => 'primary-menu',
					'container'      => false,
					'fallback_cb'    => 'corralon_pro_menu_fallback',
					'depth'          => 3,
				)
			);
			?>

			<!-- Botón Menú (mobile) -->
			<button
				class="menu-toggle mobile-menu-toggle"
				type="button"
				aria-expanded="false"
				aria-controls="primary-menu"
			>
				<?php esc_html_e( 'Menú', 'corralon-pro' ); ?>
			</button>

			<!-- Dropdown de Categorías (anclado al container) -->
			<div class="categories-dropdown" id="categories-menu-wrapper">
				<?php
				$product_categories = get_terms( array(
					'taxonomy'   => 'product_cat',
					'parent'     => 0,
					'hide_empty' => true,
					'orderby'    => 'name',
					'order'      => 'ASC',
				) );

				if ( ! is_wp_error( $product_categories ) && ! empty( $product_categories ) ) :
					?>
					<div class="categories-megamenu">

						<!-- Columna izquierda: lista de categorías padre -->
						<div class="categories-megamenu-tabs">
							<?php foreach ( $product_categories as $category ) : ?>
								<a
									class="categories-megamenu-title"
									href="<?php echo esc_url( get_term_link( $category ) ); ?>"
									data-cat-id="<?php echo esc_attr( $category->term_id ); ?>"
								>
									<?php echo esc_html( $category->name ); ?>
								</a>
							<?php endforeach; ?>
						</div>

						<!-- Columna derecha: paneles por categoría -->
						<div class="categories-megamenu-panels">
							<?php foreach ( $product_categories as $category ) : ?>
								<?php
								$children = get_terms( array(
									'taxonomy'   => 'product_cat',
									'parent'     => $category->term_id,
									'hide_empty' => false,
									'orderby'    => 'name',
									'order'      => 'ASC',
								) );
								$category_link = get_term_link( $category );
								?>
								<div
									class="categories-megamenu-panel"
									data-cat-id="<?php echo esc_attr( $category->term_id ); ?>"
								>
									<div class="categories-megamenu-panel-header">
										<h3 class="categories-megamenu-panel-title"><?php echo esc_html( $category->name ); ?></h3>
										<a class="categories-megamenu-panel-link" href="<?php echo esc_url( $category_link ); ?>">
											<?php esc_html_e( 'Ver todo', 'corralon-pro' ); ?>
											<span aria-hidden="true">→</span>
										</a>
									</div>

									<?php if ( ! is_wp_error( $children ) && ! empty( $children ) ) : ?>
										<ul class="categories-megamenu-list">
											<?php foreach ( $children as $child ) : ?>
												<li>
													<a href="<?php echo esc_url( get_term_link( $child ) ); ?>">
														<?php echo esc_html( $child->name ); ?>
													</a>
												</li>
											<?php endforeach; ?>
										</ul>
									<?php else : ?>
										<ul class="categories-megamenu-list">
											<li>
												<a href="<?php echo esc_url( $category_link ); ?>">
													<?php esc_html_e( 'Ver productos', 'corralon-pro' ); ?>
												</a>
											</li>
										</ul>
									<?php endif; ?>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</nav>
</header>