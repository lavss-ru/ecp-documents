(function () {
	'use strict';

	tinymce.PluginManager.add('ecp_documents', function (editor) {

		editor.addButton('ecp_documents', {
			text: 'Документ с ЭП',

			onclick: function () {
				editor.windowManager.alert('Плагин ECP Documents подключен.');
			}
		});

	});
})();
