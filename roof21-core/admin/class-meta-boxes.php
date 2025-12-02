<?php
/**
 * Meta boxes for property edit screen.
 *
 * @package Roof21\Core\Admin
 */

namespace Roof21\Core\Admin;

/**
 * MetaBoxes class.
 */
class MetaBoxes {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_meta_boxes' ) );
	}

	/**
	 * Add meta boxes.
	 *
	 * @since 1.0.0
	 */
	public function add_meta_boxes() {
		add_meta_box(
			'roof21_property_details',
			__( 'Property Details', 'roof21-core' ),
			array( $this, 'render_property_details' ),
			'roof21_property',
			'normal',
			'high'
		);
	}

	/**
	 * Render property details meta box.
	 *
	 * @since 1.0.0
	 * @param WP_Post $post Post object.
	 */
	public function render_property_details( $post ) {
		wp_nonce_field( 'roof21_property_details', 'roof21_property_details_nonce' );

		$reference = get_post_meta( $post->ID, '_roof21_reference_code', true );
		$price_thb = get_post_meta( $post->ID, '_roof21_price_thb', true );
		$beds = get_post_meta( $post->ID, '_roof21_beds', true );
		$baths = get_post_meta( $post->ID, '_roof21_baths', true );
		?>
		<table class="form-table">
			<tr>
				<th><label for="roof21_reference_code"><?php esc_html_e( 'Reference Code', 'roof21-core' ); ?></label></th>
				<td><input type="text" id="roof21_reference_code" name="roof21_reference_code" value="<?php echo esc_attr( $reference ); ?>" class="regular-text"></td>
			</tr>
			<tr>
				<th><label for="roof21_price_thb"><?php esc_html_e( 'Price (THB)', 'roof21-core' ); ?></label></th>
				<td><input type="number" id="roof21_price_thb" name="roof21_price_thb" value="<?php echo esc_attr( $price_thb ); ?>" class="regular-text"></td>
			</tr>
			<tr>
				<th><label for="roof21_beds"><?php esc_html_e( 'Bedrooms', 'roof21-core' ); ?></label></th>
				<td><input type="number" id="roof21_beds" name="roof21_beds" value="<?php echo esc_attr( $beds ); ?>"></td>
			</tr>
			<tr>
				<th><label for="roof21_baths"><?php esc_html_e( 'Bathrooms', 'roof21-core' ); ?></label></th>
				<td><input type="number" id="roof21_baths" name="roof21_baths" value="<?php echo esc_attr( $baths ); ?>"></td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Save meta boxes.
	 *
	 * @since 1.0.0
	 * @param int $post_id Post ID.
	 */
	public function save_meta_boxes( $post_id ) {
		if ( ! isset( $_POST['roof21_property_details_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['roof21_property_details_nonce'], 'roof21_property_details' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$fields = array( 'roof21_reference_code', 'roof21_price_thb', 'roof21_beds', 'roof21_baths' );

		foreach ( $fields as $field ) {
			if ( isset( $_POST[ $field ] ) ) {
				update_post_meta( $post_id, '_' . $field, sanitize_text_field( $_POST[ $field ] ) );
			}
		}
	}
}
