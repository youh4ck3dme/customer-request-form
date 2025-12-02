<?php
/**
 * Main plugin core class.
 *
 * @package Roof21\Core
 */

namespace Roof21\Core;

/**
 * Main Core class.
 */
class Core {

	/**
	 * Plugin instance.
	 *
	 * @var Core
	 */
	private static $instance = null;

	/**
	 * Loader instance.
	 *
	 * @var Loader
	 */
	protected $loader;

	/**
	 * Get plugin instance.
	 *
	 * @since 1.0.0
	 * @return Core
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->load_dependencies();
		$this->define_hooks();
	}

	/**
	 * Load required dependencies.
	 *
	 * @since 1.0.0
	 */
	private function load_dependencies() {
		// Helper functions
		require_once ROOF21_CORE_PATH . 'includes/helpers/functions.php';

		// Post types
		require_once ROOF21_CORE_PATH . 'includes/post-types/class-property-cpt.php';
		require_once ROOF21_CORE_PATH . 'includes/post-types/class-development-cpt.php';
		require_once ROOF21_CORE_PATH . 'includes/post-types/class-area-guide-cpt.php';
		require_once ROOF21_CORE_PATH . 'includes/post-types/class-team-cpt.php';

		// Taxonomies
		require_once ROOF21_CORE_PATH . 'includes/taxonomies/class-location-taxonomy.php';
		require_once ROOF21_CORE_PATH . 'includes/taxonomies/class-property-type-taxonomy.php';
		require_once ROOF21_CORE_PATH . 'includes/taxonomies/class-ownership-type-taxonomy.php';
		require_once ROOF21_CORE_PATH . 'includes/taxonomies/class-feature-taxonomy.php';
		require_once ROOF21_CORE_PATH . 'includes/taxonomies/class-listing-type-taxonomy.php';
		require_once ROOF21_CORE_PATH . 'includes/taxonomies/class-country-taxonomy.php';

		// Bitrix24
		require_once ROOF21_CORE_PATH . 'includes/bitrix24/class-bitrix24-api.php';
		require_once ROOF21_CORE_PATH . 'includes/bitrix24/class-bitrix24-sync.php';
		require_once ROOF21_CORE_PATH . 'includes/bitrix24/class-bitrix24-webhook.php';
		require_once ROOF21_CORE_PATH . 'includes/bitrix24/class-bitrix24-forms.php';

		// Feeds
		require_once ROOF21_CORE_PATH . 'includes/feeds/class-feed-base.php';
		require_once ROOF21_CORE_PATH . 'includes/feeds/class-proppit-feed.php';
		require_once ROOF21_CORE_PATH . 'includes/feeds/class-featured-feed.php';
		require_once ROOF21_CORE_PATH . 'includes/feeds/class-condos-feed.php';

		// Watermark
		require_once ROOF21_CORE_PATH . 'includes/watermark/class-watermark-processor.php';

		// Helpers
		require_once ROOF21_CORE_PATH . 'includes/helpers/class-currency.php';
		require_once ROOF21_CORE_PATH . 'includes/helpers/class-language.php';

		// Admin
		require_once ROOF21_CORE_PATH . 'admin/class-admin.php';
		require_once ROOF21_CORE_PATH . 'admin/class-settings.php';
		require_once ROOF21_CORE_PATH . 'admin/class-meta-boxes.php';

		// Public
		require_once ROOF21_CORE_PATH . 'public/class-public.php';
		require_once ROOF21_CORE_PATH . 'public/class-shortcodes.php';
		require_once ROOF21_CORE_PATH . 'public/class-ajax.php';
	}

