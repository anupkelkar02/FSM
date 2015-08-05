<?php echo form_open(); ?>
<?php
echo toolbar_open('Staff Edit');
echo toolbar_save();
echo toolbar_apply();
echo toolbar_cancel();
echo toolbar_close();

echo message_note();
?>

<div class='content'>
	
<?php echo jquery_tab_open(array('main'=>'Staff',
								'assignments'=>'Sites')); ?>
<?php echo jquery_tab_page_open('main'); ?>

<div class='toolbar_sub'>
<?php 
echo toolbar_button('google_sync', 'Google Sync', '', '', 'btn-primary btn-sm');
echo toolbar_button('request_setup', 'Request Setup', '', '', 'btn-primary btn-sm');
?>
</div>
<table class='DataRow' cellpadding='0' cellspacing='0'>
<tr><th>Id:</th><td><?php echo $row->id; ?></td></tr>
<tr><th>Last Name:</th><td><?php echo form_input('last_name', quotes_to_entities($row->last_name), 'size="60"'); ?></td></tr>
<tr><th>First Name:</th><td><?php echo form_input('first_name', quotes_to_entities($row->first_name), 'size="60"'); ?></td></tr>
<tr><th>Phone Number:</th><td><?php echo form_input('phone_number', quotes_to_entities($row->phone_number), 'size="60"'); ?></td></tr>
<tr><th>Email:</th><td><?php echo form_input('email', quotes_to_entities($row->email), 'size="60"'); ?></td></tr>
<tr><th>DOB:</th><td><?php echo form_input('dob', quotes_to_entities(date('d-m-Y', strtotime($row->dob))), 'size="60"'); ?></td></tr>
<tr><th>FIN:</th><td><?php echo form_input('fin_number', quotes_to_entities($row->fin_number), 'size="60"'); ?></td></tr>
<tr><th>Address:</th><td><?php echo form_textarea('address', $row->address, 'cols="60"'); ?></td></tr>
<tr><th>Send Birthday Greetings:</th><td><?php echo form_checkbox('dob_alert', 'True', $row->dob_alert == 'True'); ?></td></tr>
<tr><th>Call Preference:</th><td><?php echo form_dropdown('call_type', $call_type_list, $row->call_type);?></td></tr>
<tr><th>Call before shift:</th><td><?php echo form_input('call_minutes', quotes_to_entities($row->call_minutes), 'size="10"'); ?> Minutes</td></tr>
<tr><th>Published:</th><td><?php echo form_checkbox('is_published', 'True', $row->is_published == 'True'); ?></td></tr>
<tr><th>Update Time:</th><td><?php echo format_datetime($row->update_time); ?></td></tr>
</th></tr>
</table>

<?php echo jquery_tab_page_close(); ?>
<?php echo jquery_tab_page_open('assignments'); ?>

<div class='toolbar_sub'>
<?php 
	echo toolbar_button('request_attendance', 'Request Attendance');
	echo toolbar_button('add_site', 'Add Site', 'glyphicon glyphicon-plus');
	echo toolbar_button('remove_site', 'Remove Site', 'glyphicon glyphicon-remove');
?>
</div>
<table cellpadding='0' cellspacing='0' class="DataRows">
<tr>
	<th width="5%"><?php echo form_checkids_header('assignment_check_id');?></th>
	<th>Shift Type</th>
	<th>Assign Type</th>
	<th>Site Name</th>
	<th>Next Shift</th>
</tr>
<?php foreach ( $assignment_rows as $assignment_row) {
?>	
<?php echo form_row_color_open(); ?>
<td><?php echo form_checkids_item($assignment_row->id, '', 'assignment_check_id'); ?></td>
<td><?php echo form_dropdown('assignment_shift_type['.$assignment_row->id.']', $shift_type_list, $assignment_row->shift_type); ?></td>
<td><?php echo form_dropdown('assignment_assign_type['.$assignment_row->id.']', $assign_type_list, $assignment_row->assign_type); ?></td>
<td><?php echo form_dropdown('assignment_site_id['.$assignment_row->id.']', $site_list, $assignment_row->site_id); ?></td>
<td><?php echo $assignment_row->next_shift ? $assignment_row->next_shift->start_time: 'No Shift'; ?></td>
<?php echo form_row_color_close(); ?>
<?php	
} ?>
</table>

<?php echo jquery_tab_page_close(); ?>


<?php echo jquery_tab_close(); ?>

</div>

<?php echo form_close(); ?>
