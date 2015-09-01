<?php echo message_note(); ?>

<div class='content'>
   

    <title></title>
    <meta http-equiv="Content-Language" content="en">
    
	<meta http-equiv="pragma" content="no-cache">
	<meta http-equiv="cache-control" content="no-cache">
	<meta http-equiv="pragma-directive" content="no-cache">
	<meta http-equiv="cache-directive" content="no-cache">
	<meta http-equiv="expires" content="0">		


    <script language="JavaScript" type="text/javascript" src="./home_files/jquery-2.0.3.min.js"></script>
    <script language="JavaScript" type="text/javascript" src="./home_files/jquery-ui-1.9.2.custom.min.js"></script>
    <script language="JavaScript" type="text/javascript" src="./home_files/bootstrap.min.js"></script>
    <script language="JavaScript" type="text/javascript" src="./home_files/ddsmoothmenu.js"></script>
    <link rel="stylesheet" type="text/css" href="./home_files/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="./home_files/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="./home_files/bootstrap-glyphicons.css">
    <link rel="stylesheet" type="text/css" href="./home_files/message_note.css">
    <link rel="stylesheet" type="text/css" href="./home_files/style.css">
    <link rel="stylesheet" type="text/css" href="./home_files/toolbar.css">
    <link rel="stylesheet" type="text/css" href="./home_files/ddsmoothmenu.css">
    <link rel="stylesheet" type="text/css" href="./home_files/jquery_tab.css">
    <link rel="shortcut icon" type="image/x-icon" href="http://fsm.plug-point.com/images/favicon.ico">

    <script language="JavaScript" type="text/javascript" src="./home_files/validate-query.js"></script>


<script type="text/javascript">

ddsmoothmenu.init({
	mainmenuid: "smoothmenu", //menu DIV id
	orientation: 'h', //Horizontal or vertical menu: Set to "h" or "v"
	classname: 'ddsmoothmenu', //class added to menu's outer DIV
//	customtheme: ["#805a4a", "#18374a"],
	contentsource: "markup", // "markup" or ["container_id", "path_to_menu_file"]
	arrowimages: {down:['downarrowclass', 'http://fsm.plug-point.com/images/admin/ddsmooth_menu/down.gif', 23], 
					right:['rightarrowclass', 'http://fsm.plug-point.com/images/admin/ddsmooth_menu/right.gif']
				}

})


</script><script type="text/javascript">
    $("document").ready(function() {
		$("#jquery_tabs").tabs( {
			active: 0, 
			activate: function(event, ui) {
				$(":input[name='_jquery_last_tab_index']").val($("#jquery_tabs").tabs("option", "active"));
			}
		});
	});
</script><script>
            var SITE = 'http://fsm.plug-point.com/';
        </script>

<!--datepicker script-->
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
  <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
  <link rel="stylesheet" href="/resources/demos/style.css">
  <script>
  $(function() {
    $( "#datepicker" ).datepicker();
  });
   $(function() {
    $( "#datepicker1" ).datepicker();
  });
  </script>
    <SCRIPT>
   $(document).ready(function(){
   //alert("hi");
				/*function addMore() {
					$("<DIV>").load("input1.php", function() {
							$("#product").append($(this).html());
					});	
				}*/
				$("#addMore").click(function(){
				 //alert("hi");
				 //$("#product").append($(this).html());
				 $(this).closest("tr").prev("tr").after("<tr id='uuuu'><td><?php //for($i=1;$i<=2;$i++){ echo $i++;}?></td><td><input style='width:100%' placeholder='1 x Senior Security Officer manning from : 8 am to 8 pm (Mondays – Sundays including Public holidays)' type='text' name='item_index[]' value=''/></td><td><input style='width:100%' placeholder='$2,800.00' type='text' name='item_name[]' /></td><td><input style='width:100%' placeholder='$2,800.00' type='text' id='vista1' name='item_price[]' /></td></tr>");
				 <!-- <input style="width:100%" name="rate2" placeholder="$2,800.00" type="text" />-->
				});
				function deleteRow() {
					$('DIV.product-item').each(function(index, item){
						jQuery(':checkbox', this).each(function () {
							if ($(this).is(':checked')) {
								$(item).remove();
							}
						});
					});
				}
	});
