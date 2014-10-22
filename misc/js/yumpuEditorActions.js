$ = jQuery.noConflict();

/**
 * Variable dient zur erkennung ob uploadify bereits initialisiert war/ist
 */
var yumpuUploadify_isInit = false;


/**
 * Selektiert ein Tab im Editor und initialisiert die notwendigen Daten. 
 */
function yumpuStartTab(selected_tab) {
	/**
	 * Tabinhalt ausblenden und anschließend das gewählte Tab anzeigen
	 */
	$('#y_case').find('.yumpu_tab').hide();
	$('#media-frame-'+selected_tab).show();
	
	/**
	 * Tab Navigation für visuelle Rückmeldung abändern.
	 */
	$('#y_case').find('.media-menu-item').removeClass('active');
	$('#y_case').find('.media-menu-'+selected_tab).addClass('active');
	
	/**
	 * Das Tab für den Upload ist gewählt.
	 */
	if(selected_tab == "tab1") {
		$('#yumpu_document_title').val('');
		$('#yumpu_document_width').val('512');
    	$('#yumpu_document_height').val('384');
    	$('#yumpu_upload_queue').html('');
		// yumpuInitialUploadify();
		
		$('.media-frame-toolbar').show();
		$('.media-frame-content').css('bottom', '61px');
	}
	
	/**
	 * Tab für die direkte Auswahl einer Datei ist gewählt.
	 */
	if(selected_tab == "tab2") {
		$('.media-frame-toolbar').hide();
		$('.media-frame-content').css('bottom', '0px');
	}
}

/**
 * Initialisiert das Tab1 (Upload) für die Verwendung.
 */
function yumpuInitialUpload() {
	yumpuInitialUploadify(); // Zuerst den Uploader initialisieren.
	
}

/**
 * Initialisiert das Tab2 (File select) für die Verwendung.
 */
function yumpuInitialFileselect() {
	
}


/**
 * Eigene function für Uploadify
 */
