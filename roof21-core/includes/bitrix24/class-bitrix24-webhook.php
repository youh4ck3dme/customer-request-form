<?php
/**
 * Bitrix24 Webhook Handler.
 *
 * @package Roof21\Core\Bitrix24
 */

namespace Roof21\Core\Bitrix24;

/**
 * Bitrix24 Webhook class.
 */
class Bitrix24Webhook {

	/**
	 * Sync engine.
	 *
	 * @var Bitrix24Sync
	 */
	private $sync;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->sync = new Bitrix24Sync();
	}

	/**
	 * Register REST API routes.
	 *
	 * @since 1.0.0
	 */
	public function register_routes() {
		register_rest_route( 'roof21/v1', '/webhook/bitrix24', array(
			'methods'             => 'POST',
			'callback'            => array( $this, 'handle_webhook' ),
			'permission_callback' => array( $this, 'validate_webhook' ),
		) );
	}

	/**
	 * Validate webhook request.
	 *
	 * @since 1.0.0
	 * @param WP_REST_Request $request Request object.
	 * @return bool True if valid.
	 */
	public function validate_webhook( $request ) {
		// Validate webhook signature or token
		$secret = get_option( 'roof21_bitrix24_webhook_secret', '' );

		if ( empty( $secret ) ) {
			// If no secret is set, allow all requests (not recommended for production)
			return true;
		}

		$signature = $request->get_header( 'X-Bitrix24-Signature' );

		if ( empty( $signature ) ) {
			return false;
		}

		// Verify signature
		$body = $request->get_body();
		$expected_signature = hash_hmac( 'sha256', $body, $secret );

		return hash_equals( $expected_signature, $signature );
	}

	/**
	 * Handle webhook request from Bitrix24.
	 *
	 * @since 1.0.0
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response Response.
	 */
	public function handle_webhook( $request ) {
		$params = $request->get_json_params();

		if ( empty( $params ) ) {
			return new \WP_REST_Response( array(
				'status'  => 'error',
				'message' => 'Invalid request',
			), 400 );
		}

		$event = $params['event'] ?? '';
		$data = $params['data'] ?? array();

		switch ( $event ) {
			case 'ONCRMDEALADD':
			case 'ONCRMDEALUPDATE':
				return $this->handle_deal_update( $data );

			default:
				return new \WP_REST_Response( array(
					'status'  => 'ignored',
					'message' => 'Event not handled',
				), 200 );
		}
	}

	/**
	 * Handle deal add/update event.
	 *
	 * @since 1.0.0
	 * @param array $data Event data.
	 * @return WP_REST_Response Response.
	 */
	private function handle_deal_update( $data ) {
		$deal_id = $data['FIELDS']['ID'] ?? null;

		if ( ! $deal_id ) {
			return new \WP_REST_Response( array(
				'status'  => 'error',
				'message' => 'No deal ID provided',
			), 400 );
		}

		// Get full deal data from API
		$api = new Bitrix24API();
		$deal_response = $api->get_deal( $deal_id );

		if ( is_wp_error( $deal_response ) ) {
			return new \WP_REST_Response( array(
				'status'  => 'error',
				'message' => $deal_response->get_error_message(),
			), 500 );
		}

		$deal = $deal_response['result'] ?? array();

		// Sync property
		$result = $this->sync->sync_property( $deal );

		if ( is_wp_error( $result ) ) {
			return new \WP_REST_Response( array(
				'status'  => 'error',
				'message' => $result->get_error_message(),
			), 500 );
		}

		return new \WP_REST_Response( array(
			'status'  => 'success',
			'message' => 'Property synced',
			'action'  => $result,
		), 200 );
	}
}
