<?php
/**
 * Bitrix24 API Client.
 *
 * @package Roof21\Core\Bitrix24
 */

namespace Roof21\Core\Bitrix24;

/**
 * Bitrix24 API class.
 */
class Bitrix24API {

	/**
	 * Webhook URL.
	 *
	 * @var string
	 */
	private $webhook_url;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->webhook_url = get_option( 'roof21_bitrix24_webhook_url', '' );
	}

	/**
	 * Make API request to Bitrix24.
	 *
	 * @since 1.0.0
	 * @param string $method API method name.
	 * @param array  $params Parameters.
	 * @return array|WP_Error API response or error.
	 */
	public function request( $method, $params = array() ) {
		if ( empty( $this->webhook_url ) ) {
			return new \WP_Error( 'no_webhook', __( 'Bitrix24 webhook URL not configured', 'roof21-core' ) );
		}

		$url = trailingslashit( $this->webhook_url ) . $method . '.json';

		$response = wp_remote_post( $url, array(
			'body'    => $params,
			'timeout' => 30,
		) );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( json_last_error() !== JSON_ERROR_NONE ) {
			return new \WP_Error( 'invalid_json', __( 'Invalid JSON response from Bitrix24', 'roof21-core' ) );
		}

		if ( isset( $data['error'] ) ) {
			return new \WP_Error( 'bitrix24_error', $data['error_description'] ?? $data['error'] );
		}

		return $data;
	}

	/**
	 * Get all deals (properties).
	 *
	 * @since 1.0.0
	 * @param array $filter Filter parameters.
	 * @param int   $start  Starting index for pagination.
	 * @return array|WP_Error Deals array or error.
	 */
	public function get_deals( $filter = array(), $start = 0 ) {
		$params = array(
			'filter' => $filter,
			'select' => array( '*', 'UF_*' ), // Get all fields including custom fields
			'start'  => $start,
		);

		return $this->request( 'crm.deal.list', $params );
	}

	/**
	 * Get single deal by ID.
	 *
	 * @since 1.0.0
	 * @param int $deal_id Deal ID.
	 * @return array|WP_Error Deal data or error.
	 */
	public function get_deal( $deal_id ) {
		$params = array(
			'ID' => $deal_id,
		);

		return $this->request( 'crm.deal.get', $params );
	}

	/**
	 * Create contact in Bitrix24.
	 *
	 * @since 1.0.0
	 * @param array $fields Contact fields.
	 * @return array|WP_Error Contact ID or error.
	 */
	public function create_contact( $fields ) {
		$params = array(
			'fields' => $fields,
		);

		return $this->request( 'crm.contact.add', $params );
	}

	/**
	 * Create lead in Bitrix24.
	 *
	 * @since 1.0.0
	 * @param array $fields Lead fields.
	 * @return array|WP_Error Lead ID or error.
	 */
	public function create_lead( $fields ) {
		$params = array(
			'fields' => $fields,
		);

		return $this->request( 'crm.lead.add', $params );
	}

	/**
	 * Create deal in Bitrix24.
	 *
	 * @since 1.0.0
	 * @param array $fields Deal fields.
	 * @return array|WP_Error Deal ID or error.
	 */
	public function create_deal( $fields ) {
		$params = array(
			'fields' => $fields,
		);

		return $this->request( 'crm.deal.add', $params );
	}

	/**
	 * Get deal files.
	 *
	 * @since 1.0.0
	 * @param int $deal_id Deal ID.
	 * @return array|WP_Error Files array or error.
	 */
	public function get_deal_files( $deal_id ) {
		// Bitrix24 stores files in custom fields or related entities
		// This is a placeholder - actual implementation depends on how files are stored
		return array();
	}

	/**
	 * Download file from Bitrix24.
	 *
	 * @since 1.0.0
	 * @param string $file_url File URL.
	 * @return string|WP_Error Local file path or error.
	 */
	public function download_file( $file_url ) {
		$tmp_file = download_url( $file_url );

		if ( is_wp_error( $tmp_file ) ) {
			return $tmp_file;
		}

		return $tmp_file;
	}

	/**
	 * Map Bitrix24 deal to property meta fields.
	 *
	 * @since 1.0.0
	 * @param array $deal Deal data from Bitrix24.
	 * @return array Mapped meta fields.
	 */
	public function map_deal_to_property( $deal ) {
		// This mapping depends on your Bitrix24 custom field configuration
		// Adjust field names based on your setup

		$mapped = array(
			'_roof21_bitrix_id'          => $deal['ID'] ?? '',
			'_roof21_reference_code'     => $deal['UF_CRM_REFERENCE'] ?? $deal['ID'] ?? '',
			'_roof21_price_thb'          => floatval( $deal['OPPORTUNITY'] ?? 0 ),
			'_roof21_beds'               => intval( $deal['UF_CRM_BEDS'] ?? 0 ),
			'_roof21_baths'              => intval( $deal['UF_CRM_BATHS'] ?? 0 ),
			'_roof21_living_area'        => floatval( $deal['UF_CRM_LIVING_AREA'] ?? 0 ),
			'_roof21_land_area'          => floatval( $deal['UF_CRM_LAND_AREA'] ?? 0 ),
			'_roof21_project_name'       => sanitize_text_field( $deal['UF_CRM_PROJECT'] ?? '' ),
			'_roof21_availability_start' => sanitize_text_field( $deal['UF_CRM_AVAIL_START'] ?? '' ),
			'_roof21_availability_end'   => sanitize_text_field( $deal['UF_CRM_AVAIL_END'] ?? '' ),
			'_roof21_featured'           => ! empty( $deal['UF_CRM_FEATURED'] ) ? '1' : '',
			'_roof21_proppit_project'    => sanitize_text_field( $deal['UF_CRM_PROPPIT'] ?? '' ),
			'_roof21_lat'                => floatval( $deal['UF_CRM_LAT'] ?? 0 ),
			'_roof21_lng'                => floatval( $deal['UF_CRM_LNG'] ?? 0 ),
		);

		// Calculate other currency prices
		$price_thb = $mapped['_roof21_price_thb'];
		$mapped['_roof21_price_usd'] = roof21_convert_price( $price_thb, 'USD' );
		$mapped['_roof21_price_eur'] = roof21_convert_price( $price_thb, 'EUR' );

		// Filter for custom mapping
		return apply_filters( 'roof21_property_meta_mapping', $mapped, $deal );
	}

	/**
	 * Get deal taxonomies (location, property type, etc.).
	 *
	 * @since 1.0.0
	 * @param array $deal Deal data from Bitrix24.
	 * @return array Taxonomy terms to assign.
	 */
	public function get_deal_taxonomies( $deal ) {
		// Map Bitrix24 fields to WordPress taxonomies
		// Adjust based on your Bitrix24 setup

		$taxonomies = array();

		// Location
		if ( ! empty( $deal['UF_CRM_LOCATION'] ) ) {
			$taxonomies['roof21_location'] = array( sanitize_text_field( $deal['UF_CRM_LOCATION'] ) );
		}

		// Property Type
		if ( ! empty( $deal['UF_CRM_PROPERTY_TYPE'] ) ) {
			$taxonomies['roof21_property_type'] = array( sanitize_text_field( $deal['UF_CRM_PROPERTY_TYPE'] ) );
		}

		// Ownership Type
		if ( ! empty( $deal['UF_CRM_OWNERSHIP'] ) ) {
			$taxonomies['roof21_ownership_type'] = array( sanitize_text_field( $deal['UF_CRM_OWNERSHIP'] ) );
		}

		// Listing Type (For Sale / For Rent)
		if ( ! empty( $deal['UF_CRM_LISTING_TYPE'] ) ) {
			$taxonomies['roof21_listing_type'] = array( sanitize_text_field( $deal['UF_CRM_LISTING_TYPE'] ) );
		}

		// Country (for international properties)
		if ( ! empty( $deal['UF_CRM_COUNTRY'] ) ) {
			$taxonomies['roof21_country'] = array( sanitize_text_field( $deal['UF_CRM_COUNTRY'] ) );
		}

		// Features (multiple)
		if ( ! empty( $deal['UF_CRM_FEATURES'] ) ) {
			$features = is_array( $deal['UF_CRM_FEATURES'] ) ? $deal['UF_CRM_FEATURES'] : explode( ',', $deal['UF_CRM_FEATURES'] );
			$taxonomies['roof21_feature'] = array_map( 'sanitize_text_field', $features );
		}

		return $taxonomies;
	}

	/**
	 * Test API connection.
	 *
	 * @since 1.0.0
	 * @return bool|WP_Error True on success, error on failure.
	 */
	public function test_connection() {
		$response = $this->request( 'crm.deal.list', array( 'start' => 0, 'select' => array( 'ID' ) ) );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		return true;
	}
}
