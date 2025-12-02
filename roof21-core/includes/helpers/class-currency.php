<?php
/**
 * Currency Helper.
 *
 * @package Roof21\Core\Helpers
 */

namespace Roof21\Core\Helpers;

/**
 * Currency class.
 */
class Currency {

	/**
	 * Update exchange rates from external API.
	 *
	 * @since 1.0.0
	 * @return bool|WP_Error True on success, error on failure.
	 */
	public function update_rates() {
		$api_key = get_option( 'roof21_exchange_rate_api_key', '' );

		if ( empty( $api_key ) ) {
			// Use manual rates if no API key
			return false;
		}

		// Using exchangerate-api.io as example (free tier available)
		$api_url = sprintf( 'https://v6.exchangerate-api.com/v6/%s/latest/THB', $api_key );

		$response = wp_remote_get( $api_url, array( 'timeout' => 15 ) );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( empty( $data['result'] ) || 'success' !== $data['result'] ) {
			return new \WP_Error( 'api_error', __( 'Failed to fetch exchange rates', 'roof21-core' ) );
		}

		$rates = array(
			'THB' => 1, // Base currency
			'USD' => floatval( $data['conversion_rates']['USD'] ?? 0.028 ),
			'EUR' => floatval( $data['conversion_rates']['EUR'] ?? 0.026 ),
		);

		update_option( 'roof21_exchange_rates', $rates );
		update_option( 'roof21_exchange_rates_updated', time() );

		return true;
	}

	/**
	 * Get current exchange rates.
	 *
	 * @since 1.0.0
	 * @return array Exchange rates.
	 */
	public function get_rates() {
		return get_option( 'roof21_exchange_rates', array(
			'THB' => 1,
			'USD' => 0.028,
			'EUR' => 0.026,
		) );
	}

	/**
	 * Convert amount between currencies.
	 *
	 * @since 1.0.0
	 * @param float  $amount   Amount to convert.
	 * @param string $from     From currency code.
	 * @param string $to       To currency code.
	 * @return float Converted amount.
	 */
	public function convert( $amount, $from, $to ) {
		if ( $from === $to ) {
			return $amount;
		}

		$rates = $this->get_rates();

		if ( ! isset( $rates[ $from ] ) || ! isset( $rates[ $to ] ) ) {
			return $amount;
		}

		// Convert to THB first, then to target currency
		$thb_amount = $amount / $rates[ $from ];
		return $thb_amount * $rates[ $to ];
	}

	/**
	 * Get supported currencies.
	 *
	 * @since 1.0.0
	 * @return array Currency codes and names.
	 */
	public function get_supported_currencies() {
		return array(
			'THB' => __( 'Thai Baht', 'roof21-core' ),
			'USD' => __( 'US Dollar', 'roof21-core' ),
			'EUR' => __( 'Euro', 'roof21-core' ),
		);
	}

	/**
	 * Get currency symbol.
	 *
	 * @since 1.0.0
	 * @param string $currency Currency code.
	 * @return string Currency symbol.
	 */
	public function get_symbol( $currency ) {
		$symbols = array(
			'THB' => '฿',
			'USD' => '$',
			'EUR' => '€',
		);

		return $symbols[ $currency ] ?? $currency;
	}
}
