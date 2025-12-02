<?php
/**
 * Watermark Processor.
 *
 * @package Roof21\Core\Watermark
 */

namespace Roof21\Core\Watermark;

/**
 * Watermark Processor class.
 */
class WatermarkProcessor {

	/**
	 * Process uploaded image and create watermarked version.
	 *
	 * @since 1.0.0
	 * @param array $upload Upload data.
	 * @return array Modified upload data.
	 */
	public function process_upload( $upload ) {
		if ( ! get_option( 'roof21_watermark_enabled', '1' ) ) {
			return $upload;
		}

		// Only process images
		if ( ! isset( $upload['type'] ) || strpos( $upload['type'], 'image/' ) !== 0 ) {
			return $upload;
		}

		// Skip if this is already a watermarked image
		if ( strpos( $upload['file'], '-watermarked' ) !== false ) {
			return $upload;
		}

		do_action( 'roof21_before_watermark', $upload );

		// Create watermarked version
		$watermarked_file = $this->create_watermarked_image( $upload['file'] );

		if ( ! is_wp_error( $watermarked_file ) ) {
			// Store relationship between original and watermarked versions
			// This will be saved to attachment meta after the attachment is created
			$upload['watermarked_file'] = $watermarked_file;

			add_filter( 'wp_generate_attachment_metadata', array( $this, 'save_watermark_relationship' ), 10, 2 );
		}

		do_action( 'roof21_after_watermark', $upload, $watermarked_file );

		return $upload;
	}

	/**
	 * Create watermarked version of image.
	 *
	 * @since 1.0.0
	 * @param string $file_path Original file path.
	 * @return string|WP_Error Watermarked file path or error.
	 */
	public function create_watermarked_image( $file_path ) {
		// Get watermark settings
		$watermark_logo = get_option( 'roof21_watermark_logo', ROOF21_CORE_PATH . 'assets/images/watermark-logo.png' );
		$position = get_option( 'roof21_watermark_position', 'bottom-right' );
		$opacity = intval( get_option( 'roof21_watermark_opacity', 70 ) );
		$padding = intval( get_option( 'roof21_watermark_padding', 20 ) );

		if ( ! file_exists( $watermark_logo ) ) {
			return new \WP_Error( 'no_watermark', __( 'Watermark logo file not found', 'roof21-core' ) );
		}

		// Load image editor
		$editor = wp_get_image_editor( $file_path );

		if ( is_wp_error( $editor ) ) {
			return $editor;
		}

		// Load watermark
		$watermark_editor = wp_get_image_editor( $watermark_logo );

		if ( is_wp_error( $watermark_editor ) ) {
			return $watermark_editor;
		}

		// Get image sizes
		$image_size = $editor->get_size();
		$watermark_size = $watermark_editor->get_size();

		// Calculate watermark position
		$position_coords = $this->calculate_watermark_position(
			$image_size,
			$watermark_size,
			$position,
			$padding
		);

		// For GD library
		if ( method_exists( $editor, 'get_image' ) ) {
			$result = $this->apply_watermark_gd(
				$editor->get_image(),
				$watermark_editor->get_image(),
				$position_coords,
				$opacity
			);
		} else {
			// Imagick implementation would go here
			$result = new \WP_Error( 'unsupported', __( 'Image editor not supported', 'roof21-core' ) );
		}

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		// Generate watermarked file path
		$file_info = pathinfo( $file_path );
		$watermarked_path = $file_info['dirname'] . '/' . $file_info['filename'] . '-watermarked.' . $file_info['extension'];

		// Save watermarked image
		$save_result = $editor->save( $watermarked_path );

		if ( is_wp_error( $save_result ) ) {
			return $save_result;
		}

		return $watermarked_path;
	}

	/**
	 * Apply watermark using GD library.
	 *
	 * @since 1.0.0
	 * @param resource $image     Image resource.
	 * @param resource $watermark Watermark resource.
	 * @param array    $position  Position coordinates.
	 * @param int      $opacity   Opacity percentage.
	 * @return bool|WP_Error True on success, error on failure.
	 */
	private function apply_watermark_gd( $image, $watermark, $position, $opacity ) {
		// Convert opacity to alpha (0-127 scale)
		$alpha = intval( ( 100 - $opacity ) * 127 / 100 );

		// Set opacity
		if ( function_exists( 'imagefilter' ) ) {
			imagefilter( $watermark, IMG_FILTER_COLORIZE, 0, 0, 0, $alpha );
		}

		// Merge watermark with image
		$result = imagecopy(
			$image,
			$watermark,
			$position['x'],
			$position['y'],
			0,
			0,
			imagesx( $watermark ),
			imagesy( $watermark )
		);

		if ( ! $result ) {
			// Try with alpha blending
			imagealphablending( $image, true );
			$result = imagecopymerge(
				$image,
				$watermark,
				$position['x'],
				$position['y'],
				0,
				0,
				imagesx( $watermark ),
				imagesy( $watermark ),
				$opacity
			);
		}

		if ( ! $result ) {
			return new \WP_Error( 'watermark_failed', __( 'Failed to apply watermark', 'roof21-core' ) );
		}

		return true;
	}

