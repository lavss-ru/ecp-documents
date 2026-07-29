<?php
/**
 * Assets module.
 *
 * @package ECPDocuments
 */

declare(strict_types=1);

namespace Lavss\ECPDocuments\Assets;

defined( 'ABSPATH' ) || exit;

/**
 * Handles plugin assets.
 */
final class Assets {

	/**
	 * Register WordPress hooks.
	 */
	public function register(): void {

		add_action(
			'admin_init',
			array( $this, 'register_editor_hooks' )
		);

		add_action(
			'admin_enqueue_scripts',
			array( $this, 'enqueue_admin_assets' )
		);
	}

	/**
	 * Register Classic Editor hooks.
	 */
	public function register_editor_hooks(): void {

		add_filter(
			'mce_external_plugins',
			array( $this, 'register_tinymce_plugin' )
		);

		add_filter(
			'mce_buttons',
			array( $this, 'register_tinymce_button' )
		);
	}

	/**
	 * Enqueue admin assets.
	 *
	 * @param string $hook Current admin page.
	 */
	public function enqueue_admin_assets( string $hook ): void {

		if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
			return;
		}

		wp_enqueue_media();

		wp_enqueue_script(
			'ecp-documents-admin',
			ECP_DOCUMENTS_PLUGIN_URL . 'assets/js/admin.js',
			array( 'jquery' ),
			'0.1.0',
			true
		);
	}

	/**
	 * Register TinyMCE plugin.
	 *
	 * @param array $plugins Registered TinyMCE plugins.
	 * @return array
	 */
	public function register_tinymce_plugin( array $plugins ): array {

		$plugins['ecp_documents'] = ECP_DOCUMENTS_PLUGIN_URL . 'assets/js/editor.js';

		return $plugins;
	}

	/**
	 * Register TinyMCE toolbar button.
	 *
	 * @param array $buttons Registered toolbar buttons.
	 * @return array
	 */
	public function register_tinymce_button( array $buttons ): array {

		$buttons[] = 'ecp_documents';

		return $buttons;
	}
}
