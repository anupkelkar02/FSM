
 <?php echo form_open_multipart('admin/attendance/import_attexcel'); ?>       
 <?php echo message_note(); ?>	
<div class='content'>
		
	
<fieldset class='filter'>
    
<legend>Import Attendance</legend>
<table cellpadding='0' cellspacing='0' class='filter'>
   
	<tr>
		<th>Upload File:</th>
                <td><?php echo form_upload('file', set_value('file')); ?></td>
	</tr>
    <tr>
        <td>
            &nbsp;
        </td>
    </tr>
        <tr>
            
            <td colspan='2' align='right'><?php echo form_submit('uploadfile', 'Uploadfile', 'class="login"'); ?></td>
        </tr>
</table>
</fieldset>
		

<!--<div class='container text-center'><?php echo $pagination_links; ?></div>-->
</div>

<?php echo form_close(); ?>

