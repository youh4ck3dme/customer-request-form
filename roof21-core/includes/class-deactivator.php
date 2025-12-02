<?php
/**
 * Plugin deactivation class.
 *
 * @package Roof21\Core
 */

namespace Roof21\Core;

/**
 * Deactivation class.
 */
class Deactivator {

	/**
	 * Deactivate the plugin.
	 *
	 * @since 1.0.0
	 */
	public static function deactivate() {
		// Unschedule cron events
		self::unschedule_cron_events();

		// Flush rewrite rules
		flush_rewrite_rules();
	}

	/**
	 * Unschedule cron events.
	 *
	 * @since 1.0.0
	 */
	private static function unschedule_cron_events() {
		$timestamp = wp_next_scheduled( 'roof21_sync_properties' );
		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, 'roof21_sync_properties' );
		}

		$timestamp = wp_next_scheduled( 'roof21_update_exchange_rates' );
		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, 'roof21_update_exchange_rates' );
		}
	}
}
