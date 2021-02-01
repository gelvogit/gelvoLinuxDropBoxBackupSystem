<?php
	$app_key                        = 'gujojudmrgd1ivo';
        $app_secret                     = '8yftbcslx4cffeg';

//////////////////////////////////////////////////////////////////////
	ini_set('display_errors', 'On');
	$app_redirect_uri               = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];

	if ( isset($_POST['authorization_code']) ) {

        	$request                        = array(
                	'code'                  => $_POST['authorization_code'],
	                'grant_type'            => 'authorization_code',
        	        'redirect_uri'          => $app_redirect_uri,
	        );
        	$url                            = "https://$app_key:$app_secret@api.dropbox.com/oauth2/token";
	        $ch                             = curl_init();
        	curl_setopt($ch, CURLOPT_URL, $url);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        	curl_setopt($ch, CURLOPT_POST, 1);
	        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
	        $result                         = curl_exec($ch);
	        curl_close ($ch);

	        echo nl2br(print_r($result,true));
		die();
	}

	if ( isset($_GET['code']) ) {

		echo "<form action='https://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."' method='POST'>";
		echo "<input type='hidden' name='authorization_code' value='" . $_GET['code'] . "'>";
		echo "<input type='submit' name='submit'             value='OAuth Set Up Step 2'> ";
		echo "</form>";

	} else {
 
                echo "<a href='https://www.dropbox.com/oauth2/authorize?client_id=$app_key&redirect_uri=$app_redirect_uri&response_type=code&token_access_type=offline'>OAuth Set Up Step 1</a>";
        }

?>
