<?php
class HtmlBuilder {
	private $template_file;
	private $template_dir;
	private $_vars = array();
	
	function __construct($template_file, $template_dir = null) {
		$this->template_file = $template_file;
		$this->template_dir = $template_dir;
	}
	
	
	
	public function assign($key, $value) {
		$this->_vars[$key] = $value;
	}
	
	public function __get($param) {
		if(isset($this->_vars[$param])) {
			return $this->_vars[$param];
		} else {
			return null;
		}
	}
	
	private function build() {
		ob_start();
		require $this->template_dir.DIRECTORY_SEPARATOR.$this->template_file;
		$_tmp = ob_get_contents();
		ob_end_clean();
		return $_tmp;
	}
	
	public function get_data() {
		return $this->build();
	}
}

?>