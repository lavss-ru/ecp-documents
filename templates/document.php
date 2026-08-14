<?php
/**
 * Document card template for DSHI Fedoseevka style.
 *
 * Variables:
 *
 * @var string               $title
 * @var string               $pdf_url
 * @var string               $pdf_file_size
 * @var string               $sig_url
 * @var int                  $sig_id
 * @var array<string,string> $sig_cert_info
 */

defined( 'ABSPATH' ) || exit;

$modal_id = 'ecp-modal-' . (int) $sig_id . '-' . wp_unique_id();
$has_cert = ! empty( $sig_cert_info['serial_number'] ) || ! empty( $sig_cert_info['subject_name'] ) || ! empty( $sig_cert_info['organization'] );
?>

<div class="ecp-document ecp-document--dshi">

        <div class="ecp-document__row">

                <?php if ( $sig_url && $has_cert ) : ?>

                        <button
                                type="button"
                                class="ecp-document__sig-badge"
                                data-ecp-modal="<?php echo esc_attr( $modal_id ); ?>"
                                title="Информация об электронной подписи"
                                aria-label="Информация об электронной подписи">

                                <?php echo isset( $sig_icon_svg ) ? $sig_icon_svg : ''; ?>

                        </button>

                <?php endif; ?>

                <?php if ( $pdf_url ) : ?>

                        <a
                                class="ecp-document__title"
                                href="<?php echo esc_url( $pdf_url ); ?>"
                                target="_blank"
                                rel="noopener noreferrer"
                                title="<?php echo esc_attr( $title ); ?>"
                                data-title="<?php echo esc_attr( $title ); ?>">

                                <?php echo esc_html( $title ); ?>

                        </a>

                <?php else : ?>

                        <span
                                class="ecp-document__title"
                                title="<?php echo esc_attr( $title ); ?>"
                                data-title="<?php echo esc_attr( $title ); ?>">

                                <?php echo esc_html( $title ); ?>

                        </span>

                <?php endif; ?>

        </div>

</div>

<?php if ( $sig_url && $has_cert ) : ?>

        <div id="<?php echo esc_attr( $modal_id ); ?>" class="ecp-modal" aria-hidden="true" role="dialog">

                <div class="ecp-modal__overlay" data-ecp-close></div>

                <div class="ecp-modal__dialog">

                        <div class="ecp-modal__header">

                                <div class="ecp-modal__title-group">

                                        <span class="ecp-modal__icon">
                                                <?php echo isset( $modal_icon_svg ) ? $modal_icon_svg : ''; ?>
                                        </span>

                                        <h3 class="ecp-modal__title">Информация об электронной подписи</h3>

                                </div>

                                <button type="button" class="ecp-modal__close" data-ecp-close aria-label="Закрыть">&times;</button>

                        </div>

                        <div class="ecp-modal__body">

                                <?php if ( ! empty( $sig_cert_info['organization'] ) ) : ?>

                                        <div class="ecp-modal__field ecp-modal__field--org">

                                                <div class="ecp-modal__label">Организация</div>

                                                <div class="ecp-modal__value ecp-modal__value--org">

                                                        <?php echo esc_html( $sig_cert_info['organization'] ); ?>

                                                </div>

                                        </div>

                                <?php endif; ?>

                                <?php if ( ! empty( $sig_cert_info['subject_name'] ) ) : ?>

                                        <div class="ecp-modal__field">

                                                <div class="ecp-modal__label">Подписант</div>

                                                <div class="ecp-modal__value ecp-modal__value--highlight">

                                                        <?php echo esc_html( $sig_cert_info['subject_name'] ); ?>

                                                </div>

                                        </div>

                                <?php endif; ?>

                                <?php if ( ! empty( $sig_cert_info['position'] ) ) : ?>

                                        <div class="ecp-modal__field">

                                                <div class="ecp-modal__label">Должность</div>

                                                <div class="ecp-modal__value">

                                                        <?php echo esc_html( $sig_cert_info['position'] ); ?>

                                                </div>

                                        </div>

                                <?php endif; ?>

                                <?php if ( ! empty( $sig_cert_info['issuer_name'] ) ) : ?>

                                        <div class="ecp-modal__field">

                                                <div class="ecp-modal__label">Кем выдан сертификат</div>

                                                <div class="ecp-modal__value">

                                                        <?php echo esc_html( $sig_cert_info['issuer_name'] ); ?>

                                                </div>

                                        </div>

                                <?php endif; ?>

                                <?php if ( ! empty( $sig_cert_info['serial_number'] ) ) : ?>

                                        <div class="ecp-modal__field">

                                                <div class="ecp-modal__label">Серийный номер</div>

                                                <div class="ecp-modal__value ecp-modal__value--mono">

                                                        <?php echo esc_html( $sig_cert_info['serial_number'] ); ?>

                                                </div>

                                        </div>

                                <?php endif; ?>

                                <?php if ( ! empty( $sig_cert_info['valid_from'] ) || ! empty( $sig_cert_info['valid_to'] ) ) : ?>

                                        <div class="ecp-modal__field">

                                                <div class="ecp-modal__label">Срок действия сертификата</div>

                                                <div class="ecp-modal__value">

                                                        с <?php echo esc_html( $sig_cert_info['valid_from'] ); ?> по <?php echo esc_html( $sig_cert_info['valid_to'] ); ?>

                                                </div>

                                        </div>

                                <?php endif; ?>

                                <div class="ecp-modal__ep-block">

                                        <div class="ecp-modal__ep-header">ЭЛЕКТРОННАЯ ПОДПИСЬ</div>

                                        <div class="ecp-modal__ep-download">

                                                <a
                                                        href="<?php echo esc_url( $sig_url ); ?>"
                                                        class="ecp-modal__ep-download-link"
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        download>

                                                        📎 Скачать SIG-файл

                                                </a>

                                        </div>

                                        <div class="ecp-modal__ep-divider"></div>

                                        <div class="ecp-modal__ep-text">

                                                Проверить электронную подпись можно с помощью официального сервиса Госуслуг.

                                        </div>

                                        <a
                                                href="<?php echo esc_url( 'https://e-trust.gosuslugi.ru/check/sign' ); ?>"
                                                class="ecp-modal__ep-button"
                                                target="_blank"
                                                rel="noopener noreferrer">

                                                Проверить электронную подпись

                                        </a>

                                        <a
                                                href="<?php echo esc_url( 'https://www.gosuslugi.ru/help/faq/esignature/212160' ); ?>"
                                                class="ecp-modal__ep-info-link"
                                                target="_blank"
                                                rel="noopener noreferrer">

                                                Подробнее об электронной подписи на Госуслугах

                                        </a>

                                </div>

                        </div>

                </div>

        </div>

<?php endif; ?>
