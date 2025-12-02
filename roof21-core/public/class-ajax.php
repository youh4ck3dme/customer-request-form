<?php
/**
 * AJAX handlers.
 *
 * @package Roof21\Core\PublicFrontend
 */

namespace Roof21\Core\PublicFrontend;

/**
 * Ajax class.
 */
class Ajax {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'wp_ajax_roof21_search_properties', array( $this, 'search_properties' ) );
		add_action( 'wp_ajax_nopriv_roof21_search_properties', array( $this, 'search_properties' ) );

		add_action( 'wp_ajax_roof21_submit_contact_form', array( $this, 'submit_contact_form' ) );
		add_action( 'wp_ajax_nopriv_roof21_submit_contact_form', array( $this, 'submit_contact_form' ) );
	}

	/**
	 * AJAX property search.
	 *
	 * @since 1.0.0
	 */
	public function search_properties() {
		check_ajax_referer( 'roof21_public', 'nonce' );

		// Get search parameters
		$params = array(
			'listing_type' => isset( $_POST['listing_type'] ) ? sanitize_text_field( $_POST['listing_type'] ) : '',
			'location' => isset( $_POST['location'] ) ? sanitize_text_field( $_POST['location'] ) : '',
			'property_type' => isset( $_POST['property_type'] ) ? sanitize_text_field( $_POST['property_type'] ) : '',
			'beds' => isset( $_POST['beds'] ) ? intval( $_POST['beds'] ) : 0,
			'min_price' => isset( $_POST['min_price'] ) ? floatval( $_POST['min_price'] ) : 0,
			'max_price' => isset( $_POST['max_price'] ) ? floatval( $_POST['max_price'] ) : 0,
		);

		// Build query
		$args = array(
			'post_type' => 'roof21_property',
			'posts_per_page' => 12,
			'paged' => isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1,
		);

		// Execute query and return results
		$query = new \WP_Query( $args );

		$properties = array();
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$properties[] = array(
					'id' => get_the_ID(),
					'title' => get_the_title(),
					'url' => get_permalink(),
				);
			}
			wp_reset_postdata();
		}

		wp_send_json_success( array(
			'properties' => $properties,
			'total' => $query->found_posts,
		) );
	}

	/**
	 * Submit contact form.
	 *
	 * @since 1.0.0
	 */
	public function submit_contact_form() {
		check_ajax_referer( 'roof21_public', 'nonce' );

		$form_data = array(
			'name' => isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '',
			'email' => isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '',
			'phone' => isset( $_POST['phone'] ) ? sanitize_text_field( $_POST['phone'] ) : '',
			'message' => isset( $_POST['message'] ) ? sanitize_textarea_field( $_POST['message'] ) : '',
		);

		$bitrix_forms = new \Roof21\Core\Bitrix24\Bitrix24Forms();
		$result = $bitrix_forms->submit_contact_form( $form_data );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array(
				'message' => $result->get_error_message(),
			) );
		}

		wp_send_json_success( array(
			'message' => __( 'Thank you for your inquiry!', 'roof21-core' ),
		) );
	}
}
