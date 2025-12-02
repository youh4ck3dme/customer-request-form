<?php
/**
 * Property Custom Post Type.
 *
 * @package Roof21\Core\PostTypes
 */

namespace Roof21\Core\PostTypes;

/**
 * Property CPT class.
 */
class PropertyCPT {

	/**
	 * Register post type.
	 *
	 * @since 1.0.0
	 */
	public static function register() {
		$labels = array(
			'name'                  => _x( 'Properties', 'Post Type General Name', 'roof21-core' ),
			'singular_name'         => _x( 'Property', 'Post Type Singular Name', 'roof21-core' ),
			'menu_name'             => __( 'Properties', 'roof21-core' ),
			'name_admin_bar'        => __( 'Property', 'roof21-core' ),
			'archives'              => __( 'Property Archives', 'roof21-core' ),
			'attributes'            => __( 'Property Attributes', 'roof21-core' ),
			'parent_item_colon'     => __( 'Parent Property:', 'roof21-core' ),
			'all_items'             => __( 'All Properties', 'roof21-core' ),
			'add_new_item'          => __( 'Add New Property', 'roof21-core' ),
			'add_new'               => __( 'Add New', 'roof21-core' ),
			'new_item'              => __( 'New Property', 'roof21-core' ),
			'edit_item'             => __( 'Edit Property', 'roof21-core' ),
			'update_item'           => __( 'Update Property', 'roof21-core' ),
			'view_item'             => __( 'View Property', 'roof21-core' ),
			'view_items'            => __( 'View Properties', 'roof21-core' ),
			'search_items'          => __( 'Search Property', 'roof21-core' ),
			'not_found'             => __( 'Not found', 'roof21-core' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'roof21-core' ),
			'featured_image'        => __( 'Featured Image', 'roof21-core' ),
			'set_featured_image'    => __( 'Set featured image', 'roof21-core' ),
			'remove_featured_image' => __( 'Remove featured image', 'roof21-core' ),
			'use_featured_image'    => __( 'Use as featured image', 'roof21-core' ),
			'insert_into_item'      => __( 'Insert into property', 'roof21-core' ),
			'uploaded_to_this_item' => __( 'Uploaded to this property', 'roof21-core' ),
			'items_list'            => __( 'Properties list', 'roof21-core' ),
			'items_list_navigation' => __( 'Properties list navigation', 'roof21-core' ),
			'filter_items_list'     => __( 'Filter properties list', 'roof21-core' ),
		);

		$args = array(
			'label'               => __( 'Property', 'roof21-core' ),
			'description'         => __( 'Real estate properties', 'roof21-core' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'thumbnail', 'revisions', 'author' ),
			'taxonomies'          => array( 'roof21_location', 'roof21_property_type', 'roof21_ownership_type', 'roof21_feature', 'roof21_listing_type', 'roof21_country' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-admin-home',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => 'properties',
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
			'show_in_rest'        => true,
			'rewrite'             => array(
				'slug'       => 'property',
				'with_front' => false,
			),
		);

		register_post_type( 'roof21_property', $args );
	}
}