</SCRIPT>
<SCRIPT>
  $(document).ready(function(){
   
   	 $("#content1").hide();
   	$("#button_tab_attendance").click(function(){
     //alert("2");
	 	$("#content1").show();
		$("#content2").hide();
	 });
	$("#button_tab_shift").click(function(){
		//alert("1");
		$("#content1").hide();
		$("#content2").show();
	});
	$("#relo").click(function(){
		//alert("123");
		//$("#content1").show();
		//$("#content2").show();
	});
	$("#anc_rem").click(function(){
		$( "#uuuu" ).remove();
 	});
	
   });
</SCRIPT>
</head>
<form action="./home_files/home.html" method="post" accept-charset="utf-8">	
<input type="hidden" name="_jquery_last_tab_index" value="0"><div class="jquery_tab ui-tabs ui-widget ui-widget-content ui-corner-all" id="jquery_tabs"><ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all" role="tablist"><li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active" role="tab" tabindex="0" aria-controls="tab_shift" aria-labelledby="button_tab_shift" aria-selected="true">
<!--<a id="button_tab_shift" href="http://fsm.plug-point.com/index.php/admin/home#tab_shift" class="ui-tabs-anchor" role="presentation" tabindex="-1">Template</a>-->
<a id="button_tab_shift" href="<?php //echo base_url();?>index.php/admin/proposal/insert_data" class="ui-tabs-anchor" role="presentation" tabindex="-1">Template</a>
<!--<a id="button_tab_shift"  class="ui-tabs-anchor" role="presentation" tabindex="-1">Template</a>-->
</li>
<li class="ui-state-default ui-corner-top" role="tab" tabindex="-1" aria-controls="tab_attendance" aria-labelledby="button_tab_attendance" aria-selected="false">
<!--<a id="button_tab_attendance" href="http://fsm.plug-point.com/index.php/admin/home#tab_attendance" class="ui-tabs-anchor" role="presentation" tabindex="-1">Proposal History</a>-->
<!--<a id="button_tab_attendance" href="<?php //echo base_url();?>index.php/admin/proposal/propose_data" class="ui-tabs-anchor" role="presentation" tabindex="-1">Proposal History</a>-->
<a id="button_tab_attendance" href="<?php echo base_url();?>index.php/admin/proposal/show_data1"  class="ui-tabs-anchor" role="presentation" tabindex="-1">Proposal History</a>
</li>
</ul>
<div class="jquery_tab_page ui-tabs-panel ui-widget-content ui-corner-bottom" id="tab_shift" aria-labelledby="button_tab_shift" role="tabpanel" aria-expanded="true" aria-hidden="false" style="display: block;">
<h4><span class="col-lg-11">&nbsp;</span><a href="<?php echo base_url();?>index.php/admin/proposal/insert_data2" onclick="get_Details()"><i class="glyphicon glyphicon-refresh" title="Reload">Reload</i></a></h4>

</div>
<div class="jquery_tab_page ui-tabs-panel ui-widget-content ui-corner-bottom" id="tab_attendance" aria-labelledby="button_tab_attendance" role="tabpanel" aria-expanded="false" aria-hidden="true" style="display: none;">
<h4><span class="col-lg-11">&nbsp;</span><a href="<?php echo base_url();?>index.php/admin/proposal/insert_data2" id="relo" onclick="get_Attendance()"><i class="glyphicon glyphicon-refresh" title="Reload">Reload</i></a></h4>
</div>

</form>