function yumpuInitialUploadify() {
	if(yumpuUploadify_isInit) {
		$('#uploadButton').uploadify('destroy');
		$('#yumpu_uploadButton').unbind('click');
	}
	
	$('#yumpu_uploadButton').bind('click', function() {
		if($(this).attr('disabled') != "disabled") { //Button ist nciht deaktiviert. Upload kann gestartet werden.
			yumpuPublishFile();
		}
	});
	
	yumpuUploadify_isInit = true;
	$('#uploadButton').uploadify({
        'auto':true,
        'removeCompleted' : false,
        'queueID':'yumpu_upload_queue',
        'buttonClass' : 'yumpuButton',
        'buttonImage' : yumpu_plugin_url+'misc/images/pixel.png',
        'buttonText' : 'Browse File',
        'swf'      : yumpu_plugin_url+'editor/uploadify/uploadify.swf',
        'width':150,
        'height':50,
        /*'uploadLimit' : 1,*/
        'queueSizeLimit': 1,
        'multi'    : false,
        'fileTypeDesc' : 'PDF Document',
        'fileTypeExts' : '*.pdf',
        'uploader' : window.location.protocol + "//" + window.location.host + '/wp-admin/admin-ajax.php?action=wp_yumpu&run=editorActions&method=upload',
        'onUploadStart' : function(file) {
        	/**
        	 * Button deaktivieren und UI-Feedback an User.
        	 */
        	$('#yumpuAjaxLoader').show();
        	$('#yumpu_uploadButton').attr('disabled','disabled');
        	$('#yumpu_uploadButton').html('processing - please wait!');
        	
        	/**
        	 * Wir erhalten hier den Dateinamen ohne Dateierweiterung.
        	 */
        	var org_fileName = yumpuFileGetBasename(file.name);
        	
        	/**
        	 * Prüfen ob der Title den Vorgaben entspricht.
        	 */
            var title = $('#yumpu_document_title').val();
            if(title.length > 0 && title.length < 5) { //Title ist eingegbeen aber zu kurz!
            	yumpuNotifyUser('Error','need title - Min. length 5 characters, max. length 255 characters', 'error');
				$('#yumpu_file_uploader').uploadify('stop');
				return false;
            } else if(title.length == 0) { //Kein Title eingegeben; Daher automatisch aus Dateiname.
            	$('#yumpu_document_title').val(org_fileName);
            	title = org_fileName;
            }

            
            
            var formData = $('#uploadButton').uploadify('settings', 'formData');
            formData['title'] = title;
            $('#uploadButton').uploadify('settings', 'formData', formData);
        },
        'onUploadSuccess' : function(file,data,response) {
        	$('#yumpuAjaxLoader').hide();
        	$('#yumpu_uploadButton').removeAttr('disabled');
        	$('#yumpu_uploadButton').html('In den Beitrag einfügen');
        	
            cb = $.parseJSON(data);
            if(cb.status == "error") {				
				yumpuNotifyUser('Error', cb.message, 'error');
				yumpuStartTab('tab1');
            } else {
            	
            	/**
            	 * Hier merken wir uns dann die Daten.
            	 */
            	var width = $('#yumpu_document_width').val();
            	var height = $('#yumpu_document_height').val();
            	
            	/**
            	 * Falls die Breite oder die Höhe unzulässige Werte haben
            	 * Setzen wir einen Default-Wert.
            	 */
            	if(width < 50 || isNaN(width)) {
            		width = 512;
            	}
            	
            	if(height < 50 || isNaN(height)) {
            		height = 384;
            	}
            	
            	yumpuStoreLastUploadData(cb.filename);
            	//yumpuAddShortcode(cb.id, width, height); 
            }
        },

        'onSelect' : function(file) {
        	//$('#yumpu_uploadButton').removeAttr('disabled');
        },
        
        'onCancel' : function(file) {
        	$('#yumpu_uploadButton').attr('disabled', "disabled");
        },
        
        'onClearQueue' : function(queueItemCount) {
            alert(queueItemCount + ' file(s) were removed from the queue');
        } 
	});
}

var last_upload_filename = "";
function yumpuStoreLastUploadData(filename) {
	last_upload_filename = filename;
	if(last_upload_filename != "") {
		$('#yumpu_uploadButton').removeAttr('disabled');
	} else {
		$('#yumpu_uploadButton').attr('disabled', "disabled");
	}
}

function yumpuPublishFile() {
	var width = $('#yumpu_document_width').val();
	var height = $('#yumpu_document_height').val();
	
	/**
	 * Falls die Breite oder die Höhe unzulässige Werte haben
	 * Setzen wir einen Default-Wert.
	 */
	if(width < 50 || isNaN(width)) {
		width = 512;
	}
	
	if(height < 50 || isNaN(height)) {
		height = 384;
	}
	
	var title = $('#yumpu_document_title').val();
	if(title.length > 0 && title.length < 5) { //Title ist eingegbeen aber zu kurz!
    	yumpuNotifyUser('Error','need title - Min. length 5 characters, max. length 255 characters', 'error');
		return false;
    }
	
	var postdata = "pub_filename="+last_upload_filename;
	postdata += "&width="+width;
	postdata += "&height="+height;
	postdata += "&title="+title;
	
	$('#yumpuAjaxLoader').show();
	$('#yumpu_uploadButton').attr('disabled','disabled');
	$('#yumpu_uploadButton').html('processing - please wait!');
	
	$.post(window.location.protocol + "//" + window.location.host + '/wp-admin/admin-ajax.php?action=wp_yumpu&run=editorActions&method=publish', postdata, function(response) {
		if(response.status == "error") {				
			yumpuNotifyUser('Error', response.message, 'error');
			yumpuStartTab('tab1');
		} else {
			yumpuAddShortcode(response.id, width, height); 
		}
		
		$('#yumpuAjaxLoader').hide();
    	$('#yumpu_uploadButton').removeAttr('disabled');
    	$('#yumpu_uploadButton').html('In den Beitrag einfügen');
	}, 'json');
}

