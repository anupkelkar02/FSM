<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require_once(APPPATH . '/core/site_admin_controller' . EXT);

class Attendance extends Site_admin_controller {

    private $_sort_order;
    private $_row;

    public function __construct() {
        parent::__construct();
        $this->load->model(array('staff_model','attendance_model','site_model','schedule_model')
        );
        $this->load->helper('site_helper');
    }

    public function index($row_pos = 0) {
    }

    
public function import() {
        $this->load->view('admin/import_att');
    }

    public function import_attexcel() {

        $this->load->library('upload');
        $this->load->helper('file');
        $rootpath = $_SERVER['DOCUMENT_ROOT'] . '/uploads/staff/';
        $config['upload_path'] = $rootpath;

        $config['allowed_types'] = 'xls|xlsx';
        $config['max_size'] = '1000';

        $this->upload->initialize($config);

        echo $this->upload->file_type;
        if (!$this->upload->do_upload('file')) {

            set_message_note($this->upload->display_errors(), MESSAGE_NOTE_FAILURE);
        } else {
            require_once APPPATH . '/libraries/PHPExcel.php';
            require_once APPPATH . '/libraries/PHPExcel/IOFactory.php';
            $upload_data = $this->upload->data(); //Returns array of containing all of the data related to the file you uploaded.
            $file_name = $upload_data['file_name'];
            $inputFileType = PHPExcel_IOFactory::identify($rootpath .$file_name);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($config['upload_path'] . $file_name);

            $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
            $highestRow = $objWorksheet->getHighestRow();
           
            

                $checksite_name = $objWorksheet->getCellByColumnAndRow(0, 1)->getValue();
                $checkstaff_code = $objWorksheet->getCellByColumnAndRow(1, 1)->getValue();
                $checkdate = $objWorksheet->getCellByColumnAndRow(2, 1)->getValue();
                $checkshift = $objWorksheet->getCellByColumnAndRow(3, 1)->getValue();
                $checkintime = $objWorksheet->getCellByColumnAndRow(4, 1)->getValue();
                $checkout_time = $objWorksheet->getCellByColumnAndRow(5, 1)->getValue();
                $checkhours = $objWorksheet->getCellByColumnAndRow(6, 1)->getValue();
                $checkstatus = $objWorksheet->getCellByColumnAndRow(7, 1)->getValue();
                if (strtolower($checksite_name) !== "site") {
                set_message_note('site field is not present in excel sheet', MESSAGE_NOTE_FAILURE);
                redirect('admin/attendance/import');
                exit;
            }
            if (strtolower($checkstaff_code) !== "code") {
                set_message_note('code field is not present in excel sheet', MESSAGE_NOTE_FAILURE);
                redirect('admin/attendance/import');
                exit;
            }
            if (strtolower($checkdate) !== "date") {
                set_message_note('date field is not present in excel sheet', MESSAGE_NOTE_FAILURE);
                redirect('admin/attendance/import');
                exit;
            } 
            if (strtolower($checkshift) !== "shift") {
                set_message_note('shift field is not present in excel sheet', MESSAGE_NOTE_FAILURE);
                redirect('admin/attendance/import');
                exit;
            } 
             if (strtolower($checkintime) !== "in time") {
                set_message_note('In time field is not present in excel sheet', MESSAGE_NOTE_FAILURE);
                redirect('admin/attendance/import');
                exit;
            } 
             if (strtolower($checkout_time) !== "out time") {
                set_message_note('Out time field is not present in excel sheet', MESSAGE_NOTE_FAILURE);
                redirect('admin/attendance/import');
                exit;
            } 
             if (strtolower($checkhours) !== "hours") {
                set_message_note('Out time field is not present in excel sheet', MESSAGE_NOTE_FAILURE);
                redirect('admin/attendance/import');
                exit;
            } 
            
            if (strtolower($checkstatus) !== "status") {
                set_message_note('status is not present in excel sheet', MESSAGE_NOTE_FAILURE);
                redirect('admin/attendance/import');
                exit;
            }
             $err = ''; $scnt = 0;
            for ($i = 2; $i <= $highestRow; $i++) {
                $site_name = $objWorksheet->getCellByColumnAndRow(0, $i)->getValue();
                $staff_code = $objWorksheet->getCellByColumnAndRow(1, $i)->getValue();
                $att_date = $objWorksheet->getCellByColumnAndRow(2, $i)->getValue();
                $att_shift = $objWorksheet->getCellByColumnAndRow(3, $i)->getValue();
                $att_intime = $objWorksheet->getCellByColumnAndRow(4, $i)->getValue();
                $att_out_time = $objWorksheet->getCellByColumnAndRow(5, $i)->getValue();
                $att_hours = $objWorksheet->getCellByColumnAndRow(6, $i)->getValue();
                $att_status = $objWorksheet->getCellByColumnAndRow(7, $i)->getValue();
                $filter = filter_load('filter', array('id' =>$staff_code,'is_published' => 'True') );
                $existstaff = $this->staff_model->get_row_count($filter);
               
                $existsite = $this->site_model->checkcode($site_name);
              // var_dump($existstaff); 
                if($existstaff!=false && $existsite!=false && 
                        $att_date!='' && $att_shift!='' && 
                        (strtolower($att_status)=='p' || strtolower($att_status)=='a')){
                    //check staff site assignment in roster against the date provided
                    $filter_sch = filter_load('filter', array('staff_id' =>$staff_code,'site_id' => $existsite[0]->id,'start_date'=>date('Y-m-d',strtotime($att_date)),'shift_type'=>$att_shift) );
                    $is_sch = $this->schedule_model->get_row_count($filter_sch);
                    if($is_sch>0){
                        $data = array('site_id'=> $existsite[0]->id,
                            'staff_id'=>$staff_code,
                            'att_date'=>date('Y-m-d',strtotime($att_date)),
                            'att_in'=>$att_intime,
                            'att_out'=>$att_out_time,
                            'att_hours'=>$att_hours,
                            'att_shift'=>$att_shift,
                            'att_status'=>$att_status);
                        //var_dump($data);
                        $this->attendance_model->insert($data);
                        $scnt = $scnt+1;
                    }
                }else{
                    $err .= ' Check staff or site or date or shift. Make sure status must be "P" or "A" at row number '.$i.'<br/>';
                }
                
            }
            if ($scnt>0) {
                set_message_note($scnt.' rows imported successfully');
            } 
            if (!empty($err)) {
                set_message_note($err,MESSAGE_NOTE_FAILURE);
            }
        }
        //exit;
        redirect('admin/attendance/import');
        exit;
    }


}

?>