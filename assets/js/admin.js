(function ($) {
	'use strict';

	window.ECPDocuments = {

		dialog: null,

		title: '',

		selectedPdf: null,

		selectedSig: null,

		init: function () {
			// Инициализация плагина.
		},

		openDialog: function () {

			alert('admin.js подключен');

		},

		closeDialog: function () {
			// Здесь позже будет закрытие модального окна.
		},

		reset: function () {

			this.title = '';
			this.selectedPdf = null;
			this.selectedSig = null;

		}

	};

	$(function () {

		window.ECPDocuments.init();

	});

})(jQuery);
