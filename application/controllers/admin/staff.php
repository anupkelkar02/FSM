<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require_once(APPPATH . '/core/site_admin_controller' . EXT);
require_once(APPPATH . '/third_party/tropo.class' . EXT);

class Staff extends Site_admin_controller {

    private $_sort_order;
    private $_row;

    public function __construct() {
        parent::__construct();
        $this->load->model(array('staff_model',
            'staff_assignment_model',
            'site_model','twilio_model')
        );
        $this->load->helper('site_helper');
    }

    public function index($row_pos = 0) {
        $this->_sort_order = $this->staff_model->get_sort_order();

        $this->_filter = filter_load('filter', array('name_match' => '',
            'is_published' => 'True')
        );
        toolbar_process_task($this);


        $this->load->library('pagination');
        $row_pos = intval($row_pos);
        $row_count = $this->staff_model->get_row_count($this->_filter);
        $config = array('total_rows' => $row_count,
            'base_url' => site_url('admin/staff/index'),
            'cur_page' => $row_pos,
            'per_page' => 100
        );
        $this->pagination->initialize($config);


        $rows = $this->staff_model->get_rows($this->_filter, $this->_sort_order, $row_pos, $this->pagination->per_page);


        $data = array(
            'rows' => $rows,
            'filter' => $this->_filter,
            'sort_order' => $this->_sort_order,
            'pagination_links' => $this->pagination->create_links(),
        );
        $this->load->view('admin/staff_list', $data);
    }

    public function toolbar_reload() {
        
    }

    public function toolbar_add() {
        $data = array(
            'first_name' => 'New',
            'last_name' => 'Staff',
            'is_published' => 'True',
            'update_time' => date('Y-m-d H:i:s'),
        );
        $id = $this->staff_model->add_row($data);
        redirect('admin/staff/edit/' . $id);
    }

    public function toolbar_delete() {
        $count = 0;
        $checkids = $this->get_checkids();
        foreach ($checkids as $id) {
            if ($this->staff_model->delete_id($id)) {
                $count ++;
            }
        }
        if ($count > 0) {
            set_message_note($this->lang->line('info_delete_items', $count), MESSAGE_NOTE_INFORMATION);
        } else {
            set_message_note($this->lang->line('error_no_check_id', 'delete'), MESSAGE_NOTE_WARNING);
        }

        redirect(uri_string());
    }

    public function toolbar_toggle_published() {
        $checkid = form_checkids_id_value();
        if ($checkid) {
            $this->staff_model->toggle_is_published($checkid);
            redirect(uri_string());
        } else {
            set_message_note($this->lang->line('error_no_check_id', 'publish/unpublish'), MESSAGE_NOTE_WARNING);
        }
    }

    public function toolbar_sort_order_changed() {
        $this->_sort_order = form_sort_order_apply($this->_sort_order);
    }

    public function toolbar_edit() {
        $checkid = $this->get_checkid();
        if ($checkid) {
            redirect('admin/staff/edit/' . $checkid);
        } else {
            set_message_note($this->lang->line('error_no_check_id', 'edit'), MESSAGE_NOTE_WARNING);
        }
    }

    public function edit($id) {
        if (!$this->staff_model->load_by_id(intval($id))) {
            set_message_note($this->lang->line('error_no_record'), MESSAGE_NOTE_FAILURE);
            redirect('admin/staff');
        }
        $this->load->library(array('form_validation'));
        $this->load->helper('jquery_tab');
        $this->load->model(array('schedule_model', 'work_status_model', 'site_shift_model'));

        $this->form_validation->set_rules('last_name', 'Last Name', 'required');
        $this->form_validation->set_rules('first_name', 'First Name', 'required');
        $this->form_validation->set_rules('phone_number', 'Phone Number', 'required');
        $this->form_validation->set_rules('email', 'Email', '');
$this->form_validation->set_rules('dob', 'DOB');
        $this->form_validation->set_rules('address', 'Address', '');
        $this->form_validation->set_rules('is_published', 'Published', '');
        $this->form_validation->set_rules('call_type', 'Call Type', '');
        $this->form_validation->set_rules('call_minutes', 'Call Minutes', '');
 $this->form_validation->set_rules('dob_alert', 'Send Birthday Greetings', '');




        $this->_row = $this->staff_model->get_row();
        $this->_row = $this->form_validation->get_input_row($this->_row);

        toolbar_process_task($this);

        $assignment_rows = $this->staff_assignment_model->get_rows(array('staff_id' => $this->staff_model->id));
        foreach ($assignment_rows as $assignment_row) {
            $assignment_row->next_shift = $this->_get_next_shift_date_time($this->staff_model->id, $assignment_row->site_id, $assignment_row->shift_type);
        }

        $data = array('row' => $this->_row,
            'assignment_rows' => $assignment_rows,
            'site_list' => $this->site_model->get_dropdown_list('Select Site'),
            'shift_type_list' => $this->staff_assignment_model->get_shift_type_dropdown_list('Shift Type'),
            'assign_type_list' => $this->staff_assignment_model->get_assign_type_dropdown_list('Assign Type'),
            'staff_list' => $this->staff_model->get_dropdown_list('Select Staff'),
            'call_type_list' => $this->staff_model->get_call_type_dropdown_list(''),
        );
        $this->load->view('admin/staff_edit', $data);
    }

