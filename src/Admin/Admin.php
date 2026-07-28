<?php
/**
 * Admin module.
 *
 * @package ECPDocuments
 */

declare(strict_types=1);

namespace Lavss\ECPDocuments\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Handles the WordPress admin area.
 */
final class Admin {

	/**
	 * Register WordPress hooks.
	 */
	public function register(): void {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
	}

	/**
	 * Register plugin admin menu.
	 */
	public function register_menu(): void {
		add_menu_page(
			__( 'ECP Documents', 'ecp-documents' ),
			__( 'ECP Documents', 'ecp-documents' ),
			'manage_options',
			'ecp-documents',
			array( $this, 'render_page' ),
			'dashicons-media-document',
			80
		);
	}

	/**
	 * Render admin page.
	 */
	public function render_page(): void {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'ECP Documents', 'ecp-documents' ); ?></h1>
			<p><?php esc_html_e( 'Plugin is working!', 'ecp-documents' ); ?></p>
		</div>
		<?php
	}
}
