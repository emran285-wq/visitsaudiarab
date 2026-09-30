<?php
/**
 * Plugin Name: VisitSaudiArab Core
 * Description: Custom post types, taxonomies, meta fields and schema output for VisitSaudiArab.com
 * Version: 1.0.0
 * Author: VisitSaudiArab
 * Text Domain: visitsaudiarab
 *
 * Install: upload this file (or the whole mu-plugins folder) into wp-content/mu-plugins/
 * so it always runs without needing manual activation. If you prefer a normal
 * plugin instead, put it in wp-content/plugins/visitsaudiarab-core/visitsaudiarab-core.php
 * and activate it from the Plugins screen.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* -------------------------------------------------------------------------
 * 1. CUSTOM POST TYPES
 * ---------------------------------------------------------------------- */

add_action( 'init', function () {

	// --- Destination ---
	register_post_type( 'destination', array(
		'labels' => array(
			'name'          => __( 'Destinations', 'visitsaudiarab' ),
			'singular_name' => __( 'Destination', 'visitsaudiarab' ),
			'add_new_item'  => __( 'Add New Destination', 'visitsaudiarab' ),
		),
		'public'        => true,
		'has_archive'   => true,
		'rewrite'       => array( 'slug' => 'destinations', 'with_front' => false ),
		'menu_icon'     => 'dashicons-location-alt',
		'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'custom-fields' ),
		'show_in_rest'  => true,
	) );

	// --- Attraction ---
	register_post_type( 'attraction', array(
		'labels' => array(
			'name'          => __( 'Attractions', 'visitsaudiarab' ),
			'singular_name' => __( 'Attraction', 'visitsaudiarab' ),
			'add_new_item'  => __( 'Add New Attraction', 'visitsaudiarab' ),
		),
		'public'        => true,
		'has_archive'   => true,
		'rewrite'       => array( 'slug' => 'attractions', 'with_front' => false ),
		'menu_icon'     => 'dashicons-camera',
		'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'custom-fields' ),
		'show_in_rest'  => true,
	) );

	// --- Event ---
	register_post_type( 'sa_event', array(
		'labels' => array(
			'name'          => __( 'Events', 'visitsaudiarab' ),
			'singular_name' => __( 'Event', 'visitsaudiarab' ),
			'add_new_item'  => __( 'Add New Event', 'visitsaudiarab' ),
		),
		'public'        => true,
		'has_archive'   => true,
		'rewrite'       => array( 'slug' => 'events', 'with_front' => false ),
		'menu_icon'     => 'dashicons-calendar-alt',
		'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'custom-fields' ),
		'show_in_rest'  => true,
	) );

	// --- Itinerary ---
	register_post_type( 'itinerary', array(
		'labels' => array(
			'name'          => __( 'Itineraries', 'visitsaudiarab' ),
			'singular_name' => __( 'Itinerary', 'visitsaudiarab' ),
		),
		'public'        => true,
		'has_archive'   => true,
		'rewrite'       => array( 'slug' => 'itineraries', 'with_front' => false ),
		'menu_icon'     => 'dashicons-media-document',
		'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'custom-fields' ),
		'show_in_rest'  => true,
	) );
} );

/* -------------------------------------------------------------------------
 * 2. TAXONOMIES
 * ---------------------------------------------------------------------- */

add_action( 'init', function () {

	// Region (Riyadh, Jeddah, AlUla...) shared across all content types
	register_taxonomy( 'region', array( 'destination', 'attraction', 'sa_event', 'itinerary', 'post' ), array(
		'labels'       => array( 'name' => __( 'Regions', 'visitsaudiarab' ) ),
		'public'       => true,
		'hierarchical' => true,
		'rewrite'      => array( 'slug' => 'region' ),
		'show_in_rest' => true,
	) );

	// Experience (Beach, Desert, Family, History, Adventure, Luxury...)
	register_taxonomy( 'experience', array( 'destination', 'attraction', 'sa_event', 'itinerary', 'post' ), array(
		'labels'       => array( 'name' => __( 'Experiences', 'visitsaudiarab' ) ),
		'public'       => true,
		'hierarchical' => false,
		'rewrite'      => array( 'slug' => 'experience' ),
		'show_in_rest' => true,
	) );

	// Attraction category (Heritage, Museum, Nature, Shopping...)
	register_taxonomy( 'attraction_category', array( 'attraction' ), array(
		'labels'       => array( 'name' => __( 'Attraction Categories', 'visitsaudiarab' ) ),
		'public'       => true,
		'hierarchical' => true,
		'rewrite'      => array( 'slug' => 'attraction-category' ),
		'show_in_rest' => true,
	) );
} );