    protected function _save() {
        if ($this->form_validation->run()) {
            $this->staff_model->update_row($this->_row, $this->staff_model->id);
            $this->_save_assignment_rows();
            set_message_note($this->lang->line('success_save'), MESSAGE_NOTE_SUCCESS);
            return true;
        } else {
            set_message_note($this->form_validation->error_string(), MESSAGE_NOTE_WARNING);
        }
        return false;
    }

    public function toolbar_apply() {
        $this->_save();
        redirect(uri_string());
    }

    public function toolbar_save() {
        if ($this->_save()) {
            redirect('admin/staff');
        }
    }

    public function toolbar_cancel() {
        redirect('admin/staff');
    }

    public function toolbar_request_setup() {
        $this->load->library('staff_phone_manager');
        $this->staff_phone_manager->intitiate_setup($this->staff_model->id);
    }

    public function toolbar_add_site() {
        $data = array('staff_id' => $this->staff_model->id);
        $this->staff_assignment_model->add_row($data);
        jquery_tab_set_tab_index(1);
    }

    protected function _save_assignment_rows() {
        $site_ids = $this->input->post('assignment_site_id');
        $shift_types = $this->input->post('assignment_shift_type');
        $assign_types = $this->input->post('assignment_assign_type');

        if ($site_ids) {
            foreach ($site_ids as $assignemnt_id => $site_id) {
                $data = array('site_id' => $site_id,
                    'shift_type' => $shift_types[$assignemnt_id],
                    'assign_type' => $assign_types[$assignemnt_id],
                    'staff_id' => $this->staff_model->id,
                );
                $this->staff_assignment_model->update_row($data, $assignemnt_id);
            }
        }
    }

    public function toolbar_remove_site() {
        $ids = form_checkids_ids('assignment_check_id');
        if (count($ids) == 0) {
            set_message_note($this->lang->line('error_no_site_check_id', 'remove'), MESSAGE_NOTE_WARNING);
        }
        foreach ($ids as $id) {
            $this->staff_assignment_model->delete_id($id);
        }
        jquery_tab_set_tab_index(1);
    }

    public function toolbar_request_attendance() {
        $check_id = form_checkids_id_value('assignment_check_id');
        if ($check_id == 0) {
            set_message_note($this->lang->line('error_no_site_check_id', 'request attendance'), MESSAGE_NOTE_WARNING);
            return;
        }

        if ($this->staff_model->phone_number == '91234567') {
            set_message_note($this->lang->line('error_invalid_phone_number', $this->staff_model->phone_number), MESSAGE_NOTE_WARNING);
            return;
        }
        $this->load->library('staff_phone_manager');
        if ($this->staff_assignment_model->load_by_id($check_id)) {

            $next_shift = $this->_get_next_shift_date_time($this->staff_model->id, $this->staff_assignment_model->site_id, $this->staff_assignment_model->shift_type);
            if ($next_shift) {
                $this->staff_phone_manager->request_attendance($next_shift->staff_id, $next_shift->site_id, $next_shift->schedule_id, $next_shift->shift_type, $next_shift->start_time);
            }
        }
        set_message_note($this->lang->line('success_attendance_requested', $this->staff_model->phone_number), MESSAGE_NOTE_SUCCESS);
    }

    public function toolbar_google_sync() {
        
    }

