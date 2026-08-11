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
         * Extracts full certificate details from a SIG file.
         *
         * @param string $file_path Absolute path to .sig file.
         *
         * @return array<string,string> Array with certificate info or empty values on failure.
         */
        public function extract_cert_info( string $file_path ): array {

                $empty_result = [
                        'serial_number' => '',
                        'subject_name'  => '',
                        'position'      => '',
                        'issuer_name'   => '',
                        'valid_from'    => '',
                        'valid_to'      => '',
                ];

                if ( '' === $file_path || ! file_exists( $file_path ) || ! is_readable( $file_path ) ) {

                        return $empty_result;

                }

                $content = file_get_contents( $file_path );

                if ( ! is_string( $content ) || '' === $content ) {

                        return $empty_result;

                }

                $parsed = $this->parse_x509_from_openssl( $file_path, $content );

                if ( null === $parsed ) {

                        $parsed = $this->parse_x509_from_der_scan( $content );

                }

                if ( ! is_array( $parsed ) ) {

                        return $empty_result;

                }

                return $this->format_cert_info( $parsed );

        }

        /**
         * Extracts certificate serial number in HEX format from a SIG file.
         *
         * @param string $file_path Absolute path to .sig file.
         *
         * @return string Serial number in uppercase HEX, or empty string on failure.
         */
        public function extract_serial_number( string $file_path ): string {

                $info = $this->extract_cert_info( $file_path );

                return $info['serial_number'] ?? '';

        }

        /**
         * Parses X.509 certificate using standard OpenSSL PKCS7 / CMS functions.
         *
         * @param string $file_path File path.
         * @param string $content File content.
         *
         * @return array<string,mixed>|null Parsed array or null on failure.
         */
        private function parse_x509_from_openssl( string $file_path, string $content ): ?array {

                $tmp_cert = function_exists( 'wp_tempnam' ) ? wp_tempnam( 'ecp_cert_' ) : tempnam( sys_get_temp_dir(), 'ecp_cert_' );

                if ( ! $tmp_cert ) {

                        return null;

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

                $parsed = null;

                if ( file_exists( $tmp_cert ) ) {

                        $cert_pem = file_get_contents( $tmp_cert );

                        if ( is_string( $cert_pem ) && '' !== $cert_pem ) {

                                $result = @openssl_x509_parse( $cert_pem );

                                if ( is_array( $result ) && ! empty( $result['serialNumberHex'] ) ) {

                                        $parsed = $result;

                                }

                        }

                        @unlink( $tmp_cert );

                }

                return $parsed;

        }

        /**
         * Scans binary DER content for embedded X.509 certificates and parses certificate structure.
         *
         * @param string $content Binary file content.
         *
         * @return array<string,mixed>|null Parsed array or null on failure.
         */
        private function parse_x509_from_der_scan( string $content ): ?array {

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

                                                return $parsed;

                                        }

                                }

                        }

                }

                return null;

        }

        /**
         * Formats parsed X.509 array into plugin certificate info structure.
         *
         * @param array<string,mixed> $parsed Parsed X.509 array from openssl_x509_parse.
         *
         * @return array<string,string>
         */
        private function format_cert_info( array $parsed ): array {

                $subject = is_array( $parsed['subject'] ?? null ) ? $parsed['subject'] : [];
                $issuer  = is_array( $parsed['issuer'] ?? null )  ? $parsed['issuer']  : [];

                $subject_name = '';

                if ( ! empty( $subject['SN'] ) || ! empty( $subject['GN'] ) ) {

                        $subject_name = trim( (string) ( $subject['SN'] ?? '' ) . ' ' . (string) ( $subject['GN'] ?? '' ) );

                } elseif ( ! empty( $subject['CN'] ) ) {

                        $subject_name = (string) $subject['CN'];

                } elseif ( ! empty( $subject['O'] ) ) {

                        $subject_name = (string) $subject['O'];

                }

                $position = ! empty( $subject['title'] ) ? (string) $subject['title'] : '';

                $issuer_name = ! empty( $issuer['CN'] ) ? (string) $issuer['CN'] : ( ! empty( $issuer['O'] ) ? (string) $issuer['O'] : '' );

                $valid_from = isset( $parsed['validFrom_time_t'] ) ? date( 'd.m.Y', (int) $parsed['validFrom_time_t'] ) : '';
                $valid_to   = isset( $parsed['validTo_time_t'] )   ? date( 'd.m.Y', (int) $parsed['validTo_time_t'] )   : '';

                $serial_number = ! empty( $parsed['serialNumberHex'] ) ? strtoupper( (string) $parsed['serialNumberHex'] ) : '';

                return [
                        'serial_number' => $serial_number,
                        'subject_name'  => $subject_name,
                        'position'      => $position,
                        'issuer_name'   => $issuer_name,
                        'valid_from'    => $valid_from,
                        'valid_to'      => $valid_to,
                ];

        }

}
