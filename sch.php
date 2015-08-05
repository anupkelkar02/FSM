<?php
$conn = mysql_connect('db581384879.db.1and1.com','dbo581384879','Storm@123');
mysql_select_db('db581384879',$conn) or die('Unable to connect to DB');

$str = mysql_query('select staff_id,site_id,shift_type from grs_staff_assignment');
mysql_num_rows($str);
while($res = mysql_fetch_array($str)){
	$month = cal_days_in_month(CAL_GREGORIAN,date('m'),date('Y'));
//$month = cal_days_in_month(CAL_GREGORIAN,'06',date('Y'));
      for($i=1;$i<=$month;$i++){
        $date = $i.'-'.date('m').'-'.date('Y');
$workid = ($res['shift_type']=='Day')?'1':'2';
        echo $str_ins = 'insert into grs_schedule(staff_id,site_id,start_date,shift_type,work_status_id) values('.$res['staff_id'].','.$res['site_id'].',"'.date('Y-m-d',strtotime($date)).'","'.$res['shift_type'].'","'.$workid.'")';
        echo $str_ins .'<br/>';
        mysql_query($str_ins);
	}
}
?>