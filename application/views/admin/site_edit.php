<?php echo form_open(); ?>
<?php
echo toolbar_open('Site Edit');
echo toolbar_save();
echo toolbar_apply();
echo toolbar_cancel();
echo toolbar_close();

echo message_note();
?>

<div class='content'>
	
<?php echo jquery_tab_open(array('main'=>'Site',
								'shifts'=>'Shift Times',
								'assignments'=>'Staff',
                            'workingdays'=>'Working Days')); ?>
<?php echo jquery_tab_page_open('main'); ?>
<table class='DataRow' cellpadding='0' cellspacing='0'>
<tr><th>Id:</th><td><?php echo $row->id; ?></td></tr>
<tr><th>Name:</th><td><?php echo form_input('name', quotes_to_entities($row->name), 'size="60"'); ?></td></tr>
<tr><th>Code:</th><td><?php echo form_input('code', quotes_to_entities($row->code), 'size="60"'); ?></td></tr>
<tr><th>Street Number:</th><td><?php echo form_input('street_number', $row->street_number, 'size="20"'); ?></td></tr>
<tr><th>Street Name:</th><td><?php echo form_input('street_name', $row->street_name, 'size="60"'); ?></td></tr>
<tr><th>Unit Number:</th><td><?php echo form_input('unit_number', $row->unit_number, 'size="20"'); ?></td></tr>
<tr><th>City:</th><td><?php echo form_input('city', $row->city, 'size="60"'); ?></td></tr>
<tr><th>Post Code:</th><td><?php echo form_input('postcode', $row->postcode, 'size="20"'); ?></td></tr>
<tr><th>Country:</th><td><?php echo form_input('country', $row->country, 'size="60"'); ?></td></tr>
<tr><th>District</th><td><?php echo $postal_disctrict_row->number.'<br>'.$postal_disctrict_row->location; ?></td></tr>
<tr><th>Contract Start Date</th><td><?php echo form_input('contract_sdate', $row->contract_sdate, 'size="20"'); ?></td></tr>
<tr><th>Contract End Date</th><td><?php echo form_input('contract_edate', $row->contract_edate, 'size="20"'); ?></td></tr>
<tr><th>Published:</th><td><?php echo form_checkbox('is_published', 'True', $row->is_published == 'True'); ?></td></tr>
<tr><th>GPS Position:</th><td><?php echo form_input('gps_x', '1.289545', 'size="12"');?> Latitude<br><?php echo form_input('gps_x', '103.849972', 'size="12"');?> Longitude</td></tr>
<tr><th>Update Time:</th><td><?php echo format_datetime($row->update_time); ?></td></tr>
	

</th></tr>
</table>
<?php echo jquery_tab_page_close(); ?>
<?php echo jquery_tab_page_open('shifts'); ?>

<table cellpadding='0' cellspacing='0' class="DataRows">
<tr>
	<th width="5%"><?php echo form_checkids_header('shift_check_id');?></th>
	<th>Type</th>
	<th>Start Time</th>
	<th>End Time</th>
	<th>Staff Count</th>
	<th>Max Relief Count</th>
	<th width='5%'>Published</th>
	<?php echo form_sort_header('update_time', 'Update Time');?>
	<th>Id</th>
</tr>
<?php foreach ( $shift_rows as $shift_row) {
?>	
<?php echo form_row_color_open(); ?>
<td><?php echo form_checkids_item($shift_row->id, '', 'shift_check_id'); ?></td>
<td><?php echo $shift_row->shift_type; ?></td>
<td>
    <input type="hidden" name="shiftid[]" value="<?php echo $shift_row->id?>"/>
    <?php echo form_input('start_time[]', $shift_row->start_time, 'size="10"'); ?></td>

<td><?php echo form_input('end_time[]', $shift_row->end_time, 'size="10"'); ?></td>
<td><?php echo $shift_row->staff_count;?></td>
<td><?php echo $shift_row->max_relief_count;?></td>
<td align='center'><?php echo form_is_published($shift_row->id, $shift_row->is_published); ?></td>
<td><?php echo format_datetime($shift_row->update_time); ?></td>
<td align='center'><?php echo $shift_row->id; ?></td>
<?php echo form_row_color_close(); ?>
<?php	
} ?>
</table>

