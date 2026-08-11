<?php
/**
 * Document card template.
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

<div class="ecp-document">

        <div class="ecp-document__row">

                <div class="ecp-document__left">

                        <span class="ecp-document__icon">📄</span>

                        <span class="ecp-document__title"><?php echo esc_html( $title ); ?></span>

                </div>

                <div class="ecp-document__right">

                        <?php if ( $sig_url && $has_cert ) : ?>

                                <button
                                        type="button"
                                        class="ecp-document__sig-badge"
                                        data-ecp-modal="<?php echo esc_attr( $modal_id ); ?>"
                                        title="Информация об электронной подписи"
                                        aria-label="Информация об электронной подписи">

                                        🔐

                                </button>

                        <?php endif; ?>

                        <?php if ( ! empty( $pdf_file_size ) ) : ?>

                                <span class="ecp-document__size"><?php echo esc_html( $pdf_file_size ); ?></span>

                        <?php endif; ?>

                        <?php if ( $pdf_url ) : ?>

                                <a
                                        class="ecp-document__download-button"
                                        href="<?php echo esc_url( $pdf_url ); ?>"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        aria-label="Скачать PDF-документ">

                                        Скачать

                                </a>

                        <?php endif; ?>

                        <?php if ( $sig_url ) : ?>

                                <a
                                        class="ecp-document__download-button ecp-document__download-button--sig"
                                        href="<?php echo esc_url( $sig_url ); ?>"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        aria-label="Скачать файл электронной подписи"
                                        title="Скачать файл электронной подписи (.SIG)">

                                        SIG

                                </a>

                        <?php endif; ?>

                </div>

        </div>

</div>

<?php if ( $sig_url && $has_cert ) : ?>

        <div id="<?php echo esc_attr( $modal_id ); ?>" class="ecp-modal" aria-hidden="true" role="dialog">

                <div class="ecp-modal__overlay" data-ecp-close></div>

                <div class="ecp-modal__dialog">

                        <div class="ecp-modal__header">

                                <div class="ecp-modal__title-group">

                                        <span class="ecp-modal__icon">🔐</span>

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

                        </div>

                </div>

        </div>

<?php endif; ?>
