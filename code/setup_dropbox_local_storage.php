<?php   
	if ( php_sapi_name() != "cli" ) { echo "\nThis script is only allowed at the command line\n\n"; die(); }
	ini_set('display_errors', 'On');
        require_once('dropbox_oauth_lib.php');
	bootstrapStorage();
?>
