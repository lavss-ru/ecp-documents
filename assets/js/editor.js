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

					window.ECPDocuments.openDialog(editor);

					return;
				}

				console.error(
					'ECP Documents: admin.js is not loaded'
				);

			}

		});

	});

})();
