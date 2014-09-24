<html>
<head>
	<title>Yumpu e-Paper</title>
	<script>
		var yumpu_plugin_url = '<?php echo $this->plugin_url; ?>';
	</script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl'); ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
	<?php
		wp_enqueue_script('jquery');
		wp_print_scripts('jquery');
	?>
	<script>
		var $ = jQuery;
	</script>
	<script src="<?php echo $this->plugin_url; ?>editor/uploadify/jquery.uploadify.js"></script>
	<script src="<?php echo $this->plugin_url; ?>misc/DataTables-1.10.2/media/js/jquery.dataTables.min.js"></script>
	<script src="<?php echo $this->plugin_url; ?>misc/js/yumpuEditorActions.js"></script>
	
	<link rel='stylesheet' href='<?php echo get_option('siteurl'); ?>/wp-admin/load-styles.php?c=0&amp;dir=ltr&amp;load=dashicons,admin-bar,buttons,media-views,wp-admin,wp-auth-check&amp;ver=3.8.1' type='text/css' media='all' />
	<link rel="stylesheet" href="<?php echo $this->plugin_url; ?>misc/DataTables-1.10.2/media/css/jquery.dataTables.css">
	<link rel="stylesheet" href="<?php echo $this->plugin_url; ?>editor/uploadify/uploadify.css">
	<link rel="stylesheet" href="<?php echo $this->plugin_url; ?>misc/css/yumpuEditorTheme.css">
</head>
<body class="wp-admin wp-core-ui js  post-php auto-fold admin-bar post-type-post branch-3-8 version-3-8-1 admin-color-fresh locale-de-de customize-support svg sticky-menu">
	<div id="y_case" >
		<!-- Title 
		<div class="media-frame-title">
			<h1>Add Yumpu ePaper</h1> 
		</div>
		-->
		
		<!-- Tab Navigation -->
		<div class="media-frame-router">
			<div class="media-router">
				<a href="javascript:yumpuStartTab('tab1');" class="media-menu-item media-menu-tab1 active">Upload PDF</a>
				<a href="javascript:yumpuStartTab('tab2');" class="media-menu-item media-menu-tab2">Insert existing PDF</a>
			</div>
		</div>
		
		<div class="media-frame-content">
			<div id="media-frame-tab1" class="yumpu_tab">
				<!-- 1. Uploader view.... -->
				<div class="yumpu_uploader">
						
					<div class="intro">
						<h3>Select a file to upload</h3>
						<div id="yumpu_uploadify_case">
							<p>Browse File</p>
							<input type="file" id="uploadButton" class="uploadButton">
						</div>
						
						<p><small>Upload max filesize: <?php echo $this->upload_max_filesize; ?>.</small></p>
					</div>
					
					
					
					
					<div class="media-sidebar">
						<div class="attachment-details">
							<h3>Optional</h3>
							<label class="setting" data-setting="title">
								<span>Document Title</span>
								<input type="text" id="yumpu_document_title" value="" placeholder="Enter document title...">
								<small>Min. length 5 characters</small>
							</label>
							
							<label class="setting" data-setting="caption">
								<span>Width (px)</span>
								<input type="text" id="yumpu_document_width" value="512">
							</label>
							
							<label class="setting" data-setting="description">
								<span>Height (px)</span>
								<input type="text" id="yumpu_document_height" value="384">
							</label>
						</div>
					</div>
					
					
					
					
					
					
				</div>
				
				<!-- 2. Select File -->
			</div>
			
			<div id="media-frame-tab2" class="yumpu_tab hiddenTab">
				
				<table id="dataTable" class="widefat ">
					<thead>
						<tr>
							<th>ID</th>
							<th>Title</th>
							<th>Status</th>
							<th>Optionen</th>
						</tr>
					</thead>
					<tbody>
						<?php $count = 0;?>
						<?php foreach($this->epapers as $ePaper) { ?>
							<tr class="<?php echo (++$count % 2) ? "" : "alternate"; ?>">
								<td><?php echo $ePaper->getId(); ?></td>
								<td><?php echo $ePaper->getTitle(); ?></td>
								<td><?php echo $ePaper->getStatus(); ?></td>
								<td><a href="javascript:yumpuAddShortcode(<?php echo $ePaper->getId(); ?>, 512, 384);">[Insert Shortcode]</a></td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
				
			</div>
		</div>
	
	
		<div class="media-frame-toolbar">
			<div class="media-toolbar">
				<div class="media-toolbar-secondary">
					<div id="yumpu_upload_queue"></div>
				</div>
				
				<div class="media-toolbar-primary">
					<span class="ajaxLoader" id="yumpuAjaxLoader"></span>
					<a id="yumpu_uploadButton" href="javascript:;" class="button media-button button-primary button-large media-button-insert" disabled="disabled">	
						Insert PDF to the Document
					</a>
					
				</div>
			</div>
		</div>
	</div>

<script>
	
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
	});
</script>

<link rel="stylesheet" href="<?php echo plugins_url( 'misc/DataTables-1.10.2/media/css/jquery.dataTables.css', dirname(__FILE__) );?>">
<script src="<?php echo plugins_url( 'misc/DataTables-1.10.2/media/js/jquery.dataTables.min.js', dirname(__FILE__) );?>"></script>
<script>
	$=jQuery.noConflict();
	$('#dataTable').dataTable({
		"aaSorting": [[ 0, "desc" ]]
	});
</script>

</body>
</html>

