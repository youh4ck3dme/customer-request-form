<?php
/**
 * Global helper functions.
 *
 * @package Roof21\Core
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Get current currency code.
 *
 * @since 1.0.0
 * @return string Currency code (THB, USD, EUR)
 */
function roof21_get_current_currency() {
	if ( isset( $_COOKIE['roof21_currency'] ) ) {
		$currency = sanitize_text_field( $_COOKIE['roof21_currency'] );
		$supported = get_option( 'roof21_supported_currencies', array( 'THB', 'USD', 'EUR' ) );
		if ( in_array( $currency, $supported, true ) ) {
			return $currency;
		}
	}
	return get_option( 'roof21_default_currency', 'THB' );
}

/**
 * Get current language code.
 *
 * @since 1.0.0
 * @return string Language code (en, sk)
 */
function roof21_get_current_language() {
	// Check if Polylang or WPML is active
	if ( function_exists( 'pll_current_language' ) ) {
		return pll_current_language();
	}

	if ( function_exists( 'wpml_get_current_language' ) ) {
		return wpml_get_current_language();
	}

	// Fallback to WordPress locale
	$locale = get_locale();
	return substr( $locale, 0, 2 );
}

/**
 * Format price with currency.
 *
 * @since 1.0.0
 * @param float  $price    Price value.
 * @param string $currency Currency code.
 * @return string Formatted price.
 */
function roof21_format_price( $price, $currency = null ) {
	if ( is_null( $currency ) ) {
		$currency = roof21_get_current_currency();
	}

	$price = floatval( $price );

	$symbols = array(
		'THB' => '฿',
		'USD' => '$',
		'EUR' => '€',
	);

	$symbol = isset( $symbols[ $currency ] ) ? $symbols[ $currency ] : $currency . ' ';

	// Format number with thousand separators
	$formatted = number_format( $price, 0, '.', ',' );

	return $symbol . $formatted;
}

/**
 * Convert price to target currency.
 *
 * @since 1.0.0
 * @param float  $price         Price in THB.
 * @param string $target_currency Target currency code.
 * @return float Converted price.
 */
function roof21_convert_price( $price, $target_currency ) {
	$rates = get_option( 'roof21_exchange_rates', array(
		'THB' => 1,
		'USD' => 0.028,
		'EUR' => 0.026,
	) );

	if ( ! isset( $rates[ $target_currency ] ) ) {
		return $price;
	}

	return $price * $rates[ $target_currency ];
}

/**
 * Get property price in current currency.
 *
 * @since 1.0.0
 * @param int    $property_id Property post ID.
 * @param string $currency    Currency code (optional).
 * @return float Price in target currency.
 */
function roof21_get_property_price( $property_id, $currency = null ) {
	if ( is_null( $currency ) ) {
		$currency = roof21_get_current_currency();
	}

	// Check if we have pre-converted price
	$meta_key = '_roof21_price_' . strtolower( $currency );
	$price = get_post_meta( $property_id, $meta_key, true );

	if ( $price ) {
		return floatval( $price );
	}

	// Fallback: convert from THB
	$price_thb = get_post_meta( $property_id, '_roof21_price_thb', true );
	if ( ! $price_thb ) {
		return 0;
	}

	return roof21_convert_price( floatval( $price_thb ), $currency );
}

/**
 * Get property gallery images.
 *
 * @since 1.0.0
 * @param int  $property_id  Property post ID.
 * @param bool $watermarked  Get watermarked versions.
 * @return array Array of image IDs.
 */
function roof21_get_property_gallery( $property_id, $watermarked = false ) {
	$meta_key = $watermarked ? '_roof21_gallery_watermarked' : '_roof21_gallery';
	$gallery = get_post_meta( $property_id, $meta_key, true );

	if ( ! is_array( $gallery ) ) {
		$gallery = array();
	}

	return $gallery;
}

/**
 * Get property meta with default.
 *
 * @since 1.0.0
 * @param int    $property_id Property post ID.
 * @param string $meta_key    Meta key.
 * @param mixed  $default     Default value.
 * @return mixed Meta value or default.
 */
