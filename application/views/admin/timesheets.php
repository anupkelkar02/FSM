<?php
	if(!isset($monthName))
		$monthName=date('t',strtotime('Y-m'));
	
	$nDaysofMonth = date('t',strtotime($monthName));
?>
<script type="text/javascript" src="<?php echo base_url();?>js/tableExport.js" ></script>
<script type="text/javascript" src="<?php echo base_url();?>js/jquery.base64.js" ></script>
            <script type="text/javascript" src="<?php echo base_url();?>js/jspdf/libs/sprintf.js" ></script>
<script type="text/javascript" src="<?php echo base_url();?>js/jspdf/jspdf.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>js/jspdf/libs/base64.js"></script>
<script language="javascript">
var SITEURL="<?php echo base_url();?>";
$(document).ready(function(){
    $("select#siteNames").change(function(){
        var selectedSites = $('#siteNames').val();
		//var getSitesData = $('#siteNames').serialize()
		
		$.ajax({
			type:"GET",
			async: false,
			//data: {getSitesData,getSitesData},
			data: selectedSites,
			url: SITEURL+"index.php/admin/timesheets/getSiteStaffs/"+selectedSites,
			success: function(msg) {
				document.getElementById("staffNames").innerHTML=msg;
			}
		});	
    });
});
</script>
<?php  
echo toolbar_open('Timesheets');
echo toolbar_close();
?>
<div class='content' style="overflow:auto;">
	<fieldset class='filter'>
	<legend>Filter</legend>
		<?php
		$attributes = array('name'=>'frmTimesheets','id'=>'frmTimesheets');
		echo form_open('admin/timesheets',$attributes);
		?>
			<table cellpadding='0' cellspacing='0' class='filter'>
				<tr>
					<th>Sites</th>
					<th>Staffs</th>
					<th>Month</th>
					<th>&nbsp;</th>
				</tr>
				<tr>
					<td>
						<?php
						if(!empty($this->session->userdata['searchData']['siteName']))
								$sSiteName = $this->session->userdata['searchData']['siteName'];
						?>
						<select name="siteNames" id="siteNames">
							<option value="0">--Select--</option>
							<?php
								foreach($sitesData as $sIndex=>$siteNames) {
									if($sSiteName==$sIndex)
										echo '<option value="'.$sIndex.'" selected="selected">'.$siteNames.'</option>';
									else
										echo '<option value="'.$sIndex.'">'.$siteNames.'</option>';	
								}
							?>
						</select>		
					</td>
					<td>
						<?php
						if(!empty($this->session->userdata['searchData']['staffName']))
								$sStaffName = $this->session->userdata['searchData']['staffName'];
						?>					
						<select name="staffNames" id="staffNames">
							<option value="">--Select--</option>
							<?php
								foreach($staffsData as $stIndex=>$staffNames) {
									if($sStaffName==$stIndex)
										echo '<option value="'.$stIndex.'" selected="selected">'.$staffNames.'</option>';
									else
										echo '<option value="'.$stIndex.'">'.$staffNames.'</option>';	
								}
							?>        
						</select>		
					</td>	
					<td>
				<?php
					if(!empty($this->session->userdata['searchData']['monthName']))
							$sMonthName = $this->session->userdata['searchData']['monthName'];
												
					$cMonth=date('Y-m');
					echo '<select name="getMonths">';
						echo '<option value="'.$cMonth.'">'.date('M Y').'</option>';
						for($i=1;$i<=5;$i++) {
							$monthID="-$i month";
							$monthString=date('M Y',strtotime($monthID,strtotime($cMonth)));
							$yearMonth=date('Y-m',strtotime($monthID,strtotime($cMonth)));
							if($sMonthName==$yearMonth) {
								echo '<option value="'.date('Y-m',strtotime($monthID,strtotime($cMonth))).'" selected="selected">'.$monthString.'</option>';
							} else {
								echo '<option value="'.date('Y-m',strtotime($monthID,strtotime($cMonth))).'">'.$monthString.'</option>';							
							}
						}
					echo '</select>';
				?>		
					</td>
					<td>
						<input type="submit" name="frmSearch" value="Search" />
					</td>
				</tr>
			</table>
		<?php echo form_close();?>
	</fieldset>
    <input type="button" class="btn btn-info right_panel" value="Export to Excel" style="float: right" onclick="$('#tableID').tableExport({type:'excel',escape:'false'});">
    <div style="clear: both">&nbsp;</div>
	<table cellpadding='0' cellspacing='0' class="DataRows" id="tableID">
            <thead>
		<tr>
			<th width="5%">Site</th>
			<th>Staff</th>
			<th>Shift Type</th>
			<th>Job Type</th>
			<?php
				for($m=1;$m<=$nDaysofMonth;$m++) {
					echo'<th>'.$m.'</th>';
				}
			?>		
		</tr>
            </thead>
            <tbody>
		<?php
			if(empty($staffAssignementData)) {
                            $colspan = $nDaysofMonth+3;
		?>
		<tr>
			<td colspan="<?php echo $colspan;?>" >There is no data to display!</td>
		</tr>
		<?php 
			} else { 
				foreach($staffAssignementData as $tIndex=>$timeData) {
		?>
		<tr>
			<td><?php echo $timeData->site_id;?></td>
			<td><?php echo $timeData->staff_id;?></td>
			<td><?php echo $timeData->shift_type;?></td>
			<td><?php echo $timeData->assign_type;?></td>
			<?php
				$arrSearchTimesheet["monthName"]=$monthName;
				$arrSearchTimesheet["siteName"]=$timeData->site_id;
				$arrSearchTimesheet["staffName"]=$timeData->staff_id;
				$getTimesheetData=$this->timesheet_model->getTimesheetData($arrSearchTimesheet);

				$sno="1";
				for($t=0;$t<$nDaysofMonth;$t++) {
					if(isset($getTimesheetData[$t])) {
						$cDate = date('j',strtotime($getTimesheetData[$t]->start_date));
						$bgColor=$getTimesheetData[$t]->background_color;
						$textColor=$getTimesheetData[$t]->text_color;
                                               // echo 'srno='.$sno.';;cdate='.$cDate.'<br/>';
						if($getTimesheetData[$t]->reply_status_id=='1') {
							echo'<td style="background-color:'.$bgColor.';color:'.$textColor.'">'.$getTimesheetData[$t]->code.'</td>';
						} else {
							echo'<td style="align:center">X</td>';
						}
					} else {
						echo'<td style="align:center">X</td>';
					}
						
					$sno++;
				}
			?>														
		</tr>
		<?php 
				}
			} 
		?>
            </tbody>
	</table>
	<div class='container text-center'><?php //echo $pagination_links; ?></div>	
</div>