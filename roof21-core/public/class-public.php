<?php
/**
 * Public-facing functionality.
 *
 * @package Roof21\Core\PublicFrontend
 */

namespace Roof21\Core\PublicFrontend;

/**
 * Public class.
 */
class PublicFrontend {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'body_class', array( $this, 'add_body_classes' ) );
		add_action( 'pre_get_posts', array( $this, 'modify_property_query' ) );
	}

	/**
	 * Add custom body classes.
	 *
	 * @since 1.0.0
	 * @param array $classes Body classes.
	 * @return array Modified classes.
	 */
	public function add_body_classes( $classes ) {
		if ( is_post_type_archive( 'roof21_property' ) || is_singular( 'roof21_property' ) ) {
			$classes[] = 'roof21-property-page';
		}

		$classes[] = 'currency-' . roof21_get_current_currency();
		$classes[] = 'lang-' . roof21_get_current_language();

		return $classes;
	}

	/**
	 * Modify property query.
	 *
	 * @since 1.0.0
	 * @param WP_Query $query Query object.
	 */
	public function modify_property_query( $query ) {
		if ( ! is_admin() && $query->is_main_query() && is_post_type_archive( 'roof21_property' ) ) {
			$query->set( 'posts_per_page', get_option( 'roof21_properties_per_page', 12 ) );
		}
	}
}
