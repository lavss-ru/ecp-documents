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

                openDialog() {

                        this.dialog.addClass('ecp-documents-dialog--visible');

                }

                closeDialog() {

                        this.dialog.removeClass('ecp-documents-dialog--visible');

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

                }

        }

        const dialog = new ECPDocumentsDialog();

        $(function () {

                dialog.init();

        });

        window.ECPDocuments = dialog;

})(jQuery);
