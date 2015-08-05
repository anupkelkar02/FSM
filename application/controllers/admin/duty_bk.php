<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require_once(APPPATH . '/core/site_admin_controller' . EXT);
//require_once(APPPATH . '/third_party/tropo.class' . EXT);

class Duty extends Site_admin_controller {

    private $_sort_order;
    private $_row;

    public function __construct() {
        parent::__construct();
        $this->load->model(array('duty_model'));
	$this->load->model(array('site_model'));
        $this->load->model(array('twilio_model'));
        $this->load->helper('site_helper');
    }

    public function index($row_pos = 0) {
        $this->_sort_order = $this->duty_model->get_sort_order();

        $this->_filter = filter_load('filter', array()
        );
        toolbar_process_task($this);


        $this->load->library('pagination');
        $row_pos = intval($row_pos);
        $row_count = $this->duty_model->get_row_count($this->_filter);
        $config = array('total_rows' => $row_count,
            'base_url' => site_url('admin/duty/index'),
            'cur_page' => $row_pos,
            'per_page' => 100
        );
        $this->pagination->initialize($config);


        $rows = $this->duty_model->get_rows($this->_filter, $this->_sort_order, $row_pos, $this->pagination->per_page);
        

        $data = array(
            'rows' => $rows,
            'filter' => $this->_filter,
            'sort_order' => $this->_sort_order,
            'pagination_links' => $this->pagination->create_links(),
        );
        $this->load->view('admin/duty_list', $data);
    }

