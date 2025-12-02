<?php
/**
 * Listing Type Taxonomy (For Sale / For Rent).
 *
 * @package Roof21\Core\Taxonomies
 */

namespace Roof21\Core\Taxonomies;

/**
 * Listing Type Taxonomy class.
 */
class ListingTypeTaxonomy {

	/**
	 * Register taxonomy.
	 *
	 * @since 1.0.0
	 */
	public static function register() {
		$labels = array(
			'name'                       => _x( 'Listing Types', 'Taxonomy General Name', 'roof21-core' ),
			'singular_name'              => _x( 'Listing Type', 'Taxonomy Singular Name', 'roof21-core' ),
			'menu_name'                  => __( 'Listing Types', 'roof21-core' ),
			'all_items'                  => __( 'All Listing Types', 'roof21-core' ),
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'new_item_name'              => __( 'New Listing Type Name', 'roof21-core' ),
			'add_new_item'               => __( 'Add New Listing Type', 'roof21-core' ),
			'edit_item'                  => __( 'Edit Listing Type', 'roof21-core' ),
			'update_item'                => __( 'Update Listing Type', 'roof21-core' ),
			'view_item'                  => __( 'View Listing Type', 'roof21-core' ),
			'separate_items_with_commas' => __( 'Separate types with commas', 'roof21-core' ),
			'add_or_remove_items'        => __( 'Add or remove types', 'roof21-core' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'roof21-core' ),
			'popular_items'              => __( 'Popular Listing Types', 'roof21-core' ),
			'search_items'               => __( 'Search Listing Types', 'roof21-core' ),
			'not_found'                  => __( 'Not Found', 'roof21-core' ),
			'no_terms'                   => __( 'No listing types', 'roof21-core' ),
			'items_list'                 => __( 'Listing types list', 'roof21-core' ),
			'items_list_navigation'      => __( 'Listing types list navigation', 'roof21-core' ),
		);

		$args = array(
			'labels'            => $labels,
			'hierarchical'      => false,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_tagcloud'     => false,
			'show_in_rest'      => true,
			'rewrite'           => array(
				'slug'       => 'listing-type',
				'with_front' => false,
			),
		);

		register_taxonomy( 'roof21_listing_type', array( 'roof21_property' ), $args );
	}
}
