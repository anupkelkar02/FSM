<?php echo form_open('admin/uniforms/update_sizes'); ?>
<?php
echo toolbar_open('Update Sizes');

		
	
?>
<div class="btn-group"><input type="submit" class="btn btn-primary" value=" &nbsp;Save" onclick="return validateaddfrm();">
</div>
<?php echo toolbar_close();?>
<input type="hidden" name="s_id" value="<?php echo $id;?>" id="s_id" />

<div class='content'>
    <table class='DataRow' cellpadding='0' cellspacing='0'>
        <tr><th><i class="glyphicon-1x glyphicon-asterisk text-danger"></i> Size Title:</th><td><input type="text" value="<?php echo $size;?>" name="typetitle" id="typetitle" /></td></tr>
    </table>
    </div>

<?php echo form_close(); ?>
<script>
function validateaddfrm()
{
	if(document.getElementById('typetitle').value=="")
	{
		alert("please write size title");
		return false;
	}
	
	
}

</script>