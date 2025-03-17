<?php

require_once ("database.php");
require_once("../../include/initialize.php");

if(!isset($_SESSION['ACCOUNT_ID'])){
	redirect(web_root."admin/index.php");
}

$view = (isset($_GET['view']) && $_GET['view'] != '') ? $_GET['view'] : '';
 $title="Superadmin"; 
 $header=$view; 
switch ($view) {
	case 'list' :
		$content    = 'list.php';		
		break;

	case 'logs' :
		$content    = 'user_logs.php';		
		break;

	case 'create' :
			$content    = 'account_create.php';		
			break;	

	default :
		$content    = 'list.php';		
}
require_once ("../theme/templates.php");

?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="js/list.js"></script>

  
