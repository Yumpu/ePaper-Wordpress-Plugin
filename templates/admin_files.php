<div class="wrap">
	<h2>E-Paper powered by <a href="https://www.yumpu.com" target="_blank">Yumpu.com</a></h2>
	</br>
	<div id="overlay" style="visibility: hidden; position: absolute; left: 0px; top: 0px; width:100%; height:100%; text-align:left; z-index: 1000;">

		<div style="width:1000px; height:500px; position:fixed; top:50%; left:50%; margin-top:-250px; margin-left:-500px; background-color: #fff; border:1px solid #000; padding:5px; text-align:left;">
			<button href='#' onclick='overlay()' style="top: 0px; right: 10px; float:right;">&times;</button>
			</br>
			<h2 style="text-align: center;">Create E-Paper</h2>
			</br>
			<form style="margin-left: 30px;" id="api_token_form" method="post" enctype="multipart/form-data">
				<input type="hidden" name="yumpu_action" value="create_epaper">
				<h4>Title:</h4>
				<input type="text" class="regular-text" name="yc_title" id="yc_title" value="" style="width: 97%;"/>

				<h4>Description:</h4>
				<textarea rows="5" cols="50" class="regular-text" name="yc_description" id="yc_description" style="width: 97%; height:180px;"></textarea>

				<h4>PDF-File:</h4>
				<input type="file" class="regular-text" name="yc_file" id="yc_file" value=""><a style="float:right; padding-right: 25px;"><?php submit_button('Upload PDF','primary', 'submit',false, array('id'=>"upload_form") );?></a><a style="float:right; padding-right: 10px;"><button type="button" class="button-primary" href='#' onclick='overlay()'">Cancel</button></a>
			</form>
		</div>
	</div>
	<script>
		function overlay() {
			el = document.getElementById("overlay");
			el.style.visibility = (el.style.visibility == "visible") ? "hidden" : "visible";
		}
	</script>

	<?php if($this->yumpu_error_message) { ?>
		<div id="message" class="error">
			<p><?php echo $this->yumpu_error_message; ?></p>
		</div>
	<?php } ?>

	<?php if($this->yumpu_success_message) { ?>
		<div id="message" class="updated">
			<p><?php echo $this->yumpu_success_message; ?>

			</p>
		</div>
	<?php } ?>

	<table id="dataTable" class="widefat ">
		<thead>
		<tr>
			<th></th>
			<th>Title</th>
			<th>Shortcode</th>
			<th style="text-align: center;">State</th>
			<th style="text-align: center;">Visibility</th>
			<th style="text-align: center;">Created</th>
			<th></th>
		</tr>
		</thead>

		<tbody id="dataTableBody">

		</tbody>
	</table>

	</br>
	<h2><button class="button-primary" href='#' onclick='overlay()' class="button-primary" ">Add New</button></h2>
</div>

<script type="text/javascript">
	var myScript = "";
	var arr = jQuery.fn.jquery.split('.');
	if(arr[1] > 11){
		<?php
		$myScript = "misc/DataTables-1.10.2";
		?>
	} else {
		<?php
		$myScript = "misc/DataTables-1.10.12";
		?>
	}
</script>

<link rel="stylesheet" href="<?php echo plugins_url( $myScript . '/media/css/jquery.dataTables.css', dirname(__FILE__) );?>">
<script src="<?php echo plugins_url( $myScript . '/media/js/jquery.dataTables.min.js', dirname(__FILE__) );?>"></script>

