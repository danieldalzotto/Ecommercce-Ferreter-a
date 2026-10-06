<?php
/**
 * Shared template helpers.
 *
 * @package Corralon_Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function corralon_pro_get_setting( $key, $default = '' ) {
	$value = get_theme_mod( $key, '' );
	return '' !== $value ? $value : $default;
}

function corralon_pro_get_page_url( $slug ) {
	$page = get_page_by_path( $slug );
	return $page ? get_permalink( $page ) : home_url( '/' . trim( $slug, '/' ) . '/' );
}

function corralon_pro_get_favorites_url() {
	return corralon_pro_get_page_url( 'favoritos' );
}

function corralon_pro_cart_count() {
	return function_exists( 'WC' ) && WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
}

function corralon_pro_whatsapp_url( $message = '' ) {
	$number = preg_replace( '/[^0-9]/', '', (string) corralon_pro_get_setting( 'whatsapp' ) );
	if ( ! $number ) {
		return '#';
	}
	$message = $message ? $message : __( 'Hola, quisiera hacer una consulta.', 'corralon-pro' );
	return 'https://wa.me/' . $number . '?text=' . rawurlencode( $message );
}

function corralon_pro_product_whatsapp_url( $product ) {
	$message = sprintf(
		/* translators: 1: product name, 2: SKU, 3: product URL. */
		__( "Hola, quisiera consultar por:\n%s\nSKU: %s\nURL del producto: %s", 'corralon-pro' ),
		$product->get_name(),
		$product->get_sku() ? $product->get_sku() : __( 'Sin SKU', 'corralon-pro' ),
		get_permalink( $product->get_id() )
	);
	return corralon_pro_whatsapp_url( $message );
}

function corralon_pro_format_product_brand( $product_id ) {
	$terms = get_the_terms( $product_id, 'product_brand' );
	if ( empty( $terms ) || is_wp_error( $terms ) ) {
		return '';
	}
	return $terms[0]->name;
}

function corralon_pro_stock_badge( $product ) {
	if ( ! $product->is_in_stock() ) {
		return '<span class="badge badge-warning">' . esc_html__( 'Agotado', 'corralon-pro' ) . '</span>';
	}
	if ( $product->managing_stock() && $product->get_stock_quantity() && $product->get_stock_quantity() <= 3 ) {
		return '<span class="badge badge-warning">' . esc_html__( 'Últimas unidades', 'corralon-pro' ) . '</span>';
	}
	return '<span class="badge badge-stock">' . esc_html__( 'En stock', 'corralon-pro' ) . '</span>';
}

