<?php
/**
 * Feature Taxonomy.
 *
 * @package Roof21\Core\Taxonomies
 */

namespace Roof21\Core\Taxonomies;

/**
 * Feature Taxonomy class.
 */
class FeatureTaxonomy {

	/**
	 * Register taxonomy.
	 *
	 * @since 1.0.0
	 */
	public static function register() {
		$labels = array(
			'name'                       => _x( 'Features', 'Taxonomy General Name', 'roof21-core' ),
			'singular_name'              => _x( 'Feature', 'Taxonomy Singular Name', 'roof21-core' ),
			'menu_name'                  => __( 'Features', 'roof21-core' ),
			'all_items'                  => __( 'All Features', 'roof21-core' ),
			'parent_item'                => __( 'Parent Feature', 'roof21-core' ),
			'parent_item_colon'          => __( 'Parent Feature:', 'roof21-core' ),
			'new_item_name'              => __( 'New Feature Name', 'roof21-core' ),
			'add_new_item'               => __( 'Add New Feature', 'roof21-core' ),
			'edit_item'                  => __( 'Edit Feature', 'roof21-core' ),
			'update_item'                => __( 'Update Feature', 'roof21-core' ),
			'view_item'                  => __( 'View Feature', 'roof21-core' ),
			'separate_items_with_commas' => __( 'Separate features with commas', 'roof21-core' ),
			'add_or_remove_items'        => __( 'Add or remove features', 'roof21-core' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'roof21-core' ),
			'popular_items'              => __( 'Popular Features', 'roof21-core' ),
			'search_items'               => __( 'Search Features', 'roof21-core' ),
			'not_found'                  => __( 'Not Found', 'roof21-core' ),
			'no_terms'                   => __( 'No features', 'roof21-core' ),
			'items_list'                 => __( 'Features list', 'roof21-core' ),
			'items_list_navigation'      => __( 'Features list navigation', 'roof21-core' ),
		);

		$args = array(
			'labels'            => $labels,
			'hierarchical'      => false,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => false,
			'show_in_nav_menus' => false,
			'show_tagcloud'     => false,
			'show_in_rest'      => true,
			'rewrite'           => array(
				'slug'       => 'feature',
				'with_front' => false,
			),
		);

		register_taxonomy( 'roof21_feature', array( 'roof21_property' ), $args );
	}
}
