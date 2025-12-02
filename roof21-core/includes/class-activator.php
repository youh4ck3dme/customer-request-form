<?php
/**
 * Plugin activation class.
 *
 * @package Roof21\Core
 */

namespace Roof21\Core;

/**
 * Activation class.
 */
class Activator {

	/**
	 * Activate the plugin.
	 *
	 * @since 1.0.0
	 */
	public static function activate() {
		// Set default options
		self::set_default_options();

		// Create custom database tables
		self::create_tables();

		// Register post types and taxonomies for flush_rewrite_rules()
		self::register_post_types_temp();
		self::register_taxonomies_temp();

		// Flush rewrite rules
		flush_rewrite_rules();

		// Schedule cron events
		self::schedule_cron_events();

		// Set activation flag
		add_option( 'roof21_core_activated', time() );
		add_option( 'roof21_core_version', ROOF21_CORE_VERSION );
	}

	/**
	 * Set default plugin options.
	 *
	 * @since 1.0.0
	 */
	private static function set_default_options() {
		$defaults = array(
			'roof21_bitrix24_webhook_url'    => '',
			'roof21_bitrix24_client_id'      => '',
			'roof21_bitrix24_client_secret'  => '',
			'roof21_sync_interval'            => 'hourly',
			'roof21_sync_enabled'             => '1',
			'roof21_watermark_enabled'        => '1',
			'roof21_watermark_position'       => 'bottom-right',
			'roof21_watermark_opacity'        => '70',
			'roof21_watermark_padding'        => '20',
			'roof21_default_currency'         => 'THB',
			'roof21_supported_currencies'     => array( 'THB', 'USD', 'EUR' ),
			'roof21_exchange_rates'           => array(
				'THB' => 1,
				'USD' => 0.028,
				'EUR' => 0.026,
			),
			'roof21_properties_per_page'      => 12,
			'roof21_feed_cache_duration'      => 900, // 15 minutes
		);

		foreach ( $defaults as $key => $value ) {
			if ( get_option( $key ) === false ) {
				add_option( $key, $value );
			}
		}
	}

	/**
	 * Create custom database tables.
	 *
	 * @since 1.0.0
	 */
	private static function create_tables() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		// Sync log table
		$sync_log_table = $wpdb->prefix . 'roof21_sync_log';

		$sql_sync_log = "CREATE TABLE IF NOT EXISTS $sync_log_table (
			id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			sync_type varchar(50) NOT NULL,
			status varchar(20) NOT NULL,
			properties_added int(11) DEFAULT 0,
			properties_updated int(11) DEFAULT 0,
			properties_failed int(11) DEFAULT 0,
			message text,
			started_at datetime NOT NULL,
			completed_at datetime,
			PRIMARY KEY  (id),
			KEY sync_type (sync_type),
			KEY status (status),
			KEY started_at (started_at)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql_sync_log );

		// Add indexes to postmeta for performance
		$wpdb->query( "CREATE INDEX IF NOT EXISTS idx_roof21_bitrix_id ON {$wpdb->postmeta} (meta_key, meta_value(20)) WHERE meta_key = '_roof21_bitrix_id'" );
		$wpdb->query( "CREATE INDEX IF NOT EXISTS idx_roof21_reference ON {$wpdb->postmeta} (meta_key, meta_value(20)) WHERE meta_key = '_roof21_reference_code'" );
		$wpdb->query( "CREATE INDEX IF NOT EXISTS idx_roof21_featured ON {$wpdb->postmeta} (meta_key, meta_value(1)) WHERE meta_key = '_roof21_featured'" );
	}

	/**
	 * Register post types temporarily for activation.
	 *
	 * @since 1.0.0
	 */
	private static function register_post_types_temp() {
		register_post_type( 'roof21_property', array( 'public' => true ) );
		register_post_type( 'roof21_development', array( 'public' => true ) );
		register_post_type( 'roof21_area_guide', array( 'public' => true ) );
		register_post_type( 'roof21_team', array( 'public' => true ) );
	}

	/**
	 * Register taxonomies temporarily for activation.
	 *
	 * @since 1.0.0
	 */
	private static function register_taxonomies_temp() {
		register_taxonomy( 'roof21_location', 'roof21_property', array( 'public' => true ) );
		register_taxonomy( 'roof21_property_type', 'roof21_property', array( 'public' => true ) );
		register_taxonomy( 'roof21_ownership_type', 'roof21_property', array( 'public' => true ) );
		register_taxonomy( 'roof21_feature', 'roof21_property', array( 'public' => true ) );
		register_taxonomy( 'roof21_listing_type', 'roof21_property', array( 'public' => true ) );
		register_taxonomy( 'roof21_country', 'roof21_property', array( 'public' => true ) );
	}

	/**
	 * Schedule cron events.
	 *
	 * @since 1.0.0
	 */
	private static function schedule_cron_events() {
		if ( ! wp_next_scheduled( 'roof21_sync_properties' ) ) {
			wp_schedule_event( time(), 'hourly', 'roof21_sync_properties' );
		}

		if ( ! wp_next_scheduled( 'roof21_update_exchange_rates' ) ) {
			wp_schedule_event( time(), 'daily', 'roof21_update_exchange_rates' );
		}
	}
}
