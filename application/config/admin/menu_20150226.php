<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['items'] = <<<_EOM
Home,
Sites,sites
Staff,staff
Schedule,schedule
	List, schedule
	Assignment, schedule/assignment
Timesheets,home
Reports,home
	Payroll, home
	Timesheet, home
	Blank Page, home/blank
Status,work_status
	Work Status, work_status
	Reply Status, reply_status
Web Site, users
	Users,users
System,home
	Database Backup,dbbackup
	Database Upgrade,dbupgrade
	Control, system_control

_EOM;

$config['base_url'] = 'admin/';

$config['ddsmooth_plugin'] = array(
								'image_folder'=> BASE_URL().'images/admin/ddsmooth_menu',
								'icon_folder'=>BASE_URL().'images/admin/menu_icons',
								'show_icons'=>true,
								'is_vertical_view'=>false,
								'icon_class'=>'ddsmooth_icon'
								);
						
?>
