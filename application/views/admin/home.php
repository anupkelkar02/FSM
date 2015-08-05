<?php echo form_open(); ?>
<?php echo message_note(); ?>

<div class='content'>
	
<?php echo jquery_tab_open(array('shift'=>'Shift Confirmation',
								'attendance'=>'Attendance Discrepancy',
								'leave'=>'Leave Plans')); ?>
<?php echo jquery_tab_page_open('shift'); ?>

<h4><span class="col-lg-11">&nbsp;</span><a href="#" onclick="get_Details()"><i class="glyphicon glyphicon-refresh" title="Reload">Reload</i></a></h4>
<table cellpadding='0' cellspacing='0' class="table table-bordered col-md-6">
    <thead>
		<tr >
			<th>Site</th>
            <th>Present</th>
            <th>Absent</th>
            <th>Not Responded</th>
            <th>Callback Requested</th>
        </tr>
    </thead>
    <tbody id="live-stats">  </tbody>
</table>


<?php echo jquery_tab_page_close(); ?>
<?php echo jquery_tab_page_open('attendance'); ?>

<h4><span class="col-lg-11">&nbsp;</span><a href="#" onclick="get_Attendance()"><i class="glyphicon glyphicon-refresh" title="Reload">Reload</i></a></h4>
<table cellpadding='0' cellspacing='0' class="table table-bordered col-md-6">
    <thead>
		<tr >
			<th>Site</th>
            <th>Date</th>
            <th>Present</th>
            <th>Absent</th>
            <th>Not Responded</th>
            <th>Callback Requested</th>
            <th>Discrepancy</th>
				
		</tr>
    </thead>
     <tbody id="attendance-stats">  </tbody>
   
</table>

<?php echo jquery_tab_page_close(); ?>
<?php echo jquery_tab_page_open('leave'); ?>

<h4><span class="col-lg-11">
	<select id="leave_type">
    	<option value="">Select Leave Type</option>
        <option value="2">Medical Leave</option>
        <option value="3">Annual Leave</option>
        <option value="4">Sick Leave</option>
        <option value="7">Casual Leave</option>
        
     </select></span>
     <a href="#" onclick="get_Leaves()"><i class="glyphicon glyphicon-refresh" title="Reload">Reload</i></a></h4>
<table cellpadding='0' cellspacing='0' class="table table-bordered col-md-6">
    <thead>
		<tr >
			<th>Site</th>
            <th>Month</th>
            <th>Staff</th>
           
            <th>Callback Requested</th>
				
		</tr>
    </thead>
     <tbody id="leave-stats">  </tbody>
    
</table>

<?php echo jquery_tab_page_close(); ?>


</div>

<?php echo form_close(); ?>

<script>
    function get_Details(){
        $.ajax({
        type: "POST",
        url: SITE+'index.php/admin/staff_call/get_livestate',
        data: '',
        cache: false,
        beforeSend:function(){
            $("#live-stats").
            html('<tr><td colspan="100%" align="center"><img src="'+SITE+'images/loading.gif" /></td></tr>');
        },
        success: function(result) {
            console.log(result);
             $("#live-stats").
            html(result);
        }

    });
    }
   get_Details();
   
   
   function get_Attendance(){
        $.ajax({
        type: "POST",
        url: SITE+'index.php/admin/staff_call/attendance_discrepancey',
        data: '',
        cache: false,
        beforeSend:function(){
            $("#attendance-stats").
            html('<tr><td colspan="100%" align="center"><img src="'+SITE+'images/loading.gif" /></td></tr>');
        },
        success: function(result) {
            console.log(result);
             $("#attendance-stats").
            html(result);
        }

    });
    }
   get_Attendance();
   
   
   function get_Leaves(){
	   var data="leave_type="+$("#leave_type").val();
        $.ajax({
        type: "POST",
        url: SITE+'index.php/admin/staff_call/leave_plane',
        data: data,
        cache: false,
        beforeSend:function(){
            $("#leave-stats").
            html('<tr><td colspan="100%" align="center"><img src="'+SITE+'images/loading.gif" /></td></tr>');
        },
        success: function(result) {
            console.log(result);
             $("#leave-stats").
            html(result);
        }

    });
    }
   get_Leaves();
   
   
   
    
</script>