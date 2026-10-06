<?php
get_header();
?>
<main id="primary" class="site-main">
	<div class="page-header">
		<div class="container">
			<?php corralon_pro_breadcrumbs(); ?>
			<h1><?php printf( esc_html__( 'Resultados para: %s', 'corralon-pro' ), '<span>' . esc_html( get_search_query() ) . '</span>' ); ?></h1>
		</div>
	</div>
	<div class="container content-area">
		<?php if ( have_posts() ) : ?>
			<div class="product-grid">
				<?php while ( have_posts() ) : the_post(); ?>
					<article <?php post_class( 'product-card' ); ?>>
						<div class="product-card-content">
							<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
							<div><?php the_excerpt(); ?></div>
							<a class="button secondary" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Ver contenido', 'corralon-pro' ); ?></a>
						</div>
					</article>
				<?php endwhile; ?>
			</div>
			<?php corralon_pro_pagination(); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'No encontramos resultados. Probá con otra búsqueda.', 'corralon-pro' ); ?></p>
			<?php get_search_form(); ?>
		<?php endif; ?>
	</div>
</main>
<?php get_footer(); ?>