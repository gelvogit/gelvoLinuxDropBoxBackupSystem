
Welcome to the Gelvo Linux Drop Box Backup System

This system will allow you to backup your folders and mysql databases and store the backup in your DropBox account.

Install
=======

First you need to setup the Dropbox API

1) Goto https://www.dropbox.com/developers/apps?_tk=pilot_lp&_ad=topbar4&_camp=myapps  ( DropBox Developer Console )

2) Make an app

3) Edit  webroot/index.php and place the following settings: 

	$app_key                        = '';
        $app_secret                     = '';


4) Open webroot/index.php in a web browser and follow the steps on screen.

	Put all output settings at top of screen into -> code/dropbox_oauth_lib.php

	$app_key                        = '';
        $app_secret                     = '';
        $app_access_token               = '';
        $app_expires_in                 = '14400';
        $app_refresh_token              = '';


5) Run the command line php script: 

	php ./code/setup_dropbox_local_storage.php

6) Test that Oauth and local storage is working:

	php ./code/test_dropbox_oauth.php

Setup
=====
1) Edit backupconfig.xml

2) add -> backuprun.sh to your cron all all call manually from the command line.
