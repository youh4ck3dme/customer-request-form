<?php
/**
 * Condos Only Feed.
 *
 * @package Roof21\Core\Feeds
 */

namespace Roof21\Core\Feeds;

/**
 * Condos Feed class.
 */
class CondosFeed extends FeedBase {

	/**
	 * Feed name.
	 *
	 * @var string
	 */
	protected $feed_name = 'Condos Only';

	/**
	 * Feed slug.
	 *
	 * @var string
	 */
	protected $feed_slug = 'roof21-condos';

	/**
	 * Use watermarked images.
	 *
	 * @var bool
	 */
	protected $use_watermarked = true;

	/**
	 * Get properties for feed.
	 *
	 * @since 1.0.0
	 * @return array Array of property post IDs.
	 */
	protected function get_properties() {
		$args = $this->get_base_query_args();

		// Only condos/apartments
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'roof21_property_type',
				'field'    => 'slug',
				'terms'    => array( 'condo', 'apartment', 'condominium' ),
			),
		);

		$args = apply_filters( 'roof21_feed_query_args', $args, 'condos' );

		$query = new \WP_Query( $args );

		return wp_list_pluck( $query->posts, 'ID' );
	}
}
