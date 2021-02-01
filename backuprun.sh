#!/bin/bash
PHP_BIN="php -d display_errors "
WORKING_FOLDER="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
XML_FILE="backupconfig.xml"
LOG_FILE="backup.log"

$PHP_BIN $WORKING_FOLDER/code/backup_main.php $WORKING_FOLDER/$XML_FILE $WORKING_FOLDER/log/$LOG_FILE 

# Options which override the config.xml
#=============
#NOFOLDERS - don't backup local folders
#NOMYSQL - don't dump any MySQL Databases
#NOEMAIL - don't send an email report
#NODROPBOX - don't copy to DropBox