/* -------------------------------------------------------------------------
 * 3. META BOXES — plain custom fields (no ACF dependency)
 * ---------------------------------------------------------------------- */

add_action( 'add_meta_boxes', function () {

	add_meta_box( 'vsa_destination_fields', __( 'Know Before You Go', 'visitsaudiarab' ),
		'vsa_render_destination_fields', 'destination', 'normal', 'high' );

	add_meta_box( 'vsa_attraction_fields', __( 'Attraction Details', 'visitsaudiarab' ),
		'vsa_render_attraction_fields', 'attraction', 'normal', 'high' );

	add_meta_box( 'vsa_event_fields', __( 'Event Details', 'visitsaudiarab' ),
		'vsa_render_event_fields', 'sa_event', 'normal', 'high' );

	add_meta_box( 'vsa_trust_fields', __( 'Editorial Trust Info', 'visitsaudiarab' ),
		'vsa_render_trust_fields', array( 'destination', 'attraction', 'sa_event', 'itinerary', 'post' ), 'side', 'default' );
} );

function vsa_field_row( $post, $key, $label, $type = 'text' ) {
	$value = get_post_meta( $post->ID, $key, true );
	echo '<p><label for="' . esc_attr( $key ) . '"><strong>' . esc_html( $label ) . '</strong></label><br>';
	if ( $type === 'textarea' ) {
		echo '<textarea style="width:100%" rows="2" name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '">' . esc_textarea( $value ) . '</textarea>';
	} elseif ( $type === 'checkbox' ) {
		echo '<input type="checkbox" name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" value="1" ' . checked( $value, '1', false ) . '> ' . esc_html__( 'Yes', 'visitsaudiarab' );
	} else {
		echo '<input type="' . esc_attr( $type ) . '" style="width:100%" name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '">';
	}
	echo '</p>';
}

function vsa_render_destination_fields( $post ) {
	wp_nonce_field( 'vsa_save_meta', 'vsa_meta_nonce' );
	vsa_field_row( $post, 'vsa_arabic_name', __( 'Arabic Name', 'visitsaudiarab' ) );
	vsa_field_row( $post, 'vsa_airport', __( 'Nearest Airport', 'visitsaudiarab' ) );
	vsa_field_row( $post, 'vsa_best_months', __( 'Best Months to Visit', 'visitsaudiarab' ) );
	vsa_field_row( $post, 'vsa_avg_trip_length', __( 'Average Trip Length', 'visitsaudiarab' ) );
	vsa_field_row( $post, 'vsa_typical_budget', __( 'Typical Budget (SAR)', 'visitsaudiarab' ) );
	vsa_field_row( $post, 'vsa_transport_difficulty', __( 'Transport Difficulty', 'visitsaudiarab' ) );
	vsa_field_row( $post, 'vsa_family_suitability', __( 'Family Suitability Notes', 'visitsaudiarab' ) );
	vsa_field_row( $post, 'vsa_lat', __( 'Latitude', 'visitsaudiarab' ) );
	vsa_field_row( $post, 'vsa_lng', __( 'Longitude', 'visitsaudiarab' ) );
}

function vsa_render_attraction_fields( $post ) {
	wp_nonce_field( 'vsa_save_meta', 'vsa_meta_nonce' );
	vsa_field_row( $post, 'vsa_arabic_name', __( 'Arabic Name', 'visitsaudiarab' ) );
	vsa_field_row( $post, 'vsa_address', __( 'Address', 'visitsaudiarab' ) );
	vsa_field_row( $post, 'vsa_lat', __( 'Latitude', 'visitsaudiarab' ) );
	vsa_field_row( $post, 'vsa_lng', __( 'Longitude', 'visitsaudiarab' ) );
	vsa_field_row( $post, 'vsa_price', __( 'Price / Ticket Info', 'visitsaudiarab' ) );
	vsa_field_row( $post, 'vsa_opening_hours', __( 'Opening Hours', 'visitsaudiarab' ) );
	vsa_field_row( $post, 'vsa_booking_url', __( 'Booking URL', 'visitsaudiarab' ), 'url' );
	vsa_field_row( $post, 'vsa_official_url', __( 'Official Website', 'visitsaudiarab' ), 'url' );
	vsa_field_row( $post, 'vsa_duration', __( 'Recommended Visit Duration', 'visitsaudiarab' ) );
	vsa_field_row( $post, 'vsa_family_friendly', __( 'Family Friendly', 'visitsaudiarab' ), 'checkbox' );
	vsa_field_row( $post, 'vsa_indoor_outdoor', __( 'Indoor / Outdoor', 'visitsaudiarab' ) );
	vsa_field_row( $post, 'vsa_last_verified', __( 'Last Verified (YYYY-MM-DD)', 'visitsaudiarab' ), 'date' );
}

