<?php echo message_note(); ?>
<!--<script type="text/javascript">
        tinymce.init({
            selector: "#mytextarea",
            plugins: [
         "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
         "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
         "save table contextmenu directionality emoticons template paste textcolor"
   ],
        });
    </script>-->
<script>
    $(document).ready(function() {
    var text_max = 160;
    $('#txt_cnt').html('(0/'+text_max + ')');

    $('#mytextarea').keyup(function() {
        var text_length = $('#mytextarea').val().length;
       // var text_remaining = text_max - text_length;

        $('#txt_cnt').html('('+text_length+'/'+text_max + ')');
    });
});
</script>
<div class='content'>
    <h3>Birthday Greeting</h3>
    <form name="bdayfrm" action="<?php echo base_url();?>index.php/admin/home/bday_save" method="post">
        <div>
        <textarea id="mytextarea" name="mytextarea" required="required" rows="10" cols="159" maxlength="161"><?php echo $bday_temp;?></textarea><br/><span style="float: right" id="txt_cnt">(0/160)
        </span>
        </div>
        <br/>
 <br/> <br/>
        <div style="float: right">
        <center><button class="btn btn-success" onclick="document.bdayfrm.submit();">Save Template</button></center>
    </div>
    </form>
    <br/><br/>
    <span class="note">You can provide below dynamic attributes<br/><u>#name#</u> : Name of the staff<br/><u>#mobile#</u>: Mobile number of the staff<br/><u>#date#</u>: Today date<br/><u>#time#</u>: Current Time</span>
</div>