 public function sop_config(){
        $this->_row = $this->twilio_model->get_row();
//        var_dump($this->_row);exit
        $data= array('url'=> $this->_row->sop_url);
       // var_dump($data);exit;
        $this->load->view('admin/sop_config',$data);
    }
    public function url_save() {
            $sop_url = $this->input->post('sop_url');
            $data = array('sop_url' => $sop_url);
            $this->twilio_model->updateurl($data);
            set_message_note($this->lang->line('success_save'), MESSAGE_NOTE_SUCCESS);
           // echo $sop_url;exit;
            $this->getSOPData($sop_url);
            redirect('admin/duty');
            return true;
    }

public function getSOPData($sop_url){
             
            $key=explode('/', $sop_url);
            $feed = "https://docs.google.com/spreadsheet/pub?hl=en_US&hl=en_US&single=true&gid=0&output=csv&&key=".$key[5];
	    //var_dump($feed);exit;
            $keys = array();
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
            $sop=json_encode($newArray);
            $sop_json = json_decode($sop);
            foreach ($sop_json as $sdata){
                $site=$sdata->Site;
                $siteid=$this->site_model->getSiteId($site);
                //echo $siteid;exit;
                $sopdata=array('site_id'=>$siteid,'duty'=>$sdata->Duty,'effort'=>$sdata->Effort,'shift'=>$sdata->Shift,'start_time'=>$sdata->StartTime,'end_time'=>$sdata->EndTime,'sla'=>$sdata->SLA,'updated_time'=>date('Y-m-d H:i:s'));
                $this->duty_model->insertDuty($sopdata);
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

    

    public function toolbar_reload() {
        
    }

    public function toolbar_add() {
        $data = array(
            'first_name' => 'New',
            'last_name' => 'Staff',
            'is_published' => 'True',
            'update_time' => date('Y-m-d H:i:s'),
        );
        $id = $this->duty_model->add_row($data);
        redirect('admin/staff/edit/' . $id);
    }

    public function toolbar_delete() {
        $count = 0;
        $checkids = $this->get_checkids();
        foreach ($checkids as $id) {
            if ($this->duty_model->delete_id($id)) {
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
            $this->duty_model->toggle_is_published($checkid);
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
        if (!$this->duty_model->load_by_id(intval($id))) {
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
        $this->form_validation->set_rules('address', 'Address', '');
        $this->form_validation->set_rules('is_published', 'Published', '');
        $this->form_validation->set_rules('call_type', 'Call Type', '');
        $this->form_validation->set_rules('call_minutes', 'Call Minutes', '');




        $this->_row = $this->duty_model->get_row();
        $this->_row = $this->form_validation->get_input_row($this->_row);

        toolbar_process_task($this);

        $assignment_rows = $this->staff_assignment_model->get_rows(array('staff_id' => $this->duty_model->id));
        foreach ($assignment_rows as $assignment_row) {
            $assignment_row->next_shift = $this->_get_next_shift_date_time($this->duty_model->id, $assignment_row->site_id, $assignment_row->shift_type);
        }

        $data = array('row' => $this->_row,
            'assignment_rows' => $assignment_rows,
            'site_list' => $this->site_model->get_dropdown_list('Select Site'),
            'shift_type_list' => $this->staff_assignment_model->get_shift_type_dropdown_list('Shift Type'),
            'assign_type_list' => $this->staff_assignment_model->get_assign_type_dropdown_list('Assign Type'),
            'staff_list' => $this->duty_model->get_dropdown_list('Select Staff'),
            'call_type_list' => $this->duty_model->get_call_type_dropdown_list(''),
        );
        $this->load->view('admin/staff_edit', $data);
    }

    protected function _save() {
        if ($this->form_validation->run()) {
            $this->duty_model->update_row($this->_row, $this->duty_model->id);
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
        $this->staff_phone_manager->intitiate_setup($this->duty_model->id);
    }

    public function toolbar_add_site() {
        $data = array('staff_id' => $this->duty_model->id);
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
                    'staff_id' => $this->duty_model->id,
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

    public function send_dutysms($site_id,$site_type,$message) {
        //echo 'req'.$site_id; exit;
        
       $this->load->library('staff_phone_manager');
       echo $this->staff_phone_manager->process_duty_message($site_id,$site_type,$message);
       $this->index();
        
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
        $client_id = '354679217715-0f0fu7ejpvmh7m408ll1thjs7vlemmk5.apps.googleusercontent.com';
        $client_secret = 'hT9fCTljAoBSUcA5nucxb7FJ';

        //$scriptUri = 'http://fingerskies.com/aza_twilio/admin/sites';
        $redirect_uri = 'http://fingerskies.com/az-twilio/index.php/admin/sites/getGoogledata';

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
            echo "<h3>Results Of Contacts List:</h3>";
            $response = str_replace('$', '_', $response);
            $j = json_decode($response);
            echo '<pre>';
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
                $staff_id=  $this->duty_model->checkICExist($ic);
                //echo 'staff_id'.$staff_id; 
                if($staff_id==''){
                       //echo 'add staff';exit;
                    if (strtolower($type1) == 'staff') {
                        $date = date('Y-m-d h:i:s', time());
                        $data = array('first_name' => $first_name, 'last_name' => $last_name, 'address' => $street, 'phone_number' => $phone, 'fin_number' => $ic, 'update_time' => $date);
                        $staffid = $this->duty_model->insertStaff($data);
                       // echo $staffid;exit;
                        if ($emp_type == 'Permanent') {
                            $emp_type = 'FullTime';
                             $site_id = $this->site_model->getSiteId($site_name);
                             $staff_assign_data = array('staff_id' => $staffid, 'assign_type' => $emp_type, 'shift_type' => $shift_type, 'site_id' => $site_id);
                             $this->duty_model->insertStaffAssignment($staff_assign_data);
                           
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
                                $this->duty_model->insertStaffAssignment($staff_assign_data);
                            }
                          }
                        }
                    }
                }else{
                    echo 'update staff ID='.$staff_id;
                    $date = date('Y-m-d h:i:s', time());
                    $data = array('first_name' => $first_name, 'last_name' => $last_name, 'address' => $street, 'phone_number' => $phone, 'fin_number' => $ic, 'update_time' => $date);
                    $this->duty_model->updateStaff($data, array('fin_number' => $ic));    
                    
                    $this->duty_model->deleteStaffAsssign($staff_id);
                    //echo 'deleted';exit;
                     if ($emp_type == 'Permanent') {
                            $emp_type = 'FullTime';
                             $site_ids = $this->site_model->getSiteId($site_name);
                             $staff_assign_data = array('staff_id' => $staff_id, 'assign_type' => $emp_type, 'shift_type' => $shift_type, 'site_id' => $site_ids);
                             var_dump($staff_assign_data);
                             $this->duty_model->insertStaffAssignment($staff_assign_data);
                           
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
                                $this->duty_model->insertStaffAssignment($staff_assign_data);
                            }
                          }
                        }
                }
               }  
            }
            file_put_contents('tst.json', $response);
        }
    }

