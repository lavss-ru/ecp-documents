<?php
/**
 * Plugin Name: ECP Documents
 * Plugin URI: https://github.com/lavss-ru/ecp-documents
 * Description: Manage PDF documents with electronic signature (SIG) files for WordPress.
 * Version: 0.1.0
 * Requires at least: 6.5
 * Requires PHP: 8.1
 * Author: lavss
 * Author URI: https://github.com/lavss-ru
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: ecp-documents
 * Domain Path: /languages
 *
 * @package ECPDocuments
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/vendor/autoload.php';

use Lavss\ECPDocuments\Core\Plugin;

( new Plugin() )->run();
