<?php

class WP_Yumpu_Admin_Editor {
	private $plugin_path;
	
	function __construct($plugin_path) {
		$this->plugin_path = $plugin_path;		
	}
	
	public function run() {
		$method = $_REQUEST['method'];
		switch ($method) {
			case 'upload':
				$this->handle_upload();
				break;
			case 'publish':
				$this->handle_publish();
				break;
			default:
				$this->show_layer();
				break;
		}
	}
	
	
	private function show_layer() {
		$HB = new HtmlBuilder('admin_editor.php', $this->plugin_path.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR);
		$HB->assign('plugin_url', WP_Yumpu::$PLUGIN_URL);
		
		$ePapers = YumpuEpaper_repository::getAll();
		$HB->assign('ePapers', $ePapers);
		
		$upload_max_filesize = ini_get('upload_max_filesize');
		$HB->assign('upload_max_filesize', $upload_max_filesize);
		
		$ePapers = YumpuEpaper_repository::getAll();
		$HB->assign('epapers', $ePapers);
		
		echo $HB->get_data();
	}
	
	private function handle_upload() {
		$id = null;
		if(isset($_FILES['Filedata']) && !empty($_FILES['Filedata']['tmp_name']) && !empty($_POST['title'])) {
			$FileAPI = new FileAPI(array('pdf'));
			try {
				$imported_filename = $FileAPI->import($_FILES['Filedata']['tmp_name'], $_FILES['Filedata']['name']);
				//Upload war erfolgreich und die Datei wurde korrekt abgelegt.
				//$id = YumpuEpaper_repository::create($imported_filename, $_POST['title']);
				
				$status = "success";
			} catch(FileAPI_exception $e) {
				$status = "error";
				$message = $e->getMessage();
			} catch (YumpuEpaper_repository_exception $e) {
				/**
				 * Wenn möglich sollten wir die importierte Datei direkt entfernen.
				 */
				$FileAPI->delete($_FILES['yc_file']['name']);
				$status = "error";
				$message = $e->getMessage();
			}
		} else {
			$status = "error";
			$message = "no input file";
		}		
		
		$upload_response = json_encode(array(
			'status' => $status,
			'message' => $message,
			'id' => $id,
			'filename' => addslashes($imported_filename)
		));
		
		$HB = new HtmlBuilder('admin_upload_response.php', $this->plugin_path.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR);
		$HB->assign('upload_response', $upload_response);
		echo $HB->get_data();
	}
	
	private function handle_publish() {
		$status = false;
		$message = "";
		
		$filename = $_POST['pub_filename'];
		if(file_exists($filename)) {
			try {
				$id = YumpuEpaper_repository::create($filename, $_POST['title']);
			} catch (YumpuEpaper_repository_exception $e) {
				$status = "error";
				$message = $e->getMessage();
			}
		} else {
			$status = "error";
			$message = "file not found";
		}
		
		
		echo json_encode(array(
			'status' => $status,
			'message' => $message,
			'id' => $id
		));
	}
}

?>