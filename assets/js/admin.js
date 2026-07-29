(function ($) {
	'use strict';

	class ECPDocumentsDialog {

		constructor() {

			this.dialog = null;

			this.title = '';

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
				filename: '',
				url: ''
			};

		}

		init() {

			this.dialog = $('#ecp-documents-dialog');

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
				.find('.ecp-select-file')
				.on('click', this.handleSelectFile.bind(this));

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
				filename: attachment.filename,
				url: attachment.url
			};

			if (type === 'pdf') {

				this.selectedPdf = selection;

			} else {

				this.selectedSig = selection;

			}

			this.updateFileUI(type);

		}

		updateFileUI(type) {

			const selection = type === 'pdf'
				? this.selectedPdf
				: this.selectedSig;

			const field = this.dialog.find(
				'.ecp-field[data-type="' + type + '"]'
			);

			field
				.find('.ecp-file-placeholder')
				.prop('hidden', true);

			field
				.find('.ecp-file-link')
				.text(selection.filename)
				.attr('href', selection.url)
				.prop('hidden', false);

		}

		openDialog() {

			this.dialog.addClass('ecp-documents-dialog--visible');

		}

		closeDialog() {

			this.dialog.removeClass('ecp-documents-dialog--visible');

		}

		reset() {

			this.title = '';

			this.selectedPdf = this.createEmptySelection();

			this.selectedSig = this.createEmptySelection();

		}

	}

	const dialog = new ECPDocumentsDialog();

	$(function () {

		dialog.init();

	});

	window.ECPDocuments = dialog;

})(jQuery);
