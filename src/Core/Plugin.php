<?php
/**
 * Core plugin bootstrap.
 *
 * @package ECPDocuments
 */

declare(strict_types=1);

namespace Lavss\ECPDocuments\Core;

use Lavss\ECPDocuments\Admin\Admin;
use Lavss\ECPDocuments\Assets\Assets;
use Lavss\ECPDocuments\Shortcode\Shortcode;
use Lavss\ECPDocuments\Uploads\Uploads;

defined( 'ABSPATH' ) || exit;

/**
 * Main plugin class.
 */
final class Plugin {

	/**
	 * Initialize plugin.
	 */
	public function run(): void {

        $admin = new Admin();
        $admin->register();

        $assets = new Assets();
        $assets->register();

        $shortcode = new Shortcode();
        $shortcode->register();

		$uploads = new Uploads();
		$uploads->register();

        }
}
