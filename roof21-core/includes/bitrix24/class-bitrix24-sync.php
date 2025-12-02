<?php
/**
 * Bitrix24 Sync Engine.
 *
 * @package Roof21\Core\Bitrix24
 */

namespace Roof21\Core\Bitrix24;

/**
 * Bitrix24 Sync class.
 */
class Bitrix24Sync {

	/**
	 * API client.
	 *
	 * @var Bitrix24API
	 */
	private $api;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->api = new Bitrix24API();
	}

	/**
	 * Sync all properties from Bitrix24.
	 *
	 * @since 1.0.0
	 * @return array Sync results.
	 */
	public function sync_properties() {
		if ( ! get_option( 'roof21_sync_enabled', '1' ) ) {
			return array(
				'status'  => 'disabled',
				'message' => __( 'Sync is disabled in settings', 'roof21-core' ),
			);
		}

		// Start sync log
		$log_id = roof21_log_sync( 'properties', 'started' );

		$results = array(
			'added'   => 0,
			'updated' => 0,
			'failed'  => 0,
			'errors'  => array(),
		);

		// Get all deals from Bitrix24
		$start = 0;
		$more = true;

		while ( $more ) {
			$response = $this->api->get_deals( array(), $start );

			if ( is_wp_error( $response ) ) {
				$results['errors'][] = $response->get_error_message();
				roof21_log_sync( 'properties', 'failed', array(
					'message' => $response->get_error_message(),
				) );
				return $results;
			}

			if ( empty( $response['result'] ) ) {
				$more = false;
				break;
			}

			foreach ( $response['result'] as $deal ) {
				$result = $this->sync_property( $deal );

				if ( is_wp_error( $result ) ) {
					$results['failed']++;
					$results['errors'][] = sprintf(
						'Deal #%s: %s',
						$deal['ID'],
						$result->get_error_message()
					);
				} elseif ( 'created' === $result ) {
					$results['added']++;
				} elseif ( 'updated' === $result ) {
					$results['updated']++;
				}
			}

			// Check if there are more results
			if ( isset( $response['total'] ) && ( $start + 50 ) >= $response['total'] ) {
				$more = false;
			} else {
				$start += 50;
			}
		}

		// Complete sync log
		roof21_log_sync( 'properties', 'completed', array(
			'properties_added'   => $results['added'],
			'properties_updated' => $results['updated'],
			'properties_failed'  => $results['failed'],
			'message'            => implode( '; ', $results['errors'] ),
		) );

		do_action( 'roof21_after_property_sync', $results );

		return $results;
	}

	/**
	 * Sync single property from Bitrix24 deal.
	 *
	 * @since 1.0.0
	 * @param array $deal Deal data from Bitrix24.
	 * @return string|WP_Error 'created', 'updated', or error.
	 */
	public function sync_property( $deal ) {
		do_action( 'roof21_before_property_sync', $deal );

		// Check if property already exists
		$existing_id = $this->get_property_by_bitrix_id( $deal['ID'] );

		// Map deal to property data
		$post_data = array(
			'post_type'    => 'roof21_property',
			'post_status'  => 'publish',
			'post_title'   => sanitize_text_field( $deal['TITLE'] ?? 'Property #' . $deal['ID'] ),
			'post_content' => wp_kses_post( $deal['COMMENTS'] ?? '' ),
		);

		// Create or update property
		if ( $existing_id ) {
			$post_data['ID'] = $existing_id;
			$property_id = wp_update_post( $post_data );
			$action = 'updated';
		} else {
			$property_id = wp_insert_post( $post_data );
			$action = 'created';
		}

		if ( is_wp_error( $property_id ) || ! $property_id ) {
			return new \WP_Error( 'sync_failed', __( 'Failed to create/update property', 'roof21-core' ) );
		}

		// Update meta fields
		$meta_fields = $this->api->map_deal_to_property( $deal );
		foreach ( $meta_fields as $key => $value ) {
			update_post_meta( $property_id, $key, $value );
		}

		// Assign taxonomies
		$taxonomies = $this->api->get_deal_taxonomies( $deal );
		foreach ( $taxonomies as $taxonomy => $terms ) {
			wp_set_object_terms( $property_id, $terms, $taxonomy );
		}

		// Handle images
		if ( ! empty( $deal['UF_CRM_IMAGES'] ) ) {
			$this->sync_property_images( $property_id, $deal['UF_CRM_IMAGES'] );
		}

		do_action( 'roof21_property_' . $action, $property_id, $deal );

		return $action;
	}

	/**
	 * Get property by Bitrix24 deal ID.
	 *
	 * @since 1.0.0
	 * @param int $bitrix_id Bitrix24 deal ID.
	 * @return int|false Property post ID or false.
	 */
	private function get_property_by_bitrix_id( $bitrix_id ) {
		$args = array(
			'post_type'      => 'roof21_property',
			'posts_per_page' => 1,
			'meta_query'     => array(
				array(
					'key'   => '_roof21_bitrix_id',
					'value' => $bitrix_id,
				),
			),
			'fields'         => 'ids',
		);

		$properties = get_posts( $args );

		return ! empty( $properties ) ? $properties[0] : false;
	}

	/**
	 * Sync property images.
	 *
	 * @since 1.0.0
	 * @param int   $property_id Property post ID.
	 * @param mixed $images      Image data from Bitrix24.
	 * @return bool Success.
	 */
	private function sync_property_images( $property_id, $images ) {
		if ( ! is_array( $images ) ) {
			// Try to parse as JSON or comma-separated URLs
			$images = json_decode( $images, true );
			if ( ! is_array( $images ) ) {
				$images = array_map( 'trim', explode( ',', $images ) );
			}
		}

		$gallery_ids = array();
		$watermarked_ids = array();

		foreach ( $images as $image_url ) {
			if ( empty( $image_url ) ) {
				continue;
			}

			// Check if image already exists
			$existing_id = $this->get_attachment_by_url( $image_url, $property_id );

			if ( $existing_id ) {
				$gallery_ids[] = $existing_id;
				$watermarked_id = get_post_meta( $existing_id, '_roof21_watermarked_version', true );
				if ( $watermarked_id ) {
					$watermarked_ids[] = $watermarked_id;
				}
				continue;
			}

			// Download image
			$tmp_file = $this->api->download_file( $image_url );

			if ( is_wp_error( $tmp_file ) ) {
				continue;
			}

			// Upload to media library
			$file_array = array(
				'name'     => basename( $image_url ),
				'tmp_name' => $tmp_file,
			);

			$attachment_id = media_handle_sideload( $file_array, $property_id );

			if ( is_wp_error( $attachment_id ) ) {
				@unlink( $tmp_file );
				continue;
			}

			$gallery_ids[] = $attachment_id;

			// Watermarked version will be created by watermark processor hook
			$watermarked_id = get_post_meta( $attachment_id, '_roof21_watermarked_version', true );
			if ( $watermarked_id ) {
				$watermarked_ids[] = $watermarked_id;
			}
		}

		// Update gallery meta
		if ( ! empty( $gallery_ids ) ) {
			update_post_meta( $property_id, '_roof21_gallery', $gallery_ids );

			// Set first image as featured image
			if ( ! has_post_thumbnail( $property_id ) ) {
				set_post_thumbnail( $property_id, $gallery_ids[0] );
			}
		}

		if ( ! empty( $watermarked_ids ) ) {
			update_post_meta( $property_id, '_roof21_gallery_watermarked', $watermarked_ids );
		}

		return true;
	}

	/**
	 * Get attachment by URL.
	 *
	 * @since 1.0.0
	 * @param string $url         Image URL.
	 * @param int    $property_id Property post ID.
	 * @return int|false Attachment ID or false.
	 */
	private function get_attachment_by_url( $url, $property_id ) {
		$args = array(
			'post_type'      => 'attachment',
			'posts_per_page' => 1,
			'post_parent'    => $property_id,
			'meta_query'     => array(
				array(
					'key'     => '_roof21_original_url',
					'value'   => $url,
					'compare' => '=',
				),
			),
			'fields'         => 'ids',
		);

		$attachments = get_posts( $args );

		return ! empty( $attachments ) ? $attachments[0] : false;
	}

	/**
	 * Manual sync trigger (called from admin).
	 *
	 * @since 1.0.0
	 * @return array Sync results.
	 */
	public function manual_sync() {
		// Increase time limit for manual sync
		set_time_limit( 300 );

		return $this->sync_properties();
	}
}
