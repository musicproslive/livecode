<?php
/**************************************************************************************
Created by : Lijesh
Created on : 02-07-2011
Purpose    : Include Class Files.
**************************************************************************************/
//require_once	$projectPath.'classes/ipcountry.php';
require_once	$projectPath.'classes/territory.php';
//require_once	$projectPath.'classes/masters.php';
require_once	$projectPath.'classes/cms.php';
require_once	$projectPath.'classes/defaults.php';
//require_once	$projectPath.'classes/members.php';

require_once	$projectPath.'classes/adminUser.php';
require_once	$projectPath.'libs/recaptcha/recaptchalib.php';
require_once	$projectPath.'libs/paypalpro/paypal_pro.inc.php';

// Lijesh ST
require_once $projectPath.'classes/instrument.php';
require_once $projectPath.'classes/userManagement.php';
//require_once $projectPath.'classes/userCategory.php';
require_once $projectPath.'classes/timeZone.php';
//require_once $projectPath.'classes/currency.php';
require_once $projectPath.'classes/userClass.php';
require_once $projectPath.'classes/newsFeed.php';
require_once $projectPath.'classes/groupManagement.php';
//require_once $projectPath.'classes/messageManagement.php';
require_once $projectPath.'classes/albumlist.php';
require_once $projectPath.'classes/mailManagment.php';
require_once $projectPath.'classes/orbital.php';
// Lijesh END

// Arvind
require_once $projectPath.'classes/userCourse.php';
require_once $projectPath.'classes/privacyControlLib.php';
require_once $projectPath.'classes/friendsManagement.php';
require_once $projectPath.'classes/advertisementManagement.php';


//milanmilan
require_once	$projectPath.'classes/catSubcatManagement.php';


//Suneesh
require_once $projectPath.'classes/eventManagement.php';
require_once $projectPath.'classes/notificationManagement.php';
require_once $projectPath.'classes/userLog.php';//User Log
require_once $projectPath.'classes/videoOdemand.php';

//Prem
require_once $projectPath.'classes/dataValidation.php';
//Default Value Settings
$defaultsObj	=	new defaults();
$defaultsObj->defineConstants();

?>