<div id='content1' style="padding-left:5%;padding-bottom:2%">
<form >
<table  width="90%"  border="1" >
  <tr style="background-color:#757575;color:#FFFFFF;">
    <th style="padding-top:2%;padding-top:2%">Ref.No</th>
    <th>Date</th>
    <th>Address</th>
    <th>Name</th>
    <th>Subject</th>
  </tr>
  <?php 
  	$sql ="SELECT * FROM grs_proposal  ORDER BY id DESC";
	$query = $this->db->query($sql);
	if($query->num_rows() > 0) {
	 foreach ($query->result() as $row) {?>
    
     <tr>
        <td><?php echo $row->ref1;?></td>
        <td><?php echo $row->date1;?></td>
        <td><?php echo $row->address1;?></td>
        <td><?php echo $row->name1;?></td>
        <td><?php echo $row->subject1;?></td>
      </tr>
	<?php }}?>
	</table>
</form>
</div>
<div id='content2' style="padding-left:5%">

<form id="ff1" method="post" action="<?php echo base_url();?>index.php/admin/proposal/insert_data"><!--http://localhost/ocs/ocs-->
<table width="90%" border="1">
  <tr>
  	<td>Ref</td>
    <td><input style="width:100%" name="ref1" id="ref1" type="text"  placeholder="MSS/Q201507/000" /></td>
  </tr>
  <tr>
    <td>Date</td>
    <td><input style="width:100%" name="date1" id="datepicker" type="text" placeholder="<?php echo date("m/d/y");?>"/></td>
  </tr>
  <tr>
    <td>Address</td>
    <td><textarea style="width:100%"  name="address1" cols="30" placeholder="Suites at Orchard
C/o Knight Frank Estate Management Pte Ltd
38 Handy Road #01-15
Singapore 229239
  Attn: Ms. Cazeline Chin
" rows="5"></textarea></td>
  </tr>
  <tr>
    <td>Name</td>
    <td><input  style="width:100%" name="name1" type="text" placeholder="Chin"  /></td>
  </tr>
  <tr>
    <td>Subject</td>
    <td><input style="width:100%" name="subject1" type="text" placeholder="PROPOSAL/QUOTATION OF SECURITY SERVICES FOR SUITES AT ORCHARD @ 38 HANDY ROAD" /></td>
  </tr>
  <tr>
    <td>Description</td>
    <td><textarea  style="width:100%" name="desc1" cols="30" placeholder="1.	Thank you for the opportunity to participate in the quotation exercise. We are presently graded “A” by Police Licensing Regulatory Department every year since 2008 up to the present year 2015. Despite this, we are not complacent and are fully aware of the teething issues and constraints in the security industry due to the extreme manpower shortfall. We also strongly believe in getting good marks from our clients." rows="5"></textarea></td>
  </tr>
	<tr>
  <td colspan="2" align="center">
  <table id="tbl1" width="100%" border="1">
   <tr>
    <td>Sn.No</td>
    <td>Service Description1</td>
    <td>Unit Price</td>
    <td>Monthly Rate</td>
  </tr>
  <tr>
    <td>1</td>
    <td style="width:70%">
    <input style ="width:100%" name="service1" placeholder="
    1 x Senior Security Officer manning from : 8 am to 8 pm (Mondays – Sundays including Public holidays)" type="text" />
    </td>
    <td>
    <input style="width:100%" name="price1" placeholder="$3,000.00" type="text" />
    </td>
    <td>
    <input style="width:100%" name="rate1" placeholder="$3,000.00" type="text" />
    </td>
  </tr>
  <tr>
    <td>2</td>
    <td>
    <input style="width:100%" name="service2" placeholder="
    1 x Security Officer manning from : 8 am to 8 pm (Mondays – Sundays including Public holidays)" type="text" />
    </td>
    <td>
    <input style="width:100%" name="price2" placeholder="$3,000.00" type="text" />
    </td>
    <td>
    <input style="width:100%" name="rate2" placeholder="$2,800.00" type="text" />
    </td>
  </tr>
  <tr>
    <td>3</td>
    <td>
    <input style="width:100%" name="service3" placeholder="
    1 x Senior Security Officer manning from : 8 pm to 8 am (Mondays – Sundays including Public holidays)" type="text" />
    </td>
    <td>
    <input style="width:100%" name="price3" placeholder="$2,800.00" type="text" />
    </td>
    <td>
    <input style="width:100%" name="rate3" placeholder="$3,000.00" type="text" />
    </td>
  </tr>
 
  <tr><td id="product"><?php require_once("input1.php") ?></td></tr>
   <tr><td colspan="4">
 
  <input type="button" name="add_item" id="addMore" value="Add Row" />
	<input type="button" href="javascript:void(0);" id='anc_rem' name="del_item" value="Delete Row" onClick="deleteRow();" />
  </td></tr>
