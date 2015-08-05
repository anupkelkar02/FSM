<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html >
<head>
    <title><?php echo $title; ?></title>
    <meta http-equiv="Content-Language" content="<?php echo isset($lang) ? $lang : 'en';?>" />
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $charset; ?>" />
	<meta http-equiv="pragma" content="no-cache">
	<meta http-equiv="cache-control" content="no-cache">
	<meta http-equiv="pragma-directive" content="no-cache">
	<meta http-equiv="cache-directive" content="no-cache">
	<meta http-equiv="expires" content="0">		

<?php foreach($meta as $name=>$content): ?>
    <meta name="<?php echo $name; ?>" content="<?php echo $content; ?>" />
<?php endforeach; ?>
<?php if(count($rdf) > 0): ?>
<!--
    <rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:ddc="http://purl.org/net/ddc#">
        <rdf:Description rdf:about="<?php echo base_url(); ?>">
<?php foreach($rdf as $name=>$content): ?>
            <<?php echo $name; ?>><?php echo $content; ?></<?php echo $name; ?>>
<?php endforeach;?>
        </rdf:Description>
    </rdf:RDF>
-->
<?php endif;?>

<?php foreach($javascript as $javascript_file): ?>
    <script language="JavaScript" type="text/javascript" src="<?php echo $javascript_file;?>"></script>
<?php endforeach; ?>
<?php foreach ($css as $css_file): ?>
    <link rel="stylesheet" type="text/css" href="<?php echo $css_file; ?>" >
<?php endforeach; ?>
<?php if (isset($fav_icon)) :?>
    <link rel='shortcut icon' type="image/x-icon" href='<?php echo base_url($fav_icon); ?>' >
<?php endif; ?>

<?php  echo $inline_scripting ?><script>
            var SITE = '<?php echo base_url(); ?>';
        </script>


</head>
<body>


<div class='header'>
	<a href='<?php echo base_url();?>'><div class='header_title'><?php echo $this->config->item('title');?></div></a>
</div>
<div class='menubar'>
<div id="smoothmenu" class="ddsmoothmenu">
        <ul>
            <li><a href="<?php echo base_url();?>index.php/admin">Dashboard</a></li>
            <li style="z-index: 100;"><a href="#" style="padding-right: 23px;">HR/Admin<img src="<?php echo base_url();?>images/admin/ddsmooth_menu/down.gif" class="downarrowclass" style="border:0px;" /></a>
                <ul >
                   <li><a href="<?php echo base_url();?>index.php/admin/staff">Staff</a></li>
                    <li><a href="<?php echo base_url();?>index.php/admin/sites">Sites</a></li>
                    <li><a href="<?php echo base_url();?>index.php/admin/staff/export">Export Staff</a></li>
                    <li><a href="<?php echo base_url();?>index.php/admin/sites/export">Export Sites</a></li>
                    <li><a href="<?php echo base_url();?>index.php/admin/staff/import_staff">Import Staff</a></li>
                    <li><a href="<?php echo base_url();?>index.php/admin/sites/import_sites">Import Sites</a></li>
                </ul>
            </li>
            <li style="z-index: 99;"><a href="#" class="" style="padding-right: 23px;">Operations<img src="<?php echo base_url();?>images/admin/ddsmooth_menu/down.gif" class="downarrowclass" style="border:0px;" /></a>
                <ul style="top: 35px; box-shadow: rgb(170, 170, 170) 5px 5px 5px; -webkit-box-shadow: rgb(170, 170, 170) 5px 5px 5px; visibility: visible; left: 0px; width: 160px; display: none;">
                    <li><a href="#">Roster</a>
                        <ul>
                            <li><a href="<?php echo base_url();?>index.php/admin/schedule/assignment">Calendar View</a></li>
                            <li><a href="<?php echo base_url();?>index.php/admin/schedule">List View</a></li>
                        </ul></li>
<li><a href="<?php echo base_url();?>index.php/admin/attendance/import">Attendance</a></li>
                    <li><a href="<?php echo base_url();?>index.php/admin/duty">SOP</a></li>
                    <li style="z-index: 94;"><a href="#" style="padding-right: 23px;">Reports</a>
                <ul style="display: none; top: 35px; box-shadow: rgb(170, 170, 170) 5px 5px 5px; -webkit-box-shadow: rgb(170, 170, 170) 5px 5px 5px; visibility: visible;">
                    <li><a href="<?php echo base_url();?>index.php/admin/timesheets">Timesheet</a></li>
	<li><a href="<?php echo base_url();?>index.php/admin/staff/exportStaffAssignment">Export Staff Schedule</a></li>                