    protected function _get_next_shift_date_time($staff_id, $site_id, $shift_type) {
        $result = new StdClass();
        $result->staff_id = $staff_id;
        $result->site_id = $site_id;
        $result->shift_type = $shift_type;
        $result->schedule_id = 0;
        $schedule_row = reset($this->schedule_model->get_rows(array('staff_id' => $staff_id,
                    'site_id' => $site_id,
                    'start_date >=' => date('Y-m-d')
                        ), 'start_date'
                )
        );

        if ($schedule_row) {
            $result->schedule_id = $schedule_row->id;
            $result->start_time = $schedule_row->start_date;
            $this->work_status_model->load_by_id($schedule_row->work_status_id);
            if ($this->work_status_model->code == 'WD') {
                $result->shift_type = 'Day';
            } else if ($this->work_status_model->code == 'WN') {
                $result->shift_type = 'Night';
            }
            $shift_row = reset($this->site_shift_model->get_rows(array('site_id' => $site_id, 'shift_type' => $result->shift_type)));
            if ($shift_row) {
                $result->start_time .= ' ' . $shift_row->start_time;
            }
            return $result;
        }
        return FALSE;
    }

    public function toolbar_google_staffsync() {
        //echo 'staff sync';
        include_once APPPATH . "libraries/Google/examples/templates/base.php";
        session_start();
        require_once APPPATH . "libraries/Google/src/Google/autoload.php";
        $client_id = '562916899979-sqstcvrkb5ji1364at447l35sl5pqfa3.apps.googleusercontent.com';
        $client_secret = 'JT8lSp-62FHMjbXNqFp2Gy4V';

        //$scriptUri = 'http://fingerskies.com/aza_twilio/admin/sites';
        $redirect_uri = 'http://roster.plug-point.com/index.php/admin/sites/getGoogledata';

        $client = new Google_Client();
        $client->setClientId($client_id);
        $client->setClientSecret($client_secret);
        $client->setRedirectUri($redirect_uri);
        //$client->addScope("https://www.googleapis.com/auth/contacts.readonly");
        $client->addScope("https://www.google.com/m8/feeds");

        //$contacts = new Google_Service($client);

        if (isset($_REQUEST['logout'])) {
            unset($_SESSION['access_token']);
        }

        if (isset($_GET['code'])) {
            $client->authenticate($_GET['code']);
            //var_dump($client);exit;
            $_SESSION['access_token'] = $client->getAccessToken();
            $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
            header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
        }

        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            $client->setAccessToken($_SESSION['access_token']);
        } else {
            $authUrl = $client->createAuthUrl();
        }
        if ($client->getAccessToken()) {
            $_SESSION['access_token'] = $client->getAccessToken();

            $access_token = json_decode($client->getAccessToken())->access_token;
            $url = 'https://www.google.com/m8/feeds/contacts/default/full?alt=json&v=3.0&oauth_token=' . $access_token;

            $response = file_get_contents($url);
        }


        if (
            $client_id == '<YOUR_CLIENT_ID>' || $client_secret == '<YOUR_CLIENT_SECRET>' || $redirect_uri == '<YOUR_REDIRECT_URI>') {
            echo missingClientSecretsWarning();
        }

