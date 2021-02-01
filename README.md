# gelvoLinuxDropBoxBackupSystem
Welcome to the Gelvo Linux Drop Box Backup System

This system will allow you to backup your folders and mysql databases and store the backup in your DropBox account.

Install
=======

First you need to setup the Dropbox API

1) Goto https://www.dropbox.com/developers/apps?_tk=pilot_lp&_ad=topbar4&_camp=myapps  ( DropBox Developer Console )

2) Click 'Create a new app'
	- select Scoped access
	- select App folder	
	- select an app name

	Copy you App key and App secret
	

3) Edit  webroot/index.php and place the following settings: 

	$app_key                        = '';
        $app_secret                     = '';

	Move webroot/index.php to a website where you can call it in a browser, you can rename it to whatever you want

	Go back to your app in the above step and enter into 'Redirect URIs' the url you created to get to -> webroot/index.php


4) Open webroot/index.php in a web browser and follow the steps on screen.

	Put all output settings at top of screen into -> code/dropbox_oauth_lib.php

	$app_key                        = ''; // from previous steps
        $app_secret                     = ''; // from previous steps	

        $app_access_token               = '';
        $app_expires_in                 = '';
        $app_refresh_token              = '';


5) Run the command line php script: 

	php ./code/setup_dropbox_local_storage.php

	if it all works you will see an array of stuff

6) Test that Oauth and local storage is working:

	php ./code/test_dropbox_oauth.php

	if it all works you will see an array of stuff

Setup
=====
1) Edit backupconfig.xml

2) add -> backuprun.sh to your cron all all call manually from the command line.
