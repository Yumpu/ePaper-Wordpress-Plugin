<?php

class YumpuAPI {
	/**
	 * URI zur API
	 */
	const API_URI = "http://api.yumpu.com/2.0/";

	/**
	 * Angabe in Minuten wie oft ein ePaper auf Statusänderungen gebprüft werden darf.
	 */
	const API_STATUS_RETRY_TIME = 1;
	
	/**
	 * API Command für den Benutzer.
	 */
	const API_CMD_USER_JSON = "user.json";
	
	
	/**
	 * API Command für Dateiupload
	 */
	const API_CMD_DOCUMENT_POST = "document/file.json";
	
	
	/**
	 * API Command für ProgressAbfrage
	 */
	const API_CMD_DOCUMENT_PROGRESS = "document/progress.json";
	
	
	/**
	 * Speichert Fehler in der API zum späteren Abruf
	 */
	private $errors = array();
	
	/**
	 * Token für die APU
	 */
	private $api_token;
	
	function __construct($api_token) {
		$this->api_token = $api_token;
	}
	
	/**
	 * 
	 * Registrierung von einem Account.
	 * Wenn keine Exception geworfen wird innerhalb dieser Function, dann wird der neue API_TOKEN zurückgeliefert.
	 * 
	 * @param string $email valid email address
	 * @param string $username Allowed characters a-z, A-Z, 0-9 and a dot, min. length 5 characters, max. length 30 characters
	 * @param string $password min. length 6 characters
	 * @param string $firstname min. length 2 characters, max. length 100 characters
	 * @param string $lastname min. length 2 characters, max. length 100 characters
	 * @param string $gender male or female
	 * @throws YumpuAPI_Exception
	 * @return string API_TOKEN
	 */
	public function account_create($email, $username, $password, $firstname, $lastname, $gender) {
		$data = array(
			'email' => $email,
			'username' => $username,
			'password' => $password,
			'gender' => $gender,
			'firstname' => $firstname,
			'lastname' => $lastname
		);
		
		$result = json_decode($this->remote_post(self::API_CMD_USER_JSON, $data));
		if(!is_object($result) || $result->state != "success") {
			if (isset($result->errors)) {
				foreach($result->errors as $field => $error){
					$this->errors[] = $error->message;
				}
			} elseif (isset($result->error)) {
				$this->errors[] = $result->error;
			}
			throw new YumpuAPI_Exception("API-Error");
		} else {
			return $result->user->access_token;
		}
	}
	
	/**
	 * 
	 * Prüft den API-Token, dazu werden die Accountdaten abgerufen.
	 * @throws YumpuAPI_Exception
	 */
	public function check_api_key() {
		$result = json_decode($this->remote_get(self::API_CMD_USER_JSON));
		if(!is_object($result) || $result->state != "success") {
			if (isset($result->errors)) {
				foreach($result->errors as $field => $error){
					$this->errors[] = $error->message;
				}
			} elseif (isset($result->error)) {
				$this->errors[] = $result->error;
			}
			throw new YumpuAPI_Exception("API-Error");
		} else {
			return true;
		}
	}
	
	public function document_add($source_file, $title, $description = null) {
		
		$data = array(
			'file' => '@'.$source_file,
			'title' => $title,
		);
		
		if($description !== null) {
			$data['description'] = $description;
		}
		
		$result = json_decode($this->remote_post(self::API_CMD_DOCUMENT_POST, $data, true));
		if(!is_object($result) || $result->state != "success") {
			if (isset($result->errors)) {
				foreach($result->errors as $field => $error){
					$this->errors[] = $error->message;
				}
			} elseif (isset($result->error)) {
				$this->errors[] = $result->error;
			}
			throw new YumpuAPI_Exception("API-Error");
		} else {
			return $result;
		}
	}
	
	public function document_remove() {
		
	}
	
	public function document_update() {
		
	}
	
	public function verify_status($progress_id) {
		$response = array(
			'state' => '',
			'embed_code' => '',
			'url' => '',
			'short_url' => '',
			'ePaper_id' => ''
		);
		
		/**
		 * Bei jeder Prüfung wird die Temporäre Datei geändert. Dadurch können wir die Abfragen auf das notwendigste begrenzen
		 */
		$temp_file = sys_get_temp_dir().DIRECTORY_SEPARATOR.'yumpu_checkfile_'.md5($progress_id);
		$diff = 60 * self::API_STATUS_RETRY_TIME;
		if(file_exists($temp_file) && filemtime($temp_file) > (time() - $diff)) {
			$response['state'] = "progress";
			return $response;
		} else {
			/**
			 * Wir können die Prüfung durchführen und setzen das Tempfile zur Kontrolle
			 */
			touch($temp_file);
		}

		$result = json_decode($this->remote_get(self::API_CMD_DOCUMENT_PROGRESS.'?id='.$progress_id));
		if(is_object($result) && is_object($result->document) && isset($result->document->state)) {
			$state = $result->document->state;
			if($state == "rendering_in_progress") {
				$response['state'] = "progress";
			}
		} elseif(is_object($result) && is_array($result->document)) {
			/**
			 * Wenn die Temporäre Datei noch vorhanden ist,
			 * werden wir die nun löschen.
			 */
			if(file_exists($temp_file)) {
				unlink($temp_file);
			}
			
			$response = array(
				'state' => 'ready',
				'embed_code' => $result->document[0]->embed_code,
				'url' => $result->document[0]->url,
				'short_url' => $result->document[0]->short_url,
				'ePaper_id' => $result->document[0]->id
			);
		}

		return $response;
	}
	
	public function get_errors() {
		return $this->errors;
	}
	
	public function has_errors() {
		if(count($this->errors)>0) {
			return true;
		}
		return false;
	}
	
	
	
	private function remote_post($command, $data, $multiform = false) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, self::API_URI.$command);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		
		$header_arr = array();
		$header_arr[] = 'X-ACCESS-TOKEN: '.$this->api_token;
		
		if($multiform) {
			$header_arr[] = 'Content-Type: multipart/form-data';
		}
		
		if(count($header_arr) > 0) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header_arr);
		}
		
		
		$return_data = curl_exec($ch);
		
		$http_code_response = curl_getinfo($ch,CURLINFO_HTTP_CODE);
		$http_error = curl_error($ch);
		if($http_error) {
			$this->errors[] = $http_error;
		}
		
		curl_close($ch);
		return $return_data;
	}
	
	private function remote_get($command) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, self::API_URI.$command);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		if($this->api_token !== null) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-ACCESS-TOKEN: '.$this->api_token));
		}
		$return_data = curl_exec($ch);
		
		$http_code_response = curl_getinfo($ch,CURLINFO_HTTP_CODE);
		$http_error = curl_error($ch);
		if($http_error) {
			$this->errors[] = $http_error;
		}
		
		curl_close($ch);
		return $return_data;
	}
}

/**
 * Exception Klasse für die Yumpu-API
 */
Class YumpuAPI_Exception extends Exception {}

?>