        if (isset($authUrl)) {
            header('location:' . $authUrl);
            //echo "<a class='login' href='" . $authUrl . "'>Connect Me!</a>";
        } else {
            //echo "<h3>Results Of Contacts List:</h3>";
            $response = str_replace('$', '_', $response);
            $j = json_decode($response);
            //echo '<pre>';
            $contacts = $j->feed->entry;
            //var_dump($contacts); exit;
            for ($i = 0; $i < count($contacts); $i++) {
                // echo $gcontact=$contacts[$i]->gContact_userDefinedField[0];exit;
                $name = $contacts[$i]->gd_name->gd_fullName->_t;
                $first_name = $contacts[$i]->gd_name->gd_givenName->_t;
                $last_name = $contacts[$i]->gd_name->gd_familyName->_t;
                
                $site_name = $contacts[$i]->gd_organization[0]->gd_orgName->_t;
                //$email = $contacts[$i]->gd_email[0]->address;
                $phone = $contacts[$i]->gd_phoneNumber[0]->_t;
                $street = $contacts[$i]->gd_structuredPostalAddress[0]->gd_formattedAddress->_t;
                //echo $street;exit;
                //$city = $contacts[$i]->gd_structuredPostalAddress[0]->gd_city->_t;
                //$postalcode = $contacts[$i]->gd_structuredPostalAddress[0]->gd_postcode->_t;
                $customs = $contacts[$i]->gContact_userDefinedField;
                $cluster = $singaporean = $shift1_start = $shift1_end = $shift2_start = $shift2_end = $shift3_start = $shift3_end = $type1 = $ic = $emp_type = $shift_type = $payment_type = $call_preference = $nationality = $site_preference = '';
                for ($j = 0; $j < count($customs); $j++) {

                    $key = $customs[$j]->key;
                    if ($key == 'Cluster') {
                        $cluster = $customs[$j]->value;
                    } else if (strtolower($key) == 'type') {
                        $type1 = $customs[$j]->value;
                    } else if (strtolower($key) == 'ic') {
                        $ic = $customs[$j]->value;
                    } else if (strtolower($key) == 'employment type') {
                        $emp_type = $customs[$j]->value;
                    } else if (strtolower($key) == 'shift type') {
                        $shift_type = $customs[$j]->value;
                    } else if (strtolower($key) == 'payment type') {
                        $payment_type = $customs[$j]->value;
                    } else if (strtolower($key) == 'call preference') {
                        $call_preference = $customs[$j]->value;
                    } else if (strtolower($key) == 'nationality') {
                        $nationality = $customs[$j]->value;
                    } else if (strtolower($key) == 'site preference') {
                        $site_preference = $customs[$j]->value;
                    }
                }

               //echo 'IC='.$ic;
               if($ic!=''){
                $staff_id=  $this->staff_model->checkICExist($ic);
                //echo 'staff_id'.$staff_id; 
                if($staff_id==''){
                       //echo 'add staff';exit;
                    if (strtolower($type1) == 'staff') {
                        $date = date('Y-m-d h:i:s', time());
                        $data = array('first_name' => $first_name, 'last_name' => $last_name, 'address' => $street, 'phone_number' => $phone, 'fin_number' => $ic, 'update_time' => $date);
                        $staffid = $this->staff_model->insertStaff($data);
                       // echo $staffid;exit;
                        if ($emp_type == 'Permanent') {
                            $emp_type = 'FullTime';
                             $site_id = $this->site_model->getSiteId($site_name);
                             $staff_assign_data = array('staff_id' => $staffid, 'assign_type' => $emp_type, 'shift_type' => $shift_type, 'site_id' => $site_id);
                             $this->staff_model->insertStaffAssignment($staff_assign_data);
                           
                        }else{
                        //echo $site_preference;exit;
                        if ($site_preference != '') {
                            //echo $site_preference;
                            $site_preference = explode(',', $site_preference);
                            //var_dump($site_preference);
                            for ($k = 0; $k < count($site_preference); $k++) {
                                $site_id = $this->site_model->getSiteId($site_preference[$k]);
                                //echo $site_id . ' Staffid=' . $staffid . ' shift_type=' . $shift_type;
                                $staff_assign_data = array('staff_id' => $staffid, 'assign_type' => $emp_type, 'shift_type' => $shift_type, 'site_id' => $site_id);
                                //var_dump($staff_assign_data);
                                $this->staff_model->insertStaffAssignment($staff_assign_data);
                            }
                          }
                        }
                    }
                }else{
                    //echo 'update staff ID='.$staff_id;
                    $date = date('Y-m-d h:i:s', time());
                    $data = array('first_name' => $first_name, 'last_name' => $last_name, 'address' => $street, 'phone_number' => $phone, 'fin_number' => $ic, 'update_time' => $date);
                    $this->staff_model->updateStaff($data, array('fin_number' => $ic));    
                    
                    $this->staff_model->deleteStaffAsssign($staff_id);
                    //echo 'deleted';exit;
                     if ($emp_type == 'Permanent') {
                            $emp_type = 'FullTime';
                             $site_ids = $this->site_model->getSiteId($site_name);
                             $staff_assign_data = array('staff_id' => $staff_id, 'assign_type' => $emp_type, 'shift_type' => $shift_type, 'site_id' => $site_ids);
                             //var_dump($staff_assign_data);
                             $this->staff_model->insertStaffAssignment($staff_assign_data);
                           
                        }else{
                        //echo $site_preference;exit;
                        if ($site_preference != '') {
                            //echo $site_preference;
                            $site_preference = explode(',', $site_preference);
                            //var_dump($site_preference);
                            for ($k = 0; $k < count($site_preference); $k++) {
                                $site_id = $this->site_model->getSiteId($site_preference[$k]);
                               // echo $site_id . ' Staffid=' . $staffid . ' shift_type=' . $shift_type;
                                $staff_assign_data = array('staff_id' => $staff_id, 'assign_type' => $emp_type, 'shift_type' => $shift_type, 'site_id' => $site_id);
                                //var_dump($staff_assign_data);
                                $this->staff_model->insertStaffAssignment($staff_assign_data);
                            }
                          }
                        }
                }
               }  
            }
            set_message_note('Staff Sync Successfully', MESSAGE_NOTE_INFORMATION);
            redirect(uri_string());		
            exit;
        }
    }

    function getGoogledata() {
        include_once APPPATH . "libraries/Google/examples/templates/base.php";
        session_start();

//require_once $_SERVER['DOCUMENT_ROOT'] . '/../googleapi.secrets/secrets.inc.php';

        require_once APPPATH . "libraries/Google/src/Google/autoload.php";

        
        $client_id = '562916899979-sqstcvrkb5ji1364at447l35sl5pqfa3.apps.googleusercontent.com';
        $client_secret = 'JT8lSp-62FHMjbXNqFp2Gy4V';
        $redirect_uri = 'http://roster.plug-point.com/index.php/admin/sites/getGoogledata';
        
        $client = new Google_Client();
        $client->setClientId($client_id);
        $client->setClientSecret($client_secret);
        $client->setRedirectUri($redirect_uri);
        $client->addScope("https://www.google.com/m8/feeds");

        if (isset($_REQUEST['logout'])) {
            unset($_SESSION['access_token']);
        }

        if (isset($_GET['code'])) {
            $client->authenticate($_GET['code']);
            //var_dump($client);exit;
            $_SESSION['access_token'] = $client->getAccessToken();
            $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
            header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
        }

        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            $client->setAccessToken($_SESSION['access_token']);
        } else {
            $authUrl = $client->createAuthUrl();
        }

        if ($client->getAccessToken()) {
            $_SESSION['access_token'] = $client->getAccessToken();

            $access_token = json_decode($client->getAccessToken())->access_token;

            $url = 'https://www.google.com/m8/feeds/contacts/default/full?alt=json&v=3.0&oauth_token=' . $access_token;

            $response = file_get_contents($url);
        }