</ul>
            </li>
                    </ul>
            </li>
            <li style="z-index: 95;"><a href="#" style="padding-right: 23px;">Finance<img src="<?php echo base_url();?>images/admin/ddsmooth_menu/down.gif" class="downarrowclass" style="border:0px;" /></a>
                <ul style="display: none; top: 35px; box-shadow: rgb(170, 170, 170) 5px 5px 5px; -webkit-box-shadow: rgb(170, 170, 170) 5px 5px 5px; visibility: visible;">
                    <li><a href="#" onclick="alert('Payroll is under developement')">Payroll</a></li>
                    <li><a href="#" onclick="alert('Payslip is under developement')">Payslip</a></li>
                    <li><a href="#" onclick="alert('Invoices is under developement')">Invoices</a></li>
                </ul>
            </li>
            <li style="z-index: 95;"><a href="#" style="padding-right: 23px;">Logistics<img src="<?php echo base_url();?>images/admin/ddsmooth_menu/down.gif" class="downarrowclass" style="border:0px;" /></a>
                <ul style="display: none; top: 35px; box-shadow: rgb(170, 170, 170) 5px 5px 5px; -webkit-box-shadow: rgb(170, 170, 170) 5px 5px 5px; visibility: visible;">
                    <li><a href="#" onclick="alert('Uniform is under developement')">Uniform</a></li>
                    <li><a href="#" onclick="alert('Inventory is under developement')">Inventory</a></li>
                    
                </ul>
            </li>
<li><a href="#">Sales</a><img src="<?php echo base_url();?>images/admin/ddsmooth_menu/down.gif" class="downarrowclass" style="border:0px;" />
                <ul><li><a href="<?php echo base_url();?>index.php/admin/home/camp">Campaigns</a></li><li><a href="<?php echo base_url();?>index.php/admin/home/crm">CRM</a></li></ul>
                
            </li>
<li><a href="<?php echo base_url();?>index.php/admin/home/qc">Quality Control</a></li>
            <li style="z-index: 93;"><a href="#" style="padding-right: 23px;">Utilities<img src="<?php echo base_url();?>images/admin/ddsmooth_menu/down.gif" class="downarrowclass" style="border:0px;" /></a>
                <ul style="display: none; top: 35px; box-shadow: rgb(170, 170, 170) 5px 5px 5px; -webkit-box-shadow: rgb(170, 170, 170) 5px 5px 5px; visibility: visible;">
                    <li><a href="<?php echo base_url();?>index.php/admin/send_sms">Announcements</a></li>
                    <li><a href="<?php echo base_url();?>index.php/admin/sitemap">Site Map</a></li>
                </ul>
            </li>
            <li style="z-index: 92;"><a href="#" style="padding-right: 23px;">Control Panel<img src="<?php echo base_url();?>images/admin/ddsmooth_menu/down.gif" class="downarrowclass" style="border:0px;" /></a>
                <ul style="display: none; top: 35px; box-shadow: rgb(170, 170, 170) 5px 5px 5px; -webkit-box-shadow: rgb(170, 170, 170) 5px 5px 5px; visibility: visible;">
	<li><a href="<?php echo base_url();?>index.php/admin/systemconf">System&nbsp;Configuration</a></li>                    
	<li><a href="<?php echo base_url();?>index.php/admin/twilio">Twilio Configuration</a></li>
                    <li><a href="<?php echo base_url();?>index.php/admin/duty/sop_config">SOP Configuration</a></li>
                    <li><a href="<?php echo base_url();?>index.php/admin/staff/staff_config">PLRD Active Staff</a></li>
                   <li><a href="<?php echo base_url();?>index.php/admin/home/bday_template">Birthday Greeting</a></li>
                </ul>
            </li>
            
        </ul></div>
<?php //echo $this->menu->show(); ?></div>
<div class='menu_info'><?php echo $user->username.':&nbsp;'.anchor(site_url('admin/logout'), 'Logout');?></div>
<div class='main_page'><div class='page'><?php echo $output; ?></div></div>





</body>
</html>
