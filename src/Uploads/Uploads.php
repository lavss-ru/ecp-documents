<?php
/**
 * Uploads handler.
 *
 * @package ECPDocuments
 */

declare(strict_types=1);

namespace Lavss\ECPDocuments\Uploads;

defined( 'ABSPATH' ) || exit;

/**
 * Registers upload MIME types.
 */
final class Uploads {

        /**
         * Register WordPress hooks.
         *
         * @return void
         */
        public function register(): void {

                add_filter(
                        'upload_mimes',
                        [ $this, 'register_mime_types' ]
                );

        }

        /**
         * Register additional MIME types.
         *
         * @param array<string,string> $mimes Allowed MIME types.
         *
         * @return array<string,string>
         */
        public function register_mime_types( array $mimes ): array {

                $mimes['sig'] = 'application/octet-stream';
                $mimes['p7s'] = 'application/pkcs7-signature';

                return $mimes;

        }

}
