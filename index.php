<?php
/**
 * The fallback template.
 *
 * @package Corralon_Pro
 */

get_header();
?>
<main id="primary" class="site-main">
	<div class="container content-area">
		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<article <?php post_class(); ?>>
					<?php the_title( '<h1>', '</h1>' ); ?>
					<?php the_content(); ?>
				</article>
			<?php endwhile; ?>
		<?php else : ?>
			<p><?php esc_html_e( 'No se encontraron contenidos.', 'corralon-pro' ); ?></p>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();