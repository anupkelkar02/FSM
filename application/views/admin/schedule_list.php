<?php echo form_open(); ?>
<?php

echo toolbar_open('Schedules');
echo toolbar_reload();
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
		<th>Date</th>
		<th>Site</th>
	</tr>
	<tr>
		<td><?php echo form_input('filter[start_date]', $filter->start_date); ?></td>
		<td><?php echo form_dropdown('filter[site_id_match]', $site_list, $filter->site_id_match, 'onchange="this.form.submit();"'); ?></td>	
	</tr>
</table>
</fieldset>

		
<table cellpadding='0' cellspacing='0' class="DataRows">
<tr>
	<th width="5%"><?php echo form_checkids_header();?></th>
	<?php echo form_sort_header('start_date', 'Date');?>
	<th>Site</th>
	<th>Staff</th>
	<th>Work Status</th>
	<th>Reply Status</th>
	<th>Attendance Request</th>
</tr>
<?php foreach ( $rows as $row) {
?>	
<?php echo form_row_color_open(); ?>
<td><?php echo form_checkids_item($row->id); ?></td>
<td><?php echo anchor('admin/schedule/edit/'.$row->id, format_date($row->start_date)); ?></td>
<td><?php echo site_anchor_from_id($row->site_id); ?></td>
<td><?php echo staff_anchor_from_id($row->staff_id); ?></td>
<td><?php echo work_status_show_text($row->work_status_id); ?></td>
<td><?php echo reply_status_show_text($row->reply_status_id); ?></td>
<td><?php echo format_datetime($row->attendance_request_time); ?></td>
<?php echo form_row_color_close(); ?>
<?php	
} ?>
</table>
<div class='container text-center'><?php echo $pagination_links; ?></div>
</div>

<?php echo form_close(); ?>
