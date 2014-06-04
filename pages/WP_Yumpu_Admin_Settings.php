<?php

class WP_Yumpu_Admin_Settings {
	private $plugin_path;
	
	function __construct($plugin_path) {
		$this->plugin_path = $plugin_path;		
	}
	
	public function run() {
		$this->display();
	}
	
	
	private function display() {
		$HB = new HtmlBuilder('admin_settings.php', $this->plugin_path.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR);
		$HB->assign('API_TOKEN', WP_Yumpu::$API_TOKEN);
		echo $HB->get_data();
	}
}

?>