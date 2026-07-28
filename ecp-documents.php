<?php
/**
 * Plugin Name: ECP Documents
 * Plugin URI: https://github.com/lavss-ru/ecp-documents
 * Description: Publish PDF documents with electronic signatures (PDF + SIG) directly in the WordPress editor.
 * Version: 0.1.0
 * Requires at least: 7.0
 * Requires PHP: 8.2
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
