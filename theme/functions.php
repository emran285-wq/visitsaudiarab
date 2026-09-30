<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/* Theme setup */
add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'responsive-embeds' );
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'visitsaudiarab' ),
		'mobile'  => __( 'Mobile Menu', 'visitsaudiarab' ),
		'footer'  => __( 'Footer Menu', 'visitsaudiarab' ),
	) );
} );

/* Assets */
add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'vsa-style', get_stylesheet_uri(), array(), '1.0.0' );
}, 5 );

/* Favicons — shared helper used by front-end, login, and admin screens. */
function vsa_favicon_links() {
	static $version = '1';
	$dir = get_stylesheet_directory_uri() . '/assets/images';
	echo '<link rel="icon" type="image/x-icon" href="' . esc_url( $dir . '/favicon.ico?v=' . $version ) . '">' . "\n";
	echo '<link rel="icon" type="image/png" sizes="16x16" href="' . esc_url( $dir . '/favicon-16x16.png?v=' . $version ) . '">' . "\n";
	echo '<link rel="icon" type="image/png" sizes="32x32" href="' . esc_url( $dir . '/favicon-32x32.png?v=' . $version ) . '">' . "\n";
	echo '<link rel="apple-touch-icon" sizes="180x180" href="' . esc_url( $dir . '/apple-touch-icon.png?v=' . $version ) . '">' . "\n";
}

/* Front-end favicon is printed in header.php so it appears above wp_head(). */

/* Favicon on wp-login.php */
add_action( 'login_head', 'vsa_favicon_links' );

/* Favicon in wp-admin */
add_action( 'admin_head', 'vsa_favicon_links' );

/* Remove render-blocking emoji script + unnecessary head cruft (performance) */
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'rsd_link' );

/* Widget areas */
add_action( 'widgets_init', function () {
	register_sidebar( array(
		'name'          => __( 'Footer', 'visitsaudiarab' ),
		'id'            => 'footer-1',
		'before_widget' => '<div class="footer-widget">',
		'after_widget'  => '</div>',
		'before_title'  => '<h4>',
		'after_title'   => '</h4>',
	) );
} );

/* -------------------------------------------------------------------------
 * BILINGUAL (EN/AR) SUPPORT — lightweight, no plugin dependency.
 * Assumes site language is set per-post via a simple "language" taxonomy
 * or via WPML/Polylang if you later adopt one. This starter uses a custom
 * field `vsa_lang` (en|ar) plus a `vsa_translation_id` to link pairs.
 * If you adopt WPML/Polylang instead, remove this block and use their
 * hreflang output. NEVER run both systems at once.
 * ---------------------------------------------------------------------- */

function vsa_get_lang( $post_id = null ) {
	$post_id = $post_id ?: get_the_ID();
	$lang = get_post_meta( $post_id, 'vsa_lang', true );
	return $lang ?: 'en';
}

add_filter( 'body_class', function ( $classes ) {
	if ( is_singular() && vsa_get_lang() === 'ar' ) {
		$classes[] = 'rtl';
	}
	return $classes;
} );

add_action( 'wp_head', function () {
	if ( ! is_singular() ) return;
	$post_id = get_the_ID();
	$translation_id = get_post_meta( $post_id, 'vsa_translation_id', true );
	$lang = vsa_get_lang( $post_id );

	// Canonical tag for the current page.
	echo '<link rel="canonical" href="' . esc_url( get_permalink( $post_id ) ) . '" />' . "\n";

	// hreflang alternate URLs.
	echo '<link rel="alternate" hreflang="' . ( $lang === 'ar' ? 'ar-SA' : 'en' ) . '" href="' . esc_url( get_permalink( $post_id ) ) . '" />' . "\n";

	if ( $translation_id ) {
		$pair = get_posts( array(
			'post_type'      => get_post_type( $post_id ),
			'meta_key'       => 'vsa_translation_id',
			'meta_value'     => $translation_id,
			'exclude'        => array( $post_id ),
			'posts_per_page' => 1,
		) );
		if ( $pair ) {
			$other_lang = vsa_get_lang( $pair[0]->ID );
			echo '<link rel="alternate" hreflang="' . ( $other_lang === 'ar' ? 'ar-SA' : 'en' ) . '" href="' . esc_url( get_permalink( $pair[0]->ID ) ) . '" />' . "\n";
		}
	}
	echo '<link rel="alternate" hreflang="x-default" href="' . esc_url( home_url( '/' ) ) . '" />' . "\n";
}, 15 );

/**
 * Helper: get URL of the translated counterpart, for the language switcher.
 */
function vsa_get_translation_url() {
	$post_id = get_the_ID();
	$translation_id = get_post_meta( $post_id, 'vsa_translation_id', true );
	if ( ! $translation_id ) return home_url( vsa_get_lang( $post_id ) === 'en' ? '/ar/' : '/en/' );
	$pair = get_posts( array(
		'post_type'      => get_post_type( $post_id ),
		'meta_key'       => 'vsa_translation_id',
		'meta_value'     => $translation_id,
		'exclude'        => array( $post_id ),
		'posts_per_page' => 1,
	) );
	return $pair ? get_permalink( $pair[0]->ID ) : home_url( '/' );
}

/* -------------------------------------------------------------------------
 * BREADCRUMBS (used by template-parts/breadcrumbs.php)
 * ---------------------------------------------------------------------- */
function vsa_breadcrumbs() {
	echo '<nav class="vsa-breadcrumbs" aria-label="Breadcrumb"><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'visitsaudiarab' ) . '</a>';
	if ( is_singular( array( 'attraction', 'destination', 'sa_event', 'itinerary', 'post' ) ) ) {
		$terms = get_the_terms( get_the_ID(), 'region' );
		if ( $terms && ! is_wp_error( $terms ) ) {
			echo ' &rsaquo; <a href="' . esc_url( get_term_link( $terms[0] ) ) . '">' . esc_html( $terms[0]->name ) . '</a>';
		}
		echo ' &rsaquo; ' . esc_html( get_the_title() );
	}
	echo '</nav>';

	// BreadcrumbList schema
	$items = array(
		array( '@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => home_url( '/' ) ),
	);
	if ( is_singular() ) {
		$items[] = array( '@type' => 'ListItem', 'position' => 2, 'name' => get_the_title(), 'item' => get_permalink() );
	}
	echo '<script type="application/ld+json">' . wp_json_encode( array(
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => $items,
	) ) . '</script>';
}