function vsa_render_event_fields( $post ) {
	wp_nonce_field( 'vsa_save_meta', 'vsa_meta_nonce' );
	vsa_field_row( $post, 'vsa_venue', __( 'Venue', 'visitsaudiarab' ) );
	vsa_field_row( $post, 'vsa_start_date', __( 'Start Date (YYYY-MM-DD)', 'visitsaudiarab' ), 'date' );
	vsa_field_row( $post, 'vsa_end_date', __( 'End Date (YYYY-MM-DD)', 'visitsaudiarab' ), 'date' );
	vsa_field_row( $post, 'vsa_ticket_url', __( 'Ticket URL', 'visitsaudiarab' ), 'url' );
	vsa_field_row( $post, 'vsa_official_url', __( 'Official URL', 'visitsaudiarab' ), 'url' );
	vsa_field_row( $post, 'vsa_price_range', __( 'Price Range', 'visitsaudiarab' ) );
	vsa_field_row( $post, 'vsa_family_friendly', __( 'Family Friendly', 'visitsaudiarab' ), 'checkbox' );
}

function vsa_render_trust_fields( $post ) {
	wp_nonce_field( 'vsa_save_meta', 'vsa_meta_nonce' );
	vsa_field_row( $post, 'vsa_last_reviewed', __( 'Last Reviewed (YYYY-MM-DD)', 'visitsaudiarab' ), 'date' );
	vsa_field_row( $post, 'vsa_next_review_due', __( 'Next Review Due (YYYY-MM-DD)', 'visitsaudiarab' ), 'date' );
	vsa_field_row( $post, 'vsa_source_note', __( 'How info was obtained', 'visitsaudiarab' ), 'textarea' );
}

add_action( 'save_post', function ( $post_id ) {
	if ( ! isset( $_POST['vsa_meta_nonce'] ) || ! wp_verify_nonce( $_POST['vsa_meta_nonce'], 'vsa_save_meta' ) ) return;
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;

	$fields = array(
		'vsa_arabic_name', 'vsa_airport', 'vsa_best_months', 'vsa_avg_trip_length', 'vsa_typical_budget',
		'vsa_transport_difficulty', 'vsa_family_suitability', 'vsa_lat', 'vsa_lng', 'vsa_address', 'vsa_price',
		'vsa_opening_hours', 'vsa_booking_url', 'vsa_official_url', 'vsa_duration', 'vsa_indoor_outdoor',
		'vsa_last_verified', 'vsa_venue', 'vsa_start_date', 'vsa_end_date', 'vsa_ticket_url', 'vsa_price_range',
		'vsa_last_reviewed', 'vsa_next_review_due', 'vsa_source_note',
	);
	foreach ( $fields as $field ) {
		if ( isset( $_POST[ $field ] ) ) {
			update_post_meta( $post_id, $field, sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) );
		}
	}
	// Checkboxes need explicit handling (absent = unchecked)
	foreach ( array( 'vsa_family_friendly' ) as $cb ) {
		update_post_meta( $post_id, $cb, isset( $_POST[ $cb ] ) ? '1' : '' );
	}
} );

/* -------------------------------------------------------------------------
 * 4. SEARCH PAGES -> NOINDEX (per PRD indexation rules)
 * ---------------------------------------------------------------------- */

add_action( 'wp_head', function () {
	if ( is_search() ) {
		echo '<meta name="robots" content="noindex,follow">' . "\n";
	}
}, 1 );

/* -------------------------------------------------------------------------
 * 4b. REWRITE / CANONICAL HELPERS
 * ---------------------------------------------------------------------- */

/**
 * Flush rewrite rules when this mu-plugin is activated or updated.
 * WordPress normally requires a permalink save; this hook ensures the theme
 * and plugin rewrite slugs are registered before rules are generated.
 */
add_action( 'init', function () {
	if ( false === get_option( 'vsa_rewrite_rules_flushed' ) ) {
		flush_rewrite_rules();
		update_option( 'vsa_rewrite_rules_flushed', true );
	}
}, 99 );

/**
 * Redirect any leftover `?p=`, `?page_id=`, `?cat=`, `?tag=` or
 * `?post_type=...&p=` requests to their pretty permalink equivalents.
 * Only acts on safe GET/HEAD requests and never redirects POSTs.
 */
