<?php
/**
 * Area Guide Custom Post Type.
 *
 * @package Roof21\Core\PostTypes
 */

namespace Roof21\Core\PostTypes;

/**
 * Area Guide CPT class.
 */
class AreaGuideCPT {

	/**
	 * Register post type.
	 *
	 * @since 1.0.0
	 */
	public static function register() {
		$labels = array(
			'name'                  => _x( 'Area Guides', 'Post Type General Name', 'roof21-core' ),
			'singular_name'         => _x( 'Area Guide', 'Post Type Singular Name', 'roof21-core' ),
			'menu_name'             => __( 'Area Guides', 'roof21-core' ),
			'name_admin_bar'        => __( 'Area Guide', 'roof21-core' ),
			'archives'              => __( 'Area Guide Archives', 'roof21-core' ),
			'attributes'            => __( 'Area Guide Attributes', 'roof21-core' ),
			'parent_item_colon'     => __( 'Parent Guide:', 'roof21-core' ),
			'all_items'             => __( 'All Area Guides', 'roof21-core' ),
			'add_new_item'          => __( 'Add New Area Guide', 'roof21-core' ),
			'add_new'               => __( 'Add New', 'roof21-core' ),
			'new_item'              => __( 'New Area Guide', 'roof21-core' ),
			'edit_item'             => __( 'Edit Area Guide', 'roof21-core' ),
			'update_item'           => __( 'Update Area Guide', 'roof21-core' ),
			'view_item'             => __( 'View Area Guide', 'roof21-core' ),
			'view_items'            => __( 'View Area Guides', 'roof21-core' ),
			'search_items'          => __( 'Search Area Guide', 'roof21-core' ),
			'not_found'             => __( 'Not found', 'roof21-core' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'roof21-core' ),
			'featured_image'        => __( 'Guide Image', 'roof21-core' ),
			'set_featured_image'    => __( 'Set guide image', 'roof21-core' ),
			'remove_featured_image' => __( 'Remove guide image', 'roof21-core' ),
			'use_featured_image'    => __( 'Use as guide image', 'roof21-core' ),
			'insert_into_item'      => __( 'Insert into guide', 'roof21-core' ),
			'uploaded_to_this_item' => __( 'Uploaded to this guide', 'roof21-core' ),
			'items_list'            => __( 'Area Guides list', 'roof21-core' ),
			'items_list_navigation' => __( 'Area Guides list navigation', 'roof21-core' ),
			'filter_items_list'     => __( 'Filter guides list', 'roof21-core' ),
		);

		$args = array(
			'label'               => __( 'Area Guide', 'roof21-core' ),
			'description'         => __( 'Location and area guides', 'roof21-core' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'page-attributes' ),
			'taxonomies'          => array( 'roof21_location' ),
			'hierarchical'        => true,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => 'edit.php?post_type=roof21_property',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => 'guides',
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'page',
			'show_in_rest'        => true,
			'rewrite'             => array(
				'slug'       => 'guide',
				'with_front' => false,
			),
		);

		register_post_type( 'roof21_area_guide', $args );
	}
}
