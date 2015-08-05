<?php echo form_open(); ?>
<?php

echo toolbar_open('Users');
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
		<th>Name</th>
		<th>Username</th>
		<th>Group</th>
	</tr>
	<tr>
		<td><?php echo form_input('filter[name_match]', $filter->name_match); ?></td>
		<td><?php echo form_input('filter[username_match]', $filter->username_match); ?></td>
		<td><?php echo form_dropdown('filter[group_id]', $group_list, $filter->group_id); ?></td>
	</tr>
</table>
</fieldset>
	

<table cellpadding='0' cellspacing='0' class="DataRows">
<tr>
	<th width="5%"><?php echo form_checkids_header();?></th>
	<?php echo form_sort_header('last_name', 'Last Name');?>
	<?php echo form_sort_header('first_name', 'First Name');?>
	<th width='5%'>Published</th>
	<?php echo form_sort_header('username', 'Username');?>
	<?php echo form_sort_header('last_login', 'Last Login');?>
	<?php echo form_sort_header('update_time', 'Update Time');?>
	<?php echo form_sort_header('group_title', 'Group');?>
	<th>Id</th>
</tr>
<?php foreach ( $rows as $row) {
?>	
<?php echo form_row_color_open(); ?>
<td><?php echo form_checkids_item($row->id); ?></td>
<td><?php echo anchor('admin/users/edit/'.$row->id, $row->last_name); ?></td>
<td><?php echo $row->first_name; ?></td>
<td align='center'><?php echo form_is_published($row->id, $row->is_published); ?></td>
<td><?php echo $row->username; ?></td>
<td><?php echo format_datetime($row->last_login); ?></td>
<td><?php echo format_datetime($row->update_time); ?></td>
<td><?php echo $row->group_title; ?></td>
<td align='center'><?php echo $row->id; ?></td>
<?php echo form_row_color_close(); ?>
<?php	
} ?>
</table>
</div>

<?php echo form_close(); ?>
