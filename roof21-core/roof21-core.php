<?php
/**
 * Plugin Name: ROOF21 Core
 * Plugin URI: https://roof21.co.th
 * Description: Complete Bitrix24 integration, property management, XML feeds, and watermarking system for ROOF21 real estate platform
 * Version: 1.0.0
 * Author: ROOF21 Development Team
 * Author URI: https://roof21.co.th
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: roof21-core
 * Domain Path: /languages
 * Requires at least: 6.4
 * Requires PHP: 8.1
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Plugin version.
 */
define( 'ROOF21_CORE_VERSION', '1.0.0' );

/**
 * Plugin root directory path.
 */
define( 'ROOF21_CORE_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Plugin root directory URL.
 */
define( 'ROOF21_CORE_URL', plugin_dir_url( __FILE__ ) );

/**
 * Plugin basename.
 */
define( 'ROOF21_CORE_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Activation hook.
 */
function roof21_core_activate() {
	require_once ROOF21_CORE_PATH . 'includes/class-activator.php';
	Roof21\Core\Activator::activate();
}
register_activation_hook( __FILE__, 'roof21_core_activate' );

/**
 * Deactivation hook.
 */
function roof21_core_deactivate() {
	require_once ROOF21_CORE_PATH . 'includes/class-deactivator.php';
	Roof21\Core\Deactivator::deactivate();
}
register_deactivation_hook( __FILE__, 'roof21_core_deactivate' );

/**
 * Autoloader for plugin classes.
 */
spl_autoload_register( function ( $class ) {
	// Only autoload classes in our namespace
	if ( strpos( $class, 'Roof21\\' ) !== 0 ) {
		return;
	}

	// Convert namespace to file path
	$class = str_replace( 'Roof21\\', '', $class );
	$class = str_replace( '\\', '/', $class );

	// Convert class name format
	$parts = explode( '/', $class );
	$class_name = 'class-' . strtolower( str_replace( '_', '-', array_pop( $parts ) ) ) . '.php';

	// Build file path
	$directories = array_map( 'strtolower', $parts );
	$file = ROOF21_CORE_PATH . 'includes/' . implode( '/', $directories ) . '/' . $class_name;

	if ( file_exists( $file ) ) {
		require_once $file;
	}
} );

/**
 * Load plugin textdomain.
 */
function roof21_core_load_textdomain() {
	load_plugin_textdomain(
		'roof21-core',
		false,
		dirname( ROOF21_CORE_BASENAME ) . '/languages'
	);
}
add_action( 'plugins_loaded', 'roof21_core_load_textdomain' );

/**
 * Initialize the plugin.
 */
function roof21_core_init() {
	require_once ROOF21_CORE_PATH . 'includes/class-core.php';

	$plugin = new Roof21\Core\Core();
	$plugin->run();
}
add_action( 'plugins_loaded', 'roof21_core_init' );

/**
 * Helper function to get plugin instance.
 */
function roof21_core() {
	return Roof21\Core\Core::instance();
}