	/**
	 * Calculate watermark position coordinates.
	 *
	 * @since 1.0.0
	 * @param array  $image_size     Image dimensions.
	 * @param array  $watermark_size Watermark dimensions.
	 * @param string $position       Position name.
	 * @param int    $padding        Padding in pixels.
	 * @return array X and Y coordinates.
	 */
	private function calculate_watermark_position( $image_size, $watermark_size, $position, $padding ) {
		$coords = array( 'x' => 0, 'y' => 0 );

		switch ( $position ) {
			case 'top-left':
				$coords['x'] = $padding;
				$coords['y'] = $padding;
				break;

			case 'top-center':
				$coords['x'] = ( $image_size['width'] - $watermark_size['width'] ) / 2;
				$coords['y'] = $padding;
				break;

			case 'top-right':
				$coords['x'] = $image_size['width'] - $watermark_size['width'] - $padding;
				$coords['y'] = $padding;
				break;

			case 'middle-left':
				$coords['x'] = $padding;
				$coords['y'] = ( $image_size['height'] - $watermark_size['height'] ) / 2;
				break;

			case 'center':
				$coords['x'] = ( $image_size['width'] - $watermark_size['width'] ) / 2;
				$coords['y'] = ( $image_size['height'] - $watermark_size['height'] ) / 2;
				break;

			case 'middle-right':
				$coords['x'] = $image_size['width'] - $watermark_size['width'] - $padding;
				$coords['y'] = ( $image_size['height'] - $watermark_size['height'] ) / 2;
				break;

			case 'bottom-left':
				$coords['x'] = $padding;
				$coords['y'] = $image_size['height'] - $watermark_size['height'] - $padding;
				break;

			case 'bottom-center':
				$coords['x'] = ( $image_size['width'] - $watermark_size['width'] ) / 2;
				$coords['y'] = $image_size['height'] - $watermark_size['height'] - $padding;
				break;

			case 'bottom-right':
			default:
				$coords['x'] = $image_size['width'] - $watermark_size['width'] - $padding;
				$coords['y'] = $image_size['height'] - $watermark_size['height'] - $padding;
				break;
		}

		return apply_filters( 'roof21_watermark_position', $coords, $image_size, $watermark_size, $position );
	}

	/**
	 * Save watermark relationship in attachment meta.
	 *
	 * @since 1.0.0
	 * @param array $metadata      Attachment metadata.
	 * @param int   $attachment_id Attachment ID.
	 * @return array Modified metadata.
	 */
	public function save_watermark_relationship( $metadata, $attachment_id ) {
		// This filter runs after attachment is created
		// Get the upload data that was stored
		$upload_dir = wp_upload_dir();

		// Store watermarked version as separate attachment
		// Or just save the path in meta

		remove_filter( 'wp_generate_attachment_metadata', array( $this, 'save_watermark_relationship' ), 10 );

		return $metadata;
	}

	/**
	 * Batch re-watermark all property images.
	 *
	 * @since 1.0.0
	 * @return array Results.
	 */
	public function batch_rewatermark() {
		set_time_limit( 300 );

		$results = array(
			'success' => 0,
			'failed'  => 0,
			'errors'  => array(),
		);

		// Get all property attachments
		$attachments = get_posts( array(
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'post_parent'    => 0, // Will update to filter by property parent
			'posts_per_page' => -1,
		) );

		foreach ( $attachments as $attachment ) {
			$file_path = get_attached_file( $attachment->ID );

			if ( ! $file_path || ! file_exists( $file_path ) ) {
				continue;
			}

			$watermarked = $this->create_watermarked_image( $file_path );

			if ( is_wp_error( $watermarked ) ) {
				$results['failed']++;
				$results['errors'][] = sprintf(
					'Attachment #%d: %s',
					$attachment->ID,
					$watermarked->get_error_message()
				);
			} else {
				$results['success']++;
			}
		}

		return $results;
	}
}
