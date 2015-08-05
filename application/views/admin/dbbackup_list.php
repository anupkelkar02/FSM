<?php echo form_open(); ?>
<?php

echo toolbar_open('Database Backup and Restore', 'Current version '.$version);
echo toolbar_delete();
echo toolbar_item("backup", "Backup");
echo toolbar_item("restore", "Restore");
echo toolbar_close();

echo message_note();
?>
	
<div class='content'>
<?php
if ( count($rows) == 0 ) {
	?>
	<h3>No backup files found</h3>
<?php
	return;
}
?>

<table cellpadding='0' cellspacing='0' class="DataRows">
<th width="5%"><?php echo form_checkids_header(); ?></th>
<th>Filename</th>
<th>Date Time</th>
<th>Database Name</th>
<th>Version</th>
<th>Size</th>
</tr>

<?php
foreach ( $rows as $row ) {
?>
	<?php echo form_row_color_open(); ?>
	<td><?php echo form_checkids_item( $row->id, $row->name); ?></td>
	<td><?php echo anchor('admin/dbbackup/download/'.$row->id, $row->name); ?></td>
	<td align='center'><?php echo $row->datetime;?></td>
	<td align='center'><?php echo $row->database_name;?></td>
	<td align='center'><?php echo $row->version;?></td>
	<td align='center'><?php echo $row->get_size_text();?></td>
	<?php echo form_row_color_close(); ?>
<?php
}
?>

</table>
</div>
<?php echo form_close(); ?>





