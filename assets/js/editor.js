(function () {
	'use strict';

	tinymce.PluginManager.add('ecp_documents', function (editor) {

		editor.addButton('ecp_documents', {

			text: 'Документ с ЭП',

			onclick: function () {

				if (
					window.ECPDocuments &&
					typeof window.ECPDocuments.openDialog === 'function'
				) {

					window.ECPDocuments.openDialog();

					return;
				}

				alert('Не удалось загрузить admin.js');

			}

		});

	});

})();
