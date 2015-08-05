<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

$config['items'] = <<<_EOM
Dashboard,
Sites,sites
Staff,staff
Roster,schedule/assignment
	Calendar View,schedule/assignment
	List View,schedule
SOP,duty
Finance,home/staff
	Payroll,timesheets
	Payslip,home/staff
	Invoices,home/sites
Reports,timesheets
	Timesheet,timesheets
Utilities,sitemap
	Broadcast SMS,send_sms
	Site Map,sitemap
Control Panel,home
	Twilio Configuration,twilio
	SOP Configuration,duty/sop_config	
	PLRD Active Staff,staff/staff_config
	Export Site Info,sites/export
	Export Staff Info,staff/export
	Export Site/Staff Assignment,staff/exportStaffAssignment
	Import Sites,sites/import_sites
	Import Staff,staff/import_staff
Quality Control,home/qc

_EOM;

$config['base_url'] = 'admin/';

$config['ddsmooth_plugin'] = array(
    'image_folder' => BASE_URL() . 'images/admin/ddsmooth_menu',
    'icon_folder' => BASE_URL() . 'images/admin/menu_icons',
    'show_icons' => true,
    'is_vertical_view' => false,
    'icon_class' => 'ddsmooth_icon'
);
?>
