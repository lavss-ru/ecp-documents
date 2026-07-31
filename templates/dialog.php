<?php
/**
 * Dialog template.
 *
 * @package ECPDocuments
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;
?>

<div
	id="ecp-documents-dialog"
	class="ecp-documents-dialog"
	role="dialog"
	aria-modal="true"
	aria-labelledby="ecp-documents-dialog-title"
>

	<div class="ecp-documents-dialog__overlay"></div>

	<div class="ecp-documents-dialog__window">

		<div class="ecp-documents-dialog__header">

			<h2 id="ecp-documents-dialog-title">
				Документ с электронной подписью
			</h2>

			<button
				type="button"
				class="ecp-documents-dialog__close"
				aria-label="Закрыть">
				&times;
			</button>

		</div>

		<div class="ecp-documents-dialog__body">

			<div class="ecp-field">

				<label for="ecp-document-title">
					<strong>Название документа</strong>
				</label>

				<p>

					<input
						id="ecp-document-title"
						name="ecp_document_title"
						type="text"
						class="regular-text ecp-document-title"
						placeholder="Например: Устав">

				</p>

			</div>

			<div class="ecp-field" data-type="pdf">

				<label>
					<strong>PDF-документ</strong>
				</label>

				<p class="ecp-file-picker">

					<button
						type="button"
						class="button ecp-select-file"
						data-type="pdf">

						Выбрать PDF

					</button>

					<span class="ecp-file-placeholder">

						PDF не выбран

					</span>

					<a
						class="ecp-file-link"
						href="#"
						target="_blank"
						rel="noopener noreferrer"
						aria-label="Открыть выбранный PDF-файл"
						hidden>
					</a>

				</p>

			</div>

			<div class="ecp-field" data-type="sig">

				<label>
					<strong>SIG-файл</strong>
				</label>

				<p class="ecp-file-picker">

					<button
						type="button"
						class="button ecp-select-file"
						data-type="sig">

						Выбрать SIG

					</button>

					<span class="ecp-file-placeholder">

						SIG не выбран

					</span>

					<a
						class="ecp-file-link"
						href="#"
						target="_blank"
						rel="noopener noreferrer"
						aria-label="Открыть выбранный SIG-файл"
						hidden>
					</a>

				</p>

			</div>

		</div>

		<div class="ecp-documents-dialog__footer">

			<button
				type="button"
				class="button button-secondary ecp-dialog-cancel">

				Отмена

			</button>

			<button
				type="button"
				class="button button-primary ecp-insert-document">

				Вставить

			</button>

		</div>

	</div>

</div>
