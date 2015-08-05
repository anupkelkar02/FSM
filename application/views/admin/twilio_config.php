<?php echo form_open(); ?>
<?php
echo toolbar_open('Twilio Configuration');
echo toolbar_save();
echo toolbar_cancel();
echo toolbar_close();

echo message_note();
?>

<div class='content'>
    <table class='DataRow' cellpadding='0' cellspacing='0'>
        <tr><th><i class="glyphicon-1x glyphicon-asterisk text-danger"></i> SID:</th><td><?php echo form_input('twilio_sid', quotes_to_entities($row->twilio_sid), 'size="60"'); ?></td></tr>
<tr><th><i class="glyphicon-1x glyphicon-asterisk text-danger"></i> Token:</th><td><?php echo form_input('twilio_token', quotes_to_entities($row->twilio_token), 'size="60"'); ?></td></tr>
<tr><th><i class="glyphicon-1x glyphicon-asterisk text-danger"></i> From Number:</th><td><?php echo form_input('twilio_number', $row->twilio_number, 'size="20"'); ?></td></tr>
</table>
    </div>

<?php echo form_close(); ?>
