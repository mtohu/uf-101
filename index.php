<?php

define('APPLICATION_PATH', dirname(__FILE__));
define('APPPATH', 'application/');

$application = new Yaf_Application( APPLICATION_PATH . "/conf/application.ini");

$application->bootstrap()->run();
?>