add_action( 'template_redirect', function () {
	if ( ! in_array( strtoupper( $_SERVER['REQUEST_METHOD'] ?? 'GET' ), array( 'GET', 'HEAD' ), true ) ) {
		return;
	}

	if ( is_404() && ! empty( $_GET['p'] ) ) {
		$post_id = absint( $_GET['p'] );
		$link    = $post_id ? get_permalink( $post_id ) : false;
		if ( $link && ! is_wp_error( $link ) ) {
			wp_safe_redirect( $link, 301 );
			exit;
		}
	}

	if ( is_404() && ! empty( $_GET['page_id'] ) ) {
		$post_id = absint( $_GET['page_id'] );
		$link    = $post_id ? get_permalink( $post_id ) : false;
		if ( $link && ! is_wp_error( $link ) ) {
			wp_safe_redirect( $link, 301 );
			exit;
		}
	}

	if ( is_404() && ! empty( $_GET['cat'] ) ) {
		$term = get_term( absint( $_GET['cat'] ), 'category' );
		if ( $term && ! is_wp_error( $term ) ) {
			$link = get_term_link( $term );
			if ( $link && ! is_wp_error( $link ) ) {
				wp_safe_redirect( $link, 301 );
				exit;
			}
		}
	}

	if ( is_404() && ! empty( $_GET['tag'] ) ) {
		$term = get_term_by( 'slug', sanitize_text_field( wp_unslash( $_GET['tag'] ) ), 'post_tag' );
		if ( $term && ! is_wp_error( $term ) ) {
			$link = get_term_link( $term );
			if ( $link && ! is_wp_error( $link ) ) {
				wp_safe_redirect( $link, 301 );
				exit;
			}
		}
	}
}, 1 );

/* -------------------------------------------------------------------------
 * 5. JSON-LD STRUCTURED DATA
 * ---------------------------------------------------------------------- */

add_action( 'wp_head', function () {

	if ( is_singular( 'attraction' ) ) {
		global $post;
		$lat = get_post_meta( $post->ID, 'vsa_lat', true );
		$lng = get_post_meta( $post->ID, 'vsa_lng', true );
		$data = array(
			'@context'    => 'https://schema.org',
			'@type'       => 'TouristAttraction',
			'name'        => get_the_title(),
			'description' => wp_strip_all_tags( get_the_excerpt() ),
			'url'         => get_permalink(),
		);
		if ( $lat && $lng ) {
			$data['geo'] = array(
				'@type'     => 'GeoCoordinates',
				'latitude'  => $lat,
				'longitude' => $lng,
			);
		}
		if ( has_post_thumbnail() ) {
			$data['image'] = get_the_post_thumbnail_url( $post, 'full' );
		}
		echo '<script type="application/ld+json">' . wp_json_encode( $data ) . '</script>' . "\n";
	}

	if ( is_singular( 'sa_event' ) ) {
		global $post;
		$data = array(
			'@context'    => 'https://schema.org',
			'@type'       => 'Event',
			'name'        => get_the_title(),
			'startDate'   => get_post_meta( $post->ID, 'vsa_start_date', true ),
			'endDate'     => get_post_meta( $post->ID, 'vsa_end_date', true ),
			'url'         => get_permalink(),
			'location'    => array(
				'@type' => 'Place',
				'name'  => get_post_meta( $post->ID, 'vsa_venue', true ),
			),
		);
		echo '<script type="application/ld+json">' . wp_json_encode( $data ) . '</script>' . "\n";
	}

	if ( is_singular( array( 'post', 'destination', 'itinerary' ) ) ) {
		global $post;
		$author_id = $post->post_author;
		$data = array(
			'@context'      => 'https://schema.org',
			'@type'         => 'Article',
			'headline'      => get_the_title(),
			'description'   => wp_strip_all_tags( get_the_excerpt() ),
			'datePublished' => get_the_date( 'c' ),
			'dateModified'  => get_the_modified_date( 'c' ),
			'author'        => array(
				'@type' => 'Person',
				'name'  => get_the_author_meta( 'display_name', $author_id ),
			),
			'mainEntityOfPage' => get_permalink(),
		);
		if ( has_post_thumbnail() ) {
			$data['image'] = get_the_post_thumbnail_url( $post, 'full' );
		}
		echo '<script type="application/ld+json">' . wp_json_encode( $data ) . '</script>' . "\n";
	}
}, 20 );

/* -------------------------------------------------------------------------
 * 6. IMAGE HANDLING — force descriptive alt fallback + lazy loading is
 *    handled by WP core since 5.5, nothing extra needed there.
 * ---------------------------------------------------------------------- */

add_filter( 'wp_get_attachment_image_attributes', function ( $attr, $attachment ) {
	if ( empty( $attr['alt'] ) ) {
		$attr['alt'] = get_the_title( $attachment->ID );
	}
	return $attr;
}, 10, 2 );
