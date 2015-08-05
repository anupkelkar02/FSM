<?php echo form_open(); ?>
<?php
echo toolbar_open('Broadcast Message');
echo toolbar_save('SMS','glyphicon-comment');
echo toolbar_apply('Call','glyphicon-phone');
echo toolbar_cancel('Reset','glyphicon-remove');
echo toolbar_close();

echo message_note();
//var_dump($site_list);
?>

<div class='content'>
    <table class='DataRow' cellpadding='0' cellspacing='0'>
        <tr><th><i class="glyphicon-1x glyphicon-asterisk text-danger"></i> Site:</th><td><?php echo form_dropdown('site_id', $site_list, '', 'onchange="get_staff(this.value)"'); ?></td></tr>
<tr><th><i class="glyphicon-1x glyphicon-asterisk text-danger"></i> Staffs:</th><td><?php echo form_dropdown('staff_id[]', $staff_list, '', 'id="staff_id" multiple="multiple"'); ?></td></tr>
<tr><th><i class="glyphicon-1x glyphicon-asterisk text-danger"></i> Message:</th><td><?php echo form_textarea('text_msg'); ?></td></tr>
</table>
    </div>

<?php echo form_close(); ?>
<script>
    function get_staff(site_id){
        $.ajax({
        type: "POST",
        url: "<?php echo base_url().'index.php/admin/send_sms/get_staff';?>",
        data: 'site_id='+site_id,
        cache: false,
        success: function(result) {
            console.log(result);
            //alert(result);
            $("#staff_id").
            html("");
            
            $("#staff_id").
            html("<option value='0'>Select Staff</option>");
        
            $("#staff_id").append(result);
        }

    });
    }
</script>
