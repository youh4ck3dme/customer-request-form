<?php
/**
 * Country Taxonomy (for International Properties).
 *
 * @package Roof21\Core\Taxonomies
 */

namespace Roof21\Core\Taxonomies;

/**
 * Country Taxonomy class.
 */
class CountryTaxonomy {

	/**
	 * Register taxonomy.
	 *
	 * @since 1.0.0
	 */
	public static function register() {
		$labels = array(
			'name'                       => _x( 'Countries', 'Taxonomy General Name', 'roof21-core' ),
			'singular_name'              => _x( 'Country', 'Taxonomy Singular Name', 'roof21-core' ),
			'menu_name'                  => __( 'Countries', 'roof21-core' ),
			'all_items'                  => __( 'All Countries', 'roof21-core' ),
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'new_item_name'              => __( 'New Country Name', 'roof21-core' ),
			'add_new_item'               => __( 'Add New Country', 'roof21-core' ),
			'edit_item'                  => __( 'Edit Country', 'roof21-core' ),
			'update_item'                => __( 'Update Country', 'roof21-core' ),
			'view_item'                  => __( 'View Country', 'roof21-core' ),
			'separate_items_with_commas' => __( 'Separate countries with commas', 'roof21-core' ),
			'add_or_remove_items'        => __( 'Add or remove countries', 'roof21-core' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'roof21-core' ),
			'popular_items'              => __( 'Popular Countries', 'roof21-core' ),
			'search_items'               => __( 'Search Countries', 'roof21-core' ),
			'not_found'                  => __( 'Not Found', 'roof21-core' ),
			'no_terms'                   => __( 'No countries', 'roof21-core' ),
			'items_list'                 => __( 'Countries list', 'roof21-core' ),
			'items_list_navigation'      => __( 'Countries list navigation', 'roof21-core' ),
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
				'slug'       => 'country',
				'with_front' => false,
			),
		);

		register_taxonomy( 'roof21_country', array( 'roof21_property' ), $args );
	}
}
