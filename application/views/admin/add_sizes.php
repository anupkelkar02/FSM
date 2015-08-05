<?php echo form_open('admin/uniforms/save_sizes'); ?>
<?php
echo toolbar_open('Add Sizes');

		
	
?>
<div class="btn-group"><input type="submit" class="btn btn-primary" value=" &nbsp;Save" onclick="return validateaddfrm();">
</div>
<?php echo toolbar_close();?>
<div class='content'>
    <table class='DataRow' cellpadding='0' cellspacing='0'>
        <tr><th><i class="glyphicon-1x glyphicon-asterisk text-danger"></i> Size Title:</th><td><input type="text" name="typetitle" id="typetitle" /></td></tr>
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