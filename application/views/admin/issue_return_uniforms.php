<?php echo form_open('admin/uniforms/save_inventory'); ?>
<?php
echo toolbar_open('Issue/Return Uniform');
//echo toolbar_item('url_save','Save');

		
	
?>
<div class="btn-group"><input type="submit" class="btn btn-primary" value=" &nbsp;Save" onclick="return validateaddfrm();">
</div>
<div class="btn-group"><a href="<?php echo base_url();?>index.php/admin/uniforms/listings" style="color:#FFF"><input type="button" class="btn btn-primary" value="&laquo; &nbsp;Cancel"></a>
</div>
<?php echo toolbar_close();
?>
<div class='content'>
<input type="hidden" name="type_id" value="<?php echo $id;?>" id="type_id" />
<input type="hidden" name="stock" value="" id="stock" />
    <table class='DataRow' cellpadding='0' cellspacing='0' width="50%">
        <tr><th><i class="glyphicon-1x glyphicon-asterisk text-danger"></i> Uniform Title:</th><td><strong><?php echo $title;?></strong></td></tr>
         <tr><th><i class="glyphicon-1x glyphicon-asterisk text-danger"></i> Select Size:</th>
         	<td>
            	<select name="size_id" id="size_id" onchange="CheckStock();">
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
          
           <tr><th><i class="glyphicon-1x glyphicon-asterisk text-danger"></i> Available Stock:</th>
         	<td>
            	<span id="availstock"></span>
            </td>
          </tr>
          
          <tr><th><i class="glyphicon-1x glyphicon-asterisk text-danger"></i> Qty:</th>
         	<td>
            <input type="text" name="qty" value="" id="qty" />

            </td>
          </tr>
          
          <tr><th><i class="glyphicon-1x glyphicon-asterisk text-danger"></i> Name:</th>
         	<td>
            <select name="name" id="name" onchange="CheckStock();">
                	<option value="">Select Name</option>
                    <?php 
    				foreach ( $staff as $staffmembers) 
						{
    				?>
	    		    <option value="<?php echo $staffmembers->id;?>"><?php echo $staffmembers->first_name;?></option>
                   
    			 <?php }?>
                </select>

            </td>
          </tr>
          
           <tr><th><i class="glyphicon-1x glyphicon-asterisk text-danger"></i>Issue/Return:</th>
         	<td>
            	<select name="type" id="type">
                	<option value="">Select Inventory Type</option>
                    <option value="Issued">Issue</option>
                    <option value="Return">Return</option>
                </select>
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
	else if(document.getElementById('qty').value=="" || document.getElementById('qty').value==0)
	{
		alert("please check qty field value this is should not be empty or 0");
		return false;
	}
	else if(document.getElementById('name').value=="")
	{
		alert("please write name");
		return false;
	}
	else if(document.getElementById('type').value=="")
	{
		alert("please select inventory type");
		return false;
	}
	
	var stock= Number(document.getElementById('stock').value);
	var qty= Number(document.getElementById('qty').value);
	
	if(document.getElementById('type').value=='Issued')
	{
		if( stock < qty)
		{
		alert("you have not sufficient stock to issue");
		var er=0;
		}
	}
	else
	{
	var er=1;	
	}
	
	
	
	if(er==0)
	{
	return false;	
	}
	else
	{
	return true;	
	}
	
}

</script>