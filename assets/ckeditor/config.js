/**
 * CKEditor Custom Configuration
 */

CKEDITOR.editorConfig = function( config ) {
    config.language = 'id';
    config.allowedContent = true;
    config.toolbarGroups = [
        { name: 'mode',        groups: ['mode', 'undo' ] },
        { name: 'clipboard',   groups: [ 'clipboard' ] },
        { name: 'editing',     groups: [ 'selection', 'spellchecker' ] },
        { name: 'insert' },
        '/',
        { name: 'basicstyles', groups: [ 'align', 'basicstyles' ] },
        { name: 'colors',      groups: [ 'colors', 'cleanup' ] },
        { name: 'paragraph',   groups: [ 'list', 'indent', 'blocks' ] },
        '/',
        { name: 'links' },
        { name: 'styles' },
        { name: 'tools' },
        { name: 'others' }
    ];
    config.toolbar_Basic =  [
        [ 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'Bold', 'Italic', 'Underline', 'Strike', '-', 'Link', 'Image', 'Flash', '-', 'Source' ]
    ];
};
