<?php
//define the core paths
//Define them as absolute paths to make sure that require_once works as expected

//DIRECTORY_SEPARATOR is a PHP Pre-defined constants:
//(\ for windows, / for Unix)
defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);

// Fix the path - remove 'onlineenrolmentsystem' from the path since we're already in the root directory on the server
defined('SITE_ROOT') ? null : define('SITE_ROOT', $_SERVER['DOCUMENT_ROOT']);

defined('LIB_PATH') ? null : define('LIB_PATH', SITE_ROOT.DS.'include');

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


