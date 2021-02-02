<?php
	$app_key   			= '';
        $app_secret  			= '';
        $app_access_token   		= '';
	$app_expires_in			= '';	
	$app_refresh_token		= '';
function getAppSettings() {
	$filename                       = __DIR__ . '/dropbox_settings.db';
	if ( file_exists($filename) ) {
		return getAppSettingsDatabase();
	}
	return getAppSettingsHardCoded();
}
function getAppSettingsDatabase() {
	$filename                       = __DIR__ . '/dropbox_settings.db';
	$sqlHandle 			= new PDO("sqlite:".$filename);
	$Iterator 			= $sqlHandle->query("SELECT * FROM dropbox");
        foreach($Iterator as $Row) {
		$app_settings                   = array(
			'storage'		=> 'database',
                	'app_key'               => $Row['app_key'],
                	'app_secret'            => $Row['app_secret'],
                	'app_access_token'      => $Row['app_access_token'],
                	'app_expires_in'        => $Row['app_expires_in'],
			'app_expires_in_actual'	=> $Row['app_expires_in_actual'],
			'app_refresh_token'	=> $Row['app_refresh_token'],
		);
        	unset($sqlHandle);
		return $app_settings;
	}
}
function getAppSettingsHardCoded() {
        global $app_key;
        global $app_secret;
        global $app_uid;
        global $app_access_token;
        global $app_expires_in;
        global $app_token_type;
        global $app_scope;
        global $app_account_id;
	global $app_refresh_token;
	$app_settings 			= array(
		'storage'               => 'hard coded',
		'app_key' 		=> $app_key,
		'app_secret' 		=> $app_secret,
		'app_access_token'    	=> $app_access_token,
        	'app_expires_in'      	=> $app_expires_in,
		'app_expires_in_actual' => 0,
		'app_refresh_token'	=> $app_refresh_token,
	);
	return $app_settings;
}
function oauthRefresh() {
	$app_settings                   = getAppSettings();
        $app_key                        = $app_settings['app_key'];
        $app_secret                     = $app_settings['app_secret'];
	$app_refresh_token		= $app_settings['app_refresh_token'];
        $request                        = array(
                'grant_type'            => 'refresh_token',
                'refresh_token'         => $app_refresh_token,
        );
        $url                            = "https://$app_key:$app_secret@api.dropbox.com/oauth2/token";
        $ch                             = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        $result                         = curl_exec($ch);
        curl_close ($ch);
	$result 			= json_decode($result);
	updatestorage($result->access_token, $result->expires_in);
	return getAppSettings();	
}
function bootstrapStorage() {
	$filename 			= __DIR__ . '/dropbox_settings.db';
	@unlink($filename);
	$app_settings                   = getAppSettings();
    	$app_key 			= $app_settings['app_key'];
     	$app_secret 			= $app_settings['app_secret'];
        $app_access_token 		= $app_settings['app_access_token'];
        $app_expires_in 		= $app_settings['app_expires_in'];
	$app_expires_in_actual		= time() + $app_settings['app_expires_in'];
	$app_refresh_token		= $app_settings['app_refresh_token'];
	try {
    		$sqlHandle = new PDO("sqlite:".$filename);
	} catch(PDOException $e) {
    		echo $e->getMessage()." :: ".$filename;
		die();
	}
	$sqlHandle->exec("CREATE TABLE IF NOT EXISTS dropbox (
	ID INTEGER PRIMARY KEY, 
	app_key TEXT,
	app_secret TEXT,
	app_access_token TEXT,
	app_expires_in TEXT,
	app_expires_in_actual TEXT,
	app_refresh_token TEXT 
	)");
	$sqlHandle->beginTransaction();
	$sql  =  "INSERT INTO dropbox ";
	$sql .= "(  app_key,   app_secret,   app_access_token,   app_expires_in,   app_expires_in_actual,   app_refresh_token) VALUES ";
	$sql .= "('$app_key','$app_secret','$app_access_token','$app_expires_in','$app_expires_in_actual','$app_refresh_token') " ;
	$sqlHandle->exec($sql);
	$sqlHandle->commit();
	$Iterator = $sqlHandle->query("SELECT * FROM dropbox");
	foreach($Iterator as $Row) {
		print_r($Row);
	}
	unset($sqlHandle);
	echo "\n\n";
}
function updateStorage($app_access_token, $app_expires_in) {
	$filename = __DIR__ . '/dropbox_settings.db';
        try {
                $sqlHandle = new PDO("sqlite:".$filename);
        } catch(PDOException $e) {
                echo $e->getMessage()." :: ".$filename;
                die();
        }
	$app_expires_in_actual          = time() + $app_expires_in;
        $sqlHandle->beginTransaction();
        $sqlHandle->exec("UPDATE dropbox SET app_access_token = '$app_access_token', app_expires_in = '$app_expires_in', app_expires_in_actual = '$app_expires_in_actual' ");
        $sqlHandle->commit();
        unset($sqlHandle);
}
?>
