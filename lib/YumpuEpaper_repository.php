<?php

class YumpuEpaper_repository {
	
	/**
	 * 
	 * Helper um ein ePaper mit dem Dateinamen zu laden
	 * @param string $filename
	 * @throws YumpuEpaper_repository_exception
	 */
	public static function loadByFilename($filename) {
		global $wpdb;
		$table_name = $wpdb->prefix.'yumpu_documents';
		$qry = "SELECT id FROM ".$table_name." WHERE source_filename='".$filename."'";
		$row = $wpdb->get_row($qry);
		if(!$row) {
			throw new YumpuEpaper_repository_exception("item not found by filename ".$filename);
		}
		return YumpuEpaper_repository::loadById($row->id);
	}
	
	/**
	 * 
	 * Lädt ein ePaper und liefert das Objekt YumpuEpaper zurück. Bei Fehler wird eine Exception geworfen.
	 * @param integer $id
	 * @return YumpuEpaper
	 * @throws YumpuEpaper_repository_exception
	 */
	public static function loadById($id) {
		global $wpdb;
		$table_name = $wpdb->prefix.'yumpu_documents';
		$qry = "SELECT * FROM ".$table_name." WHERE id='".intval($id)."'";
		$row = $wpdb->get_row($qry);
		
		if(!$row) {
			throw new YumpuEpaper_repository_exception("item not found ".$id);
		}
		
		$ePaper = new YumpuEpaper();
		$ePaper->setId($row->id);
		$ePaper->setProgress_id($row->progress_id);
		$ePaper->setEpaper_id($row->epaper_id);
		$ePaper->setUrl($row->url);
		$ePaper->setShort_url($row->short_url);
		$ePaper->setTitle($row->title);
		$ePaper->setDescription($row->description);
		$ePaper->setSource_filename($row->source_filename);
		$ePaper->setStatus($row->status);
		$ePaper->setEmbed_code($row->embed_code);
		
		$YAPI = new YumpuAPI(WP_Yumpu::$API_TOKEN);
		if($ePaper->getStatus() == "progress") {
			$result = $YAPI->verify_status($ePaper->getProgress_id());
			if($result['state'] == "ready")  {
				//Wenn das Dokument erstellt ist
				$ePaper->setStatus('ready');
				$ePaper->setEpaper_id($result['ePaper_id']);
				$ePaper->setUrl($result['url']);
				$ePaper->setShort_url($result['short_url']);
				$ePaper->setEmbed_code($result['embed_code']);
				YumpuEpaper_repository::store($ePaper);
			}
		}
		
		return $ePaper;
	}
	
	
	public static function removeByFilename($filename) {
		
	}
	
	public static function removeById($id) {
		
	}
	
	/**
	 * Liefert ein Array mit den YumpuEpapers
	 */
	public static function getAll() {
		global $wpdb;
		$table_name = $wpdb->prefix.'yumpu_documents';
		$items = array();
		
		$qry = "SELECT id FROM ".$table_name;
		$result = $wpdb->get_results($qry);
		foreach($result as $data) {
			$items[] = YumpuEpaper_repository::loadById($data->id);
		}
		
		
		return $items;
	}
	
	/**
	 * 
	 * Erstellt ein neues Yumpu ePaper
	 * @param string $file_to_import
	 * @param string $title
	 * @param description $description
	 * @throws YumpuEpaper_repository_exception
	 */
	public static function create($file_to_import, $title, $description = "") {
		global $wpdb;
		$table_name = $wpdb->prefix.'yumpu_documents';
		
		if(strlen($title) < 5 || strlen($title) > 255) {
			throw new YumpuEpaper_repository_exception("need title - Min. length 5 characters, max. length 255 characters");
		}
		
		if(!empty($description) && strlen($description) < 5) {
			throw new YumpuEpaper_repository_exception("description to short - Min. length 5 characters, max. length 2500 characters");
		}
		
		if(!empty($description) && strlen($description) > 2500) {
			throw new YumpuEpaper_repository_exception("description to long - Min. length 5 characters, max. length 2500 characters");
		}
		
		if(empty($description)) {
			$description = null;
		}
		
		//Nun übertragen wir die Daten an YUMPU
		$YAPI = new YumpuAPI(WP_Yumpu::$API_TOKEN);
		try {
			$result = $YAPI->document_add($file_to_import, $title, $description);
			
			
			$source_filename = basename($file_to_import);
			
			/**
			 * Es hat bisher alles geklappt, daher speichern wir die Daten nun in die Datenbank
			 */
			 $qry = sprintf(
			 	'INSERT INTO '.$table_name.' (progress_id, title, description, source_filename, status) VALUES (\'%s\', \'%s\', \'%s\', \'%s\', \'%s\')',
			 	$result->progress_id, $title, $description, $source_filename, 'progress'
			 );
			 $wpdb->query($qry);
			 return $wpdb->insert_id;
		} catch (YumpuAPI_Exception $e) {
			// Eigentlich nicht so toll Exception aus einem Try/Catch werfen
			// Könnte man besser lösen.
			throw new YumpuEpaper_repository_exception(implode("<br>", $YAPI->get_errors()));
		}
	}
	
	/**
	 * 
	 * Speichert ein ePaper Object in der Datenbank.
	 * @param YumpuEpaper $ePaper
	 */
	public static function store(YumpuEpaper $ePaper) {
		global $wpdb;
		$table_name = $wpdb->prefix.'yumpu_documents';
		
		$qry = "UPDATE ".$table_name." SET 
		status='".$ePaper->getStatus()."', 
		url='".$ePaper->getUrl()."', 
		short_url='".$ePaper->getShort_url()."', 
		embed_code='".$ePaper->getEmbed_code()."'
		WHERE id='".$ePaper->getId()."'";
		return $wpdb->query($qry);
	}
}

class YumpuEpaper_repository_exception extends Exception {}

?>