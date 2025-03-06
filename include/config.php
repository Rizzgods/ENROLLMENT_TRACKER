<?php
// Update database credentials for production server
defined('server') ? null : define("server", "localhost");
defined('user') ? null : define("user", "admi_greenvalley");
defined('pass') ? null : define("pass", "xr9%kxu%*my^+kf2");
defined('database_name') ? null : define("database_name", "admi_dbgreenvalley");

// Path configurations
$this_file = str_replace('\\', '/', __File__);
$doc_root = $_SERVER['DOCUMENT_ROOT'];

$web_root = str_replace(array($doc_root, "include/config.php"), '', $this_file);
$server_root = str_replace('config/config.php', '', $this_file);

define('web_root', $web_root);
define('server_root', $server_root);

// Add helpful constants for your application
define('SITE_TITLE', 'Online Enrollment System');

// Error reporting - change to 0 for production
define('SHOW_ERROR_DETAIL', 0);
?>