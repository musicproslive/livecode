/*---------------------------------------------------------
  Configuration
---------------------------------------------------------*/
// Set culture to display localized messages
var culture = 'en';

// Autoload text in GUI
var autoload = true;

// Display full path - default : false
var showFullPath = false;

// Set this to the server side language you wish to use.
var lang = 'php'; // options: lasso, php, py

// Set this to the directory you wish to manage.

var am = document.location.pathname.substring(1, document.location.pathname
.lastIndexOf('/') + 1);
// Set this to the directory you wish to manage.
var fileRoot = '/' + am + 'userfiles/';
//var fileRoot = '/app/webroot/userfiles/';

// Show image previews in grid views?
var showThumbs = true;


