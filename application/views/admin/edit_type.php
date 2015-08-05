<?php echo form_open('admin/uniforms/update_type'); ?>
<?php
echo toolbar_open('Update Type');
//echo toolbar_item('url_save','Save');
//echo toolbar_close();
	
?>
<div class="btn-group"><input type="submit" class="btn btn-primary" value=" &nbsp;Update Record" onclick="return validateaddfrm();">
</div>

<div class="btn-group"><a href="<?php echo base_url();?>index.php/admin/uniforms/typeslist" style="color:#FFF"><input type="button" class="btn btn-primary" value="&laquo; &nbsp;Cancel"></a>
</div>
<?php echo toolbar_close();?>
<input type="hidden" name="u_id" value="<?php echo $id;?>" id="type_id" />

<div class='content'>
    <table class='DataRow' cellpadding='0' cellspacing='0'>
        <tr><th><i class="glyphicon-1x glyphicon-asterisk text-danger"></i>Type Title:</th><td><input type="text" name="typetitle" id="typetitle" value="<?php echo $title;?>" /></td></tr>
    </table>
    </div>

<?php echo form_close(); ?>

<script>
function validateaddfrm()
{
	if(document.getElementById('typetitle').value=="")
	{
		alert("please write type title");
		return false;
	}
	
	
}

</script>