function roof21_get_property_meta( $property_id, $meta_key, $default = '' ) {
	$value = get_post_meta( $property_id, $meta_key, true );
	return $value ? $value : $default;
}

/**
 * Check if property is featured.
 *
 * @since 1.0.0
 * @param int $property_id Property post ID.
 * @return bool True if featured.
 */
function roof21_is_property_featured( $property_id ) {
	return (bool) get_post_meta( $property_id, '_roof21_featured', true );
}

/**
 * Get property reference code.
 *
 * @since 1.0.0
 * @param int $property_id Property post ID.
 * @return string Reference code.
 */
function roof21_get_property_reference( $property_id ) {
	return roof21_get_property_meta( $property_id, '_roof21_reference_code', '' );
}

/**
 * Get property location term.
 *
 * @since 1.0.0
 * @param int $property_id Property post ID.
 * @return WP_Term|false Location term or false.
 */
function roof21_get_property_location( $property_id ) {
	$terms = get_the_terms( $property_id, 'roof21_location' );
	return is_array( $terms ) && count( $terms ) > 0 ? $terms[0] : false;
}

/**
 * Get property type term.
 *
 * @since 1.0.0
 * @param int $property_id Property post ID.
 * @return WP_Term|false Property type term or false.
 */
function roof21_get_property_type( $property_id ) {
	$terms = get_the_terms( $property_id, 'roof21_property_type' );
	return is_array( $terms ) && count( $terms ) > 0 ? $terms[0] : false;
}

/**
 * Get property ownership type term.
 *
 * @since 1.0.0
 * @param int $property_id Property post ID.
 * @return WP_Term|false Ownership type term or false.
 */
function roof21_get_property_ownership_type( $property_id ) {
	$terms = get_the_terms( $property_id, 'roof21_ownership_type' );
	return is_array( $terms ) && count( $terms ) > 0 ? $terms[0] : false;
}

/**
 * Check if property is available for rent on a specific date.
 *
 * @since 1.0.0
 * @param int    $property_id Property post ID.
 * @param string $date        Date to check (Y-m-d format).
 * @return bool True if available.
 */
function roof21_is_property_available( $property_id, $date ) {
	$availability_start = get_post_meta( $property_id, '_roof21_availability_start', true );
	$availability_end = get_post_meta( $property_id, '_roof21_availability_end', true );

	if ( ! $availability_start && ! $availability_end ) {
		return true; // No restrictions
	}

	$check_date = strtotime( $date );

	if ( $availability_start ) {
		$start = strtotime( $availability_start );
		if ( $check_date < $start ) {
			return false;
		}
	}

	if ( $availability_end ) {
		$end = strtotime( $availability_end );
		if ( $check_date > $end ) {
			return false;
		}
	}

	return true;
}

/**
 * Get similar properties.
 *
 * @since 1.0.0
 * @param int $property_id Property post ID.
 * @param int $limit       Number of similar properties to return.
 * @return array Array of post IDs.
 */
function roof21_get_similar_properties( $property_id, $limit = 3 ) {
	$location = roof21_get_property_location( $property_id );
	$property_type = roof21_get_property_type( $property_id );
	$beds = get_post_meta( $property_id, '_roof21_beds', true );
	$price_thb = get_post_meta( $property_id, '_roof21_price_thb', true );

	$args = array(
		'post_type'      => 'roof21_property',
		'posts_per_page' => $limit,
		'post__not_in'   => array( $property_id ),
		'tax_query'      => array(
			'relation' => 'AND',
		),
		'meta_query'     => array(
			'relation' => 'OR',
		),
	);

	// Match by location
	if ( $location ) {
		$args['tax_query'][] = array(
			'taxonomy' => 'roof21_location',
			'field'    => 'term_id',
			'terms'    => $location->term_id,
		);
	}

	// Match by property type
	if ( $property_type ) {
		$args['tax_query'][] = array(
			'taxonomy' => 'roof21_property_type',
			'field'    => 'term_id',
			'terms'    => $property_type->term_id,
		);
	}

	// Match by similar number of beds
	if ( $beds ) {
		$args['meta_query'][] = array(
			'key'     => '_roof21_beds',
			'value'   => array( max( 1, $beds - 1 ), $beds + 1 ),
			'type'    => 'NUMERIC',
			'compare' => 'BETWEEN',
		);
	}

	// Match by similar price range (±30%)
	if ( $price_thb ) {
		$min_price = $price_thb * 0.7;
		$max_price = $price_thb * 1.3;

		$args['meta_query'][] = array(
			'key'     => '_roof21_price_thb',
			'value'   => array( $min_price, $max_price ),
			'type'    => 'NUMERIC',
			'compare' => 'BETWEEN',
		);
	}

	$query = new WP_Query( $args );

	return wp_list_pluck( $query->posts, 'ID' );
}

