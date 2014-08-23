/**
 * CKEditor KwitangCMS Configuration
 */

CKEDITOR.editorConfig = function( config ) {
    config.language       = 'id';
    config.allowedContent = true;
    config.skin           = 'office2013';
    config.extraPlugins   = 'autogrow';
    config.toolbar        = 'Kwitang';
    config.toolbar_Basic =  [
        [ 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'Bold', 'Italic',
        'Underline', 'Strike', '-', 'Link', 'Image', 'Flash', '-', 'Source' ]
    ];
    config.toolbar_Kwitang = [
        ['Copy','Paste','-','Undo','Redo','Font','FontSize','-','Outdent','Indent','-'],
        ['HorizontalRule','Blockquote','Table','-','Flash','Image','-','Source'],
        '/',
        ['PasteText','PasteFromWord','-','NumberedList','BulletedList','-','Bold','Italic','Underline','-'],
        ['Strike','TextColor','BGColor','-','JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],
        ['SpecialChar', '-','Link','Unlink','-','RemoveFormat','Maximize']
    ]
};
