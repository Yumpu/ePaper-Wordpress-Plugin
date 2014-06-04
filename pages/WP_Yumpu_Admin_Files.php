<?php

class WP_Yumpu_Admin_Files {
	private $plugin_path;
	
	function __construct($plugin_path) {
		$this->plugin_path = $plugin_path;		
	}
	
	public function run() {
		$this->display();
	}
	
	/**
	 * Funktionen für das Ajax-Callback
	 */
	public function ajax_run() {
		
	}
	
	private function display() {
		if(WP_Yumpu::$API_TOKEN === null) {
			echo '';
			return;
		}
		
		$HB = new HtmlBuilder('admin_files.php', $this->plugin_path.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR);
		$yumpu_action = isset($_REQUEST['yumpu_action']) ? $_REQUEST['yumpu_action'] : null;
		switch ($yumpu_action) {
			case "create_epaper":
				if(isset($_FILES['yc_file']) && !empty($_FILES['yc_file']['tmp_name']) && !empty($_POST['yc_title'])) {
					$FileAPI = new FileAPI(array('pdf'));
					try {
						$imported_filename = $FileAPI->import($_FILES['yc_file']['tmp_name'], $_FILES['yc_file']['name']);

						//Upload war erfolgreich und die Datei wurde korrekt abgelegt.
						YumpuEpaper_repository::create($imported_filename, $_POST['yc_title'], $_POST['yc_description']);
						
						$HB->assign('yumpu_success_message', 'upload successfull');
					} catch(FileAPI_exception $e) {
						$HB->assign('yumpu_error_message', $e->getMessage());
					} catch (YumpuEpaper_repository_exception $e) {
						/**
						 * Wenn möglich sollten wir die importierte Datei direkt entfernen.
						 */
						$FileAPI->delete($_FILES['yc_file']['name']);
						$HB->assign('yumpu_error_message', $e->getMessage());
					}
				} else {
					$HB->assign('yumpu_error_message', 'no input file or title missing');
				}
				break;
		}
		
		
		/**
		 * Hier Liste der gesmaten Dokumenten auflisten.
		 */
		$ePapers = YumpuEpaper_repository::getAll();
		$HB->assign('epapers', $ePapers);
		
		
		echo $HB->get_data();
	}
}

?>