/**
 * Log sync activity.
 *
 * @since 1.0.0
 * @param string $sync_type Type of sync (properties, developments, etc.).
 * @param string $status    Status (started, completed, failed).
 * @param array  $data      Additional data.
 * @return int|false Log ID or false on failure.
 */
function roof21_log_sync( $sync_type, $status, $data = array() ) {
	global $wpdb;

	$table = $wpdb->prefix . 'roof21_sync_log';

	$defaults = array(
		'properties_added'   => 0,
		'properties_updated' => 0,
		'properties_failed'  => 0,
		'message'            => '',
	);

	$data = wp_parse_args( $data, $defaults );

	$log_data = array(
		'sync_type'          => $sync_type,
		'status'             => $status,
		'properties_added'   => $data['properties_added'],
		'properties_updated' => $data['properties_updated'],
		'properties_failed'  => $data['properties_failed'],
		'message'            => $data['message'],
		'started_at'         => current_time( 'mysql' ),
	);

	if ( 'completed' === $status || 'failed' === $status ) {
		$log_data['completed_at'] = current_time( 'mysql' );
	}

	$result = $wpdb->insert( $table, $log_data );

	return $result ? $wpdb->insert_id : false;
}

/**
 * Get sync logs.
 *
 * @since 1.0.0
 * @param array $args Query arguments.
 * @return array Array of log entries.
 */
function roof21_get_sync_logs( $args = array() ) {
	global $wpdb;

	$table = $wpdb->prefix . 'roof21_sync_log';

	$defaults = array(
		'limit'     => 20,
		'offset'    => 0,
		'sync_type' => '',
		'status'    => '',
	);

	$args = wp_parse_args( $args, $defaults );

	$where = array( '1=1' );

	if ( ! empty( $args['sync_type'] ) ) {
		$where[] = $wpdb->prepare( 'sync_type = %s', $args['sync_type'] );
	}

	if ( ! empty( $args['status'] ) ) {
		$where[] = $wpdb->prepare( 'status = %s', $args['status'] );
	}

	$where_sql = implode( ' AND ', $where );

	$query = "SELECT * FROM $table WHERE $where_sql ORDER BY started_at DESC LIMIT %d OFFSET %d";

	return $wpdb->get_results( $wpdb->prepare( $query, $args['limit'], $args['offset'] ), ARRAY_A );
}

/**
 * Sanitize Bitrix24 field value.
 *
 * @since 1.0.0
 * @param mixed  $value Field value from Bitrix24.
 * @param string $type  Field type (text, number, date, etc.).
 * @return mixed Sanitized value.
 */
function roof21_sanitize_bitrix24_field( $value, $type = 'text' ) {
	if ( is_null( $value ) || '' === $value ) {
		return '';
	}

	switch ( $type ) {
		case 'number':
		case 'price':
			return floatval( $value );

		case 'integer':
			return intval( $value );

		case 'date':
			return sanitize_text_field( $value );

		case 'email':
			return sanitize_email( $value );

		case 'url':
			return esc_url_raw( $value );

		case 'textarea':
			return sanitize_textarea_field( $value );

		case 'text':
		default:
			return sanitize_text_field( $value );
	}
}
