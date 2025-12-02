<?php
/**
 * Bitrix24 Forms Integration.
 *
 * @package Roof21\Core\Bitrix24
 */

namespace Roof21\Core\Bitrix24;

/**
 * Bitrix24 Forms class.
 */
class Bitrix24Forms {

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
	 * Submit contact form to Bitrix24.
	 *
	 * @since 1.0.0
	 * @param array $form_data Form data.
	 * @return array|WP_Error Lead/Contact ID or error.
	 */
	public function submit_contact_form( $form_data ) {
		$name = sanitize_text_field( $form_data['name'] ?? '' );
		$email = sanitize_email( $form_data['email'] ?? '' );
		$phone = sanitize_text_field( $form_data['phone'] ?? '' );
		$message = sanitize_textarea_field( $form_data['message'] ?? '' );

		// Split name into first and last name
		$name_parts = explode( ' ', $name, 2 );
		$first_name = $name_parts[0] ?? '';
		$last_name = $name_parts[1] ?? '';

		// Create contact
		$contact_fields = array(
			'NAME'  => $first_name,
			'LAST_NAME' => $last_name,
		);

		if ( $email ) {
			$contact_fields['EMAIL'] = array(
				array( 'VALUE' => $email, 'VALUE_TYPE' => 'WORK' ),
			);
		}

		if ( $phone ) {
			$contact_fields['PHONE'] = array(
				array( 'VALUE' => $phone, 'VALUE_TYPE' => 'WORK' ),
			);
		}

		$contact_response = $this->api->create_contact( $contact_fields );

		if ( is_wp_error( $contact_response ) ) {
			return $contact_response;
		}

		$contact_id = $contact_response['result'] ?? null;

		// Create lead
		$lead_fields = array(
			'TITLE'      => sprintf( __( 'Website inquiry from %s', 'roof21-core' ), $name ),
			'NAME'       => $first_name,
			'LAST_NAME'  => $last_name,
			'COMMENTS'   => $message,
			'SOURCE_ID'  => 'WEB',
		);

		if ( $email ) {
			$lead_fields['EMAIL'] = array(
				array( 'VALUE' => $email, 'VALUE_TYPE' => 'WORK' ),
			);
		}

		if ( $phone ) {
			$lead_fields['PHONE'] = array(
				array( 'VALUE' => $phone, 'VALUE_TYPE' => 'WORK' ),
			);
		}

		if ( $contact_id ) {
			$lead_fields['CONTACT_ID'] = $contact_id;
		}

		$lead_response = $this->api->create_lead( $lead_fields );

		return $lead_response;
	}

	/**
	 * Submit property inquiry to Bitrix24.
	 *
	 * @since 1.0.0
	 * @param array $form_data Form data.
	 * @param int   $property_id Property post ID.
	 * @return array|WP_Error Lead ID or error.
	 */
	public function submit_property_inquiry( $form_data, $property_id ) {
		$name = sanitize_text_field( $form_data['name'] ?? '' );
		$email = sanitize_email( $form_data['email'] ?? '' );
		$phone = sanitize_text_field( $form_data['phone'] ?? '' );
		$message = sanitize_textarea_field( $form_data['message'] ?? '' );

		$property_title = get_the_title( $property_id );
		$reference_code = get_post_meta( $property_id, '_roof21_reference_code', true );

		// Split name
		$name_parts = explode( ' ', $name, 2 );
		$first_name = $name_parts[0] ?? '';
		$last_name = $name_parts[1] ?? '';

		// Create contact
		$contact_fields = array(
			'NAME'      => $first_name,
			'LAST_NAME' => $last_name,
		);

		if ( $email ) {
			$contact_fields['EMAIL'] = array(
				array( 'VALUE' => $email, 'VALUE_TYPE' => 'WORK' ),
			);
		}

		if ( $phone ) {
			$contact_fields['PHONE'] = array(
				array( 'VALUE' => $phone, 'VALUE_TYPE' => 'WORK' ),
			);
		}

		$contact_response = $this->api->create_contact( $contact_fields );

		if ( is_wp_error( $contact_response ) ) {
			return $contact_response;
		}

		$contact_id = $contact_response['result'] ?? null;

		// Create lead with property reference
		$lead_fields = array(
			'TITLE'     => sprintf(
				__( 'Property inquiry: %s (Ref: %s)', 'roof21-core' ),
				$property_title,
				$reference_code
			),
			'NAME'      => $first_name,
			'LAST_NAME' => $last_name,
			'COMMENTS'  => sprintf(
				__( 'Property: %s (Ref: %s)%sMessage: %s', 'roof21-core' ),
				$property_title,
				$reference_code,
				"\n\n",
				$message
			),
			'SOURCE_ID' => 'WEB',
		);

		if ( $email ) {
			$lead_fields['EMAIL'] = array(
				array( 'VALUE' => $email, 'VALUE_TYPE' => 'WORK' ),
			);
		}

		if ( $phone ) {
			$lead_fields['PHONE'] = array(
				array( 'VALUE' => $phone, 'VALUE_TYPE' => 'WORK' ),
			);
		}

		if ( $contact_id ) {
			$lead_fields['CONTACT_ID'] = $contact_id;
		}

		// Link to property deal if available
		$bitrix_id = get_post_meta( $property_id, '_roof21_bitrix_id', true );
		if ( $bitrix_id ) {
			$lead_fields['UF_CRM_RELATED_DEAL'] = $bitrix_id;
		}

		$lead_response = $this->api->create_lead( $lead_fields );

		return $lead_response;
	}
}