    function getGoogledata() {
        include_once APPPATH . "libraries/Google/examples/templates/base.php";
        session_start();

//require_once $_SERVER['DOCUMENT_ROOT'] . '/../googleapi.secrets/secrets.inc.php';

        require_once APPPATH . "libraries/Google/src/Google/autoload.php";

        /*         * **********************************************
          ATTENTION: Fill in these values! Make sure
          the redirect URI is to this page, e.g:
          http://localhost:8080/user-example.php
         * ********************************************** */
        $client_id = '354679217715-0f0fu7ejpvmh7m408ll1thjs7vlemmk5.apps.googleusercontent.com';
        $client_secret = 'hT9fCTljAoBSUcA5nucxb7FJ';
//$scriptUri = 'http://fingerskies.com/aza_twilio/admin/sites';
        $redirect_uri = 'http://fingerskies.com/az-twilio/index.php/admin/sites/getGoogledata';

        /*         * **********************************************
          Make an API request on behalf of a user. In
          this case we need to have a valid OAuth 2.0
          token for the user, so we need to send them
          through a login flow. To do this we need some
          information from our API console project.
         * ********************************************** */
        $client = new Google_Client();
        $client->setClientId($client_id);
        $client->setClientSecret($client_secret);
        $client->setRedirectUri($redirect_uri);
//$client->addScope("https://www.googleapis.com/auth/contacts.readonly");
        $client->addScope("https://www.google.com/m8/feeds");

        /*         * **********************************************
          A general service created
         * ********************************************** */
//$contacts = new Google_Service($client);



        /*         * **********************************************
          Boilerplate auth management - see
          user-example.php for details.
         * ********************************************** */
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
//echo $authUrl; exit; 
        /*         * **********************************************
          If we're signed in, retrieve contacts
         * ********************************************** */

        if ($client->getAccessToken()) {
            $_SESSION['access_token'] = $client->getAccessToken();

            $access_token = json_decode($client->getAccessToken())->access_token;

            //$url = 'https://www.google.com/m8/feeds/contacts/default/full?max-results=3&alt=json&v=3.0&oauth_token='.$access_token;
            //$Gurl = 'https://www.google.com/m8/feeds/groups/default/full?alt=json&v=3.0&oauth_token='.$access_token;
            //$Gresponse =  file_get_contents($Gurl);
            //echo '<pre>';
            //var_dump(json_decode($Gresponse); 
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
            echo "<h3>Results Of Contacts List:</h3>";
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
    }
    public function toolbar_send() {
        
        $checkid = $this->get_checkids();
        
        if ($checkid) {
            $this->send_message($checkid);
        } else {
            set_message_note($this->lang->line('error_no_check_id', 'Send'), MESSAGE_NOTE_WARNING);
        }
    }
    
    public function send_message($ids) {
        $this->load->library('staff_phone_manager');
        //var_dump($id); exit;
        //for($i=1;$i<=count($id);$i++)
        {
            foreach ($ids as $id) {
                
                $res = $this->duty_model->siteid($id);
                
                $staffmember = $this->duty_model->staff_member($res);
                $message=  $this->duty_model->get_message($id);
                
                for ($i = 0; $i < count($staffmember); $i++)
                {
                    
                    $number= $this->duty_model->get_staff_number($staffmember[$i]);
                    
                    $this->staff_phone_manager->send_sms_message($number, urldecode($message));
                }
            }
        }
    }
}
?>