<?php
/**
 * Product archive template (tienda, categorías, tags).
 *
 * Layout con sidebar de categorías + grid de productos + paginación AJAX.
 * Solo se usa para archivos, NO para producto individual.
 *
 * @package Corralon_Pro
 */

defined( 'ABSPATH' ) || exit;

get_header();

$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/productos/' );

// Detectar categoría activa.
$active_cat      = null;
$active_cat_slug = '';

if ( is_product_category() ) {
	$active_cat      = get_queried_object();
	$active_cat_slug = $active_cat->slug;
} elseif ( is_product_tag() ) {
	$active_cat      = get_queried_object();
	$active_cat_slug = $active_cat->slug;
} elseif ( isset( $_GET['product_cat'] ) ) {
	$active_cat_slug = sanitize_title( wp_unslash( $_GET['product_cat'] ) );
	$active_cat      = get_term_by( 'slug', $active_cat_slug, 'product_cat' );
}

// Título dinámico.
if ( is_search() ) {
	$products_title    = sprintf( __( 'Resultados para: %s', 'corralon-pro' ), get_search_query() );
	$products_subtitle = __( 'Estos son los productos que coinciden con tu búsqueda.', 'corralon-pro' );
} elseif ( $active_cat && ! is_wp_error( $active_cat ) ) {
	$products_title    = sprintf( __( 'Productos en %s', 'corralon-pro' ), $active_cat->name );
	$products_subtitle = sprintf( __( 'Mostrando productos de %s.', 'corralon-pro' ), $active_cat->name );
} else {
	$products_title    = __( 'Nuestros productos', 'corralon-pro' );
	$products_subtitle = __( 'Explorá todo nuestro catálogo o filtrá por categoría.', 'corralon-pro' );
}

// Página actual.
$paged = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );

// Armar query.
$args = array(
	'post_type'      => 'product',
	'post_status'    => 'publish',
	'posts_per_page' => 20,
	'paged'          => $paged,
);

if ( $active_cat && ! is_wp_error( $active_cat ) ) {
	$taxonomy = is_product_tag() ? 'product_tag' : 'product_cat';
	$args['tax_query'] = array(
		array(
			'taxonomy' => $taxonomy,
			'field'    => 'term_id',
			'terms'    => $active_cat->term_id,
		),
	);
}

if ( is_search() ) {
	$args['s'] = get_search_query();
}

// Orden.
$orderby = isset( $_GET['orderby'] ) ? sanitize_key( $_GET['orderby'] ) : '';
switch ( $orderby ) {
	case 'price':
		$args['orderby']  = 'meta_value_num';
		$args['meta_key'] = '_price';
		$args['order']    = 'ASC';
		break;
	case 'price-desc':
		$args['orderby']  = 'meta_value_num';
		$args['meta_key'] = '_price';
		$args['order']    = 'DESC';
		break;
	case 'date':
		$args['orderby'] = 'date';
		$args['order']   = 'DESC';
		break;
	case 'popularity':
		$args['orderby']  = 'meta_value_num';
		$args['meta_key'] = 'total_sales';
		$args['order']    = 'DESC';
		break;
	default:
		$args['orderby'] = 'menu_order title';
		$args['order']   = 'ASC';
		break;
}

$products_query = new WP_Query( $args );
?>
<main id="primary" class="site-main">

	<div class="container shop-layout">

		<!-- ============================= -->
		<!-- SIDEBAR DE CATEGORÍAS         -->
		<!-- ============================= -->
		<aside class="shop-sidebar" aria-label="<?php esc_attr_e( 'Filtros de productos', 'corralon-pro' ); ?>">

			<div class="widget">
				<h3 class="widget-title"><?php esc_html_e( 'Categorías', 'corralon-pro' ); ?></h3>
				<ul class="category-filter-list">
					<li class="<?php echo $active_cat ? '' : 'current-cat'; ?>">
						<a href="<?php echo esc_url( $shop_url ); ?>" data-cat="">
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
							$is_current = ( $active_cat && ! is_wp_error( $active_cat ) && $active_cat->term_id === $category->term_id ) ? 'current-cat is-active' : '';
							?>
							<li class="<?php echo esc_attr( $is_current ); ?>">
								<a href="<?php echo esc_url( get_term_link( $category ) ); ?>" data-cat="<?php echo esc_attr( $category->slug ); ?>">
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
		<!-- CONTENIDO PRINCIPAL           -->
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

				<?php if ( $products_query->have_posts() ) : ?>
					<div class="product-grid">
						<?php
						while ( $products_query->have_posts() ) :
							$products_query->the_post();
							wc_get_template_part( 'content', 'product' );
						endwhile;
						?>
					</div>

					<?php
					$total_pages = (int) $products_query->max_num_pages;
					if ( $total_pages > 1 ) :
						?>
						<nav class="shop-pagination" aria-label="<?php esc_attr_e( 'Paginación de productos', 'corralon-pro' ); ?>">
							<?php
							echo paginate_links( array(
								'base'      => add_query_arg( 'paged', '%#%' ),
								'format'    => '',
								'current'   => $paged,
								'total'     => $total_pages,
								'mid_size'  => 2,
								'end_size'  => 1,
								'prev_text' => '&larr; ' . esc_html__( 'Anterior', 'corralon-pro' ),
								'next_text' => esc_html__( 'Siguiente', 'corralon-pro' ) . ' &rarr;',
								'type'      => 'list',
								'add_args'  => $active_cat_slug ? array( 'product_cat' => $active_cat_slug ) : array(),
							) );
							?>
						</nav>
					<?php endif; ?>

					<?php wp_reset_postdata(); ?>
				<?php else : ?>
					<div class="no-products-found">
						<strong><?php esc_html_e( 'No hay productos disponibles.', 'corralon-pro' ); ?></strong>
						<p><?php esc_html_e( 'Probá con otra categoría o volvé a ver todo el catálogo.', 'corralon-pro' ); ?></p>
						<a class="button" href="<?php echo esc_url( $shop_url ); ?>">
							<?php esc_html_e( 'Ver todos los productos', 'corralon-pro' ); ?>
						</a>
					</div>
				<?php endif; ?>
			</section>
		</div>
	</div>
</main>
<?php get_footer(); ?>