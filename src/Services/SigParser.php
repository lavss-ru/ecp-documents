<?php
/**
 * SIG file parser service.
 *
 * @package ECPDocuments
 */

declare(strict_types=1);

namespace Lavss\ECPDocuments\Services;

defined( 'ABSPATH' ) || exit;

/**
 * Parses electronic signature (.sig) files and extracts signer certificate details.
 */
final class SigParser {

        /**
         * Extracts certificate serial number in HEX format from a SIG file.
         *
         * @param string $file_path Absolute path to .sig file.
         *
         * @return string Serial number in uppercase HEX, or empty string on failure.
         */
        public function extract_serial_number( string $file_path ): string {

                if ( '' === $file_path || ! file_exists( $file_path ) || ! is_readable( $file_path ) ) {

                        return '';

                }

                $content = file_get_contents( $file_path );

                if ( ! is_string( $content ) || '' === $content ) {

                        return '';

                }

                // 1. Try standard OpenSSL PKCS7/CMS functions first.
                $serial = $this->extract_via_openssl( $file_path, $content );

                if ( '' !== $serial ) {

                        return $serial;

                }

                // 2. Fallback: extract X.509 certificate DER structure directly from binary SIG content.
                return $this->extract_via_der_scan( $content );

        }

        /**
         * Extracts serial number using standard OpenSSL PKCS7 / CMS functions.
         *
         * @param string $file_path File path.
         * @param string $content File content.
         *
         * @return string
         */
        private function extract_via_openssl( string $file_path, string $content ): string {

                $tmp_cert = function_exists( 'wp_tempnam' ) ? wp_tempnam( 'ecp_cert_' ) : tempnam( sys_get_temp_dir(), 'ecp_cert_' );

                if ( ! $tmp_cert ) {

                        return '';

                }

                $success = @openssl_pkcs7_verify( $file_path, PKCS7_NOVERIFY, $tmp_cert );

                if ( ! $success && false === strpos( $content, '-----BEGIN' ) ) {

                        $pem_content = "-----BEGIN PKCS7-----\n" . chunk_split( base64_encode( $content ), 64, "\n" ) . "-----END PKCS7-----\n";
                        $tmp_pem     = function_exists( 'wp_tempnam' ) ? wp_tempnam( 'ecp_pem_' ) : tempnam( sys_get_temp_dir(), 'ecp_pem_' );

                        if ( $tmp_pem ) {

                                file_put_contents( $tmp_pem, $pem_content );
                                $success = @openssl_pkcs7_verify( $tmp_pem, PKCS7_NOVERIFY, $tmp_cert );
                                @unlink( $tmp_pem );

                        }

                }

                $serial = '';

                if ( file_exists( $tmp_cert ) ) {

                        $cert_pem = file_get_contents( $tmp_cert );

                        if ( is_string( $cert_pem ) && '' !== $cert_pem ) {

                                $parsed = @openssl_x509_parse( $cert_pem );

                                if ( is_array( $parsed ) && ! empty( $parsed['serialNumberHex'] ) ) {

                                        $serial = strtoupper( (string) $parsed['serialNumberHex'] );

                                }

                        }

                        @unlink( $tmp_cert );

                }

                return $serial;

        }

        /**
         * Fallback: Scans binary DER content for embedded X.509 certificates and extracts serial number.
         *
         * @param string $content Binary file content.
         *
         * @return string
         */
        private function extract_via_der_scan( string $content ): string {

                $len = strlen( $content );

                for ( $i = 0; $i < $len - 200; $i++ ) {

                        if ( 0x30 === ord( $content[ $i ] ) && ( 0x82 === ord( $content[ $i + 1 ] ) || 0x81 === ord( $content[ $i + 1 ] ) ) ) {

                                if ( 0x82 === ord( $content[ $i + 1 ] ) ) {

                                        $cert_len = ( ord( $content[ $i + 2 ] ) << 8 ) + ord( $content[ $i + 3 ] ) + 4;

                                } else {

                                        $cert_len = ord( $content[ $i + 2 ] ) + 3;

                                }

                                if ( $cert_len > 200 && ( $i + $cert_len ) <= $len ) {

                                        $der = substr( $content, $i, $cert_len );
                                        $pem = "-----BEGIN CERTIFICATE-----\n" . chunk_split( base64_encode( $der ), 64, "\n" ) . "-----END CERTIFICATE-----\n";

                                        $parsed = @openssl_x509_parse( $pem );

                                        if ( is_array( $parsed ) && ! empty( $parsed['serialNumberHex'] ) ) {

                                                return strtoupper( (string) $parsed['serialNumberHex'] );

                                        }

                                }

                        }

                }

                return '';

        }

}
