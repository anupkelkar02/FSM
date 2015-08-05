<?php echo form_open(); ?>
<?php

echo toolbar_open('Duty');

echo toolbar_send('Send','Send');
echo toolbar_close();
echo message_note();
echo form_sort_order($sort_order);
?>

<div class='content'>  
		
	
		
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