<?php echo jquery_tab_page_close(); ?>
<?php echo jquery_tab_page_open('assignments'); ?>

<div class='toolbar_sub'>
<?php echo toolbar_button('request_attendance', 'Request Attendance', 'glyphicon glyphicon-calendar');?> &nbsp;
<?php echo toolbar_button('add_staff', 'Add Staff', 'glyphicon glyphicon-plus');?> &nbsp;
<?php echo toolbar_button('remove_staff', 'Remove Staff', 'glyphicon glyphicon-remove');?>
</div>
<table cellpadding='0' cellspacing='0' class="DataRows">
<tr>
	<th width="5%"><?php echo form_checkids_header('assignment_check_id');?></th>
	<th>Shift Type</th>
	<th>Assign Type</th>
	<th>Staff Name</th>
</tr>
<?php foreach ( $assignment_rows as $assignment_row) {
?>	
<?php echo form_row_color_open(); ?>
<td><?php echo form_checkids_item($assignment_row->id, '', 'assignment_check_id'); ?></td>
<td><?php echo form_dropdown('assignment_shift_type['.$assignment_row->id.']', $shift_type_list, $assignment_row->shift_type); ?></td>
<td><?php echo form_dropdown('assignment_assign_type['.$assignment_row->id.']', $assign_type_list, $assignment_row->assign_type); ?></td>
<td><?php echo form_dropdown('assignment_staff_id['.$assignment_row->id.']', $staff_list, $assignment_row->staff_id); ?></td>
<?php echo form_row_color_close(); ?>
<?php	
} ?>
</table>

<?php echo jquery_tab_page_close(); ?>

<?php echo jquery_tab_page_open('workingdays'); ?>

<table cellpadding='0' cellspacing='0' class="DataRows">
<tr>
	
	<th>Day</th>
	<th>Half Day / Full Day / Off</th>
	
</tr>
<?php //foreach ( $shift_rows as $shift_row) {
?>	
<?php echo form_row_color_open(); ?>
<?php $dayNames = array(
    'Monday', 
    'Tuesday', 
    'Wednesday', 
    'Thursday', 
    'Friday', 
    'Saturday', 
'Sunday'
 );
 //var_dump($workingdays_rows);
for($i=1;$i<=7;$i++) { ?>
<td><input type="hidden" name="days[]" value="<?php echo $i;?>" /><?php echo $dayNames[$i-1];?></td>
<td><select name="selday[]">
        <option value="1" <?php if($workingdays_rows[$i-1]->shift_type=='1'){ ?> selected="selected" <?php } ?>>Full Day</option>
        <option  value="2" <?php if($workingdays_rows[$i-1]->shift_type=='2'){ ?> selected="selected" <?php } ?>>Half Day</option>
        <option  value="3"  <?php if($workingdays_rows[$i-1]->shift_type=='3'){ ?> selected="selected" <?php } ?>>Off</option></select></td>

<?php echo form_row_color_close(); ?>
<?php	
} ?>
</table>

<?php echo jquery_tab_page_close(); ?>
<?php echo jquery_tab_close(); ?>

</div>

<?php echo form_close(); ?>

<?php 

function off_days_select($off_day_names)
{
	$off_day_names = explode(",", $off_day_names);
	$result = array();
	$day_names = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');
	foreach ( $day_names as $day_name ) {
		$result[] = form_checkbox('off_day_name', $day_name, in_array($day_name, $off_day_names) ).$day_name;
	}
	return implode(",&nbsp;", $result);
}
?>
