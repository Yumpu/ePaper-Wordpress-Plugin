<?php

/**
 * Die Klasse FileAPI ist für das speichern und löschen der Dateien zuständig
 */
class FileAPI {
	/**
	 * Verzeichnis welches im WP-Upload Dir angelegt wird.
	 */
	const MY_UPLOAD_DIR = "yumpu_files";
	
	private $upload_dir;
	
	private $accepted_extensions = array();
	
	/**
	 * 
	 * Mittels dem Parameter kann festgelegt werden welche Dateiendungen gestattet sind.
	 * Standardmäßig werden KEINE Dateien akzeptiert.
	 * @param array $accepted_extensions
	 * Example: new FileAPI(array('png','pdf','gif));
	 */
	function __construct($accepted_extensions = array()) {
		$wp_upload_dir = wp_upload_dir();
		
		$this->accepted_extensions = $accepted_extensions;
		$this->upload_dir = $wp_upload_dir['basedir'].DIRECTORY_SEPARATOR.FileAPI::MY_UPLOAD_DIR;
	}
	
	/**
	 * 
	 * Importiert eine Datei aus dem Upload in das Upload Verzeichnis
	 * @param string $source Pfad zur Datei die importiert werden soll
	 * @param string $org_filename Dateiname der original Datei
	 * @return string Liefert den vollständigen Pfad zur Datei.
	 */
	public function import($source, $org_filename) {
		$this->verfiy_env();
		
		if(!is_uploaded_file($source)) {
			throw new FileAPI_exception("this file was not correctly uploaded (".$source.")");
		}
		
		$this->verify_filename($org_filename);
		
		$destination = $this->upload_dir.DIRECTORY_SEPARATOR.$org_filename;
		
		#if(file_exists($destination)) {
		#	throw new FileAPI_exception("file already exists");
		#}
		
		if(move_uploaded_file($source, $destination)) {
			return $destination;
		}
		
		throw new FileAPI_exception("failed to save file");
	}
	
	/**
	 * 
	 * Löscht eine Datei aus dem Upload-Verzeichnis
	 * @param string $filename Dateiname der Datei die gelöscht werden soll
	 */
	public function delete($filename) {
		$destination = $this->upload_dir.DIRECTORY_SEPARATOR.$filename;
		if(file_exists($destination)) {
			return unlink($destination);
		} else {
			return true;
		}
	}
	
	
	private function verfiy_env() {
		if(!is_dir($this->upload_dir)) {
			if(!mkdir($this->upload_dir)) {
				throw new FileAPI_exception("upload directory create failed (".$this->upload_dir.")");
			}
		}
	}
	
	private function verify_filename($filename) {
		$data = pathinfo($filename);
		if(!isset($data['extension'])) {
			throw new FileAPI_exception("unexpected file extension");	
		}
		
		if(in_array($data['extension'], $this->accepted_extensions)) {
			return true;
		}
		
		throw new FileAPI_exception("file extension ".$data['extension']." is not allowed");
	}
}


class FileAPI_exception extends Exception {}
?>