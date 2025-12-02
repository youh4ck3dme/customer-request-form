<?php
/**
 * Property Type Taxonomy.
 *
 * @package Roof21\Core\Taxonomies
 */

namespace Roof21\Core\Taxonomies;

/**
 * Property Type Taxonomy class.
 */
class PropertyTypeTaxonomy {

	/**
	 * Register taxonomy.
	 *
	 * @since 1.0.0
	 */
	public static function register() {
		$labels = array(
			'name'                       => _x( 'Property Types', 'Taxonomy General Name', 'roof21-core' ),
			'singular_name'              => _x( 'Property Type', 'Taxonomy Singular Name', 'roof21-core' ),
			'menu_name'                  => __( 'Property Types', 'roof21-core' ),
			'all_items'                  => __( 'All Property Types', 'roof21-core' ),
			'parent_item'                => __( 'Parent Property Type', 'roof21-core' ),
			'parent_item_colon'          => __( 'Parent Property Type:', 'roof21-core' ),
			'new_item_name'              => __( 'New Property Type Name', 'roof21-core' ),
			'add_new_item'               => __( 'Add New Property Type', 'roof21-core' ),
			'edit_item'                  => __( 'Edit Property Type', 'roof21-core' ),
			'update_item'                => __( 'Update Property Type', 'roof21-core' ),
			'view_item'                  => __( 'View Property Type', 'roof21-core' ),
			'separate_items_with_commas' => __( 'Separate property types with commas', 'roof21-core' ),
			'add_or_remove_items'        => __( 'Add or remove property types', 'roof21-core' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'roof21-core' ),
			'popular_items'              => __( 'Popular Property Types', 'roof21-core' ),
			'search_items'               => __( 'Search Property Types', 'roof21-core' ),
			'not_found'                  => __( 'Not Found', 'roof21-core' ),
			'no_terms'                   => __( 'No property types', 'roof21-core' ),
			'items_list'                 => __( 'Property types list', 'roof21-core' ),
			'items_list_navigation'      => __( 'Property types list navigation', 'roof21-core' ),
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
				'slug'       => 'property-type',
				'with_front' => false,
			),
		);

		register_taxonomy( 'roof21_property_type', array( 'roof21_property', 'roof21_development' ), $args );
	}
}
