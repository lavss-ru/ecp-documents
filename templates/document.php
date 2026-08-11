<?php
/**
 * Document card template.
 *
 * Variables:
 *
 * @var string $title
 * @var string $pdf_url
 * @var string $sig_url
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="ecp-document">

        <div class="ecp-document__title">

                📄 <?php echo esc_html( $title ); ?>

        </div>

        <?php if ( $sig_url ) : ?>

                <div class="ecp-document__status">

                        ✅ Документ подписан электронной подписью

                </div>

        <?php endif; ?>

        <?php if ( $pdf_url || $sig_url ) : ?>

                <div class="ecp-document__actions">

                        <?php if ( $pdf_url ) : ?>

                                <a
                                        class="ecp-document__button ecp-document__button--pdf"
                                        href="<?php echo esc_url( $pdf_url ); ?>"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        aria-label="Скачать PDF-документ">
                                        📄 Скачать PDF

                                </a>

                        <?php endif; ?>

                        <?php if ( $sig_url ) : ?>

                                <a
                                        class="ecp-document__button ecp-document__button--sig"
                                        href="<?php echo esc_url( $sig_url ); ?>"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        aria-label="Скачать файл электронной подписи">

                                        🔐 Скачать SIG

                                </a>

                        <?php endif; ?>

                </div>

        <?php endif; ?>

</div>
