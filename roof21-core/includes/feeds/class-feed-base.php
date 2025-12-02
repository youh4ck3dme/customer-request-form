<?php
/**
 * Base Feed Class.
 *
 * @package Roof21\Core\Feeds
 */

namespace Roof21\Core\Feeds;

/**
 * Feed Base class.
 */
abstract class FeedBase {

	/**
	 * Feed name.
	 *
	 * @var string
	 */
	protected $feed_name = '';

	/**
	 * Feed slug.
	 *
	 * @var string
	 */
	protected $feed_slug = '';

	/**
	 * Use watermarked images.
	 *
	 * @var bool
	 */
	protected $use_watermarked = true;

	/**
	 * Register feed endpoint.
	 *
	 * @since 1.0.0
	 */
	public static function register_endpoint() {
		$instance = new static();
		add_feed( $instance->feed_slug, array( $instance, 'generate_feed' ) );
	}

	/**
	 * Get properties for feed.
	 *
	 * @since 1.0.0
	 * @return array Array of property post IDs.
	 */
	abstract protected function get_properties();

	/**
	 * Generate XML feed.
	 *
	 * @since 1.0.0
	 */
	public function generate_feed() {
		// Check for cached feed
		$cache_key = 'roof21_feed_' . $this->feed_slug;
		$cache_duration = intval( get_option( 'roof21_feed_cache_duration', 900 ) );

		$cached_feed = get_transient( $cache_key );

		if ( false !== $cached_feed && ! isset( $_GET['nocache'] ) ) {
			header( 'Content-Type: application/xml; charset=' . get_option( 'blog_charset' ), true );
			echo $cached_feed;
			exit;
		}

		// Generate feed
		$properties = $this->get_properties();

		$xml = new \SimpleXMLElement( '<?xml version="1.0" encoding="UTF-8"?><properties></properties>' );

		foreach ( $properties as $property_id ) {
			$this->add_property_to_xml( $xml, $property_id );
		}

		// Format XML
		$dom = new \DOMDocument( '1.0', 'UTF-8' );
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		$dom->loadXML( $xml->asXML() );

		$output = $dom->saveXML();

		// Cache feed
		set_transient( $cache_key, $output, $cache_duration );

		// Output feed
		header( 'Content-Type: application/xml; charset=' . get_option( 'blog_charset' ), true );
		echo $output;

		do_action( 'roof21_feed_generated', $this->feed_slug, count( $properties ) );

		exit;
	}

	/**
	 * Add property to XML.
	 *
	 * @since 1.0.0
	 * @param SimpleXMLElement $xml         XML object.
	 * @param int              $property_id Property post ID.
	 */
	protected function add_property_to_xml( $xml, $property_id ) {
		$property = $xml->addChild( 'property' );

		// Basic info
		$property->addChild( 'id', $property_id );
		$property->addChild( 'reference', htmlspecialchars( roof21_get_property_reference( $property_id ) ) );
		$property->addChild( 'title', htmlspecialchars( get_the_title( $property_id ) ) );
		$property->addChild( 'description', htmlspecialchars( get_the_content( null, false, $property_id ) ) );
		$property->addChild( 'url', esc_url( get_permalink( $property_id ) ) );

		// Price
		$price_thb = roof21_get_property_meta( $property_id, '_roof21_price_thb', 0 );
		$property->addChild( 'price', $price_thb );
		$property->addChild( 'currency', 'THB' );

		// Property details
		$property->addChild( 'beds', roof21_get_property_meta( $property_id, '_roof21_beds', '' ) );
		$property->addChild( 'baths', roof21_get_property_meta( $property_id, '_roof21_baths', '' ) );
		$property->addChild( 'living_area', roof21_get_property_meta( $property_id, '_roof21_living_area', '' ) );
		$property->addChild( 'land_area', roof21_get_property_meta( $property_id, '_roof21_land_area', '' ) );
		$property->addChild( 'project_name', htmlspecialchars( roof21_get_property_meta( $property_id, '_roof21_project_name', '' ) ) );

		// Location
		$location = roof21_get_property_location( $property_id );
		if ( $location ) {
			$property->addChild( 'location', htmlspecialchars( $location->name ) );
		}

		// Property type
		$property_type = roof21_get_property_type( $property_id );
		if ( $property_type ) {
			$property->addChild( 'property_type', htmlspecialchars( $property_type->name ) );
		}

		// Ownership type
		$ownership_type = roof21_get_property_ownership_type( $property_id );
		if ( $ownership_type ) {
			$property->addChild( 'ownership_type', htmlspecialchars( $ownership_type->name ) );
		}

		// Listing type
		$listing_terms = get_the_terms( $property_id, 'roof21_listing_type' );
		if ( $listing_terms && ! is_wp_error( $listing_terms ) ) {
			$property->addChild( 'listing_type', htmlspecialchars( $listing_terms[0]->name ) );
		}

		// Features
		$features = get_the_terms( $property_id, 'roof21_feature' );
		if ( $features && ! is_wp_error( $features ) ) {
			$features_node = $property->addChild( 'features' );
			foreach ( $features as $feature ) {
				$features_node->addChild( 'feature', htmlspecialchars( $feature->name ) );
			}
		}

		// Images
		$gallery = roof21_get_property_gallery( $property_id, $this->use_watermarked );
		if ( ! empty( $gallery ) ) {
			$images_node = $property->addChild( 'images' );
			foreach ( $gallery as $image_id ) {
				$image_url = wp_get_attachment_url( $image_id );
				if ( $image_url ) {
					$images_node->addChild( 'image', esc_url( $image_url ) );
				}
			}
		}

		// Coordinates (approximate)
		$lat = roof21_get_property_meta( $property_id, '_roof21_lat', '' );
		$lng = roof21_get_property_meta( $property_id, '_roof21_lng', '' );
		if ( $lat && $lng ) {
			$coords = $property->addChild( 'coordinates' );
			$coords->addChild( 'latitude', $lat );
			$coords->addChild( 'longitude', $lng );
		}
	}

	/**
	 * Get base query args for properties.
	 *
	 * @since 1.0.0
	 * @return array Query args.
	 */
	protected function get_base_query_args() {
		return array(
			'post_type'      => 'roof21_property',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'date',
			'order'          => 'DESC',
		);
	}
}
