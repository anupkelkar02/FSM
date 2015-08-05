<?php echo form_open('admin/uniforms/save_type'); ?>
<?php
echo toolbar_open('Add Type');
//echo toolbar_item('url_save','Save');
//echo toolbar_close();

		
	
?>
<div class="btn-group"><input type="submit" class="btn btn-primary" value=" &nbsp;Save" onclick="return validateaddfrm();">
</div>

<div class="btn-group"><a href="<?php echo base_url();?>index.php/admin/uniforms/typeslist" style="color:#FFF"><input type="button" class="btn btn-primary" value="&laquo; &nbsp;Cancel"></a>
</div>
<?php echo toolbar_close();?>
<div class='content'>
    <table class='DataRow' cellpadding='0' cellspacing='0'>
        <tr><th><i class="glyphicon-1x glyphicon-asterisk text-danger"></i> Type Title:</th><td><input type="text" name="typetitle" id="typetitle" /></td></tr>
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