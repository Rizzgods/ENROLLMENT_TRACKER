<?php
//define the core paths
//Define them as absolute paths to make sure that require_once works as expected

//DIRECTORY_SEPARATOR is a PHP Pre-defined constants:
//(\ for windows, / for Unix)
defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);

// More flexible approach to define the application root directory
defined('SITE_ROOT') ? null : define('SITE_ROOT', str_replace('include', '', dirname(__FILE__)));

// Define the include directory path
defined('LIB_PATH') ? null : define('LIB_PATH', SITE_ROOT.DS.'include');

// Define a web root constant for URLs (useful for links, redirects, etc.)
$web_root = "http://" . $_SERVER['HTTP_HOST'];
$doc_root = preg_replace('/\/include$/', '', dirname($_SERVER['PHP_SELF']));
defined('WEB_ROOT') ? null : define('WEB_ROOT', $web_root . $doc_root . '/');

//load the database configuration first.
require_once(LIB_PATH.DS."config.php");
require_once(LIB_PATH.DS."function.php");
require_once(LIB_PATH.DS."session.php");
require_once(LIB_PATH.DS."accounts.php");
require_once(LIB_PATH.DS."autonumbers.php");
require_once(LIB_PATH.DS."departments.php");
require_once(LIB_PATH.DS."courses.php");
require_once(LIB_PATH.DS."subjects.php");
require_once(LIB_PATH.DS."sidebarFunction.php"); 
require_once(LIB_PATH.DS."instructors.php");
require_once(LIB_PATH.DS."schedules.php");
require_once(LIB_PATH.DS."students.php");
require_once(LIB_PATH.DS."classes.php");
require_once(LIB_PATH.DS."studentsubjects.php");
require_once(LIB_PATH.DS."studentschedule.php");
require_once(LIB_PATH.DS."grades.php");
require_once(LIB_PATH.DS."semester.php"); 
require_once(LIB_PATH.DS."studentdetails.php");
require_once(LIB_PATH.DS."ay.php");

require_once(LIB_PATH.DS."database.php");
?>