//echo pageHeader("User Query - Multiple APIs");
        if (
                $client_id == '<YOUR_CLIENT_ID>' || $client_secret == '<YOUR_CLIENT_SECRET>' || $redirect_uri == '<YOUR_REDIRECT_URI>') {
            echo missingClientSecretsWarning();
        }

        if (isset($authUrl)) {
            echo "<a class='login' href='" . $authUrl . "'>Connect Me!</a>";
        } else {
            //echo "<h3>Results Of Contacts List:</h3>";
            $response = str_replace('$', '_', $response);
            //var_dump($response);
            //$t = str_replace("\n", '', $response);
            $j = json_decode($response);
            $j = str_replace('$', '_', $j);
            echo '<pre>';
            // var_dump($j->feed->entry);
            /* foreach($j->feed->entry as $contacts){
              $name = $contacts->title;
              $email = $contacts->gd$email[0]->address;
              $phone = $contacts->gd$phoneNumber[0]->$t;
              $address = $contacts->structuredPostalAddress[0]->gd$formattedAddress;
              $customs = $contacts->gContact$userDefinedField;
              foreach ($customs as $f){
              $key = $f->key;
              $val = $f->value;
              if($key=='Type' && $val=='Sites'){
              //Insert into DB
              echo $name.';'.$email.';'.$phone.';'.$address.';'.$key.';'.$val.'<br/>';
              }
              }
              } */
            file_put_contents('tst.json', $response);
        }
        set_message_note('Staff Sync Successfully', MESSAGE_NOTE_INFORMATION);
        redirect(uri_string());		
        exit;
    }
    
      public function export(){
        $this->load->library('PHPExcel');
//$this->load->library('PHPExcel/iofactory');
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
                ->setLastModifiedBy("Maarten Balliauw")
                ->setTitle("Office 2007 XLSX Test Document")
                ->setSubject("Office 2007 XLSX Test Document")
                ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                ->setKeywords("office 2007 openxml php")
                ->setCategory("Test result file");

        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'ID')
                ->setCellValue('B1', 'First Name')
                ->setCellValue('C1', 'Last Name')
                ->setCellValue('D1', 'Address')
                ->setCellValue('E1', 'Phone')
                ->setCellValue('F1', 'Email')
                ->setCellValue('G1', 'Fin Number')
                ->setCellValue('H1', 'Call Type')
                ->setCellValue('I1', 'Call Minutes')
                ->setCellValue('J1', 'Is Published')
                ->setCellValue('K1', 'Update Time');
                
        $i = 2;
       $site_list = $this->staff_model->getStaffList();
        foreach ($site_list as $slist) {
        foreach (range('A', 'K') as $columnID) {
                $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
        }
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $slist->id)
                    ->setCellValue('B' . $i, $slist->first_name)
                    ->setCellValue('C' . $i, $slist->last_name)
                    ->setCellValue('D' . $i, $slist->address)
                    ->setCellValue('E' . $i, $slist->phone_number)
                    ->setCellValue('F' . $i, $slist->email)
                    ->setCellValue('G' . $i, $slist->fin_number)
                    ->setCellValue('H' . $i, $slist->call_type)
                    ->setCellValue('I' . $i, $slist->call_minutes)
                    ->setCellValue('J' . $i, $slist->is_published)
                    ->setCellValue('K' . $i, $slist->update_time);
                    

            $i++;
        }

        $objPHPExcel->getActiveSheet()->setTitle('Staff List');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);


        // Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Staff.xls"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

  

