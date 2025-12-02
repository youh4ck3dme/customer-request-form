<?php
/**
 * Location Taxonomy.
 *
 * @package Roof21\Core\Taxonomies
 */

namespace Roof21\Core\Taxonomies;

/**
 * Location Taxonomy class.
 */
class LocationTaxonomy {

	/**
	 * Register taxonomy.
	 *
	 * @since 1.0.0
	 */
	public static function register() {
		$labels = array(
			'name'                       => _x( 'Locations', 'Taxonomy General Name', 'roof21-core' ),
			'singular_name'              => _x( 'Location', 'Taxonomy Singular Name', 'roof21-core' ),
			'menu_name'                  => __( 'Locations', 'roof21-core' ),
			'all_items'                  => __( 'All Locations', 'roof21-core' ),
			'parent_item'                => __( 'Parent Location', 'roof21-core' ),
			'parent_item_colon'          => __( 'Parent Location:', 'roof21-core' ),
			'new_item_name'              => __( 'New Location Name', 'roof21-core' ),
			'add_new_item'               => __( 'Add New Location', 'roof21-core' ),
			'edit_item'                  => __( 'Edit Location', 'roof21-core' ),
			'update_item'                => __( 'Update Location', 'roof21-core' ),
			'view_item'                  => __( 'View Location', 'roof21-core' ),
			'separate_items_with_commas' => __( 'Separate locations with commas', 'roof21-core' ),
			'add_or_remove_items'        => __( 'Add or remove locations', 'roof21-core' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'roof21-core' ),
			'popular_items'              => __( 'Popular Locations', 'roof21-core' ),
			'search_items'               => __( 'Search Locations', 'roof21-core' ),
			'not_found'                  => __( 'Not Found', 'roof21-core' ),
			'no_terms'                   => __( 'No locations', 'roof21-core' ),
			'items_list'                 => __( 'Locations list', 'roof21-core' ),
			'items_list_navigation'      => __( 'Locations list navigation', 'roof21-core' ),
		);

		$args = array(
			'labels'            => $labels,
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_tagcloud'     => false,
			'show_in_rest'      => true,
			'rewrite'           => array(
				'slug'       => 'location',
				'with_front' => false,
			),
		);

		register_taxonomy( 'roof21_location', array( 'roof21_property', 'roof21_development', 'roof21_area_guide' ), $args );
	}
}
