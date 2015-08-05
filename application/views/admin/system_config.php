<?php echo form_open(); ?>
<?php
echo toolbar_open('System Configuration');
echo toolbar_save();
echo toolbar_close();
echo message_note();
?>

<div class='content'>
<table class='DataRow' cellpadding='0' cellspacing='0'>
        <tr><th><i class="glyphicon-1x glyphicon-asterisk text-danger"></i> Automatic Call Confirmation:</th><td><?php echo form_checkbox('autocall_confirm', 'True', $row->autocall_confirm == 'True'); ?></td></tr>
        <tr><th><i class="glyphicon-1x glyphicon-asterisk text-danger"></i> Automatic Call Confirmation Time Before:</th><td><?php echo form_input('autocall_time', quotes_to_entities($row->autocall_time), 'size="30" placeholder="0"'); ?> Minutes<br/><span class="note">Keep 0 If want to confirm on shift start time</span></td></tr>
        <tr><th><i class="glyphicon-1x glyphicon-asterisk text-danger"></i> Check-in Reminder SMS:</th><td><?php echo form_checkbox('check_in_alert', 'True', $row->check_in_alert == 'True'); ?></td></tr>
        <tr><th><i class="glyphicon-1x glyphicon-asterisk text-danger"></i> Check-out Reminder SMS:</th><td><?php echo form_checkbox('check_out_alert', 'True', $row->check_out_alert == 'True'); ?></td></tr>
        <tr><th><i class="glyphicon-1x glyphicon-asterisk text-danger"></i> SOP Alert By Calls:</th><td><?php echo form_checkbox('sop_alert', 'True', $row->sop_alert == 'True'); ?></td></tr>
        <tr><th><i class="glyphicon-1x glyphicon-asterisk text-danger"></i> SOP Alert By SMS:</th><td><?php echo form_checkbox('sop_alert_sms', 'True', $row->sop_alert_sms == 'True'); ?></td></tr>
        <tr><th><i class="glyphicon-1x glyphicon-asterisk text-danger"></i> Automatic Leave Planning Calls:</th><td><?php echo form_checkbox('autoleave_plan_call', 'True', $row->autoleave_plan_call == 'True'); ?></td></tr>
        <tr><th><i class="glyphicon-1x glyphicon-asterisk text-danger"></i> Calls to Guard House at Sites instead of calling Staff:</th><td><?php echo form_checkbox('call_gaurdhouse', 'True', $row->call_gaurdhouse == 'True'); ?></td></tr>
        <tr><th><i class="glyphicon-1x glyphicon-asterisk text-danger"></i> Timezone:</th><td>
                <select name="sys_timezone">
                    <option value="">Select Timezone</option>
                    <?php foreach ($timezone as $z) { $sel = ($z->id==$row->sys_timezone)?'selected':''; ?>
                        <option value="<?php echo $z->id; ?>" <?php echo $sel ; ?>><?php echo $z->timezone; ?></option>
                    <?php } ?>
                </select></td></tr>
        <tr><th><i class="glyphicon-1x glyphicon-asterisk text-danger"></i> Control Room Phone# :</th><td><?php echo form_input('ofc_number', quotes_to_entities($row->ofc_number), 'size="30"'); ?> </td></tr>
</table>
    </div>

<?php echo form_close(); ?>