public function import_staff() {
        $this->load->view('admin/import_staff');
    }

    public function import_staffexcel() {

        $this->load->library('upload');
        $this->load->helper('file');
//echo $_SERVER['DOCUMENT_ROOT']; exit;
        $rootpath = $_SERVER['DOCUMENT_ROOT'] . '/uploads/staff/';
        $config['upload_path'] = $rootpath;

        $config['allowed_types'] = '*';
        $config['max_size'] = '1000000';

        $this->upload->initialize($config);

        echo $this->upload->file_type;
        if (!$this->upload->do_upload('file')) {

            set_message_note($this->upload->display_errors(), MESSAGE_NOTE_FAILURE);
        } else {
            require_once APPPATH . '/libraries/PHPExcel.php';
            require_once APPPATH . '/libraries/PHPExcel/IOFactory.php';

          //  $objReader = PHPExcel_IOFactory::createReader('Excel2007');
            //set to read only
            
            //load excel file
            $upload_data = $this->upload->data(); //Returns array of containing all of the data related to the file you uploaded.
            $file_name = $upload_data['file_name'];
$inputFileType = PHPExcel_IOFactory::identify($rootpath .$file_name);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
$objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($config['upload_path'] . $file_name);

            $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
            //load model
            $this->load->model("staff_model");
            $highestRow = $objWorksheet->getHighestRow();
            //$highestRow = $worksheet->getHighestRow(); 
            //loop from first data until last data
            $checkid = $objWorksheet->getCellByColumnAndRow(0, 1)->getValue();

                $checkfirst_name = $objWorksheet->getCellByColumnAndRow(1, 1)->getValue();
                $checklast_name = $objWorksheet->getCellByColumnAndRow(2, 1)->getValue();
                $checkaddress = $objWorksheet->getCellByColumnAndRow(3, 1)->getValue();
                //$address='nothing';
                $checkphone_number = $objWorksheet->getCellByColumnAndRow(4, 1)->getValue();
                $checkemail = $objWorksheet->getCellByColumnAndRow(5, 1)->getValue();
                //$email='nothng@gmail.com';
                $checkfin_number = $objWorksheet->getCellByColumnAndRow(6, 1)->getValue();
                $checkcall_type = $objWorksheet->getCellByColumnAndRow(7, 1)->getValue();
                //$call_type="None";
                $checkcall_minutes = $objWorksheet->getCellByColumnAndRow(8, 1)->getValue();
                $checkis_published = $objWorksheet->getCellByColumnAndRow(9, 1)->getValue();
                
               if (strtolower($checkfirst_name) !== "first name") {
                set_message_note('first name field is not present in excel sheet', MESSAGE_NOTE_FAILURE);
                redirect('admin/staff/import_staff');
                exit;
            }
            if (strtolower($checklast_name) !== "last name") {
                set_message_note('last name field is not present in excel sheet', MESSAGE_NOTE_FAILURE);
                redirect('admin/staff/import_staff');
                exit;
            }
            if (strtolower($checkaddress) !== "address") {
                set_message_note('address field is not present in excel sheet', MESSAGE_NOTE_FAILURE);
                redirect('admin/staff/import_staff');
                exit;
            } 
            if (strtolower($checkphone_number) !== "phone") {
                set_message_note('phone field is not present in excel sheet', MESSAGE_NOTE_FAILURE);
                redirect('admin/staff/import_staff');
                exit;
            }
             if (strtolower($checkemail) !== "email") {
                set_message_note('email field is not present in excel sheet', MESSAGE_NOTE_FAILURE);
                redirect('admin/staff/import_staff');
                exit;
            }
             if (strtolower($checkfin_number) !== "fin number") {
                set_message_note('fin number field is not present in excel sheet', MESSAGE_NOTE_FAILURE);
                redirect('admin/staff/import_staff');
                exit;
            }
             if (strtolower($checkcall_type) !== "call type") {
                set_message_note('call type field is not present in excel sheet', MESSAGE_NOTE_FAILURE);
                redirect('admin/staff/import_staff');
                exit;
            }
             if (strtolower($checkcall_minutes) !== "call minutes") {
                set_message_note('call minutes field is not present in excel sheet', MESSAGE_NOTE_FAILURE);
                redirect('admin/staff/import_staff');
                exit;
            }
            if (strtolower($checkis_published) !== "is published") {
                set_message_note('is published field is not present in excel sheet', MESSAGE_NOTE_FAILURE);
                redirect('admin/staff/import_staff');
                exit;
            }
            for ($i = 2; $i <= $highestRow; $i++) {
                $first_name = $objWorksheet->getCellByColumnAndRow(1, $i)->getValue();
                $last_name = $objWorksheet->getCellByColumnAndRow(2, $i)->getValue();
                $address = $objWorksheet->getCellByColumnAndRow(3, $i)->getValue();
                //$address='nothing';
                $phone_number = $objWorksheet->getCellByColumnAndRow(4, $i)->getValue();
                $email = $objWorksheet->getCellByColumnAndRow(5, $i)->getValue();
                //$email='nothng@gmail.com';
                $fin_number = $objWorksheet->getCellByColumnAndRow(6, $i)->getValue();
                $call_type = $objWorksheet->getCellByColumnAndRow(7, $i)->getValue();
                //$call_type="None";
                $call_minutes = $objWorksheet->getCellByColumnAndRow(8, $i)->getValue();
                $is_published = $objWorksheet->getCellByColumnAndRow(9, $i)->getValue();
$update = $objWorksheet->getCellByColumnAndRow(10, $i)->getValue();
if(empty($update)){
                $date = date('Y-m-d h:i:s', time());
}else{
      $date = date('Y-m-d H:i:s',strtotime($update));
}               
 $data_user = array(
                    "first_name" => $first_name,
                    "last_name" => $last_name,
                    "address" => $address,
                    "phone_number" => $phone_number,
                    "email" => $email,
                    "fin_number" => $fin_number,
                    "call_type" => $call_type,
                    "call_minutes" => $call_minutes,
                    "is_published" => $is_published,
                    "update_time" => $date);
                //var_dump($data_user); exit;

                $existfinnumber = $this->staff_model->checknumber($fin_number);
                
                if ($existfinnumber) {
                    $result = $this->staff_model->update_staffdata($data_user, array('fin_number' => $fin_number));
                } else {
                    $result = $this->staff_model->add_staffdata($data_user);
                }
            }
            if ($result) {
                set_message_note('Sites imported successfully');
            } else {
                set_message_note('Something wrong',MESSAGE_NOTE_FAILURE);
            }
        }
        redirect('admin/staff/import_staff');
        exit;
    }


    public function staff_config(){
        //echo 'call';exit;
        $this->_row = $this->twilio_model->get_row();
        //var_dump($this->_row->sop_url);exit;
        $data= array('url'=> $this->_row->staff_url);
        $this->load->view('admin/staff_config',$data);
    }
    public function staffurl_save() {
            $staff_url = $this->input->post('staff_url');
            $data = array('staff_url' => $staff_url);
            $this->twilio_model->updateurl($data);
            set_message_note($this->lang->line('success_save'), MESSAGE_NOTE_SUCCESS);
           // echo $sop_url;exit;
           $this->getStaffData($staff_url);
            redirect('admin/staff');
            return true;
    }     
