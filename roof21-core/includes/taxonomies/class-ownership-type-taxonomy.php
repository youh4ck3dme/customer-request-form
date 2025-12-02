<?php
/**
 * Ownership Type Taxonomy.
 *
 * @package Roof21\Core\Taxonomies
 */

namespace Roof21\Core\Taxonomies;

/**
 * Ownership Type Taxonomy class.
 */
class OwnershipTypeTaxonomy {

	/**
	 * Register taxonomy.
	 *
	 * @since 1.0.0
	 */
	public static function register() {
		$labels = array(
			'name'                       => _x( 'Ownership Types', 'Taxonomy General Name', 'roof21-core' ),
			'singular_name'              => _x( 'Ownership Type', 'Taxonomy Singular Name', 'roof21-core' ),
			'menu_name'                  => __( 'Ownership Types', 'roof21-core' ),
			'all_items'                  => __( 'All Ownership Types', 'roof21-core' ),
			'parent_item'                => __( 'Parent Type', 'roof21-core' ),
			'parent_item_colon'          => __( 'Parent Type:', 'roof21-core' ),
			'new_item_name'              => __( 'New Ownership Type Name', 'roof21-core' ),
			'add_new_item'               => __( 'Add New Ownership Type', 'roof21-core' ),
			'edit_item'                  => __( 'Edit Ownership Type', 'roof21-core' ),
			'update_item'                => __( 'Update Ownership Type', 'roof21-core' ),
			'view_item'                  => __( 'View Ownership Type', 'roof21-core' ),
			'separate_items_with_commas' => __( 'Separate types with commas', 'roof21-core' ),
			'add_or_remove_items'        => __( 'Add or remove types', 'roof21-core' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'roof21-core' ),
			'popular_items'              => __( 'Popular Ownership Types', 'roof21-core' ),
			'search_items'               => __( 'Search Ownership Types', 'roof21-core' ),
			'not_found'                  => __( 'Not Found', 'roof21-core' ),
			'no_terms'                   => __( 'No ownership types', 'roof21-core' ),
			'items_list'                 => __( 'Ownership types list', 'roof21-core' ),
			'items_list_navigation'      => __( 'Ownership types list navigation', 'roof21-core' ),
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
				'slug'       => 'ownership',
				'with_front' => false,
			),
		);

		register_taxonomy( 'roof21_ownership_type', array( 'roof21_property' ), $args );
	}
}
