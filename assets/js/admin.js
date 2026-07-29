(function ($) {
	'use strict';

	class ECPDocumentsDialog {

		constructor() {

			this.dialog = null;

			this.title = '';

			this.selectedPdf = null;

			this.selectedSig = null;

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

		}

		openDialog() {

			this.dialog.addClass('ecp-documents-dialog--visible');

		}

		closeDialog() {

			this.dialog.removeClass('ecp-documents-dialog--visible');

		}

		reset() {

			this.title = '';

			this.selectedPdf = null;

			this.selectedSig = null;

		}

	}

	const dialog = new ECPDocumentsDialog();

	$(function () {

		dialog.init();

	});

	window.ECPDocuments = dialog;

})(jQuery);
