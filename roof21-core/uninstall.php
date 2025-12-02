<?php
/**
 * Plugin uninstall script.
 *
 * @package Roof21\Core
 */

// If uninstall not called from WordPress, exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

// Check if we should delete all data
$delete_data = get_option( 'roof21_delete_data_on_uninstall', false );

if ( $delete_data ) {
	// Delete all properties
	$properties = get_posts( array(
		'post_type'      => 'roof21_property',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	) );

	foreach ( $properties as $property_id ) {
		wp_delete_post( $property_id, true );
	}

	// Delete all developments
	$developments = get_posts( array(
		'post_type'      => 'roof21_development',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	) );

	foreach ( $developments as $development_id ) {
		wp_delete_post( $development_id, true );
	}

	// Delete all area guides
	$guides = get_posts( array(
		'post_type'      => 'roof21_area_guide',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	) );

	foreach ( $guides as $guide_id ) {
		wp_delete_post( $guide_id, true );
	}

	// Delete all team members
	$team = get_posts( array(
		'post_type'      => 'roof21_team',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	) );

	foreach ( $team as $member_id ) {
		wp_delete_post( $member_id, true );
	}

	// Delete taxonomies
	$taxonomies = array(
		'roof21_location',
		'roof21_property_type',
		'roof21_ownership_type',
		'roof21_feature',
		'roof21_listing_type',
		'roof21_country',
	);

	foreach ( $taxonomies as $taxonomy ) {
		$terms = get_terms( array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => false,
		) );

		if ( ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				wp_delete_term( $term->term_id, $taxonomy );
			}
		}
	}

	// Delete custom tables
	$sync_log_table = $wpdb->prefix . 'roof21_sync_log';
	$wpdb->query( "DROP TABLE IF EXISTS $sync_log_table" );

	// Delete all plugin options
	$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'roof21_%'" );

	// Delete all transients
	$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_roof21_%'" );
	$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_timeout_roof21_%'" );

	// Clear any cached data
	wp_cache_flush();
}

// Unschedule cron events
$timestamp = wp_next_scheduled( 'roof21_sync_properties' );
if ( $timestamp ) {
	wp_unschedule_event( $timestamp, 'roof21_sync_properties' );
}

$timestamp = wp_next_scheduled( 'roof21_update_exchange_rates' );
if ( $timestamp ) {
	wp_unschedule_event( $timestamp, 'roof21_update_exchange_rates' );
}