</table>

  
  </td>
  </tr>
  <tr>
    <td>Note1</td>
    <td><input style="width:100%" name="note1" placeholder="Price Quoted is preliminary and subject to GST Charges." type="text" /></td>
  </tr>
  <tr>
    <td>Note2</td>
    <td><input style="width:100%" name="note2" placeholder="We need an advance notice of 35 days to start the project" type="text" /></td>
  </tr>
  <tr>
    <td>Note3</td>
    <td><input style="width:100%" name="note3" placeholder="The validity of the quotation is one month." type="text" /></td>
  </tr>
  <tr>
    <td>Note4</td>
    <td><input style="width:100%" name="note4" placeholder="Payment terms net 30 days." type="text" /></td>
  </tr>
  <tr>
    <td>Note5</td>
    <td><input style="width:100%" name="note5" placeholder="Above pricing includes provision of Guard Tour Systems & 6sets of Walkie-Talkie" type="text" /></td>
  </tr>
  <tr>
    <td>Note6</td>
    <td><input style="width:100%"  name="note6" placeholder="Please kindly note that in Sep 2016, the PWM implementation shall enforced. Hence, the job scope of the rank Security Officer shall be prescribed as access control, basic incident response, general screening and guarding and patrolling." type="text" /></td>
  </tr>
   <tr>
    <td>3.	Why You Should Consider Us</td>
    <td><textarea style="width:100%" name="whyyou" placeholder="If selected, I am confident that my team can respond to your needs more promptly and effectively. These attributes become more relevant and important if any responsible agency wish to tackle the related problems in manpower starved industry. The following are the attributes" cols="30" rows="5"></textarea></td>
  </tr>
  <tr>
    <td> Organizational Structure</td>
    <td><textarea style="width:100%" name="stu" cols="30" placeholder="Our management team consists of the Director of Operations, Operation Managers, Assistant Operation Managers, Operation Executive, Quality and Compliance Executive, Recruitment Executive and 7 Operations Centre crew. In the Headquarters, there are 7 staffs handling the HR, admin and payroll matters. In this way, the security officers on the ground have adequate communications links to address their operational, logistics, administrative or pay matters. Our teams worked seamlessly round the clock as security is a 24 hour business." rows="5"></textarea></td>
  </tr>
  <tr>
    <td> b.	Location and accountability of HR/Payroll teams and Ops teams</td>
    <td><textarea style="width:100%" name="loca" cols="30" placeholder="The 24 hours Ops team is stationed at the Operations Office at Paya Lebar.  The HR/Payroll department is stationed at the Head Office at Sin Ming. The Ops team and the HR/Payroll department exclusively monitor, verify and ensure the attendance of the security staff. The advantages are :" rows="5"></textarea></td>
  </tr>
   <tr>
    <td> b.	Site inspections conducted by 24 Hours Operations Centre crew</td>
    <td><textarea style="width:100%" name="site" cols="30" placeholder=".  The Operations centre runs on a 24 hour basis. Site inspections are conducted regularly so as to ensure that the day and the night security teams are vigilant and non-complacent. Site visit reports are sent to the clients when needed." rows="5"></textarea></td>
  </tr>
   <tr>
    <td colspan="2"> d.	High Tech Resources</td>
    
  </tr>
  <tr>
    <td>i)	Company Vans</td>
    <td><textarea style="width:100%" name="company" placeholder=". We have company vehicles so as to respond to any operational and logistics needs swiftly. " cols="30" rows="5"></textarea></td>
  </tr>
   <tr>
    <td>ii)	Mobile apps and Email on the go</td>
    <td><textarea style="width:100%" name="mobile" cols="30" placeholder=". All Executives are equipped with data plan and email accounts and are equipped with smartphone. This enables them to use the latest technological tools to stay on top of the operational issues or respond to clients while on the go." rows="5"></textarea></td>
  </tr>
  <tr>
    <td>iii) GPS Tracking</td>
    <td><textarea style="width:100%" name="gps" cols="30" placeholder=". All company vans are tracked with GPS to be on top of situations. This helps the ops room manage resources more efficiently and effectively. Operational movements become more transparent." rows="5"></textarea></td>
  </tr>
  <tr>
    <td>iv)	High Tech guardroom</td>
    <td><textarea style="width:100%" name="hightechguard" cols="30" placeholder=".  We have the resources to equip the guardroom with the relevant to so as to tap on the technology to improve productivity. For example, Visitor Management Software System. CCTV cameras, computer with communications tools." rows="5"></textarea></td>
  </tr>
   <tr>
    <td>e.	Metropolis Welfare</td>
    <td><textarea style="width:100%" name="metropolis" cols="30" placeholder="To give the security officers a peace of mind when executing their duties, the Metropolis management will actively manage and take care of the welfare of the guards. At Metropolis, we provide the guard with : " rows="5"></textarea></td>
  </tr>
   <tr>
    <td>guard with1</td>
    <td><input style="width:100%"  name="guardwith1" placeholder="i)NTUC voucher reward schemes for good performers" type="text" /></td>
  </tr>
   <tr>
    <td>guard with2</td>
    <td><input style="width:100%"  name="guardwith2" placeholder="ii)Beverages and snacks (on a monthly basis). " type="text" /></td>
  </tr>
   <tr>
    <td>guard with3</td>
     <td><input style="width:100%"  name="guardwith3" placeholder="iii)Cash advances and loans as and when needed." type="text" /></td>
  </tr>
   <tr>
    <td>guard with4</td>
     <td><input style="width:100%" name="guardwith4" placeholder="iv)At least 4 times salary payment per month." type="text" /></td>
  </tr>
   <tr>
    <td>guard with5</td>
     <td><input style="width:100%" name="guardwith5" placeholder="v)Salary payment at the guard's premises if he has no bank account." type="text" /></td>
  </tr>
   <tr>
    <td>guard with6</td>
    <td><input style="width:100%" name="guardwith6" placeholder="vi)4 or more Off days every month." type="text" /></td>
  </tr>
   <tr>
    <td>guard with7</td>
     <td><input style="width:100%" name="guardwith7" placeholder="vii)Operational backup should the guard require any urgent assistance." type="text" /></td>
  </tr>
   <tr>
    <td>guard with8</td>
     <td><input style="width:100%" name="guardwith8" placeholder="viii)Medical claims for consultancy or full hospitalization leave." type="text" /></td>
  </tr>
   <tr>
    <td>Equipment1</td>
   <td><input style="width:100%" name="equipment1" placeholder="i)Proper nametags bearing the Contractor’s name followed by the personnel’s name." type="text" /></td>
  </tr>
   <tr>
    <td>Equipment2</td>
   <td><input style="width:100%" name="equipment2" placeholder="ii)Standard Uniform with company logo and raincoats will also be issued." type="text" /></td>
  </tr>
   <tr>
    <td>Equipment3</td>
    <td><input style="width:100%"  name="equipment3" placeholder="iii)Powerful Rechargeable LED torchlight." type="text" /></td>
  </tr>
   <tr>
    <td>Equipment4</td>
    <td><input style="width:100%" name="equipment4" placeholder="iv)Contractor Log book and Occurrence book to record any incidents and contractors' particulars on a daily basis." type="text" /></td>
  </tr>
   <tr>
    <td>Equipment5</td>
   <td><input style="width:100%" name="equipment5" placeholder="v)Attendance Record book to record the attendance of the guard and his relief guard." type="text" /></td>
  </tr>
   <tr>
    <td>Equipment6</td>
   <td><input style="width:100%" name="equipment6" placeholder="vi)Operations booklet for essential telephone numbers, operational procedures that is customized for your prestigious estate" type="text" /></td>
  </tr>
   <tr>
    <td>Equipment7</td>
    <td><input style="width:100%" style="width:100%" name="equipment7" placeholder="vii)Optional walkie-talkies, guard tour systems, visitor management systems, camera systems and time recording devices at the request of our clients." type="text" /></td>
  </tr>
  <tr>
    <td>g.Workmen Compensation and Public Liability Insurance</td>
    <td><textarea style="width:100%" name="workmen" placeholder=". For the welfare of our workers, all our workers are protected by workmen compensation. For peace of mind for my clients and myself, Metropolis is covered by Public Liability Insurance of up to SGD 2 Million." cols="30" rows="5"></textarea></td>
  </tr>
  <tr>
    <td>i)Police Licensing Regulatory Department</td>
    <td><textarea style="width:100%" name="police1" cols="30" placeholder="All the deployed Security Officers are pre-approved and screened with PLRD and we comply fully with the Private Investigation and Security Industry Act." rows="5"></textarea></td>
  </tr>
   <tr>
    <td>ii)	Ministry of Manpower Exemption from Overtime</td>
    <td><textarea style="width:100%" name="ministry1" placeholder="As required by the law, we have successfully applied for the OT Exemption order from Ministry of Manpower and we have worked closely with MOM to ensure full compliance." cols="30" rows="5"></textarea></td>
  </tr>
   <tr>
    <td>i.Professional Affiliation</td>
    <td><textarea style="width:100%" name="professional" placeholder="As an established and responsible player in the security industry, Metropolis is and Executive Committee Member of Association of Certified Security Agencies (ACSA), professionally affiliated with Asian Professional Security Association (APSA) and is also a Corporate Member of Security Association of Singapore (SAS)." cols="30" rows="5"></textarea></td>
  </tr>
  <tr>
   <tr>
    <td>4.last condition</td>
    <td><textarea style="width:100%" name="last" placeholder="4.	Our team will listen more to your needs and respond more effectively so as to get things done. Despite our track record of achieving many years of ‘A’ or Excellent grading by the regulatory body, we are maintaining a small pool of clientele as we understand the need to stay focused when security manpower are lacking. This will help us focus our limited resources to and do whatever is necessary to ensure every new project is successful. Attached are some updated brochures and testimonials that I have received from my clients. I look forward to be your humble partner in the provision of security services at your prestigious estate." cols="30" rows="5"></textarea></td>
  </tr>
   <tr>
    <td>LName</td>
    <td><input style="width:100%" name="lname" placeholder="James Soh*" type="text" /></td>
  </tr>
   <tr>
    <td>Signature</td>
    <td><input style="width:100%" name="signature" placeholder="" type="text" /></td>
  </tr>
   <tr>
    <td>Date</td>
    <td><input style="width:100%" name="date2"  placeholder="<?php echo date("m/d/y");?>" id="datepicker1"  type="text" /></td>
  </tr>
 	
   <tr>
  <td colspan="2" align="center"><input name="save" value="Save" type="submit" /></td>
  </tr>
  <tr>
 
</table>
</form>
</div>
</div>