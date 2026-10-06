<?php
get_header();
?>
<main id="primary" class="site-main">
	<div class="container content-area">
		<h1><?php esc_html_e( 'No encontramos esa página', 'corralon-pro' ); ?></h1>
		<p><?php esc_html_e( 'Podés volver al inicio o buscar el producto que necesitás.', 'corralon-pro' ); ?></p>
		<a class="button" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Ir al inicio', 'corralon-pro' ); ?></a>
	</div>
</main>
<?php get_footer(); ?>