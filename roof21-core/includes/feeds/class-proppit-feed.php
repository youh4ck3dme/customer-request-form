<?php
/**
 * Proppit Feed.
 *
 * @package Roof21\Core\Feeds
 */

namespace Roof21\Core\Feeds;

/**
 * Proppit Feed class.
 */
class ProppitFeed extends FeedBase {

	/**
	 * Feed name.
	 *
	 * @var string
	 */
	protected $feed_name = 'Proppit';

	/**
	 * Feed slug.
	 *
	 * @var string
	 */
	protected $feed_slug = 'roof21-proppit';

	/**
	 * Use watermarked images.
	 *
	 * @var bool
	 */
	protected $use_watermarked = false; // Proppit gets originals

	/**
	 * Get properties for feed.
	 *
	 * @since 1.0.0
	 * @return array Array of property post IDs.
	 */
	protected function get_properties() {
		$args = $this->get_base_query_args();

		// Only include properties with Proppit project mapping
		$args['meta_query'] = array(
			array(
				'key'     => '_roof21_proppit_project',
				'value'   => '',
				'compare' => '!=',
			),
		);

		// Apply custom filters from settings
		$filters = get_option( 'roof21_proppit_feed_filters', array() );

		if ( ! empty( $filters['locations'] ) ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'roof21_location',
				'field'    => 'slug',
				'terms'    => $filters['locations'],
			);
		}

		if ( ! empty( $filters['property_types'] ) ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'roof21_property_type',
				'field'    => 'slug',
				'terms'    => $filters['property_types'],
			);
		}

		$args = apply_filters( 'roof21_feed_query_args', $args, 'proppit' );

		$query = new \WP_Query( $args );

		return wp_list_pluck( $query->posts, 'ID' );
	}

	/**
	 * Add property to XML (Proppit-specific format).
	 *
	 * @since 1.0.0
	 * @param SimpleXMLElement $xml         XML object.
	 * @param int              $property_id Property post ID.
	 */
	protected function add_property_to_xml( $xml, $property_id ) {
		$property = $xml->addChild( 'property' );

		// Proppit-specific fields
		$proppit_project = roof21_get_property_meta( $property_id, '_roof21_proppit_project', '' );
		$reference = roof21_get_property_reference( $property_id );

		$property->addChild( 'project_name', htmlspecialchars( $proppit_project ) );
		$property->addChild( 'unit_reference', htmlspecialchars( $reference ) );
		$property->addChild( 'title', htmlspecialchars( get_the_title( $property_id ) ) );
		$property->addChild( 'description', htmlspecialchars( wp_strip_all_tags( get_the_content( null, false, $property_id ) ) ) );

		// Price
		$price_thb = roof21_get_property_meta( $property_id, '_roof21_price_thb', 0 );
		$property->addChild( 'price_thb', $price_thb );

		// Property details
		$property->addChild( 'bedrooms', roof21_get_property_meta( $property_id, '_roof21_beds', '' ) );
		$property->addChild( 'bathrooms', roof21_get_property_meta( $property_id, '_roof21_baths', '' ) );
		$property->addChild( 'floor_area', roof21_get_property_meta( $property_id, '_roof21_living_area', '' ) );
		$property->addChild( 'land_area', roof21_get_property_meta( $property_id, '_roof21_land_area', '' ) );

		// Location
		$location = roof21_get_property_location( $property_id );
		if ( $location ) {
			$property->addChild( 'location', htmlspecialchars( $location->name ) );
		}

		// Property type
		$property_type = roof21_get_property_type( $property_id );
		if ( $property_type ) {
			$property->addChild( 'type', htmlspecialchars( $property_type->name ) );
		}

		// Listing type
		$listing_terms = get_the_terms( $property_id, 'roof21_listing_type' );
		if ( $listing_terms && ! is_wp_error( $listing_terms ) ) {
			$property->addChild( 'sale_type', htmlspecialchars( strtolower( $listing_terms[0]->name ) ) );
		}

		// Images (original, not watermarked)
		$gallery = roof21_get_property_gallery( $property_id, false );
		if ( ! empty( $gallery ) ) {
			$images_node = $property->addChild( 'images' );
			foreach ( $gallery as $image_id ) {
				$image_url = wp_get_attachment_url( $image_id );
				if ( $image_url ) {
					$images_node->addChild( 'image', esc_url( $image_url ) );
				}
			}
		}

		// URL
		$property->addChild( 'url', esc_url( get_permalink( $property_id ) ) );
	}
}
