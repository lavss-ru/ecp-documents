<?php
/**
 * Assets loader.
 *
 * @package ECPDocuments
 */

declare(strict_types=1);

namespace Lavss\ECPDocuments\Assets;

defined( 'ABSPATH' ) || exit;

/**
 * Registers plugin assets.
 */
final class Assets {

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register(): void {

		add_action( 'admin_init', [ $this, 'register_editor_plugin' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
		add_action( 'admin_footer', [ $this, 'render_dialog_template' ] );

		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_frontend_assets' ] );

	}

	/**
	 * Registers TinyMCE plugin.
	 *
	 * @return void
	 */
	public function register_editor_plugin(): void {

		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
			return;
		}

		add_filter( 'mce_external_plugins', [ $this, 'add_editor_plugin' ] );
		add_filter( 'mce_buttons', [ $this, 'add_editor_button' ] );

	}

	/**
	 * Adds TinyMCE plugin.
	 *
	 * @param array $plugins Plugins.
	 *
	 * @return array
	 */
	public function add_editor_plugin( array $plugins ): array {

		$plugins['ecp_documents'] = ECP_DOCUMENTS_PLUGIN_URL . 'assets/js/editor.js';

		return $plugins;

	}

	/**
	 * Adds TinyMCE button.
	 *
	 * @param array $buttons Buttons.
	 *
	 * @return array
	 */
	public function add_editor_button( array $buttons ): array {

		$buttons[] = 'ecp_documents';

		return $buttons;

	}

	/**
	 * Enqueue admin assets.
	 *
	 * @return void
	 */
	public function enqueue_admin_assets(): void {

		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
			return;
		}

		$screen = get_current_screen();

		if ( ! $screen || 'post' !== $screen->base ) {
			return;
		}

		wp_enqueue_media();

		wp_enqueue_style(
			'ecp-documents-admin',
			ECP_DOCUMENTS_PLUGIN_URL . 'assets/css/admin.css',
			[],
			ECP_DOCUMENTS_VERSION
		);

		wp_enqueue_script(
			'ecp-documents-admin',
			ECP_DOCUMENTS_PLUGIN_URL . 'assets/js/admin.js',
			[ 'jquery' ],
			ECP_DOCUMENTS_VERSION,
			true
		);

	}

	/**
	 * Enqueue frontend assets.
	 *
	 * @return void
	 */
	public function enqueue_frontend_assets(): void {

		wp_enqueue_style(
			'ecp-documents-frontend',
			ECP_DOCUMENTS_PLUGIN_URL . 'assets/css/frontend.css',
			[],
			ECP_DOCUMENTS_VERSION
		);

	}

	/**
	 * Render dialog template.
	 *
	 * @return void
	 */
	public function render_dialog_template(): void {

		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
			return;
		}

		$screen = get_current_screen();

		if ( ! $screen || 'post' !== $screen->base ) {
			return;
		}

		require ECP_DOCUMENTS_PLUGIN_DIR . 'templates/dialog.php';

	}

}
