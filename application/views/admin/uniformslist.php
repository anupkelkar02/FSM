<?php echo form_open(); ?>

<?php /*?><div class="btn-toolbar pull-right"><div class="btn-group">
<a href="<?php echo base_url();?>index.php/admin/uniforms/add">
<input type="button" class="btn btn-primary" value="+ Add Type">
</a>
</div>
<div class="btn-group">
<a href="<?php echo base_url();?>index.php/admin/uniforms/add_sizes">
<input type="button" class="btn btn-primary" value="+ Add Sizes">
</a>


</div></div><?php */?>


<div class="btn-toolbar pull-right"><div class="btn-group">
<a href="<?php echo base_url();?>index.php/admin/uniforms/typeslist">
<input type="button" class="btn btn-primary" value="Available types">
</a>
</div>
<div class="btn-group">
<a href="<?php echo base_url();?>index.php/admin/uniforms/sizeslist">
<input type="button" class="btn btn-primary" value="Available sizes">
</a>


</div></div>

<div style="clear:both; height:20px"></div>

<?php

echo toolbar_open('Uniforms In Stock');
?>
<div class='content'>  
		
	
		
<table cellpadding='0' cellspacing='0' class="DataRows">
<tr>
	<th width="5%">#</th>
  	<th width='35%'>Type</th>
    <?php 
    foreach ( $sizes as $size) 
		{
    ?>
	    <th width='5%'><?php echo $size->size;?></th>
     <?php }?>
</tr>
<?php foreach ( $rows as $row) {
?>
<tr>	
    <td><?php echo $row->u_id; ?></td>
    <td><?php echo $row->title; ?>
    
     <a href="<?php echo base_url();?>index.php/admin/uniforms/inventory/<?php echo $row->u_id; ?>">
	<input type="button" class="btn btn-primary" value="+ Issue/Return" style="float:right; margin:5px; padding:5px;">
	</a>
   
    <a href="<?php echo base_url();?>index.php/admin/uniforms/additem/<?php echo $row->u_id; ?>">
	<input type="button" class="btn btn-primary" value="+ Add Item " style="float:right;margin:5px;padding:5px;">
	</a>
    
    
    </td>
	<?php 
    foreach ( $sizes as $size) 
		{
    ?>
      <td>
		<?php 
		$getaddstock=$this->uniforms_model->get_uniforms_stock($row->u_id,$size->s_id);
		$getissuestock=$this->uniforms_model->get_uniforms_issue($row->u_id,$size->s_id);
		$getreturnstock=$this->uniforms_model->get_uniforms_return($row->u_id,$size->s_id); 
     	
		echo ($getaddstock[0]->totalstock-$getissuestock[0]->totalissued)+$getreturnstock[0]->totalreturn;?>
     </td>
   <?php }?>
</tr>
<?php	
} ?>
</table>
<div class='container text-center'><?php echo $pagination_links; ?></div>
</div>

<?php echo form_close(); ?>
