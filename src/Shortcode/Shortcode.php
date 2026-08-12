<?php
/**
 * Shortcode handler.
 *
 * @package ECPDocuments
 */

declare(strict_types=1);

namespace Lavss\ECPDocuments\Shortcode;

use Lavss\ECPDocuments\Services\SigParser;

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
         * @param array<string,string>|string $atts Shortcode attributes.
         *
         * @return string
         */
        public function render_document( $atts ): string {

                $atts = shortcode_atts(
                        [
                                'title'  => '',
                                'pdf_id' => '',
                                'sig_id' => '',
                                'pdf'    => '',
                                'sig'    => '',
                        ],
                        is_array( $atts ) ? $atts : [],
                        'ecp_document'
                );

                $title = sanitize_text_field( (string) $atts['title'] );

				if ( '' === trim( $title ) ) {

                $title = 'Документ';

				}

                $pdf_id = ! empty( $atts['pdf_id'] ) ? (int) $atts['pdf_id'] : ( ! empty( $atts['pdf'] ) ? (int) $atts['pdf'] : 0 );
                $sig_id = ! empty( $atts['sig_id'] ) ? (int) $atts['sig_id'] : ( ! empty( $atts['sig'] ) ? (int) $atts['sig'] : 0 );

                $pdf_url = $this->get_valid_pdf_url( $pdf_id );

                if ( '' === $pdf_url ) {

                        return '';

                }

                $pdf_file_size = '';

                if ( $pdf_id > 0 ) {

                        $file_path = get_attached_file( $pdf_id );

                        if ( is_string( $file_path ) && file_exists( $file_path ) ) {

                                $size_bytes = (int) filesize( $file_path );

                                if ( $size_bytes > 0 ) {

                                        $pdf_file_size = $this->format_file_size( $size_bytes );

                                }

                        }

                }

                $sig_url = $this->get_valid_sig_url( $sig_id );

                $sig_cert_info = [
                        'organization'  => '',
                        'serial_number' => '',
                        'subject_name'  => '',
                        'position'      => '',
                        'issuer_name'   => '',
                        'valid_from'    => '',
                        'valid_to'      => '',
                ];

                if ( '' !== $sig_url ) {

                        $sig_cert_info = $this->get_sig_cert_info( $sig_id );

                }

                $sig_icon_svg   = $this->get_sig_icon_svg( 26, 26, 'ecp-document__sig-icon' );
                $modal_icon_svg = $this->get_sig_icon_svg( 20, 20, 'ecp-modal__icon-svg' );

                ob_start();

                require ECP_DOCUMENTS_PLUGIN_DIR . 'templates/document.php';

                return (string) ob_get_clean();

        }

        /**
         * Gets cached certificate info for a SIG attachment ID or parses and caches it.
         *
         * @param int $sig_id SIG Attachment ID.
         *
         * @return array<string,string> Certificate info array or empty structure.
         */
        private function get_sig_cert_info( int $sig_id ): array {

                $empty_info = [
                        'organization'  => '',
                        'serial_number' => '',
                        'subject_name'  => '',
                        'position'      => '',
                        'issuer_name'   => '',
                        'valid_from'    => '',
                        'valid_to'      => '',
                ];

                if ( $sig_id <= 0 ) {

                        return $empty_info;

                }

                $cached = get_post_meta( $sig_id, '_ecp_sig_cert_info', true );

                if ( is_array( $cached ) && ( isset( $cached['serial_number'] ) || isset( $cached['organization'] ) ) ) {

                        return array_merge( $empty_info, $cached );

                }

                $file_path = get_attached_file( $sig_id );

                if ( ! is_string( $file_path ) || '' === $file_path ) {

                        update_post_meta( $sig_id, '_ecp_sig_cert_info', $empty_info );
                        update_post_meta( $sig_id, '_ecp_sig_serial_number', 'none' );

                        return $empty_info;

                }

                $parser = new SigParser();
                $info   = $parser->extract_cert_info( $file_path );

                update_post_meta( $sig_id, '_ecp_sig_cert_info', $info );

                if ( ! empty( $info['serial_number'] ) ) {

                        update_post_meta( $sig_id, '_ecp_sig_serial_number', $info['serial_number'] );

                } else {

                        update_post_meta( $sig_id, '_ecp_sig_serial_number', 'none' );

                }

                return $info;

        }

        /**
         * Validates PDF attachment ID and returns valid URL or empty string.
         *
         * @param int $attachment_id Attachment ID.
         *
         * @return string
         */
        private function get_valid_pdf_url( int $attachment_id ): string {

                if ( $attachment_id <= 0 ) {

                        return '';

                }

                $post = get_post( $attachment_id );

                if ( ! $post || 'attachment' !== $post->post_type ) {

                        return '';

                }

                $mime_type = get_post_mime_type( $post );
                $file_path = get_attached_file( $attachment_id );
                $extension = is_string( $file_path ) ? strtolower( pathinfo( $file_path, PATHINFO_EXTENSION ) ) : '';

                if ( 'application/pdf' !== $mime_type || 'pdf' !== $extension ) {

                        return '';

                }

                $url = wp_get_attachment_url( $attachment_id );

                return is_string( $url ) ? $url : '';

        }

        /**
         * Validates SIG attachment ID and returns valid URL or empty string.
         *
         * @param int $attachment_id Attachment ID.
         *
         * @return string
         */
        private function get_valid_sig_url( int $attachment_id ): string {

                if ( $attachment_id <= 0 ) {

                        return '';

                }

                $post = get_post( $attachment_id );

                if ( ! $post || 'attachment' !== $post->post_type ) {

                        return '';

                }

                $mime_type          = get_post_mime_type( $post );
                $allowed_mime_types = [ 'application/octet-stream', 'application/pkcs7-signature' ];

                $file_path          = get_attached_file( $attachment_id );
                $extension          = is_string( $file_path ) ? strtolower( pathinfo( $file_path, PATHINFO_EXTENSION ) ) : '';
                $allowed_extensions = [ 'sig' ];

                if ( ! in_array( $mime_type, $allowed_mime_types, true ) && ! in_array( $extension, $allowed_extensions, true ) ) {

                        return '';

                }

                $url = wp_get_attachment_url( $attachment_id );

                return is_string( $url ) ? $url : '';

        }

        /**
         * Formats file size in megabytes (MB) according to Gosweb style guidelines.
         *
         * @param int $size_bytes Size in bytes.
         *
         * @return string Formatted size string (e.g., "0,03 МБ", "0,83 МБ", "2 МБ", "1,5 МБ").
         */
        private function format_file_size( int $size_bytes ): string {

                if ( $size_bytes <= 0 ) {

                        return '';

                }

                $mb = $size_bytes / ( 1024 * 1024 );

                $rounded = round( $mb, 2 );

                if ( $rounded <= 0 && $size_bytes > 0 ) {

                        $rounded = 0.01;

                }

                $formatted = number_format( $rounded, 2, ',', '' );

                if ( false !== strpos( $formatted, ',' ) ) {

                        $formatted = rtrim( rtrim( $formatted, '0' ), ',' );

                }

                return $formatted . ' МБ';

        }

        /**
         * Returns reusable SVG icon markup for electronic signature from assets/images/sig-icon.svg.
         *
         * @param int    $width Width in px.
         * @param int    $height Height in px.
         * @param string $class CSS class name.
         *
         * @return string SVG HTML markup.
         */
        public function get_sig_icon_svg( int $width = 24, int $height = 24, string $class = 'ecp-document__sig-icon' ): string {

                $svg_file = ECP_DOCUMENTS_PLUGIN_DIR . 'assets/images/sig-icon.svg';

                if ( ! file_exists( $svg_file ) ) {

                        return '';

                }

                $svg_content = file_get_contents( $svg_file );

                if ( ! is_string( $svg_content ) || '' === $svg_content ) {

                        return '';

                }

                $replaced = preg_replace(
                        '/<svg\b/',
                        sprintf( '<svg class="%s" width="%d" height="%d"', esc_attr( $class ), (int) $width, (int) $height ),
                        $svg_content,
                        1
                );

                return is_string( $replaced ) ? $replaced : $svg_content;

        }

}
