<?php echo form_open(); ?>
<?php

echo toolbar_open('Duty');
//echo toolbar_reload();
//echo toolbar_item('google_sitesync','Google Sync');
//echo toolbar_item('site_upload','Upload Excel Sheet');
////echo toolbar_button('send_dutysms', 'Send SMS');
//echo toolbar_toggle_publish();
//echo toolbar_delete();
//echo toolbar_edit();
//echo toolbar_add();
echo toolbar_send('Send','Send');
echo toolbar_close();

//echo toolbar_open('');
//echo toolbar_item('google_sitesync/sites','Google Sync');
//echo toolbar_close();
echo message_note();


echo form_sort_order($sort_order);
?>

<div class='content'>  
		
		
<!--<fieldset class='filter'>
<legend>Filter</legend>
<table cellpadding='0' cellspacing='0' class='filter'>
	<tr>
		<th>Name</th>
		<th>Published</th>
	</tr>
	<tr>
		<td><?php echo form_input('filter[name_match]', $filter->site_id); ?></td>
		<td><?php echo form_dropdown_boolean('filter[is_published]', $filter->site_id, 'onchange="this.form.submit();"'); ?></td>	
	</tr>
</table>
</fieldset>-->

		
<table cellpadding='0' cellspacing='0' class="DataRows">
<tr>
	<th width="5%"><?php echo form_checkids_header();?></th>
        <?php echo form_sort_header('site', 'Site');?>
	<th width='35%'>Duty</th>
	<th width='5%'>Shift</th>
        <th width='5%'>Start Time</th>
        <th width='5%'>End Time</th>
	<?php echo form_sort_header('update_time', 'Update Time');?>
        <th width='5%'>Send SMS</th>
	
</tr>
<?php foreach ( $rows as $row) {
?>	
<?php echo form_row_color_open(); ?>    
<td><?php echo form_checkids_item($row->id); ?></td>
<td><?php echo anchor('admin/duty/edit/'.$row->id, $row->code); ?></td>
<td><?php echo $row->duty ?></td>
<td align='center'><?php echo $row->shift ?></td>
<td><?php echo $row->start_time; ?></td>
<td><?php echo $row->end_time; ?></td>
<td><?php echo $row->updated_time; ?></td>
<?php $message=$row->duty.' '.$row->start_time?>
<td><?php echo anchor('admin/duty/send_dutysms/'.$row->site_id.'/'.$row->shift.'/'.$message,'Send'); ?></td>
<?php echo form_row_color_close(); ?>
<?php	
} ?>
</table>
<div class='container text-center'><?php echo $pagination_links; ?></div>
</div>

<?php echo form_close(); ?>