function corralon_pro_render_product_card( $product = null ) {
	if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
		return;
	}
	?>
	<article <?php wc_product_class( 'product-card', $product ); ?>>
		<div class="product-card-image">

			<div class="product-badges">
				<?php if ( $product->is_on_sale() ) : ?>
					<span class="product-badge badge-sale"><?php esc_html_e( 'Oferta', 'corralon-pro' ); ?></span>
				<?php elseif ( $product->is_featured() ) : ?>
					<span class="product-badge badge-featured"><?php esc_html_e( 'Destacado', 'corralon-pro' ); ?></span>
				<?php endif; ?>

				<?php if ( $product->is_in_stock() ) : ?>
					<span class="product-badge badge-instock"><?php esc_html_e( 'En stock', 'corralon-pro' ); ?></span>
				<?php else : ?>
					<span class="product-badge badge-outofstock"><?php esc_html_e( 'Sin stock', 'corralon-pro' ); ?></span>
				<?php endif; ?>
			</div>

			<button
				type="button"
				class="wishlist-button"
				data-wishlist-id="<?php echo esc_attr( $product->get_id() ); ?>"
				aria-label="<?php esc_attr_e( 'Agregar a favoritos', 'corralon-pro' ); ?>"
			>
				<svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
					<path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
				</svg>
			</button>

			<a href="<?php echo esc_url( $product->get_permalink() ); ?>">
				<?php echo wp_kses_post( $product->get_image( 'woocommerce_thumbnail' ) ); ?>
			</a>
		</div>

		<div class="product-card-content">
			<?php
			$terms    = get_the_terms( $product->get_id(), 'product_cat' );
			$cat_name = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : '';
			?>
			<?php if ( $cat_name ) : ?>
				<span class="product-cat-label"><?php echo esc_html( $cat_name ); ?></span>
			<?php endif; ?>

			<h3 class="product-title">
				<a href="<?php echo esc_url( $product->get_permalink() ); ?>"><?php echo esc_html( $product->get_name() ); ?></a>
			</h3>

			<div class="product-price">
				<?php if ( $product->get_price() ) : ?>
					<?php echo wp_kses_post( $product->get_price_html() ); ?>
				<?php else : ?>
					<span><?php esc_html_e( 'Consultar precio', 'corralon-pro' ); ?></span>
				<?php endif; ?>
			</div>

			<?php if ( $product->get_sku() ) : ?>
				<div class="product-meta">
					<span><?php esc_html_e( 'SKU:', 'corralon-pro' ); ?> <strong><?php echo esc_html( $product->get_sku() ); ?></strong></span>
				</div>
			<?php endif; ?>

			<div class="product-actions">
				<?php if ( $product->is_purchasable() && $product->is_in_stock() ) : ?>
					<a
						class="button btn-add add_to_cart_button ajax_add_to_cart"
						href="<?php echo esc_url( $product->add_to_cart_url() ); ?>"
						data-quantity="1"
						data-product_id="<?php echo esc_attr( $product->get_id() ); ?>"
					>
						<?php esc_html_e( 'Agregar al carrito', 'corralon-pro' ); ?>
					</a>
				<?php else : ?>
					<a class="button outline btn-view" href="<?php echo esc_url( $product->get_permalink() ); ?>">
						<?php esc_html_e( 'Ver', 'corralon-pro' ); ?>
					</a>
				<?php endif; ?>
				<a class="button outline btn-view" href="<?php echo esc_url( $product->get_permalink() ); ?>">
					<?php esc_html_e( 'Ver', 'corralon-pro' ); ?>
				</a>
			</div>
		</div>
	</article>
	<?php
}

function corralon_pro_render_mega_menu() {
	if ( ! taxonomy_exists( 'product_cat' ) ) {
		return;
	}
	$parents = get_terms( array( 'taxonomy' => 'product_cat', 'parent' => 0, 'hide_empty' => true, 'number' => 12 ) );
	foreach ( $parents as $parent ) :
		$children = get_terms( array( 'taxonomy' => 'product_cat', 'parent' => $parent->term_id, 'hide_empty' => true, 'number' => 8 ) );
		?>
		<div class="mega-menu-group">
			<h3><a href="<?php echo esc_url( get_term_link( $parent ) ); ?>"><?php echo esc_html( $parent->name ); ?></a></h3>
			<?php if ( $children && ! is_wp_error( $children ) ) : ?>
				<ul>
					<?php foreach ( $children as $child ) : ?>
						<li><a href="<?php echo esc_url( get_term_link( $child ) ); ?>"><?php echo esc_html( $child->name ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
		<?php
	endforeach;
}

function corralon_pro_breadcrumbs() {
	if ( is_front_page() ) {
		return;
	}
	echo '<nav class="breadcrumbs" aria-label="' . esc_attr__( 'Migas de pan', 'corralon-pro' ) . '">';
	echo '<a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Inicio', 'corralon-pro' ) . '</a> / ';
	if ( function_exists( 'woocommerce_breadcrumb' ) && ( is_shop() || is_product() || is_product_category() ) ) {
		woocommerce_breadcrumb( array( 'delimiter' => '' ) );
	} else {
		echo esc_html( wp_get_document_title() );
	}
	echo '</nav>';
}

