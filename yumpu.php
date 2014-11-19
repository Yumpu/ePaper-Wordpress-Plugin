<?php
/*
  Plugin Name: Yumpu ePaper publishing
  Description:  Yumpu is a free PDF to ePaper site. <br/>The Service allows you to upload a PDF and embed it as an ePaper via shortcode.
  Author: Yumpu.com
  Author URI: http://www.yumpu.com
  Version: 1.0.4
 */
Class WP_Yumpu {
	static private $instance = null;
	
	/**
	 * Mit diesem Schlüssel wird der Access-Token in WP abgelegt.
	 */
	const YUMPU_WP_SETTINGS_KEY = "YUMPU_API_ACCESS_TOKEN";
	
	/**
	 * YUMPU API-Token
	 */
	static public $API_TOKEN = null;
	
	/**
	 * YUMPU - PluginPath
	 */
	static public $PLUGIN_PATH = null;
	
	/**
	 * YUMPU - PluginURL
	 */
	static public $PLUGIN_URL = null;
	
	/**
	 * URL zum Plugin
	 */
	private $plugin_url;
	
	/**
	 * Pfad zum Plugin
	 */
	private $plugin_path;

	/**
	 * Pfad zum Pages Verzeichnis
	 */
	private $plugin_pages_path;
	
	/**
	 * Pfad zum Lib Verzeichnis
	 */
	private $plugin_lib_path;
	
	
	
	function __construct() {
		$this->plugin_url = plugin_dir_url(__FILE__); 
		$this->plugin_path = realpath(dirname( __FILE__ ));
		$this->plugin_pages_path = $this->plugin_path.DIRECTORY_SEPARATOR.'pages'.DIRECTORY_SEPARATOR;
		$this->plugin_lib_path = $this->plugin_path.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR;
		
		WP_Yumpu::$PLUGIN_PATH = $this->plugin_path;
		WP_Yumpu::$PLUGIN_URL = $this->plugin_url;
		
		$this->plugin_init();
	}
	
	/**
	 * Ausführung immer wenn das Plugin geladen wird.
	 */
	private function plugin_init() {
		require_once $this->plugin_lib_path.'HtmlBuilder.php';
		require_once $this->plugin_lib_path.'YumpuAPI.php';
		
		require_once $this->plugin_lib_path.'FileAPI.php';
		require_once $this->plugin_lib_path.'YumpuEpaper.php';
		require_once $this->plugin_lib_path.'YumpuEpaper_repository.php';
		
		require_once $this->plugin_pages_path.'WP_Yumpu_Admin_Settings.php';
		require_once $this->plugin_pages_path.'WP_Yumpu_Admin_Files.php';
		require_once $this->plugin_pages_path.'WP_Yumpu_Admin_Editor.php';
		
		
		
		/**
		 * Registriert einen Hook für die Plugin-Aktivierung
		 */
		register_activation_hook( __FILE__, array( &$this, 'plugin_activate' ) );
		
		/**
		 * Registriert einen Hook für die Plugin-Deaktivierung
		 */
        register_deactivation_hook(__FILE__, array( &$this, 'plugin_deactivate' ) );
        
        /**
         * Registriert das Admin Menü
         */
        add_action( 'admin_menu', array( &$this, "add_menu" ) );
        
        /**
         * Info-Meldung wenn der API-Key nicht registriert ist.
         */
        add_action( 'admin_notices', array( &$this, 'admin_notice' ) );
        
        /**
         * Ajax Handler für API Registrieren.
         */
        add_action( 'wp_ajax_wp_yumpu', array( &$this, 'ajax_handler' ) );
        
        
		/**
		 * Add chartbeat and google analytics
		 */
		add_action('admin_head-settings_page_yumpu-settings', array(&$this, 'add_head_settings') );
		add_action('admin_head-toplevel_page_yumpu-filemanager', array(&$this, 'add_head_settings') );
		add_action('admin_footer-settings_page_yumpu-settings', array(&$this, 'add_footer_settings') );
		add_action('admin_footer-toplevel_page_yumpu-filemanager', array(&$this, 'add_footer_settings') );
		
        /**
         * Shortcode Registrieren
         */
        add_shortcode( "YUMPU", array( $this, 'shortcode_embed' ) );
        
        /**
         * Load API-Token
         */
        WP_Yumpu::$API_TOKEN = get_option("YUMPU_API_ACCESS_TOKEN", null);
        
        
        /**
         * Vorbereitung für den Editor.
         */
        if(WP_Yumpu::$API_TOKEN !== null) {
            add_filter( "mce_external_plugins", array( $this, "register_editor_plugin" ), 5 );
            add_filter( 'mce_buttons', array( $this, 'add_editor_button' ), 5 );
        }
	}
	
	public function register_editor_plugin($plugin_array) {
        $plugin_array['my_yumpu'] = $this->plugin_url.'misc/js/yumpuEditor/editor_plugin.js';
        return $plugin_array;
    }
    
    public function add_editor_button($buttons) {
        array_push( $buttons, "|", "my_yumpu" );
        return $buttons;
    }
	
	
	/**
	 * Ausführung wenn der Benutzer das Plugin aktiviert
	 */
	public function plugin_activate() {
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        global $wpdb;
        
		$table_name = $wpdb->prefix.'yumpu_documents';

		$qry = "CREATE TABLE ".$table_name." (
		id INTEGER NOT NULL AUTO_INCREMENT,
		progress_id VARCHAR(255) NOT NULL,
		epaper_id VARCHAR(255) NOT NULL,
		url VARCHAR(255) NOT NULL,
		short_url VARCHAR(255) NOT NULL,
		title VARCHAR(255) NOT NULL,
		description TEXT DEFAULT '',
		source_filename VARCHAR(255) NOT NULL,
		status VARCHAR(255) NOT NULL,
		embed_code TEXT DEFAULT '',
		UNIQUE KEY id (id)
		);";
		dbDelta($qry);
	}
	
	/**
	 * Ausführung wenn der Benutzer das Plugin deaktiviert.
	 */
	public function plugin_deactivate() {
		
	}
	
	
	/**
	 * Fügt einen Einstellungspunkt unter Settings bei Wordpress hinzu.
	 */
	public function add_menu() {
		/**
		 * Admin API-TOKEN Settings Page
		 */
		add_submenu_page( 
			"options-general.php", 
			"Yumpu PDF Settings", 
			"Yumpu PDF Settings", 
			"manage_options", 
			"yumpu-settings", 
			array( $this, 'page_admin_settings' ) 
		);
		
		/**
		 * Admin Yumpu Files
		 */
		add_menu_page(
			"E-Paper", 
			"E-Paper", 
			"edit_others_posts",
			"yumpu-filemanager",
			array($this, 'page_admin_files'),
			'dashicons-format-aside',
			21
		);
		
    }
	
	public function add_head_settings() {
		echo '<script type="text/javascript">var _sf_startpt=(new Date()).getTime()</script>';
	}
	
	public function add_footer_settings() {
		echo '<script type="text/javascript">
	var _sf_async_config = { uid: 33630, domain: "yumpu.com", useCanonical: true };
	(function() {
		function loadChartbeat() {
			window._sf_endpt = (new Date()).getTime();
			var e = document.createElement("script");
			e.setAttribute("language", "javascript");
			e.setAttribute("type", "text/javascript");
			e.setAttribute("src","//static.chartbeat.com/js/chartbeat.js");
			document.body.appendChild(e);
		};
		var oldonload = window.onload;
		window.onload = (typeof window.onload != "function") ?
		loadChartbeat : function() { oldonload(); loadChartbeat(); };
	})();
</script>
<script>
(function(i,s,o,g,r,a,m){i["GoogleAnalyticsObject"]=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,"script","//www.google-analytics.com/analytics.js","ga");

ga("create", "UA-27868640-1", "auto");
ga("send", "pageview");

</script>';
	}
    
    public function page_admin_settings() {
    	$WP_Yumpu_Admin_Settings = new WP_Yumpu_Admin_Settings($this->plugin_path);
    	$WP_Yumpu_Admin_Settings->run();
    }
    
    public function page_admin_files() {
    	$WP_Yumpu_Admin_Files = new WP_Yumpu_Admin_Files($this->plugin_path);
    	$WP_Yumpu_Admin_Files->run();
    }
    
    
    /**
     * AJAX Handler für Admin Aufrufe.
     */
    public function ajax_handler() {
    	$run = isset($_REQUEST['run']) ? $_REQUEST['run'] : null;
    	switch($run) {
    		/**
    		 * AJAX Call vom Admin zum prüfen des API-TOKEN
    		 */
    		case 'checkAPI_Token':
    			$api_token = $_REQUEST['API_TOKEN'];
    			
    			$YAPI = new YumpuAPI($api_token);
    			try {
    				$YAPI->check_api_key();
    				/**
    				 * Wir speichern den API Token
    				 */
    				update_option(self::YUMPU_WP_SETTINGS_KEY, $api_token);
    				
    				/**
    				 * Und setzen den API-Token direkt.
    				 */
    				WP_Yumpu::$API_TOKEN = $api_token;
    				echo json_encode(array('result' => true));
    			} catch (YumpuAPI_Exception $e) {
    				echo json_encode(array('result' => false, 'error' => $YAPI->get_errors()));
    			}
    			break;
    		/**
    		 * AJAX Call vom Admin zum erstellen eines Accounts
    		 */
    		case 'createAccount':
    			$acc_email = $_REQUEST['email'];
    			$acc_password = $_REQUEST['password'];
    			$acc_username = $_REQUEST['username'];
    			$acc_firstname = $_REQUEST['firstname'];
    			$acc_lastname = $_REQUEST['lastname'];
    			$acc_gender = $_REQUEST['gender'];
    			
    			$YAPI = new YumpuAPI(null);
    			try {
    				$API_TOKEN = $YAPI->account_create($acc_email, $acc_username, $acc_password, $acc_firstname, $acc_lastname, $acc_gender);
    				echo json_encode(array('result' => true, 'error' => array(), 'API_TOKEN' => $API_TOKEN));
    			} catch (YumpuAPI_Exception $e) {
    				echo json_encode(array('result' => false, 'error' => $YAPI->get_errors()));
    			}
    			break;
			/**
    		 * AJAX Call from Posts to open pdf editor manager
    		 */
    		case 'editorActions':
				$WP_Yumpu_Admin_Editor = new WP_Yumpu_Admin_Editor(WP_Yumpu::$PLUGIN_PATH);
				$WP_Yumpu_Admin_Editor->run();
				break;
    	}
    	
    	exit;
    }
    
    /**
     * Wenn kein API_TOKEN verfügbar ist, dann wird eine Warnmeldung eingeblendet.
     */
 	public function admin_notice() {
 		$api_token = WP_Yumpu::$API_TOKEN;
        if (empty($api_token) || is_null($api_token)) {
            echo '<div class="updated"><p>Get your Yumpu.com API Key from your account and add it <a href="options-general.php?page=yumpu-settings">here</a>. You can get more informations <a href="http://support.yumpu.com/en/question/yumpucom-developer-interface">here</a>.</p></div>';
        }
    }
    
    
    public function shortcode_embed($attr, $content=null , $code) {

    	$document_id = (int)$attr['epaper_id'];
        $document_width = isset($attr['width']) ? (int)$attr['width']: 512;
		$document_height = isset($attr['height']) ? (int)$attr['height'] : 384;
		
		if(!isset($attr['epaper_id'])) {
			return $content.'<p>misconfigured shortcode</p>';
		}
		
		
		try {
			$ePaper = YumpuEpaper_repository::loadById($document_id);
			if($ePaper->getStatus() == "progress") {
				$ret = $content.'<div style="position:relative; width:'.$document_width.'px;height:'.$document_height.'px; background:#233039;margin-bottom:10px;"><p style="text-align:center;padding-top:'.(($document_height/2)-30).'px;color:#ffffff;font-weight:normal;font-size:1.5em;">ePaper in progress <br><span style="color:#93A8B7;font-size:0.7em;">Powered by Yumpu.com</span></p>';
				$ret .= '<a class="yumpuLink" target="yumpu" href="http://www.yumpu.com/de/"><img src="'.plugins_url('misc/images/yumpu_logo_trans.png', __FILE__).'"></a>';
				$ret .= '</div>';
				$ret .= '<style>';
				$ret .= '.yumpuLink img { opacity:0.5;filter:alpha(opacity=50);width:75px;bottom:10px;right:10px;position:absolute; }';
				$ret .= '.yumpuLink:hover img { opacity:0.8;filter:alpha(opacity=80);width:75px;bottom:10px;right:10px;position:absolute; }';
				$ret .= '</style>';
				return $ret;
			}
			
			/**
			 * ePaper nun hier anzeigen.
			 */
			$output = $ePaper->getEmbed_code();
			
			if(isset($_SERVER["HTTPS"])) { // IFrame muss via HTTPS aufgerufen werden.
				$output = preg_replace( '#http\://#i', "https://", $output );	
			}
			
			
			
			$output = preg_replace( '/width:(.*?)px/i', "width:".$document_width."px", $output );
			$output = preg_replace( '/height:(.*?)px/i', "height:".$document_height."px", $output );
			$output = preg_replace( '/width="(.*?)px"/i', "width=\"".$document_width."px\"", $output );
			$output = preg_replace( '/height="(.*?)px"/i', "height=\"".$document_height."px\"", $output );
			
		
		
			
			return $content.$output;
		} catch(YumpuEpaper_repository_exception $e) {
			return '<div style="width:'.$document_width.'px;height:'.$document_height.'px"><p>ePaper not found</p></div>';
		}
    }
    
    
	
	function __destruct() {
		
	}
	
	/**
	 * @return WP_Yumpu
	 */
	static public function getInstance() {
		if (null === self::$instance) {
			self::$instance = new self;
		}
		return self::$instance;
	}

}




WP_Yumpu::getInstance();
