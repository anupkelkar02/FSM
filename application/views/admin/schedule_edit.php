<?php echo form_open(); ?>
<?php
echo toolbar_open('Schedule Edit');
echo toolbar_save();
echo toolbar_apply();
echo toolbar_cancel();
echo toolbar_close();

echo message_note();
?>

<div class='content'>
<table class='DataRow' cellpadding='0' cellspacing='0'>
<tr><th>Id:</th><td><?php echo $row->id; ?></td></tr>
<tr><th>Date:</th><td><?php echo form_input('start_date', $row->start_date); ?></td></tr>
<tr><th>Site:</th><td><?php echo form_dropdown('site_id', $site_list, $row->site_id); ?></td></tr>
<tr><th>Staff:</th><td><?php echo form_dropdown('staff_id', $staff_list, $row->staff_id); ?></td></tr>
<tr><th>Work Status:</th><td><?php echo form_dropdown('work_status_id', $work_status_list, $row->work_status_id); ?></td></tr>
<tr><th>Reply Status:</th><td><?php echo form_dropdown('reply_status_id', $reply_status_list, $row->reply_status_id); ?></td></tr>
<tr><th>Last Attendance Request</th><td><?php echo format_datetime($row->attendance_request_time); ?></td></tr>
</th></tr>
</table>
</div>

<?php echo form_close(); ?>
