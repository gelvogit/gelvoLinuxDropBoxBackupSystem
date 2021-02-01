<?php
	if ( php_sapi_name() != "cli" ) { echo "\nThis script is only allowed at the command line\n\n"; die(); }

	$tar_args  = " czf ";
	$tar_ext   = ".tgz";

	ini_set('display_errors', 'On');
	set_time_limit(0);
	ignore_user_abort(true);
	set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__));
	date_default_timezone_set("Australia/South");

	// Dropbox key refresh code
	require_once("dropbox_oauth_lib.php");
	

	// Third party Dropbox Code
        require_once __DIR__ . '/vendor/autoload.php';
        use Kunnu\Dropbox\Dropbox;
        use Kunnu\Dropbox\DropboxApp;
        use Kunnu\Dropbox\DropboxFile;

	//Backup Code
	require_once("backup_load_xml.php");
	require_once("backup_functions.php");
	require_once("backup_class.php");
	require_once("backup_email.php");
	
	logger_backup("Backup Started");

	define('NOFOLDERS', 		'NOFOLDERS');
	define('NOMYSQL',		'NOMYSQL');
	define('NOEMAIL', 		'NOEMAIL');
	define('NODROPBOX', 		'NODROPBOX');

	//construct model
	$backupSessionObj = build_backup_session($argv);

	// folders_backup
	if (in_array(NOFOLDERS, $argv)) {
		logger_backup("no folders backups"); 
	}else {
		folders_backup($backupSessionObj);
	}	

	// mysql_backup
    	if (in_array(NOMYSQL, $argv)) {
		logger_backup("no mysql backups");
    	} else {
        	mysql_backup($backupSessionObj);
    	}

	// dropbox_backup
    	if (in_array(NODROPBOX, $argv)) {
		logger_backup("no DROPBOX copy");
    	} else {
		$config                 = $backupSessionObj->config;	

		// requires app.php
		oauthRefresh();
	        $settings = getAppSettings();


		$dropbox_key            = $settings['app_key'];
        	$dropbox_secret         = $settings['app_secret'];
        	$dropbox_token          = $settings['app_access_token'];
        	$dropbox_chunksize      = $config[DROPBOX_CHUNKSIZE];
	
        	$app                    = new DropboxApp($dropbox_key, $dropbox_secret, $dropbox_token);
        	$dropbox                = new Dropbox($app);

        	dropbox_backup($backupSessionObj, $dropbox);
    	}

	// email_results
	if (in_array(NOEMAIL, $argv)) {
		logger_backup("no reporting required");
	} else {	
		email_results($backupSessionObj);
	}	

	logger_backup("Backup Ended");
?>
