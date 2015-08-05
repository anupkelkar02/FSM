<?php echo form_open('admin/duty/url_save'); ?>
<?php
echo toolbar_open('SOP Configuration');
echo toolbar_item('url_save','Save');
echo toolbar_cancel();
echo toolbar_close();

echo message_note();
?>

<div class='content'>
    <table class='DataRow' cellpadding='0' cellspacing='0'>
        <tr><th><i class="glyphicon-1x glyphicon-asterisk text-danger"></i> URL:</th><td><?php echo form_input('sop_url', quotes_to_entities($url), 'size="100"'); ?></td></tr>
    </table>
    </div>

<?php echo form_close(); ?>
