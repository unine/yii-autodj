<?php
define('LIBRARY',dirname(__FILE__).DIRECTORY_SEPARATOR.'upload'.DIRECTORY_SEPARATOR.'files');
define('CONSOLE',dirname(__FILE__).DIRECTORY_SEPARATOR.'console.php');
define('RADIO',dirname(__FILE__).DIRECTORY_SEPARATOR.'liquidsoap'.DIRECTORY_SEPARATOR.'radio.liq');

// change the following paths if necessary
$yii=dirname(__FILE__).'/yii/yii.php';
$config=dirname(__FILE__).'/protected/config/console.php';

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

require_once($yii);
Yii::createConsoleApplication($config)->run();
