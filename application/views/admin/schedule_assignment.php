<?php 

function convertDateToJSDate($dateText)
{
	return 'new Date('.substr($dateText, 0, 4).','.(substr($dateText, 5, 2) - 1).','.substr($dateText, 8, 2).')';
}

function convertDateTimeToJSDate($dateTimeText)
{
	if ( $dateTimeText == '0000-00-00 00:00:00' ) {
		return 'null';
	}
	
	$values = array( intval(substr($dateTimeText, 0, 4)),
					(substr($dateTimeText, 5, 2) - 1),
					intval(substr($dateTimeText, 8, 2)),
					intval(substr($dateTimeText, 11, 2)),
					intval(substr($dateTimeText, 14, 2)),
					intval(substr($dateTimeText, 17, 2))
				);
					
	return ' new Date('.implode(', ', $values).')';
}

function convertColorToJSColor($color)
{
	if ( preg_match('/^#/', $color ) ) {
		return $color;
	}
	return '#'.$color;
}

?>

<script type="text/javascript">
	
	var staffRows = new Array(<?php
	$result = array();
	foreach ( $staff_rows as $staff_row ) {
		$result[] = '{'
						. 'title: "'.$staff_row->first_name.' '.$staff_row->last_name.'",'
						. 'assignType: "'.$staff_row->assign_type.'",'
						. 'phone_number: "'.$staff_row->phone_number.'", '
						. 'staffId: '.$staff_row->id.''
						. '}';
	}
	echo implode(', ', $result);
	?>);
	
	
	var scheduleRows = new Array(<?php
	
	$result = array();
	foreach ( $schedule_rows as $schedule_row ) {
		$result[] = '{'
						. 'scheduleId: '.$schedule_row->id.','
						. 'staffId: '.$schedule_row->staff_id.','
						. 'startDate: '.convertDateToJSDate($schedule_row->start_date, 0, 4).','
						. 'shiftType: "'.$schedule_row->shift_type.'",'
						. 'workStatusId: '.$schedule_row->work_status_id.','
						. 'replyStatusId: '.$schedule_row->reply_status_id.','
						. 'attendanceRequestTime: '.convertDateTimeToJSDate($schedule_row->attendance_request_time).''
						. '}';
	}
	echo implode(', ', $result);
	?>);

	var workStatusRows = new Array(<?php
	
	$result = array();
	foreach ( $work_status_rows as $work_status_row ) {
		$result[] = '{'
						. 'workStatusId: '.$work_status_row->id.','
						. 'code: "'.$work_status_row->code.'",'
						. 'title: "'.$work_status_row->title.'",'
						. 'textColor: "'.convertColorToJSColor($work_status_row->text_color).'",'
						. 'backgroundColor: "'.convertColorToJSColor($work_status_row->background_color).'"'
						. '}';
	}
	echo implode(', ', $result);
	?>);


	var replyStatusRows = new Array(<?php
	
	$result = array();
	foreach ( $reply_status_rows as $reply_status_row ) {
		$result[] = '{'
						. 'replyStatusId: '.$reply_status_row->id.','
						. 'code: "'.$reply_status_row->code.'",'
						. 'title: "'.$reply_status_row->title.'",'
						. 'backgroundColor: "'.convertColorToJSColor($reply_status_row->background_color).'"'
						. '}';
	}
	echo implode(', ', $result);
	?>);



    $("document").ready(function() {
		var params = {'onScheduleChange': function ( scheduleId, workStatusId ) {
				$.ajax({ type: "POST", 
							url: 'update_row',
				  			data: {'schedule_id': scheduleId, 'work_status_id': workStatusId}
				});
			},
			'onScheduleAdd': function ( staffId, startDate, workStatusId ) {
				return $.ajax({ type: "POST", 
							url: 'add_row',
	
				  			data: {'staff_id': staffId, 'site_id': <?php echo $site_id; ?>,
										'start_date': (1900 + startDate.getYear()) + '-' + (startDate.getMonth() + 1) + '-' + startDate.getDate(), 'work_status_id': workStatusId},
				  			async: false
				  		}).responseText;
			},
			'onScheduleDelete': function ( scheduleId ) {
				$.ajax({ type: "POST", 
							url: 'delete_row',
				  			data: {'schedule_id': scheduleId}
				});
			},
			'onCallClick': function (scheduleId,send_pref ) {
				$.ajax({ type: "POST", 
							url: 'request_attendance',
				  			data: {'schedule_id': scheduleId,'send_pref':send_pref},
success:function(res){
                                                           // alert(res);
                                                            $('#msg').html('<div class="alert alert-success">SMS Sent.</div>');
                                                        }
				});
			}
                        ,
			'onSendCallClick': function (scheduleId,send_pref ) {
				$.ajax({ type: "POST", 
							url: 'request_attendance',
				  			data: {'schedule_id': scheduleId,'send_pref':send_pref},
                                                        success:function(res){
                                                           // alert(res);
                                                            $('#msg').html('<div class="alert alert-success">Call is initiated</div>');
                                                        }
				});
			}
			
		};
		var staffAssignment = StaffAssignment("staff_assignment", staffRows, scheduleRows, workStatusRows, replyStatusRows, params);
		$("#staff_assignment").keypress( function (event) {
			console.log('keypress');
			console.log(staffAssignment);
			staffAssignment.onKeyPress(event);
		});

	});

</script>	

<?php echo form_open();?>
<div class='content'>

Client: <?php echo form_dropdown('site_id', $site_list, $site_id, 'onchange="this.form.submit();"');?>
&nbsp;Date:<?php echo form_dropdown('start_date', $month_year_list, $start_date,  'onchange="this.form.submit();"');?>
<div id="msg"></div>
<div id='staff_assignment' style='padding-top: 1em;' width="900" height="500"></div></div>

</div>
<?php echo form_close();?>
