/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	var path = '../';
	config.uiColor = 'silver';
	config.width = '640';
	config.filebrowserBrowseUrl 	 = path+'/ckeditor/filemanager/index.html';
	config.filebrowserImageBrowseUrl = path+'/ckeditor/filemanager/index.html?type=Images';
	config.filebrowserFlashBrowseUrl = path+'/ckeditor/filemanager/index.html?type=Flash';
	config.filebrowserUploadUrl 	 = path+'/ckeditor/filemanager/connectors/php/filemanager.php';
	config.filebrowserImageUploadUrl = path+'/ckeditor/filemanager/connectors/php/filemanager.php?command=QuickUpload&type;=Images';
	config.filebrowserFlashUploadUrl = path+'/ckeditor/filemanager/connectors/php/filemanager.php?command=QuickUpload&type;=Flash';
	config.toolbar_Full =	[['Bold','TextColor', 'Table', 'Format' ,'Strike','Underline','Italic', '-', 'NumberedList', 'BulletedList', '-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock', '-', 'Link', 'Unlink','-', 'Image', 'Style', '-', 'Source']];

};
