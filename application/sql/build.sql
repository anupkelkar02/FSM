# The Build 

--
-- Table structure for table `grs_site`
--

DROP TABLE IF EXISTS `grs_client`;


DROP TABLE IF EXISTS `grs_site`;

CREATE TABLE `grs_site` (
  `id` int(11) NOT NULL AUTO_INCREMENT, 
  name varchar(250) NOT NULL default '',
  street_number varchar(20) NOT NULL default '',
  street_name varchar(250) NOT NULL default '',
  unit_number varchar(40) NOT NULL default '',
  city varchar(250) NOT NULL default '',
  postcode varchar(20) NOT NULL default '',
  country varchar(250) NOT NULL default '',
  is_published enum('True', 'False') NOT NULL default 'True',  
  `update_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',  
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



--
-- Table structure for table `grs_site_shift`
--

DROP TABLE IF EXISTS `grs_client_shift`;

DROP TABLE IF EXISTS `grs_site_shift`;

CREATE TABLE `grs_site_shift` (
  `id` int(11) NOT NULL AUTO_INCREMENT, 
  site_id int(11) NOT NULL default 0,
  shift_type enum('Day', 'Night') NOT NULL default 'Day',
  start_time time NOT NULL default '00:00:00',
  end_time time NOT NULL default '00:00:00',
  staff_count int NOT NULL default 0,
  max_relief_count int NOT NULL default 0,
  is_published enum('True', 'False') NOT NULL default 'True',  
  `update_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



--
-- Table structure for table `grs_login_session`
--

DROP TABLE IF EXISTS `grs_login_session`;

CREATE TABLE `grs_login_session` (
  `id` int(11) NOT NULL AUTO_INCREMENT, 
  session_value varchar(120) NOT NULL default '',
  link_id int(11) NOT NULL default 0,
  link_key varchar(250) NOT NULL default '',
  expire_time datetime NOT NULL default '0000-00-00 00:00:00',
  `update_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


--
-- Table structure for table `grs_staff`
--

DROP TABLE IF EXISTS `grs_guard`;

DROP TABLE IF EXISTS `grs_staff`;

CREATE TABLE `grs_staff` (
  `id` int(11) NOT NULL AUTO_INCREMENT, 
  first_name varchar(250) NOT NULL default '',
  last_name varchar(250) NOT NULL default '',
  address TEXT NOT NULL default '',
  phone_number varchar(120) NOT NULL default '',
  email varchar(250) NOT NULL default '',
  fin_number varchar(120) NOT NULL default '',
  call_type enum('None', 'SMS', 'Voice') NOT NULL default 'None',
  call_minutes int NOT NULL default 10,
  is_published enum('True', 'False') NOT NULL default 'True',  
  `update_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


--
-- Table structure for table `grs_staff_attendance`
--


DROP TABLE IF EXISTS `grs_staff_attendance`;

CREATE TABLE `grs_staff_attendance` (
  `id` int(11) NOT NULL AUTO_INCREMENT, 
  staff_id int(11) NOT NULL default 0,
  site_id int(11) NOT NULL default 0,
  assign_type enum('FullTime', 'Relief') NOT NULL default 'FullTime',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


--
-- Table structure for table `grs_staff_assignment`
--


DROP TABLE IF EXISTS `grs_guard_assignment`;
DROP TABLE IF EXISTS `grs_staff_assignment`;

CREATE TABLE `grs_staff_assignment` (
  `id` int(11) NOT NULL AUTO_INCREMENT, 
  staff_id int(11) NOT NULL default 0,
  site_id int(11) NOT NULL default 0,
  assign_type enum('FullTime', 'Relief') NOT NULL default 'FullTime',
  shift_type enum('Day', 'Night') NOT NULL default 'Day',
  off_day_names varchar(120) NOT NULL default '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


--
-- Table structure for table `grs_guard_shift`
--


DROP TABLE IF EXISTS `grs_guard_shift`;

DROP TABLE IF EXISTS `grs_schedule`;


CREATE TABLE `grs_schedule` (
  `id` int(11) NOT NULL AUTO_INCREMENT, 
  staff_id int(11) NOT NULL default 0,
  site_id int(11) NOT NULL default 0,
  start_date date NOT NULL default '0000-00-00',
  shift_type enum('Day', 'Night') NOT NULL default 'Day',
  attendance_request_time datetime NOT NULL default '0000-00-00 00:00:00',
  work_status_id int NOT NULL default 0,
  reply_status_id int NULL default 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


--
-- Table structure for table `grs_session`
--

DROP TABLE IF EXISTS `grs_session`;


CREATE TABLE IF NOT EXISTS  `grs_session` (
	session_id varchar(40) DEFAULT '0' NOT NULL,
	ip_address varchar(45) DEFAULT '0' NOT NULL,
	user_agent varchar(255) NOT NULL,
	last_activity int(10) unsigned DEFAULT 0 NOT NULL,
	user_data text NOT NULL,
	PRIMARY KEY (session_id),
	KEY `last_activity_idx` (`last_activity`)
);



--
-- Table structure for table `grs_phone_session`
--

DROP TABLE IF EXISTS `grs_phone_session`;


CREATE TABLE IF NOT EXISTS  `grs_phone_session` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	staff_id int(11) NOT NULL default 0,
	phone_number varchar(45) NOT NULL DEFAULT '',
	last_activity timestamp NOT NULL default '0000-00-00 00:00:00',
	user_data varchar(120) NOT NULL default '',
  PRIMARY KEY (`id`)
);





--
-- Table structure for table `grs_user`
--

DROP TABLE IF EXISTS `grs_user`;


CREATE TABLE `grs_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(250) NOT NULL DEFAULT '',
  `last_name` varchar(250) NOT NULL DEFAULT '',
  `username` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `last_login` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `login_session_id` varchar(120) NOT NULL DEFAULT '0',
  `group_id` int(11) NOT NULL DEFAULT '0',
  `is_published` enum('True', 'False') NOT NULL DEFAULT 'False',
  `update_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `grs_user`
--

INSERT INTO `grs_user` VALUES (1,'Admin', 'Admin', 'Admin','','',NOW(),'',2, 'True', NOW());
							



--
-- Table structure for table `grs_user_group`
--

DROP TABLE IF EXISTS `grs_user_group`;

CREATE TABLE `grs_user_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `access_level` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `grs_user_group`
--

INSERT INTO `grs_user_group` VALUES (1,'none','None',0),
									(2,'admin','Administrator',100),
									(3,'manager','Manager',60),							
									(4,'charity','Charity',50),
									(5,'company','Company',40),
									(6,'worksite', 'Worksite', 30),
									(7,'participant','Participant',20),
									(8,'user','Web User',10);



--
-- Table structure for table `grs_version`
--

DROP TABLE IF EXISTS `grs_version`;
CREATE TABLE `grs_version` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`version` double NOT NULL DEFAULT '0',
	`update_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`id`)
);

--
-- Dumping data for table `grs_version`
--

INSERT INTO `grs_version` VALUES (1, 0.01, NOW());




DROP TABLE IF EXISTS `grs_work_status`;


CREATE TABLE `grs_work_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT, 
  code varchar(4) NOT NULL default '',
  title varchar(250) NOT NULL default '',
  background_color varchar(12) NOT NULL default '#000000',
  text_color varchar(12) NOT NULL default '#FFFFFF',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO grs_work_status ( code, title, background_color, text_color)  VALUES 
('WD','Day Shift', '#00FF00', '#0000FF'),
('WN','Night Shift', '#00FF00', '#0000FF'),
('O', 'Off Day', '#FF6600', '#0000FF'),
('OC', 'Off Cash', '#FF6600', '#0000FF'),
('AL', 'Annual Leave', '#FF00FF', '#FFFFFF'),
('UL', 'Urgent LEAVE', '#FF00FF', '#FFFFFF'),
('CSE', 'On Course', '#FFFF00', '#0000FF'),
('ABS', 'Absent Without Leave', '#CC99FF', '#FF0000'),
('WOS', 'Walk Out Site', '#CC99FF', '#FF0000'),
('MC', 'Medical Certificate', '#00FFFF', '#FF0000'),
('RS', 'Report Sick', '#00FFFF', '#FF0000'),
('MA', 'Medical Appointment', '#00FFFF', '#FF0000'),
('UPL', 'Unpaid Leave', '#FF0000', '#FFFFFF'),
('CL', 'Compassionate Leave', '#000000', '#FFFFFF'),
('OJT', 'On the job training', '#FFFFFF', '#0000FF'),
('D', 'Festival Package', '#339966', '#FFFFFF');


DROP TABLE IF EXISTS `grs_reply_status`;

CREATE TABLE `grs_reply_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT, 
  number int NOT NULL default 0,
  code varchar(4) NOT NULL default '',
  title varchar(250) NOT NULL default '',
  background_color varchar(12) NOT NULL default '#000000',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO grs_reply_status (number, code, title, background_color) VALUES
(1, 'P', 'Present', '#0000FF'),
(2, 'M', 'Medical Leave', '#00FFFF'),
(3, 'A', 'Annual Leave',  '#FF00FF'),
(4, 'S', 'Sick Leave', '#00FFFF'),
(5, 'O', 'Off', '#FF0000');


--
-- Table structure for table `grs_postal_district`
--

DROP TABLE IF EXISTS `grs_postal_district`;
CREATE TABLE `grs_postal_district` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`number` int NOT NULL DEFAULT 0,
	location varchar(250) NOT NULL default '',
	PRIMARY KEY (`id`)
);

--
-- data for table `grs_postal_district`
--

INSERT INTO `grs_postal_district` (number, location) VALUES 
(01,  'Raffles Place, Cecil, Marina, People\'s Park'),  
(02,  'Anson, Tanjong Pagar'),
(03,  'Queenstown, Tiong Bahru'),
(04,  'Telok Blangah, Harbourfront'),
(05,  'Pasir Panjang, Hong Leong Garden, Clementi New Town'),
(06,  'High Street, Beach Road (part)'),
(07,  'Middle Road, Golden Mile'),
(08,  'Little India'),
(09,  'Orchard, Cairnhill, River Valley'),
(10,  'Ardmore, Bukit Timah, Holland Road, Tanglin'),
(11,  'Watten Estate, Novena, Thomson'),
(12,  'Balestier, Toa Payoh, Serangoon'),
(13,  'Macpherson, Braddell'),
(14,  'Geylang, Eunos'),
(15,  'Katong, Joo Chiat, Amber Road'),
(16,  'Bedok, Upper East Coast, Eastwood, Kew Drive'),
(17,  'Loyang, Changi'),
(18,  'Tampines, Pasir Ris'),
(19,  'Serangoon Garden, Hougang, Ponggol'),
(20,  'Bishan, Ang Mo Kio'),
(21,  'Upper Bukit Timah, Clementi Park, Ulu Pandan'),
(22,  'Jurong'),
(23,  'Hillview, Dairy Farm, Bukit Panjang, Choa Chu Kang'),
(24,  'Lim Chu Kang, Tengah'),
(25,  'Kranji, Woodgrove'),
(26,  'Upper Thomson, Springleaf'),
(27,  'Yishun, Sembawang'),
(28,  'Seletar');

-- '
--
-- Table structure for table `grs_postal_district_sector`
--

DROP TABLE IF EXISTS `grs_postal_district_sector`;
CREATE TABLE `grs_postal_district_sector` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	postal_district_number int NOT NULL default 0,
	postal_sector int NOT NULL default 0,
	PRIMARY KEY (`id`)
);


