<?php
class BackupSession {
	
	public $errors;
	public $error_messages;
	public $total_size;
	public $folder_list;
	public $mysql_list;
	public $config;
	public $messages;
	public $options;
	public $sftp_list;
	public $sftp_zip_list;
	public $dropbox_list;

	public function __construct() {
		$this->total_size = 0;
		$this->errors = 0;
		$this->error_messages = array();
		$this->folder_list = array();
		$this->mysql_list = array();
		$this->config = array();
		$this->sftp_zip_list = array();
		$this->messages = "";
		$this->options = array();
		$this->dropbox_list = array();
	}

	public function getOptions() {
		$result = "";
		for ($i=0; $i < count($this->options); $i++) {
			$result .= 	$this->options[$i]." ";
		}		
		return $result;
	}

	public function appendRemoteList($name) {
		$this->remote_list .= $name."\n";
	}

	public function appendSFTPList($name) {
		$this->sftp_list .= $name."\n";
		array_push($this->dropbox_list, $name);
	}

	public function appendMessages($message) {
		$this->messages .= $message."\n";
	}

	public function appendErrors($message) {
		array_push($this->error_messages, $message);
		$this->errors = $this->errors + 1;	
	}	

	public function appendSize($size) {
		$this->total_size = bcadd($this->total_size,$size);
	}
}
?>