/**
 * 
 * @param id ePaper-ID
 * @param width iframe px
 * @param height iframe px
 */
function yumpuAddShortcode(ePaper_id, width, height) {
	var code='[YUMPU epaper_id="'+ePaper_id+'" width="'+width+'" height="'+height+'"]';
    tinyMCEPopup.editor.execCommand('mceInsertContent', false,code );
    tinyMCEPopup.close();
}

function yumpuFileGetBasename(filename) {
	var basename = filename.substr(0, filename.length-4);
	return basename;
}

function yumpuNotifyUser(title, message, classname) {
	alert(title+"\n\n"+message);
}

function yumpuUploadResponse(data) {
	$('#yumpuAjaxLoader').hide();
	$('#yumpu_uploadButton').removeAttr('disabled');
	$('#yumpu_uploadButton').html('In den Beitrag einfügen');

	cb = $.parseJSON(data);
	if(cb.status == "error") {				
		yumpuNotifyUser('Error', cb.message, 'error');
		yumpuStartTab('tab1');
	} else {

		/**
		 * Hier merken wir uns dann die Daten.
		 */
		var width = $('#yumpu_document_width').val();
		var height = $('#yumpu_document_height').val();

		/**
		 * Falls die Breite oder die Höhe unzulässige Werte haben
		 * Setzen wir einen Default-Wert.
		 */
		if(width < 50 || isNaN(width)) {
			width = 512;
		}

		if(height < 50 || isNaN(height)) {
			height = 384;
		}

		yumpuStoreLastUploadData(cb.filename);
		//yumpuAddShortcode(cb.id, width, height); 
	}
}

$(document).ready(function() {
	/**
	 * Wenn die Seite fertig geladen ist können wir direkt zum ersten Tab springen.
	 */
	yumpuStartTab('tab1');


	$('#yumpu_document_width').keyup(function() {
		var me = (($(this).val() / 4) * 3);
		$('#yumpu_document_height').val(Math.round(me));
	});

	$('#yumpu_document_height').keyup(function() {
		var me = (($(this).val() / 3) * 4);
		$('#yumpu_document_width').val(Math.round(me));
	});
	
	$('#dataTable').dataTable({
		"aaSorting": [[ 0, "desc" ]]
	});
	
	$('.yumpu_uploader').on('click', '#yumpu_uploadify_case', function () {
		$('#uploadButton').click();
	});
	
	$('.attachment-details').on('change', '#uploadButton', function () {
		/**
		* Button deaktivieren und UI-Feedback an User.
		*/
	   $('#yumpuAjaxLoader').show();
	   $('#yumpu_uploadButton').attr('disabled','disabled');
	   $('#yumpu_uploadButton').html('processing - please wait!');

	   /**
		* Wir erhalten hier den Dateinamen ohne Dateierweiterung.
		*/
	   var org_fileName = yumpuFileGetBasename($(this).val().split('\\').pop());

	   /**
		* Prüfen ob der Title den Vorgaben entspricht.
		*/
	   var title = $('#yumpu_document_title').val();
	   if(title.length > 0 && title.length < 5) { //Title ist eingegbeen aber zu kurz!
		   yumpuNotifyUser('Error','need title - Min. length 5 characters, max. length 255 characters', 'error');
		   $('#yumpu_file_uploader').uploadify('stop');
		   return false;
	   } else if(title.length == 0) { //Kein Title eingegeben; Daher automatisch aus Dateiname.
		   $('#yumpu_document_title').val(org_fileName);
		   title = org_fileName;
	   }
		$(this).parent().submit();
	});
	
	$('#yumpu_uploadButton').bind('click', function() {
		if($(this).attr('disabled') != "disabled") { //Button ist nciht deaktiviert. Upload kann gestartet werden.
			yumpuPublishFile();
		}
	});
});