function corralon_pro_pagination() {
	echo '<nav class="pagination" aria-label="' . esc_attr__( 'Paginación', 'corralon-pro' ) . '">';
	echo wp_kses_post(
		paginate_links(
			array(
				'type'      => 'plain',
				'prev_text' => '←',
				'next_text' => '→',
				'current'   => max( 1, get_query_var( 'paged' ) ),
				'total'     => $GLOBALS['wp_query']->max_num_pages,
			)
		)
	);
	echo '</nav>';
}

function corralon_pro_render_listing_page( $title, $description, $products ) {
	get_header();
	?>
	<main id="primary" class="site-main">
		<div class="page-header">
			<div class="container">
				<?php corralon_pro_breadcrumbs(); ?>
				<h1><?php echo esc_html( $title ); ?></h1>
				<?php if ( $description ) : ?><p><?php echo esc_html( $description ); ?></p><?php endif; ?>
			</div>
		</div>
		<div class="container content-area">
			<?php if ( ! class_exists( 'WooCommerce' ) ) : ?>
				<p><?php esc_html_e( 'Esta sección requiere WooCommerce activo.', 'corralon-pro' ); ?></p>
			<?php elseif ( empty( $products ) ) : ?>
				<p><?php esc_html_e( 'Todavía no hay productos para mostrar.', 'corralon-pro' ); ?></p>
			<?php else : ?>
				<div class="product-grid">
					<?php foreach ( $products as $product ) : ?>
						<?php corralon_pro_render_product_card( $product ); ?>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</main>
	<?php
	get_footer();
}

/* =========================================================
   FAVORITOS
   ========================================================= */

/**
 * Shortcode [corralon_favoritos] para mostrar la grilla de favoritos.
 * Pegalo en la página Favoritos de WordPress.
 */
function corralon_pro_favoritos_shortcode() {
	ob_start();
	?>
	<div class="favorites-page section">
		<div class="container">
			<div id="corralon-favorites-grid" class="product-grid">
				<p><?php esc_html_e( 'Cargando tus favoritos…', 'corralon-pro' ); ?></p>
			</div>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'corralon_favoritos', 'corralon_pro_favoritos_shortcode' );

/**
 * AJAX: devuelve el HTML de los productos favoritos a partir de los IDs.
 */
function corralon_pro_favorites_ajax() {
	check_ajax_referer( 'corralon_pro_search', 'nonce' );

	$ids_raw = isset( $_GET['ids'] ) ? sanitize_text_field( wp_unslash( $_GET['ids'] ) ) : '';
	$ids     = array_filter( array_map( 'absint', explode( ',', $ids_raw ) ) );

	ob_start();

	if ( empty( $ids ) ) {
		echo '<p>' . esc_html__( 'No tenés productos guardados todavía.', 'corralon-pro' ) . '</p>';
		wp_send_json_success( array( 'html' => ob_get_clean() ) );
	}

	$query = new WP_Query( array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'post__in'       => $ids,
		'orderby'        => 'post__in',
		'posts_per_page' => count( $ids ),
		'no_found_rows'  => true,
	) );

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$product = wc_get_product( get_the_ID() );
			if ( $product ) {
				corralon_pro_render_product_card( $product );
			}
		}
		wp_reset_postdata();
	} else {
		echo '<p>' . esc_html__( 'No pudimos cargar tus favoritos.', 'corralon-pro' ) . '</p>';
	}

	wp_send_json_success( array( 'html' => ob_get_clean() ) );
}
add_action( 'wp_ajax_corralon_pro_favorites', 'corralon_pro_favorites_ajax' );
add_action( 'wp_ajax_nopriv_corralon_pro_favorites', 'corralon_pro_favorites_ajax' );

/**
 * Título de página para Favoritos.
 */
function corralon_pro_favoritos_the_title( $title ) {
	if ( is_page( 'favoritos' ) && in_the_loop() ) {
		return __( 'Mis favoritos', 'corralon-pro' );
	}
	return $title;
}
add_filter( 'the_title', 'corralon_pro_favoritos_the_title' );