(function ($) {
	'use strict';

	class ECPDocumentsDialog {

		constructor() {

			this.dialog = null;

			this.title = '';

			this.mode = 'create';

			this.editor = null;

			this.selectedPdf = this.createEmptySelection();

			this.selectedSig = this.createEmptySelection();

			this.frames = {
				pdf: null,
				sig: null
			};

		}

		createEmptySelection() {

			return {
				id: null,
				title: '',
				filename: '',
				url: ''
			};

		}

		createDocumentTitle(filename) {

			return filename
				.replace(/\.pdf$/i, '')
				.replace(/[_-]+/g, ' ')
				.replace(/\s+/g, ' ')
				.trim();

		}

		getSelectButtonLabel(type, hasSelection) {

			const labels = {

				pdf: {
					select: 'Выбрать PDF',
					change: 'Изменить PDF'
				},

				sig: {
					select: 'Выбрать SIG',
					change: 'Изменить SIG'
				}

			};

			return hasSelection
				? labels[type].change
				: labels[type].select;

		}

		fillDocumentTitle(selection) {

			const field = this.dialog.find('.ecp-document-title');

			if (field.val().trim() !== '') {

				return;

			}

			const title = selection.title && selection.title.trim() !== ''
				? selection.title
				: this.createDocumentTitle(selection.filename);

			field.val(title);

		}

		updateInsertButtonState() {

			if (!this.dialog) {

				return;

			}

			const enabled =
				this.selectedPdf.id !== null &&
				this.selectedSig.id !== null &&
				this.dialog
					.find('.ecp-document-title')
					.val()
					.trim() !== '';

			this.dialog
				.find('.ecp-insert-document')
				.prop('disabled', !enabled);

		}

		init() {

			this.dialog = $('#ecp-documents-dialog');

			if (!this.dialog.length) {

				return;

			}

			this.bindEvents();

		}

		bindEvents() {

			this.dialog
				.find('.ecp-documents-dialog__close')
				.on('click', this.closeDialog.bind(this));

			this.dialog
				.find('.ecp-documents-dialog__overlay')
				.on('click', this.closeDialog.bind(this));

			this.dialog
				.find('.ecp-dialog-cancel')
				.on('click', this.cancelDialog.bind(this));

			this.dialog
				.find('.ecp-select-file')
				.on('click', this.handleSelectFile.bind(this));

			this.dialog
				.find('.ecp-insert-document')
				.on('click', this.insertDocument.bind(this));

			this.dialog
				.find('.ecp-document-title')
				.on(
					'input',
					this.updateInsertButtonState.bind(this)
				);

		}

		handleSelectFile(event) {

			event.preventDefault();

			const type = $(event.currentTarget).data('type');

			this.openMedia(type);

		}

		openMedia(type) {

			if (!this.frames[type]) {

				this.frames[type] = this.createMediaFrame(type);

			}

			this.frames[type].open();

		}

		createMediaFrame(type) {

			const frame = wp.media(
				this.getMediaConfig(type)
			);

			frame.on(
				'select',
				() => this.handleMediaSelect(type, frame)
			);

			return frame;

		}

		getMediaConfig(type) {

			const configs = {

				pdf: {

					title: 'Выберите PDF-документ',

					button: {
						text: 'Выбрать'
					},

					library: {
						type: 'application/pdf'
					},

					multiple: false

				},

				sig: {

					title: 'Выберите SIG-файл',

					button: {
						text: 'Выбрать'
					},

					library: {},

					multiple: false

				}

			};

			return configs[type];

		}

		handleMediaSelect(type, frame) {

			const attachment = frame
				.state()
				.get('selection')
				.first()
				.toJSON();

			this.setSelection(type, attachment);

		}

		setSelection(type, attachment) {

			const selection = {
				id: attachment.id,
				title: attachment.title,
				filename: attachment.filename,
				url: attachment.url
			};

			if (type === 'pdf') {

				this.selectedPdf = selection;

				this.fillDocumentTitle(selection);

			} else {

				this.selectedSig = selection;

			}

			this.updateFileUI(type);

			this.updateInsertButtonState();

		}

		updateFileUI(type) {

			const selection = type === 'pdf'
				? this.selectedPdf
				: this.selectedSig;

			const field = this.dialog.find(
				'.ecp-field[data-type="' + type + '"]'
			);

			const placeholder = field.find('.ecp-file-placeholder');

			const link = field.find('.ecp-file-link');

			field
				.find('.ecp-select-file')
				.text(
					this.getSelectButtonLabel(
						type,
						selection.id !== null
					)
				);

			if (selection.id) {

				const linkTitle = selection.title && selection.title.trim() !== ''
					? selection.title
					: this.createDocumentTitle(selection.filename);

				placeholder.prop('hidden', true);

				link
					.text(linkTitle)
					.attr('href', selection.url)
					.attr(
						'title',
						type === 'pdf'
							? 'Открыть PDF-документ'
							: 'Открыть SIG-файл'
					)
					.prop('hidden', false);

			} else {

				link
					.text('')
					.attr('href', '#')
					.removeAttr('title')
					.prop('hidden', true);

				placeholder.prop('hidden', false);

			}

		}
		insertDocument() {

			if (!this.selectedPdf.id) {
				alert('Выберите PDF-документ');
				return;
			}

			if (!this.selectedSig.id) {
				alert('Выберите SIG-файл');
				return;
			}

			const title = this.dialog
				.find('.ecp-document-title')
				.val()
				.trim();

			if (!title) {
				alert('Введите название документа');
				return;
			}

			const shortcode =
				'[ecp_document title="' +
				title +
				'" pdf_id="' +
				this.selectedPdf.id +
				'" sig_id="' +
				this.selectedSig.id +
				'"]';

			if (this.mode === 'edit') {
				this.replaceSelectedContent(shortcode);
			} else {
				this.insertIntoEditor(shortcode);
			}

			this.reset();
			this.closeDialog();

		}

		replaceSelectedContent(content) {

			if (
				this.editor &&
				typeof this.editor.selection !== 'undefined' &&
				typeof this.editor.selection.setContent === 'function' &&
				!this.editor.isHidden()
			) {
				this.editor.selection.setContent(content);
				return;
			}

			const textarea = document.getElementById('content');
			if (!textarea) {
				return;
			}

			const start = textarea.selectionStart;
			const end = textarea.selectionEnd;

			textarea.setRangeText(
				content,
				start,
				end,
				'end'
			);

			textarea.focus();

		}

		insertIntoEditor(content) {

			if (
				typeof tinymce !== 'undefined' &&
				tinymce.activeEditor &&
				!tinymce.activeEditor.isHidden()
			) {

				tinymce.activeEditor.execCommand(
					'mceInsertContent',
					false,
					content
				);

				return;

			}

			const editor = document.getElementById('content');

			if (!editor) {

				return;

			}

			const start = editor.selectionStart;
			const end = editor.selectionEnd;

			editor.setRangeText(
				content,
				start,
				end,
				'end'
			);

			editor.focus();

		}

		getEditorContent() {

			if (!this.editor) {
				return '';
			}

			return this.editor.getContent({
				format: 'raw'
			});

		}

		parseShortcode(shortcode) {

			const pdfMatch = shortcode.match(/(?:pdf_id|pdf)="(\d+)"/);
			const sigMatch = shortcode.match(/(?:sig_id|sig)="(\d+)"/);
			const titleMatch = shortcode.match(/title="([^"]+)"/);

			if (!pdfMatch || !sigMatch) {
				return null;
			}

			return {
				pdf: Number(pdfMatch[1]),
				sig: Number(sigMatch[1]),
				title: titleMatch ? titleMatch[1] : ''
			};

		}

		detectEditingShortcode() {

			this.mode = 'create';

			if (!this.editor) {
				return;
			}

			let selected = '';

			if (typeof this.editor.isHidden === 'function' && !this.editor.isHidden()) {
				selected = this.editor.selection.getContent({
					format: 'text'
				}).trim();
			}

			if (!selected) {
				const textarea = document.getElementById('content');

				if (textarea) {
					selected = textarea.value
						.substring(textarea.selectionStart, textarea.selectionEnd)
						.trim();
				}
			}

			const match = selected.match(
				/^\[ecp_document\b([\s\S]*)\]$/
			);

			if (!match) {
				console.log('Create mode');
				return;
			}

			const shortcode = this.parseShortcode(selected);

			if (!shortcode) {
				console.log('Create mode: invalid shortcode');
				return;
			}

			this.mode = 'edit';
			this.dialog.find('.ecp-document-title').val(shortcode.title);

			this.loadAttachment('pdf', shortcode.pdf);
			this.loadAttachment('sig', shortcode.sig);

			console.log('Edit mode', shortcode);

		}

		loadAttachment(type, id) {

			if (!id || typeof wp === 'undefined' || !wp.media) {
				return;
			}

			const attachment = wp.media.attachment(id);

			attachment.fetch().done(() => {
				const data = attachment.toJSON();
				this.setSelection(type, data);
			});

		}

		openDialog(editor) {

			this.editor = editor;

			this.detectEditingShortcode();

			console.log('Dialog mode:', this.mode);

			this.updateInsertButtonState();

			this.dialog.addClass(
				'ecp-documents-dialog--visible'
			);

		}

		closeDialog() {

			this.dialog.removeClass(
				'ecp-documents-dialog--visible');

		}

		cancelDialog() {

			this.reset();

			this.closeDialog();

		}

		reset() {

			this.title = '';

			this.selectedPdf = this.createEmptySelection();

			this.selectedSig = this.createEmptySelection();

			this.dialog
				.find('.ecp-document-title')
				.val('');

			this.updateFileUI('pdf');

			this.updateFileUI('sig');

			this.updateInsertButtonState();

		}

	}

	const dialog = new ECPDocumentsDialog();

	$(function () {

		dialog.init();

	});

	window.ECPDocuments = dialog;

})(jQuery);
