<?php
/**
 * Theme Customizer settings.
 *
 * @package Elektron
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sanitizes a select value against its choices.
 *
 * @param string               $value   Submitted value.
 * @param WP_Customize_Setting $setting Setting instance.
 * @return string
 */
function elektron_sanitize_select( $value, $setting ) {
	$value   = sanitize_key( $value );
	$control = $setting->manager->get_control( $setting->id );
	return array_key_exists( $value, $control->choices ) ? $value : $setting->default;
}

/**
 * Sanitizes a checkbox.
 *
 * @param mixed $value Submitted value.
 * @return bool
 */
function elektron_sanitize_checkbox( $value ) {
	return (bool) $value;
}

/**
 * Sanitizes a URL so that only https addresses are kept.
 *
 * @param string $value Submitted value.
 * @return string
 */
function elektron_sanitize_https_url( $value ) {
	return esc_url_raw( trim( (string) $value ), array( 'https' ) );
}

/**
 * Registers Customizer sections, settings and controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 */
function elektron_customize_register( $wp_customize ) {
	$defaults = elektron_front_defaults();

	$wp_customize->get_setting( 'blogname' )->transport        = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->selective_refresh->add_partial(
			'blogname',
			array(
				'selector'        => '.site-title a',
				'render_callback' => static function () {
					bloginfo( 'name' );
				},
			)
		);
		$wp_customize->selective_refresh->add_partial(
			'blogdescription',
			array(
				'selector'        => '.site-description',
				'render_callback' => static function () {
					bloginfo( 'description' );
				},
			)
		);
	}

	$wp_customize->add_section(
		'elektron_layout',
		array(
			'title'    => __( 'Theme Options', 'elektron' ),
			'priority' => 30,
		)
	);

	$wp_customize->add_setting(
		'elektron_default_scheme',
		array(
			'default'           => 'dark',
			'sanitize_callback' => 'elektron_sanitize_select',
		)
	);
	$wp_customize->add_control(
		'elektron_default_scheme',
		array(
			'label'   => __( 'Default color scheme', 'elektron' ),
			'section' => 'elektron_layout',
			'type'    => 'select',
			'choices' => array(
				'dark'  => __( 'Dark', 'elektron' ),
				'light' => __( 'Light', 'elektron' ),
				'auto'  => __( 'Follow system setting', 'elektron' ),
			),
		)
	);

	$wp_customize->add_setting(
		'elektron_palette',
		array(
			'default'           => 'blue',
			'sanitize_callback' => 'elektron_sanitize_select',
		)
	);
	$wp_customize->add_control(
		'elektron_palette',
		array(
			'label'   => __( 'Color theme', 'elektron' ),
			'section' => 'elektron_layout',
			'type'    => 'select',
			'choices' => elektron_palettes(),
		)
	);

	$wp_customize->add_setting(
		'elektron_fx',
		array(
			'default'           => 'subtle',
			'sanitize_callback' => 'elektron_sanitize_select',
		)
	);
	$wp_customize->add_control(
		'elektron_fx',
		array(
			'label'       => __( 'Cyberpunk effects', 'elektron' ),
			'description' => __( 'Neon glow and HUD corners (Subtle), plus scanlines, grid floor, glitch headline, angular cards and the Orbitron font (Full). Visitors who prefer reduced motion see no animation.', 'elektron' ),
			'section'     => 'elektron_layout',
			'type'        => 'select',
			'choices'     => elektron_fx_levels(),
		)
	);

	$wp_customize->add_setting(
		'elektron_show_style_menu',
		array(
			'default'           => true,
			'sanitize_callback' => 'elektron_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'elektron_show_style_menu',
		array(
			'label'   => __( 'Let visitors choose the color theme and effects', 'elektron' ),
			'section' => 'elektron_layout',
			'type'    => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'elektron_show_hud',
		array(
			'default'           => true,
			'sanitize_callback' => 'elektron_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'elektron_show_hud',
		array(
			'label'       => __( 'Show the HUD status bar', 'elektron' ),
			'description' => __( 'A strip above the header with block height, hash rate, fee and adjustment progress. It only appears when live data is available.', 'elektron' ),
			'section'     => 'elektron_layout',
			'type'        => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'elektron_show_scheme_toggle',
		array(
			'default'           => true,
			'sanitize_callback' => 'elektron_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'elektron_show_scheme_toggle',
		array(
			'label'   => __( 'Show light/dark switch in the header', 'elektron' ),
			'section' => 'elektron_layout',
			'type'    => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'elektron_show_latest_posts',
		array(
			'default'           => true,
			'sanitize_callback' => 'elektron_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'elektron_show_latest_posts',
		array(
			'label'   => __( 'Show latest posts on the front page', 'elektron' ),
			'section' => 'elektron_layout',
			'type'    => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'elektron_footer_text',
		array(
			'default'           => '',
			'sanitize_callback' => 'wp_kses_post',
		)
	);
	$wp_customize->add_control(
		'elektron_footer_text',
		array(
			'label'       => __( 'Footer text', 'elektron' ),
			'description' => __( 'Leave empty to show the copyright line.', 'elektron' ),
			'section'     => 'elektron_layout',
			'type'        => 'textarea',
		)
	);

	$wp_customize->add_section(
		'elektron_front',
		array(
			'title'       => __( 'Front Page Hero', 'elektron' ),
			'description' => __( 'Texts shown on the front page.', 'elektron' ),
			'priority'    => 31,
		)
	);

	$text_fields = array(
		'eyebrow'         => array( __( 'Eyebrow', 'elektron' ), 'text', 'sanitize_text_field' ),
		'title'           => array( __( 'Headline', 'elektron' ), 'textarea', 'sanitize_text_field' ),
		'text'            => array( __( 'Description', 'elektron' ), 'textarea', 'sanitize_textarea_field' ),
		'primary_label'   => array( __( 'Primary button label', 'elektron' ), 'text', 'sanitize_text_field' ),
		'primary_url'     => array( __( 'Primary button link', 'elektron' ), 'url', 'esc_url_raw' ),
		'secondary_label' => array( __( 'Secondary button label', 'elektron' ), 'text', 'sanitize_text_field' ),
		'secondary_url'   => array( __( 'Secondary button link', 'elektron' ), 'url', 'esc_url_raw' ),
	);

	foreach ( $text_fields as $key => $field ) {
		$wp_customize->add_setting(
			'elektron_' . $key,
			array(
				'default'           => $defaults[ $key ],
				'sanitize_callback' => $field[2],
			)
		);
		$wp_customize->add_control(
			'elektron_' . $key,
			array(
				'label'   => $field[0],
				'section' => 'elektron_front',
				'type'    => $field[1],
			)
		);
	}

	$wp_customize->add_setting(
		'elektron_hero_effect',
		array(
			'default'           => true,
			'sanitize_callback' => 'elektron_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'elektron_hero_effect',
		array(
			'label'       => __( 'Animated 3D network background', 'elektron' ),
			'description' => __( 'Shows a three.js node and block chain effect behind the front page hero. It stays still for visitors who prefer reduced motion.', 'elektron' ),
			'section'     => 'elektron_front',
			'type'        => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'elektron_repo_url',
		array(
			'default'           => 'https://github.com/kutlusoy/elektron-net',
			'sanitize_callback' => 'elektron_sanitize_https_url',
		)
	);
	$wp_customize->add_control(
		'elektron_repo_url',
		array(
			'label'       => __( 'Source repository URL', 'elektron' ),
			'description' => __( 'Used for the source code links on the Protocol, Build and front pages. HTTPS only.', 'elektron' ),
			'section'     => 'elektron_layout',
			'type'        => 'url',
		)
	);

	$wp_customize->add_section(
		'elektron_live',
		array(
			'title'       => __( 'Live Network Data', 'elektron' ),
			'description' => __( 'Shows block height, difficulty retarget progress and the fastest fee in the overview card. The data is requested by your server from a public block explorer API, never by the visitor, and cached for one minute.', 'elektron' ),
			'priority'    => 33,
		)
	);

	if ( elektron_plugin_active() ) {
		$wp_customize->get_section( 'elektron_live' )->description = wp_kses_post(
			sprintf(
				/* translators: %s: link to the plugin settings page */
				__( 'Live data is provided by the Elektron Live Blocks plugin, which is active. Change the explorer address and cache time in its <a href="%s">settings</a>.', 'elektron' ),
				esc_url( admin_url( 'options-general.php?page=elektron-live-blocks' ) )
			)
		);
	} else {
		$wp_customize->add_setting(
			'elektron_live_enabled',
			array(
				'default'           => false,
				'sanitize_callback' => 'elektron_sanitize_checkbox',
			)
		);
		$wp_customize->add_control(
			'elektron_live_enabled',
			array(
				'label'   => __( 'Enable live data', 'elektron' ),
				'section' => 'elektron_live',
				'type'    => 'checkbox',
			)
		);

		$wp_customize->add_setting(
			'elektron_live_api',
			array(
				'default'           => '',
				'sanitize_callback' => 'elektron_sanitize_https_url',
			)
		);
		$wp_customize->add_control(
			'elektron_live_api',
			array(
				'label'       => __( 'Explorer API base URL', 'elektron' ),
				'description' => __( 'HTTPS only, for example https://mempool.elektron-net.org/api/v1', 'elektron' ),
				'section'     => 'elektron_live',
				'type'        => 'url',
			)
		);
	}

	$wp_customize->add_section(
		'elektron_features',
		array(
			'title'    => __( 'Front Page Features', 'elektron' ),
			'priority' => 34,
		)
	);

	$feature_fields = array(
		'features_title' => array( __( 'Section title', 'elektron' ), 'text' ),
	);
	for ( $i = 1; $i <= 3; $i++ ) {
		/* translators: %d: feature number */
		$feature_fields[ 'feature_' . $i . '_title' ] = array( sprintf( __( 'Feature %d title', 'elektron' ), $i ), 'text' );
		/* translators: %d: feature number */
		$feature_fields[ 'feature_' . $i . '_text' ] = array( sprintf( __( 'Feature %d text', 'elektron' ), $i ), 'textarea' );
	}

	foreach ( $feature_fields as $key => $field ) {
		$wp_customize->add_setting(
			'elektron_' . $key,
			array(
				'default'           => $defaults[ $key ],
				'sanitize_callback' => 'textarea' === $field[1] ? 'sanitize_textarea_field' : 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			'elektron_' . $key,
			array(
				'label'   => $field[0],
				'section' => 'elektron_features',
				'type'    => $field[1],
			)
		);
	}

	$wp_customize->add_section(
		'elektron_stats',
		array(
			'title'    => __( 'Front Page Overview Card', 'elektron' ),
			'priority' => 32,
		)
	);

	$stat_fields = array(
		'stats_title' => __( 'Card title', 'elektron' ),
		'stats_note'  => __( 'Footnote', 'elektron' ),
	);
	for ( $i = 1; $i <= 4; $i++ ) {
		/* translators: %d: stat number */
		$stat_fields[ 'stat_' . $i . '_value' ] = sprintf( __( 'Value %d', 'elektron' ), $i );
		/* translators: %d: stat number */
		$stat_fields[ 'stat_' . $i . '_label' ] = sprintf( __( 'Label %d', 'elektron' ), $i );
	}

	foreach ( $stat_fields as $key => $label ) {
		$wp_customize->add_setting(
			'elektron_' . $key,
			array(
				'default'           => $defaults[ $key ],
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			'elektron_' . $key,
			array(
				'label'   => $label,
				'section' => 'elektron_stats',
				'type'    => 'text',
			)
		);
	}
}
add_action( 'customize_register', 'elektron_customize_register' );

/**
 * Adds the default color scheme as an attribute on the html element.
 *
 * @param string $output Language attributes.
 * @return string
 */
function elektron_html_scheme_attribute( $output ) {
	$scheme = get_theme_mod( 'elektron_default_scheme', 'dark' );
	if ( in_array( $scheme, array( 'dark', 'light', 'auto' ), true ) ) {
		$output .= ' data-default-scheme="' . esc_attr( $scheme ) . '"';
	}

	$palette = get_theme_mod( 'elektron_palette', 'blue' );
	$output .= ' data-palette="' . esc_attr( array_key_exists( $palette, elektron_palettes() ) ? $palette : 'blue' ) . '"';

	$fx      = get_theme_mod( 'elektron_fx', 'subtle' );
	$output .= ' data-fx="' . esc_attr( array_key_exists( $fx, elektron_fx_levels() ) ? $fx : 'subtle' ) . '"';
	return $output;
}
add_filter( 'language_attributes', 'elektron_html_scheme_attribute' );