--
-- data for table `grs_postal_district_sector`
--

INSERT INTO `grs_postal_district_sector` (postal_district_number, postal_sector) VALUES 
(01, 01),
(01, 02), 
(01, 03), 
(01, 04), 
(01, 05), 
(01, 06),
(02, 07), 
(02, 08),
(03, 14),
(03, 15),
(03, 16),
(04, 09), 
(04, 10), 
(05, 11),
(05, 12), 
(05, 13), 
(06, 17), 
(07, 18),
(07, 19), 
(08, 20),
(08, 21),
(09, 22),
(09, 23), 
(10, 24), 
(10, 25), 
(10, 26), 
(10, 27),
(11, 28), 
(11, 29), 
(11, 30),
(12, 31), 
(12, 32),
(12, 33), 
(13, 34), 
(13, 35), 
(13, 36), 
(13, 37),
(14, 38), 
(14, 39), 
(14, 40), 
(14, 41), 
(15, 42),
(15, 43),
(15, 44), 
(15, 45), 
(16, 46), 
(16, 47), 
(16, 48), 
(17, 49), 
(17, 50), 
(17, 81), 
(18, 51), 
(18, 52), 
(19, 53), 
(19, 54), 
(19, 55), 
(19, 82),
(20, 56), 
(20, 57), 
(21, 58), 
(21, 59), 
(22, 60), 
(22, 61), 
(22, 62), 
(22, 63), 
(22, 64), 
(23, 65), 
(23, 66), 
(23, 67), 
(23, 68), 
(24, 69), 
(24, 70), 
(24, 71), 
(25, 72),
(25, 73), 
(26, 77), 
(26, 78), 
(27, 75), 
(27, 76), 
(28, 79), 
(28, 80);


