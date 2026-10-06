<?php
/**
 * Template for static pages.
 *
 * @package Corralon_Pro
 */

get_header();
?>

<main id="primary" class="site-main">

	<!-- ============================= -->
	<!-- HEADER DE LA PÁGINA           -->
	<!-- ============================= -->
	<div class="page-header">
		<div class="container">
			<?php corralon_pro_breadcrumbs(); ?>
			<?php the_title( '<h1>', '</h1>' ); ?>
		</div>
	</div>

	<!-- ============================= -->
	<!-- CONTENIDO DE LA PÁGINA        -->
	<!-- ============================= -->
	<div class="container content-area">
		<?php while ( have_posts() ) : the_post(); ?>
			<article <?php post_class(); ?>>
				<div class="entry-content">
					<?php the_content(); ?>
				</div>
			</article>
		<?php endwhile; ?>
	</div>
</main>

<?php get_footer(); ?>