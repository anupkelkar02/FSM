<?php echo form_open('admin/uniforms/delete_size'); ?>
<?php

echo toolbar_open('Multiple Sizes of uniforms');
?>
<div class="btn-toolbar pull-right"><div class="btn-group">
<a href="<?php echo base_url();?>index.php/admin/uniforms/add_sizes">
<input type="button" class="btn btn-primary" value="+ Add Sizes">
</a>
</div>



<div class="btn-group">
<input type="submit" class="btn btn-primary" value="- Delete Sizes" onclick="return confirm('Are you sure to delete selected items?');">



</div>



</div>




<div style="clear:both; height:20px"></div>


<div class='content'>  
		
	
		
<table cellpadding='0' cellspacing='0' class="DataRows" width="100%" style="float:left">
<tr>
	<th width="5%">#</th>
  	<th width='95%'>Size Title</th>
    <th>Action</th>
    
</tr>
<?php foreach ( $sizes as $row) {
?>
<tr>
	<td><?php echo form_checkids_item($row->s_id); ?></td>	
   
    <td><?php echo $row->size; ?></td>
    <td colspan="2">
    <div class="btn-group">
<a href="<?php echo base_url();?>index.php/admin/uniforms/edit_sizes/<?php echo $row->s_id;?>">
<input type="button" class="btn btn-primary" value=" Edit Size">
</a>


</div>
    </td>
	
</tr>
<?php	
} ?>
</table>
<div class='container text-center'><?php echo $pagination_links; ?></div>
</div>

<?php echo form_close(); ?>