public function getStaffData($staff_url){
             
            $key=explode('/', $staff_url);
            $feed = "https://docs.google.com/spreadsheet/pub?hl=en_US&hl=en_US&single=true&output=csv&&key=".$key[5];
            //var_dump($feed);exit;
            $newArray = array();
            $data = $this->csvToArray($feed, ',');
            $count = count($data) - 1;
            $labels = array_shift($data);
            foreach ($labels as $label) {
                $keys[] = $label;
            }
            $keys[] = 'id';
            for ($i = 0; $i < $count; $i++) {
                $data[$i][] = $i;
            }
            for ($j = 0; $j < $count; $j++) {
                $d = array_combine($keys, $data[$j]);
                $newArray[$j] = $d;
            }
            $staff=json_encode($newArray);
            $staff_json = json_decode($staff);
            //var_dump($staff_json);exit;
            foreach ($staff_json as $sdata){
                $finno=$sdata->IDNo;
                $staffid=$this->staff_model->getStaffId($finno);
                if($staffid>0){
                    $noti_date=date_create($sdata->NotificationDate);
                    $licence_date=date_create($sdata->LicenceApprovedDate);
                    //var_dump(date_format($noti_date,"Y-m-d"));exit;
                    $updatetime = date('Y-m-d h:i:s', time());
                    $staffdata=array('staff_id'=>$staffid,'post'=>$sdata->Post,'notification_date'=>date_format($noti_date,"Y-m-d"),'licence_appr_date'=>date_format($licence_date,"Y-m-d"),'licence_type'=>$sdata->Licencetype,'training'=>$sdata->Training,'updated_date'=>$updatetime);
                    $this->staff_model->insertactivestaff($staffdata);
                    $ispublished = array("is_published" =>'True','update_time'=>$updatetime);
                    $staff_id=array("id" => $staffid);
                    $this->staff_model->updateispublished($ispublished,$staff_id);
                
            }
    }
    } 
  
    function csvToArray($file, $delimiter) {
        if (($handle = fopen($file, 'r')) !== FALSE) {
            $i = 0;
            while (($lineArray = fgetcsv($handle, 4000, $delimiter, '"')) !== FALSE) {
                for ($j = 0; $j < count($lineArray); $j++) {
                    $arr[$i][$j] = $lineArray[$j];
                }
                $i++;
            }
            fclose($handle);
        }
        return $arr;
    }    
