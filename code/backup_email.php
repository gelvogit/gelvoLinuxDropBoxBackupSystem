<?php

function backup_email_send($_to,$_from,$_fromname,$_subject,$_message,$_username,$_password,$_server='smtp.gmail.com',$_auth=true,$_port=465,$_secure='ssl',$_html=false) {

/*
echo $_to;
echo "\n";
echo $_from;
echo "\n";
echo $_fromname;
echo "\n";
echo $_subject;
echo "\n";
echo $_username;
echo "\n";
echo $_password;
echo "\n";
echo $_server;
echo "\n";
echo $_auth;
echo "\n";
echo $_port;
echo "\n";
echo $_secure;
echo "\n";
echo $_html;
echo "\n";
*/
	require_once("phpmailer/class.phpmailer.php");

	$mail = new PHPMailer();
        $mail->IsSMTP();
	$mail->Host             = $_server;
        $mail->Port             = $_port;
        $mail->SMTPSecure       = $_secure; // ssl or empty string
        $mail->SMTPAuth         = $_auth;   // true or false

        if ($_auth) {
        	$mail->Username = $_username;
                $mail->Password = $_password;
	}

        $mail->From 	= $_from;
	$mail->FromName = $_fromname;
	$mail->AddAddress($_to);

        $mail->IsHTML($_html); // true or false

	$mail->Subject = $_subject;
        $mail->Body    = $_message;

        $result = $mail->Send();

//echo $result;
	
	return $result;
}

?>