	/**
	 * Define hooks.
	 *
	 * @since 1.0.0
	 */
	private function define_hooks() {
		// Register post types
		add_action( 'init', array( 'Roof21\Core\PostTypes\PropertyCPT', 'register' ) );
		add_action( 'init', array( 'Roof21\Core\PostTypes\DevelopmentCPT', 'register' ) );
		add_action( 'init', array( 'Roof21\Core\PostTypes\AreaGuideCPT', 'register' ) );
		add_action( 'init', array( 'Roof21\Core\PostTypes\TeamCPT', 'register' ) );

		// Register taxonomies
		add_action( 'init', array( 'Roof21\Core\Taxonomies\LocationTaxonomy', 'register' ) );
		add_action( 'init', array( 'Roof21\Core\Taxonomies\PropertyTypeTaxonomy', 'register' ) );
		add_action( 'init', array( 'Roof21\Core\Taxonomies\OwnershipTypeTaxonomy', 'register' ) );
		add_action( 'init', array( 'Roof21\Core\Taxonomies\FeatureTaxonomy', 'register' ) );
		add_action( 'init', array( 'Roof21\Core\Taxonomies\ListingTypeTaxonomy', 'register' ) );
		add_action( 'init', array( 'Roof21\Core\Taxonomies\CountryTaxonomy', 'register' ) );

		// Initialize components
		new Admin\Admin();
		new Admin\Settings();
		new Admin\MetaBoxes();
		new PublicFrontend\PublicFrontend();
		new PublicFrontend\Shortcodes();
		new PublicFrontend\Ajax();

		// Bitrix24
		$bitrix24_sync = new Bitrix24\Bitrix24Sync();
		add_action( 'roof21_sync_properties', array( $bitrix24_sync, 'sync_properties' ) );

		$bitrix24_webhook = new Bitrix24\Bitrix24Webhook();
		add_action( 'rest_api_init', array( $bitrix24_webhook, 'register_routes' ) );

		// Feeds
		add_action( 'init', array( 'Roof21\Core\Feeds\ProppitFeed', 'register_endpoint' ) );
		add_action( 'init', array( 'Roof21\Core\Feeds\FeaturedFeed', 'register_endpoint' ) );
		add_action( 'init', array( 'Roof21\Core\Feeds\CondosFeed', 'register_endpoint' ) );

		// Watermark
		$watermark = new Watermark\WatermarkProcessor();
		add_filter( 'wp_handle_upload', array( $watermark, 'process_upload' ) );

		// Currency helper
		$currency = new Helpers\Currency();
		add_action( 'roof21_update_exchange_rates', array( $currency, 'update_rates' ) );

		// Enqueue scripts and styles
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_public_assets' ) );
	}

	/**
	 * Enqueue admin assets.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_admin_assets() {
		wp_enqueue_style(
			'roof21-admin',
			ROOF21_CORE_URL . 'admin/css/admin.css',
			array(),
			ROOF21_CORE_VERSION
		);

		wp_enqueue_script(
			'roof21-admin',
			ROOF21_CORE_URL . 'admin/js/admin.js',
			array( 'jquery' ),
			ROOF21_CORE_VERSION,
			true
		);

		wp_localize_script(
			'roof21-admin',
			'roof21Admin',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'roof21_admin' ),
			)
		);
	}

	/**
	 * Enqueue public assets.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_public_assets() {
		wp_enqueue_style(
			'roof21-public',
			ROOF21_CORE_URL . 'public/css/public.css',
			array(),
			ROOF21_CORE_VERSION
		);

		wp_enqueue_script(
			'roof21-search',
			ROOF21_CORE_URL . 'public/js/search.js',
			array( 'jquery' ),
			ROOF21_CORE_VERSION,
			true
		);

		wp_enqueue_script(
			'roof21-currency-switcher',
			ROOF21_CORE_URL . 'public/js/currency-switcher.js',
			array( 'jquery' ),
			ROOF21_CORE_VERSION,
			true
		);

		wp_enqueue_script(
			'roof21-language-switcher',
			ROOF21_CORE_URL . 'public/js/language-switcher.js',
			array( 'jquery' ),
			ROOF21_CORE_VERSION,
			true
		);

		wp_localize_script(
			'roof21-search',
			'roof21Public',
			array(
				'ajaxUrl'          => admin_url( 'admin-ajax.php' ),
				'nonce'            => wp_create_nonce( 'roof21_public' ),
				'currentCurrency'  => roof21_get_current_currency(),
				'currentLanguage'  => roof21_get_current_language(),
			)
		);
	}

	/**
	 * Run the plugin.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		// Plugin is loaded and ready
		do_action( 'roof21_core_loaded' );
	}
}
