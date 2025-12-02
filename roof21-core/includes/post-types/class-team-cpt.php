<?php
/**
 * Team Member Custom Post Type.
 *
 * @package Roof21\Core\PostTypes
 */

namespace Roof21\Core\PostTypes;

/**
 * Team CPT class.
 */
class TeamCPT {

	/**
	 * Register post type.
	 *
	 * @since 1.0.0
	 */
	public static function register() {
		$labels = array(
			'name'                  => _x( 'Team Members', 'Post Type General Name', 'roof21-core' ),
			'singular_name'         => _x( 'Team Member', 'Post Type Singular Name', 'roof21-core' ),
			'menu_name'             => __( 'Team', 'roof21-core' ),
			'name_admin_bar'        => __( 'Team Member', 'roof21-core' ),
			'archives'              => __( 'Team Archives', 'roof21-core' ),
			'attributes'            => __( 'Team Member Attributes', 'roof21-core' ),
			'parent_item_colon'     => __( 'Parent Member:', 'roof21-core' ),
			'all_items'             => __( 'All Team Members', 'roof21-core' ),
			'add_new_item'          => __( 'Add New Team Member', 'roof21-core' ),
			'add_new'               => __( 'Add New', 'roof21-core' ),
			'new_item'              => __( 'New Team Member', 'roof21-core' ),
			'edit_item'             => __( 'Edit Team Member', 'roof21-core' ),
			'update_item'           => __( 'Update Team Member', 'roof21-core' ),
			'view_item'             => __( 'View Team Member', 'roof21-core' ),
			'view_items'            => __( 'View Team Members', 'roof21-core' ),
			'search_items'          => __( 'Search Team Member', 'roof21-core' ),
			'not_found'             => __( 'Not found', 'roof21-core' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'roof21-core' ),
			'featured_image'        => __( 'Team Member Photo', 'roof21-core' ),
			'set_featured_image'    => __( 'Set member photo', 'roof21-core' ),
			'remove_featured_image' => __( 'Remove member photo', 'roof21-core' ),
			'use_featured_image'    => __( 'Use as member photo', 'roof21-core' ),
			'insert_into_item'      => __( 'Insert into member', 'roof21-core' ),
			'uploaded_to_this_item' => __( 'Uploaded to this member', 'roof21-core' ),
			'items_list'            => __( 'Team members list', 'roof21-core' ),
			'items_list_navigation' => __( 'Team members list navigation', 'roof21-core' ),
			'filter_items_list'     => __( 'Filter team members list', 'roof21-core' ),
		);

		$args = array(
			'label'               => __( 'Team Member', 'roof21-core' ),
			'description'         => __( 'Team members and staff', 'roof21-core' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => 'edit.php?post_type=roof21_property',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => 'team',
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
			'show_in_rest'        => true,
			'rewrite'             => array(
				'slug'       => 'team',
				'with_front' => false,
			),
		);

		register_post_type( 'roof21_team', $args );
	}
}
