/**
 * 
 */
(function() {
    tinymce.create('tinymce.plugins.my_yumpu', {
		
        init : function(ed, url) {                    
			var target_url = window.location.protocol + "//" + window.location.host + '/wp-admin/admin-ajax.php?action=wp_yumpu&run=editorActions';
            
            // Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceExample');	
            ed.addCommand('my_yumpu_cmd', function() {                
                ed.windowManager.open({
                    title : 'Yumpu PDF',
                    file : target_url,
                    width : 800,
                    height : 450,
                    inline : 1,
                    resizable:false
                });
            });

            // Register example button
            ed.addButton('my_yumpu', {
                title : 'Yumpu PDF',				
                cmd : 'my_yumpu_cmd',
                image : url + '/icon.png',                
            });

            // Add a node change handler, selects the button in the UI when a image is selected
            ed.onNodeChange.add(function(ed, cm, n) {
                cm.setActive('my_yumpu', n.nodeName == 'IMG');
            });
        },
        createControl : function(n, cm) {
            return null;
        },
        getInfo : function() {
            return {
                longname : "Yumpu PDF",
                author : 'Weddig & Keutel AG',
                authorurl : 'mailto:info@weddig-keutel.de',
                infourl : 'mailto:info@weddig-keutel.de',
                version : "1.0"
            };
        }
    });

    // Register plugin
    tinymce.PluginManager.add('my_yumpu', tinymce.plugins.my_yumpu);
})();