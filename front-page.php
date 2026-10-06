<?php
/**
 * Storefront homepage with sidebar filters and full-width promo.
 *
 * El sidebar filtra por categoría (product_cat). Al hacer clic en una
 * categoría, se hace una petición AJAX (ver assets/js/filter.js) y se
 * actualiza solo el bloque de productos. Si JS está deshabilitado,
 * funciona igual con recarga vía ?product_cat=slug.
 *
 * @package Corralon_Pro
 */

get_header();

$hero_image = get_theme_mod( 'hero_image' );
$shop_url   = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/productos/' );

// Categoría activa desde la URL (si el usuario hizo clic en el sidebar).
$active_cat_slug = isset( $_GET['product_cat'] ) ? sanitize_title( wp_unslash( $_GET['product_cat'] ) ) : '';
$active_cat      = $active_cat_slug ? get_term_by( 'slug', $active_cat_slug, 'product_cat' ) : null;

// Título dinámico del bloque de productos.
if ( $active_cat && ! is_wp_error( $active_cat ) ) {
	$products_title = sprintf(
		/* translators: %s: category name */
		__( 'Productos en %s', 'corralon-pro' ),
		$active_cat->name
	);
	$products_subtitle = sprintf(
		/* translators: %s: category name */
		__( 'Mostrando productos de %s.', 'corralon-pro' ),
		$active_cat->name
	);
} else {
	$products_title    = __( 'Nuestros productos', 'corralon-pro' );
	$products_subtitle = __( 'Explorá todo nuestro catálogo o filtrá por categoría.', 'corralon-pro' );
}
?>
<main id="primary" class="site-main">
<?php
// DEBUG temporal
$debug_q = new WP_Query( array(
	'post_type'      => 'product',
	'post_status'    => 'publish',
	'posts_per_page' => 10,
	'no_found_rows'  => true,
) );
echo '<!-- DEBUG productos encontrados: ' . $debug_q->found_posts . ' -->';
echo '<!-- DEBUG post_type product existe: ' . ( post_type_exists( 'product' ) ? 'sí' : 'no' ) . ' -->';
echo '<!-- DEBUG WooCommerce activo: ' . ( class_exists( 'WooCommerce' ) ? 'sí' : 'no' ) . ' -->';
wp_reset_postdata();
?>
	<!-- ============================= -->
	<!-- SLIDER DE PRODUCTOS           -->
	<!-- ============================= -->
	<section class="hero hero-slider">
		<div class="container">

			<div class="section-heading">
				<div>
					<h2><?php esc_html_e( 'Últimos ingresos', 'corralon-pro' ); ?></h2>
				</div>
			</div>

			<?php
			$latest_products = new WP_Query( array(
					'post_type'      => 'product',
					'post_status'    => 'publish',
					'posts_per_page' => 8,
					'orderby'        => 'date',
					'order'          => 'DESC',
					'no_found_rows'  => true,
				) );

			if ( $latest_products->have_posts() ) : ?>
				<div class="hero-slider-wrapper" aria-roledescription="carrusel" aria-label="<?php esc_attr_e( 'Últimos productos', 'corralon-pro' ); ?>">

					<button type="button" class="hero-slider-nav hero-slider-prev" aria-label="<?php esc_attr_e( 'Anterior', 'corralon-pro' ); ?>">
						<svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true">
							<path fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" d="M15 5l-7 7 7 7"/>
						</svg>
					</button>

					<div class="hero-slider-viewport">
						<div class="hero-slider-track" id="hero-slider-track">
							<?php while ( $latest_products->have_posts() ) : $latest_products->the_post();
								global $product;
								if ( ! $product instanceof WC_Product ) {
									$product = wc_get_product( get_the_ID() );
								}
								if ( ! $product ) {
									continue;
								}

								$terms    = get_the_terms( get_the_ID(), 'product_cat' );
								$cat_name = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : '';

								$discount = 0;
								if ( $product->is_on_sale() ) {
									$regular = (float) $product->get_regular_price();
									$sale    = (float) $product->get_sale_price();
									if ( $regular > 0 && $sale > 0 ) {
										$discount = round( ( ( $regular - $sale ) / $regular ) * 100 );
									}
								}
								?>
								<article class="hero-slide">
									<a class="hero-slide-link" href="<?php the_permalink(); ?>">
										<div class="hero-slide-media">
											<?php if ( $discount > 0 ) : ?>
												<span class="hero-slide-badge"><?php echo esc_html( $discount ); ?>% OFF</span>
											<?php endif; ?>
											<?php echo $product->get_image( 'woocommerce_thumbnail' ); ?>
										</div>
										<div class="hero-slide-info">
											<?php if ( $cat_name ) : ?>
												<span class="hero-slide-cat"><?php echo esc_html( $cat_name ); ?></span>
											<?php endif; ?>
											<h3 class="hero-slide-title"><?php the_title(); ?></h3>
											<div class="hero-slide-price"><?php echo wp_kses_post( $product->get_price_html() ); ?></div>
											<span class="hero-slide-cta"><?php esc_html_e( 'Ver producto', 'corralon-pro' ); ?></span>
										</div>
									</a>
								</article>
							<?php endwhile; wp_reset_postdata(); ?>
						</div>
					</div>

					<button type="button" class="hero-slider-nav hero-slider-next" aria-label="<?php esc_attr_e( 'Siguiente', 'corralon-pro' ); ?>">
						<svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true">
							<path fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
						</svg>
					</button>

				</div>
			<?php endif; ?>
		</div>
	</section>

	<!-- ============================= -->
	<!-- BANNERS DE TARJETAS           -->
	<!-- ============================= -->
	<section class="section banners-marcas">
		<div class="container">
			<div class="benefit-grid">

				<a class="benefit-card banner-marca" aria-label="<?php esc_attr_e( 'Ver promociones de Visa', 'corralon-pro' ); ?>">
					<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/tarjeta-1.png' ); ?>" alt="<?php esc_attr_e( 'Visa', 'corralon-pro' ); ?>">
				</a>

				<a class="benefit-card banner-marca" aria-label="<?php esc_attr_e( 'Ver promociones de Mastercard', 'corralon-pro' ); ?>">
					<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/tarjeta-2.png' ); ?>" alt="<?php esc_attr_e( 'Mastercard', 'corralon-pro' ); ?>">
				</a>

				<a class="benefit-card banner-marca" aria-label="<?php esc_attr_e( 'Ver promociones de American Express', 'corralon-pro' ); ?>">
					<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/tarjeta-3.png' ); ?>" alt="<?php esc_attr_e( 'American Express', 'corralon-pro' ); ?>">
				</a>
				<a class="benefit-card banner-marca" aria-label="<?php esc_attr_e( 'Ver promociones de Tarjeta Naranja', 'corralon-pro' ); ?>">
					<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/tarjeta-4.jpg' ); ?>" alt="<?php esc_attr_e( 'American Express', 'corralon-pro' ); ?>">
				</a>

			</div>
		</div>
	</section>

	<?php if ( class_exists( 'WooCommerce' ) ) : ?>
		<div class="container shop-layout">

			<!-- ============================= -->
			<!-- SIDEBAR DE FILTROS            -->
			<!-- ============================= -->
			<aside class="shop-sidebar" aria-label="<?php esc_attr_e( 'Filtros de productos', 'corralon-pro' ); ?>">

				<div class="widget">
					<h3 class="widget-title"><?php esc_html_e( 'Categorías', 'corralon-pro' ); ?></h3>
					<ul class="category-filter-list">
						<li class="<?php echo $active_cat ? '' : 'current-cat'; ?>">
							<a href="<?php echo esc_url( remove_query_arg( 'product_cat' ) ); ?>" data-cat="">
								<span><?php esc_html_e( 'Todos los productos', 'corralon-pro' ); ?></span>
								<span class="count"><?php echo esc_html( wp_count_posts( 'product' )->publish ); ?></span>
							</a>
						</li>
						<?php
						$categories = get_terms( array(
							'taxonomy'   => 'product_cat',
							'parent'     => 0,
							'hide_empty' => true,
						) );
						if ( ! is_wp_error( $categories ) && ! empty( $categories ) ) :
							foreach ( $categories as $category ) :
								$is_current = ( $active_cat && $active_cat->term_id === $category->term_id ) ? 'current-cat is-active' : '';
								?>
								<li class="<?php echo esc_attr( $is_current ); ?>">
									<a href="<?php echo esc_url( add_query_arg( 'product_cat', $category->slug, remove_query_arg( 'product_cat' ) ) ); ?>" data-cat="<?php echo esc_attr( $category->slug ); ?>">
										<span><?php echo esc_html( $category->name ); ?></span>
										<span class="count"><?php echo esc_html( $category->count ); ?></span>
									</a>
								</li>
							<?php
							endforeach;
						endif;
						?>
					</ul>
				</div>

				<?php if ( is_active_sidebar( 'sidebar-shop' ) ) : ?>
					<?php dynamic_sidebar( 'sidebar-shop' ); ?>
				<?php endif; ?>
			</aside>

			<!-- ============================= -->
			<!-- PRODUCTOS FILTRADOS           -->
			<!-- ============================= -->
			<div class="shop-content">
				<section class="section">
					<div class="section-heading">
						<div>
							<h2><?php echo esc_html( $products_title ); ?></h2>
							<p><?php echo esc_html( $products_subtitle ); ?></p>
						</div>
						<a class="view-all-link" href="<?php echo esc_url( $shop_url ); ?>">
							<?php esc_html_e( 'Ver tienda completa', 'corralon-pro' ); ?>
						</a>
					</div>

					<?php
					// Query de productos según categoría activa.
					$paged = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );

					$args = array(
						'post_type'      => 'product',
						'post_status'    => 'publish',
						'posts_per_page' => 9,
						'paged'          => $paged,
					);

					if ( $active_cat && ! is_wp_error( $active_cat ) ) {
						$args['tax_query'] = array(
							array(
								'taxonomy' => 'product_cat',
								'field'    => 'term_id',
								'terms'    => $active_cat->term_id,
							),
						);
					}

					$products_query = new WP_Query( $args );
					?>

					<?php if ( $products_query->have_posts() ) : ?>
						<div class="product-grid">
							<?php
							while ( $products_query->have_posts() ) :
								$products_query->the_post();
								wc_get_template_part( 'content', 'product' );
							endwhile;
							wp_reset_postdata();
							?>
						</div>
					<?php else : ?>
						<div class="no-products-found">
							<strong><?php esc_html_e( 'No hay productos en esta categoría.', 'corralon-pro' ); ?></strong>
							<p><?php esc_html_e( 'Probá con otra categoría o volvé a ver todo el catálogo.', 'corralon-pro' ); ?></p>
							<a class="button" href="<?php echo esc_url( remove_query_arg( 'product_cat' ) ); ?>">
								<?php esc_html_e( 'Ver todos los productos', 'corralon-pro' ); ?>
							</a>
						</div>
					<?php endif; ?>
				</section>
			</div>
		</div>

		<!-- ============================= -->
		<!-- OFERTA DE LA SEMANA (FULL)    -->
		<!-- ============================= -->
		<?php
		$sale_products = wc_get_products( array(
			'on_sale' => true,
			'limit'   => 4,
			'orderby' => 'date',
			'order'   => 'DESC',
			'status'  => 'publish',
		) );

		if ( ! empty( $sale_products ) ) : ?>
			<section class="promo-strip">
				<div class="container">
					<div class="promo-strip-inner">
						<div class="promo-strip-content">
							<span class="promo-eyebrow">🔥 <?php esc_html_e( 'Oferta limitada', 'corralon-pro' ); ?></span>
							<h2><?php esc_html_e( 'Oferta de la semana', 'corralon-pro' ); ?></h2>
							<p><?php esc_html_e( 'Aprovechá precios especiales por tiempo limitado.', 'corralon-pro' ); ?></p>
							<div class="promo-strip-actions">
								<a class="button button-primary-offer" href="<?php echo esc_url( $shop_url ); ?>">
									<?php esc_html_e( 'Ver todas las ofertas', 'corralon-pro' ); ?>
								</a>
								<a class="button button-ghost-offer" href="<?php echo esc_url( $shop_url ); ?>">
									<?php esc_html_e( 'Explorar catálogo', 'corralon-pro' ); ?>
								</a>
							</div>
						</div>
						<div class="promo-countdown" aria-hidden="true">
							<div class="promo-countdown-unit"><strong>07</strong><span><?php esc_html_e( 'Días', 'corralon-pro' ); ?></span></div>
							<div class="promo-countdown-unit"><strong>12</strong><span><?php esc_html_e( 'Horas', 'corralon-pro' ); ?></span></div>
							<div class="promo-countdown-unit"><strong>45</strong><span><?php esc_html_e( 'Min', 'corralon-pro' ); ?></span></div>
							<div class="promo-countdown-unit"><strong>30</strong><span><?php esc_html_e( 'Seg', 'corralon-pro' ); ?></span></div>
						</div>
					</div>
				</div>
			</section>

			<section class="section promo-products">
				<div class="container">
					<div class="product-grid">
						<?php foreach ( $sale_products as $product ) : ?>
							<?php
							// Renderizar cada producto usando el template content-product.php.
							$post_object = get_post( $product->get_id() );
							if ( $post_object ) :
								setup_postdata( $GLOBALS['post'] = $post_object ); // phpcs:ignore
								wc_get_template_part( 'content', 'product' );
							endif;
							?>
						<?php endforeach; ?>
						<?php wp_reset_postdata(); ?>
					</div>
				</div>
			</section>
		<?php endif; ?>
	<?php endif; ?>

	<!-- ============================= -->
	<!-- BENEFICIOS                    -->
	<!-- ============================= -->
	<section class="section">
		<div class="container">
			<div class="section-heading">
				<div>
					<h2><?php esc_html_e( 'Comprá con tranquilidad', 'corralon-pro' ); ?></h2>
					<p><?php esc_html_e( 'Te acompañamos desde la elección hasta la entrega.', 'corralon-pro' ); ?></p>
				</div>
			</div>
			<div class="benefit-grid">
				<div class="benefit-card">
					<strong><?php esc_html_e( 'Asesoramiento', 'corralon-pro' ); ?></strong>
					<span><?php esc_html_e( 'Ayuda para elegir materiales y herramientas.', 'corralon-pro' ); ?></span>
				</div>
				<div class="benefit-card">
					<strong><?php esc_html_e( 'Envíos coordinados', 'corralon-pro' ); ?></strong>
					<span><?php esc_html_e( 'Opciones de retiro y entrega para tu zona.', 'corralon-pro' ); ?></span>
				</div>
				<div class="benefit-card">
					<strong><?php esc_html_e( 'Compra segura', 'corralon-pro' ); ?></strong>
					<span><?php esc_html_e( 'Checkout preparado para medios de pago WooCommerce.', 'corralon-pro' ); ?></span>
				</div>
				<div class="benefit-card">
					<strong><?php esc_html_e( 'Atención directa', 'corralon-pro' ); ?></strong>
					<span><?php esc_html_e( 'Consultanos por WhatsApp cuando lo necesites.', 'corralon-pro' ); ?></span>
				</div>
			</div>
		</div>
	</section>
</main>
<?php get_footer(); ?>