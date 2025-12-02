<?php
/**
 * Settings page handler.
 *
 * @package Roof21\Core\Admin
 */

namespace Roof21\Core\Admin;

/**
 * Settings class.
 */
class Settings {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Add settings page.
	 *
	 * @since 1.0.0
	 */
	public function add_settings_page() {
		add_submenu_page(
			'roof21-core',
			__( 'Settings', 'roof21-core' ),
			__( 'Settings', 'roof21-core' ),
			'manage_options',
			'roof21-core-settings',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Register settings.
	 *
	 * @since 1.0.0
	 */
	public function register_settings() {
		// Bitrix24 settings
		register_setting( 'roof21_bitrix24_settings', 'roof21_bitrix24_webhook_url' );
		register_setting( 'roof21_bitrix24_settings', 'roof21_sync_interval' );
		register_setting( 'roof21_bitrix24_settings', 'roof21_sync_enabled' );

		// Watermark settings
		register_setting( 'roof21_watermark_settings', 'roof21_watermark_enabled' );
		register_setting( 'roof21_watermark_settings', 'roof21_watermark_position' );
		register_setting( 'roof21_watermark_settings', 'roof21_watermark_opacity' );

		// Currency settings
		register_setting( 'roof21_currency_settings', 'roof21_default_currency' );
		register_setting( 'roof21_currency_settings', 'roof21_exchange_rates' );
	}

	/**
	 * Render settings page.
	 *
	 * @since 1.0.0
	 */
	public function render_settings_page() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'ROOF21 Core Settings', 'roof21-core' ); ?></h1>
			<?php settings_errors(); ?>

			<h2 class="nav-tab-wrapper">
				<a href="?page=roof21-core-settings&tab=bitrix24" class="nav-tab"><?php esc_html_e( 'Bitrix24', 'roof21-core' ); ?></a>
				<a href="?page=roof21-core-settings&tab=watermark" class="nav-tab"><?php esc_html_e( 'Watermark', 'roof21-core' ); ?></a>
				<a href="?page=roof21-core-settings&tab=currency" class="nav-tab"><?php esc_html_e( 'Currency', 'roof21-core' ); ?></a>
			</h2>

			<form method="post" action="options.php">
				<?php
				$tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'bitrix24';

				if ( 'bitrix24' === $tab ) {
					settings_fields( 'roof21_bitrix24_settings' );
					do_settings_sections( 'roof21_bitrix24_settings' );
					$this->render_bitrix24_settings();
				} elseif ( 'watermark' === $tab ) {
					settings_fields( 'roof21_watermark_settings' );
					do_settings_sections( 'roof21_watermark_settings' );
					$this->render_watermark_settings();
				} elseif ( 'currency' === $tab ) {
					settings_fields( 'roof21_currency_settings' );
					do_settings_sections( 'roof21_currency_settings' );
					$this->render_currency_settings();
				}

				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Render Bitrix24 settings.
	 *
	 * @since 1.0.0
	 */
	private function render_bitrix24_settings() {
		$webhook_url = get_option( 'roof21_bitrix24_webhook_url', '' );
		$sync_interval = get_option( 'roof21_sync_interval', 'hourly' );
		$sync_enabled = get_option( 'roof21_sync_enabled', '1' );
		?>
		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e( 'Webhook URL', 'roof21-core' ); ?></th>
				<td>
					<input type="url" name="roof21_bitrix24_webhook_url" value="<?php echo esc_url( $webhook_url ); ?>" class="regular-text">
					<p class="description"><?php esc_html_e( 'Enter your Bitrix24 webhook URL', 'roof21-core' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Sync Interval', 'roof21-core' ); ?></th>
				<td>
					<select name="roof21_sync_interval">
						<option value="hourly" <?php selected( $sync_interval, 'hourly' ); ?>><?php esc_html_e( 'Hourly', 'roof21-core' ); ?></option>
						<option value="twicedaily" <?php selected( $sync_interval, 'twicedaily' ); ?>><?php esc_html_e( 'Twice Daily', 'roof21-core' ); ?></option>
						<option value="daily" <?php selected( $sync_interval, 'daily' ); ?>><?php esc_html_e( 'Daily', 'roof21-core' ); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Enable Sync', 'roof21-core' ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="roof21_sync_enabled" value="1" <?php checked( $sync_enabled, '1' ); ?>>
						<?php esc_html_e( 'Enable automatic sync', 'roof21-core' ); ?>
					</label>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Render watermark settings.
	 *
	 * @since 1.0.0
	 */
	private function render_watermark_settings() {
		?>
		<p><?php esc_html_e( 'Watermark settings will be displayed here.', 'roof21-core' ); ?></p>
		<?php
	}

	/**
	 * Render currency settings.
	 *
	 * @since 1.0.0
	 */
	private function render_currency_settings() {
		?>
		<p><?php esc_html_e( 'Currency settings will be displayed here.', 'roof21-core' ); ?></p>
		<?php
	}
}
