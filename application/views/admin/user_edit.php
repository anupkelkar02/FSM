<?php echo form_open(); ?>
<?php
echo toolbar_open('User Edit');
echo toolbar_item('logs','Logs', 'log_view.png');
echo toolbar_save();
echo toolbar_apply();
echo toolbar_cancel();
echo toolbar_close();

echo message_note();
?>

<div class='content'>
<table class='DataRow'>
<tr><th>Id:</th><td><?php echo $row->id; ?></td></tr>
<tr><th>Group:</th><td><?php echo form_dropdown('group_id', $groups, $row->group_id); ?></td></tr>
<tr><th>Published:</th><td><?php echo form_checkbox('is_published', 'True', $row->is_published == 'True'); ?></td></tr>
<tr><th>Name:</th><td><?php echo form_input('name', quotes_to_entities($row->name), 'size="60"'); ?></td></tr>
<tr><th>Username:</th><td><?php echo form_input('username', quotes_to_entities($row->username), 'size="60"'); ?></td></tr>
<tr><th>Password:</th><td><?php echo form_password('password', '', 'size="60"'); ?></td></tr>
<tr><th>Email:</th><td><?php echo form_input('email', $row->email, 'size="60"'); ?></td></tr>
<tr><th>Update Time:</th><td><?php echo format_datetime($row->update_time); ?></td></tr>
<tr><th>Last Login Time:</th><td><?php echo format_datetime($row->last_login); ?></td></tr>


</th></tr>
</table>
</div>

<?php echo form_close(); ?>
