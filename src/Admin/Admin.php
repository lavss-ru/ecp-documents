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
	 *
	 * @return void
	 */
	public function register(): void {

		add_filter( 'ajax_query_attachments_args', [ $this, 'filter_sig_attachments' ] );

	}

	/**
	 * Filters attachment query args when opening WP Media Library to select a SIG file.
	 *
	 * @param array $query WP_Query arguments.
	 *
	 * @return array
	 */
	public function filter_sig_attachments( array $query ): array {

		$ecp_sig = isset( $_POST['query']['ecp_sig'] ) ? $_POST['query']['ecp_sig'] : null;
		$type    = isset( $_POST['query']['type'] ) ? wp_unslash( $_POST['query']['type'] ) : '';

		$is_sig = ! empty( $ecp_sig ) || 'ecp_sig' === $type || ( is_array( $type ) && in_array( 'ecp_sig', $type, true ) );

		if ( $is_sig ) {

			unset( $query['post_mime_type'] );

			if ( ! isset( $query['meta_query'] ) || ! is_array( $query['meta_query'] ) ) {

				$query['meta_query'] = [];

			}

			$query['meta_query'][] = [
				'key'     => '_wp_attached_file',
				'value'   => '.sig',
				'compare' => 'LIKE',
			];

		}

		return $query;

	}
}
