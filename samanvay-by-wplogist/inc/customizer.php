<?php
/**
 * Theme Customizer settings and styles.
 *
 * @package Samanvay
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function samanvay_customize_register( $wp_customize ) {
	$colors = array(
		'primary'   => array( '#2aa6a4', __( 'Primary color', 'samanvay' ) ),
		'secondary' => array( '#172d54', __( 'Secondary color', 'samanvay' ) ),
		'text'      => array( '#4a4a4a', __( 'Body text color', 'samanvay' ) ),
		'accent'    => array( '#ff5b32', __( 'Accent color', 'samanvay' ) ),
	);

	foreach ( $colors as $key => $color ) {
		$wp_customize->add_setting( 'samanvay_' . $key, array(
			'default'           => $color[0],
			'sanitize_callback' => 'sanitize_hex_color',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'samanvay_' . $key, array(
			'label'   => $color[1],
			'section' => 'colors',
		) ) );
	}

	$wp_customize->add_section( 'samanvay_typography', array(
		'title'       => __( 'Typography', 'samanvay' ),
		'description' => __( 'Choose site fonts and tune the default reading size.', 'samanvay' ),
		'priority'    => 31,
	) );

	$font_choices = array(
		'system'     => __( 'System Sans — clean and fast', 'samanvay' ),
		'arial'      => __( 'Arial', 'samanvay' ),
		'trebuchet'  => __( 'Trebuchet MS', 'samanvay' ),
		'verdana'    => __( 'Verdana', 'samanvay' ),
		'georgia'    => __( 'Georgia — classic serif', 'samanvay' ),
		'times'      => __( 'Times New Roman', 'samanvay' ),
		'devanagari' => __( 'Devanagari / Indian language stack', 'samanvay' ),
	);

	$wp_customize->add_setting( 'samanvay_body_font', array( 'default' => 'devanagari', 'sanitize_callback' => 'samanvay_sanitize_font' ) );
	$wp_customize->add_control( 'samanvay_body_font', array( 'label' => __( 'Body font', 'samanvay' ), 'section' => 'samanvay_typography', 'type' => 'select', 'choices' => $font_choices ) );
	$wp_customize->add_setting( 'samanvay_heading_font', array( 'default' => 'devanagari', 'sanitize_callback' => 'samanvay_sanitize_font' ) );
	$wp_customize->add_control( 'samanvay_heading_font', array( 'label' => __( 'Heading font', 'samanvay' ), 'section' => 'samanvay_typography', 'type' => 'select', 'choices' => $font_choices ) );
	$wp_customize->add_setting( 'samanvay_body_size', array( 'default' => 17, 'sanitize_callback' => 'samanvay_sanitize_body_size' ) );
	$wp_customize->add_control( 'samanvay_body_size', array( 'label' => __( 'Body font size (px)', 'samanvay' ), 'section' => 'samanvay_typography', 'type' => 'number', 'input_attrs' => array( 'min' => 14, 'max' => 22, 'step' => 1 ) ) );
	$wp_customize->add_setting( 'samanvay_line_height', array( 'default' => 1.7, 'sanitize_callback' => 'samanvay_sanitize_line_height' ) );
	$wp_customize->add_control( 'samanvay_line_height', array( 'label' => __( 'Body line height', 'samanvay' ), 'section' => 'samanvay_typography', 'type' => 'number', 'input_attrs' => array( 'min' => 1.3, 'max' => 2, 'step' => 0.1 ) ) );
}
add_action( 'customize_register', 'samanvay_customize_register' );

function samanvay_sanitize_font( $value ) {
	$allowed = array( 'system', 'arial', 'trebuchet', 'verdana', 'georgia', 'times', 'devanagari' );
	return in_array( $value, $allowed, true ) ? $value : 'system';
}

function samanvay_sanitize_body_size( $value ) {
	return min( 22, max( 14, absint( $value ) ) );
}

function samanvay_sanitize_line_height( $value ) {
	$value = (float) $value;
	return min( 2, max( 1.3, $value ) );
}

function samanvay_customizer_css() {
	$font_stacks = array(
		'system'     => '-apple-system, BlinkMacSystemFont, "Segoe UI", Arial, sans-serif',
		'arial'      => 'Arial, Helvetica, sans-serif',
		'trebuchet'  => '"Trebuchet MS", Arial, sans-serif',
		'verdana'    => 'Verdana, Geneva, sans-serif',
		'georgia'    => 'Georgia, "Times New Roman", serif',
		'times'      => '"Times New Roman", Times, serif',
		'devanagari' => '"Noto Sans Devanagari", "Mangal", "Kohinoor Devanagari", sans-serif',
	);
	$body_font    = samanvay_sanitize_font( get_theme_mod( 'samanvay_body_font', 'devanagari' ) );
	$heading_font = samanvay_sanitize_font( get_theme_mod( 'samanvay_heading_font', 'devanagari' ) );
	$values       = array(
		'--sam-primary'      => get_theme_mod( 'samanvay_primary', '#2aa6a4' ),
		'--sam-secondary'    => get_theme_mod( 'samanvay_secondary', '#172d54' ),
		'--sam-body-text'    => get_theme_mod( 'samanvay_text', '#4a4a4a' ),
		'--sam-accent'       => get_theme_mod( 'samanvay_accent', '#ff5b32' ),
		'--sam-font-body'    => $font_stacks[ $body_font ],
		'--sam-font-heading' => $font_stacks[ $heading_font ],
		'--sam-body-size'    => samanvay_sanitize_body_size( get_theme_mod( 'samanvay_body_size', 17 ) ) . 'px',
		'--sam-line-height'  => samanvay_sanitize_line_height( get_theme_mod( 'samanvay_line_height', 1.7 ) ),
	);
	$css = ':root{';
	foreach ( $values as $property => $value ) {
		$css .= $property . ':' . $value . ';';
	}
	$css .= '}';
	wp_add_inline_style( 'samanvay-style', $css );
}
add_action( 'wp_enqueue_scripts', 'samanvay_customizer_css', 20 );
