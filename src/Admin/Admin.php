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
 * Handles WordPress admin integration.
 */
final class Admin {

	/**
	 * Register WordPress hooks.
	 */
	public function register(): void {
		add_action( 'admin_init', array( $this, 'register_editor_hooks' ) );
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
