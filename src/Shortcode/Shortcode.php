<?php
/**
 * Shortcode handler.
 *
 * @package ECPDocuments
 */

declare(strict_types=1);

namespace Lavss\ECPDocuments\Shortcode;

defined( 'ABSPATH' ) || exit;

/**
 * Registers and renders plugin shortcodes.
 */
final class Shortcode {

        /**
         * Register WordPress hooks.
         */
        public function register(): void {

                add_action(
                        'init',
                        [ $this, 'register_shortcodes' ]
                );

        }

        /**
         * Register plugin shortcodes.
         */
        public function register_shortcodes(): void {

                add_shortcode(
                        'ecp_document',
                        [ $this, 'render_document' ]
                );

        }

        /**
         * Render document shortcode.
         *
         * @param array<string,string> $atts Shortcode attributes.
         *
         * @return string
         */
        public function render_document( array $atts ): string {

                $atts = shortcode_atts(
                        [
                                'title' => '',
                                'pdf'   => '',
                                'sig'   => '',
                        ],
                        $atts,
                        'ecp_document'
                );

                $title = (string) $atts['title'];

				if ( '' === trim( $title ) ) {

                $title = 'Документ';

				}

                $pdf_url = '';
                $sig_url = '';

                if ( ! empty( $atts['pdf'] ) ) {

                        $pdf_url = (string) wp_get_attachment_url(
                                (int) $atts['pdf']
                        );

                }

                if ( ! empty( $atts['sig'] ) ) {

                        $sig_url = (string) wp_get_attachment_url(
                                (int) $atts['sig']
                        );

                }

                ob_start();

                require ECP_DOCUMENTS_PLUGIN_DIR . 'templates/document.php';

                return (string) ob_get_clean();

        }

}