<script>
	$=jQuery.noConflict();

	window.onload = function(){
		try {

			$(document.body).on('click', 'input', function() {
				if(this.id == 'copytext') {
					copyToClipboard(this)
				}
			});
		} catch(err) {
			//alert(err.message);
		}
	};

	try {
		var myTokenID = "<?php echo (WP_Yumpu::$API_TOKEN); ?>";
		var myTable;
		var t;
		if(myTokenID.length > 0) {
			$(document).ready(function () {
				connectionlogtable = $('#dataTable').dataTable({
					"language": {
						"lengthMenu": "_MENU_ records per page",
						"processing": "",
						"loadingRecords": '<div style="text-align: center"><img src="../../../../wp-admin/images/loading.gif" /></div>',
						"search": "Search",
						"zeroRecords": '<div style="text-align: center">No epaper to show.</div>'
					},
					"lengthMenu": [
						[10, 25, 50, -1],
						[10, 25, 50, "All"]
					],
					"displayLength": 10,
					"processing": true,
					"autoWidth": false,
					"retrieve": true,
					"ajax": {
						"url": '../wp-content/plugins/yumpu-epaper-publishing/lib/epapers.php?tokenid=' + myTokenID,
						"dataSrc": "epapers",
						"error": function (xhr, error, thrown) {
							console.log( error.message );
						}
					},
					"sorting": [[5, "desc"]],
					"columns": [
						{"data": "Cover"},
						{"data": "Title"},
						{"data": "Shortcode"},
						{"data": "State"},
						{"data": "Visibility"},
						{"data": "Created"},
						{"data": "ePaperID"}
					],

					"rowCallback": function (nRow, aData) {
						var editButton = '<button id="editButton" class="button-primary"><a href="https://www.yumpu.com/de/account/magazines/edit/' + aData['ePaperID'] + '/' + aData['Title'] + '" target="_blank" style="text-decoration: none;color: white;">Edit</a></button>';
						$('td:eq(6)', nRow).html(editButton).css('text-align', 'center');

						$('td:eq(5)', nRow).html(aData['Created']).css('text-align', 'center');

						$('td:eq(4)', nRow).html(aData['Visibility']).css('text-align', 'center');

						var myState = '';
						if (aData['State'] == "progress") {
							myState = '<span id="myStatus" class="dashicons dashicons-yes"></span>';
						} else {
							myState = '<span id="myStatus" class="dashicons dashicons-external"></span>';
							t = setTimeout(refreshTable, 5000);
						}
						$('td:eq(3)', nRow).html(myState).css('text-align', 'center');

						var myShortcode = '<div class="input-group"><input type="text" style="width: 400px; background-color:white" class="form-control" value="' + aData['Shortcode'] + '" id="copytext" readonly"></div>';
						$('td:eq(2)', nRow).html(myShortcode);

						$('td:eq(1)', nRow).html(aData['Title']);

						var Image = '<a href="https://www.yumpu.com/xx/document/view/' + aData['ePaperID'] + '" target="_blank"><img src="' + aData['Cover'] + '" alt="' + aData['Title'] + '" height="42" width="32"></a>';
						$('td:eq(0)', nRow).html(Image).css('text-align', 'center');

						return nRow;
					}
				});
			});
		}
	} catch(err) {
		console.log(err.message);
	}

	function refreshTable(){
		connectionlogtable.api().ajax.reload();
		clearTimeout(t);
	}

	function copyToClipboard(elem) {
		// create hidden text element, if it doesn't already exist
		try {
			var targetId = "_hiddenCopyText_";
			var isInput = elem.tagName === "INPUT" || elem.tagName === "TEXTAREA";
			var origSelectionStart, origSelectionEnd;
			if (isInput) {
				// can just use the original source element for the selection and copy
				target = elem;
				origSelectionStart = elem.selectionStart;
				origSelectionEnd = elem.selectionEnd;
			} else {
				// must use a temporary form element for the selection and copy
				target = document.getElementById(targetId);
				if (!target) {
					var target = document.createElement("textarea");
					target.style.position = "absolute";
					target.style.left = "-9999px";
					target.style.top = "0";
					target.id = targetId;
					document.body.appendChild(target);
				}
				target.textContent = elem.textContent;
			}
			// select the content
			var currentFocus = document.activeElement;
			target.focus();
			target.setSelectionRange(0, target.value.length);

			// copy the selection
			var succeed;
			try {
				succeed = document.execCommand("copy");
			} catch(e) {
				succeed = false;
			}
			// restore original focus
			if (currentFocus && typeof currentFocus.focus === "function") {
				currentFocus.focus();
			}

			if (isInput) {
				// restore prior selection
				elem.setSelectionRange(origSelectionStart, origSelectionEnd);
			} else {
				// clear temporary content
				target.textContent = "";
			}
			return succeed;
		} catch(err) {
			//alert(err.message);
		}
	}

</script>
