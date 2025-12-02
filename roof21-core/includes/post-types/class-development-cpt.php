<?php
/**
 * Development Custom Post Type.
 *
 * @package Roof21\Core\PostTypes
 */

namespace Roof21\Core\PostTypes;

/**
 * Development CPT class.
 */
class DevelopmentCPT {

	/**
	 * Register post type.
	 *
	 * @since 1.0.0
	 */
	public static function register() {
		$labels = array(
			'name'                  => _x( 'Developments', 'Post Type General Name', 'roof21-core' ),
			'singular_name'         => _x( 'Development', 'Post Type Singular Name', 'roof21-core' ),
			'menu_name'             => __( 'Developments', 'roof21-core' ),
			'name_admin_bar'        => __( 'Development', 'roof21-core' ),
			'archives'              => __( 'Development Archives', 'roof21-core' ),
			'attributes'            => __( 'Development Attributes', 'roof21-core' ),
			'parent_item_colon'     => __( 'Parent Development:', 'roof21-core' ),
			'all_items'             => __( 'All Developments', 'roof21-core' ),
			'add_new_item'          => __( 'Add New Development', 'roof21-core' ),
			'add_new'               => __( 'Add New', 'roof21-core' ),
			'new_item'              => __( 'New Development', 'roof21-core' ),
			'edit_item'             => __( 'Edit Development', 'roof21-core' ),
			'update_item'           => __( 'Update Development', 'roof21-core' ),
			'view_item'             => __( 'View Development', 'roof21-core' ),
			'view_items'            => __( 'View Developments', 'roof21-core' ),
			'search_items'          => __( 'Search Development', 'roof21-core' ),
			'not_found'             => __( 'Not found', 'roof21-core' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'roof21-core' ),
			'featured_image'        => __( 'Development Image', 'roof21-core' ),
			'set_featured_image'    => __( 'Set development image', 'roof21-core' ),
			'remove_featured_image' => __( 'Remove development image', 'roof21-core' ),
			'use_featured_image'    => __( 'Use as development image', 'roof21-core' ),
			'insert_into_item'      => __( 'Insert into development', 'roof21-core' ),
			'uploaded_to_this_item' => __( 'Uploaded to this development', 'roof21-core' ),
			'items_list'            => __( 'Developments list', 'roof21-core' ),
			'items_list_navigation' => __( 'Developments list navigation', 'roof21-core' ),
			'filter_items_list'     => __( 'Filter developments list', 'roof21-core' ),
		);

		$args = array(
			'label'               => __( 'Development', 'roof21-core' ),
			'description'         => __( 'New development projects', 'roof21-core' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ),
			'taxonomies'          => array( 'roof21_location', 'roof21_property_type' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => 'edit.php?post_type=roof21_property',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => 'developments',
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
			'show_in_rest'        => true,
			'rewrite'             => array(
				'slug'       => 'development',
				'with_front' => false,
			),
		);

		register_post_type( 'roof21_development', $args );
	}
}
