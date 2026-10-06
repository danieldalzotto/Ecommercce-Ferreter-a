<?php
/**
 * Single product template.
 *
 * Layout con sidebar de categorías + ficha del producto.
 *
 * @package Corralon_Pro
 */

defined( 'ABSPATH' ) || exit;

get_header();

$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/productos/' );
?>
<main id="primary" class="site-main">

	<div class="container shop-layout">

		<!-- ============================= -->
		<!-- SIDEBAR DE CATEGORÍAS         -->
		<!-- ============================= -->
		<aside class="shop-sidebar" aria-label="<?php esc_attr_e( 'Categorías', 'corralon-pro' ); ?>">

			<div class="widget">
				<h3 class="widget-title"><?php esc_html_e( 'Categorías', 'corralon-pro' ); ?></h3>
				<ul class="category-filter-list">
					<li>
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
							$is_current = has_term( $category->term_id, 'product_cat' ) ? 'current-cat is-active' : '';
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
		<!-- FICHA DEL PRODUCTO            -->
		<!-- ============================= -->
		<div class="shop-content">
			<?php
			while ( have_posts() ) :
				the_post();
				wc_get_template_part( 'content', 'single-product' );
			endwhile;
			?>
		</div>
	</div>
</main>
<?php get_footer(); ?>