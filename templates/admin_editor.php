<html>
<head>
	<script type="text/javascript">var _sf_startpt=(new Date()).getTime()</script>
	<title>Yumpu e-Paper</title>
</head>
<body class="wp-admin wp-core-ui js  post-php auto-fold admin-bar post-type-post branch-3-8 version-3-8-1 admin-color-fresh locale-de-de customize-support svg sticky-menu">
	<div id="y_case" >
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
						</div>
						<p><small>Upload max filesize: <?php echo $this->upload_max_filesize; ?>.</small></p>
					</div>
					<div class="media-sidebar">
						<div class="attachment-details">
							<form method="post" target="ifmUpload" action="/wp-admin/admin-ajax.php?action=wp_yumpu&run=editorActions&method=upload" enctype="multipart/form-data">
							<input type="file" id="uploadButton" name="Filedata" class="uploadButton" style="display:none">
							<h3>Optional</h3>
							<label class="setting" data-setting="title">
								<span>Document Title</span>
								<input type="text" id="yumpu_document_title" value="" placeholder="Enter document title..." name="title">
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
							</form>
							<iframe id="ifmUpload" name="ifmUpload" width="0" height="0"></iframe>
						</div>
					</div>
				</div>
			</div>
			
			<div id="media-frame-tab2" class="yumpu_tab hiddenTab">
				<!-- 2. Select File -->
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
<link rel='stylesheet' href='<?php echo get_option('siteurl'); ?>/wp-admin/load-styles.php?c=0&amp;dir=ltr&amp;load=dashicons,admin-bar,buttons,media-views,wp-admin,wp-auth-check&amp;ver=3.8.1' type='text/css' media='all' />
<link rel="stylesheet" href="<?php echo $this->plugin_url; ?>misc/DataTables-1.10.2/media/css/jquery.dataTables.css">
<link rel="stylesheet" href="<?php echo $this->plugin_url; ?>misc/css/yumpuEditorTheme.css">
<link rel="stylesheet" href="<?php echo plugins_url( 'misc/DataTables-1.10.2/media/css/jquery.dataTables.css', dirname(__FILE__) );?>">

<script>
	var yumpu_plugin_url = '<?php echo $this->plugin_url; ?>';
</script>
<?php
	wp_enqueue_script('jquery');
	wp_print_scripts('jquery');
?>
<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl'); ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
<script src="<?php echo $this->plugin_url; ?>misc/DataTables-1.10.2/media/js/jquery.dataTables.min.js"></script>
<script src="<?php echo $this->plugin_url; ?>misc/js/yumpuEditorActions.js"></script>
<script type="text/javascript">
	var _sf_async_config = { uid: 33630, domain: 'yumpu.com', useCanonical: true };
	(function() {
		function loadChartbeat() {
			window._sf_endpt = (new Date()).getTime();
			var e = document.createElement('script');
			e.setAttribute('language', 'javascript');
			e.setAttribute('type', 'text/javascript');
			e.setAttribute('src','//static.chartbeat.com/js/chartbeat.js');
			document.body.appendChild(e);
		};
		var oldonload = window.onload;
		window.onload = (typeof window.onload != 'function') ?
		loadChartbeat : function() { oldonload(); loadChartbeat(); };
	})();
</script>
<script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

ga('create', 'UA-27868640-1', 'auto');
ga('send', 'pageview');

</script>
</body>
</html>

