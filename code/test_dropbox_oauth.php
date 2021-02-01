<?php   
	ini_set('display_errors', 'On');
        require_once('app.php');
	
	$settings = getAppSettings();
        print_r($settings);

	oauthRefresh();

	$settings = getAppSettings();
        print_r($settings);
?>