public function exportStaffAssignment(){
        $this->load->library('PHPExcel');
        //$this->load->library('PHPExcel/iofactory');
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
                ->setLastModifiedBy("Maarten Balliauw")
                ->setTitle("Office 2007 XLSX Test Document")
                ->setSubject("Office 2007 XLSX Test Document")
                ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                ->setKeywords("office 2007 openxml php")
                ->setCategory("Test result file");

        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'Sr.No')
                ->setCellValue('B1', 'Site Name')
                ->setCellValue('C1', 'Staff Name')
                ->setCellValue('D1', 'Assign Type')
                ->setCellValue('E1', 'Shift Type');
        
        $objPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("B1")->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("C1")->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("D1")->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("E1")->getFont()->setBold(true);
      
       
        $i = 2;
        $k = 1;
        $site_list = $this->staff_model->getStaffIds();
        foreach ($site_list as $sitelist) {
            foreach (range('A', 'F') as $columnID) {
                $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
            }
        $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $i, $k) 
                        ->setCellValue('B' . $i, $sitelist->name);
             
        $staff_assign=  $this->staff_model->getStaffAssignList($sitelist->site_id);
        
          foreach($staff_assign as $slist){
            $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('C' . $i, $slist->fname)
                        ->setCellValue('D' . $i, $slist->assign_type)
                        ->setCellValue('E' . $i, $slist->shift_type);
                $i++;
            }
          $k++;   
        }
        $objPHPExcel->getActiveSheet()->setTitle('Staff Assign List');
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="StaffAssignment.xls"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

}

?>