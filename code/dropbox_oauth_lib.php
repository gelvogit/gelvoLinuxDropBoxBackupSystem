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
function appCurlDropBox( $url, $request = array(), $user_auth = false ) {
	$app_settings                   = oauthRefresh();
	$app_key                        = $app_settings['app_key'];
	$app_secret                  	= $app_settings['app_secret'];	

	$headers    			= array();
    	$headers[]  			= "Authorization: Bearer $app_token";
	$headers[]  			= "Content-Type: application/json";

	//$post_fields           		= json_encode($request);
	$post_fields			= $request;

    	$ch             		= curl_init();
    	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);	
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);

    	//curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	//if ( true === $user_auth ) {
//		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
//		curl_setopt($ch, CURLOPT_USERPWD, "$app_key:$app_secret");
	//}

	curl_setopt($ch, CURLINFO_HEADER_OUT, true);

	$result         		= curl_exec($ch);

echo "\n\n";
echo $result;
echo "\n\n";
$information = curl_getinfo($ch, CURLINFO_HEADER_OUT);
print_r($information);
echo "\n\n";
die();

	try {
        	$resultAry  		= json_decode($result,true);
    	} catch (Exception $e) {
        	$resultAry 		= array();
    	}
    	curl_close ($ch);
    	return $resultAry;
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
