<?php echo form_open(); ?>
<?php
echo toolbar_open('Work Status Edit');
echo toolbar_save();
echo toolbar_apply();
echo toolbar_cancel();
echo toolbar_close();

echo message_note();
?>

<div class='content'>
<table class='DataRow' cellpadding='0' cellspacing='0'>
<tr><th>Id:</th><td><?php echo $row->id; ?></td></tr>
<tr><th>Title:</th><td><?php echo form_input('title', quotes_to_entities($row->title), 'size="60"'); ?></td></tr>
<tr><th>Code:</th><td><?php echo form_input('code', quotes_to_entities($row->code), 'size="10"'); ?></td></tr>
<tr><th>Text Color:</th><td><?php echo jscolor_picker('text_color', quotes_to_entities($row->text_color), 'size="12"'); ?></td></tr>
<tr><th>Background Color:</th><td><?php echo jscolor_picker('background_color', quotes_to_entities($row->background_color), 'size="12"'); ?></td></tr>
</table>
</div>

<?php echo form_close(); ?>
