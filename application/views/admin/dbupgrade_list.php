<?php echo form_open(); ?>
<?php

echo toolbar_open('Database Upgrade', 'Current version '.$version);
echo toolbar_item("upgrade", "Upgrade");
echo toolbar_close();

echo message_note();
?>
	
<div class='content'>
<table cellpadding='0' cellspacing='0' class="DataRows">
<th width="5%"><?php echo form_checkids_header();?></th>
	<th>Filename</th>
	<th>Version From</th>
	<th>Version To</th>
</tr>

<?php
foreach ( $rows as $row) {
?>	
<?php echo form_row_color_open(); ?>
<td><?php echo form_checkids_item($row->id, $row->name);?></td>
<td><?php echo $row->name; ?></td>
<td><?php echo $row->from; ?></td>
<td><?php echo $row->to; ?></td>
<?php echo form_row_color_close(); ?>
<?php	
}
?>
</table>
</div>
<?php echo form_close(); ?>
