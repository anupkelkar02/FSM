<?php echo form_open(); ?>
<?php

echo toolbar_open('Staff');

echo toolbar_item('google_staffsync','Google Sync');echo toolbar_reload();
echo toolbar_toggle_publish();
echo toolbar_delete();
echo toolbar_edit();
echo toolbar_add();
echo toolbar_close();

echo message_note();

echo form_sort_order($sort_order);
?>

<div class='content'>
		
		
<fieldset class='filter'>
<legend>Filter</legend>
<table cellpadding='0' cellspacing='0' class='filter'>
	<tr>
		<th>Name</th>
		<th>Published</th>
	</tr>
	<tr>
		<td><?php echo form_input('filter[name_match]', $filter->name_match); ?></td>
		<td><?php echo form_dropdown_boolean('filter[is_published]', $filter->is_published, 'onchange="this.form.submit();"'); ?></td>	
	</tr>
</table>
</fieldset>
<div class='container text-center'><?php echo $pagination_links; ?></div>		
<table cellpadding='0' cellspacing='0' class="DataRows">
<tr>
	<th width="5%"><?php echo form_checkids_header();?></th>
	<?php echo form_sort_header('last_name', 'Last Name');?>
	<?php echo form_sort_header('first_name', 'First Name');?>
	<th>Phone number</th>
<th>DOB</th>
	<th>Site</th>
	<th width='5%'>Published</th>
	<?php echo form_sort_header('update_time', 'Update Time');?>
	<th>Id</th>
</tr>
<?php foreach ( $rows as $row) {
?>	
<?php echo form_row_color_open(); ?>
<td><?php echo form_checkids_item($row->id); ?></td>
<td><?php echo $row->last_name; ?></td>
<td><?php echo anchor('admin/staff/edit/'.$row->id,$row->first_name); ?></td>
<td><?php echo $row->phone_number; ?></td>
<?php if($row->dob==null){
$date='';
}else{
//$date=date_create($row->dob);}
$date=date('d-m-Y',strtotime($row->dob));}
 ?>
<td><?php  echo $date; //echo date_format($date,"d-m-Y"); ?></td>

<td><?php echo implode("<br>", site_list_assignment_staff_id($row->id));?></td>
<td align='center'><?php echo form_is_published($row->id, $row->is_published); ?></td>
<td><?php echo format_datetime($row->update_time); ?></td>
<td align='center'><?php echo $row->id; ?></td>
<?php echo form_row_color_close(); ?>
<?php	
} ?>
</table>
<div class='container text-center'><?php echo $pagination_links; ?></div>
</div>

<?php echo form_close(); ?>
