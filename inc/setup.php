<?php
/**
 * Theme setup.
 *
 * @package Corralon_Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function corralon_pro_setup() {
	load_theme_textdomain( 'corralon-pro', CORRALON_PRO_DIR . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo', array( 'height' => 80, 'width' => 260, 'flex-height' => true, 'flex-width' => true ) );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'woocommerce', array( 'thumbnail_image_width' => 560, 'single_image_width' => 760 ) );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	register_nav_menus(
		array(
			'primary'           => __( 'Menú principal', 'corralon-pro' ),
			'categories_menu'   => __( 'Menú de Categorías (header)', 'corralon-pro' ),
			'footer_categories' => __( 'Footer: categorías', 'corralon-pro' ),
			'footer_info'       => __( 'Footer: información', 'corralon-pro' ),
		)
	);
}
add_action( 'after_setup_theme', 'corralon_pro_setup' );

function corralon_pro_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'corralon_pro_content_width', 1200 );
}
add_action( 'after_setup_theme', 'corralon_pro_content_width', 0 );

function corralon_pro_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'Sidebar de tienda', 'corralon-pro' ),
			'id'            => 'shop-sidebar',
			'description'   => __( 'Widgets visibles en archivos de productos.', 'corralon-pro' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'corralon_pro_widgets_init' );

function corralon_pro_menu_fallback() {
	echo '<ul id="primary-menu" class="menu">';
	echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Inicio', 'corralon-pro' ) . '</a></li>';
	if ( function_exists( 'wc_get_page_permalink' ) ) {
		echo '<li><a href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '">' . esc_html__( 'Productos', 'corralon-pro' ) . '</a></li>';
	}
	echo '<li><a href="' . esc_url( corralon_pro_get_page_url( 'contacto' ) ) . '">' . esc_html__( 'Contacto', 'corralon-pro' ) . '</a></li>';
	echo '</ul>';
}

function corralon_pro_categories_menu_fallback() {
	echo '<ul id="categories-menu" class="menu">';
	if ( taxonomy_exists( 'product_cat' ) ) {
		$categories = get_terms( array( 'taxonomy' => 'product_cat', 'parent' => 0, 'hide_empty' => true, 'number' => 12 ) );
		if ( ! is_wp_error( $categories ) ) {
			foreach ( $categories as $category ) {
				echo '<li><a href="' . esc_url( get_term_link( $category ) ) . '">' . esc_html( $category->name ) . '</a></li>';
			}
		}
	}
	echo '</ul>';
}

function corralon_pro_footer_category_fallback() {
	echo '<ul class="footer-menu">';
	if ( taxonomy_exists( 'product_cat' ) ) {
		$categories = get_terms( array( 'taxonomy' => 'product_cat', 'parent' => 0, 'number' => 6, 'hide_empty' => true ) );
		foreach ( $categories as $category ) {
			echo '<li><a href="' . esc_url( get_term_link( $category ) ) . '">' . esc_html( $category->name ) . '</a></li>';
		}
	}
	echo '</ul>';
}

function corralon_pro_footer_info_fallback() {
	echo '<ul class="footer-menu">';
	foreach ( array( 'nosotros' => __( 'Nosotros', 'corralon-pro' ), 'contacto' => __( 'Contacto', 'corralon-pro' ), 'envios' => __( 'Envíos', 'corralon-pro' ), 'preguntas-frecuentes' => __( 'Preguntas frecuentes', 'corralon-pro' ) ) as $slug => $label ) {
		echo '<li><a href="' . esc_url( corralon_pro_get_page_url( $slug ) ) . '">' . esc_html( $label ) . '</a></li>';
	}
	echo '</ul>';
}