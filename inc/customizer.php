<?php
/**
 * Theme Customizer settings.
 *
 * @package Corralon_Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function corralon_pro_customize_register( $wp_customize ) {
	$wp_customize->add_panel(
		'corralon_pro_settings',
		array(
			'title'       => __( 'Corralón Pro', 'corralon-pro' ),
			'description' => __( 'Configuración del comercio, contacto y portada.', 'corralon-pro' ),
			'priority'    => 30,
		)
	);

	$wp_customize->add_section(
		'corralon_pro_colors',
		array(
			'title' => __( 'Colores del theme', 'corralon-pro' ),
			'panel' => 'corralon_pro_settings',
		)
	);

	foreach ( array( 'primary_color' => __( 'Color principal', 'corralon-pro' ), 'secondary_color' => __( 'Color secundario', 'corralon-pro' ), 'accent_color' => __( 'Color de acento', 'corralon-pro' ) ) as $setting => $label ) {
		$wp_customize->add_setting(
			$setting,
			array(
				'default'           => corralon_pro_default_setting( $setting ),
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				$setting,
				array(
					'label'   => $label,
					'section' => 'corralon_pro_colors',
				)
			)
		);
	}

	$wp_customize->add_section(
		'corralon_pro_contact',
		array(
			'title' => __( 'Datos de contacto', 'corralon-pro' ),
			'panel' => 'corralon_pro_settings',
		)
	);

	$contact_fields = array(
		'phone'              => __( 'Teléfono', 'corralon-pro' ),
		'whatsapp'           => __( 'WhatsApp (solo números con código de país)', 'corralon-pro' ),
		'email'              => __( 'Email', 'corralon-pro' ),
		'address'            => __( 'Dirección', 'corralon-pro' ),
		'hours'              => __( 'Horarios', 'corralon-pro' ),
		'topbar_text'        => __( 'Texto de barra superior', 'corralon-pro' ),
		'footer_description' => __( 'Descripción del footer', 'corralon-pro' ),
	);

	foreach ( $contact_fields as $setting => $label ) {
		$wp_customize->add_setting(
			$setting,
			array(
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			$setting,
			array(
				'label'   => $label,
				'section' => 'corralon_pro_contact',
				'type'    => 'text',
			)
		);
	}

	$wp_customize->add_section(
		'corralon_pro_home',
		array(
			'title' => __( 'Portada', 'corralon-pro' ),
			'panel' => 'corralon_pro_settings',
		)
	);

	$home_fields = array(
		'hero_title'       => __( 'Título principal', 'corralon-pro' ),
		'hero_text'        => __( 'Texto principal', 'corralon-pro' ),
		'hero_button_text' => __( 'Texto del botón', 'corralon-pro' ),
		'hero_button_url'  => __( 'URL del botón', 'corralon-pro' ),
		'promo_title'      => __( 'Título de promoción', 'corralon-pro' ),
		'promo_text'       => __( 'Texto de promoción', 'corralon-pro' ),
	);

	foreach ( $home_fields as $setting => $label ) {
		$wp_customize->add_setting(
			$setting,
			array(
				'default'           => corralon_pro_default_setting( $setting ),
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			$setting,
			array(
				'label'   => $label,
				'section' => 'corralon_pro_home',
				'type'    => 'text',
			)
		);
	}

	$wp_customize->add_setting(
		'hero_image',
		array(
			'default'           => '',
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'hero_image',
			array(
				'label'     => __( 'Imagen principal', 'corralon-pro' ),
				'section'   => 'corralon_pro_home',
				'mime_type' => 'image',
			)
		)
	);
}
add_action( 'customize_register', 'corralon_pro_customize_register' );

function corralon_pro_default_setting( $setting ) {
	$defaults = array(
		'primary_color'   => '#174c3c',
		'secondary_color' => '#e9b949',
		'accent_color'    => '#d96c32',
		'hero_title'      => __( 'Todo para construir, reparar y mejorar', 'corralon-pro' ),
		'hero_text'       => __( 'Materiales, herramientas y asesoramiento para cada etapa de tu obra.', 'corralon-pro' ),
		'hero_button_text' => __( 'Ver productos', 'corralon-pro' ),
		'hero_button_url'  => '',
		'promo_title'     => __( 'Soluciones para tu obra', 'corralon-pro' ),
		'promo_text'      => __( 'Encontrá materiales, herramientas y entregas coordinadas en un solo lugar.', 'corralon-pro' ),
	);
	return isset( $defaults[ $setting ] ) ? $defaults[ $setting ] : '';
}

function corralon_pro_customizer_css() {
	$primary   = get_theme_mod( 'primary_color', '#174c3c' );
	$secondary = get_theme_mod( 'secondary_color', '#e9b949' );
	$accent    = get_theme_mod( 'accent_color', '#d96c32' );
	?>
	<style id="corralon-pro-customizer-css">
		:root {
			--primary-color: <?php echo esc_html( $primary ); ?>;
			--primary-dark: <?php echo esc_html( corralon_pro_darken_color( $primary, 18 ) ); ?>;
			--secondary-color: <?php echo esc_html( $secondary ); ?>;
			--accent-color: <?php echo esc_html( $accent ); ?>;
		}
	</style>
	<?php
}
add_action( 'wp_head', 'corralon_pro_customizer_css' );

function corralon_pro_darken_color( $hex, $percent ) {
	$hex = ltrim( (string) $hex, '#' );
	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}
	$rgb = array_map( 'hexdec', str_split( $hex, 2 ) );
	$rgb = array_map(
		static function ( $value ) use ( $percent ) {
			return max( 0, round( $value * ( 1 - ( $percent / 100 ) ) ) );
		},
		$rgb
	);
	return sprintf( '#%02x%02x%02x', $rgb[0], $rgb[1], $rgb[2] );
}