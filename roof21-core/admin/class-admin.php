<?php
/**
 * Admin functionality.
 *
 * @package Roof21\Core\Admin
 */

namespace Roof21\Core\Admin;

/**
 * Admin class.
 */
class Admin {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );
		add_filter( 'plugin_action_links_' . ROOF21_CORE_BASENAME, array( $this, 'add_settings_link' ) );
		add_action( 'admin_post_roof21_manual_sync', array( $this, 'handle_manual_sync' ) );
	}

	/**
	 * Add plugin admin menu.
	 *
	 * @since 1.0.0
	 */
	public function add_plugin_admin_menu() {
		add_menu_page(
			__( 'ROOF21 Core', 'roof21-core' ),
			__( 'ROOF21 Core', 'roof21-core' ),
			'manage_options',
			'roof21-core',
			array( $this, 'display_sync_status' ),
			'dashicons-building',
			30
		);

		add_submenu_page(
			'roof21-core',
			__( 'Sync Status', 'roof21-core' ),
			__( 'Sync Status', 'roof21-core' ),
			'manage_options',
			'roof21-core',
			array( $this, 'display_sync_status' )
		);

		add_submenu_page(
			'roof21-core',
			__( 'Sync Logs', 'roof21-core' ),
			__( 'Sync Logs', 'roof21-core' ),
			'manage_options',
			'roof21-sync-logs',
			array( $this, 'display_sync_logs' )
		);
	}

	/**
	 * Add settings link to plugins page.
	 *
	 * @since 1.0.0
	 * @param array $links Plugin action links.
	 * @return array Modified links.
	 */
	public function add_settings_link( $links ) {
		$settings_link = sprintf(
			'<a href="%s">%s</a>',
			admin_url( 'admin.php?page=roof21-core-settings' ),
			__( 'Settings', 'roof21-core' )
		);

		array_unshift( $links, $settings_link );

		return $links;
	}

	/**
	 * Display sync status page.
	 *
	 * @since 1.0.0
	 */
	public function display_sync_status() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'roof21-core' ) );
		}

		// Get latest sync log
		$logs = roof21_get_sync_logs( array( 'limit' => 1 ) );
		$last_sync = ! empty( $logs ) ? $logs[0] : null;

		// Get property count
		$property_count = wp_count_posts( 'roof21_property' );

		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'ROOF21 Sync Status', 'roof21-core' ); ?></h1>

			<div class="roof21-sync-status">
				<div class="roof21-card">
					<h2><?php esc_html_e( 'Property Statistics', 'roof21-core' ); ?></h2>
					<p>
						<strong><?php esc_html_e( 'Total Properties:', 'roof21-core' ); ?></strong>
						<?php echo esc_html( $property_count->publish ?? 0 ); ?>
					</p>
					<p>
						<strong><?php esc_html_e( 'Drafts:', 'roof21-core' ); ?></strong>
						<?php echo esc_html( $property_count->draft ?? 0 ); ?>
					</p>
				</div>

				<?php if ( $last_sync ) : ?>
				<div class="roof21-card">
					<h2><?php esc_html_e( 'Last Sync', 'roof21-core' ); ?></h2>
					<p>
						<strong><?php esc_html_e( 'Date:', 'roof21-core' ); ?></strong>
						<?php echo esc_html( $last_sync['started_at'] ); ?>
					</p>
					<p>
						<strong><?php esc_html_e( 'Status:', 'roof21-core' ); ?></strong>
						<span class="sync-status-<?php echo esc_attr( $last_sync['status'] ); ?>">
							<?php echo esc_html( ucfirst( $last_sync['status'] ) ); ?>
						</span>
					</p>
					<p>
						<strong><?php esc_html_e( 'Added:', 'roof21-core' ); ?></strong>
						<?php echo esc_html( $last_sync['properties_added'] ); ?>
					</p>
					<p>
						<strong><?php esc_html_e( 'Updated:', 'roof21-core' ); ?></strong>
						<?php echo esc_html( $last_sync['properties_updated'] ); ?>
					</p>
					<p>
						<strong><?php esc_html_e( 'Failed:', 'roof21-core' ); ?></strong>
						<?php echo esc_html( $last_sync['properties_failed'] ); ?>
					</p>
				</div>
				<?php endif; ?>

				<div class="roof21-card">
					<h2><?php esc_html_e( 'Manual Sync', 'roof21-core' ); ?></h2>
					<p><?php esc_html_e( 'Manually trigger a sync from Bitrix24.', 'roof21-core' ); ?></p>
					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
						<?php wp_nonce_field( 'roof21_manual_sync', 'roof21_nonce' ); ?>
						<input type="hidden" name="action" value="roof21_manual_sync">
						<?php submit_button( __( 'Sync Now', 'roof21-core' ), 'primary', 'submit', false ); ?>
					</form>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Display sync logs page.
	 *
	 * @since 1.0.0
	 */
	public function display_sync_logs() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'roof21-core' ) );
		}

		$logs = roof21_get_sync_logs( array( 'limit' => 50 ) );

		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Sync Logs', 'roof21-core' ); ?></h1>

			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Date', 'roof21-core' ); ?></th>
						<th><?php esc_html_e( 'Type', 'roof21-core' ); ?></th>
						<th><?php esc_html_e( 'Status', 'roof21-core' ); ?></th>
						<th><?php esc_html_e( 'Added', 'roof21-core' ); ?></th>
						<th><?php esc_html_e( 'Updated', 'roof21-core' ); ?></th>
						<th><?php esc_html_e( 'Failed', 'roof21-core' ); ?></th>
						<th><?php esc_html_e( 'Message', 'roof21-core' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php if ( ! empty( $logs ) ) : ?>
						<?php foreach ( $logs as $log ) : ?>
						<tr>
							<td><?php echo esc_html( $log['started_at'] ); ?></td>
							<td><?php echo esc_html( $log['sync_type'] ); ?></td>
							<td><span class="sync-status-<?php echo esc_attr( $log['status'] ); ?>"><?php echo esc_html( ucfirst( $log['status'] ) ); ?></span></td>
							<td><?php echo esc_html( $log['properties_added'] ); ?></td>
							<td><?php echo esc_html( $log['properties_updated'] ); ?></td>
							<td><?php echo esc_html( $log['properties_failed'] ); ?></td>
							<td><?php echo esc_html( wp_trim_words( $log['message'], 20 ) ); ?></td>
						</tr>
						<?php endforeach; ?>
					<?php else : ?>
						<tr>
							<td colspan="7"><?php esc_html_e( 'No sync logs found.', 'roof21-core' ); ?></td>
						</tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
		<?php
	}

	/**
	 * Handle manual sync request.
	 *
	 * @since 1.0.0
	 */
	public function handle_manual_sync() {
		check_admin_referer( 'roof21_manual_sync', 'roof21_nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions.', 'roof21-core' ) );
		}

		$sync = new \Roof21\Core\Bitrix24\Bitrix24Sync();
		$results = $sync->manual_sync();

		$message = sprintf(
			/* translators: 1: added count, 2: updated count, 3: failed count */
			__( 'Sync completed. Added: %1$d, Updated: %2$d, Failed: %3$d', 'roof21-core' ),
			$results['added'] ?? 0,
			$results['updated'] ?? 0,
			$results['failed'] ?? 0
		);

		$redirect_url = add_query_arg(
			array(
				'page'            => 'roof21-core',
				'sync_completed'  => '1',
				'sync_message'    => urlencode( $message ),
			),
			admin_url( 'admin.php' )
		);

		wp_safe_redirect( $redirect_url );
		exit;
	}
}
