<div class="wrap">
	<h2>Yumpu.com ePapers</h2>
	<?php if($this->yumpu_error_message) { ?>
		<div id="message" class="error">
			<p><?php echo $this->yumpu_error_message; ?></p>
		</div>
	<?php } ?>
	
	<?php if($this->yumpu_success_message) { ?>
		<div id="message" class="updated">
			<p><?php echo $this->yumpu_success_message; ?></p>
		</div>
	<?php } ?>
	
	
	<table id="dataTable" class="widefat ">
		<thead>
			<tr>
				<th>ID</th>
				<th>Title</th>
				<th>Shortcode</th>
				<th>Status</th>
			</tr>
		</thead>
		<tbody>
			<?php $count = 0;?>
			<?php foreach($this->epapers as $ePaper) { ?>
				<tr class="<?php echo (++$count % 2) ? "" : "alternate"; ?>">
					<td><?php echo $ePaper->getId(); ?></td>
					<td><?php echo $ePaper->getTitle(); ?></td>
					<td>[YUMPU epaper_id="<?php echo $ePaper->getId(); ?>" width="512" height="384"]</td>
					<td><?php echo $ePaper->getStatus(); ?></td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
	<p style="clear:both;padding-bottom:20px;"></p>
	<h3>Create E-Paper</h3>
	<form style="margin-left: 30px;" id="api_token_form" method="post" enctype="multipart/form-data">
		<input type="hidden" name="yumpu_action" value="create_epaper">
        <table class="form-table">
            <tbody>
                <tr>
                    <th>Title:</th>
                    <td>
                        <input type="text" class="regular-text" name="yc_title" id="yc_title" value=""/>
                    </td>
                </tr>
                <tr>
                    <th>Description:</th>
                    <td>
                        <textarea rows="5" cols="50" class="regular-text" name="yc_description" id="yc_description"></textarea>
                    </td>
                </tr>
                <tr>
                    <th>PDF-File:</th>
                    <td>
                        <input type="file" class="regular-text" name="yc_file" id="yc_file" value=""/> <?php submit_button('Upload PDF','primary', 'submit',false, array('id'=>"upload_form") );?>
                    </td>
                </tr>
            </tbody>
        </table>                 
    </form>
	
</div>

<link rel="stylesheet" href="<?php echo plugins_url( 'misc/DataTables-1.10.2/media/css/jquery.dataTables.css', dirname(__FILE__) );?>">
<script src="<?php echo plugins_url( 'misc/DataTables-1.10.2/media/js/jquery.dataTables.min.js', dirname(__FILE__) );?>"></script>
<script>
	$=jQuery.noConflict();
	$('#dataTable').dataTable({
		"aaSorting": [[ 0, "desc" ]]
    });
</script>
