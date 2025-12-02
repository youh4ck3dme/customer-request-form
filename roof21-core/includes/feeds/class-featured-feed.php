<?php
/**
 * Featured Properties Feed.
 *
 * @package Roof21\Core\Feeds
 */

namespace Roof21\Core\Feeds;

/**
 * Featured Feed class.
 */
class FeaturedFeed extends FeedBase {

	/**
	 * Feed name.
	 *
	 * @var string
	 */
	protected $feed_name = 'Featured Properties';

	/**
	 * Feed slug.
	 *
	 * @var string
	 */
	protected $feed_slug = 'roof21-featured';

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

		// Only featured properties
		$args['meta_query'] = array(
			array(
				'key'   => '_roof21_featured',
				'value' => '1',
			),
		);

		$args = apply_filters( 'roof21_feed_query_args', $args, 'featured' );

		$query = new \WP_Query( $args );

		return wp_list_pluck( $query->posts, 'ID' );
	}
}
