<?php echo form_open('admin/uniforms/save_items'); ?>
<?php
echo toolbar_open('Add Uniform Item');
//echo toolbar_item('url_save','Save');

		
	
?>

<div class="btn-group"><input type="submit" class="btn btn-primary" value=" &nbsp;Save" onclick="return validateaddfrm();">
</div>

<div class="btn-group"><a href="<?php echo base_url();?>index.php/admin/uniforms/listings" style="color:#FFF"><input type="button" class="btn btn-primary" value="&laquo; &nbsp;Cancel"></a>
</div>
<?php echo toolbar_close();
?>
<div class='content'>
<input type="hidden" name="type_id" value="<?php echo $id;?>" />
    <table class='DataRow' cellpadding='0' cellspacing='0'>
        <tr><th><i class="glyphicon-1x glyphicon-asterisk text-danger"></i> Uniform Title:</th><td><strong><?php echo $title;?></strong></td></tr>
         <tr><th><i class="glyphicon-1x glyphicon-asterisk text-danger"></i> Select Size:</th>
         	<td>
            	<select name="size_id" id="size_id">
                	<option value="">Select Size</option>
                    <?php 
    				foreach ( $sizes as $size) 
						{
    				?>
	    		    <option value="<?php echo $size->s_id;?>"><?php echo $size->size;?></option>
                   
    			 <?php }?>
                </select>
            </td>
          </tr>
          <tr><th><i class="glyphicon-1x glyphicon-asterisk text-danger"></i> Qty:</th>
         	<td>
            <input type="text" name="qty" value="" id="qty" />

            </td>
          </tr>
          
    </table>
    </div>

<?php echo form_close(); ?>
<script>
function validateaddfrm()
{
	if(document.getElementById('size_id').value=="")
	{
		alert("please select Item size");
		return false;
	}
	else if(document.getElementById('qty').value=="")
	{
		alert("please put quantity");
		return false;
	}
	
}

</script>