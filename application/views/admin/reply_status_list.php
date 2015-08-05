<?php echo form_open(); ?>
<?php

echo toolbar_open('Reply Status');
echo toolbar_reload();
echo toolbar_edit();
echo toolbar_close();

echo message_note();

echo form_sort_order($sort_order);
?>

<div class='content'>
		
		
		
<table cellpadding='0' cellspacing='0' class="DataRows">
<tr>
	<th width="5%"><?php echo form_checkids_header();?></th>
	<?php echo form_sort_header('title', 'Title');?>
	<th>Code</th>
	<th>Number</th>
	<th>Id</th>
</tr>
<?php foreach ( $rows as $row) {
?>	
<?php echo form_row_color_open(); ?>
<td><?php echo form_checkids_item($row->id); ?></td>
<td><?php echo anchor('admin/reply_status/edit/'.$row->id, $row->title); ?></td>
<td><?php echo $row->code; ?></td>
<td><?php echo $row->number; ?></td>
<td align='center'><?php echo $row->id; ?></td>
<?php echo form_row_color_close(); ?>
<?php	
} ?>
</table>
</div>

<?php echo form_close(); ?>
